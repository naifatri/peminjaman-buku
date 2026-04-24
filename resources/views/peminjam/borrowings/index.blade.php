<x-peminjam-layout page-title="Riwayat Peminjaman">
    @php
        $statusClasses = [
            'diajukan' => 'border-slate-200 bg-slate-50 text-slate-600',
            'dipinjam' => 'border-amber-200 bg-amber-50 text-amber-700',
            'terlambat' => 'border-rose-200 bg-rose-50 text-rose-700',
            'dikembalikan' => 'border-indigo-200 bg-indigo-50 text-indigo-700',
            'verifikasi_denda' => 'border-rose-200 bg-rose-50 text-rose-700',
            'proses_bayar' => 'border-amber-200 bg-amber-50 text-amber-700',
            'selesai' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
            'ditolak' => 'border-slate-300 bg-slate-100 text-slate-700',
        ];
        $statusLabels = [
            'diajukan' => 'Menunggu Proses',
            'dipinjam' => 'Sedang Dipinjam',
            'terlambat' => 'Terlambat',
            'dikembalikan' => 'Verifikasi Pengembalian',
            'verifikasi_denda' => 'Belum Bayar Denda',
            'proses_bayar' => 'Verifikasi Pembayaran',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
        ];
    @endphp

    <div class="space-y-8" x-data="{
        loading: false,
        returnModal: false,
        payModal: false,
        activeBorrowing: null,
        activeBookTitle: '',
        activeFineAmount: 0,
        selectedPayment: ''
    }">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-slate-800">Riwayat Peminjaman</h2>
                <p class="mt-1 text-sm text-slate-500">Pantau status peminjaman, jatuh tempo, denda, dan proses verifikasi dari satu halaman.</p>
            </div>
            <div class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-4 py-2 text-xs font-bold uppercase tracking-[0.2em] text-white">
                <i class="fas fa-book-open-reader"></i>
                <span>Dashboard Peminjam</span>
            </div>
        </div>

        <div class="metric-grid grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-[2rem] border border-amber-100 bg-amber-50/80 p-6 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-amber-500">Sedang Dipinjam</p>
                <p class="mt-4 text-3xl font-black text-amber-700">{{ number_format($activeCount) }}</p>
                <p class="mt-2 text-sm text-amber-700/70">Masih berada di tangan Anda.</p>
            </div>
            <div class="rounded-[2rem] border border-rose-100 bg-rose-50/80 p-6 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-rose-500">Terlambat</p>
                <p class="mt-4 text-3xl font-black text-rose-700">{{ number_format($lateCount) }}</p>
                <p class="mt-2 text-sm text-rose-700/70">Perlu segera dikembalikan atau diselesaikan.</p>
            </div>
            <div class="rounded-[2rem] border border-indigo-100 bg-indigo-50/80 p-6 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-indigo-500">Menunggu Proses</p>
                <p class="mt-4 text-3xl font-black text-indigo-700">{{ number_format($pendingCount) }}</p>
                <p class="mt-2 text-sm text-indigo-700/70">Menunggu approval, verifikasi, atau pembayaran.</p>
            </div>
            <div class="rounded-[2rem] border border-emerald-100 bg-emerald-50/80 p-6 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-emerald-500">Selesai</p>
                <p class="mt-4 text-3xl font-black text-emerald-700">{{ number_format($completedCount) }}</p>
                <p class="mt-2 text-sm text-emerald-700/70">Transaksi yang sudah tuntas.</p>
            </div>
        </div>

        @if($alerts['due_soon']->isNotEmpty() || $alerts['unpaid_fines']->isNotEmpty())
            <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                @if($alerts['due_soon']->isNotEmpty())
                    <div class="rounded-[2rem] border border-amber-100 bg-gradient-to-br from-amber-50 to-white p-6 shadow-sm">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-amber-500">Akan Jatuh Tempo</p>
                                <h3 class="mt-2 text-xl font-black text-slate-800">{{ $alerts['due_soon']->count() }} buku perlu perhatian</h3>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-600">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="mt-5 space-y-3">
                            @foreach($alerts['due_soon'] as $item)
                                <div class="rounded-2xl border border-amber-100 bg-white/90 p-4">
                                    <p class="text-sm font-bold text-slate-800">{{ $item->book->title }}</p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        Jatuh tempo {{ $item->due_date?->translatedFormat('d M Y') }}
                                        @if($item->due_date)
                                            • {{ now()->startOfDay()->diffInDays($item->due_date, false) === 0 ? 'hari ini' : 'dalam ' . now()->startOfDay()->diffInDays($item->due_date) . ' hari' }}
                                        @endif
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($alerts['unpaid_fines']->isNotEmpty())
                    <div class="rounded-[2rem] border border-rose-100 bg-gradient-to-br from-rose-50 to-white p-6 shadow-sm">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-rose-500">Denda Belum Dibayar</p>
                                <h3 class="mt-2 text-xl font-black text-slate-800">{{ $alerts['unpaid_fines']->count() }} transaksi perlu dibayar</h3>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600">
                                <i class="fas fa-wallet"></i>
                            </div>
                        </div>
                        <div class="mt-5 space-y-3">
                            @foreach($alerts['unpaid_fines'] as $item)
                                <div class="rounded-2xl border border-rose-100 bg-white/90 p-4">
                                    <p class="text-sm font-bold text-slate-800">{{ $item->book->title }}</p>
                                    <p class="mt-1 text-xs text-slate-500">Total denda Rp {{ number_format($item->outstanding_fine_amount, 0, ',', '.') }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <div class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm">
            <form action="{{ route('peminjam.borrowings.index') }}" method="GET" class="responsive-filter-form grid grid-cols-1 gap-4 lg:grid-cols-[minmax(0,1.6fr)_220px_220px_auto]" @submit="loading = true">
                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari berdasarkan judul buku..." class="w-full rounded-2xl border-slate-200 py-3 pl-12 pr-4 text-slate-600 placeholder:text-slate-300 transition-all duration-300 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10">
                </div>
                <select name="status" class="w-full rounded-2xl border-slate-200 px-4 py-3 text-slate-600 transition-all duration-300 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10">
                    <option value="">Semua Status</option>
                    <option value="dipinjam" @selected($status === 'dipinjam')>Sedang Dipinjam</option>
                    <option value="terlambat" @selected($status === 'terlambat')>Terlambat</option>
                    <option value="selesai" @selected($status === 'selesai')>Selesai</option>
                    <option value="proses" @selected($status === 'proses')>Menunggu Proses</option>
                    <option value="denda" @selected($status === 'denda')>Denda Belum Lunas</option>
                </select>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500">
                    Menampilkan <span class="font-bold text-slate-700">{{ $borrowings->total() }}</span> transaksi
                </div>
                <div class="responsive-filter-actions flex gap-2">
                    <button type="submit" class="flex-1 rounded-2xl bg-slate-900 px-4 py-3 text-sm font-bold text-white transition-all duration-300 hover:bg-slate-950" :disabled="loading">
                        <span x-show="!loading">Terapkan</span>
                        <span x-show="loading" x-cloak>Memuat...</span>
                    </button>
                    <a href="{{ route('peminjam.borrowings.index') }}" class="flex items-center justify-center rounded-2xl bg-slate-100 px-4 py-3 text-slate-600 transition-all duration-300 hover:bg-slate-200">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                </div>
            </form>
        </div>

        <div class="responsive-table-card overflow-hidden rounded-[2rem] border border-slate-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1180px] border-collapse text-left">
                    <thead>
                        <tr class="bg-slate-50/80">
                            <th class="border-b border-slate-100 px-6 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Buku</th>
                            <th class="border-b border-slate-100 px-6 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Tanggal</th>
                            <th class="border-b border-slate-100 px-6 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Status</th>
                            <th class="border-b border-slate-100 px-6 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Keterlambatan</th>
                            <th class="border-b border-slate-100 px-6 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Denda</th>
                            <th class="border-b border-slate-100 px-6 py-4 text-right text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($borrowings as $borrowing)
                            <tr class="align-top transition-colors hover:bg-slate-50/70">
                                <td class="px-6 py-5">
                                    <div class="max-w-xs">
                                        <p class="text-sm font-bold text-slate-800">{{ $borrowing->book->title }}</p>
                                        <p class="mt-1 text-xs text-slate-500">Jumlah: {{ $borrowing->quantity }} buku</p>
                                        <p class="mt-2 text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">ID #{{ $borrowing->id }}</p>
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
                                            <p class="font-semibold {{ $borrowing->status === 'terlambat' ? 'text-rose-600' : 'text-slate-700' }}">{{ $borrowing->due_date?->translatedFormat('d M Y') ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Kembali</p>
                                            <p class="font-semibold text-slate-700">{{ $borrowing->return_date?->translatedFormat('d M Y') ?? 'Belum kembali' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="space-y-3">
                                        <span class="inline-flex items-center rounded-full border px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] {{ $statusClasses[$borrowing->status] ?? 'border-slate-200 bg-slate-50 text-slate-600' }}">
                                            @if(in_array($borrowing->status, ['diajukan', 'dikembalikan', 'proses_bayar'], true))
                                                <span class="mr-2 h-1.5 w-1.5 animate-pulse rounded-full bg-current"></span>
                                            @endif
                                            {{ $statusLabels[$borrowing->status] ?? str_replace('_', ' ', $borrowing->status) }}
                                        </span>
                                        <p class="text-xs text-slate-500">
                                            {{ in_array($borrowing->status, ['dipinjam', 'terlambat'], true) ? 'Pinjaman aktif' : 'Transaksi tercatat' }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="space-y-2 text-sm">
                                        <div class="flex items-center justify-between gap-3">
                                            <span class="text-slate-400">Hari terlambat</span>
                                            <span class="font-bold {{ $borrowing->late_days > 0 ? 'text-rose-600' : 'text-slate-700' }}">{{ $borrowing->late_days }} hari</span>
                                        </div>
                                        <div class="flex items-center justify-between gap-3">
                                            <span class="text-slate-400">Hari kena denda</span>
                                            <span class="font-bold text-slate-700">{{ $borrowing->chargeable_late_days }} hari</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="space-y-2 text-sm">
                                        <div class="flex items-center justify-between gap-3">
                                            <span class="text-slate-400">Jumlah</span>
                                            <span class="font-bold {{ $borrowing->outstanding_fine_amount > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                                Rp {{ number_format($borrowing->outstanding_fine_amount, 0, ',', '.') }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between gap-3">
                                            <span class="text-slate-400">Status</span>
                                            <span class="font-bold {{ $borrowing->fine && $borrowing->fine->status === 'lunas' ? 'text-emerald-600' : ($borrowing->outstanding_fine_amount > 0 ? 'text-rose-600' : 'text-slate-600') }}">
                                                {{ $borrowing->fine_payment_status_label }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <a href="{{ route('peminjam.borrowings.show', $borrowing) }}" class="inline-flex items-center rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold text-slate-600 transition-all hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                                            Detail
                                        </a>

                                        @if(in_array($borrowing->status, ['dipinjam', 'terlambat'], true))
                                            <button @click="returnModal = true; activeBorrowing = {{ $borrowing->id }}; activeBookTitle = @js($borrowing->book->title)" class="rounded-xl bg-indigo-600 px-3 py-2 text-xs font-bold text-white transition-all hover:bg-indigo-700">
                                                Kembalikan
                                            </button>
                                        @elseif($borrowing->status === 'verifikasi_denda')
                                            <button @click="payModal = true; activeBorrowing = {{ $borrowing->id }}; activeBookTitle = @js($borrowing->book->title); activeFineAmount = '{{ number_format($borrowing->outstanding_fine_amount, 0, ',', '.') }}'; selectedPayment = ''" class="rounded-xl bg-rose-600 px-3 py-2 text-xs font-bold text-white transition-all hover:bg-rose-700">
                                                Bayar Denda
                                            </button>
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
                                        <p class="mt-2 text-sm text-slate-500">Coba ubah kata kunci pencarian atau filter status untuk melihat transaksi lainnya.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div x-show="returnModal" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center px-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="returnModal = false"></div>
            <div class="responsive-modal relative w-full max-w-md overflow-hidden rounded-[2rem] bg-white p-6 sm:p-8 shadow-2xl" x-transition>
                <div class="mb-6">
                    <h3 class="text-xl font-bold text-slate-800">Kembalikan Buku</h3>
                    <p class="mt-1 text-sm text-slate-500" x-text="'Anda akan mengembalikan: ' + activeBookTitle"></p>
                </div>
                <form :action="'/peminjam/borrowings/' + activeBorrowing + '/return'" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label class="ml-1 text-xs font-bold uppercase tracking-widest text-slate-400">Kondisi & Catatan Buku</label>
                        <textarea name="return_notes" required class="mt-2 block h-32 w-full rounded-2xl border-slate-200 px-4 py-3 text-sm transition-all duration-300 placeholder:text-slate-300 focus:border-indigo-500 focus:ring-indigo-500/10" placeholder="Contoh: Buku baik, tidak ada halaman hilang, ada sedikit lipatan di sampul."></textarea>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" @click="returnModal = false" class="flex-1 rounded-2xl bg-slate-100 px-4 py-3 font-bold text-slate-600 transition-all hover:bg-slate-200">Batal</button>
                        <button type="submit" class="flex-1 rounded-2xl bg-indigo-600 px-4 py-3 font-bold text-white shadow-lg shadow-indigo-200 transition-all hover:bg-indigo-700">Konfirmasi</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="payModal" x-cloak x-transition class="fixed inset-0 z-[60] flex items-center justify-center px-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="payModal = false"></div>
            <div class="responsive-modal relative flex max-h-[90vh] w-full max-w-md flex-col overflow-hidden rounded-[2.4rem] bg-white p-6 shadow-2xl md:p-9">
                <div class="mb-7">
                    <div class="mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-50 text-rose-500 shadow-[0_16px_34px_-24px_rgba(244,63,94,0.55)]">
                        <i class="fas fa-wallet text-xl"></i>
                    </div>
                    <h3 class="text-[2.05rem] font-black leading-tight tracking-tight text-slate-800">Pembayaran Denda</h3>
                    <p class="mt-3 text-[1.05rem] text-slate-500" x-text="'Buku: ' + activeBookTitle"></p>
                    <p class="mt-5 text-[2.35rem] font-black leading-none text-rose-500" x-text="'Rp ' + activeFineAmount"></p>
                </div>

                <form :action="'/peminjam/borrowings/' + activeBorrowing + '/pay-fine'" method="POST" enctype="multipart/form-data" class="custom-scrollbar flex-1 overflow-y-auto pr-1">
                    @csrf
                    <input type="hidden" name="payment_method" :value="selectedPayment">

                    <div x-show="selectedPayment !== 'qris'" x-transition.opacity.duration.200ms class="mb-8">
                        <label class="ml-1 text-sm font-black uppercase tracking-[0.22em] text-slate-400">Pilih Metode Pembayaran</label>
                        <div class="mt-3 grid grid-cols-1 gap-3">
                            <label class="relative flex cursor-pointer items-center rounded-[1.4rem] border p-4 shadow-sm shadow-slate-100/80 transition-all duration-300" :class="selectedPayment === 'tunai' ? 'border-indigo-400 bg-white ring-2 ring-indigo-400/20' : 'border-slate-200/80 bg-white hover:border-slate-300 hover:bg-slate-50/60'">
                                <input type="radio" name="payment_method_picker" value="tunai" x-model="selectedPayment" class="h-5 w-5 border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <div class="ml-4">
                                    <span class="block text-[1.05rem] font-black leading-tight text-slate-700">Bayar Tunai</span>
                                    <span class="block text-[11px] uppercase tracking-tight text-slate-400">Serahkan ke petugas perpustakaan</span>
                                </div>
                            </label>

                            <label class="relative flex cursor-pointer items-center rounded-[1.4rem] border p-4 shadow-sm shadow-slate-100/80 transition-all duration-300" :class="selectedPayment === 'qris' ? 'border-indigo-500 bg-white ring-2 ring-indigo-500/15 shadow-[0_16px_36px_-28px_rgba(79,70,229,0.7)]' : 'border-slate-200/80 bg-white hover:border-slate-300 hover:bg-slate-50/60'">
                                <input type="radio" name="payment_method_picker" value="qris" x-model="selectedPayment" class="h-5 w-5 border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <div class="ml-4 flex-1">
                                    <span class="block text-[1.05rem] font-black leading-tight text-slate-700">QRIS / E-Wallet</span>
                                    <span class="block text-[11px] uppercase tracking-tight text-slate-400">Scan kode QR yang muncul</span>
                                </div>
                                <div class="flex h-11 w-11 items-center justify-center rounded-xl border border-slate-100 bg-white shadow-sm">
                                    <i class="fas fa-qrcode text-lg text-indigo-500"></i>
                                </div>
                            </label>

                            <label class="relative flex cursor-pointer items-center rounded-[1.4rem] border p-4 shadow-sm shadow-slate-100/80 transition-all duration-300" :class="selectedPayment === 'ganti_buku' ? 'border-indigo-400 bg-white ring-2 ring-indigo-400/20' : 'border-slate-200/80 bg-white hover:border-slate-300 hover:bg-slate-50/60'">
                                <input type="radio" name="payment_method_picker" value="ganti_buku" x-model="selectedPayment" class="h-5 w-5 border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <div class="ml-4">
                                    <span class="block text-[1.05rem] font-black leading-tight text-slate-700">Ganti Buku Baru</span>
                                    <span class="block text-[11px] uppercase tracking-tight text-slate-400">Membeli buku fisik yang sama</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div x-show="selectedPayment === 'qris'" x-transition.opacity.duration.250ms class="mb-6 flex flex-col items-center overflow-hidden rounded-[2.1rem] border border-dashed border-slate-200 bg-gradient-to-b from-slate-50 via-white to-white px-5 py-8 shadow-[inset_0_1px_0_rgba(255,255,255,0.85)] md:px-6 md:py-9">
                        <p class="text-center text-[11px] font-black uppercase leading-[2] tracking-[0.34em] text-slate-400">
                            Scan QRIS SIPBUK<br><span class="text-slate-300">Silakan scan dan bayar denda</span>
                        </p>

                        <div class="mt-6 rounded-[2rem] bg-white p-8 shadow-[0_26px_60px_-34px_rgba(37,99,235,0.2)]">
                            <svg width="132" height="132" viewBox="0 0 100 100" class="text-slate-800">
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

                        <div class="mt-6 inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-indigo-500 to-violet-500 px-4 py-2 text-[10px] font-black uppercase tracking-wide text-white shadow-[0_18px_36px_-22px_rgba(79,70,229,0.95)]">
                            <i class="fas fa-shield-alt text-[11px]"></i>
                            <span>Pembayaran Terenkripsi</span>
                        </div>

                        <div class="mt-6 w-full">
                            <label for="payment_proof_modal" class="ml-1 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Upload Bukti Pembayaran</label>
                            <input id="payment_proof_modal" type="file" name="payment_proof" accept="image/*" class="mt-2 block w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-600 transition-all focus:border-indigo-500 focus:ring-indigo-500/10" :required="selectedPayment === 'qris'">
                            <p class="mt-2 text-xs text-slate-400">Format gambar JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.</p>
                            @error('payment_proof')
                                <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="sticky bottom-0 flex gap-3 bg-white pt-4">
                        <button type="button" @click="selectedPayment === 'qris' ? selectedPayment = '' : payModal = false" class="flex-1 rounded-[1.45rem] bg-slate-100 px-4 py-4 text-[1.05rem] font-black text-slate-700 shadow-[inset_0_1px_0_rgba(255,255,255,0.9)] transition-all hover:bg-slate-200" x-text="selectedPayment === 'qris' ? 'Kembali' : 'Batal'"></button>
                        <button type="submit" :disabled="!selectedPayment" :class="selectedPayment ? 'bg-gradient-to-r from-indigo-500 to-violet-600 text-white shadow-[0_24px_42px_-24px_rgba(79,70,229,0.95)] hover:from-indigo-600 hover:to-violet-700' : 'cursor-not-allowed bg-slate-200 text-slate-400'" class="flex-1 rounded-[1.45rem] px-4 py-4 text-[1.05rem] font-black transition-all" x-text="selectedPayment === 'qris' ? 'Konfirmasi Bayar' : 'Lanjutkan'"></button>
                    </div>
                </form>
            </div>
        </div>

        <div>
            {{ $borrowings->withQueryString()->links() }}
        </div>
    </div>
</x-peminjam-layout>
