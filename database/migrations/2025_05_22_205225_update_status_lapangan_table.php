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
        Schema::table('status_lapangan', function (Blueprint $table) {
            // Ubah kolom id_sesi menjadi json jika belum
            if (Schema::hasColumn('status_lapangan', 'id_sesi')) {
                $table->json('id_sesi')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('status_lapangan', function (Blueprint $table) {
            // Tidak perlu melakukan apa-apa saat rollback
        });
    }
};
