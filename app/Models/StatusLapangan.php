<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusLapangan extends Model
{
    protected $table = 'status_lapangan';
    protected $primaryKey = 'id_status';
    
    protected $fillable = [
        'id_lapangan',
        'deskripsi_status'
    ];

    public function lapangan()
    {
        return $this->belongsTo(Lapangan::class, 'id_lapangan');
    }
}
