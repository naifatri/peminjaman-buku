<!DOCTYPE html>
<html>
<head>
    <title>Pengingat Pengembalian Buku</title>
</head>
<body>
    <h1>Halo, {{ $borrowing->user->name }}!</h1>
    <p>Ini adalah pengingat bahwa masa peminjaman buku Anda akan segera berakhir.</p>
    
    <div style="background: #f4f4f4; padding: 15px; border-radius: 5px;">
        <p><strong>Buku:</strong> {{ $borrowing->book->title }}</p>
        <p><strong>Batas Pengembalian:</strong> {{ $borrowing->due_date }}</p>
    </div>

    <p>
        Mohon kembalikan buku tepat waktu untuk menghindari denda keterlambatan sebesar
        Rp {{ number_format($borrowing->late_fee_per_day ?? 5000, 0, ',', '.') }} per hari
        @if(($borrowing->grace_period_days ?? 0) > 0)
            setelah masa toleransi {{ $borrowing->grace_period_days }} hari
        @endif.
    </p>
    
    <p>Terima kasih,<br>Perpus Naifa</p>
</body>
</html>
