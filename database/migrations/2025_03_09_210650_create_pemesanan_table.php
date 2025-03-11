<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pemesanan', function (Blueprint $table) {
            $table->id('id_pemesanan');
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_lapangan')->constrained('lapangan')->onDelete('cascade');
            $table->foreignId('id_hari')->constrained('hari')->onDelete('cascade');
            $table->json('sesi'); // Menyimpan array sesi dalam format JSON
            $table->enum('status', ['menunggu verifikasi', 'diverifikasi', 'ditolak', 'dibatalkan', 'selesai'])
                ->default('menunggu verifikasi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemesanan');
    }
};
