<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PengembalianResource\Pages;
use App\Models\Peminjaman; 
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PengembalianResource extends Resource
{
    // Kita pakai model Peminjaman karena data pengembalian ada di tabel itu
    protected static ?string $model = Peminjaman::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $modelLabel = 'Data Pengembalian';
    protected static ?string $pluralModelLabel = 'Data Pengembalian';
    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return $table
            // ✅ INI KUNCINYA: Supaya yang muncul cuma yang statusnya 'dikembalikan'
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'dikembalikan'))
            ->columns([
                Tables\Columns\TextColumn::make('user.nama')
                    ->label('Peminjam')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('buku.nama_alat')
                    ->label('Judul Buku')
                    ->description(fn ($record) => "Kategori: " . ($record->buku?->kategori?->nama_kategori ?? 'Tanpa Kategori'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('tanggal_pinjam')
                    ->label('Tgl Pinjam')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_kembali_rencana')
                    ->label('Deadline')
                    ->date('d M Y')
                    ->color('danger'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color('info') // Warna biru untuk menandakan sudah kembali
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),
            ])
            ->filters([
                // Tambah filter biar bisa cari berdasarkan kategori
                Tables\Filters\SelectFilter::make('kategori')
                    ->relationship('buku.kategori', 'nama_kategori')
                    ->label('Filter Kategori'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(), // Jaga-jaga kalau mau hapus history
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengembalians::route('/'),
        ];
    }
}