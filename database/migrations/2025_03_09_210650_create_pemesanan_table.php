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
        // Migrasi dibagi menjadi 2 tahap untuk menangani dependensi
        // Tahap 1: Buat tabel tanpa foreign key ke sesis
        Schema::create('pemesanan', function (Blueprint $table) {
            $table->id('id_pemesanan');
            $table->foreignId('id_user')->constrained('users', 'id')->onDelete('cascade');
            $table->foreignId('id_lapangan')->constrained('lapangan', 'id')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->unsignedBigInteger('id_sesi')->nullable();
            $table->enum('status', ['menunggu verifikasi', 'diverifikasi', 'ditolak', 'dibatalkan', 'selesai'])->default('menunggu verifikasi');
            $table->decimal('total_harga', 10, 2)->default(0);
            $table->string('nama_pelanggan')->nullable();
            $table->string('email')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->text('catatan')->nullable();
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
