<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PemesananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pemesanan = Pemesanan::all();

        return response()->json([
            'success' => true,
            'message' => 'Data Pemesanan berhasil diambil',
            'data' => $pemesanan
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required|exists:users,id',
            'id_lapangan' => 'required|exists:lapangan,id',
            'id_hari' => 'required|exists:hari,id',
            // 'sesi' => 'required|array',
            'status' => 'required|in:menunggu verifikasi,diverifikasi,ditolak,dibatalkan,selesai'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi error',
                'errors' => $validator->errors()
            ], 422);
        }

        $pemesanan = Pemesanan::create([
            'id_user' => $request->id_user,
            'id_lapangan' => $request->id_lapangan,
            'id_hari' => $request->id_hari,
            'sesi' => json_encode($request->sesi), // Simpan sesi sebagai JSON
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pemesanan berhasil dibuat',
            'data' => $pemesanan
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pemesanan = Pemesanan::find($id);

        if (!$pemesanan) {
            return response()->json([
                'success' => false,
                'message' => 'Pemesanan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pemesanan berhasil didapat',
            'data' => $pemesanan
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $pemesanan = Pemesanan::findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'id_user' => 'exists:users,id',
                'id_lapangan' => 'exists:lapangan,id',
                'id_hari' => 'exists:hari,id',
                'sesi' => 'array',
                'status' => 'in:menunggu verifikasi,diverifikasi,ditolak,dibatalkan,selesai'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = $request->only(['id_user', 'id_lapangan', 'id_hari', 'status']);
            if ($request->has('sesi')) {
                $updateData['sesi'] = json_encode($request->sesi);
            }
            
            $pemesanan->update($updateData);

            return response()->json([
                'status' => true,
                'message' => 'Pemesanan berhasil diupdate',
                'data' => $pemesanan->fresh()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengupdate pemesanan: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pemesanan = Pemesanan::find($id);

        if (!$pemesanan) {
            return response()->json([
                'success' => false,
                'message' => 'Pemesanan tidak ditemukan'
            ], 404);
        }

        $pemesanan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pemesanan berhasil dihapus'
        ]);
    }
}
