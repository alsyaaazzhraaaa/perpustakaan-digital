<?php

namespace Database\Seeders;

use App\Models\Alat;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $kategoriNames = [
            'Sains & Teknologi', 'Novel & Sastra', 'Sejarah Dunia', 
            'Pengembangan Diri', 'Komik & Manga', 'Religi & Spiritual', 
            'Hukum & Politik', 'Ekonomi & Bisnis', 'Kesehatan', 'Seni & Desain'
        ];

        foreach ($kategoriNames as $name) {
            // 1. Buat Kategori
            $kategori = Kategori::create([
                'nama_kategori' => $name
            ]);

            // 2. Buat Buku terkait kategori tersebut
            Alat::create([
                'id_kategori' => $kategori->id_kategori,
                'nama_alat'   => 'Buku Master: ' . $name,
                'harga'       => rand(50000, 150000),
                'jumlah'      => rand(5, 20),
                'kondisi'     => 'Baik',
                'status'      => 'Tersedia',
            ]);
        }
    }
}