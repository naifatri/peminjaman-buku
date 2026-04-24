@extends('layouts.admin')

@section('content')
<div class="space-y-8" x-data="{ loading: false }">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Manajemen Denda</h2>
            <p class="text-sm text-slate-500 mt-1">Pantau tunggakan, status pembayaran, dan detail perhitungan denda keterlambatan secara profesional.</p>
        </div>
        <div class="inline-flex items-center gap-2 rounded-full bg-rose-600 px-4 py-2 text-xs font-bold uppercase tracking-[0.2em] text-white">
            <i class="fas fa-file-invoice-dollar"></i>
            <span>Denda Aktif</span>
        </div>
    </div>

    <div class="metric-grid grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        <div class="rounded-[2rem] border border-rose-100 bg-rose-50/80 p-6 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-rose-500">Total Belum Dibayar</p>
            <p class="mt-4 text-3xl font-black text-rose-700">Rp {{ number_format($stats['unpaid_total'], 0, ',', '.') }}</p>
            <p class="mt-2 text-sm text-rose-700/70">Akumulasi seluruh denda yang masih menunggu pelunasan.</p>
        </div>
        <div class="rounded-[2rem] border border-emerald-100 bg-emerald-50/80 p-6 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-emerald-500">Total Sudah Dibayar</p>
            <p class="mt-4 text-3xl font-black text-emerald-700">Rp {{ number_format($stats['paid_total'], 0, ',', '.') }}</p>
            <p class="mt-2 text-sm text-emerald-700/70">Riwayat pelunasan yang sudah tercatat oleh admin.</p>
        </div>
        <div class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">User Memiliki Denda</p>
            <p class="mt-4 text-3xl font-black text-slate-800">{{ number_format($stats['users_with_fines']) }}</p>
            <p class="mt-2 text-sm text-slate-500">Jumlah pengguna unik yang pernah memiliki tagihan denda.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
        <div class="rounded-[2rem] border border-rose-100 bg-gradient-to-br from-rose-50 to-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-rose-500">Belum Bayar</p>
                    <h3 class="mt-2 text-xl font-black text-slate-800">{{ $alerts['unpaid_count'] }} denda</h3>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600">
                    <i class="fas fa-bell text-lg"></i>
                </div>
            </div>
            <div class="mt-5 space-y-3">
                @forelse($alerts['unpaid_items'] as $fine)
                    <div class="rounded-2xl border border-rose-100 bg-white/90 p-4">
                        <p class="text-sm font-bold text-slate-800">{{ $fine->borrowing->user->name }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ $fine->borrowing->book->title }}</p>
                        <p class="mt-2 text-xs font-semibold text-rose-600">Tagihan Rp {{ number_format($fine->amount, 0, ',', '.') }}</p>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-rose-200 bg-white/80 p-4 text-sm text-slate-500">
                        Tidak ada denda belum bayar saat ini.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="rounded-[2rem] border border-amber-100 bg-gradient-to-br from-amber-50 to-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-amber-500">Tunggakan Lama</p>
                    <h3 class="mt-2 text-xl font-black text-slate-800">{{ $alerts['old_unpaid_count'] }} denda</h3>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-600">
                    <i class="fas fa-clock text-lg"></i>
                </div>
            </div>
            <div class="mt-5 space-y-3">
                @forelse($alerts['old_unpaid_items'] as $fine)
                    <div class="rounded-2xl border border-amber-100 bg-white/90 p-4">
                        <p class="text-sm font-bold text-slate-800">{{ $fine->borrowing->user->name }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ $fine->borrowing->book->title }}</p>
                        <p class="mt-2 text-xs font-semibold text-amber-600">Belum dibayar {{ $fine->payment_age_in_days }} hari</p>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-amber-200 bg-white/80 p-4 text-sm text-slate-500">
                        Tidak ada tunggakan lama yang perlu perhatian khusus.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm">
        <div class="mb-5 rounded-[1.5rem] border border-indigo-100 bg-indigo-50/70 px-5 py-4 text-sm text-indigo-700">
            Tarif aktif: <span class="font-black">Rp {{ number_format($activePolicy['late_fee_per_day'], 0, ',', '.') }}</span>/hari,
            grace period <span class="font-black">{{ $activePolicy['grace_period_days'] }} hari</span>,
            maksimal <span class="font-black">{{ $activePolicy['max_fine_amount'] ? 'Rp ' . number_format($activePolicy['max_fine_amount'], 0, ',', '.') : 'tanpa batas' }}</span>.
        </div>

        <form action="{{ route('admin.fines.index') }}" method="GET" class="responsive-filter-form grid grid-cols-1 gap-4 lg:grid-cols-6" @submit="loading = true">
            <div class="relative lg:col-span-2">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peminjam atau judul buku..." class="w-full rounded-2xl border-slate-200 py-3 pl-12 pr-4 text-slate-600 placeholder:text-slate-300 transition-all duration-300 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10">
            </div>

            <select name="status" class="w-full rounded-2xl border-slate-200 px-4 py-3 text-slate-600 transition-all duration-300 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10">
                <option value="">Semua Status</option>
                <option value="belum_lunas" @selected(request('status') === 'belum_lunas')>Belum Bayar</option>
                <option value="lunas" @selected(request('status') === 'lunas')>Lunas</option>
            </select>

            <select name="amount_range" class="w-full rounded-2xl border-slate-200 px-4 py-3 text-slate-600 transition-all duration-300 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10">
                <option value="">Semua Nominal</option>
                <option value="lt_10000" @selected(request('amount_range') === 'lt_10000')>< Rp 10.000</option>
                <option value="10000_25000" @selected(request('amount_range') === '10000_25000')>Rp 10.000 - Rp 25.000</option>
                <option value="gt_25000" @selected(request('amount_range') === 'gt_25000')>> Rp 25.000</option>
            </select>

            <select name="date_filter" class="w-full rounded-2xl border-slate-200 px-4 py-3 text-slate-600 transition-all duration-300 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10">
                <option value="">Semua Tanggal</option>
                <option value="today" @selected(request('date_filter') === 'today')>Hari Ini</option>
                <option value="this_week" @selected(request('date_filter') === 'this_week')>Minggu Ini</option>
                <option value="old_unpaid" @selected(request('date_filter') === 'old_unpaid')>Tunggakan Lama</option>
            </select>

            <select name="sort" class="w-full rounded-2xl border-slate-200 px-4 py-3 text-slate-600 transition-all duration-300 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10">
                <option value="latest" @selected(request('sort', 'latest') === 'latest')>Terbaru</option>
                <option value="highest" @selected(request('sort') === 'highest')>Denda Terbesar</option>
            </select>

            <div class="responsive-filter-actions flex gap-2 lg:col-span-6 lg:justify-end">
                <button type="submit" class="rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition-all duration-300 hover:bg-slate-950" :disabled="loading">
                    <span x-show="!loading">Terapkan</span>
                    <span x-show="loading" x-cloak>Memuat...</span>
                </button>
                <a href="{{ route('admin.fines.index') }}" class="flex items-center justify-center rounded-2xl bg-slate-100 px-4 py-3 text-slate-600 transition-all duration-300 hover:bg-slate-200" title="Reset">
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="responsive-table-card overflow-hidden rounded-[2rem] border border-slate-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1200px] border-collapse text-left">
                <thead>
                    <tr class="bg-slate-50/80">
                        <th class="border-b border-slate-100 px-6 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Pengguna & Buku</th>
                        <th class="border-b border-slate-100 px-6 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Tanggal</th>
                        <th class="border-b border-slate-100 px-6 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Keterlambatan</th>
                        <th class="border-b border-slate-100 px-6 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Total Denda</th>
                        <th class="border-b border-slate-100 px-6 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Status</th>
                        <th class="border-b border-slate-100 px-6 py-4 text-right text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($fines as $fine)
                        <tr class="transition-colors hover:bg-slate-50/70 {{ $fine->status === 'belum_lunas' ? 'bg-rose-50/20' : '' }}">
                            <td class="px-6 py-5">
                                <div class="flex items-start gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $fine->status === 'belum_lunas' ? 'bg-rose-100 text-rose-600' : 'bg-emerald-100 text-emerald-600' }}">
                                        <i class="fas fa-wallet"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">{{ $fine->borrowing->user->name }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $fine->borrowing->book->title }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="space-y-2 text-sm">
                                    <div>
                                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Pinjam</p>
                                        <p class="font-semibold text-slate-700">{{ $fine->borrowing->borrow_date?->translatedFormat('d M Y') ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Jatuh Tempo</p>
                                        <p class="font-semibold text-slate-700">{{ $fine->borrowing->due_date?->translatedFormat('d M Y') ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Kembali</p>
                                        <p class="font-semibold text-slate-700">{{ $fine->borrowing->return_date?->translatedFormat('d M Y') ?? 'Belum kembali' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="space-y-2 text-sm">
                                    <p class="font-bold text-rose-600">{{ $fine->days_late }} hari</p>
                                    <p class="text-slate-500">{{ $fine->charged_late_days }} hari ditagihkan • Rp {{ number_format($fine->late_fee_per_day, 0, ',', '.') }} per hari</p>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="space-y-2 text-sm">
                                    <p class="text-lg font-black {{ $fine->status === 'belum_lunas' ? 'text-rose-700' : 'text-slate-800' }}">Rp {{ number_format($fine->amount, 0, ',', '.') }}</p>
                                    <p class="text-xs text-slate-500">{{ $fine->charged_late_days }} x Rp {{ number_format($fine->late_fee_per_day, 0, ',', '.') }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="space-y-2">
                                    <span class="inline-flex items-center rounded-full border px-3 py-1 text-[11px] font-bold uppercase tracking-[0.2em] {{ $fine->status_color }}">
                                        {{ $fine->status_label }}
                                    </span>
                                    @if($fine->paid_at)
                                        <p class="text-xs text-slate-500">Dibayar {{ $fine->paid_at->translatedFormat('d M Y H:i') }}</p>
                                    @else
                                        <p class="text-xs text-slate-500">Belum dibayar {{ $fine->payment_age_in_days }} hari</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('admin.fines.show', $fine) }}" class="inline-flex items-center rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold text-slate-600 transition-all hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                                        Detail Denda
                                    </a>
                                    <a href="{{ route('admin.borrowings.show', $fine->borrowing) }}" class="inline-flex items-center rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold text-slate-600 transition-all hover:border-slate-300 hover:bg-slate-100">
                                        Detail Peminjaman
                                    </a>
                                    @if($fine->status === 'belum_lunas')
                                        <form action="{{ route('admin.fines.pay', $fine) }}" method="POST" x-data="{ submitting: false }" @submit="submitting = true">
                                            @csrf
                                            <button type="submit" class="rounded-xl bg-emerald-600 px-3 py-2 text-xs font-bold text-white transition-all hover:bg-emerald-700" :disabled="submitting">
                                                <span x-show="!submitting">Tandai Dibayar</span>
                                                <span x-show="submitting" x-cloak>Proses...</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16">
                                <div class="mx-auto flex max-w-md flex-col items-center text-center">
                                    <div class="flex h-20 w-20 items-center justify-center rounded-full bg-slate-100 text-slate-300">
                                        <i class="fas fa-wallet text-3xl"></i>
                                    </div>
                                    <h3 class="mt-5 text-lg font-bold text-slate-800">Tidak ada data denda</h3>
                                    <p class="mt-2 text-sm text-slate-500">Coba ubah filter atau kata kunci pencarian untuk melihat data denda lainnya.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $fines->links() }}
    </div>
</div>
@endsection
