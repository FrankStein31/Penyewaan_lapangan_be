<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriLap extends Model
{
    protected $table = 'kategori_laps';
    protected $fillable = ['nama_kategori', 'deskripsi'];
}
