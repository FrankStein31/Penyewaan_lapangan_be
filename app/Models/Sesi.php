<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sesi extends Model
{
    protected $primaryKey = 'id_jam';
    protected $fillable = ['jam_mulai', 'jam_selesai', 'deskripsi'];
}