<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\Action;
use App\Models\Pengembalian;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class LaporanPengembalian extends Page
{
    // PERBAIKAN: Menggunakan ?string agar kompatibel dengan Filament v3
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $title = 'Laporan Pengembalian';
    protected static ?string $slug = 'laporan-pengembalian';

    // PERBAIKAN: Menambahkan kata kunci 'static'
    protected static string $view = 'filament.pages.laporan-pengembalian';

    public ?string $pdfData     = null;
    public ?string $filterStart = null;
    public ?string $filterEnd   = null;

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->role !== 'peminjam';
    }

    public function downloadPdf(): mixed
    {
        $pengembalianQuery = Pengembalian::with(['peminjaman.user']);

        if ($this->filterStart) {
            $pengembalianQuery->whereDate('tanggal_kembali', '>=', Carbon::parse($this->filterStart));
        }
        if ($this->filterEnd) {
            $pengembalianQuery->whereDate('tanggal_kembali', '<=', Carbon::parse($this->filterEnd));
        }

        $pengembalian = $pengembalianQuery->get();

        $pdf = Pdf::loadView('pdf.laporan-pengembalian', [
            'data'      => $pengembalian,
            'startDate' => $this->filterStart,
            'endDate'   => $this->filterEnd,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'laporan-pengembalian-' . Carbon::now()->format('Ymd') . '.pdf');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetak_pdf')
                ->label('Cetak PDF')
                ->color('primary')
                ->modalHeading('Filter Laporan Pengembalian')
                ->modalSubmitActionLabel('Tampilkan Preview')
                ->form([
                    DatePicker::make('start_date')
                        ->label('Tanggal Mulai')
                        ->native(false),
                    DatePicker::make('end_date')
                        ->label('Tanggal Sampai')
                        ->native(false)
                        ->afterOrEqual('start_date'),
                ])
                ->action(function (array $data) {
                    $this->filterStart = $data['start_date'] ?? null;
                    $this->filterEnd   = $data['end_date'] ?? null;

                    $pengembalianQuery = Pengembalian::with(['peminjaman.user']);

                    if ($this->filterStart) {
                        $pengembalianQuery->whereDate('tanggal_kembali', '>=', Carbon::parse($this->filterStart));
                    }
                    if ($this->filterEnd) {
                        $pengembalianQuery->whereDate('tanggal_kembali', '<=', Carbon::parse($this->filterEnd));
                    }

                    $pengembalian = $pengembalianQuery->get();

                    $pdf = Pdf::loadView('pdf.laporan-pengembalian', [
                        'data'      => $pengembalian,
                        'startDate' => $this->filterStart,
                        'endDate'   => $this->filterEnd,
                    ]);

                    $this->pdfData = base64_encode($pdf->output());
                }),
        ];
    }
}