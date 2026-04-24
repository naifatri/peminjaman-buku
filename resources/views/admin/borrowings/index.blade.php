@extends('layouts.admin')

@section('content')
<div class="space-y-8" x-data="{
    loading: false,
    returnModal: false,
    extendModal: false,
    activeBorrowing: null,
    activeUserName: '',
    activeBookTitle: '',
    activeDueDate: '',
    openReturnModal(borrowing) {
        this.activeBorrowing = borrowing.id;
        this.activeUserName = borrowing.user;
        this.activeBookTitle = borrowing.book;
        this.returnModal = true;
    },
    openExtendModal(borrowing) {
        this.activeBorrowing = borrowing.id;
        this.activeUserName = borrowing.user;
        this.activeBookTitle = borrowing.book;
        this.activeDueDate = borrowing.dueDate;
        this.extendModal = true;
    }
}">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Manajemen Peminjaman</h2>
            <p class="text-sm text-slate-500 mt-1">Pantau pinjaman aktif, keterlambatan, pengembalian, dan pembayaran denda dari satu dashboard admin.</p>
        </div>
        <div class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-4 py-2 text-xs font-bold uppercase tracking-[0.2em] text-white">
            <i class="fas fa-shield-halved"></i>
            <span>Workflow Admin</span>
        </div>
    </div>

    <div class="metric-grid grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Total Peminjaman</p>
            <p class="mt-4 text-3xl font-black text-slate-800">{{ number_format($stats['total']) }}</p>
            <p class="mt-2 text-sm text-slate-500">Semua histori transaksi tercatat.</p>
        </div>
        <div class="rounded-[2rem] border border-amber-100 bg-amber-50/80 p-6 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-amber-500">Sedang Dipinjam</p>
            <p class="mt-4 text-3xl font-black text-amber-700">{{ number_format($stats['borrowed']) }}</p>
            <p class="mt-2 text-sm text-amber-700/70">Masih berada di tangan peminjam.</p>
        </div>
        <div class="rounded-[2rem] border border-rose-100 bg-rose-50/80 p-6 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-rose-500">Terlambat</p>
            <p class="mt-4 text-3xl font-black text-rose-700">{{ number_format($stats['late']) }}</p>
            <p class="mt-2 text-sm text-rose-700/70">Perlu follow up atau perpanjangan.</p>
        </div>
        <div class="rounded-[2rem] border border-emerald-100 bg-emerald-50/80 p-6 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-emerald-500">Selesai</p>
            <p class="mt-4 text-3xl font-black text-emerald-700">{{ number_format($stats['completed']) }}</p>
            <p class="mt-2 text-sm text-emerald-700/70">Buku sudah kembali ke perpustakaan.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
        <div class="rounded-[2rem] border border-rose-100 bg-gradient-to-br from-rose-50 to-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-rose-500">Peringatan Terlambat</p>
                    <h3 class="mt-2 text-xl font-black text-slate-800">{{ $alerts['overdue_count'] }} transaksi</h3>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600">
                    <i class="fas fa-triangle-exclamation text-lg"></i>
                </div>
            </div>
            <div class="mt-5 space-y-3">
                @forelse($alerts['overdue_items'] as $item)
                    <div class="rounded-2xl border border-rose-100 bg-white/90 p-4">
                        <p class="text-sm font-bold text-slate-800">{{ $item->user->name }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ $item->book->title }}</p>
                        <p class="mt-2 text-xs font-semibold text-rose-600">Jatuh tempo {{ $item->due_date->translatedFormat('d M Y') }}</p>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-rose-200 bg-white/80 p-4 text-sm text-slate-500">
                        Tidak ada buku yang terlambat saat ini.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="rounded-[2rem] border border-amber-100 bg-gradient-to-br from-amber-50 to-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-amber-500">Jatuh Tempo Hari Ini</p>
                    <h3 class="mt-2 text-xl font-black text-slate-800">{{ $alerts['due_today_count'] }} transaksi</h3>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-600">
                    <i class="fas fa-clock text-lg"></i>
                </div>
            </div>
            <div class="mt-5 space-y-3">
                @forelse($alerts['due_today_items'] as $item)
                    <div class="rounded-2xl border border-amber-100 bg-white/90 p-4">
                        <p class="text-sm font-bold text-slate-800">{{ $item->user->name }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ $item->book->title }}</p>
                        <p class="mt-2 text-xs font-semibold text-amber-600">Harus kembali {{ $item->due_date->translatedFormat('d M Y') }}</p>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-amber-200 bg-white/80 p-4 text-sm text-slate-500">
                        Tidak ada pinjaman yang jatuh tempo hari ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm">
        <form action="{{ route('admin.borrowings.index') }}" method="GET" class="responsive-filter-form grid grid-cols-1 gap-4 lg:grid-cols-6" @submit="loading = true">
            <div class="relative lg:col-span-2">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama peminjam atau judul buku..."
                    class="w-full rounded-2xl border-slate-200 py-3 pl-12 pr-4 text-slate-600 placeholder:text-slate-300 transition-all duration-300 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10"
                >
            </div>

            <select name="status" class="w-full rounded-2xl border-slate-200 px-4 py-3 text-slate-600 transition-all duration-300 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10">
                <option value="">Semua Status</option>
                <option value="dipinjam" @selected(request('status') === 'dipinjam')>Dipinjam</option>
                <option value="terlambat" @selected(request('status') === 'terlambat')>Terlambat</option>
                <option value="dikembalikan" @selected(request('status') === 'dikembalikan')>Selesai</option>
            </select>

            <select name="date_filter" class="w-full rounded-2xl border-slate-200 px-4 py-3 text-slate-600 transition-all duration-300 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10">
                <option value="">Semua Tanggal</option>
                <option value="today" @selected(request('date_filter') === 'today')>Hari Ini</option>
                <option value="this_week" @selected(request('date_filter') === 'this_week')>Minggu Ini</option>
                <option value="due_today" @selected(request('date_filter') === 'due_today')>Jatuh Tempo Hari Ini</option>
            </select>

            <select name="sort" class="w-full rounded-2xl border-slate-200 px-4 py-3 text-slate-600 transition-all duration-300 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10">
                <option value="latest" @selected(request('sort', 'latest') === 'latest')>Terbaru</option>
                <option value="due_soonest" @selected(request('sort') === 'due_soonest')>Jatuh Tempo Terdekat</option>
            </select>

            <div class="responsive-filter-actions flex gap-2">
                <button type="submit" class="flex-1 rounded-2xl bg-slate-900 px-4 py-3 text-sm font-bold text-white transition-all duration-300 hover:bg-slate-950" :disabled="loading">
                    <span x-show="!loading">Terapkan</span>
                    <span x-show="loading" x-cloak>Memuat...</span>
                </button>
                <a href="{{ route('admin.borrowings.index') }}" class="flex items-center justify-center rounded-2xl bg-slate-100 px-4 py-3 text-slate-600 transition-all duration-300 hover:bg-slate-200">
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="responsive-table-card overflow-hidden rounded-[2rem] border border-slate-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px] border-collapse text-left">
                <thead>
                    <tr class="bg-slate-50/80">
                        <th class="border-b border-slate-100 px-6 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Peminjam & Buku</th>
                        <th class="border-b border-slate-100 px-6 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Tanggal</th>
                        <th class="border-b border-slate-100 px-6 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Denda</th>
                        <th class="border-b border-slate-100 px-6 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Pembayaran</th>
                        <th class="border-b border-slate-100 px-6 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Status</th>
                        <th class="border-b border-slate-100 px-6 py-4 text-right text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($borrowings as $borrowing)
                        <tr class="align-top transition-colors hover:bg-slate-50/70">
                            <td class="px-6 py-5">
                                <div class="flex items-start gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-sm font-black text-slate-700">
                                        {{ strtoupper(substr($borrowing->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">{{ $borrowing->user->name }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $borrowing->book->title }}</p>
                                        <p class="mt-2 text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $borrowing->quantity }} eksemplar</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="space-y-2 text-sm">
                                    <div>
                                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Pinjam</p>
                                        <p class="font-semibold text-slate-700">{{ $borrowing->borrow_date?->translatedFormat('d M Y') ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Jatuh Tempo</p>
                                        <p class="font-semibold {{ $borrowing->admin_status === 'terlambat' ? 'text-rose-600' : 'text-slate-700' }}">{{ $borrowing->due_date?->translatedFormat('d M Y') ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Kembali</p>
                                        <p class="font-semibold text-slate-700">{{ $borrowing->return_date?->translatedFormat('d M Y') ?? 'Belum kembali' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="space-y-2 text-sm">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="text-slate-400">Hari terlambat</span>
                                        <span class="font-bold text-slate-700">{{ $borrowing->late_days }} hari</span>
                                    </div>
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="text-slate-400">Total denda</span>
                                        <span class="font-bold {{ $borrowing->outstanding_fine_amount > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                            Rp {{ number_format($borrowing->outstanding_fine_amount, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                @php
                                    $paymentClass = match (true) {
                                        ! $borrowing->fine || $borrowing->outstanding_fine_amount <= 0 => 'bg-slate-100 text-slate-600 border-slate-200',
                                        $borrowing->fine->status === 'lunas' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        default => 'bg-rose-50 text-rose-700 border-rose-200',
                                    };
                                @endphp
                                <div class="space-y-2">
                                    <span class="inline-flex items-center rounded-full border px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] {{ $paymentClass }}">
                                        {{ $borrowing->fine_payment_status_label }}
                                    </span>
                                    @if($borrowing->fine?->paid_at)
                                        <p class="text-xs text-slate-500">Dibayar {{ $borrowing->fine->paid_at->translatedFormat('d M Y H:i') }}</p>
                                    @elseif($borrowing->fine?->payment_method)
                                        <p class="text-xs text-slate-500">Metode: {{ str_replace('_', ' ', $borrowing->fine->payment_method) }}</p>
                                    @else
                                        <p class="text-xs text-slate-400">Belum ada pembayaran</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="inline-flex items-center rounded-full border px-3 py-1 text-[11px] font-bold uppercase tracking-[0.2em] {{ $borrowing->admin_status_color }}">
                                    {{ $borrowing->admin_status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('admin.borrowings.show', $borrowing) }}" class="inline-flex items-center rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold text-slate-600 transition-all hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                                        Detail
                                    </a>

                                    @if($borrowing->status === 'diajukan')
                                        <form action="{{ route('admin.borrowings.approve', $borrowing) }}" method="POST" x-data="{ submitting: false }" @submit="submitting = true">
                                            @csrf
                                            <button type="submit" class="rounded-xl bg-emerald-600 px-3 py-2 text-xs font-bold text-white transition-all hover:bg-emerald-700" :disabled="submitting">
                                                <span x-show="!submitting">Setujui</span>
                                                <span x-show="submitting" x-cloak>Proses...</span>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.borrowings.reject', $borrowing) }}" method="POST" x-data="{ submitting: false }" @submit="submitting = true">
                                            @csrf
                                            <button type="submit" class="rounded-xl bg-rose-600 px-3 py-2 text-xs font-bold text-white transition-all hover:bg-rose-700" :disabled="submitting">
                                                <span x-show="!submitting">Tolak</span>
                                                <span x-show="submitting" x-cloak>Proses...</span>
                                            </button>
                                        </form>
                                    @endif

                                    @if(in_array($borrowing->status, ['dipinjam', 'terlambat'], true) && ! $borrowing->return_date)
                                        <button
                                            type="button"
                                            @click="openExtendModal({
                                                id: {{ $borrowing->id }},
                                                user: @js($borrowing->user->name),
                                                book: @js($borrowing->book->title),
                                                dueDate: @js($borrowing->due_date?->format('Y-m-d'))
                                            })"
                                            class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 transition-all hover:bg-slate-200"
                                        >
                                            Perpanjang
                                        </button>
                                        <button
                                            type="button"
                                            @click="openReturnModal({
                                                id: {{ $borrowing->id }},
                                                user: @js($borrowing->user->name),
                                                book: @js($borrowing->book->title)
                                            })"
                                            class="rounded-xl bg-indigo-600 px-3 py-2 text-xs font-bold text-white transition-all hover:bg-indigo-700"
                                        >
                                            Tandai Kembali
                                        </button>
                                    @endif

                                    @if($borrowing->status === 'dikembalikan' && ! $borrowing->return_date)
                                        <button
                                            type="button"
                                            @click="openReturnModal({
                                                id: {{ $borrowing->id }},
                                                user: @js($borrowing->user->name),
                                                book: @js($borrowing->book->title)
                                            })"
                                            class="rounded-xl bg-indigo-600 px-3 py-2 text-xs font-bold text-white transition-all hover:bg-indigo-700"
                                        >
                                            Verifikasi Pengembalian
                                        </button>
                                    @endif

                                    @if($borrowing->fine && $borrowing->fine->status !== 'lunas' && in_array($borrowing->status, ['verifikasi_denda', 'proses_bayar'], true))
                                        <form action="{{ route('admin.borrowings.mark-fine-paid', $borrowing) }}" method="POST" x-data="{ submitting: false }" @submit="submitting = true">
                                            @csrf
                                            <button type="submit" class="rounded-xl bg-amber-500 px-3 py-2 text-xs font-bold text-white transition-all hover:bg-amber-600" :disabled="submitting">
                                                <span x-show="!submitting">{{ $borrowing->status === 'proses_bayar' ? 'Verifikasi Pembayaran' : 'Tandai Lunas' }}</span>
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
                                        <i class="fas fa-book-open text-3xl"></i>
                                    </div>
                                    <h3 class="mt-5 text-lg font-bold text-slate-800">Belum ada transaksi yang cocok</h3>
                                    <p class="mt-2 text-sm text-slate-500">Coba ubah kata kunci, filter, atau sorting untuk melihat data peminjaman lain.</p>
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

    <div x-show="returnModal" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center px-4">
        <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm" @click="returnModal = false"></div>
        <div class="responsive-modal relative w-full max-w-xl rounded-[2rem] bg-white p-6 sm:p-8 shadow-2xl">
            <div class="mb-6">
                <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-indigo-500">Pengembalian Buku</p>
                <h3 class="mt-2 text-2xl font-black text-slate-800" x-text="activeUserName"></h3>
                <p class="mt-1 text-sm text-slate-500" x-text="activeBookTitle"></p>
            </div>

            <form :action="`/admin/borrowings/${activeBorrowing}/mark-returned`" method="POST" class="space-y-4" x-data="{ submitting: false }" @submit="submitting = true">
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
                    <input type="number" name="damage_fine" value="0" min="0" class="mt-2 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm transition-all focus:border-indigo-500 focus:ring-indigo-500/10" placeholder="0">
                    <p class="mt-1 text-xs text-slate-400">Isi jika ada denda kerusakan atau kehilangan.</p>
                </div>
                <div>
                    <label class="ml-1 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Catatan Admin</label>
                    <textarea name="admin_notes" rows="4" class="mt-2 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm transition-all focus:border-indigo-500 focus:ring-indigo-500/10" placeholder="Catatan pengecekan fisik buku..."></textarea>
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
        <div class="responsive-modal relative w-full max-w-lg rounded-[2rem] bg-white p-6 sm:p-8 shadow-2xl">
            <div class="mb-6">
                <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-500">Perpanjang Masa Pinjam</p>
                <h3 class="mt-2 text-2xl font-black text-slate-800" x-text="activeUserName"></h3>
                <p class="mt-1 text-sm text-slate-500" x-text="activeBookTitle"></p>
            </div>

            <form :action="`/admin/borrowings/${activeBorrowing}/extend`" method="POST" class="space-y-4" x-data="{ submitting: false }" @submit="submitting = true">
                @csrf
                @method('PATCH')
                <div>
                    <label class="ml-1 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Jatuh Tempo Baru</label>
                    <input type="date" name="due_date" :min="activeDueDate" class="mt-2 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm transition-all focus:border-indigo-500 focus:ring-indigo-500/10">
                    <p class="mt-1 text-xs text-slate-400">Pilih tanggal setelah jatuh tempo saat ini.</p>
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
</div>
@endsection
