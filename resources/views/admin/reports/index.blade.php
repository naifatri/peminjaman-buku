@extends('layouts.admin')

@section('header', 'Laporan Peminjaman')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Laporan Peminjaman</h2>
        <p class="text-slate-500 text-sm">Analisis data peminjaman buku per periode.</p>
    </div>
    
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.reports.export-pdf', request()->all()) }}" class="flex items-center px-5 py-2.5 bg-rose-500 hover:bg-rose-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-rose-200 transition-all duration-300 transform hover:scale-[1.02]">
            <i class="fas fa-file-pdf mr-2.5"></i>
            Export PDF
        </a>
        <a href="{{ route('admin.reports.export-excel', request()->all()) }}" class="flex items-center px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-200 transition-all duration-300 transform hover:scale-[1.02]">
            <i class="fas fa-file-csv mr-2.5"></i>
            Export CSV
        </a>
    </div>
</div>

<!-- Filter Section -->
<div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 mb-8">
    <div class="flex items-center space-x-3 mb-6">
        <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center">
            <i class="fas fa-filter text-indigo-500"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-800">Filter Laporan</h3>
    </div>
    
    <form action="{{ route('admin.reports.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
        <div class="space-y-2">
            <label for="start_date" class="text-xs font-bold uppercase tracking-widest text-slate-400 ml-1">Tanggal Mulai</label>
            <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="block w-full px-4 py-3 rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200 bg-slate-50/50">
        </div>
        
        <div class="space-y-2">
            <label for="end_date" class="text-xs font-bold uppercase tracking-widest text-slate-400 ml-1">Tanggal Selesai</label>
            <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="block w-full px-4 py-3 rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200 bg-slate-50/50">
        </div>
        
        <div class="space-y-2">
            <label for="status" class="text-xs font-bold uppercase tracking-widest text-slate-400 ml-1">Status</label>
            <select name="status" id="status" class="block w-full px-4 py-3 rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200 bg-slate-50/50">
                <option value="">Semua Status</option>
                <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
            </select>
        </div>
        
        <div class="flex gap-2">
            <button type="submit" class="flex-1 py-3.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl font-bold text-sm transition-all duration-200">
                Terapkan Filter
            </button>
            <a href="{{ route('admin.reports.index') }}" class="px-5 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold text-sm transition-all duration-200">
                <i class="fas fa-undo"></i>
            </a>
        </div>
    </form>
</div>

<!-- Report Table -->
<div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Peminjam</th>
                    <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Buku</th>
                    <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Tgl Pinjam</th>
                    <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Tgl Kembali</th>
                    <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Status</th>
                    <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Denda</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @php $totalDenda = 0; @endphp
                @forelse($borrowings as $borrowing)
                @php $totalDenda += $borrowing->fine_amount; @endphp
                <tr class="group hover:bg-slate-50/50 transition-colors">
                    <td class="px-8 py-6">
                        <span class="text-sm font-bold text-slate-800">{{ $borrowing->user->name }}</span>
                    </td>
                    <td class="px-8 py-6 text-sm text-slate-600">
                        {{ $borrowing->book->title }}
                    </td>
                    <td class="px-8 py-6 text-sm text-slate-500">
                        <span class="inline-flex items-center">
                            <i class="far fa-calendar-alt mr-2 opacity-40"></i>
                            {{ $borrowing->borrow_date }}
                        </span>
                    </td>
                    <td class="px-8 py-6 text-sm text-slate-500">
                        @if($borrowing->return_date)
                            <span class="inline-flex items-center">
                                <i class="far fa-calendar-check mr-2 opacity-40"></i>
                                {{ $borrowing->return_date }}
                            </span>
                        @else
                            <span class="text-slate-300">-</span>
                        @endif
                    </td>
                    <td class="px-8 py-6">
                        @if($borrowing->status === 'dipinjam')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tighter bg-amber-50 text-amber-600 border border-amber-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-2 animate-pulse"></span>
                                Dipinjam
                            </span>
                        @elseif($borrowing->status === 'dikembalikan')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tighter bg-emerald-50 text-emerald-600 border border-emerald-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-2"></span>
                                Kembali
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tighter bg-rose-50 text-rose-600 border border-rose-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-2"></span>
                                Terlambat
                            </span>
                        @endif
                    </td>
                    <td class="px-8 py-6">
                        @if($borrowing->fine_amount > 0)
                            <span class="text-sm font-black text-rose-600">Rp {{ number_format($borrowing->fine_amount, 0, ',', '.') }}</span>
                        @else
                            <span class="text-slate-300">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-8 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-search text-5xl text-slate-200 mb-4"></i>
                            <p class="text-slate-400 font-medium">Data tidak ditemukan dalam periode ini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($borrowings->count() > 0)
            <tfoot class="bg-slate-50/50">
                <tr>
                    <td colspan="5" class="px-8 py-6 text-right">
                        <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Total Akumulasi Denda:</span>
                    </td>
                    <td class="px-8 py-6">
                        <span class="text-xl font-black text-rose-600">Rp {{ number_format($totalDenda, 0, ',', '.') }}</span>
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

<div class="mt-8">
    {{ $borrowings->links() }}
</div>
@endsection
