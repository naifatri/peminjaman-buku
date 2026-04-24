@extends('layouts.admin')

@section('content')
<div class="space-y-8" x-data="{ returnModal: false, extendModal: false }">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <a href="{{ route('admin.borrowings.index') }}" class="inline-flex items-center text-sm font-semibold text-slate-400 transition-colors hover:text-indigo-600">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke daftar
            </a>
            <h2 class="mt-4 text-3xl font-black text-slate-800">Detail Transaksi #{{ $borrowing->id }}</h2>
            <p class="mt-2 text-sm text-slate-500">Ringkasan lengkap transaksi peminjaman, timeline, denda, dan aksi admin.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <span class="inline-flex items-center rounded-full border px-4 py-2 text-xs font-bold uppercase tracking-[0.2em] {{ $borrowing->admin_status_color }}">
                {{ $borrowing->admin_status_label }}
            </span>
            <span class="inline-flex items-center rounded-full border px-4 py-2 text-xs font-bold uppercase tracking-[0.2em] {{ $borrowing->fine && $borrowing->fine->status === 'lunas' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-slate-100 text-slate-600' }}">
                {{ $summary['payment_status'] }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Tanggal Pinjam</p>
            <p class="mt-4 text-2xl font-black text-slate-800">{{ $borrowing->borrow_date?->translatedFormat('d M Y') ?? '-' }}</p>
        </div>
        <div class="rounded-[2rem] border border-amber-100 bg-amber-50/80 p-6 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-amber-500">Jatuh Tempo</p>
            <p class="mt-4 text-2xl font-black {{ $borrowing->admin_status === 'terlambat' ? 'text-rose-700' : 'text-amber-700' }}">{{ $borrowing->due_date?->translatedFormat('d M Y') ?? '-' }}</p>
        </div>
        <div class="rounded-[2rem] border border-emerald-100 bg-emerald-50/80 p-6 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-emerald-500">Tanggal Kembali</p>
            <p class="mt-4 text-2xl font-black text-emerald-700">{{ $borrowing->return_date?->translatedFormat('d M Y') ?? 'Belum kembali' }}</p>
        </div>
        <div class="rounded-[2rem] border border-rose-100 bg-rose-50/80 p-6 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-rose-500">Total Denda</p>
            <p class="mt-4 text-2xl font-black text-rose-700">Rp {{ number_format($summary['total_fine'], 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 xl:grid-cols-[1.35fr_0.65fr]">
        <div class="space-y-8">
            <div class="rounded-[2rem] border border-slate-100 bg-white p-8 shadow-sm">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="rounded-[1.75rem] border border-slate-100 bg-slate-50/80 p-6">
                        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Data Pengguna</p>
                        <div class="mt-5 flex items-start gap-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-100 text-lg font-black text-indigo-600">
                                {{ strtoupper(substr($borrowing->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-800">{{ $borrowing->user->name }}</h3>
                                <p class="mt-1 text-sm text-slate-500">{{ $borrowing->user->email }}</p>
                                <p class="mt-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $borrowing->user->role }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-[1.75rem] border border-slate-100 bg-slate-50/80 p-6">
                        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Data Buku</p>
                        <div class="mt-5">
                            <h3 class="text-lg font-black text-slate-800">{{ $borrowing->book->title }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $borrowing->book->author }}</p>
                            <p class="mt-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $borrowing->quantity }} eksemplar</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-[2rem] border border-slate-100 bg-white p-8 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Timeline Transaksi</p>
                        <h3 class="mt-2 text-xl font-black text-slate-800">Perjalanan peminjaman</h3>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="rounded-[1.75rem] border border-slate-100 bg-slate-50 p-5">
                        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Tanggal Pinjam</p>
                        <p class="mt-3 text-lg font-black text-slate-800">{{ $borrowing->borrow_date?->translatedFormat('d M Y') ?? '-' }}</p>
                    </div>
                    <div class="rounded-[1.75rem] border border-amber-100 bg-amber-50 p-5">
                        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-amber-500">Jatuh Tempo</p>
                        <p class="mt-3 text-lg font-black {{ $borrowing->admin_status === 'terlambat' ? 'text-rose-700' : 'text-amber-700' }}">{{ $borrowing->due_date?->translatedFormat('d M Y') ?? '-' }}</p>
                    </div>
                    <div class="rounded-[1.75rem] border border-emerald-100 bg-emerald-50 p-5">
                        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-emerald-500">Tanggal Kembali</p>
                        <p class="mt-3 text-lg font-black text-emerald-700">{{ $borrowing->return_date?->translatedFormat('d M Y') ?? 'Belum kembali' }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-[2rem] border border-slate-100 bg-white p-8 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Catatan Transaksi</p>
                <div class="mt-6 space-y-5">
                    <div class="rounded-[1.75rem] border border-slate-100 bg-slate-50 p-5">
                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Alasan Peminjaman</p>
                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ $borrowing->borrow_reason ?: 'Tidak ada keterangan.' }}</p>
                    </div>

                    @if($borrowing->return_notes)
                        <div class="rounded-[1.75rem] border border-slate-100 bg-slate-50 p-5">
                            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Catatan Pengembalian</p>
                            <p class="mt-3 text-sm leading-7 text-slate-600">{{ $borrowing->return_notes }}</p>
                        </div>
                    @endif

                    @if($borrowing->admin_notes)
                        <div class="rounded-[1.75rem] border border-indigo-100 bg-indigo-50/80 p-5">
                            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-indigo-500">Catatan Admin</p>
                            <p class="mt-3 text-sm leading-7 text-indigo-700">{{ $borrowing->admin_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <div class="rounded-[2rem] border border-slate-100 bg-white p-8 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Ringkasan Denda</p>
                <div class="mt-6 space-y-4">
                    <div class="flex items-center justify-between gap-4 text-sm">
                        <span class="text-slate-500">Terlambat</span>
                        <span class="font-bold text-slate-800">{{ $summary['late_days'] }} hari</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 text-sm">
                        <span class="text-slate-500">Grace period</span>
                        <span class="font-bold text-slate-800">{{ $summary['grace_period_days'] }} hari</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 text-sm">
                        <span class="text-slate-500">Hari kena denda</span>
                        <span class="font-bold text-slate-800">{{ $summary['charged_late_days'] }} hari</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 text-sm">
                        <span class="text-slate-500">Tarif per hari</span>
                        <span class="font-bold text-slate-800">Rp {{ number_format($summary['late_fee_per_day'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 text-sm">
                        <span class="text-slate-500">Denda keterlambatan</span>
                        <span class="font-bold text-slate-800">Rp {{ number_format($summary['late_fine'], 0, ',', '.') }}</span>
                    </div>
                    @if($summary['max_fine_amount'])
                        <div class="flex items-center justify-between gap-4 text-sm">
                            <span class="text-slate-500">Maksimal denda</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format($summary['max_fine_amount'], 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between gap-4 text-sm">
                        <span class="text-slate-500">Denda kerusakan</span>
                        <span class="font-bold text-slate-800">Rp {{ number_format($summary['damage_fine'], 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-slate-100 pt-4">
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-sm font-bold text-slate-800">Total denda</span>
                            <span class="text-xl font-black text-rose-600">Rp {{ number_format($summary['total_fine'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 rounded-[1.5rem] border px-4 py-4 {{ $borrowing->fine && $borrowing->fine->status === 'lunas' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-slate-50 text-slate-700' }}">
                    <p class="text-[11px] font-bold uppercase tracking-[0.2em]">Status pembayaran</p>
                    <p class="mt-2 text-sm font-semibold">{{ $summary['payment_status'] }}</p>
                    @if($borrowing->fine?->payment_method)
                        <p class="mt-2 text-xs">Metode: {{ strtoupper(str_replace('_', ' ', $borrowing->fine->payment_method)) }}</p>
                    @endif
                    @if($borrowing->fine?->paid_at)
                        <p class="mt-2 text-xs">Dibayar pada {{ $borrowing->fine->paid_at->translatedFormat('d M Y H:i') }}</p>
                    @endif
                </div>

                @if($borrowing->fine?->payment_method === 'qris')
                    <div class="mt-6 rounded-[1.75rem] border border-indigo-100 bg-indigo-50/70 p-5">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-indigo-500">Bukti Pembayaran QRIS</p>
                                <p class="mt-2 text-sm text-indigo-700">
                                    @if($borrowing->fine->payment_proof)
                                        Bukti pembayaran diunggah oleh peminjam dan bisa diperiksa di bawah ini.
                                    @else
                                        Peminjam memilih QRIS, tetapi bukti pembayaran belum diunggah.
                                    @endif
                                </p>
                            </div>
                            @if($borrowing->fine->payment_proof)
                                <a href="{{ asset('storage/' . $borrowing->fine->payment_proof) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center rounded-xl bg-white px-4 py-2 text-xs font-bold text-indigo-700 shadow-sm transition-all hover:bg-indigo-100">
                                    Lihat penuh
                                </a>
                            @endif
                        </div>

                        @if($borrowing->fine->payment_proof)
                            <div class="mt-5 overflow-hidden rounded-[1.5rem] border border-indigo-100 bg-white">
                                <img src="{{ asset('storage/' . $borrowing->fine->payment_proof) }}" alt="Bukti pembayaran QRIS untuk transaksi {{ $borrowing->id }}" class="h-auto max-h-[28rem] w-full object-contain bg-slate-50">
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="rounded-[2rem] border border-slate-100 bg-white p-8 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Aksi Admin</p>
                <div class="mt-6 flex flex-col gap-3">
                    @if($borrowing->status === 'diajukan')
                        <form action="{{ route('admin.borrowings.approve', $borrowing) }}" method="POST" x-data="{ submitting: false }" @submit="submitting = true">
                            @csrf
                            <button type="submit" class="w-full rounded-2xl bg-emerald-600 px-4 py-3 font-bold text-white transition-all hover:bg-emerald-700" :disabled="submitting">
                                <span x-show="!submitting">Setujui Peminjaman</span>
                                <span x-show="submitting" x-cloak>Memproses...</span>
                            </button>
                        </form>
                        <form action="{{ route('admin.borrowings.reject', $borrowing) }}" method="POST" x-data="{ submitting: false }" @submit="submitting = true">
                            @csrf
                            <button type="submit" class="w-full rounded-2xl bg-rose-600 px-4 py-3 font-bold text-white transition-all hover:bg-rose-700" :disabled="submitting">
                                <span x-show="!submitting">Tolak Peminjaman</span>
                                <span x-show="submitting" x-cloak>Memproses...</span>
                            </button>
                        </form>
                    @endif

                    @if(in_array($borrowing->status, ['dipinjam', 'terlambat'], true) && ! $borrowing->return_date)
                        <button type="button" @click="returnModal = true" class="w-full rounded-2xl bg-indigo-600 px-4 py-3 font-bold text-white transition-all hover:bg-indigo-700">
                            Tandai Sebagai Dikembalikan
                        </button>
                        <button type="button" @click="extendModal = true" class="w-full rounded-2xl bg-slate-900 px-4 py-3 font-bold text-white transition-all hover:bg-slate-950">
                            Perpanjang Masa Pinjam
                        </button>
                    @endif

                    @if($borrowing->status === 'dikembalikan' && ! $borrowing->return_date)
                        <button type="button" @click="returnModal = true" class="w-full rounded-2xl bg-indigo-600 px-4 py-3 font-bold text-white transition-all hover:bg-indigo-700">
                            Verifikasi Pengembalian
                        </button>
                    @endif

                    @if($borrowing->fine && $borrowing->fine->status !== 'lunas' && in_array($borrowing->status, ['verifikasi_denda', 'proses_bayar'], true))
                        <form action="{{ route('admin.borrowings.mark-fine-paid', $borrowing) }}" method="POST" x-data="{ submitting: false }" @submit="submitting = true">
                            @csrf
                            <button type="submit" class="w-full rounded-2xl bg-amber-500 px-4 py-3 font-bold text-white transition-all hover:bg-amber-600" :disabled="submitting">
                                <span x-show="!submitting">{{ $borrowing->status === 'proses_bayar' ? 'Verifikasi Pembayaran Denda' : 'Tandai Denda Sudah Dibayar' }}</span>
                                <span x-show="submitting" x-cloak>Memproses...</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @if($borrowing->book_condition)
                <div class="rounded-[2rem] border border-slate-100 bg-white p-8 shadow-sm">
                    <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Kondisi Buku</p>
                    <p class="mt-4 text-xl font-black uppercase tracking-[0.08em] text-slate-800">{{ str_replace('_', ' ', $borrowing->book_condition) }}</p>
                </div>
            @endif
        </div>
    </div>

    @if(in_array($borrowing->status, ['dipinjam', 'terlambat', 'dikembalikan'], true) && ! $borrowing->return_date)
        <div x-show="returnModal" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center px-4">
            <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm" @click="returnModal = false"></div>
            <div class="relative w-full max-w-xl rounded-[2rem] bg-white p-8 shadow-2xl">
                <div class="mb-6">
                    <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-indigo-500">{{ $borrowing->status === 'dikembalikan' ? 'Verifikasi Pengembalian' : 'Pengembalian Buku' }}</p>
                    <h3 class="mt-2 text-2xl font-black text-slate-800">{{ $borrowing->user->name }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $borrowing->book->title }}</p>
                </div>

                <form action="{{ route('admin.borrowings.mark-returned', $borrowing) }}" method="POST" class="space-y-4" x-data="{ submitting: false }" @submit="submitting = true">
                    @csrf
                    <div>
                        <label class="ml-1 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Tanggal Kembali</label>
                        <input type="date" name="return_date" value="{{ now()->format('Y-m-d') }}" class="mt-2 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm transition-all focus:border-indigo-500 focus:ring-indigo-500/10">
                    </div>
                    <div>
                        <label class="ml-1 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Kondisi Buku</label>
                        <select name="book_condition" required class="mt-2 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm transition-all focus:border-indigo-500 focus:ring-indigo-500/10">
                            <option value="baik">Baik</option>
                            <option value="rusak_ringan">Rusak Ringan</option>
                            <option value="rusak_berat">Rusak Berat</option>
                            <option value="hilang">Hilang</option>
                        </select>
                    </div>
                    <div>
                        <label class="ml-1 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Denda Tambahan</label>
                        <input type="number" name="damage_fine" value="0" min="0" class="mt-2 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm transition-all focus:border-indigo-500 focus:ring-indigo-500/10">
                    </div>
                    <div>
                        <label class="ml-1 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Catatan Admin</label>
                        <textarea name="admin_notes" rows="4" class="mt-2 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm transition-all focus:border-indigo-500 focus:ring-indigo-500/10" placeholder="Catatan pemeriksaan buku..."></textarea>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="returnModal = false" class="flex-1 rounded-2xl bg-slate-100 px-4 py-3 font-bold text-slate-600 transition-all hover:bg-slate-200">Batal</button>
                        <button type="submit" class="flex-1 rounded-2xl bg-indigo-600 px-4 py-3 font-bold text-white transition-all hover:bg-indigo-700" :disabled="submitting">
                            <span x-show="!submitting">Simpan Pengembalian</span>
                            <span x-show="submitting" x-cloak>Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="extendModal" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center px-4">
            <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm" @click="extendModal = false"></div>
            <div class="relative w-full max-w-lg rounded-[2rem] bg-white p-8 shadow-2xl">
                <div class="mb-6">
                    <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-500">Perpanjang Masa Pinjam</p>
                    <h3 class="mt-2 text-2xl font-black text-slate-800">{{ $borrowing->user->name }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $borrowing->book->title }}</p>
                </div>

                <form action="{{ route('admin.borrowings.extend', $borrowing) }}" method="POST" class="space-y-4" x-data="{ submitting: false }" @submit="submitting = true">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="ml-1 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Jatuh Tempo Baru</label>
                        <input type="date" name="due_date" min="{{ $borrowing->due_date?->format('Y-m-d') }}" class="mt-2 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm transition-all focus:border-indigo-500 focus:ring-indigo-500/10">
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="extendModal = false" class="flex-1 rounded-2xl bg-slate-100 px-4 py-3 font-bold text-slate-600 transition-all hover:bg-slate-200">Batal</button>
                        <button type="submit" class="flex-1 rounded-2xl bg-slate-900 px-4 py-3 font-bold text-white transition-all hover:bg-slate-950" :disabled="submitting">
                            <span x-show="!submitting">Simpan Perpanjangan</span>
                            <span x-show="submitting" x-cloak>Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
