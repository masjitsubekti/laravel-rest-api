<?php

namespace App\Http\Controllers;

use App\Models\Artikel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use File;

class ArtikelController extends Controller
{
    protected $artikelModel;
    public function __construct()
    {
        $this->artikelModel = new Artikel();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = $request->get('page');
        $limit = $request->get('limit');
        $sortBy = $request->get('sortby');
        $sortType = $request->get('sorttype');
        $q = ($request->get('q') != "") ? $request->get('q') : "";
        $q = str_replace(" ", "%", $q);

        // Filter Pagination
        $filter = array(
            'q' => $q,
            'page' => $page,
            'limit' => $limit,
            'sortby' => (isset($columnMap[$sortBy])) ? $columnMap[$sortBy] : 'created_at',
            'sorttype' => ($sortType != "") ? $sortType : 'desc',
            'isPublish' => "",
        );

        $totalData = $this->artikelModel->getCountArtikel($filter);
        $data = $this->artikelModel->getListArtikel($filter);
        foreach ($data as $row) {
            $row->image = ($row->image) ? url('/') . '/' . $row->image : null;
        }

        $response = [
            'success' => true,
            'data' => $data,
            'meta' => array(
                'page' => $page,
                'pageSize' => $limit,
                'total' => $totalData,
            ),
        ];
        return response()->json($response, 200, [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * Get Artikel Published
     */
    public function getArtikelPublish(Request $request)
    {
        $page = $request->get('page');
        $limit = $request->get('limit');
        $sortBy = $request->get('sortby');
        $sortType = $request->get('sorttype');
        $q = ($request->get('q') != "") ? $request->get('q') : "";
        $q = str_replace(" ", "%", $q);

        // Filter Pagination
        $filter = array(
            'q' => $q,
            'page' => $page,
            'limit' => $limit,
            'sortby' => (isset($columnMap[$sortBy])) ? $columnMap[$sortBy] : 'created_at',
            'sorttype' => ($sortType != "") ? $sortType : 'desc',
            'isPublish' => "PUBLISHED",
        );

        $totalData = $this->artikelModel->getCountArtikel($filter);
        $data = $this->artikelModel->getListArtikel($filter);
        foreach ($data as $row) {
            $row->image = ($row->image) ? url('/') . '/' . $row->image : null;
        }

        $response = [
            'success' => true,
            'data' => $data,
            'meta' => array(
                'page' => $page,
                'pageSize' => $limit,
                'total' => $totalData,
            ),
        ];
        return response()->json($response, 200, [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * Get All Artikel
     */
    public function getAll()
    {
        $data = Artikel::where([
            'status' => 1
        ])->orderBy('created_at', 'DESC')->get();
        foreach ($data as $row) {
            $row->image = ($row->image) ? url('/') . '/' . $row->image : null;
        }

        $response = [
            'success' => true,
            'data' => $data,
        ];
        return response()->json($response, 200, [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "title" => ['required'],
            "deskripsi" => ['required'],
            "is_publish" => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Silahkan isi bidang yang Kosong',
                'data'    => $validator->errors()
            ], 401);
        }

        try {
            $path = null;
            if ($request->file('image')) {
                $ext = explode(".", $request->file('image')->getClientOriginalName());
                $name = 'foto_artikel_' . time() . '.' . $ext[count($ext) - 1];
                $path = 'uploads/artikel/' . $name;
                $request->file('image')->move(public_path('uploads/artikel'), $name);
            }

            DB::beginTransaction();
            $artikel = Artikel::create([
                'title' => $request->title,
                'deskripsi' => $request->deskripsi,
                'is_publish' => $request->is_publish, // PUBLISHED, ARCHIVED
                'status' => '1',
                'image' => $path,
            ]);

            DB::commit();
            $response = [
                'success' => true,
                'message' => 'Data berhasil disimpan !',
                'data' => $artikel,
            ];

            return response()->json($response, 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->errorInfo,
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $artikel = Artikel::where([
                'id' => $id,
                'status' => 1
            ])->first();

            if (is_null($artikel)) {
                return response()->json([
                    "success" => false,
                    "message" => "Data tidak ditemukan !",
                ]);
            }
            $artikel->image = ($artikel->image) ? url('/') . '/' . $artikel->image : null;

            $response = [
                "success" => true,
                "data" => $artikel,
            ];
            return response()->json($response, 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errorInfo,
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //validate data
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'title' => 'required',
            'deskripsi' => 'required',
            'is_publish' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Silahkan isi bidang yang Kosong',
                'data'    => $validator->errors()
            ], 401);
        }

        try {
            $id = $request->id;
            $artikel = Artikel::find($id);
            if (is_null($artikel)) {
                return response()->json([
                    "success" => false,
                    "message" => "Data tidak ditemukan !",
                ]);
            }

            $path = $artikel->image;
            if ($request->file('image')) {
                // Save image new image
                $ext = explode(".", $request->file('image')->getClientOriginalName());
                $name = 'foto_artikel_' . time() . '.' . $ext[count($ext) - 1];
                $path = 'uploads/artikel/' . $name;
                $request->file('image')->move(public_path('uploads/artikel'), $name);
            
                // Delete file
                if($artikel->image!=""){
                    $pathFileLama = './'.$artikel->image;
                    if(file_exists($pathFileLama)){
                        File::delete($artikel->image);
                    } 
                }
            }

            DB::beginTransaction();
            $artikel->update([
                'title' => $request->title,
                'deskripsi' => $request->deskripsi,
                'is_publish' => $request->is_publish, // PUBLISHED, ARCHIVED
                'image' => $path,
            ]);

            DB::commit();
            $response = [
                "success" => true,
                "message" => "Data berhasil diubah !",
                "data" => $artikel,
            ];
            return response()->json($response, 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->errorInfo,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            // Soft Delete
            $artikel = Artikel::find($id);
            if (is_null($artikel)) {
                return response()->json([
                    "success" => false,
                    "message" => "Data tidak ditemukan !",
                ]);
            }

            DB::beginTransaction();
            $artikel->update([
                'status' => '0',
            ]);
            DB::commit();

            return response()->json([
                "success" => true,
                "message" => "Data berhasil dihapus !",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->errorInfo,
            ]);
        }
    }
}
