<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hari extends Model
{
    use HasFactory;

    protected $table = 'hari';

    protected $fillable = [
        'nama_hari'
    ];

    // Relasi dengan Pemesanan
    public function pemesanan()
    {
        return $this->hasMany(Pemesanan::class, 'id_hari');
    }
}
