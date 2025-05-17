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
        // 1. Tambahkan foreign key hari_id pada tabel sesis
        Schema::table('sesis', function (Blueprint $table) {
            $table->foreignId('hari_id')->nullable()->after('deskripsi')
                ->constrained('hari')->nullOnDelete();
        });

        // 2. Perbarui struktur tabel pemesanan
        Schema::table('pemesanan', function (Blueprint $table) {
            // Ubah sesi dari json menjadi kolom spesifik
            $table->dropColumn('sesi');
            
            // Tambahkan kolom waktu yang spesifik
            $table->time('jam_mulai')->after('tanggal');
            $table->time('jam_selesai')->after('jam_mulai');
            
            // Tambahkan referensi ke tabel sesi
            $table->foreignId('id_sesi')->nullable()->after('jam_selesai')
                ->references('id_jam')->on('sesis')->nullOnDelete();
            
            // Tambahkan kolom untuk total harga
            $table->decimal('total_harga', 10, 2)->default(0)->after('status');
        });

        // 3. Perbarui struktur tabel status_lapangan
        Schema::table('status_lapangan', function (Blueprint $table) {
            $table->date('tanggal')->nullable()->after('deskripsi_status');
            $table->foreignId('id_sesi')->nullable()->after('tanggal')
                ->references('id_jam')->on('sesis')->nullOnDelete();
        });

        // 4. Perbarui struktur tabel pembayaran
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->decimal('total_bayar', 10, 2)->default(0)->after('status');
            $table->string('kode_pembayaran', 100)->nullable()->after('total_bayar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan perubahan pada tabel pembayaran
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn(['total_bayar', 'kode_pembayaran']);
        });

        // Kembalikan perubahan pada tabel status_lapangan
        Schema::table('status_lapangan', function (Blueprint $table) {
            $table->dropForeign(['id_sesi']);
            $table->dropColumn(['tanggal', 'id_sesi']);
        });

        // Kembalikan perubahan pada tabel pemesanan
        Schema::table('pemesanan', function (Blueprint $table) {
            $table->dropForeign(['id_sesi']);
            $table->dropColumn(['jam_mulai', 'jam_selesai', 'id_sesi', 'total_harga']);
            $table->json('sesi')->after('tanggal');
        });

        // Kembalikan perubahan pada tabel sesis
        Schema::table('sesis', function (Blueprint $table) {
            $table->dropForeign(['hari_id']);
            $table->dropColumn('hari_id');
        });
    }
};
