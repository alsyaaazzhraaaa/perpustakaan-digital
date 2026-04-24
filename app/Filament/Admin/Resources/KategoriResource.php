<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\KategoriResource\Pages;
use App\Models\Kategori;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\TextSize;

class KategoriResource extends Resource
{
    protected static ?string $model = Kategori::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Kategori Buku';
    protected static ?string $pluralModelLabel = 'Kategori Buku';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kategori')
                    ->description('Kelola kategori utama seperti Fiksi, Non-Fiksi, atau Pengetahuan.')
                    ->schema([
                        Forms\Components\TextInput::make('nama_kategori')
                            ->label('Nama Kategori')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Fiksi'),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Deskripsi Kategori')
                            ->placeholder('Jelaskan cakupan kategori ini...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Detail Kategori')
                    ->schema([
                        Infolists\Components\TextEntry::make('nama_kategori')
                            ->label('Nama Kategori')
                            ->weight('bold')
                            ->size(TextSize::Large)
                            ->color('primary'),
                        Infolists\Components\TextEntry::make('keterangan')
                            ->label('Deskripsi'),
                    ])->columns(2),

                Infolists\Components\Section::make('Daftar Judul Buku')
                    ->description('Buku-buku yang terdaftar dalam kategori ini.')
                    ->schema([
                        // Menampilkan list buku secara detail di halaman View
                        Infolists\Components\RepeatableEntry::make('alats')
                            ->label(false)
                            ->schema([
                                Infolists\Components\Grid::make(3)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('nama_alat')
                                            ->label('Judul Buku')
                                            ->weight('bold')
                                            ->icon('heroicon-o-book-open')
                                            ->color('info'),
                                        Infolists\Components\TextEntry::make('jumlah')
                                            ->label('Stok')
                                            ->badge(),
                                        Infolists\Components\TextEntry::make('status')
                                            ->label('Status')
                                            ->badge()
                                            ->color(fn ($state) => $state === 'tersedia' ? 'success' : 'warning'),
                                    ]),
                            ])
                            ->grid(1)
                            ->emptyStateHeading('Belum ada koleksi buku untuk kategori ini.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_kategori')
                    ->label('Kategori')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),
                
                // ✅ BAGIAN PEMISAH JUDUL BUKU (List with Badges)
                Tables\Columns\TextColumn::make('alats.nama_alat')
                    ->label('Daftar Koleksi Buku')
                    ->badge() // Dipisahkan per kotak (badge)
                    ->color('info')
                    ->listWithLineBreaks() // Dipisahkan baris jika banyak
                    ->limitList(3) // Batasi 3 dulu, sisanya bisa di-expand
                    ->expandableLimitedList(),

                Tables\Columns\TextColumn::make('alats_count')
                    ->label('Total Judul')
                    ->counts('alats')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),
            ])
            ->defaultSort('nama_kategori', 'asc')
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListKategoris::route('/'),
            'create' => Pages\CreateKategori::route('/create'),
            'view' => Pages\ViewKategori::route('/{record}'),
            'edit' => Pages\EditKategori::route('/{record}/edit'),
        ];
    }
}