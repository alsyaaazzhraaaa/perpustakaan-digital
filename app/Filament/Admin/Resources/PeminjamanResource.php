<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PeminjamanResource\Pages;
use App\Models\Peminjaman;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action; 
use Filament\Notifications\Notification;

class PeminjamanResource extends Resource
{
    protected static ?string $model = Peminjaman::class;

    protected static ?string $slug = 'peminjaman'; 

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Sirkulasi Buku';
    protected static ?string $pluralModelLabel = 'Sirkulasi Buku';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Peminjaman')
                    ->schema([
                        Forms\Components\Select::make('id_user')
                            ->label('Peminjam')
                            // ✅ FIX: Pake 'nama' sesuai hasil tinker lu
                            ->relationship('user', 'nama') 
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('id_alat')
                            ->label('Buku yang Dipinjam')
                            ->relationship('buku', 'nama_alat')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\DatePicker::make('tanggal_pinjam')
                            ->label('Tanggal Pinjam')
                            ->default(now())
                            ->required(),

                        Forms\Components\DatePicker::make('tanggal_kembali_rencana')
                            ->label('Deadline Kembali')
                            ->default(now()->addDays(7))
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->options([
                                'menunggu' => 'Menunggu',
                                'disetujui' => 'Disetujui/Dipinjam',
                                'ditolak' => 'Ditolak',
                                'dikembalikan' => 'Sudah Kembali',
                            ])
                            ->default('menunggu')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                
                // ✅ FIX: Pake 'nama' (atau 'username' kalau mau lebih unik)
                Tables\Columns\TextColumn::make('user.nama')
                    ->label('Peminjam')
                    ->searchable(),

                Tables\Columns\TextColumn::make('buku.nama_alat')
                    ->label('Judul Buku')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tanggal_pinjam')
                    ->label('Tgl Pinjam')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'menunggu' => 'warning',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                        'dikembalikan' => 'info',
                        default => 'gray',
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                
                Action::make('setujui')
                    ->label('Serahkan Buku')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->status === 'menunggu')
                    ->action(function ($record) {
                        $record->update(['status' => 'disetujui']);
                        
                        Notification::make()
                            ->title('Status Diperbarui')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeminjamans::route('/'),
            'create' => Pages\CreatePeminjaman::route('/create'),
            'view' => Pages\ViewPeminjaman::route('/{record}'),
            'edit' => Pages\EditPeminjaman::route('/{record}/edit'),
        ];
    }
}