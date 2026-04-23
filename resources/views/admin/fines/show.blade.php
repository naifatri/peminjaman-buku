@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <a href="{{ route('admin.fines.index') }}" class="inline-flex items-center text-sm font-semibold text-slate-400 transition-colors hover:text-indigo-600">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke daftar denda
            </a>
            <h2 class="mt-4 text-3xl font-black text-slate-800">Detail Denda #{{ $fine->id }}</h2>
            <p class="mt-2 text-sm text-slate-500">Lihat breakdown perhitungan, status pembayaran, dan relasi ke transaksi peminjaman.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <span class="inline-flex items-center rounded-full border px-4 py-2 text-xs font-bold uppercase tracking-[0.2em] {{ $fine->status_color }}">
                {{ $fine->status_label }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Jumlah Hari Terlambat</p>
            <p class="mt-4 text-2xl font-black text-rose-700">{{ $summary['raw_late_days'] }} hari</p>
        </div>
        <div class="rounded-[2rem] border border-indigo-100 bg-indigo-50/80 p-6 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-indigo-500">Tarif Per Hari</p>
            <p class="mt-4 text-2xl font-black text-indigo-700">Rp {{ number_format($summary['late_fee_per_day'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-[2rem] border border-amber-100 bg-amber-50/80 p-6 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-amber-500">Denda Keterlambatan</p>
            <p class="mt-4 text-2xl font-black text-amber-700">Rp {{ number_format($summary['late_fee_total'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-[2rem] border border-rose-100 bg-rose-50/80 p-6 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-rose-500">Total Denda</p>
            <p class="mt-4 text-2xl font-black text-rose-700">Rp {{ number_format($fine->amount, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 xl:grid-cols-[1.35fr_0.65fr]">
        <div class="space-y-8">
            <div class="rounded-[2rem] border border-slate-100 bg-white p-8 shadow-sm">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="rounded-[1.75rem] border border-slate-100 bg-slate-50/80 p-6">
                        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Data Pengguna</p>
                        <div class="mt-5">
                            <h3 class="text-lg font-black text-slate-800">{{ $fine->borrowing->user->name }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $fine->borrowing->user->email }}</p>
                        </div>
                    </div>

                    <div class="rounded-[1.75rem] border border-slate-100 bg-slate-50/80 p-6">
                        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Data Buku</p>
                        <div class="mt-5">
                            <h3 class="text-lg font-black text-slate-800">{{ $fine->borrowing->book->title }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $fine->borrowing->book->author }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-[2rem] border border-slate-100 bg-white p-8 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Informasi Transaksi</p>
                <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="rounded-[1.75rem] border border-slate-100 bg-slate-50 p-5">
                        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Tanggal Pinjam</p>
                        <p class="mt-3 text-lg font-black text-slate-800">{{ $fine->borrowing->borrow_date?->translatedFormat('d M Y') ?? '-' }}</p>
                    </div>
                    <div class="rounded-[1.75rem] border border-amber-100 bg-amber-50 p-5">
                        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-amber-500">Jatuh Tempo</p>
                        <p class="mt-3 text-lg font-black text-amber-700">{{ $fine->borrowing->due_date?->translatedFormat('d M Y') ?? '-' }}</p>
                    </div>
                    <div class="rounded-[1.75rem] border border-emerald-100 bg-emerald-50 p-5">
                        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-emerald-500">Tanggal Kembali</p>
                        <p class="mt-3 text-lg font-black text-emerald-700">{{ $fine->borrowing->return_date?->translatedFormat('d M Y') ?? 'Belum kembali' }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-[2rem] border border-slate-100 bg-white p-8 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Breakdown Perhitungan</p>
                <div class="mt-6 space-y-4">
                    <div class="rounded-[1.75rem] border border-slate-100 bg-slate-50 p-5">
                        <p class="text-sm text-slate-600">
                            {{ $summary['charged_late_days'] }} hari ditagihkan x Rp {{ number_format($summary['late_fee_per_day'], 0, ',', '.') }}
                            = <span class="font-black text-slate-800">Rp {{ number_format($summary['late_fee_total'], 0, ',', '.') }}</span>
                        </p>
                        <p class="mt-2 text-xs text-slate-500">
                            Hari terlambat aktual {{ $summary['raw_late_days'] }} hari, grace period {{ $summary['grace_period_days'] }} hari.
                        </p>
                    </div>
                    @if($summary['max_fine_amount'])
                        <div class="rounded-[1.75rem] border border-indigo-100 bg-indigo-50/70 p-5">
                            <p class="text-sm text-indigo-700">
                                Maksimal denda untuk transaksi ini:
                                <span class="font-black">Rp {{ number_format($summary['max_fine_amount'], 0, ',', '.') }}</span>
                            </p>
                        </div>
                    @endif
                    @if($summary['damage_amount'] > 0)
                        <div class="rounded-[1.75rem] border border-rose-100 bg-rose-50/70 p-5">
                            <p class="text-sm text-rose-700">
                                Denda tambahan kerusakan/hilang:
                                <span class="font-black">Rp {{ number_format($summary['damage_amount'], 0, ',', '.') }}</span>
                            </p>
                        </div>
                    @endif
                    <div class="rounded-[1.75rem] border border-slate-100 bg-white p-5">
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-sm font-bold text-slate-800">Total akhir denda</span>
                            <span class="text-xl font-black text-rose-600">Rp {{ number_format($fine->amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <div class="rounded-[2rem] border border-slate-100 bg-white p-8 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Status Pembayaran</p>
                <div class="mt-6 rounded-[1.5rem] border px-4 py-4 {{ $fine->status === 'lunas' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-rose-200 bg-rose-50 text-rose-700' }}">
                    <p class="text-sm font-bold uppercase tracking-[0.18em]">{{ $summary['payment_status'] }}</p>
                    <p class="mt-2 text-sm">
                        @if($summary['payment_date'])
                            Dibayar pada {{ $summary['payment_date']->translatedFormat('d M Y H:i') }}
                        @else
                            Pembayaran belum tercatat.
                        @endif
                    </p>
                </div>
            </div>

            <div class="rounded-[2rem] border border-slate-100 bg-white p-8 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Aksi Admin</p>
                <div class="mt-6 flex flex-col gap-3">
                    <a href="{{ route('admin.borrowings.show', $fine->borrowing) }}" class="w-full rounded-2xl bg-slate-900 px-4 py-3 text-center font-bold text-white transition-all hover:bg-slate-950">
                        Lihat Detail Peminjaman
                    </a>
                    @if($fine->status === 'belum_lunas')
                        <form action="{{ route('admin.fines.pay', $fine) }}" method="POST" x-data="{ submitting: false }" @submit="submitting = true">
                            @csrf
                            <button type="submit" class="w-full rounded-2xl bg-emerald-600 px-4 py-3 font-bold text-white transition-all hover:bg-emerald-700" :disabled="submitting">
                                <span x-show="!submitting">Tandai Sebagai Sudah Dibayar</span>
                                <span x-show="submitting" x-cloak>Memproses...</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
