<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KategoriLapController;
use App\Http\Controllers\FasilitasController;
use App\Http\Controllers\LapanganController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\StatusLapanganController;
use App\Http\Controllers\SesiController;
use App\Http\Controllers\HariController;
use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;

// Route yang bisa diakses tanpa login
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Kategori Lapangan (publik)
Route::get('/kategori-lap', [KategoriLapController::class, 'index']);
Route::get('/kategori-lap/{id}', [KategoriLapController::class, 'show']);

// Lapangan (publik)
Route::get('/lapangan', [LapanganController::class, 'index']);
Route::get('/lapangan/{id}', [LapanganController::class, 'show']);

// Sesi (publik)
Route::get('/sesi', [SesiController::class, 'index']);

// Fasilitas (publik)
Route::get('/fasilitas', [FasilitasController::class, 'index']);
Route::get('/fasilitas/{id}', [FasilitasController::class, 'show']);

// Cek ketersediaan lapangan (publik)
Route::get('/pemesanan/check-availability', [PemesananController::class, 'checkAvailability']);

// Midtrans Notification Handler (publik)
Route::post('/payments/midtrans-notification', [PembayaranController::class, 'handleMidtransNotification']);

// Bungkus semua route yang membutuhkan autentikasi
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Route untuk mendapatkan user yang terautentikasi
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    //status lapangan
    Route::get('/status-lapangan', [StatusLapanganController::class, 'index']);
    Route::get('/status-lapangan/{id}', [StatusLapanganController::class, 'show']);
    
    //hari
    Route::get('/hari', [HariController::class, 'index']);
    Route::get('/hari/{id}', [HariController::class, 'show']);
    
    // sesi
    Route::get('/sesi/{id}', [SesiController::class, 'show']);
    
    // Pemesanan
    Route::get('/pemesanan', [PemesananController::class, 'index']);
    Route::post('/pemesanan', [PemesananController::class, 'store']);
    Route::get('/pemesanan/user', [PemesananController::class, 'getUserBookings']);
    Route::get('/pemesanan/{id}', [PemesananController::class, 'show']);
    Route::put('/pemesanan/{id}', [PemesananController::class, 'update']);
    Route::delete('/pemesanan/{id}', [PemesananController::class, 'destroy']);
    
    // Pembayaran
    Route::get('/pembayaran', [PembayaranController::class, 'index']);
    Route::post('/pembayaran/create-transaction', [PembayaranController::class, 'createMidtransTransaction']);
    Route::get('/pembayaran/{id}', [PembayaranController::class, 'show']);
    Route::put('/pembayaran/{id}', [PembayaranController::class, 'update']);
    Route::delete('/pembayaran/{id}', [PembayaranController::class, 'destroy']);
    Route::post('/pemesanan/{id}/payment/update', [PembayaranController::class, 'updatePaymentStatus']);
    
    // Update profile
    Route::put('/users/{id}', [UserController::class, 'update']);

    // Route khusus admin
    Route::middleware('role:admin')->group(function () {
        // Users
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']); 
        Route::get('/users/no_hp/{no_hp}', [UserController::class, 'getByNim']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);

        // Kategori Lapangan (admin only)
        Route::post('/kategori-lap', [KategoriLapController::class, 'store']);
        Route::put('/kategori-lap/{id}', [KategoriLapController::class, 'update']);
        Route::delete('/kategori-lap/{id}', [KategoriLapController::class, 'destroy']);

        // Fasilitas
        Route::post('/fasilitas', [FasilitasController::class, 'store']);
        Route::put('/fasilitas/{id}', [FasilitasController::class, 'update']);
        Route::delete('/fasilitas/{id}', [FasilitasController::class, 'destroy']);

        // Lapangan (admin only)
        Route::post('/lapangan', [LapanganController::class, 'store']);
        Route::put('/lapangan/{id}', [LapanganController::class, 'update']);
        Route::delete('/lapangan/{id}', [LapanganController::class, 'destroy']);

        // Status Lapangan
        Route::post('/status-lapangan', [StatusLapanganController::class, 'store']);
        Route::put('/status-lapangan/{id}', [StatusLapanganController::class, 'update']);
        Route::delete('/status-lapangan/{id}', [StatusLapanganController::class, 'destroy']);

        // Sesi
        Route::post('/sesi', [SesiController::class, 'store']);
        Route::get('/sesi/{id}', [SesiController::class, 'show']);
        Route::put('/sesi/{id}', [SesiController::class, 'update']);
        Route::delete('/sesi/{id}', [SesiController::class, 'destroy']);

        // Hari
        Route::post('/hari', [HariController::class, 'store']);
        Route::get('/hari/{id}', [HariController::class, 'show']);
        Route::put('/hari/{id}', [HariController::class, 'update']);
        Route::delete('/hari/{id}', [HariController::class, 'destroy']);
    });
});
