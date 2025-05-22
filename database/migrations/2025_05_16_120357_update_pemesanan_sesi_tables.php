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
        // Skip migrasi ini karena kolom-kolom sudah ditambahkan pada migrasi pembuatan tabel
        // Migrasi ini dipertahankan untuk kompatibilitas tetapi tidak melakukan perubahan
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak melakukan apa-apa pada saat rollback
    }
};
