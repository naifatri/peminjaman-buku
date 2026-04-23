@extends('layouts.admin')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <a href="{{ route('admin.borrowings.index') }}" class="inline-flex items-center text-sm font-semibold text-slate-400 hover:text-indigo-600 transition-colors mb-4 group">
            <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
            Kembali ke Daftar
        </a>
        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Detail Transaksi #{{ $borrowing->id }}</h2>
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

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 text-left">
    <!-- Main Info -->
    <div class="lg:col-span-2 space-y-8">
        <!-- User & Book Card -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- User Section -->
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl font-black">
                        {{ substr($borrowing->user->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Peminjam</p>
                        <h4 class="text-lg font-bold text-slate-800">{{ $borrowing->user->name }}</h4>
                        <p class="text-xs text-slate-500">{{ $borrowing->user->email }}</p>
                    </div>
                </div>
                <!-- Book Section -->
                <div class="flex items-center gap-4">
                    <div class="w-12 h-16 rounded-lg bg-slate-50 overflow-hidden border border-slate-100 p-1 flex items-center justify-center">
                        @if($borrowing->book->cover_image)
                            <img src="{{ asset('storage/' . $borrowing->book->cover_image) }}" class="w-full h-full object-contain">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-200"><i class="fas fa-book"></i></div>
                        @endif
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Buku</p>
                        <h4 class="text-sm font-bold text-slate-800 line-clamp-1">{{ $borrowing->book->title }}</h4>
                        <p class="text-xs text-slate-500">{{ $borrowing->quantity }} Eksemplar</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dates Card -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
            <h4 class="text-lg font-bold text-slate-800 mb-6">Timeline Peminjaman</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-5 rounded-3xl bg-slate-50 border border-slate-100">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Diajukan/Pinjam</p>
                    <p class="text-base font-bold text-slate-700">{{ \Carbon\Carbon::parse($borrowing->borrow_date)->format('d M Y') }}</p>
                </div>
                <div class="p-5 rounded-3xl bg-amber-50 border border-amber-100">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-amber-400 mb-1">Jatuh Tempo</p>
                    <p class="text-base font-bold text-amber-700">{{ \Carbon\Carbon::parse($borrowing->due_date)->format('d M Y') }}</p>
                </div>
                <div class="p-5 rounded-3xl bg-indigo-50 border border-indigo-100">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-indigo-400 mb-1">Dikembalikan</p>
                    <p class="text-base font-bold text-indigo-700">{{ $borrowing->return_date ? \Carbon\Carbon::parse($borrowing->return_date)->format('d M Y') : 'Belum Kembali' }}</p>
                </div>
            </div>
        </div>

        <!-- Notes Section -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
            <div class="space-y-6">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Alasan Pinjam (User)</label>
                    <div class="mt-2 p-4 bg-slate-50 rounded-2xl text-sm text-slate-600 border border-slate-100 italic">"{{ $borrowing->borrow_reason }}"</div>
                </div>
                @if($borrowing->return_notes)
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Catatan Pengembalian (User)</label>
                    <div class="mt-2 p-4 bg-slate-50 rounded-2xl text-sm text-slate-600 border border-slate-100 italic">"{{ $borrowing->return_notes }}"</div>
                </div>
                @endif
                @if($borrowing->admin_notes)
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-indigo-400">Catatan Verifikasi (Admin)</label>
                    <div class="mt-2 p-4 bg-indigo-50/50 rounded-2xl text-sm text-indigo-700 border border-indigo-100 italic">"{{ $borrowing->admin_notes }}"</div>
                </div>
                @endif
            </div>
        </div>

        @if($borrowing->fine && $borrowing->fine->payment_method === 'qris')
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 overflow-hidden relative">
            <div class="absolute right-0 top-0 h-40 w-40 rounded-full bg-indigo-50/70 blur-3xl"></div>
            <div class="relative">
                <div class="flex items-start justify-between gap-4 mb-6">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.28em] text-indigo-400 mb-2">Bukti Pembayaran</p>
                        <h4 class="text-xl font-black text-slate-800">Pembayaran QRIS</h4>
                        <p class="text-sm text-slate-500 mt-1">Tampilan bukti pembayaran digital untuk proses verifikasi admin.</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-sm">
                        <i class="fas fa-qrcode text-xl"></i>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-[1.2fr_0.8fr] gap-6">
                    <div class="rounded-[1.75rem] border border-slate-100 bg-slate-50/80 p-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="rounded-2xl bg-white border border-slate-100 p-4">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Nama Peminjam</p>
                                <p class="text-sm font-bold text-slate-800">{{ $borrowing->user->name }}</p>
                            </div>
                            <div class="rounded-2xl bg-white border border-slate-100 p-4">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Metode</p>
                                <p class="text-sm font-bold text-indigo-600">QRIS / E-Wallet</p>
                            </div>
                            <div class="rounded-2xl bg-white border border-slate-100 p-4">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Nominal</p>
                                <p class="text-lg font-black text-rose-500">Rp {{ number_format($borrowing->fine_amount, 0, ',', '.') }}</p>
                            </div>
                            <div class="rounded-2xl bg-white border border-slate-100 p-4">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Status Verifikasi</p>
                                <p class="text-sm font-bold {{ $borrowing->status === 'proses_bayar' ? 'text-amber-600' : 'text-emerald-600' }}">
                                    {{ $borrowing->status === 'proses_bayar' ? 'Menunggu Approve Admin' : 'Pembayaran Diverifikasi' }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 rounded-2xl bg-white border border-dashed border-slate-200 p-5">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Referensi</p>
                                    <p class="text-sm font-bold text-slate-700">TRX-QRIS-{{ str_pad((string) $borrowing->id, 5, '0', STR_PAD_LEFT) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Waktu</p>
                                    <p class="text-sm font-bold text-slate-700">
                                        {{ optional($borrowing->fine->paid_at)->format('d M Y H:i') ?? 'Menunggu pembayaran' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-[1.75rem] border border-dashed border-slate-200 bg-gradient-to-b from-slate-50 via-white to-white p-6 flex flex-col items-center justify-center text-center">
                        <p class="text-[10px] font-bold uppercase tracking-[0.32em] text-slate-400 leading-[1.9]">
                            Scan QRIS SIPBUK<br><span class="text-slate-300">Bukti pembayaran digital</span>
                        </p>

                        <div class="mt-5 rounded-[1.8rem] bg-white p-5 shadow-[0_28px_60px_-34px_rgba(37,99,235,0.28)]">
                            <svg width="138" height="138" viewBox="0 0 100 100" class="text-slate-800">
                                <rect width="100" height="100" fill="white"/>
                                <rect x="10" y="10" width="25" height="25" fill="currentColor"/>
                                <rect x="15" y="15" width="15" height="15" fill="white"/>
                                <rect x="18" y="18" width="9" height="9" fill="currentColor"/>
                                <rect x="65" y="10" width="25" height="25" fill="currentColor"/>
                                <rect x="70" y="15" width="15" height="15" fill="white"/>
                                <rect x="73" y="18" width="9" height="9" fill="currentColor"/>
                                <rect x="10" y="65" width="25" height="25" fill="currentColor"/>
                                <rect x="15" y="70" width="15" height="15" fill="white"/>
                                <rect x="18" y="73" width="9" height="9" fill="currentColor"/>
                                <rect x="40" y="40" width="20" height="20" fill="currentColor"/>
                                <rect x="45" y="45" width="10" height="10" fill="white"/>
                                <rect x="70" y="70" width="5" height="5" fill="currentColor"/>
                                <rect x="80" y="80" width="10" height="10" fill="currentColor"/>
                                <rect x="40" y="10" width="15" height="5" fill="currentColor"/>
                                <rect x="10" y="40" width="5" height="15" fill="currentColor"/>
                                <rect x="85" y="40" width="5" height="20" fill="currentColor"/>
                                <rect x="40" y="85" width="20" height="5" fill="currentColor"/>
                            </svg>
                        </div>

                        <div class="mt-5 inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-indigo-500 to-violet-600 px-4 py-2 text-[10px] font-bold uppercase tracking-wider text-white shadow-[0_20px_38px_-24px_rgba(79,70,229,0.95)]">
                            <i class="fas fa-shield-alt text-[11px]"></i>
                            <span>Terverifikasi Sistem</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar Info -->
    <div class="space-y-8">
        <!-- Condition Card -->
        @if($borrowing->book_condition)
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
            <h4 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">Kondisi Buku</h4>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400">
                    <i class="fas fa-info-circle"></i>
                </div>
                <span class="text-lg font-black text-slate-800 uppercase tracking-tighter">{{ str_replace('_', ' ', $borrowing->book_condition) }}</span>
            </div>
        </div>
        @endif

        <!-- Fine Details Card -->
        @if($borrowing->fine)
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-50 rounded-full group-hover:scale-110 transition-transform duration-500"></div>
            <div class="relative">
                <h4 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
                    <i class="fas fa-wallet text-rose-500 mr-3"></i>
                    Rincian Denda
                </h4>
                
                <div class="space-y-4 mb-6">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-400">Terlambat ({{ $borrowing->fine->days_late }} Hari)</span>
                        <span class="font-bold text-slate-700">Rp {{ number_format($borrowing->fine->days_late * 5000, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-400">Kerusakan</span>
                        <span class="font-bold text-slate-700">Rp {{ number_format($borrowing->fine->damage_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="pt-4 border-t border-slate-50 flex justify-between items-center">
                        <span class="text-sm font-bold text-slate-800">Total Tagihan</span>
                        <span class="text-xl font-black text-rose-600">Rp {{ number_format($borrowing->fine_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="p-4 rounded-2xl border flex items-center gap-3 {{ $borrowing->fine->status === 'lunas' ? 'bg-emerald-50 border-emerald-100 text-emerald-700' : 'bg-rose-50 border-rose-100 text-rose-700' }}">
                    <i class="fas {{ $borrowing->fine->status === 'lunas' ? 'fa-check-circle' : 'fa-clock' }}"></i>
                    <div class="text-xs font-bold uppercase tracking-widest">
                        Status: {{ $borrowing->fine->status }}
                        @if($borrowing->fine->payment_method)
                            <br><span class="opacity-60">Metode: {{ $borrowing->fine->payment_method }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
