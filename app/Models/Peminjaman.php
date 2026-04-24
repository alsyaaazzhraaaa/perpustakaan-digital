<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';

    // Matikan timestamps karena kolom updated_at gak ada di DB lu
    public $timestamps = false; 

    protected $fillable = [
        'id_user',
        'id_alat',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'status',
        'disetujui_oleh',
    ];

    // ✅ RELASI KE USER
    public function user(): BelongsTo
    {
        // Pastikan foreign key-nya 'id_user'
        return $this->belongsTo(User::class, 'id_user');
    }

    // ✅ RELASI KE BUKU (Ini yang tadi bikin error null)
    public function buku(): BelongsTo
    {
        // Nama fungsi ini HARUS 'buku' karena di Resource lu panggil 'buku'
        // Relasi ke model Alat, foreign key-nya 'id_alat'
        return $this->belongsTo(Alat::class, 'id_alat');
    }
}