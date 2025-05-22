<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\KategoriLap;
use App\Models\Fasilitas;
use App\Models\Lapangan;
use App\Models\Hari;
use App\Models\Sesi;
use App\Models\StatusLapangan;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat user admin
        User::create([
            'nama' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'no_hp' => '081234567890'
        ]);

        // Buat user biasa
        User::create([
            'nama' => 'User',
            'email' => 'user@user.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'no_hp' => '081234567891'
        ]);

        // Buat kategori lapangan
        $kategoriLapangan = [
            ['nama_kategori' => 'Basket', 'deskripsi' => 'Lapangan basket'],
            ['nama_kategori' => 'Futsal', 'deskripsi' => 'Lapangan futsal'],
            ['nama_kategori' => 'Badminton', 'deskripsi' => 'Lapangan badminton'],
            ['nama_kategori' => 'Tenis', 'deskripsi' => 'Lapangan tenis'],
        ];

        foreach ($kategoriLapangan as $kategori) {
            KategoriLap::create($kategori);
        }

        // Buat fasilitas
        $fasilitas = [
            ['nama_fasilitas' => 'Toilet', 'deskripsi' => 'Toilet pria dan wanita'],
            ['nama_fasilitas' => 'Ruang Ganti', 'deskripsi' => 'Ruang ganti pria dan wanita'],
            ['nama_fasilitas' => 'Parkir', 'deskripsi' => 'Area parkir luas'],
            ['nama_fasilitas' => 'Kantin', 'deskripsi' => 'Kantin dan tempat makan'],
        ];

        foreach ($fasilitas as $f) {
            Fasilitas::create($f);
        }

        // Buat lapangan
        $lapangan = [
            [
                'nama' => 'Lapangan Basket A',
                'kapasitas' => 10,
                'deskripsi' => 'Lapangan basket standar',
                'harga' => 100000,
                'kategori_id' => 1,
                'status' => 'tersedia'
            ],
            [
                'nama' => 'Lapangan Futsal A',
                'kapasitas' => 10,
                'deskripsi' => 'Lapangan futsal dengan rumput sintetis',
                'harga' => 150000,
                'kategori_id' => 2,
                'status' => 'tersedia'
            ],
            [
                'nama' => 'Lapangan Badminton A',
                'kapasitas' => 4,
                'deskripsi' => 'Lapangan badminton standar',
                'harga' => 50000,
                'kategori_id' => 3,
                'status' => 'tersedia'
            ]
        ];

        foreach ($lapangan as $l) {
            Lapangan::create($l);
        }

        // Buat hari
        $hari = [
            ['nama_hari' => 'Senin'],
            ['nama_hari' => 'Selasa'],
            ['nama_hari' => 'Rabu'],
            ['nama_hari' => 'Kamis'],
            ['nama_hari' => 'Jumat'],
            ['nama_hari' => 'Sabtu'],
            ['nama_hari' => 'Minggu'],
        ];

        foreach ($hari as $h) {
            Hari::create($h);
        }

        // Buat sesi
        $sesi = [
            ['jam_mulai' => '08:00:00', 'jam_selesai' => '09:00:00', 'deskripsi' => 'Sesi Pagi 1'],
            ['jam_mulai' => '09:00:00', 'jam_selesai' => '10:00:00', 'deskripsi' => 'Sesi Pagi 2'],
            ['jam_mulai' => '10:00:00', 'jam_selesai' => '11:00:00', 'deskripsi' => 'Sesi Pagi 3'],
            ['jam_mulai' => '11:00:00', 'jam_selesai' => '12:00:00', 'deskripsi' => 'Sesi Siang 1'],
            ['jam_mulai' => '13:00:00', 'jam_selesai' => '14:00:00', 'deskripsi' => 'Sesi Siang 2'],
            ['jam_mulai' => '14:00:00', 'jam_selesai' => '15:00:00', 'deskripsi' => 'Sesi Siang 3'],
            ['jam_mulai' => '15:00:00', 'jam_selesai' => '16:00:00', 'deskripsi' => 'Sesi Sore 1'],
            ['jam_mulai' => '16:00:00', 'jam_selesai' => '17:00:00', 'deskripsi' => 'Sesi Sore 2'],
            ['jam_mulai' => '17:00:00', 'jam_selesai' => '18:00:00', 'deskripsi' => 'Sesi Sore 3'],
            ['jam_mulai' => '18:00:00', 'jam_selesai' => '19:00:00', 'deskripsi' => 'Sesi Malam 1'],
            ['jam_mulai' => '19:00:00', 'jam_selesai' => '20:00:00', 'deskripsi' => 'Sesi Malam 2'],
            ['jam_mulai' => '20:00:00', 'jam_selesai' => '21:00:00', 'deskripsi' => 'Sesi Malam 3'],
        ];

        foreach ($sesi as $s) {
            Sesi::create($s);
        }

        // Buat status awal untuk lapangan
        foreach (Lapangan::all() as $lap) {
            StatusLapangan::create([
                'id_lapangan' => $lap->id,
                'deskripsi_status' => 'tersedia'
            ]);
        }
    }
} 