<?php

namespace App\Http\Controllers;

use App\Models\Lapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

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
                'status' => 'required|in:tersedia,tidak tersedia',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Data yang akan disimpan
            $data = $request->except(['fasilitas', 'foto']);
            
            // Upload foto jika ada
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $filename = time() . '_' . $foto->getClientOriginalName();
                $path = $foto->storeAs('lapangan', $filename, 'public');
                $data['foto'] = $path;
            }

            $lapangan = Lapangan::create($data);
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
                'status' => 'in:tersedia,tidak tersedia',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Data yang akan diupdate
            $data = $request->except(['fasilitas', 'foto', '_method']);
            
            // Upload foto baru jika ada
            if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada
                if ($lapangan->foto && Storage::disk('public')->exists($lapangan->foto)) {
                    Storage::disk('public')->delete($lapangan->foto);
                }
                
                $foto = $request->file('foto');
                $filename = time() . '_' . $foto->getClientOriginalName();
                $path = $foto->storeAs('lapangan', $filename, 'public');
                $data['foto'] = $path;
            }

            $lapangan->update($data);
            
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
            
            // Hapus foto jika ada
            if ($lapangan->foto && Storage::disk('public')->exists($lapangan->foto)) {
                Storage::disk('public')->delete($lapangan->foto);
            }
            
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
