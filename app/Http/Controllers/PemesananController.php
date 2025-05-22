<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use App\Models\Lapangan;
use App\Models\StatusLapangan;
use App\Models\Sesi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PemesananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            $pemesanan = Pemesanan::with(['user', 'lapangan', 'sesi', 'pembayaran'])->get();
        } else {
            $pemesanan = Pemesanan::with(['lapangan', 'sesi', 'pembayaran'])
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
        Log::info('Request pemesanan:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'id_lapangan' => 'required|exists:lapangan,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'id_sesi' => 'required',
            'nama_pelanggan' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'no_hp' => 'nullable|string|max:20',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::error('Validasi gagal: ' . json_encode($validator->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Proses data id_sesi
        $sesiIds = $request->id_sesi;
        // Pastikan id_sesi dalam bentuk array
        if (!is_array($sesiIds)) {
            // Jika hanya satu id (string atau integer), ubah menjadi array
            $sesiIds = [$sesiIds];
        }

        // Validasi semua ID sesi ada di database
        $sesiCount = Sesi::whereIn('id_jam', $sesiIds)->count();
        if ($sesiCount != count($sesiIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Beberapa ID sesi tidak valid',
            ], 422);
        }

        // Ambil semua data sesi terkait
        $sesiList = Sesi::whereIn('id_jam', $sesiIds)->orderBy('jam_mulai')->get();
        if ($sesiList->isEmpty()) {
            Log::error('Sesi tidak ditemukan untuk ID: ' . json_encode($sesiIds));
            return response()->json([
                'success' => false,
                'message' => 'Sesi dengan ID yang diberikan tidak ditemukan',
            ], 404);
        }

        // Tentukan jam mulai (paling awal) dan jam selesai (paling akhir)
        $jamMulai = $sesiList->min('jam_mulai');
        $jamSelesai = $sesiList->max('jam_selesai');
        
        // Hitung total harga berdasarkan harga lapangan dan durasi total
        try {
            $lapangan = Lapangan::findOrFail($request->id_lapangan);
            $hargaPerJam = $lapangan->harga;
            
            // Hitung total durasi dari semua sesi
            $totalDurasi = 0;
            foreach ($sesiList as $sesi) {
                $mulai = Carbon::parse($sesi->jam_mulai);
                $selesai = Carbon::parse($sesi->jam_selesai);
                $durasi = $selesai->diffInHours($mulai);
                $totalDurasi += $durasi;
            }
            
            if (!$totalDurasi) {
                Log::error('Durasi tidak valid: ' . json_encode($sesiList));
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mendapatkan durasi sesi',
                ], 500);
            }
            
            $totalHarga = $hargaPerJam * $totalDurasi;
            
            Log::info('Perhitungan harga: harga/jam=' . $hargaPerJam . ', durasi=' . $totalDurasi . ', total=' . $totalHarga);
        } catch (\Exception $e) {
            Log::error('Error menghitung harga: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghitung harga',
                'error' => $e->getMessage()
            ], 500);
        }

        // Cek ketersediaan lapangan untuk tanggal dan semua sesi tersebut
        $bookedSessions = [];
        foreach ($sesiIds as $sesiId) {
            $checkPemesanan = Pemesanan::where('id_lapangan', $request->id_lapangan)
                ->where('tanggal', $request->tanggal)
                ->whereJsonContains('id_sesi', $sesiId)
                ->whereIn('status', ['menunggu verifikasi', 'diverifikasi'])
                ->first();

            if ($checkPemesanan) {
                $bookedSessions[] = $sesiId;
            }
        }

        if (!empty($bookedSessions)) {
            // Ambil informasi sesi yang sudah dipesan untuk ditampilkan
            $bookedSesiInfo = Sesi::whereIn('id_jam', $bookedSessions)->get(['id_jam', 'jam_mulai', 'jam_selesai', 'deskripsi'])->toArray();
            
            Log::warning('Sesi sudah dipesan: Lapangan=' . $request->id_lapangan . ', Tanggal=' . $request->tanggal . ', Sesi=' . json_encode($bookedSessions));
            return response()->json([
                'success' => false,
                'message' => 'Beberapa sesi lapangan sudah dipesan untuk tanggal tersebut',
                'booked_sessions' => $bookedSesiInfo
            ], 400);
        }

        DB::beginTransaction();
        
        try {
            // Buat pemesanan baru
            $pemesanan = Pemesanan::create([
                'id_user' => auth()->id(),
                'id_lapangan' => $request->id_lapangan,
                'tanggal' => $request->tanggal,
                'jam_mulai' => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'id_sesi' => $sesiIds,
                'status' => 'menunggu verifikasi',
                'total_harga' => $totalHarga,
                'nama_pelanggan' => $request->nama_pelanggan,
                'email' => $request->email,
                'no_hp' => $request->no_hp,
                'catatan' => $request->catatan,
            ]);

            // Update status lapangan menjadi disewa untuk tanggal dan semua sesi tersebut
            $statusLapangan = StatusLapangan::updateOrCreate(
                [
                    'id_lapangan' => $request->id_lapangan,
                    'tanggal' => $request->tanggal,
                ],
                [
                    'deskripsi_status' => 'disewa',
                    'id_sesi' => $sesiIds
                ]
            );
            
            Log::info('StatusLapangan berhasil diupdate: ' . json_encode($statusLapangan));

            // Commit transaksi jika semua operasi berhasil
            DB::commit();
            
            // Load relasi untuk respons
            $pemesanan->load('lapangan');
            // Tambahkan data sesi secara manual karena sekarang menjadi array
            $pemesanan->sesi_info = $sesiList;
            
            return response()->json([
                'success' => true,
                'message' => 'Pemesanan berhasil dibuat',
                'data' => $pemesanan
            ], 201);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            
            Log::error('Error saat membuat pemesanan: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat pemesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pemesanan = Pemesanan::with(['user', 'lapangan', 'sesi', 'pembayaran'])->find($id);
        
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

        // Jika status berubah menjadi dibatalkan atau selesai, update status lapangan
        if (($request->status === 'dibatalkan' || $request->status === 'selesai') &&
            ($oldStatus === 'menunggu verifikasi' || $oldStatus === 'diverifikasi')) {
            
            StatusLapangan::where('id_lapangan', $pemesanan->id_lapangan)
                ->where('tanggal', $pemesanan->tanggal)
                ->where('id_sesi', $pemesanan->id_sesi)
                ->update(['deskripsi_status' => 'tersedia']);
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

        // Jika pemesanan masih aktif, kembalikan status lapangan menjadi tersedia
        if ($pemesanan->status === 'menunggu verifikasi' || $pemesanan->status === 'diverifikasi') {
            StatusLapangan::where('id_lapangan', $pemesanan->id_lapangan)
                ->where('tanggal', $pemesanan->tanggal)
                ->where('id_sesi', $pemesanan->id_sesi)
                ->update(['deskripsi_status' => 'tersedia']);
        }

        $pemesanan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pemesanan berhasil dihapus'
        ]);
    }
    
    public function checkAvailability(Request $request)
    {
        // Log permintaan untuk debugging
        Log::info('Check availability request', $request->all());
        
        $validator = Validator::make($request->all(), [
            'id_lapangan' => 'required|exists:lapangan,id',
            'tanggal' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
                'request_data' => $request->all() // Mengembalikan data yang dikirim untuk membantu debugging
            ], 422);
        }

        try {
            // Dapatkan semua sesi
            $allSesi = Sesi::all();
            
            // Dapatkan sesi yang sudah dipesan
            $bookedSesi = Pemesanan::where('id_lapangan', $request->id_lapangan)
                ->where('tanggal', $request->tanggal)
                ->whereIn('status', ['menunggu verifikasi', 'diverifikasi', 'pending', 'confirmed'])
                ->pluck('id_sesi')
                ->toArray();
            
            // Dapatkan info lapangan
            $lapangan = Lapangan::findOrFail($request->id_lapangan);

            // Siapkan data ketersediaan
            $available = [];
            foreach ($allSesi as $sesi) {
                $available[] = [
                    'id_sesi' => $sesi->id_jam,
                    'jam_mulai' => $sesi->jam_mulai,
                    'jam_selesai' => $sesi->jam_selesai,
                    'deskripsi' => $sesi->deskripsi,
                    'durasi' => $sesi->getDurasiAttribute(),
                    'harga' => $lapangan->harga,
                    'total_harga' => $lapangan->harga * $sesi->getDurasiAttribute(),
                    'tersedia' => !in_array($sesi->id_jam, $bookedSesi)
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Ketersediaan sesi lapangan untuk tanggal ' . $request->tanggal,
                'tanggal' => $request->tanggal,
                'lapangan' => $lapangan->nama,
                'data' => $available
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking availability: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memeriksa ketersediaan lapangan',
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ], 500);
        }
    }

    // Tambahkan method getUserBookings
    public function getUserBookings(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 401);
            }
            
            // Ambil pemesanan user dengan eager loading yang benar
            $bookings = Pemesanan::with(['lapangan', 'pembayaran'])
                ->where('id_user', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Tambahkan data sesi secara manual untuk menghindari masalah eager loading
            $bookings->each(function ($booking) {
                // Ambil sesi berdasarkan array id_sesi
                if (is_array($booking->id_sesi) && !empty($booking->id_sesi)) {
                    $sesi_data = Sesi::whereIn('id_jam', $booking->id_sesi)->get();
                    $booking->setAttribute('sesi_data', $sesi_data);
                } else {
                    $booking->setAttribute('sesi_data', []);
                }
            });
            
            return response()->json([
                'success' => true,
                'data' => $bookings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error mendapatkan data pemesanan: ' . $e->getMessage()
            ], 500);
        }
    }
}
