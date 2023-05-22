<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Slider::where('status', '1')
                    ->orderBy('created_at', 'DESC')->get();
        foreach ($data as $row) {
            $row->image = ($row->image) ? url('/') .'/'. $row->image : null;
        }

        $response = [
            'success' => true,
            'data' => $data,
        ];
        return response()->json($response, 200, [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * Get Artikel Published
     */
    public function getSliderActive()
    {   
        $data = Slider::where([
                        'is_active' => 1,
                        'status' => 1
                    ])
                    ->orderBy('created_at', 'DESC')->get();
        foreach ($data as $row) {
            $row->image = ($row->image) ? url('/') .'/'. $row->image : null;
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
            "is_active" => ['required'],
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
            if($request->image){
                $ext = explode(".", $request->file('image')->getClientOriginalName());
                $name = 'slider_'. time() .'.'. $ext[count($ext)-1];
                $path = 'uploads/slider/'. $name;
                $request->file('image')->move(public_path('uploads/slider'), $name); 
            }

            DB::beginTransaction();
            $slider = Slider::create([
                'title' => $request->title,
                'deskripsi' => $request->deskripsi,
                'color_title' => ($request->color_title) ? $request->color_title : null,
                'color_deskripsi' => ($request->color_deskripsi) ? $request->color_deskripsi : null,
                'is_active' => $request->is_active, 
                'status' => '1',
                'image' => $path,
            ]);
            
            DB::commit();
            $response = [
                'success' => true,
                'message' => 'Data berhasil disimpan !',
                'data' => $slider,
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
            $slider = Slider::where([
                'id' => $id,
                'status' => 1
            ])->first();
            
            if (is_null($slider)) {
                return response()->json([
                    "success" => false,
                    "message" => "Data tidak ditemukan !",
                ]);
            }

            return response()->json([
                "success" => true,
                "data" => $slider,
            ]);
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
            "title" => 'required',
            "deskripsi" => 'required',
            "is_active" => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Silahkan isi bidang yang Kosong',
                'data'    => $validator->errors()
            ], 401);
        }

        try {
            $slider = Slider::find($id);
            if (is_null($slider)) {
                return response()->json([
                    "success" => false,
                    "message" => "Data tidak ditemukan !",
                ]);
            }

            DB::beginTransaction();
            $slider->update([
                'title' => $request->title,
                'deskripsi' => $request->deskripsi,
                'color_title' => ($request->color_title) ? $request->color_title : null,
                'color_deskripsi' => ($request->color_deskripsi) ? $request->color_deskripsi : null,
                'is_active' => $request->is_active, 
            ]);
            
            DB::commit();
            return response()->json([
                "success" => true,
                "message" => "Data berhasil diubah !",
                "data" => $slider,
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
            $slider = Slider::find($id);
            if (is_null($slider)) {
                return response()->json([
                    "success" => false,
                    "message" => "Data tidak ditemukan !",
                ]);
            }

            DB::beginTransaction();
            $slider->update([
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
