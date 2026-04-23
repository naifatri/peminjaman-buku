<!DOCTYPE html>
<html>
<head>
    <title>Laporan Peminjaman</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { bg-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
        .footer { margin-top: 20px; text-align: right; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN PEMINJAMAN BUKU</h2>
        <p>Periode: {{ $startDate ?? 'Awal' }} s/d {{ $endDate ?? 'Sekarang' }}</p>
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
                <th>Denda</th>
            </tr>
        </thead>
        <tbody>
            @php $totalDenda = 0; @endphp
            @foreach($borrowings as $index => $borrowing)
            @php $totalDenda += $borrowing->fine_amount; @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $borrowing->user->name }}</td>
                <td>{{ $borrowing->book->title }}</td>
                <td>{{ $borrowing->borrow_date }}</td>
                <td>{{ $borrowing->return_date ?? '-' }}</td>
                <td>{{ ucfirst($borrowing->status) }}</td>
                <td>Rp {{ number_format($borrowing->fine_amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        TOTAL DENDA: Rp {{ number_format($totalDenda, 0, ',', '.') }}
    </div>
</body>
</html>
