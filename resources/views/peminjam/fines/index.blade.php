<x-peminjam-layout page-title="Denda Saya">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Denda Saya</h2>
        <p class="text-sm text-slate-500 mt-1">Lihat rincian denda keterlambatan dan pantau status pembayaranmu.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-2xl bg-rose-500 text-white shadow-lg shadow-rose-200">
                    <i class="fas fa-file-invoice-dollar text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Belum Lunas</p>
                    <p class="text-3xl font-black text-slate-800">{{ $unpaidCount }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-2xl bg-amber-500 text-white shadow-lg shadow-amber-200">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Tagihan Aktif</p>
                    <p class="text-lg font-black text-slate-800">Rp {{ number_format($unpaidAmount, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-2xl bg-emerald-500 text-white shadow-lg shadow-emerald-200">
                    <i class="fas fa-circle-check text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Total Terbayar</p>
                    <p class="text-lg font-black text-slate-800">Rp {{ number_format($paidAmount, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Buku</th>
                        <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Tanggal Pinjam</th>
                        <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100 text-center">Keterlambatan</th>
                        <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Jumlah Denda</th>
                        <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Status</th>
                        <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Tanggal Bayar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($fines as $fine)
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="px-8 py-5">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">{{ $fine->borrowing->book->title }}</span>
                                    <span class="text-xs text-slate-400 italic mt-1">Ref: #{{ $fine->id }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-sm text-slate-600 font-medium">
                                {{ \Carbon\Carbon::parse($fine->borrowing->borrow_date)->format('d M Y') }}
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
                            <td class="px-8 py-5 text-sm text-slate-600 font-medium">
                                {{ $fine->paid_at ? \Carbon\Carbon::parse($fine->paid_at)->format('d M Y') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4">
                                        <i class="fas fa-wallet text-2xl text-slate-200"></i>
                                    </div>
                                    <p class="text-slate-400 font-medium">Tidak ada catatan denda. Bagus!</p>
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
</x-peminjam-layout>
