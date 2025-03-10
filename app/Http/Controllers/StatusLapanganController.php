<?php

namespace App\Http\Controllers;

use App\Models\StatusLapangan;
use App\Models\Lapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatusLapanganController extends Controller
{
    public function index()
    {
        try {
            $status = StatusLapangan::with('lapangan')->get();
            return response()->json([
                'status' => true,
                'message' => 'Data status lapangan berhasil diambil',
                'data' => $status
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
            DB::beginTransaction();

            $status = StatusLapangan::create([
                'id_lapangan' => $request->id_lapangan,
                'deskripsi_status' => $request->deskripsi_status
            ]);

            // Update status di tabel lapangan
            $lapangan = Lapangan::find($request->id_lapangan);
            $lapangan->status = ($request->deskripsi_status === 'tersedia') ? 'tersedia' : 'tidak tersedia';
            $lapangan->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Status lapangan berhasil ditambahkan',
                'data' => $status->load('lapangan')
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Gagal menambahkan status: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $status = StatusLapangan::findOrFail($id);
            $status->update([
                'deskripsi_status' => $request->deskripsi_status
            ]);

            // Update status di tabel lapangan
            $lapangan = Lapangan::find($status->id_lapangan);
            $lapangan->status = ($request->deskripsi_status === 'tersedia') ? 'tersedia' : 'tidak tersedia';
            $lapangan->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Status lapangan berhasil diupdate',
                'data' => $status->load('lapangan')
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengupdate status: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $status = StatusLapangan::with('lapangan')->findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'Data status lapangan ditemukan',
                'data' => $status
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data status tidak ditemukan',
                'data' => null
            ], 404);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $status = StatusLapangan::findOrFail($id);
            
            // Reset status lapangan menjadi tersedia
            $lapangan = Lapangan::find($status->id_lapangan);
            $lapangan->status = 'tersedia';
            $lapangan->save();

            $status->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Status lapangan berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus status: ' . $e->getMessage()
            ], 500);
        }
    }
}