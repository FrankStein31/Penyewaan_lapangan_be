<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Midtrans\Config;
use Midtrans\Snap;

class PembayaranController extends Controller
{
    public function __construct()
    {
        // Set konfigurasi Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-MNo3xTYokgclNykKFrjUtVDg');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

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

    public function createMidtransTransaction(Request $request): JsonResponse
    {
        try {
            // Log request untuk debugging
            \Log::info('Midtrans transaction request:', $request->all());

            // Validasi request
            $validator = Validator::make($request->all(), [
                'booking_id' => 'required',
                'amount' => 'required|numeric|min:1',
                'customer_details' => 'required|array',
                'customer_details.first_name' => 'required|string',
                'customer_details.email' => 'required|email',
                'customer_details.phone' => 'required|string'
            ]);

            if ($validator->fails()) {
                \Log::error('Validation failed:', $validator->errors()->toArray());
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cari pemesanan
            $pemesanan = Pemesanan::find($request->booking_id);
            if (!$pemesanan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Pemesanan tidak ditemukan',
                ], 404);
            }

            // Generate order ID yang unik
            $orderId = 'BOOK-' . $pemesanan->id_pemesanan . '-' . time();
            
            // Buat parameter untuk Midtrans
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $request->amount,
                ],
                'customer_details' => [
                    'first_name' => $request->customer_details['first_name'],
                    'email' => $request->customer_details['email'],
                    'phone' => $request->customer_details['phone']
                ],
                'enabled_payments' => [
                    'credit_card', 'bca_va', 'bni_va', 'bri_va', 'gopay', 'shopeepay'
                ],
            ];

            // Log params untuk debugging
            \Log::info('Midtrans params:', $params);

            try {
                // Dapatkan Snap Token dari Midtrans
                $snapToken = Snap::getSnapToken($params);
                \Log::info('Snap token generated:', ['token' => $snapToken]);
            } catch (\Exception $e) {
                \Log::error('Error getting snap token:', ['error' => $e->getMessage()]);
                throw new \Exception('Gagal mendapatkan token dari Midtrans: ' . $e->getMessage());
            }

            // Simpan atau update data pembayaran
            $pembayaran = Pembayaran::updateOrCreate(
                ['id_pemesanan' => $request->booking_id],
                [
                    'metode' => 'midtrans',
                    'status' => 'belum dibayar',
                    'total_bayar' => $request->amount,
                    'snap_token' => $snapToken,
                ]
            );

            return response()->json([
                'status' => true,
                'message' => 'Snap token berhasil dibuat',
                'data' => [
                    'snap_token' => $snapToken,
                    'payment' => $pembayaran
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in createMidtransTransaction:', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Gagal membuat transaksi: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function handleMidtransNotification(Request $request): JsonResponse
    {
        try {
            \Log::info('Midtrans notification received:', $request->all());
            
            $notif = new \Midtrans\Notification();
            
            $transaction = $notif->transaction_status;
            $type = $notif->payment_type;
            $orderId = $notif->order_id;
            $fraud = $notif->fraud_status;

            \Log::info('Parsed notification:', [
                'transaction_status' => $transaction,
                'payment_type' => $type,
                'order_id' => $orderId,
                'fraud_status' => $fraud
            ]);

            // Extract booking ID from order_id (format: BOOK-{id}-{timestamp})
            $bookingId = explode('-', $orderId)[1];
            
            $pembayaran = Pembayaran::where('id_pemesanan', $bookingId)->first();
            if (!$pembayaran) {
                throw new \Exception('Pembayaran tidak ditemukan');
            }

            // Update data pembayaran
            $statusPembayaran = 'belum dibayar';
            $statusPemesanan = 'menunggu verifikasi';

            // Cek status transaksi
            if ($transaction == 'capture' || $transaction == 'settlement') {
                // Jika pembayaran berhasil
                $statusPembayaran = 'diverifikasi';
                $statusPemesanan = 'diverifikasi';
            } else if ($transaction == 'pending') {
                $statusPembayaran = 'belum dibayar';
                $statusPemesanan = 'menunggu verifikasi';
            } else if (in_array($transaction, ['deny', 'expire', 'cancel'])) {
                $statusPembayaran = 'ditolak';
                $statusPemesanan = 'ditolak';
            }

            \Log::info('Status akan diupdate:', [
                'status_pembayaran' => $statusPembayaran,
                'status_pemesanan' => $statusPemesanan
            ]);

            // Update pembayaran
            $pembayaran->update([
                'transaction_status' => $transaction,
                'payment_type' => $type,
                'transaction_id' => $notif->transaction_id,
                'status' => $statusPembayaran,
                'transaction_time' => date('Y-m-d H:i:s', strtotime($notif->transaction_time)),
                'payment_code' => $notif->payment_code ?? null,
                'pdf_url' => $notif->pdf_url ?? null,
                'paid_at' => in_array($transaction, ['capture', 'settlement']) ? date('Y-m-d H:i:s') : null
            ]);

            // Update status pemesanan
            $pemesanan = Pemesanan::find($bookingId);
            if ($pemesanan) {
                $pemesanan->update(['status' => $statusPemesanan]);
                \Log::info('Status pemesanan berhasil diupdate:', [
                    'id_pemesanan' => $pemesanan->id_pemesanan,
                    'status_baru' => $statusPemesanan
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Notifikasi berhasil diproses',
                'data' => [
                    'transaction_status' => $transaction,
                    'payment_status' => $statusPembayaran,
                    'booking_status' => $statusPemesanan
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error processing Midtrans notification:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => false,
                'message' => 'Gagal memproses notifikasi: ' . $e->getMessage()
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

    public function updatePaymentStatus(Request $request, $id): JsonResponse
    {
        try {
            \Log::info('Update payment status request:', [
                'booking_id' => $id,
                'data' => $request->all()
            ]);

            // Cari pemesanan
            $pemesanan = Pemesanan::find($id);
            if (!$pemesanan) {
                throw new \Exception('Pemesanan tidak ditemukan');
            }

            // Cari pembayaran
            $pembayaran = Pembayaran::where('id_pemesanan', $id)->first();
            if (!$pembayaran) {
                throw new \Exception('Data pembayaran tidak ditemukan');
            }

            // Update status pembayaran
            $pembayaran->update([
                'status' => $request->payment_status ?? 'diverifikasi',
                'transaction_status' => $request->transaction_status,
                'transaction_id' => $request->transaction_id,
                'payment_type' => $request->payment_type,
                'paid_at' => $request->paid_at ?? now()
            ]);

            // Update status pemesanan
            $pemesanan->update([
                'status' => $request->status ?? 'diverifikasi'
            ]);

            \Log::info('Payment status updated successfully:', [
                'booking_id' => $id,
                'payment_status' => $pembayaran->status,
                'booking_status' => $pemesanan->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status pembayaran berhasil diupdate',
                'data' => [
                    'payment' => $pembayaran,
                    'booking' => $pemesanan
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating payment status:', [
                'booking_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate status pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }
}