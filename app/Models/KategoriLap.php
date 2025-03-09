<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriLap extends Model
{
    protected $table = 'kategori_laps';
    protected $fillable = ['nama_kategori', 'deskripsi'];

    // Tambah relasi one-to-many dengan lapangan
    public function lapangan()
    {
        return $this->hasMany(Lapangan::class, 'kategori_id');
    }
}
