<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use Illuminate\Http\Request;

class FasilitasController extends Controller
{
    public function index()
    {
        try {
            $fasilitas = Fasilitas::all();
            if($fasilitas->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data fasilitas tidak ditemukan',
                    'data' => null
                ], 404);
            }
            return response()->json([
                'status' => true,
                'message' => 'Data fasilitas berhasil diambil',
                'data' => $fasilitas
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $fasilitas = Fasilitas::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Fasilitas berhasil ditambahkan',
                'data' => $fasilitas
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menambahkan fasilitas: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $fasilitas = Fasilitas::findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'Data fasilitas ditemukan',
                'data' => $fasilitas
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data fasilitas tidak ditemukan',
                'data' => null
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $fasilitas = Fasilitas::findOrFail($id);
            $fasilitas->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Fasilitas berhasil diupdate',
                'data' => $fasilitas
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengupdate fasilitas: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $fasilitas = Fasilitas::findOrFail($id);
            $fasilitas->delete();
            return response()->json([
                'status' => true,
                'message' => 'Fasilitas berhasil dihapus',
                'data' => null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus fasilitas: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
