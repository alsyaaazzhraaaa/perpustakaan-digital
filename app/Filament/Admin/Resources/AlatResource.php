<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AlatResource\Pages;
use App\Models\Alat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class AlatResource extends Resource
{
    protected static ?string $model = Alat::class;

    // Ganti icon ke buku biar pas
    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Master Data';

    // Label di Sidebar dan Header
    protected static ?string $modelLabel = 'Koleksi Buku';
    protected static ?string $pluralModelLabel = 'Koleksi Buku';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Buku')
                    ->description('Masukkan detail koleksi buku perpustakaan.')
                    ->schema([
                        Forms\Components\Select::make('id_kategori')
                            ->label('Kategori Buku')
                            ->relationship('kategori', 'nama_kategori')
                            ->required()
                            ->searchable()
                            ->preload(),
                        
                        Forms\Components\TextInput::make('nama_alat')
                            ->label('Judul Buku')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Laskar Pelangi'),

                        Forms\Components\TextInput::make('jumlah')
                            ->label('Jumlah Stok')
                            ->numeric()
                            ->default(1)
                            ->prefixIcon('heroicon-o-archive-box'),

                        Forms\Components\TextInput::make('harga')
                            ->label('Harga Buku (Opsional)')
                            ->numeric()
                            ->prefix('Rp'),

                        Forms\Components\Select::make('kondisi')
                            ->label('Kondisi Buku')
                            ->options([
                                'Baik' => 'Baik',
                                'Rusak' => 'Rusak',
                                'Hilang' => 'Hilang',
                            ])
                            ->default('Baik')
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label('Status Ketersediaan')
                            ->options([
                                'tersedia' => 'Tersedia',
                                'dipinjam' => 'Sedang Dipinjam',
                                'perbaikan' => 'Dalam Perbaikan',
                            ])
                            ->default('tersedia')
                            ->required(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_alat')
                    ->label('Judul Buku')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('kategori.nama_kategori')
                    ->label('Kategori')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Stok')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'tersedia' => 'success',
                        'dipinjam' => 'warning',
                        'perbaikan' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
            ])
            // Force urutan berdasarkan judul buku biar gak error ID
            ->defaultSort('nama_alat', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('id_kategori')
                    ->label('Filter Kategori')
                    ->relationship('kategori', 'nama_kategori'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListAlats::route('/'),
            'create' => Pages\CreateAlat::route('/create'),
            'edit' => Pages\EditAlat::route('/{record}/edit'),
        ];
    }
}