<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusLapangan extends Model
{
    protected $table = 'status_lapangan';
    protected $primaryKey = 'id_status';
    
    protected $fillable = [
        'id_lapangan',
        'deskripsi_status',
        'tanggal',
        'id_sesi'
    ];

    protected $casts = [
        'tanggal' => 'date'
    ];

    public function lapangan()
    {
        return $this->belongsTo(Lapangan::class, 'id_lapangan');
    }
    
    public function sesi()
    {
        return $this->belongsTo(Sesi::class, 'id_sesi', 'id_jam');
    }
    
    // Accessor untuk mendapatkan status dalam format yang mudah dibaca
    public function getStatusFormatAttribute()
    {
        $deskripsi = $this->deskripsi_status;
        
        $format = [
            'tersedia' => 'Tersedia',
            'disewa' => 'Sedang Disewa',
            'perbaikan' => 'Dalam Perbaikan',
        ];
        
        return $format[$deskripsi] ?? ucfirst($deskripsi);
    }
}
