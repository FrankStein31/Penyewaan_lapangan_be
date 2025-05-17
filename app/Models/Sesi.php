<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sesi extends Model
{
    protected $table = 'sesis';
    protected $primaryKey = 'id_jam';
    
    protected $fillable = [
        'jam_mulai', 
        'jam_selesai', 
        'deskripsi',
        'hari_id'
    ];
    
    protected $appends = ['durasi', 'waktu_format'];
    
    // Relasi dengan Hari
    public function hari()
    {
        return $this->belongsTo(Hari::class, 'hari_id');
    }
    
    // Relasi dengan Pemesanan
    public function pemesanan()
    {
        return $this->hasMany(Pemesanan::class, 'id_sesi', 'id_jam');
    }
    
    // Relasi dengan Status Lapangan
    public function statusLapangan()
    {
        return $this->hasMany(StatusLapangan::class, 'id_sesi', 'id_jam');
    }
    
    // Accessor untuk mendapatkan durasi dalam jam
    public function getDurasiAttribute()
    {
        $mulai = strtotime($this->jam_mulai);
        $selesai = strtotime($this->jam_selesai);
        
        // Hitung selisih dalam jam
        return round(($selesai - $mulai) / 3600, 1);
    }
    
    // Accessor untuk format waktu yang mudah dibaca
    public function getWaktuFormatAttribute()
    {
        return date('H:i', strtotime($this->jam_mulai)) . ' - ' . date('H:i', strtotime($this->jam_selesai));
    }
    
    // Ubah perilaku toArray dan toJson untuk memastikan format yang diharapkan
    public function toArray()
    {
        $array = parent::toArray();
        
        // Pastikan jam_mulai dan jam_selesai selalu dalam format H:i:s
        if (isset($array['jam_mulai'])) {
            $array['jam_mulai'] = date('H:i:s', strtotime($array['jam_mulai']));
        }
        
        if (isset($array['jam_selesai'])) {
            $array['jam_selesai'] = date('H:i:s', strtotime($array['jam_selesai']));
        }
        
        return $array;
    }
}