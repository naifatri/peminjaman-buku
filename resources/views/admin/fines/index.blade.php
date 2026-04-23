@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Manajemen Denda</h2>
    <p class="text-sm text-slate-500 mt-1">Kelola dan pantau status pembayaran denda keterlambatan pengembalian buku.</p>
</div>

<!-- Search & Filter -->
<div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 mb-8">
    <form action="{{ route('admin.fines.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2 relative">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peminjam atau judul buku..." 
                class="w-full pl-12 pr-4 py-3 rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 text-slate-600 placeholder:text-slate-300">
        </div>

        <select name="status" class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 text-slate-600 px-4 py-3 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat">
            <option value="">Semua Status</option>
            <option value="belum_lunas" {{ request('status') == 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
            <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
        </select>

        <div class="flex gap-2">
            <button type="submit" class="flex-1 px-4 py-3 bg-slate-800 hover:bg-slate-900 text-white rounded-2xl font-bold transition-all duration-300">
                Filter
            </button>
            <a href="{{ route('admin.fines.index') }}" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl font-bold transition-all duration-300 flex items-center justify-center" title="Reset">
                <i class="fas fa-undo"></i>
            </a>
        </div>
    </form>
</div>

<!-- Tabel Denda -->
<div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Peminjam & Buku</th>
                    <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100 text-center">Keterlambatan</th>
                    <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Jumlah Denda</th>
                    <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Status</th>
                    <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($fines as $fine)
                <tr class="group hover:bg-slate-50/50 transition-colors">
                    <td class="px-8 py-5">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">{{ $fine->borrowing->user->name }}</span>
                            <span class="text-xs text-slate-400 line-clamp-1 mt-0.5 italic">{{ $fine->borrowing->book->title }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5 text-center">
                        <span class="px-3 py-1 rounded-lg bg-rose-50 text-rose-600 text-[10px] font-bold uppercase tracking-wider">
                            {{ $fine->days_late }} Hari
                        </span>
                    </td>
                    <td class="px-8 py-5">
                        <span class="text-sm font-black text-slate-800">Rp {{ number_format($fine->amount, 0, ',', '.') }}</span>
                    </td>
                    <td class="px-8 py-5">
                        @if($fine->status === 'lunas')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tighter bg-emerald-50 text-emerald-600 border border-emerald-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-2"></span>
                                Lunas
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tighter bg-rose-50 text-rose-600 border border-rose-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-2"></span>
                                Belum Lunas
                            </span>
                        @endif
                    </td>
                    <td class="px-8 py-5 text-right">
                        @if($fine->status === 'belum_lunas')
                            <form action="{{ route('admin.fines.pay', $fine) }}" method="POST" class="inline-block" onsubmit="return confirm('Konfirmasi pembayaran denda ini?');">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-xl text-xs font-bold transition-all duration-300">
                                    <i class="fas fa-check mr-2"></i>
                                    Bayar Denda
                                </button>
                            </form>
                        @else
                            <div class="flex flex-col items-end">
                                <span class="text-slate-300 font-medium text-[10px] italic uppercase tracking-widest">Dibayar pada</span>
                                <span class="text-sm text-slate-500 font-bold">{{ \Carbon\Carbon::parse($fine->paid_at)->format('d M Y') }}</span>
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4">
                                <i class="fas fa-wallet text-2xl text-slate-200"></i>
                            </div>
                            <p class="text-slate-400 font-medium">Tidak ada data denda ditemukan.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-8">
    {{ $fines->links() }}
</div>
@endsection