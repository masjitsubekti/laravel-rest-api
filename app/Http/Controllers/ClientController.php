<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Client::where('status', '1')
                    ->orderBy('created_at', 'DESC')
                    ->get();
        $response = [
            'success' => true,
            'data' => $data,
        ];
        return response()->json($response, 200);
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
            "nama" => ['required'],
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
            $client = Client::create([
                'nama' => $request->nama,
                'keterangan' => $request->keterangan,
            ]);
            
            DB::commit();
            $response = [
                'success' => true,
                'message' => 'Data berhasil disimpan !',
                'data' => $client,
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
            $client = Client::where([
                'id' => $id,
                'status' => 1
            ])->first();
            
            if (is_null($client)) {
                return response()->json([
                    "success" => false,
                    "message" => "Data tidak ditemukan !",
                ]);
            }

            return response()->json([
                "success" => true,
                "data" => $client,
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
            'nama' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Silahkan isi bidang yang Kosong',
                'data'    => $validator->errors()
            ], 401);
        }

        try {
            $client = Client::find($id);
            if (is_null($client)) {
                return response()->json([
                    "success" => false,
                    "message" => "Data tidak ditemukan !",
                ]);
            }

            DB::beginTransaction();
            $client->update([
                'nama' => $request->nama,
                'keterangan' => $request->keterangan,
            ]);
            
            DB::commit();
            return response()->json([
                "success" => true,
                "message" => "Data berhasil diubah !",
                "data" => $client,
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
            $client = Client::find($id);
            if (is_null($client)) {
                return response()->json([
                    "success" => false,
                    "message" => "Data tidak ditemukan !",
                ]);
            }

            DB::beginTransaction();
            $client->update([
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
