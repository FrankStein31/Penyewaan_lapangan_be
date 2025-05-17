<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';
    protected $primaryKey = 'id_pembayaran';

    protected $fillable = [
        'id_pemesanan',
        'metode',
        'bukti_transfer',
        'status',
        'total_bayar',
        'kode_pembayaran'
    ];
    
    protected $casts = [
        'total_bayar' => 'decimal:2',
    ];

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class, 'id_pemesanan');
    }
    
    // Accessor untuk format status
    public function getStatusFormatAttribute()
    {
        $status = $this->status;
        
        $format = [
            'menunggu verifikasi' => 'Menunggu Verifikasi',
            'belum dibayar' => 'Belum Dibayar',
            'ditolak' => 'Ditolak',
            'diverifikasi' => 'Diverifikasi',
        ];
        
        return $format[$status] ?? ucfirst($status);
    }
    
    // Accessor untuk format total pembayaran
    public function getTotalFormatAttribute()
    {
        return 'Rp ' . number_format($this->total_bayar, 0, ',', '.');
    }
}