<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Actions\Action;
use App\Models\Peminjaman;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class LaporanPeminjaman extends Page
{
    // PERBAIKAN: Gunakan ?string untuk kompatibilitas Filament v3
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $title = 'Laporan Peminjaman';
    protected static ?string $slug = 'laporan-peminjaman';

    // PERBAIKAN: Harus ditambahkan kata 'static'
    protected static string $view = 'filament.pages.laporan-peminjaman';

    public ?string $pdfData    = null;
    public ?string $filterFrom  = null;
    public ?string $filterUntil = null;
    public ?string $filterStatus = null;

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->role !== 'peminjam';
    }

    public function generatePreview(array $data): void
    {
        $this->filterFrom   = $data['from'] ?? null;
        $this->filterUntil  = $data['until'] ?? null;
        $this->filterStatus = $data['status'] ?? null;

        $query = Peminjaman::query()->with(['user', 'approver'])->orderBy('created_at', 'desc');

        if ($this->filterFrom) {
            $query->whereDate('tanggal_pinjam', '>=', $this->filterFrom);
        }
        if ($this->filterUntil) {
            $query->whereDate('tanggal_pinjam', '<=', $this->filterUntil);
        }
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $pdf = Pdf::loadView('pdf.laporan-peminjaman', [
            'data'      => $query->get(),
            'startDate' => $this->filterFrom,
            'endDate'   => $this->filterUntil,
        ]);

        $this->pdfData = base64_encode($pdf->output());
    }

    public function downloadPdf(): mixed
    {
        $query = Peminjaman::query()->with(['user', 'approver'])->orderBy('created_at', 'desc');

        if ($this->filterFrom) {
            $query->whereDate('tanggal_pinjam', '>=', $this->filterFrom);
        }
        if ($this->filterUntil) {
            $query->whereDate('tanggal_pinjam', '<=', $this->filterUntil);
        }
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $pdf = Pdf::loadView('pdf.laporan-peminjaman', [
            'data'      => $query->get(),
            'startDate' => $this->filterFrom,
            'endDate'   => $this->filterUntil,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'laporan-peminjaman-' . Carbon::now()->format('Ymd') . '.pdf');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetak_pdf')
                ->label('Cetak PDF')
                ->color('primary')
                ->modalHeading('Filter Laporan Peminjaman')
                ->modalSubmitActionLabel('Tampilkan Preview')
                ->form([
                    DatePicker::make('from')
                        ->label('Tanggal Pinjam (Dari)')
                        ->native(false),
                    DatePicker::make('until')
                        ->label('Tanggal Pinjam (Sampai)')
                        ->native(false)
                        ->afterOrEqual('from'),
                    Select::make('status')
                        ->label('Status')
                        ->placeholder('Semua Status')
                        ->options([
                            'menunggu'     => 'Menunggu',
                            'disetujui'    => 'Disetujui',
                            'ditolak'      => 'Ditolak',
                            'dikembalikan' => 'Dikembalikan',
                        ]),
                ])
                ->action(function (array $data) {
                    $this->filterFrom   = $data['from'] ?? null;
                    $this->filterUntil  = $data['until'] ?? null;
                    $this->filterStatus = $data['status'] ?? null;

                    $query = Peminjaman::query()->with(['user', 'approver'])->orderBy('created_at', 'desc');

                    if ($this->filterFrom) {
                        $query->whereDate('tanggal_pinjam', '>=', $this->filterFrom);
                    }
                    if ($this->filterUntil) {
                        $query->whereDate('tanggal_pinjam', '<=', $this->filterUntil);
                    }
                    if ($this->filterStatus) {
                        $query->where('status', $this->filterStatus);
                    }

                    $pdf = Pdf::loadView('pdf.laporan-peminjaman', [
                        'data'      => $query->get(),
                        'startDate' => $this->filterFrom,
                        'endDate'   => $this->filterUntil,
                    ]);

                    $this->pdfData = base64_encode($pdf->output());
                }),
        ];
    }
}