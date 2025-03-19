<?php

namespace App\Http\Controllers;

use App\Models\Hari;
use Illuminate\Http\Request;
use Exception;

class HariController extends Controller
{
    public function index()
    {
        try {
            $hari = Hari::all();
            if($hari->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data hari tidak ditemukan',
                    'data' => []
                ], 404);
            }
            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengambil data hari',
                'data' => $hari
            ], 200);
        } catch(Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_hari' => 'required|unique:hari,nama_hari'
            ], [
                'nama_hari.required' => 'Nama hari wajib diisi',
                'nama_hari.unique' => 'Nama hari sudah ada'
            ]);

            $hari = Hari::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Berhasil menambah hari',
                'data' => $hari
            ], 201);
        } catch(Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $hari = Hari::find($id);
            if(!$hari) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data hari tidak ditemukan',
                    'data' => null
                ], 404);
            }
            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengambil detail hari',
                'data' => $hari
            ], 200);
        } catch(Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $hari = Hari::find($id);
            if(!$hari) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data hari tidak ditemukan',
                    'data' => null
                ], 404);
            }

            $request->validate([
                'nama_hari' => 'required|unique:hari,nama_hari,'.$id
            ], [
                'nama_hari.required' => 'Nama hari wajib diisi',
                'nama_hari.unique' => 'Nama hari sudah ada'
            ]);

            $hari->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengupdate hari',
                'data' => $hari
            ], 200);
        } catch(Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $hari = Hari::find($id);
            if(!$hari) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data hari tidak ditemukan'
                ], 404);
            }

            $hari->delete();
            return response()->json([
                'status' => true,
                'message' => 'Berhasil menghapus hari'
            ], 200);
        } catch(Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
