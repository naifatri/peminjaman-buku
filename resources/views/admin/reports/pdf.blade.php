<!DOCTYPE html>
<html>
<head>
    <title>Laporan Peminjaman</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #0f172a; }
        h1, h2, p { margin: 0; }
        .header { margin-bottom: 18px; }
        .muted { color: #64748b; }
        .grid { width: 100%; margin: 18px 0; }
        .card {
            display: inline-block;
            width: 23%;
            padding: 10px;
            margin-right: 1%;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            vertical-align: top;
            box-sizing: border-box;
        }
        .card h3 { font-size: 10px; text-transform: uppercase; color: #64748b; margin-bottom: 8px; }
        .card p { font-size: 18px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #e2e8f0; padding: 8px; text-align: left; }
        th { background: #f8fafc; font-size: 10px; text-transform: uppercase; color: #475569; }
        .text-right { text-align: right; }
        .footer { margin-top: 18px; text-align: right; font-weight: bold; }
        .meta { margin-top: 8px; line-height: 1.7; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Peminjaman Buku</h1>
        <p class="muted">Dicetak pada {{ now()->translatedFormat('d M Y H:i') }}</p>
        <div class="meta">
            <p>Pengguna: {{ $filters['user_name'] ?: 'Semua' }}</p>
            <p>Buku: {{ $filters['book_title'] ?: 'Semua' }}</p>
            <p>Status: {{ $filters['status'] ?: 'Semua' }}</p>
            <p>Periode: {{ $filters['start_date'] ?: 'Awal' }} s/d {{ $filters['end_date'] ?: 'Sekarang' }}</p>
        </div>
    </div>

    <div class="grid">
        <div class="card">
            <h3>Total Peminjaman</h3>
            <p>{{ number_format($summary['total_borrowings']) }}</p>
        </div>
        <div class="card">
            <h3>Sudah Dikembalikan</h3>
            <p>{{ number_format($summary['returned_borrowings']) }}</p>
        </div>
        <div class="card">
            <h3>Keterlambatan</h3>
            <p>{{ number_format($summary['late_borrowings']) }}</p>
        </div>
        <div class="card">
            <h3>Total Denda</h3>
            <p>Rp {{ number_format($summary['total_fines'], 0, ',', '.') }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Peminjam</th>
                <th>Buku</th>
                <th>Tgl Pinjam</th>
                <th>Tgl Kembali</th>
                <th>Status</th>
                <th class="text-right">Denda</th>
            </tr>
        </thead>
        <tbody>
            @forelse($borrowings as $index => $borrowing)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $borrowing->user->name }}</td>
                    <td>{{ $borrowing->book->title }}</td>
                    <td>{{ $borrowing->borrow_date?->translatedFormat('d M Y') ?? '-' }}</td>
                    <td>{{ $borrowing->return_date?->translatedFormat('d M Y') ?? '-' }}</td>
                    <td>{{ $borrowing->admin_status_label }}</td>
                    <td class="text-right">Rp {{ number_format($borrowing->fine_amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Tidak ada data untuk filter yang dipilih.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Total Denda: Rp {{ number_format($summary['total_fines'], 0, ',', '.') }}
    </div>
</body>
</html>
