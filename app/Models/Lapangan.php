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
        'status',
        'foto'
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriLap::class, 'kategori_id');
    }

    public function status_lapangan()
    {
        return $this->hasMany(StatusLapangan::class, 'id_lapangan');
    }

    public function pemesanan()
    {
        return $this->hasMany(Pemesanan::class, 'id_lapangan');
    }

    public function fasilitas()
    {
        return $this->belongsToMany(Fasilitas::class, 'fasilitas_lapangan');
    }
}
