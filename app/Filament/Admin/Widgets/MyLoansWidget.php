<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Peminjaman;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Widgets\TableWidget as BaseWidget;

class MyLoansWidget extends BaseWidget
{
    protected static ?string $heading = 'Peminjaman Saya';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 4;

    // Hanya Peminjam yang bisa lihat widget ini
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->isPeminjam();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Peminjaman::query()
                    ->where('id_user', auth()->id()) // Filter punya sendiri
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_pinjam')
                    ->label('Tanggal Pinjam')
                    ->date('d M Y'),
                Tables\Columns\TextColumn::make('tanggal_kembali_rencana')
                    ->label('Rencana Kembali')
                    ->date('d M Y'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'menunggu' => 'warning',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                        'dikembalikan' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('detailPeminjaman_count') // Cara lebih efisien di Filament
                    ->label('Jumlah Alat')
                    ->getStateUsing(function (Peminjaman $record) {
                        // Pengaman: Jika relasi null, tampilkan 0
                        $count = $record->detailPeminjaman()->count() ?? 0;
                        return $count . ' alat';
                    }),
            ])
            ->actions([
                Action::make('lihat')
                    ->label('Lihat')
                    ->url(fn(Peminjaman $record) => route('filament.admin.resources.peminjaman.view', $record))
                    ->icon('heroicon-o-eye'),
            ])
            ->paginated(false);
    }
}