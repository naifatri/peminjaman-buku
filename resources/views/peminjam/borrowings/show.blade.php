<x-peminjam-layout page-title="Detail Transaksi">
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
        $paymentLabel = $borrowing->fine_payment_status_label;
        $hasFine = $borrowing->fine_amount > 0 || $borrowing->fine;
    @endphp

    <div class="space-y-8" x-data="{ payModal: false, selectedPayment: '' }">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <a href="{{ route('peminjam.borrowings.index') }}" class="group inline-flex items-center text-sm font-semibold text-slate-400 transition-colors hover:text-indigo-600">
                    <i class="fas fa-arrow-left mr-2 transition-transform group-hover:-translate-x-1"></i>
                    Kembali ke Riwayat
                </a>
                <h2 class="mt-4 text-3xl font-black tracking-tight text-slate-800">Detail Peminjaman #{{ $borrowing->id }}</h2>
                <p class="mt-2 text-sm text-slate-500">Informasi lengkap peminjaman, timeline, denda, dan status pembayaran transaksi Anda.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <span class="rounded-full px-4 py-1.5 text-xs font-bold uppercase tracking-widest {{ $statusClasses[$borrowing->status] ?? 'bg-slate-100 text-slate-500' }}">
                    {{ $statusLabels[$borrowing->status] ?? str_replace('_', ' ', $borrowing->status) }}
                </span>
                <span class="rounded-full px-4 py-1.5 text-xs font-bold uppercase tracking-widest {{ $borrowing->fine && $borrowing->fine->status === 'lunas' ? 'bg-emerald-100 text-emerald-700' : ($borrowing->outstanding_fine_amount > 0 ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600') }}">
                    {{ $paymentLabel }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Tanggal Pinjam</p>
                <p class="mt-4 text-2xl font-black text-slate-800">{{ $borrowing->borrow_date?->translatedFormat('d M Y') ?? '-' }}</p>
            </div>
            <div class="rounded-[2rem] border border-indigo-100 bg-indigo-50/80 p-6 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-indigo-500">Jatuh Tempo</p>
                <p class="mt-4 text-2xl font-black {{ $borrowing->status === 'terlambat' ? 'text-rose-700' : 'text-indigo-700' }}">{{ $borrowing->due_date?->translatedFormat('d M Y') ?? '-' }}</p>
            </div>
            <div class="rounded-[2rem] border border-emerald-100 bg-emerald-50/80 p-6 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-emerald-500">Tanggal Kembali</p>
                <p class="mt-4 text-2xl font-black text-emerald-700">{{ $borrowing->return_date?->translatedFormat('d M Y') ?? 'Belum kembali' }}</p>
            </div>
            <div class="rounded-[2rem] border border-rose-100 bg-rose-50/80 p-6 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-rose-500">Hari Keterlambatan</p>
                <p class="mt-4 text-2xl font-black text-rose-700">{{ $borrowing->late_days }} hari</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 xl:grid-cols-[1.25fr_0.75fr]">
            <div class="space-y-8">
                <div class="rounded-[2rem] border border-slate-100 bg-white p-8 shadow-sm">
                    <div class="flex items-start gap-6">
                        <div class="flex h-44 w-32 flex-shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-slate-100 p-2">
                            @if($borrowing->book->cover_image)
                                <img src="{{ asset('storage/' . $borrowing->book->cover_image) }}" class="h-full w-full object-contain" alt="{{ $borrowing->book->title }}">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-slate-300">
                                    <i class="fas fa-image text-3xl"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-slate-800">{{ $borrowing->book->title }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $borrowing->book->author }}</p>
                            <div class="mt-6 grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Jumlah</p>
                                    <p class="text-sm font-bold text-slate-700">{{ $borrowing->quantity }} buku</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Status Denda</p>
                                    <p class="text-sm font-bold {{ $borrowing->outstanding_fine_amount > 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ $paymentLabel }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-slate-100 bg-white p-8 shadow-sm">
                    <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Catatan Transaksi</p>
                    <div class="mt-6 space-y-5">
                        <div class="rounded-[1.75rem] border border-slate-100 bg-slate-50 p-5">
                            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Alasan Meminjam</p>
                            <p class="mt-3 text-sm leading-7 text-slate-600">{{ $borrowing->borrow_reason ?: 'Tidak ada keterangan.' }}</p>
                        </div>

                        @if($borrowing->return_notes)
                            <div class="rounded-[1.75rem] border border-slate-100 bg-slate-50 p-5">
                                <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Catatan Pengembalian Anda</p>
                                <p class="mt-3 text-sm leading-7 text-slate-600">{{ $borrowing->return_notes }}</p>
                            </div>
                        @endif

                        @if($borrowing->admin_notes)
                            <div class="rounded-[1.75rem] border border-indigo-100 bg-indigo-50/80 p-5">
                                <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-indigo-500">Catatan Petugas</p>
                                <p class="mt-3 text-sm leading-7 text-indigo-700">{{ $borrowing->admin_notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="space-y-8">
                <div class="rounded-[2rem] border border-slate-100 bg-white p-8 shadow-sm">
                    <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Perhitungan Denda</p>
                    <div class="mt-6 space-y-4">
                        <div class="flex items-center justify-between gap-4 text-sm">
                            <span class="text-slate-500">Hari terlambat</span>
                            <span class="font-bold text-slate-800">{{ $borrowing->late_days }} hari</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 text-sm">
                            <span class="text-slate-500">Hari kena denda</span>
                            <span class="font-bold text-slate-800">{{ $borrowing->chargeable_late_days }} hari</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 text-sm">
                            <span class="text-slate-500">Tarif per hari</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format($borrowing->fine->late_fee_per_day ?? $policy['late_fee_per_day'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 text-sm">
                            <span class="text-slate-500">Grace period</span>
                            <span class="font-bold text-slate-800">{{ $borrowing->fine->grace_period_days ?? $policy['grace_period_days'] }} hari</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 text-sm">
                            <span class="text-slate-500">Denda keterlambatan</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format($borrowing->fine->late_fee_subtotal ?? $borrowing->calculated_late_fine, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 text-sm">
                            <span class="text-slate-500">Denda kerusakan</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format($borrowing->fine->damage_amount ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="border-t border-slate-100 pt-4">
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-sm font-bold text-slate-800">Total denda</span>
                                <span class="text-xl font-black {{ $borrowing->outstanding_fine_amount > 0 ? 'text-rose-600' : 'text-emerald-600' }}">Rp {{ number_format($borrowing->outstanding_fine_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 rounded-[1.5rem] border px-4 py-4 {{ $borrowing->fine && $borrowing->fine->status === 'lunas' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : ($hasFine ? 'border-rose-200 bg-rose-50 text-rose-700' : 'border-slate-200 bg-slate-50 text-slate-700') }}">
                        <p class="text-[11px] font-bold uppercase tracking-[0.2em]">Status pembayaran</p>
                        <p class="mt-2 text-sm font-semibold">{{ $paymentLabel }}</p>
                        @if($borrowing->fine?->payment_method)
                            <p class="mt-2 text-xs">Metode: {{ strtoupper(str_replace('_', ' ', $borrowing->fine->payment_method)) }}</p>
                        @endif
                        @if($borrowing->fine?->paid_at)
                            <p class="mt-2 text-xs">Dibayar pada {{ $borrowing->fine->paid_at->translatedFormat('d M Y H:i') }}</p>
                        @endif
                    </div>
                </div>

                <div class="rounded-[2rem] border border-slate-100 bg-white p-8 shadow-sm">
                    <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Aksi Pengguna</p>
                    <div class="mt-6 flex flex-col gap-3">
                        @if($borrowing->status === 'verifikasi_denda')
                            <button type="button" @click="payModal = true; selectedPayment = ''" class="w-full rounded-2xl bg-rose-600 px-4 py-3 font-bold text-white transition-all hover:bg-rose-700">
                                Bayar Denda Sekarang
                            </button>
                        @endif

                        @if($borrowing->status === 'dipinjam')
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-600">
                                Buku masih dalam masa pinjam aktif. Jika butuh perpanjangan, hubungi petugas perpustakaan.
                            </div>
                        @elseif($borrowing->status === 'terlambat')
                            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-700">
                                Buku sudah melewati jatuh tempo. Segera kembalikan buku agar denda tidak bertambah.
                            </div>
                        @elseif(in_array($borrowing->status, ['diajukan', 'dikembalikan', 'proses_bayar'], true))
                            <div class="rounded-2xl border border-indigo-200 bg-indigo-50 px-4 py-4 text-sm text-indigo-700">
                                Transaksi Anda sedang diproses petugas. Pantau status secara berkala di halaman riwayat.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="rounded-[2rem] bg-indigo-600 p-8 text-white shadow-xl shadow-indigo-200">
                    <h4 class="text-lg font-bold">Informasi Bantuan</h4>
                    <p class="mt-3 text-xs leading-relaxed text-indigo-100">Jika ada kendala pada peminjaman, pengembalian, atau pembayaran denda, silakan hubungi petugas perpustakaan.</p>
                    <div class="mt-6 space-y-3">
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

        <div x-show="payModal" x-cloak x-transition class="fixed inset-0 z-[60] flex items-center justify-center px-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="payModal = false"></div>
            <div class="relative flex max-h-[90vh] w-full max-w-md flex-col overflow-hidden rounded-[2.4rem] bg-white p-8 shadow-2xl md:p-9">
                <div class="mb-7">
                    <div class="mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-50 text-rose-500 shadow-[0_16px_34px_-24px_rgba(244,63,94,0.55)]">
                        <i class="fas fa-wallet text-xl"></i>
                    </div>
                    <h3 class="text-[2.05rem] font-black leading-tight tracking-tight text-slate-800">Pembayaran Denda</h3>
                    <p class="mt-3 text-[1.05rem] text-slate-500">{{ $borrowing->book->title }}</p>
                    <p class="mt-5 text-[2.35rem] font-black leading-none text-rose-500">Rp {{ number_format($borrowing->outstanding_fine_amount, 0, ',', '.') }}</p>
                </div>

                <form action="{{ route('peminjam.borrowings.pay-fine', $borrowing) }}" method="POST" class="flex-1 overflow-y-auto pr-1">
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
                    </div>

                    <div class="sticky bottom-0 flex gap-3 bg-white pt-4">
                        <button type="button" @click="selectedPayment === 'qris' ? selectedPayment = '' : payModal = false" class="flex-1 rounded-[1.45rem] bg-slate-100 px-4 py-4 text-[1.05rem] font-black text-slate-700 transition-all hover:bg-slate-200" x-text="selectedPayment === 'qris' ? 'Kembali' : 'Batal'"></button>
                        <button type="submit" :disabled="!selectedPayment" :class="selectedPayment ? 'bg-gradient-to-r from-indigo-500 to-violet-600 text-white hover:from-indigo-600 hover:to-violet-700' : 'cursor-not-allowed bg-slate-200 text-slate-400'" class="flex-1 rounded-[1.45rem] px-4 py-4 text-[1.05rem] font-black transition-all" x-text="selectedPayment === 'qris' ? 'Konfirmasi Bayar' : 'Lanjutkan'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-peminjam-layout>
