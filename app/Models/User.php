<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nama',
        'username',
        'password',
        'role',
        'kelas',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting otomatis untuk keamanan password.
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Menampilkan nama user di header Filament.
     */
    public function getFilamentName(): string
    {
        return $this->nama ?? $this->username;
    }

    /**
     * Logika akses Panel Filament.
     * Hanya Admin dan Petugas yang boleh masuk ke dashboard /admin.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Jika sedang mengakses panel admin
        if ($panel->getId() === 'admin') {
            // Hanya Admin dan Petugas yang boleh masuk
            return in_array($this->role, ['admin', 'petugas']);
        }

        // Untuk panel lainnya (jika nanti ada panel 'peminjam')
        return true;
    }

    /**
     * Helper check role
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPetugas(): bool
    {
        return $this->role === 'petugas';
    }

    public function isPeminjam(): bool
    {
        return $this->role === 'peminjam';
    }

    /**
     * Staff adalah mereka yang mengelola aplikasi (Admin & Petugas)
     */
    public function isStaff(): bool
    {
        return $this->isAdmin() || $this->isPetugas();
    }

    /**
     * Relasi Database
     */
    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'id_user');
    }

    public function approvedPeminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'disetujui_oleh');
    }

    public function logAktivitas(): HasMany
    {
        return $this->hasMany(LogAktivitas::class, 'id_user');
    }
}