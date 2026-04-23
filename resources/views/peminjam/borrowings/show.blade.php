<x-peminjam-layout page-title="Detail Transaksi">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <a href="{{ route('peminjam.borrowings.index') }}" class="inline-flex items-center text-sm font-semibold text-slate-400 hover:text-indigo-600 transition-colors mb-4 group">
                <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
                Kembali ke Riwayat
            </a>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Detail Peminjaman #{{ $borrowing->id }}</h2>
        </div>
        <div class="flex items-center gap-3">
            @php
                $statusClasses = [
                    'diajukan' => 'bg-slate-100 text-slate-600',
                    'dipinjam' => 'bg-amber-100 text-amber-700',
                    'terlambat' => 'bg-rose-100 text-rose-700',
                    'dikembalikan' => 'bg-indigo-100 text-indigo-700',
                    'verifikasi_denda' => 'bg-rose-100 text-rose-700',
                    'proses_bayar' => 'bg-amber-100 text-amber-700',
                    'selesai' => 'bg-emerald-100 text-emerald-700',
                    'ditolak' => 'bg-slate-200 text-slate-700',
                ];
            @endphp
            <span class="px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest {{ $statusClasses[$borrowing->status] ?? 'bg-slate-100 text-slate-500' }}">
                {{ str_replace('_', ' ', $borrowing->status) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Book Info Card -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
                <div class="flex items-start gap-6">
                    <div class="w-32 h-44 rounded-2xl bg-slate-100 overflow-hidden flex-shrink-0 p-2 flex items-center justify-center">
                        @if($borrowing->book->cover_image)
                            <img src="{{ asset('storage/' . $borrowing->book->cover_image) }}" class="w-full h-full object-contain">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                <i class="fas fa-image text-3xl"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-slate-800">{{ $borrowing->book->title }}</h3>
                        <p class="text-slate-500 text-sm mt-1">{{ $borrowing->book->author }}</p>
                        
                        <div class="mt-6 grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Jumlah Pinjam</p>
                                <p class="text-sm font-bold text-slate-700">{{ $borrowing->quantity }} Buku</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">ID Transaksi</p>
                                <p class="text-sm font-bold text-slate-700">#{{ $borrowing->id }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline/Dates Card -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
                <h4 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
                    <i class="fas fa-calendar-alt text-indigo-500 mr-3"></i>
                    Informasi Waktu
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Tanggal Pinjam</p>
                        <p class="text-sm font-black text-slate-700">{{ \Carbon\Carbon::parse($borrowing->borrow_date)->format('d F Y') }}</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-indigo-50 border border-indigo-100">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-indigo-400 mb-1">Batas Kembali</p>
                        <p class="text-sm font-black text-indigo-700">{{ \Carbon\Carbon::parse($borrowing->due_date)->format('d F Y') }}</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Tanggal Kembali</p>
                        <p class="text-sm font-black text-slate-700">{{ $borrowing->return_date ? \Carbon\Carbon::parse($borrowing->return_date)->format('d F Y') : '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Reasons & Notes -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
                <div class="space-y-6">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Alasan Meminjam</p>
                        <p class="text-sm text-slate-600 bg-slate-50 p-4 rounded-2xl border border-slate-100 italic">"{{ $borrowing->borrow_reason }}"</p>
                    </div>
                    @if($borrowing->return_notes)
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Catatan Pengembalian Anda</p>
                        <p class="text-sm text-slate-600 bg-slate-50 p-4 rounded-2xl border border-slate-100 italic">"{{ $borrowing->return_notes }}"</p>
                    </div>
                    @endif
                    @if($borrowing->admin_notes)
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-indigo-400 mb-2">Catatan Verifikasi Petugas</p>
                        <p class="text-sm text-indigo-700 bg-indigo-50/50 p-4 rounded-2xl border border-indigo-100 italic">"{{ $borrowing->admin_notes }}"</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Info (Fines & Actions) -->
        <div class="space-y-8">
            @if($borrowing->fine_amount > 0 || $borrowing->fine)
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 overflow-hidden relative group">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-50 rounded-full group-hover:scale-110 transition-transform duration-500"></div>
                <div class="relative">
                    <h4 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
                        <i class="fas fa-wallet text-rose-500 mr-3"></i>
                        Rincian Denda
                    </h4>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-400">Denda Keterlambatan</span>
                            <span class="font-bold text-slate-700">Rp {{ number_format(($borrowing->fine->days_late ?? 0) * 5000, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-400">Denda Kerusakan</span>
                            <span class="font-bold text-slate-700">Rp {{ number_format($borrowing->fine->damage_amount ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="pt-4 border-t border-slate-50 flex justify-between items-center">
                            <span class="text-sm font-bold text-slate-800">Total Denda</span>
                            <span class="text-xl font-black text-rose-600">Rp {{ number_format($borrowing->fine_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    @if($borrowing->status === 'selesai' && $borrowing->fine && $borrowing->fine->status === 'lunas')
                        <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-100 flex items-center gap-3">
                            <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-600">Status Pembayaran</p>
                                <p class="text-xs font-bold text-emerald-700">Lunas via {{ strtoupper($borrowing->fine->payment_method ?? 'TUNAI') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <div class="bg-indigo-600 rounded-[2rem] shadow-xl shadow-indigo-200 p-8 text-white">
                <h4 class="text-lg font-bold mb-4">Informasi Bantuan</h4>
                <p class="text-indigo-100 text-xs leading-relaxed mb-6">Jika terjadi kendala pada proses peminjaman atau pembayaran denda, silakan hubungi petugas perpustakaan di meja layanan fisik.</p>
                <div class="space-y-3">
                    <div class="flex items-center gap-3 text-sm">
                        <i class="fas fa-phone-alt opacity-60"></i>
                        <span>(021) 1234-5678</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <i class="fas fa-envelope opacity-60"></i>
                        <span>support@sipbuk.id</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-peminjam-layout>
