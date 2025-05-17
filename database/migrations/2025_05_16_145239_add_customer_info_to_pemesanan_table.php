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
        Schema::table('pemesanan', function (Blueprint $table) {
            // Tambahkan kolom baru
            $table->string('nama_pelanggan')->nullable()->after('total_harga');
            $table->string('email')->nullable()->after('nama_pelanggan');
            $table->string('no_hp', 20)->nullable()->after('email');
            $table->text('catatan')->nullable()->after('no_hp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemesanan', function (Blueprint $table) {
            // Hapus kolom jika migrasi di-rollback
            $table->dropColumn(['nama_pelanggan', 'email', 'no_hp', 'catatan']);
        });
    }
};
