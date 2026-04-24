@extends('layouts.admin')

@section('header', 'Dashboard')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Dashboard Admin</h2>
    <p class="text-slate-500 text-sm">Selamat datang kembali, berikut adalah statistik perpustakaan Anda hari ini.</p>
</div>

<div class="metric-grid grid grid-cols-1 md:grid-cols-3 gap-5 lg:gap-8 mb-10">
    <!-- Total Buku Card -->
    <div class="relative overflow-hidden bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-100 group hover:shadow-xl hover:shadow-indigo-500/5 transition-all duration-300">
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-indigo-50 rounded-full group-hover:scale-110 transition-transform duration-500 opacity-50"></div>
        <div class="relative flex items-center gap-4">
            <div class="p-4 rounded-2xl bg-indigo-500 text-white shadow-lg shadow-indigo-200 group-hover:rotate-6 transition-transform">
                <i class="fas fa-book text-2xl"></i>
            </div>
            <div>
                <p class="text-sm text-slate-400 font-bold uppercase tracking-wider mb-1">Total Buku</p>
                <p class="text-4xl font-black text-slate-800">{{ $totalBooks }}</p>
            </div>
        </div>
        <div class="mt-6 flex items-center text-xs font-semibold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full w-fit">
            <i class="fas fa-arrow-up mr-1.5"></i>
            <span>Terdata di sistem</span>
        </div>
    </div>
    
    <!-- Peminjaman Aktif Card -->
    <div class="relative overflow-hidden bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-100 group hover:shadow-xl hover:shadow-amber-500/5 transition-all duration-300">
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-amber-50 rounded-full group-hover:scale-110 transition-transform duration-500 opacity-50"></div>
        <div class="relative flex items-center gap-4">
            <div class="p-4 rounded-2xl bg-amber-500 text-white shadow-lg shadow-amber-200 group-hover:rotate-6 transition-transform">
                <i class="fas fa-exchange-alt text-2xl"></i>
            </div>
            <div>
                <p class="text-sm text-slate-400 font-bold uppercase tracking-wider mb-1">Peminjaman Aktif</p>
                <p class="text-4xl font-black text-slate-800">{{ $activeBorrowings }}</p>
            </div>
        </div>
        <div class="mt-6 flex items-center text-xs font-semibold text-amber-600 bg-amber-50 px-3 py-1 rounded-full w-fit">
            <i class="fas fa-clock mr-1.5"></i>
            <span>Sedang dipinjam</span>
        </div>
    </div>

    <!-- Denda Card -->
    <div class="relative overflow-hidden bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-100 group hover:shadow-xl hover:shadow-rose-500/5 transition-all duration-300">
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-rose-50 rounded-full group-hover:scale-110 transition-transform duration-500 opacity-50"></div>
        <div class="relative flex items-center gap-4">
            <div class="p-4 rounded-2xl bg-rose-500 text-white shadow-lg shadow-rose-200 group-hover:rotate-6 transition-transform">
                <i class="fas fa-wallet text-2xl"></i>
            </div>
            <div>
                <p class="text-sm text-slate-400 font-bold uppercase tracking-wider mb-1">Total Denda</p>
                <p class="text-3xl font-black text-slate-800">Rp {{ number_format($unpaidFines, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="mt-6 flex items-center text-xs font-semibold text-rose-600 bg-rose-50 px-3 py-1 rounded-full w-fit">
            <i class="fas fa-exclamation-circle mr-1.5"></i>
            <span>Belum dibayar</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-5 gap-5 lg:gap-8 mb-8">
    <!-- Chart Section -->
    <div class="lg:col-span-3 bg-white rounded-[2rem] shadow-sm border border-slate-100 p-5 sm:p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h3 class="text-xl font-bold text-slate-800">Statistik Peminjaman</h3>
                <p class="text-sm text-slate-400">Data peminjaman per bulan di tahun {{ $currentYear }}</p>
            </div>
            <div class="p-3 bg-slate-50 rounded-2xl">
                <i class="fas fa-chart-line text-indigo-500"></i>
            </div>
        </div>
        <div class="h-[350px]">
            <canvas id="borrowingsChart"></canvas>
        </div>
    </div>

    <!-- Recent Activity Table Section -->
    <div class="responsive-table-card lg:col-span-2 bg-white rounded-[2rem] shadow-sm border border-slate-100 flex flex-col overflow-hidden">
        <div class="p-5 sm:p-8 border-b border-slate-50 flex flex-col gap-4 sm:flex-row sm:justify-between sm:items-center">
            <div>
                <h3 class="text-xl font-bold text-slate-800">Peminjaman Terbaru</h3>
                <p class="text-sm text-slate-400">Aktivitas terakhir di perpustakaan</p>
            </div>
            <a href="{{ route('admin.borrowings.index') }}" class="p-3 bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-2xl transition-all duration-300 group">
                <i class="fas fa-arrow-right group-hover:translate-x-0.5 transition-transform"></i>
            </a>
        </div>
        <div class="flex-1 overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Peminjam</th>
                        <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Buku</th>
                        <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($recentBorrowings as $borrowing)
                    <tr class="group hover:bg-slate-50/50 transition-colors">
                        <td class="px-8 py-5">
                            <span class="text-sm font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">{{ $borrowing->user->name }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-sm text-slate-500 line-clamp-1">{{ $borrowing->book->title }}</span>
                        </td>
                        <td class="px-8 py-5">
                            @php
                                $statusClasses = [
                                    'diajukan' => 'bg-slate-100 text-slate-600 border-slate-200',
                                    'dipinjam' => 'bg-amber-50 text-amber-600 border-amber-100',
                                    'terlambat' => 'bg-rose-50 text-rose-600 border-rose-100',
                                    'dikembalikan' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                    'verifikasi_denda' => 'bg-rose-100 text-rose-700 border-rose-200',
                                    'proses_bayar' => 'bg-amber-100 text-amber-700 border-amber-200',
                                    'selesai' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                    'ditolak' => 'bg-slate-200 text-slate-700 border-slate-300',
                                ];
                                $statusLabels = [
                                    'diajukan' => 'Pengajuan',
                                    'dipinjam' => 'Dipinjam',
                                    'terlambat' => 'Terlambat',
                                    'dikembalikan' => 'Menunggu Verif',
                                    'verifikasi_denda' => 'Denda User',
                                    'proses_bayar' => 'Proses Bayar',
                                    'selesai' => 'Selesai',
                                    'ditolak' => 'Ditolak',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tighter border {{ $statusClasses[$borrowing->status] ?? 'bg-slate-50 text-slate-500 border-slate-100' }}">
                                @if(in_array($borrowing->status, ['diajukan', 'dipinjam', 'proses_bayar']))
                                    <span class="w-1.5 h-1.5 rounded-full bg-current mr-2 animate-pulse"></span>
                                @else
                                    <span class="w-1.5 h-1.5 rounded-full bg-current mr-2"></span>
                                @endif
                                {{ $statusLabels[$borrowing->status] ?? $borrowing->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-8 py-10 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-inbox text-4xl text-slate-200 mb-4"></i>
                                <p class="text-sm text-slate-400 italic">Belum ada data peminjaman.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('borrowingsChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.4)');
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($months) !!},
                datasets: [{
                    label: 'Jumlah Peminjaman',
                    data: {!! json_encode($borrowingsPerMonth) !!},
                    backgroundColor: gradient,
                    borderColor: '#6366f1',
                    borderWidth: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#6366f1',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            color: '#f8fafc',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                family: "'Figtree', sans-serif",
                                size: 11
                            },
                            color: '#94a3b8',
                            stepSize: 1
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: "'Figtree', sans-serif",
                                size: 11
                            },
                            color: '#94a3b8'
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
