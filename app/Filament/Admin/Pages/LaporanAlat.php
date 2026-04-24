<?php

namespace App\Filament\Admin\Pages;

use App\Models\Alat;
use Filament\Pages\Page;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanAlat extends Page
{
    protected static ?string $title = 'Laporan Data Buku';
    protected static ?string $navigationLabel = 'Laporan Buku';
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'Laporan';

    // BAGIAN INI HARUS PERSIS SAMA DENGAN STRUKTUR FOLDER DI VIEWS
    protected static string $view = 'filament.admin.pages.laporan-alat';

    public function cetakPdf()
    {
        $data = Alat::with('kategori')->get()->groupBy(function($item) {
            return $item->kategori->nama_kategori ?? 'Tanpa Kategori';
        });

        $pdf = Pdf::loadView('pdf.laporan-buku', [
            'data' => $data,
            'tanggal' => now()->translatedFormat('d F Y'),
        ]);

        return response()->streamDownload(fn () => print($pdf->output()), 'laporan-data-buku.pdf');
    }
}