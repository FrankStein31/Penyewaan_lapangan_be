<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriLap;
use Illuminate\Support\Facades\Log;

class KategoriLapController extends Controller
{
    public function index()
    {
        try {
            Log::info('Mencoba mendapatkan semua kategori');
            
            $kategori = KategoriLap::all();
            Log::info('Hasil query kategori: ', ['count' => $kategori->count(), 'data' => $kategori->toArray()]);
            
            if($kategori->isEmpty()) {
                Log::info('Data kategori kosong');
                return response()->json([
                    'status' => false,
                    'message' => 'Data kategori tidak ditemukan',
                    'data' => null
                ], 404);
            }
            
            Log::info('Berhasil mendapatkan data kategori');
            return response()->json([
                'status' => true,
                'message' => 'Data kategori berhasil diambil',
                'data' => $kategori
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error dalam KategoriLapController@index: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
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
            $kategori = KategoriLap::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Kategori berhasil ditambahkan',
                'data' => $kategori
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menambahkan kategori: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $kategori = KategoriLap::findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'Data kategori ditemukan',
                'data' => $kategori
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data kategori tidak ditemukan',
                'data' => null
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $kategori = KategoriLap::findOrFail($id);
            $kategori->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Kategori berhasil diupdate',
                'data' => $kategori
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengupdate kategori: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $kategori = KategoriLap::findOrFail($id);
            $kategori->delete();
            return response()->json([
                'status' => true,
                'message' => 'Kategori berhasil dihapus',
                'data' => null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus kategori: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
