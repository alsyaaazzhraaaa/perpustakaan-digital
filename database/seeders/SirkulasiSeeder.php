<?php

namespace Database\Seeders;

use App\Models\Alat;
use App\Models\User;
use App\Models\Peminjaman;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SirkulasiSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $book = Alat::first();

        if (!$user || !$book) return;

        Peminjaman::create([
            'id_user' => $user->id,
            'id_alat' => $book->id,
            'tanggal_pinjam' => Carbon::now(),
            'tanggal_kembali_rencana' => Carbon::now()->addDays(7),
            'status' => 'disetujui',
            'jumlah' => 1,
        ]);
    }
}