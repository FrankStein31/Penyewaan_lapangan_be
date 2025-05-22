<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'id_sesi' => 'array', // Casting id_sesi sebagai array
        'total_harga' => 'decimal:2'
    ];

    /**
     * Get the user that owns the booking
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Get the field/lapangan for the booking
     */
    public function lapangan()
    {
        return $this->belongsTo(Lapangan::class, 'id_lapangan');
    }

    /**
     * Get all sessions for this booking
     * Akan mengembalikan collection dari objek Sesi yang sesuai dengan array id_sesi
     */
    public function sesi()
    {
        // Gunakan array id_sesi untuk mengambil semua sesi terkait
        return Sesi::whereIn('id_jam', $this->id_sesi ?? [])->get();
    }

    /**
     * Get hari attribute from sesi
     * Ini masih menggunakan ID sesi pertama untuk backward compatibility
     */
    public function getHariAttribute()
    {
        if (empty($this->id_sesi)) {
            return null;
        }

        $firstSesiId = is_array($this->id_sesi) ? $this->id_sesi[0] : $this->id_sesi;
        $sesi = Sesi::find($firstSesiId);

        if (!$sesi || !$sesi->hari_id) {
            return null;
        }

        return Hari::find($sesi->hari_id);
    }

    /**
     * Get related payment
     */
    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'id_pemesanan');
    }

    /**
     * Calculate durasi (total durasi dari semua sesi)
     */
    public function getDurasiAttribute()
    {
        // Jika id_sesi null atau kosong, kembalikan 0
        if (empty($this->id_sesi)) {
            return 0;
        }

        // Jika single value, gunakan cara lama untuk backward compatibility
        if (!is_array($this->id_sesi)) {
            $sesi = Sesi::find($this->id_sesi);
            if (!$sesi) return 0;

            $jam_mulai = Carbon::parse($sesi->jam_mulai);
            $jam_selesai = Carbon::parse($sesi->jam_selesai);

            return $jam_selesai->diffInHours($jam_mulai);
        }

        // Untuk array sesi, hitung total durasi dari semua sesi
        $totalDurasi = 0;
        $sesiCollection = Sesi::whereIn('id_jam', $this->id_sesi)->get();

        foreach ($sesiCollection as $sesi) {
            $jam_mulai = Carbon::parse($sesi->jam_mulai);
            $jam_selesai = Carbon::parse($sesi->jam_selesai);
            $totalDurasi += $jam_selesai->diffInHours($jam_mulai);
        }

        return $totalDurasi;
    }

    /**
     * Convert to array, with proper formatting
     */
    public function toArray()
    {
        $array = parent::toArray();

        // Handle jam_mulai dan jam_selesai formatting
        if (isset($array['jam_mulai']) && $array['jam_mulai']) {
            $array['jam_mulai'] = Carbon::parse($array['jam_mulai'])->format('H:i');
        }
        
        if (isset($array['jam_selesai']) && $array['jam_selesai']) {
            $array['jam_selesai'] = Carbon::parse($array['jam_selesai'])->format('H:i');
        }

        // Format tanggal
        if (isset($array['tanggal']) && $array['tanggal']) {
            $array['tanggal'] = Carbon::parse($array['tanggal'])->format('Y-m-d');
        }

        return $array;
    }
}
