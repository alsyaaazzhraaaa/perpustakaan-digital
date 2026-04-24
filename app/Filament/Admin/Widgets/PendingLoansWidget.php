<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Peminjaman;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingLoansWidget extends BaseWidget
{
    protected static ?string $heading = 'Peminjaman Menunggu Persetujuan';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 3;

    // Logic: Hanya Admin yang bisa lihat widget ini
    public static function canView(): bool
    {
        return auth()->user()->isAdmin(); 
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Peminjaman::query()
                    ->where('status', 'menunggu')
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.username')
                    ->label('Peminjam')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_pinjam')
                    ->label('Tanggal Pinjam')
                    ->date('d M Y'),
                Tables\Columns\TextColumn::make('tanggal_kembali_rencana')
                    ->label('Rencana Kembali')
                    ->date('d M Y'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color('warning'),
            ])
            ->actions([
                Action::make('lihat')
                    ->label('Lihat')
                    ->url(fn(Peminjaman $record) => route('filament.admin.resources.peminjaman.view', $record))
                    ->icon('heroicon-o-eye'),
                
                Action::make('setujui')
                    ->label('Setujui')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->action(fn (Peminjaman $record) => $record->update(['status' => 'disetujui'])),
            ])
            ->paginated(false);
    }
}