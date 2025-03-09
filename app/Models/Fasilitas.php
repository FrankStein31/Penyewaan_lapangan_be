<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{
    protected $table = 'fasilitas';
    protected $fillable = ['nama_fasilitas', 'deskripsi'];

    // Tambah relasi many-to-many dengan lapangan
    public function lapangan()
    {
        return $this->belongsToMany(Lapangan::class, 'fasilitas_lapangan');
    }
}
