<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use App\Models\Lapangan;
use App\Models\StatusLapangan;
use App\Models\Sesi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PemesananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            $pemesanan = Pemesanan::with(['user', 'lapangan', 'pembayaran'])->get();
        } else {
            $pemesanan = Pemesanan::with(['lapangan', 'pembayaran'])
                ->where('id_user', $user->id)
                ->get();
        }
        
        // Tambahkan info hari secara manual ke response
        $result = $pemesanan->map(function($item) {
            $data = $item->toArray();
            $hari = $item->getHariAttribute();
            if ($hari) {
                $data['hari'] = [
                    'id' => $hari->id,
                    'nama_hari' => $hari->nama_hari
                ];
            }
            return $data;
        });
        
        return response()->json([
            'success' => true,
            'message' => 'Daftar pemesanan berhasil diambil',
            'data' => $result
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
            'id_lapangan' => 'required|exists:lapangan,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'sesi' => 'required|array',
            'sesi.*' => 'exists:sesis,id_jam'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cek ketersediaan lapangan untuk hari, tanggal, dan sesi tersebut
        $checkPemesanan = Pemesanan::where('id_lapangan', $request->id_lapangan)
            ->where('tanggal', $request->tanggal)
            ->whereIn('status', ['menunggu verifikasi', 'diverifikasi'])
            ->get();

        foreach ($checkPemesanan as $pemesanan) {
            $sesiTerpesan = $pemesanan->sesi;
            $sesiReq = $request->sesi;
            
            // Cek apakah ada sesi yang bentrok
            $intersect = array_intersect($sesiTerpesan, $sesiReq);
            
            if (count($intersect) > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi lapangan sudah dipesan untuk hari tersebut',
                    'sesi_bentrok' => $intersect
                ], 400);
            }
        }

        // Buat pemesanan baru
        $pemesanan = Pemesanan::create([
            'id_user' => auth()->id(),
            'id_lapangan' => $request->id_lapangan,
            'tanggal' => $request->tanggal,
            'sesi' => $request->sesi,
            'status' => 'menunggu verifikasi'
        ]);

        // Update status lapangan menjadi disewa
        $statusLapangan = StatusLapangan::where('id_lapangan', $request->id_lapangan)->first();
        if ($statusLapangan) {
            $statusLapangan->update(['deskripsi_status' => 'disewa']);
        } else {
            StatusLapangan::create([
                'id_lapangan' => $request->id_lapangan,
                'deskripsi_status' => 'disewa'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pemesanan berhasil dibuat',
            'data' => $pemesanan
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pemesanan = Pemesanan::with(['user', 'lapangan', 'pembayaran'])->find($id);
        
        if (!$pemesanan) {
            return response()->json([
                'success' => false,
                'message' => 'Pemesanan tidak ditemukan'
            ], 404);
        }

        $user = auth()->user();
        if ($user->role !== 'admin' && $pemesanan->id_user !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke pemesanan ini'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail pemesanan',
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
        $pemesanan = Pemesanan::find($id);
        
        if (!$pemesanan) {
            return response()->json([
                'success' => false,
                'message' => 'Pemesanan tidak ditemukan'
            ], 404);
        }

        $user = auth()->user();
        if ($user->role !== 'admin' && $pemesanan->id_user !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengupdate pemesanan ini'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:menunggu verifikasi,diverifikasi,ditolak,dibatalkan,selesai'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $oldStatus = $pemesanan->status;
        $pemesanan->status = $request->status;
        $pemesanan->save();

        // Jika status berubah menjadi dibatalkan atau selesai, kembalikan status lapangan
        if (($request->status === 'dibatalkan' || $request->status === 'selesai') &&
            ($oldStatus === 'menunggu verifikasi' || $oldStatus === 'diverifikasi')) {
            $statusLapangan = StatusLapangan::where('id_lapangan', $pemesanan->id_lapangan)->first();
            if ($statusLapangan) {
                $statusLapangan->update(['deskripsi_status' => 'tersedia']);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Status pemesanan berhasil diupdate',
            'data' => $pemesanan
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pemesanan = Pemesanan::find($id);
        
        if (!$pemesanan) {
            return response()->json([
                'success' => false,
                'message' => 'Pemesanan tidak ditemukan'
            ], 404);
        }

        $user = auth()->user();
        if ($user->role !== 'admin' && $pemesanan->id_user !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk menghapus pemesanan ini'
            ], 403);
        }

        // Jika pemesanan masih aktif, kembalikan status lapangan
        if ($pemesanan->status === 'menunggu verifikasi' || $pemesanan->status === 'diverifikasi') {
            $statusLapangan = StatusLapangan::where('id_lapangan', $pemesanan->id_lapangan)->first();
            if ($statusLapangan) {
                $statusLapangan->update(['deskripsi_status' => 'tersedia']);
            }
        }

        $pemesanan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pemesanan berhasil dihapus'
        ]);
    }
    
    public function checkAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_lapangan' => 'required|exists:lapangan,id',
            'tanggal' => 'required|date|after_or_equal:today'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $sesi = Sesi::all();
        $booked = [];

        $pemesanan = Pemesanan::where('id_lapangan', $request->id_lapangan)
            ->where('tanggal', $request->tanggal)
            ->whereIn('status', ['menunggu verifikasi', 'diverifikasi'])
            ->get();

        foreach ($pemesanan as $pesan) {
            $booked = array_merge($booked, $pesan->sesi);
        }

        $available = [];
        foreach ($sesi as $s) {
            $available[] = [
                'id_jam' => $s->id_jam,
                'jam_mulai' => $s->jam_mulai,
                'jam_selesai' => $s->jam_selesai,
                'deskripsi' => $s->deskripsi,
                'tersedia' => !in_array($s->id_jam, $booked)
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Ketersediaan sesi lapangan untuk tanggal ' . $request->tanggal,
            'tanggal' => $request->tanggal,
            'data' => $available
        ]);
    }
}
