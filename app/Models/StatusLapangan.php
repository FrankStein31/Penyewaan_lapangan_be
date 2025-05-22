<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusLapangan extends Model
{
    use HasFactory;

    protected $table = 'status_lapangan';
    protected $primaryKey = 'id_status';
    
    protected $fillable = [
        'id_lapangan',
        'tanggal',
        'id_sesi',
        'deskripsi_status',
    ];
    
    // Cast id_sesi sebagai array agar otomatis dikonversi ke/dari JSON
    protected $casts = [
        'tanggal' => 'date',
        'id_sesi' => 'array'
    ];
    
    protected $appends = ['status_format'];
    
    /**
     * Get the lapangan that owns the status
     */
    public function lapangan()
    {
        return $this->belongsTo(Lapangan::class, 'id_lapangan');
    }
    
    /**
     * Get all sesi objects related to this status
     */
    public function sesi()
    {
        // Gunakan whereIn untuk mengambil semua sesi yang terkait
        return Sesi::whereIn('id_jam', $this->id_sesi ?? [])->get();
    }
    
    /**
     * Get formatted status attribute
     */
    public function getStatusFormatAttribute()
    {
        if ($this->deskripsi_status == 'tersedia') {
            return 'Tersedia';
        } else if ($this->deskripsi_status == 'disewa') {
            return 'Disewa';
        } else if ($this->deskripsi_status == 'maintenance') {
            return 'Dalam Perbaikan';
        }
        
        return $this->deskripsi_status;
    }
}
