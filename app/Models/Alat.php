<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alat extends Model
{
    use HasFactory;

    protected $table = 'alat';
    
    // ✅ FIX: Ganti ke 'id' karena SQL lu bilang 'id_alat' nggak ada
    protected $primaryKey = 'id'; 

    public $timestamps = false;

    protected $fillable = [
        'nama_alat',
        'id_kategori',
        'harga',
        'jumlah',
        'kondisi',
        'status',
    ];

    public function kategori(): BelongsTo
    {
        // Parameter 3 disesuaikan ke 'id' (Primary Key Kategori)
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id');
    }
}