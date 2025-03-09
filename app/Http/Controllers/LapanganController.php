<?php

namespace App\Http\Controllers;

use App\Models\Lapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LapanganController extends Controller
{
    public function index()
    {
        try {
            $lapangan = Lapangan::with(['kategori', 'fasilitas'])->get();
            return response()->json([
                'status' => true,
                'message' => 'Data lapangan berhasil diambil',
                'data' => $lapangan
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
            $validator = Validator::make($request->all(), [
                'nama' => 'required|string',
                'kapasitas' => 'required|integer',
                'deskripsi' => 'required|string',
                'harga' => 'required|numeric',
                'kategori_id' => 'required|exists:kategori_laps,id',
                'fasilitas' => 'required|array',
                'fasilitas.*' => 'exists:fasilitas,id',
                'status' => 'required|in:tersedia,tidak tersedia'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $lapangan = Lapangan::create($request->except('fasilitas'));
            $lapangan->fasilitas()->attach($request->fasilitas);

            return response()->json([
                'status' => true,
                'message' => 'Lapangan berhasil ditambahkan',
                'data' => $lapangan->load(['kategori', 'fasilitas'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menambahkan lapangan: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $lapangan = Lapangan::with(['kategori', 'fasilitas'])->findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'Data lapangan ditemukan',
                'data' => $lapangan
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data lapangan tidak ditemukan',
                'data' => null
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $lapangan = Lapangan::findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'nama' => 'string',
                'kapasitas' => 'integer',
                'deskripsi' => 'string',
                'harga' => 'numeric',
                'kategori_id' => 'exists:kategori_laps,id',
                'fasilitas' => 'array',
                'fasilitas.*' => 'exists:fasilitas,id',
                'status' => 'in:tersedia,tidak tersedia'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $lapangan->update($request->except('fasilitas'));
            
            if ($request->has('fasilitas')) {
                $lapangan->fasilitas()->sync($request->fasilitas);
            }

            return response()->json([
                'status' => true,
                'message' => 'Lapangan berhasil diupdate',
                'data' => $lapangan->load(['kategori', 'fasilitas'])
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengupdate lapangan: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $lapangan = Lapangan::findOrFail($id);
            $lapangan->fasilitas()->detach();
            $lapangan->delete();
            
            return response()->json([
                'status' => true,
                'message' => 'Lapangan berhasil dihapus',
                'data' => null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus lapangan: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
