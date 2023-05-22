<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use File;

class ProjectController extends Controller
{
    protected $projectModel;
    public function __construct()
    {
        $this->projectModel = new Project();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $page = $request->get('page');
            $limit = $request->get('limit');
            $sortBy = $request->get('sortby');
            $sortType = $request->get('sorttype');
            $q = ($request->get('q') != "") ? $request->get('q') : "";
            $q = str_replace(" ", "%", $q);
            $columnMap = $this->projectModel->columnMap();

            // Filter Pagination
            $filter = array(
                'q' => $q,
                'page' => $page,
                'limit' => $limit,
                'sortby' => (isset($columnMap[$sortBy])) ? $columnMap[$sortBy] : 'created_at',
                'sorttype' => ($sortType != "") ? $sortType : 'desc',
                'idJenisProject' => ($request->get('idJenisProject') != "") ? $request->get('idJenisProject') : "",
            );

            $totalData = $this->projectModel->getCountProject($filter);
            $data = $this->projectModel->getListProject($filter);
            foreach ($data as $row) {
                $row->foto = ($row->foto) ? url('/') . '/' . $row->foto : null;
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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errorInfo,
            ]);
        }
    }

    public function getAll(Request $request)
    {
        $data = $this->projectModel->getAllProject();
        foreach ($data as $row) {
            $row->foto = ($row->foto) ? url('/') . '/' . $row->foto : null;
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
            'nama' => 'required',
            'id_jenis_project' => 'required',
            'id_client' => 'required',
            'tanggal' => 'required',
            'alamat' => 'required',
            'scope' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Silahkan isi bidang yang Kosong',
                'data'    => $validator->errors()
            ], 401);
        }

        try {
            DB::beginTransaction();
            $project = Project::create([
                'nama' => $request->nama,
                'id_jenis_project' => $request->id_jenis_project,
                'id_client' => $request->id_client,
                'tanggal' => $request->tanggal,
                'alamat' => $request->alamat,
                'scope' => $request->scope,
                'keterangan' => $request->keterangan,
                'status_project' => $request->status_project,
            ]);

            DB::commit();
            $response = [
                'success' => true,
                'message' => 'Data berhasil disimpan !',
                'data' => $project,
            ];

            return response()->json($response, 200);
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
            $project = $this->projectModel->getByID($id)->first();
            if (is_null($project)) {
                return response()->json([
                    "success" => false,
                    "message" => "Data tidak ditemukan !",
                ]);
            }

            $fotoProject = ProjectImage::select('id', 'id_project', 'image as foto')->where('id_project', $id)->get();
            foreach ($fotoProject as $row) {
                $row->foto = ($row->foto) ? url('/') . '/' . $row->foto : null;
            }
            $project->project_image = $fotoProject;

            return response()->json([
                "success" => true,
                "data" => $project,
            ], 200, [], JSON_UNESCAPED_SLASHES);
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
    public function update(Request $request, $id)
    {
        //validate data
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'id_jenis_project' => 'required',
            'id_client' => 'required',
            'tanggal' => 'required',
            'alamat' => 'required',
            'scope' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Silahkan isi bidang yang Kosong',
                'data'    => $validator->errors()
            ], 401);
        }

        try {
            $project = Project::find($id);
            if (is_null($project)) {
                return response()->json([
                    "success" => false,
                    "message" => "Data tidak ditemukan !",
                ]);
            }

            DB::beginTransaction();
            $project->update([
                'nama' => $request->nama,
                'id_jenis_project' => $request->id_jenis_project,
                'id_client' => $request->id_client,
                'tanggal' => $request->tanggal,
                'alamat' => $request->alamat,
                'scope' => $request->scope,
                'keterangan' => $request->keterangan,
                'status_project' => $request->status_project,
            ]);

            DB::commit();
            return response()->json([
                "success" => true,
                "message" => "Data berhasil diubah !",
                "data" => $project,
            ]);
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
            $project = Project::find($id);
            if (is_null($project)) {
                return response()->json([
                    "success" => false,
                    "message" => "Data tidak ditemukan !",
                ]);
            }

            DB::beginTransaction();
            $project->update([
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

    /**
     * Upload Image
     */
    public function uploadImage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_project' => 'required',
                'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silahkan isi bidang yang Kosong',
                    'data'    => $validator->errors()
                ], 401);
            }

            $ext = explode(".", $request->file('file')->getClientOriginalName());
            $name = 'foto_project_' . time() . '.' . $ext[count($ext) - 1];
            $path = 'uploads/project/' . $name;
            $request->file('file')->move(public_path('uploads/project'), $name);

            DB::beginTransaction();
            ProjectImage::create([
                'id_project' => $request->id_project,
                'image' => $path
            ]);
            DB::commit();
            return response()->json([
                "success" => true,
                "message" => "Upload foto project berhasil !",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->errorInfo,
            ]);
        }
    }

    /**
     * Get List Foto Project
     */
    public function getImageProject(Request $request)
    {
        try {
            $id_project = $request->id_project;
            $project = Project::where([
                'id' => $id_project,
                'status' => 1
            ])->first();

            if (is_null($project)) {
                return response()->json([
                    "success" => false,
                    "message" => "Data tidak ditemukan !",
                ]);
            }

            $fotoProject = ProjectImage::select('id', 'id_project', 'image as foto')->where('id_project', $id_project)->get();
            foreach ($fotoProject as $row) {
                $row->foto = ($row->foto) ? url('/') . '/' . $row->foto : null;
            }

            return response()->json([
                "success" => true,
                "data" => $fotoProject,
            ], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errorInfo,
            ]);
        }
    }

    /**
     * Delete Image
     */
    public function deleteImage($id)
    {
        try {
            // Soft Delete
            $projectImg = ProjectImage::find($id);
            if (is_null($projectImg)) {
                return response()->json([
                    "success" => false,
                    "message" => "Data tidak ditemukan !",
                ]);
            }

            // hapus file
            File::delete($projectImg->image);

            DB::beginTransaction();
            ProjectImage::where('id', $id)->delete();
            DB::commit();
            return response()->json([
                "success" => true,
                "message" => "Foto project berhasil dihapus !",
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
