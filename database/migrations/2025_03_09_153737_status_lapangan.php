<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_lapangan', function (Blueprint $table) {
            $table->id('id_status');
            $table->foreignId('id_lapangan')->constrained('lapangan')->onDelete('cascade');
            $table->enum('deskripsi_status', ['tersedia', 'disewa', 'perbaikan']);
            $table->date('tanggal')->nullable();
            $table->json('id_sesi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_lapangan');
    }
};
