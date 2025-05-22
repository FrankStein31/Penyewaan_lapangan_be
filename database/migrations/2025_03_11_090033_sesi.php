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
        Schema::create('sesis', function (Blueprint $table) {
            $table->id('id_jam');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('deskripsi');
            $table->foreignId('hari_id')->nullable()->constrained('hari')->nullOnDelete();
            $table->timestamps();
        });

        // Tahap 2: Tambahkan foreign key ke tabel pemesanan yang sudah dibuat
        Schema::table('pemesanan', function (Blueprint $table) {
            if (Schema::hasColumn('pemesanan', 'id_sesi')) {
                $table->foreign('id_sesi')->references('id_jam')->on('sesis')->onDelete('set null');
            }
        });

        // Pastikan status_lapangan bisa mereferensikan sesi
        Schema::table('status_lapangan', function (Blueprint $table) {
            if (!Schema::hasColumn('status_lapangan', 'id_sesi')) {
                $table->unsignedBigInteger('id_sesi')->nullable()->after('tanggal');
                $table->foreign('id_sesi')->references('id_jam')->on('sesis')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove foreign keys first
        Schema::table('pemesanan', function (Blueprint $table) {
            $table->dropForeign(['id_sesi']);
        });

        Schema::table('status_lapangan', function (Blueprint $table) {
            if (Schema::hasColumn('status_lapangan', 'id_sesi')) {
                $table->dropForeign(['id_sesi']);
                $table->dropColumn('id_sesi');
            }
        });

        Schema::dropIfExists('sesis');
    }
};