<?php

namespace App\Http\Controllers;

use App\Models\Sesi;
use Illuminate\Http\Request;

class SesiController extends Controller
{
    public function index()
    {
        try {
            $sesi = Sesi::all();
            return response()->json([
                'status' => 'success',
                'message' => 'Data sesi berhasil diambil',
                'data' => $sesi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data sesi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'jam_mulai' => 'required|date_format:H:i',
                'jam_selesai' => 'required|date_format:H:i',
                'deskripsi' => 'required|string'
            ]);

            $sesi = Sesi::create($request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'Data sesi berhasil ditambahkan',
                'data' => $sesi
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan data sesi',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function show($id)
    {
        try {
            $sesi = Sesi::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'message' => 'Data sesi ditemukan',
                'data' => $sesi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data sesi tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'jam_mulai' => 'date_format:H:i',
                'jam_selesai' => 'date_format:H:i',
                'deskripsi' => 'string'
            ]);

            $sesi = Sesi::findOrFail($id);
            $sesi->update($request->all());
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data sesi berhasil diperbarui',
                'data' => $sesi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui data sesi',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $sesi = Sesi::findOrFail($id);
            $sesi->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data sesi berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus data sesi',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}