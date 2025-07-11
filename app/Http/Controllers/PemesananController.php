<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use App\Models\Lapangan;
use App\Models\StatusLapangan;
use App\Models\Sesi;
use App\Models\Pembayaran;
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

        if ($user && $user->role === 'admin') {
            $pemesanan = Pemesanan::with(['user', 'lapangan', 'pembayaran'])->get();
        } else if ($user) {
            $pemesanan = Pemesanan::with(['user', 'lapangan', 'pembayaran'])
                ->where('id_user', $user->id)
                ->get();
        } else {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Ambil data sesi manual
        $result = $pemesanan->map(function($item) {
            $sesi_data = [];
            if (is_array($item->id_sesi) && !empty($item->id_sesi)) {
                $sesi_data = \App\Models\Sesi::whereIn('id_jam', $item->id_sesi)->get();
            }
            return [
                'id_pemesanan'   => $item->id_pemesanan,
                'id'             => $item->id_pemesanan,
                'booking_id'     => $item->id_pemesanan,
                'nama_pelanggan' => $item->nama_pelanggan ?? $item->user->nama ?? '-',
                'email'          => $item->email ?? $item->user->email ?? '-',
                'no_hp'          => $item->no_hp ?? $item->user->no_hp ?? '-',
                'lapangan'       => $item->lapangan,
                'tanggal'        => $item->tanggal,
                'status'         => $item->status,
                'catatan'        => $item->catatan,
                'total_harga'    => $item->total_harga,
                'sesi'           => $sesi_data,
                'created_at'     => $item->created_at,
                'updated_at'     => $item->updated_at,
                'pembayaran'     => $item->pembayaran
            ];
        });

        return response()->json([
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
        try {
            \Log::info('Incoming booking request:', $request->all());

            // Validasi input
            $validator = Validator::make($request->all(), [
                'id_lapangan' => 'required|exists:lapangan,id',
                'id_sesi' => 'required|array',
                'id_sesi.*' => 'required|exists:sesis,id_jam',
                'tanggal' => 'required|date',
                'nama_pelanggan' => 'required|string',
                'email' => 'required|email',
                'no_hp' => 'required|string',
                'catatan' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                \Log::error('Validation failed:', $validator->errors()->toArray());
                return response()->json(['message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
            }

            // Ambil data lapangan
            $lapangan = Lapangan::findOrFail($request->id_lapangan);
            
            // Ambil data sesi
            $sesiList = Sesi::whereIn('id_jam', $request->id_sesi)->get();
            if ($sesiList->count() != count($request->id_sesi)) {
                return response()->json(['message' => 'Beberapa sesi tidak ditemukan'], 404);
            }

            // Cek ketersediaan sesi
            foreach ($request->id_sesi as $sesiId) {
                $existingBooking = Pemesanan::where('tanggal', $request->tanggal)
                    ->where('id_lapangan', $request->id_lapangan)
                    ->whereJsonContains('id_sesi', $sesiId)
                    ->where('status', '!=', 'dibatalkan')
                    ->first();

                if ($existingBooking) {
                    return response()->json(['message' => 'Sesi sudah dipesan'], 422);
                }
            }

            // Hitung total harga
            $totalHarga = $lapangan->harga * count($request->id_sesi);

            // Tambah biaya tambahan berdasarkan waktu
            foreach ($sesiList as $sesi) {
                $jamMulai = Carbon::createFromFormat('H:i:s', $sesi->jam_mulai)->format('H:i');
                
                // Biaya tambahan sore (15:00-17:59)
                if ($jamMulai >= '15:00' && $jamMulai < '18:00') {
                    $totalHarga += 25000;
                }
                // Biaya tambahan malam (18:00+)
                elseif ($jamMulai >= '18:00') {
                    $totalHarga += 50000;
                }
            }

            DB::beginTransaction();
            try {
                // Buat pemesanan
                $pemesanan = Pemesanan::create([
                    'id_user' => auth()->id(),
                    'id_lapangan' => $request->id_lapangan,
                    'tanggal' => $request->tanggal,
                    'jam_mulai' => $sesiList->min('jam_mulai'),
                    'jam_selesai' => $sesiList->max('jam_selesai'),
                    'id_sesi' => $request->id_sesi,
                    'status' => 'menunggu verifikasi',
                    'total_harga' => $totalHarga,
                    'nama_pelanggan' => $request->nama_pelanggan,
                    'email' => $request->email,
                    'no_hp' => $request->no_hp,
                    'catatan' => $request->catatan
                ]);

                // Update status lapangan
                foreach ($request->id_sesi as $sesiId) {
                    StatusLapangan::create([
                        'id_lapangan' => $request->id_lapangan,
                        'deskripsi_status' => 'disewa',
                        'tanggal' => $request->tanggal,
                        'id_sesi' => $sesiId
                    ]);
                }

                // Buat pembayaran
                Pembayaran::create([
                    'id_pemesanan' => $pemesanan->id_pemesanan,
                    'metode' => 'transfer',
                    'status' => 'belum dibayar'
                ]);

                DB::commit();
                \Log::info('Booking created successfully:', ['booking_id' => $pemesanan->id_pemesanan]);
                
                return response()->json([
                    'message' => 'Pemesanan berhasil dibuat',
                    'data' => $pemesanan
                ], 201);

            } catch (\Exception $e) {
                DB::rollback();
                \Log::error('Error in transaction:', ['error' => $e->getMessage()]);
                throw $e;
            }

        } catch (\Exception $e) {
            \Log::error('Error creating booking:', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Terjadi kesalahan saat membuat pemesanan'], 500);
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
                'request_data' => $request->all()
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
                ->flatten()
                ->toArray();
            
            // Dapatkan info lapangan
            $lapangan = Lapangan::findOrFail($request->id_lapangan);

            // Dapatkan waktu sekarang
            $now = Carbon::now();
            $selectedDate = Carbon::parse($request->tanggal);
            
            // Siapkan data ketersediaan
            $available = [];
            foreach ($allSesi as $sesi) {
                $sesiTime = Carbon::parse($request->tanggal . ' ' . $sesi->jam_mulai);
                $jamMulai = Carbon::parse($sesi->jam_mulai);
                
                // Cek apakah sesi sudah lewat untuk hari ini
                $isExpired = false;
                if ($selectedDate->isToday()) {
                    $isExpired = $now->gt($sesiTime);
                }
                
                // Cek apakah sesi sudah dipesan
                $isBooked = in_array($sesi->id_jam, $bookedSesi);
                
                // Hitung biaya tambahan berdasarkan waktu
                $additionalCost = 0;
                if ($jamMulai->hour >= 15 && $jamMulai->hour < 18) {
                    // Sesi sore (15:00 - 17:59)
                    $additionalCost = 25000;
                } elseif ($jamMulai->hour >= 18) {
                    // Sesi malam (18:00+)
                    $additionalCost = 50000;
                }
                
                $basePrice = $lapangan->harga;
                $totalPrice = $basePrice + $additionalCost;
                
                $available[] = [
                    'id_sesi' => $sesi->id_jam,
                    'jam_mulai' => $sesi->jam_mulai,
                    'jam_selesai' => $sesi->jam_selesai,
                    'deskripsi' => $sesi->deskripsi,
                    'durasi' => $sesi->getDurasiAttribute(),
                    'harga_dasar' => $basePrice,
                    'biaya_tambahan' => $additionalCost,
                    'total_harga' => $totalPrice,
                    'tersedia' => !$isBooked && !$isExpired,
                    'alasan' => $isExpired ? 'expired' : ($isBooked ? 'booked' : null)
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
            
            // Format data untuk response
            $formattedBookings = $bookings->map(function($booking) {
                // Ambil sesi berdasarkan array id_sesi
                $sesi_data = [];
                if (is_array($booking->id_sesi) && !empty($booking->id_sesi)) {
                    $sesi_data = Sesi::whereIn('id_jam', $booking->id_sesi)->get();
                }

                // Format data booking
                $data = [
                    'id_pemesanan' => $booking->id_pemesanan,
                    'id' => $booking->id_pemesanan,
                    'booking_id' => $booking->id_pemesanan,
                    'nama_pelanggan' => $booking->nama_pelanggan,
                    'email' => $booking->email,
                    'no_hp' => $booking->no_hp,
                    'lapangan' => $booking->lapangan,
                    'tanggal' => $booking->tanggal,
                    'status' => $booking->status,
                    'catatan' => $booking->catatan,
                    'total_harga' => $booking->total_harga,
                    'sesi' => $sesi_data,
                    'created_at' => $booking->created_at,
                    'updated_at' => $booking->updated_at
                ];

                // Tambahkan data pembayaran jika ada
                if ($booking->pembayaran->isNotEmpty()) {
                    $pembayaran = $booking->pembayaran->first();
                    $data['pembayaran'] = [
                        'id_pembayaran' => $pembayaran->id_pembayaran,
                        'metode' => $pembayaran->metode,
                        'status' => $pembayaran->status,
                        'total_bayar' => $pembayaran->total_bayar,
                        'snap_token' => $pembayaran->snap_token,
                        'transaction_id' => $pembayaran->transaction_id,
                        'payment_type' => $pembayaran->payment_type,
                        'transaction_status' => $pembayaran->transaction_status,
                        'transaction_time' => $pembayaran->transaction_time,
                        'payment_code' => $pembayaran->payment_code,
                        'pdf_url' => $pembayaran->pdf_url,
                        'paid_at' => $pembayaran->paid_at
                    ];
                }

                return $data;
            });
            
            return response()->json([
                'success' => true,
                'data' => $formattedBookings
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting user bookings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error mendapatkan data pemesanan: ' . $e->getMessage()
            ], 500);
        }
    }
}
