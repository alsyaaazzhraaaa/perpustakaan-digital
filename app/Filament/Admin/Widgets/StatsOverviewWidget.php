<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\User;
use App\Models\Peminjaman;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('Pengguna terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            
            Stat::make('Total Buku', Alat::count())
                ->description('Koleksi buku tersedia')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('success'),
            
            Stat::make('Total Kategori', Kategori::count()) // Pakai count langsung biar aman
                ->description('Kategori buku')
                ->descriptionIcon('heroicon-m-tag')
                ->color('warning'),
            
            Stat::make('Peminjaman Aktif', Peminjaman::where('status', 'disetujui')->count())
                ->description('Sedang dipinjam')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info'),
        ];
    }
}