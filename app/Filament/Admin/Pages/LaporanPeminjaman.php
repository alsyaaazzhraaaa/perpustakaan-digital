<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\Action;
use App\Models\Peminjaman;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;

class LaporanPeminjaman extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';
    protected static string | \UnitEnum | null $navigationGroup = 'Laporan';
    protected static ?string $title = 'Laporan Peminjaman';
    protected static ?string $slug = 'laporan-peminjaman';

    protected string $view = 'filament.pages.laporan-form';

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->role !== 'peminjam';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Peminjaman::query()->with(['user', 'approver'])->orderBy('created_at', 'desc'))
            ->columns([
                TextColumn::make('user.username')
                    ->label('Peminjam')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tanggal_pinjam')
                    ->label('Tgl Pinjam')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('tanggal_kembali_rencana')
                    ->label('Tgl Kembali (Rencana)')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'menunggu' => 'warning',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                        'dikembalikan' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('approver.username')
                    ->label('Disetujui Oleh')
                    ->default('-'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'menunggu' => 'Menunggu',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                        'dikembalikan' => 'Dikembalikan',
                    ]),
                Filter::make('tanggal_pinjam')
                    ->form([
                        DatePicker::make('from')->label('Tanggal Pinjam (Dari)'),
                        DatePicker::make('until')->label('Tanggal Pinjam (Sampai)'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_pinjam', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_pinjam', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators['from'] = 'Dari: ' . Carbon::parse($data['from'])->toFormattedDateString();
                        }
                        if ($data['until'] ?? null) {
                            $indicators['until'] = 'Sampai: ' . Carbon::parse($data['until'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),
            ])
            ->headerActions([
                Action::make('cetak_pdf')
                    ->label('Cetak PDF')
                    ->icon('heroicon-o-printer')
                    ->color('primary')
                    ->action(function ($livewire) {
                        $query = $livewire->getFilteredTableQuery();
                        $data = $query->get();
                        
                        $filters = $livewire->tableFilters;
                        $startDate = $filters['tanggal_pinjam']['from'] ?? null;
                        $endDate = $filters['tanggal_pinjam']['until'] ?? null;

                        $pdf = Pdf::loadView('pdf.laporan-peminjaman', [
                            'data' => $data,
                            'startDate' => $startDate,
                            'endDate' => $endDate
                        ]);

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->stream();
                        }, 'laporan-peminjaman-' . Carbon::now()->format('Ymd') . '.pdf');
                    }),
            ]);
    }
}
