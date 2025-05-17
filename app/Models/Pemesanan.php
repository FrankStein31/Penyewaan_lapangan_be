<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    use HasFactory;

    protected $table = 'pemesanan';
    protected $primaryKey = 'id_pemesanan';

    protected $fillable = [
        'id_user',
        'id_lapangan',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'id_sesi',
        'status',
        'total_harga',
        'nama_pelanggan',
        'email',
        'no_hp',
        'catatan'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
        'total_harga' => 'decimal:2'
    ];

    // Relasi dengan User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // Relasi dengan Lapangan
    public function lapangan()
    {
        return $this->belongsTo(Lapangan::class, 'id_lapangan');
    }

    // Relasi dengan Sesi
    public function sesi()
    {
        return $this->belongsTo(Sesi::class, 'id_sesi', 'id_jam');
    }

    // Tambahkan accessor untuk mendapatkan hari berdasarkan tanggal jika diperlukan
    public function getHariAttribute()
    {
        // 0 = Minggu, 1 = Senin, dst.
        $dayOfWeek = date('w', strtotime($this->tanggal));
        
        // Sesuaikan dengan tabel hari (asumsi 1 = Senin, dst.)
        $dayMappings = [
            0 => 7, // Minggu = 7
            1 => 1, // Senin = 1
            2 => 2, // Selasa = 2
            3 => 3, // Rabu = 3
            4 => 4, // Kamis = 4
            5 => 5, // Jumat = 5
            6 => 6  // Sabtu = 6
        ];
        
        $hariId = $dayMappings[$dayOfWeek];
        return Hari::find($hariId);
    }

    // Relasi dengan pembayaran
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'id_pemesanan');
    }
    
    // Accessor untuk mendapatkan durasi dalam jam (untuk perhitungan biaya)
    public function getDurasiAttribute()
    {
        if (!$this->jam_mulai || !$this->jam_selesai) {
            return 0;
        }
        
        $mulai = strtotime($this->jam_mulai);
        $selesai = strtotime($this->jam_selesai);
        
        // Hitung selisih dalam jam
        return round(($selesai - $mulai) / 3600, 1);
    }
}
