<x-peminjam-layout page-title="Detail Buku">
    @php
        $today = now()->startOfDay();
        $defaultDuration = (int) ($finePolicy['default_loan_duration_days'] ?? 7);
        $defaultDueDate = $today->copy()->addDays($defaultDuration)->format('Y-m-d');
        $statusConfig = match ($book->inventory_status) {
            'available' => ['label' => 'Tersedia', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
            'low' => ['label' => 'Stok Sedikit', 'class' => 'bg-amber-50 text-amber-700 border-amber-200'],
            default => ['label' => 'Tidak Tersedia', 'class' => 'bg-rose-50 text-rose-700 border-rose-200'],
        };
    @endphp

    <div
        class="space-y-8"
        x-data="{
            confirmBorrow: false,
            bypassBorrowConfirm: false,
            borrowDate: '{{ $today->format('Y-m-d') }}',
            durationDays: {{ $defaultDuration }},
            dueDate: '{{ $defaultDueDate }}',
            isFavorite: {{ $isFavorite ? 'true' : 'false' }},
            favoriteBusy: false,
            favoritesEnabled: {{ $favoritesEnabled ? 'true' : 'false' }},
            updateDueDate() {
                const base = new Date(this.borrowDate + 'T00:00:00');
                if (Number.isNaN(base.getTime())) return;
                if (!this.dueDate || this.dueDate < this.borrowDate) {
                    base.setDate(base.getDate() + Number(this.durationDays));
                    this.dueDate = base.toISOString().slice(0, 10);
                }
            },
            submitBorrowForm() {
                this.bypassBorrowConfirm = true;
                this.confirmBorrow = false;
                this.$nextTick(() => this.$refs.borrowForm.requestSubmit());
            },
            async toggleFavorite() {
                if (this.favoriteBusy || !this.favoritesEnabled) return;
                this.favoriteBusy = true;
                try {
                    const response = await fetch('{{ route('peminjam.books.favorite', $book) }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content,
                            'Accept': 'application/json',
                        },
                    });
                    const data = await response.json();
                    this.isFavorite = !!data.is_favorite;
                } finally {
                    this.favoriteBusy = false;
                }
            }
        }"
        x-init="updateDueDate()"
    >
        @if(! $favoritesEnabled)
            <div class="rounded-[1.5rem] border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-800">
                Fitur favorit belum aktif karena tabel `book_favorites` belum tersedia. Jalankan `php artisan migrate` untuk mengaktifkannya.
            </div>
        @endif

        <div>
            <a href="{{ route('peminjam.books.index') }}" class="inline-flex items-center text-sm font-semibold text-slate-400 transition-colors hover:text-indigo-600">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Katalog
            </a>
        </div>

        <div class="grid grid-cols-1 gap-8 xl:grid-cols-[minmax(320px,0.82fr)_minmax(0,1.18fr)]">
            <div class="space-y-6">
                <div class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <span class="inline-flex items-center rounded-full border px-3 py-1 text-[10px] font-bold uppercase tracking-[0.18em] {{ $statusConfig['class'] }}">
                            <span class="mr-2 h-1.5 w-1.5 rounded-full bg-current"></span>
                            {{ $statusConfig['label'] }}
                        </span>
                        <button type="button" @click="toggleFavorite()" :class="isFavorite ? 'bg-rose-50 border-rose-200 text-rose-500' : 'bg-white border-rose-100 text-rose-300 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50'" class="flex h-10 w-10 items-center justify-center rounded-full border shadow-sm transition-all duration-300 {{ $favoritesEnabled ? '' : 'cursor-not-allowed opacity-50' }}">
                            <i :class="isFavorite ? 'fas fa-heart' : 'far fa-heart'" class="text-sm"></i>
                        </button>
                    </div>
                    <div class="mt-4 overflow-hidden rounded-[1.5rem] border border-slate-100 bg-slate-100 p-4">
                        @if($book->cover_image)
                            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="h-[460px] w-full rounded-[1.25rem] object-cover object-center">
                        @else
                            <div class="flex h-[460px] w-full items-center justify-center rounded-[1.25rem] bg-white text-slate-300">
                                <i class="fas fa-image text-5xl"></i>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm">
                    <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Aturan Peminjaman</p>
                    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="rounded-[1.5rem] border border-indigo-100 bg-indigo-50/80 p-4">
                            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-indigo-500">Maksimal Pinjam</p>
                            <p class="mt-2 text-lg font-black text-indigo-700">{{ $borrowEligibility['max_borrowings'] }} transaksi</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-amber-100 bg-amber-50/80 p-4">
                            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-amber-500">Durasi Pinjam</p>
                            <p class="mt-2 text-lg font-black text-amber-700">{{ $defaultDuration }} hari</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-rose-100 bg-rose-50/80 p-4">
                            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-rose-500">Denda Per Hari</p>
                            <p class="mt-2 text-lg font-black text-rose-700">Rp {{ number_format((float) ($finePolicy['late_fee_per_day'] ?? 0), 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="mt-8">
                        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400 mb-4">Informasi Kondisi Buku</p>
                        <div class="overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-slate-200 bg-slate-50">
                                        <th class="px-5 py-4 text-left text-[11px] font-bold uppercase tracking-[0.18em] text-slate-600">Kondisi Buku</th>
                                        <th class="px-5 py-4 text-left text-[11px] font-bold uppercase tracking-[0.18em] text-slate-600">Persyaratan Pengembalian</th>
                                        <th class="px-5 py-4 text-left text-[11px] font-bold uppercase tracking-[0.18em] text-slate-600">Sanksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200">
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-5 py-4 text-sm font-semibold text-slate-700">Baik</td>
                                        <td class="px-5 py-4 text-sm text-slate-600">Harus dikembalikan dalam kondisi baik</td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex items-center rounded-full bg-emerald-50 border border-emerald-200 px-3 py-1 text-xs font-semibold text-emerald-700">
                                                Tidak ada denda
                                            </span>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-5 py-4 text-sm font-semibold text-slate-700">Rusak/Bermasalah</td>
                                        <td class="px-5 py-4 text-sm text-slate-600">Jika dikembalikan dalam kondisi rusak, akan dikenakan denda</td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex items-center rounded-full bg-rose-50 border border-rose-200 px-3 py-1 text-xs font-semibold text-rose-700">
                                                Dikenakan denda
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-[2rem] border border-slate-100 bg-white p-8 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-indigo-500">{{ $book->category->name ?? 'Umum' }}</p>
                            <h2 class="mt-3 text-3xl font-black leading-tight text-slate-800">{{ $book->title }}</h2>
                            <p class="mt-2 text-lg italic text-slate-500">{{ $book->author }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3 text-right">
                            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">Rating</p>
                            <p class="mt-1 text-xl font-black text-amber-500">
                                <i class="fas fa-star mr-1 text-sm"></i>{{ number_format((float) ($book->rating ?? 4.0), 1) }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/50 px-4 py-4">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">ISBN</p>
                            <p class="mt-2 text-sm font-semibold text-slate-700">{{ $book->isbn }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/50 px-4 py-4">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Tahun Terbit</p>
                            <p class="mt-2 text-sm font-semibold text-slate-700">{{ $book->published_year }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/50 px-4 py-4">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Penerbit</p>
                            <p class="mt-2 text-sm font-semibold text-slate-700">{{ $book->publisher ?: '-' }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/50 px-4 py-4">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Jumlah Halaman</p>
                            <p class="mt-2 text-sm font-semibold text-slate-700">{{ $book->page_count ? $book->page_count . ' halaman' : '-' }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/50 px-4 py-4">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Stok Tersisa</p>
                            <p class="mt-2 text-sm font-semibold {{ $book->stock > 0 ? 'text-slate-700' : 'text-rose-600' }}">{{ $book->stock }} buku</p>
                        </div>
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/50 px-4 py-4">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Popularitas</p>
                            <p class="mt-2 text-sm font-semibold text-slate-700">{{ $book->borrowings()->count() }} kali dipinjam</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-slate-100 bg-white p-8 shadow-sm">
                    <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Sinopsis</p>
                    <div class="mt-4 prose prose-slate max-w-none text-sm leading-7 text-slate-600">
                        <p class="whitespace-pre-line">{{ $book->description ?: 'Belum ada sinopsis lengkap untuk buku ini.' }}</p>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-slate-100 bg-white p-8 shadow-sm">
                    <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Ajukan Peminjaman</p>

                    @if(! $borrowEligibility['can_borrow'])
                        <div class="mt-6 rounded-[1.5rem] border border-rose-200 bg-rose-50 p-5">
                            <p class="text-sm font-bold text-rose-700">Anda belum bisa meminjam buku saat ini.</p>
                            <ul class="mt-3 space-y-2 text-sm text-rose-700">
                                @foreach($borrowEligibility['reasons'] as $reason)
                                    <li class="flex items-start gap-2">
                                        <i class="fas fa-circle-exclamation mt-1 text-xs"></i>
                                        <span>{{ $reason }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @elseif($book->stock <= 0)
                        <div class="mt-6 rounded-[1.5rem] border border-rose-200 bg-rose-50 p-5 text-sm font-semibold text-rose-700">
                            Stok buku sedang habis. Silakan simpan ke favorit dan cek lagi nanti.
                        </div>
                    @else
                        <form x-ref="borrowForm" action="{{ route('peminjam.borrowings.store', $book) }}" method="POST" class="mt-6 space-y-6" @submit="if (!bypassBorrowConfirm) { $event.preventDefault(); confirmBorrow = true; return; } bypassBorrowConfirm = false;">
                            @csrf
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <label class="ml-1 text-xs font-bold uppercase tracking-widest text-slate-400">Jumlah Pinjam</label>
                                    <input type="number" name="quantity" value="1" min="1" max="{{ $book->stock }}" required class="mt-2 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500/10">
                                </div>
                                <div>
                                    <label class="ml-1 text-xs font-bold uppercase tracking-widest text-slate-400">Tanggal Pinjam</label>
                                    <input type="date" name="borrow_date" x-model="borrowDate" @change="updateDueDate()" value="{{ $today->format('Y-m-d') }}" min="{{ $today->format('Y-m-d') }}" required class="mt-2 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500/10">
                                </div>
                                <div>
                                    <label class="ml-1 text-xs font-bold uppercase tracking-widest text-slate-400">Alasan Meminjam</label>
                                    <input type="text" name="borrow_reason" required class="mt-2 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500/10" placeholder="Contoh: Referensi tugas akhir">
                                </div>
                                <div>
                                    <label class="ml-1 text-xs font-bold uppercase tracking-widest text-slate-400">Tanggal Kembali</label>
                                    <input type="date" name="due_date" x-model="dueDate" :min="borrowDate" required class="mt-2 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 focus:border-indigo-500 focus:ring-indigo-500/10">
                                </div>
                            </div>

                            <div class="rounded-[1.5rem] border border-slate-100 bg-slate-50 p-5 text-sm text-slate-600">
                                <div class="flex items-center justify-between gap-4">
                                    <span>Durasi pinjam default</span>
                                    <span class="font-bold text-slate-800">{{ $defaultDuration }} hari</span>
                                </div>
                                <div class="mt-3 flex items-center justify-between gap-4">
                                    <span>Grace period</span>
                                    <span class="font-bold text-slate-800">{{ (int) ($finePolicy['grace_period_days'] ?? 0) }} hari</span>
                                </div>
                                <div class="mt-3 flex items-center justify-between gap-4">
                                    <span>Maksimal denda</span>
                                    <span class="font-bold text-slate-800">{{ !empty($finePolicy['max_fine_amount']) ? 'Rp ' . number_format((float) $finePolicy['max_fine_amount'], 0, ',', '.') : 'Tanpa batas' }}</span>
                                </div>
                            </div>

                            <button type="submit" class="inline-flex items-center rounded-2xl bg-indigo-600 px-8 py-4 font-bold text-white transition-all hover:bg-indigo-700">
                                <i class="fas fa-book-reader mr-2"></i>
                                Ajukan Peminjaman
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div x-show="confirmBorrow" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center px-4 py-6">
            <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm" @click="confirmBorrow = false"></div>
            <div class="relative w-full max-w-lg rounded-[2rem] bg-white p-8 shadow-2xl">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-600">
                    <i class="fas fa-book-reader text-xl"></i>
                </div>
                <h3 class="mt-6 text-2xl font-black text-slate-800">Konfirmasi Peminjaman</h3>
                <p class="mt-3 text-sm leading-7 text-slate-500">
                    Anda akan mengajukan peminjaman buku <span class="font-bold text-slate-700">{{ $book->title }}</span> dengan durasi {{ $defaultDuration }} hari. Lanjutkan?
                </p>
                <div class="mt-8 flex gap-3">
                    <button type="button" @click="confirmBorrow = false" class="flex-1 rounded-2xl bg-slate-100 px-4 py-3 font-bold text-slate-600 transition-all hover:bg-slate-200">
                        Batal
                    </button>
                    <button type="button" @click="submitBorrowForm()" class="flex-1 rounded-2xl bg-indigo-600 px-4 py-3 font-bold text-white transition-all hover:bg-indigo-700">
                        Ya, Ajukan
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-peminjam-layout>
