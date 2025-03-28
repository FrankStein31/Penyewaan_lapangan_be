<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PembayaranController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $pembayaran = Pembayaran::with('pemesanan')->get();
            
            return response()->json([
                'status' => true,
                'message' => 'Data pembayaran berhasil diambil',
                'data' => $pembayaran
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_pemesanan' => 'required|exists:pemesanan,id_pemesanan',
                'metode' => 'required|in:transfer,midtrans',
                'bukti_transfer' => 'required_if:metode,transfer|nullable|image|mimes:jpeg,png,jpg|max:2048',
                'status' => 'required|in:menunggu verifikasi,belum dibayar,ditolak,diverifikasi'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->all();

            // Handle file upload
            if ($request->hasFile('bukti_transfer')) {
                $file = $request->file('bukti_transfer');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('bukti_transfer'), $fileName);
                $data['bukti_transfer'] = 'bukti_transfer/' . $fileName;
            }

            $pembayaran = Pembayaran::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Pembayaran berhasil dibuat',
                'data' => $pembayaran
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal membuat pembayaran: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $pembayaran = Pembayaran::with('pemesanan')->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Data pembayaran ditemukan',
                'data' => $pembayaran
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data pembayaran tidak ditemukan',
                'data' => null
            ], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $pembayaran = Pembayaran::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'id_pemesanan' => 'exists:pemesanan,id_pemesanan',
                'metode' => 'in:transfer,midtrans',
                'bukti_transfer' => 'required_if:metode,transfer|nullable|image|mimes:jpeg,png,jpg|max:2048',
                'status' => 'in:menunggu verifikasi,belum dibayar,ditolak,diverifikasi'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Ambil data yang akan diupdate
            $updateData = [];
            
            if ($request->has('id_pemesanan')) {
                $updateData['id_pemesanan'] = $request->id_pemesanan;
            }
            if ($request->has('metode')) {
                $updateData['metode'] = $request->metode;
            }
            if ($request->has('status')) {
                $updateData['status'] = $request->status;
            }

            // Handle file upload
            if ($request->hasFile('bukti_transfer')) {
                // Hapus file lama jika ada
                if ($pembayaran->bukti_transfer && file_exists(public_path($pembayaran->bukti_transfer))) {
                    unlink(public_path($pembayaran->bukti_transfer));
                }

                $file = $request->file('bukti_transfer');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('bukti_transfer'), $fileName);
                $updateData['bukti_transfer'] = 'bukti_transfer/' . $fileName;
            }

            $pembayaran->update($updateData);

            return response()->json([
                'status' => true,
                'message' => 'Pembayaran berhasil diupdate',
                'data' => $pembayaran->fresh()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengupdate pembayaran: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $pembayaran = Pembayaran::findOrFail($id);
            $pembayaran->delete();

            return response()->json([
                'status' => true,
                'message' => 'Pembayaran berhasil dihapus',
                'data' => null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus pembayaran: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}