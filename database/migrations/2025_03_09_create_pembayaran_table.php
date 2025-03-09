<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id('id_pembayaran');
            $table->foreignId('id_pemesanan')->constrained('pemesanan');
            $table->enum('metode', ['transfer', 'midtrans']);
            $table->string('bukti_transfer')->nullable();
            $table->enum('status', ['menunggu verifikasi', 'belum dibayar', 'ditolak', 'diverifikasi']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembayaran');
    }
}; 