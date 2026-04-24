<?php

namespace App\Console\Commands; // TAMBAHKAN BARIS INI

use Illuminate\Console\Command;
use App\Models\Peminjaman;

class CheckRelationCommand extends Command
{
    protected $signature = 'test:relation';
    protected $description = 'Check relation for Peminjaman';

    public function handle()
    {
        $p = Peminjaman::with(['user', 'approver'])->first();
        if (!$p) {
            $this->info("No Peminjaman found");
            return;
        }
        $this->info("Peminjaman ID: " . $p->id_peminjaman);
        $this->info("User ID: " . $p->id_user);
        $this->info("User is empty? " . ($p->user ? 'No' : 'Yes'));
        if ($p->user) {
            $this->info("User relation mapping: pk is " . $p->user->getKeyName() . ", value=" . $p->user->getKey());
            $this->info("User Username: " . $p->user->username);
        }
        $this->info("Approver ID: " . $p->disetujui_oleh);
        $this->info("Approver is empty? " . ($p->approver ? 'No' : 'Yes'));
    }
}