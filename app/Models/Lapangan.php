<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lapangan extends Model
{
    protected $table = 'lapangan';
    protected $fillable = [
        'nama',
        'kapasitas',
        'deskripsi',
        'harga',
        'kategori_id',
        'status'
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriLap::class, 'kategori_id');
    }

    public function fasilitas()
    {
        return $this->belongsToMany(Fasilitas::class, 'fasilitas_lapangan');
    }
}
