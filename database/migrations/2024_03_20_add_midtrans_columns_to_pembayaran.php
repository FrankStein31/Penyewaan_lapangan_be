<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->string('snap_token')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('transaction_status')->nullable();
            $table->timestamp('transaction_time')->nullable();
            $table->string('payment_code')->nullable();
            $table->string('pdf_url')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->decimal('total_bayar', 10, 2)->nullable();
            $table->string('kode_pembayaran')->nullable();
        });
    }

    public function down()
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn([
                'snap_token',
                'transaction_id', 
                'payment_type',
                'transaction_status',
                'transaction_time',
                'payment_code',
                'pdf_url',
                'paid_at',
                'total_bayar',
                'kode_pembayaran'
            ]);
        });
    }
}; 