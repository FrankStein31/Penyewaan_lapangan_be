<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KategoriLapController;
use App\Http\Controllers\FasilitasController;
use App\Http\Controllers\LapanganController;
use App\Http\Controllers\PembayaranController;

// Route untuk users
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']); 
Route::get('/users/nim/{nim}', [UserController::class, 'getByNim']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

// Route untuk Kategori Lapangan
Route::get('/kategori-lap', [KategoriLapController::class, 'index']);
Route::post('/kategori-lap', [KategoriLapController::class, 'store']);
Route::get('/kategori-lap/{id}', [KategoriLapController::class, 'show']);
Route::put('/kategori-lap/{id}', [KategoriLapController::class, 'update']);
Route::delete('/kategori-lap/{id}', [KategoriLapController::class, 'destroy']);

// Route untuk Fasilitas
Route::get('/fasilitas', [FasilitasController::class, 'index']);
Route::post('/fasilitas', [FasilitasController::class, 'store']);
Route::get('/fasilitas/{id}', [FasilitasController::class, 'show']);
Route::put('/fasilitas/{id}', [FasilitasController::class, 'update']);
Route::delete('/fasilitas/{id}', [FasilitasController::class, 'destroy']);

// Route untuk Lapangan
Route::get('/lapangan', [LapanganController::class, 'index']);
Route::post('/lapangan', [LapanganController::class, 'store']);
Route::get('/lapangan/{id}', [LapanganController::class, 'show']);
Route::put('/lapangan/{id}', [LapanganController::class, 'update']);
Route::delete('/lapangan/{id}', [LapanganController::class, 'destroy']);

// Route untuk Pembayaran
Route::get('/pembayaran', [PembayaranController::class, 'index']);
Route::post('/pembayaran', [PembayaranController::class, 'store']);
Route::get('/pembayaran/{id}', [PembayaranController::class, 'show']);
Route::put('/pembayaran/{id}', [PembayaranController::class, 'update']);
Route::delete('/pembayaran/{id}', [PembayaranController::class, 'destroy']);

