<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    use HasFactory;

    protected $table = 'pemesanan';
    protected $primaryKey = 'id_pemesanan';

    protected $fillable = [
        'id_user',
        'id_lapangan',
        'id_hari',
        'sesi',
        'status'
    ];

    protected $casts = [
        'sesi' => 'array' // Konversi JSON ke array otomatis
    ];

    // Relasi dengan User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // Relasi dengan Lapangan
    public function lapangan()
    {
        return $this->belongsTo(Lapangan::class, 'id_lapangan');
    }

    // Relasi dengan Hari
    public function hari()
    {
        return $this->belongsTo(Hari::class, 'id_hari');
    }
}
