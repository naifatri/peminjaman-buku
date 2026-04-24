@extends('layouts.admin')

@php
    $activeFilterCount = collect($filters)->filter(fn ($value) => filled($value))->count();
@endphp

@section('content')
<div class="space-y-8" x-data="{ loading: false, exporting: false }">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Laporan Peminjaman</h2>
            <p class="mt-1 text-sm text-slate-500">Dashboard laporan dan analitik peminjaman buku untuk membantu admin membaca tren, status, dan denda.</p>
        </div>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.reports.export-pdf', request()->query()) }}"
                @click="exporting = true"
                class="inline-flex items-center rounded-2xl bg-rose-500 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-rose-200 transition-all hover:bg-rose-600">
                <i class="fas fa-file-pdf mr-2"></i>
                Export PDF
            </a>
            <a href="{{ route('admin.reports.export-excel', request()->query()) }}"
                @click="exporting = true"
                class="inline-flex items-center rounded-2xl bg-emerald-500 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-200 transition-all hover:bg-emerald-600">
                <i class="fas fa-file-csv mr-2"></i>
                Export CSV
            </a>
        </div>
    </div>

    <div class="metric-grid grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Total Peminjaman</p>
            <p class="mt-4 text-3xl font-black text-slate-800">{{ number_format($summary['total_borrowings']) }}</p>
            <p class="mt-2 text-sm text-slate-500">Total transaksi sesuai filter aktif.</p>
        </div>
        <div class="rounded-[2rem] border border-emerald-100 bg-emerald-50/80 p-6 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-emerald-500">Sudah Dikembalikan</p>
            <p class="mt-4 text-3xl font-black text-emerald-700">{{ number_format($summary['returned_borrowings']) }}</p>
            <p class="mt-2 text-sm text-emerald-700/70">Transaksi yang telah selesai.</p>
        </div>
        <div class="rounded-[2rem] border border-rose-100 bg-rose-50/80 p-6 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-rose-500">Total Keterlambatan</p>
            <p class="mt-4 text-3xl font-black text-rose-700">{{ number_format($summary['late_borrowings']) }}</p>
            <p class="mt-2 text-sm text-rose-700/70">Perlu perhatian karena terlambat.</p>
        </div>
        <div class="rounded-[2rem] border border-amber-100 bg-amber-50/80 p-6 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-amber-500">Total Denda</p>
            <p class="mt-4 text-3xl font-black text-amber-700">Rp {{ number_format($summary['total_fines'], 0, ',', '.') }}</p>
            <p class="mt-2 text-sm text-amber-700/70">Akumulasi denda dari hasil filter.</p>
        </div>
    </div>

    <div class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Filter Laporan</p>
                <h3 class="mt-2 text-xl font-black text-slate-800">Cari dan segmentasikan data</h3>
            </div>
            <div class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-slate-500">
                <i class="fas fa-sliders"></i>
                <span>{{ $activeFilterCount }} filter aktif</span>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-2">
            @foreach (['today' => 'Hari Ini', 'this_week' => 'Minggu Ini', 'this_month' => 'Bulan Ini'] as $value => $label)
                <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['quick_filter' => $value, 'start_date' => null, 'end_date' => null])) }}"
                    class="inline-flex items-center rounded-full border px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] transition-all {{ $filters['quick_filter'] === $value ? 'border-indigo-200 bg-indigo-50 text-indigo-700' : 'border-slate-200 bg-white text-slate-500 hover:border-slate-300 hover:bg-slate-50' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <form action="{{ route('admin.reports.index') }}" method="GET" class="responsive-filter-form mt-6 grid grid-cols-1 gap-4 lg:grid-cols-6" @submit="loading = true">
            <div class="relative lg:col-span-2">
                <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="user_name" value="{{ $filters['user_name'] }}" placeholder="Cari nama pengguna..." class="w-full rounded-2xl border-slate-200 py-3 pl-12 pr-4 text-slate-600 placeholder:text-slate-300 focus:border-indigo-500 focus:ring-indigo-500/10">
            </div>
            <div class="relative lg:col-span-2">
                <i class="fas fa-book absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="book_title" value="{{ $filters['book_title'] }}" placeholder="Cari judul buku..." class="w-full rounded-2xl border-slate-200 py-3 pl-12 pr-4 text-slate-600 placeholder:text-slate-300 focus:border-indigo-500 focus:ring-indigo-500/10">
            </div>
            <div>
                <input type="date" name="start_date" value="{{ $filters['start_date'] }}" class="w-full rounded-2xl border-slate-200 px-4 py-3 text-slate-600 focus:border-indigo-500 focus:ring-indigo-500/10">
            </div>
            <div>
                <input type="date" name="end_date" value="{{ $filters['end_date'] }}" class="w-full rounded-2xl border-slate-200 px-4 py-3 text-slate-600 focus:border-indigo-500 focus:ring-indigo-500/10">
            </div>
            <div>
                <select name="status" class="w-full rounded-2xl border-slate-200 px-4 py-3 text-slate-600 focus:border-indigo-500 focus:ring-indigo-500/10">
                    <option value="">Semua Status</option>
                    <option value="diajukan" @selected($filters['status'] === 'diajukan')>Diajukan</option>
                    <option value="dipinjam" @selected($filters['status'] === 'dipinjam')>Dipinjam</option>
                    <option value="terlambat" @selected($filters['status'] === 'terlambat')>Terlambat</option>
                    <option value="selesai" @selected($filters['status'] === 'selesai')>Selesai</option>
                    <option value="ditolak" @selected($filters['status'] === 'ditolak')>Ditolak</option>
                </select>
            </div>
            <div>
                <select name="sort" class="w-full rounded-2xl border-slate-200 px-4 py-3 text-slate-600 focus:border-indigo-500 focus:ring-indigo-500/10">
                    <option value="borrow_date_desc" @selected($filters['sort'] === 'borrow_date_desc')>Tanggal pinjam terbaru</option>
                    <option value="borrow_date_asc" @selected($filters['sort'] === 'borrow_date_asc')>Tanggal pinjam terlama</option>
                    <option value="return_date_desc" @selected($filters['sort'] === 'return_date_desc')>Tanggal kembali terbaru</option>
                    <option value="return_date_asc" @selected($filters['sort'] === 'return_date_asc')>Tanggal kembali terlama</option>
                    <option value="fine_desc" @selected($filters['sort'] === 'fine_desc')>Denda terbesar</option>
                    <option value="fine_asc" @selected($filters['sort'] === 'fine_asc')>Denda terkecil</option>
                </select>
            </div>
            <input type="hidden" name="quick_filter" value="{{ $filters['quick_filter'] }}">

            <div class="responsive-filter-actions flex gap-2 lg:col-span-6 lg:justify-end">
                <button type="submit" class="rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition-all hover:bg-slate-950" :disabled="loading || exporting">
                    <span x-show="!loading">Terapkan Filter</span>
                    <span x-show="loading" x-cloak>Memuat...</span>
                </button>
                <a href="{{ route('admin.reports.index') }}" class="flex items-center justify-center rounded-2xl bg-slate-100 px-4 py-3 text-slate-600 transition-all hover:bg-slate-200" title="Reset">
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 gap-8 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Tren Peminjaman</p>
                    <h3 class="mt-2 text-xl font-black text-slate-800">Peminjaman per hari / bulan</h3>
                </div>
                <div class="rounded-full bg-slate-100 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-slate-500">
                    Mengikuti filter
                </div>
            </div>
            <div class="mt-6 h-[320px]">
                <canvas id="borrowingsTrendChart"></canvas>
            </div>
        </div>

        <div class="space-y-8">
            <div class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Distribusi Status</p>
                <h3 class="mt-2 text-xl font-black text-slate-800">Selesai vs terlambat</h3>
                <div class="mt-6 h-[240px]">
                    <canvas id="statusPieChart"></canvas>
                </div>
            </div>

            <div class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Agregasi Status</p>
                <div class="mt-6 space-y-3">
                    @foreach ($statusBreakdown as $label => $count)
                        <div class="flex items-center justify-between rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3 text-sm">
                            <span class="font-semibold text-slate-600">{{ ucfirst($label) }}</span>
                            <span class="font-black text-slate-800">{{ number_format($count) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="responsive-table-card overflow-hidden rounded-[2rem] border border-slate-100 bg-white shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-slate-100 px-6 py-5">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Data Detail</p>
                <h3 class="mt-2 text-xl font-black text-slate-800">Transaksi peminjaman</h3>
            </div>
            <div class="rounded-full bg-amber-50 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-amber-700">
                Klik baris untuk detail
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1200px] border-collapse text-left">
                <thead>
                    <tr class="bg-slate-50/80">
                        <th class="border-b border-slate-100 px-6 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Peminjam</th>
                        <th class="border-b border-slate-100 px-6 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Buku</th>
                        <th class="border-b border-slate-100 px-6 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Tanggal Pinjam</th>
                        <th class="border-b border-slate-100 px-6 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Tanggal Kembali</th>
                        <th class="border-b border-slate-100 px-6 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Status</th>
                        <th class="border-b border-slate-100 px-6 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Denda</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($borrowings as $borrowing)
                        @php
                            $isLate = $borrowing->admin_status === 'terlambat';
                            $isHighFine = (float) $borrowing->fine_amount >= 50000;
                        @endphp
                        <tr onclick="window.location='{{ route('admin.borrowings.show', $borrowing) }}'"
                            class="cursor-pointer transition-colors hover:bg-slate-50/70 {{ $isLate ? 'bg-rose-50/20' : '' }}">
                            <td class="px-6 py-5">
                                <div>
                                    <p class="text-sm font-bold text-slate-800">{{ $borrowing->user->name }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $borrowing->user->email }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-sm text-slate-600">{{ $borrowing->book->title }}</td>
                            <td class="px-6 py-5 text-sm font-semibold text-slate-700">{{ $borrowing->borrow_date?->translatedFormat('d M Y') ?? '-' }}</td>
                            <td class="px-6 py-5 text-sm font-semibold text-slate-700">{{ $borrowing->return_date?->translatedFormat('d M Y') ?? '-' }}</td>
                            <td class="px-6 py-5">
                                <span class="inline-flex items-center rounded-full border px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] {{ $borrowing->admin_status_color }}">
                                    {{ $borrowing->admin_status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                @if($borrowing->fine_amount > 0)
                                    <span class="text-sm font-black {{ $isHighFine ? 'text-rose-700' : 'text-amber-700' }}">
                                        Rp {{ number_format($borrowing->fine_amount, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-sm text-slate-300">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16">
                                <div class="mx-auto flex max-w-md flex-col items-center text-center">
                                    <div class="flex h-20 w-20 items-center justify-center rounded-full bg-slate-100 text-slate-300">
                                        <i class="fas fa-chart-line text-3xl"></i>
                                    </div>
                                    <h3 class="mt-5 text-lg font-bold text-slate-800">Belum ada data laporan</h3>
                                    <p class="mt-2 text-sm text-slate-500">Ubah filter atau rentang tanggal untuk menemukan transaksi yang ingin dianalisis.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $borrowings->links() }}
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const trendCtx = document.getElementById('borrowingsTrendChart');
    const pieCtx = document.getElementById('statusPieChart');

    if (trendCtx) {
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: @json($charts['trend']['labels']),
                datasets: [{
                    label: 'Jumlah peminjaman',
                    data: @json($charts['trend']['values']),
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.12)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointHoverRadius: 5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    if (pieCtx) {
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: @json($charts['status']['labels']),
                datasets: [{
                    data: @json($charts['status']['values']),
                    backgroundColor: ['#10b981', '#f43f5e', '#f59e0b'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
</script>
@endsection
