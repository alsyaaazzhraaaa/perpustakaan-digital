<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Inventaris Buku</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; margin: 0; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 20px; text-transform: uppercase; }
        .kategori-header { background-color: #2563eb; color: #ffffff; padding: 8px 12px; font-weight: bold; font-size: 13px; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f8fafc; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9fafb; }
        .text-center { text-align: center; }
        .footer { margin-top: 50px; text-align: right; padding-right: 30px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Sistem Informasi Perpustakaan</h1>
        <p>Laporan Data Inventaris Koleksi Buku</p>
        <p style="font-size: 10px;">Dicetak pada: {{ $tanggal }}</p>
    </div>

    @forelse($data as $kategori => $items)
        <div class="kategori-header">
            KATEGORI: {{ strtoupper($kategori ?: 'Tanpa Kategori') }}
        </div>
        <table>
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="55%">Judul Buku</th>
                    <th width="20%" class="text-center">Jumlah Stok</th>
                    <th width="20%" class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $buku)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $buku->nama_alat }}</strong></td>
                    <td class="text-center">{{ $buku->jumlah }}</td>
                    <td class="text-center">{{ ucfirst($buku->status) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @empty
        <p style="text-align: center; margin-top: 50px; color: #999;">Belum ada data buku.</p>
    @endforelse

    <div class="footer">
        <p>{{ now()->translatedFormat('d F Y') }}</p>
        <p>Petugas Perpustakaan,</p>
        <br><br><br>
        <p><strong>( ____________________ )</strong></p>
    </div>

</body>
</html>