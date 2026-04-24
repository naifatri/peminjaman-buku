<x-peminjam-layout page-title="Denda Saya">
    @php
        $currency = static fn ($amount) => 'Rp ' . number_format((float) $amount, 0, ',', '.');
        $filterTabs = [
            'all' => 'Semua',
            'lunas' => 'Lunas',
            'belum_lunas' => 'Belum Lunas',
        ];
    @endphp

    <div
        class="space-y-8"
        x-data="{
            ready: false,
            loading: false,
            payModal: false,
            activeBorrowing: null,
            activeBookTitle: '',
            activeFineAmount: '',
            selectedPayment: ''
        }"
        x-init="setTimeout(() => ready = true, 180)"
    >
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-slate-800">Denda Saya</h2>
                <p class="mt-1 text-sm text-slate-500">Pantau tagihan, status pembayaran, dan transaksi yang perlu segera kamu selesaikan.</p>
            </div>

            @if($hasMultipleUnpaid)
                <a href="#tagihan-aktif" class="inline-flex items-center justify-center rounded-2xl bg-rose-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-rose-200 transition-all hover:bg-rose-700">
                    <i class="fas fa-bolt mr-2"></i>
                    Bayar Semua
                </a>
            @endif
        </div>

        @if($unpaidCount > 0)
            <div class="rounded-[2rem] border border-rose-200 bg-gradient-to-r from-rose-50 to-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600">
                            <i class="fas fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-rose-500">Perlu Perhatian</p>
                            <h3 class="mt-2 text-xl font-black text-slate-800">Kamu memiliki denda yang belum dibayar</h3>
                            <p class="mt-2 text-sm text-slate-500">Masih ada {{ $unpaidCount }} tagihan aktif dengan total {{ $currency($unpaidAmount) }}.</p>
                        </div>
                    </div>
                    <a href="#tagihan-aktif" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition-all hover:bg-slate-950">
                        Tinjau Tagihan
                    </a>
                </div>
            </div>
        @else
            <div class="rounded-[2rem] border border-emerald-200 bg-gradient-to-r from-emerald-50 to-white p-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600">
                        <i class="fas fa-circle-check"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-emerald-500">Status Aman</p>
                        <h3 class="mt-2 text-xl font-black text-slate-800">Semua denda sudah dibayar</h3>
                        <p class="mt-2 text-sm text-slate-500">Tidak ada tagihan aktif. Riwayat pembayaranmu tetap bisa dilihat kapan saja.</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="metric-grid grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="rounded-[2rem] border border-rose-100 bg-rose-50/80 p-6 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-rose-500">Belum Lunas</p>
                <p class="mt-4 text-3xl font-black text-rose-700">{{ number_format($unpaidCount) }}</p>
                <p class="mt-2 text-sm text-rose-700/70">Jumlah transaksi yang masih perlu diselesaikan.</p>
            </div>
            <div class="rounded-[2rem] border border-amber-100 bg-amber-50/80 p-6 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-amber-500">Tagihan Aktif</p>
                <p class="mt-4 text-3xl font-black text-amber-700">{{ $currency($unpaidAmount) }}</p>
                <p class="mt-2 text-sm text-amber-700/70">Total nominal denda yang belum lunas.</p>
            </div>
            <div class="rounded-[2rem] border border-emerald-100 bg-emerald-50/80 p-6 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-emerald-500">Total Terbayar</p>
                <p class="mt-4 text-3xl font-black text-emerald-700">{{ $currency($paidAmount) }}</p>
                <p class="mt-2 text-sm text-emerald-700/70">Akumulasi pembayaran denda yang sudah tercatat.</p>
            </div>
        </div>

        <div class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Filter & Pencarian</p>
                    <h3 class="mt-2 text-xl font-black text-slate-800">Temukan transaksi denda dengan cepat</h3>
                </div>

                <div class="flex flex-wrap gap-2">
                    @foreach($filterTabs as $tabValue => $tabLabel)
                        <a
                            href="{{ route('peminjam.fines.index', array_filter(['search' => $search, 'status' => $tabValue, 'sort' => $sort], fn ($value) => $value !== '')) }}"
                            class="inline-flex items-center rounded-full border px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] transition-all {{ $status === $tabValue ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-200 bg-white text-slate-500 hover:border-slate-300 hover:text-slate-700' }}"
                        >
                            {{ $tabLabel }}
                        </a>
                    @endforeach
                </div>
            </div>

            <form action="{{ route('peminjam.fines.index') }}" method="GET" class="responsive-filter-form mt-6 grid grid-cols-1 gap-4 lg:grid-cols-[minmax(0,1.6fr)_220px_220px_auto]" @submit="loading = true">
                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari berdasarkan judul buku..." class="w-full rounded-2xl border-slate-200 py-3 pl-12 pr-4 text-slate-600 placeholder:text-slate-300 transition-all duration-300 focus:border-indigo-500 focus:ring-indigo-500/10">
                </div>

                <select name="status" class="w-full rounded-2xl border-slate-200 px-4 py-3 text-slate-600 transition-all duration-300 focus:border-indigo-500 focus:ring-indigo-500/10">
                    <option value="all" @selected($status === 'all')>Semua Status</option>
                    <option value="lunas" @selected($status === 'lunas')>Lunas</option>
                    <option value="belum_lunas" @selected($status === 'belum_lunas')>Belum Lunas</option>
                </select>

                <select name="sort" class="w-full rounded-2xl border-slate-200 px-4 py-3 text-slate-600 transition-all duration-300 focus:border-indigo-500 focus:ring-indigo-500/10">
                    <option value="latest" @selected($sort === 'latest')>Tanggal terbaru</option>
                    <option value="amount_desc" @selected($sort === 'amount_desc')>Jumlah denda tertinggi</option>
                    <option value="amount_asc" @selected($sort === 'amount_asc')>Jumlah denda terendah</option>
                </select>

                <div class="responsive-filter-actions flex gap-2">
                    <button type="submit" class="flex-1 rounded-2xl bg-slate-900 px-4 py-3 text-sm font-bold text-white transition-all duration-300 hover:bg-slate-950" :disabled="loading">
                        <span x-show="!loading">Terapkan</span>
                        <span x-show="loading" x-cloak>Memuat...</span>
                    </button>
                    <a href="{{ route('peminjam.fines.index') }}" class="flex items-center justify-center rounded-2xl bg-slate-100 px-4 py-3 text-slate-600 transition-all duration-300 hover:bg-slate-200" title="Reset filter">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                </div>
            </form>
        </div>

        <div x-show="!ready" x-cloak class="space-y-4">
            @for($i = 0; $i < 4; $i++)
                <div class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm">
                    <div class="animate-pulse">
                        <div class="h-4 w-28 rounded bg-slate-200"></div>
                        <div class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-6">
                            <div class="h-16 rounded-2xl bg-slate-200 lg:col-span-2"></div>
                            <div class="h-16 rounded-2xl bg-slate-200"></div>
                            <div class="h-16 rounded-2xl bg-slate-200"></div>
                            <div class="h-16 rounded-2xl bg-slate-200"></div>
                            <div class="h-16 rounded-2xl bg-slate-200"></div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>

        <div x-show="ready" x-cloak class="responsive-table-card rounded-[2rem] border border-slate-100 bg-white shadow-sm">
            @if($fines->isEmpty())
                <div class="px-8 py-16 text-center">
                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-slate-50 text-slate-300">
                        <i class="fas fa-wallet text-3xl"></i>
                    </div>
                    <h3 class="mt-5 text-lg font-bold text-slate-800">Belum ada data denda</h3>
                    <p class="mt-2 text-sm text-slate-500">
                        {{ $totalFines > 0 ? 'Tidak ada data yang cocok dengan filter saat ini.' : 'Riwayat denda akan muncul di halaman ini ketika ada transaksi keterlambatan.' }}
                    </p>
                </div>
            @else
                <div id="tagihan-aktif" class="overflow-x-auto">
                    <table class="w-full min-w-[1180px] border-collapse text-left">
                        <thead>
                            <tr class="bg-slate-50/80">
                                <th class="border-b border-slate-100 px-6 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Buku</th>
                                <th class="border-b border-slate-100 px-6 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Tanggal Pinjam</th>
                                <th class="border-b border-slate-100 px-6 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Keterlambatan</th>
                                <th class="border-b border-slate-100 px-6 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Jumlah Denda</th>
                                <th class="border-b border-slate-100 px-6 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Status</th>
                                <th class="border-b border-slate-100 px-6 py-4 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Tanggal Bayar</th>
                                <th class="border-b border-slate-100 px-6 py-4 text-right text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($fines as $fine)
                                @php
                                    $lateDays = (int) ($fine->raw_late_days ?: $fine->days_late);
                                    $isPaid = $fine->status === 'lunas';
                                    $isOnTime = $lateDays === 0;
                                    $canPayNow = $fine->borrowing && $fine->borrowing->status === 'verifikasi_denda';
                                @endphp

                                <tr class="align-top transition-all duration-300 hover:bg-slate-50/80 {{ $isPaid ? '' : 'bg-rose-50/40' }}">
                                    <td class="px-6 py-5">
                                        <div class="max-w-xs">
                                            <p class="text-sm font-bold text-slate-800">{{ $fine->borrowing->book->title }}</p>
                                            <div class="mt-2 flex items-center gap-2 text-xs text-slate-500">
                                                <span class="font-semibold uppercase tracking-[0.18em] text-slate-400">Ref ID</span>
                                                <span class="rounded-full bg-slate-100 px-2.5 py-1 font-bold text-slate-600" title="ID referensi transaksi denda">#{{ $fine->id }}</span>
                                                <span class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-slate-200 text-[10px] text-slate-400" title="Gunakan Ref ID ini saat konfirmasi ke petugas">i</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-sm font-semibold text-slate-700">
                                        {{ $fine->borrowing->borrow_date?->translatedFormat('d M Y') ?? '-' }}
                                    </td>
                                    <td class="px-6 py-5">
                                        @if($isOnTime)
                                            <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-emerald-700">
                                                <span class="mr-2 h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                Tepat waktu
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-rose-700">
                                                <span class="mr-2 h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                                {{ $lateDays }} hari
                                            </span>
                                        @endif

                                        <p class="mt-2 text-xs text-slate-400">
                                            {{ (int) $fine->charged_late_days }} hari ditagihkan
                                        </p>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="text-sm font-black {{ $isPaid ? 'text-emerald-700' : 'text-rose-600' }}">{{ $currency($fine->amount) }}</span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="inline-flex items-center rounded-full border px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] {{ $isPaid ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-rose-200 bg-rose-50 text-rose-700' }}">
                                            <span class="mr-2 h-1.5 w-1.5 rounded-full bg-current"></span>
                                            {{ $isPaid ? 'Lunas' : 'Belum Lunas' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-sm font-medium text-slate-600">
                                        {{ $fine->paid_at?->translatedFormat('d M Y') ?? '-' }}
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-wrap justify-end gap-2">
                                            @if(! $isPaid)
                                                @if($canPayNow)
                                                    <button
                                                        type="button"
                                                        @click="
                                                            payModal = true;
                                                            activeBorrowing = {{ $fine->borrowing->id }};
                                                            activeBookTitle = @js($fine->borrowing->book->title);
                                                            activeFineAmount = '{{ number_format((float) $fine->amount, 0, ',', '.') }}';
                                                            selectedPayment = '';
                                                        "
                                                        class="inline-flex items-center rounded-xl bg-rose-600 px-3 py-2 text-xs font-bold text-white transition-all hover:bg-rose-700"
                                                    >
                                                        Bayar Sekarang
                                                    </button>
                                                @else
                                                    <a href="{{ route('peminjam.borrowings.show', $fine->borrowing) }}" class="inline-flex items-center rounded-xl bg-rose-600 px-3 py-2 text-xs font-bold text-white transition-all hover:bg-rose-700">
                                                        Bayar Sekarang
                                                    </a>
                                                @endif
                                            @endif

                                            <a href="{{ route('peminjam.borrowings.show', $fine->borrowing) }}" class="inline-flex items-center rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold text-slate-600 transition-all hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                                                Lihat Detail
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div>
            {{ $fines->withQueryString()->links() }}
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

                <form :action="'/peminjam/borrowings/' + activeBorrowing + '/pay-fine'" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto pr-1">
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

                        <div class="mt-6 w-full">
                            <label for="payment_proof_fines" class="ml-1 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Upload Bukti Pembayaran</label>
                            <input id="payment_proof_fines" type="file" name="payment_proof" accept="image/*" class="mt-2 block w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-600 transition-all focus:border-indigo-500 focus:ring-indigo-500/10" :required="selectedPayment === 'qris'">
                            <p class="mt-2 text-xs text-slate-400">Format gambar JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.</p>
                            @error('payment_proof')
                                <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                            @enderror
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
