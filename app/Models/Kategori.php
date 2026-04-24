<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    protected $table = 'kategori'; 
    
    // ✅ FIX: Ganti ke 'id' jika di database kolomnya cuma 'id'
    // Ini yang bikin error SQL "Column not found" tadi ilang.
    protected $primaryKey = 'id'; 

    public $timestamps = false;

    protected $fillable = ['nama_kategori', 'keterangan'];

    public function alats(): HasMany
    {
        // Parameter 2: Foreign Key di tabel alat (tetap id_kategori)
        // Parameter 3: Local Key di tabel kategori (sekarang 'id')
        return $this->hasMany(Alat::class, 'id_kategori', 'id');
    }
}