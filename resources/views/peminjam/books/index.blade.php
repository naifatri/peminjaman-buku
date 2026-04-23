<x-peminjam-layout page-title="Katalog Buku">
    @php
        $durationDays = (int) ($finePolicy['default_loan_duration_days'] ?? 7);
    @endphp

    <div class="space-y-8" x-data="{ ready: false, loading: false }" x-init="setTimeout(() => ready = true, 180)">
        @if(! $favoritesEnabled)
            <div class="rounded-[1.5rem] border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-800">
                Fitur favorit belum aktif karena tabel `book_favorites` belum tersedia. Jalankan `php artisan migrate` untuk mengaktifkannya.
            </div>
        @endif

        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Katalog Buku</h2>
                <p class="mt-1 text-sm text-slate-500">Temukan buku, simpan favorit, dan ajukan peminjaman dengan aturan yang jelas seperti sistem perpustakaan modern.</p>
            </div>
            <a href="{{ route('peminjam.books.favorites') }}" class="inline-flex items-center rounded-2xl bg-rose-500 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-rose-200 transition-all hover:bg-rose-600">
                <i class="fas fa-heart mr-2"></i>
                Buku Favorit
            </a>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Total Buku</p>
                <p class="mt-4 text-3xl font-black text-slate-800">{{ number_format($totalBooks) }}</p>
                <p class="mt-2 text-sm text-slate-500">Seluruh koleksi yang tersedia di katalog.</p>
            </div>
            <div class="rounded-[2rem] border border-emerald-100 bg-emerald-50/80 p-6 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-emerald-500">Siap Dipinjam</p>
                <p class="mt-4 text-3xl font-black text-emerald-700">{{ number_format($availableBooks) }}</p>
                <p class="mt-2 text-sm text-emerald-700/70">Buku yang stoknya masih tersedia.</p>
            </div>
            <div class="rounded-[2rem] border {{ $borrowEligibility['can_borrow'] ? 'border-indigo-100 bg-indigo-50/80' : 'border-rose-100 bg-rose-50/80' }} p-6 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] {{ $borrowEligibility['can_borrow'] ? 'text-indigo-500' : 'text-rose-500' }}">Status Peminjaman</p>
                <p class="mt-4 text-xl font-black {{ $borrowEligibility['can_borrow'] ? 'text-indigo-700' : 'text-rose-700' }}">
                    {{ $borrowEligibility['can_borrow'] ? 'Bisa Ajukan Peminjaman' : 'Peminjaman Dikunci Sementara' }}
                </p>
                <p class="mt-2 text-sm {{ $borrowEligibility['can_borrow'] ? 'text-indigo-700/70' : 'text-rose-700/80' }}">
                    {{ $borrowEligibility['can_borrow'] ? 'Anda tidak memiliki transaksi aktif maupun denda tertunggak.' : $borrowEligibility['reasons'][0] }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-[minmax(0,1.2fr)_360px]">
            <div class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Filter</p>
                        <h3 class="mt-2 text-xl font-black text-slate-800">Cari buku yang paling relevan</h3>
                    </div>
                </div>

                <form action="{{ route('peminjam.books.index') }}" method="GET" class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-12" @submit="loading = true">
                    <div class="relative md:col-span-2 xl:col-span-4">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, penulis, penerbit..." class="w-full rounded-2xl border-slate-200 py-3 pl-12 pr-4 text-slate-600 placeholder:text-slate-300 focus:border-indigo-500 focus:ring-indigo-500/10">
                    </div>

                    <select name="category_id" class="w-full rounded-2xl border-slate-200 px-4 py-3 pr-12 text-slate-600 focus:border-indigo-500 focus:ring-indigo-500/10 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%2364758b%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat xl:col-span-2">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>

                    <select name="author" class="w-full rounded-2xl border-slate-200 px-4 py-3 pr-12 text-slate-600 focus:border-indigo-500 focus:ring-indigo-500/10 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%2364758b%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat xl:col-span-2">
                        <option value="">Semua Penulis</option>
                        @foreach($authors as $authorName)
                            <option value="{{ $authorName }}" @selected(request('author') === $authorName)>{{ $authorName }}</option>
                        @endforeach
                    </select>

                    <select name="published_year" class="w-full rounded-2xl border-slate-200 px-4 py-3 pr-12 text-slate-600 focus:border-indigo-500 focus:ring-indigo-500/10 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%2364758b%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat xl:col-span-2">
                        <option value="">Semua Tahun</option>
                        @foreach($publishedYears as $year)
                            <option value="{{ $year }}" @selected(request('published_year') == $year)>{{ $year }}</option>
                        @endforeach
                    </select>

                    <select name="status" class="w-full rounded-2xl border-slate-200 px-4 py-3 pr-12 text-slate-600 focus:border-indigo-500 focus:ring-indigo-500/10 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%2364758b%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat xl:col-span-2">
                        <option value="">Semua Status</option>
                        <option value="tersedia" @selected(request('status') === 'tersedia')>Tersedia</option>
                        <option value="stok_sedikit" @selected(request('status') === 'stok_sedikit')>Stok Sedikit</option>
                        <option value="tidak_tersedia" @selected(request('status') === 'tidak_tersedia')>Tidak Tersedia</option>
                    </select>

                    <select name="sort" class="w-full rounded-2xl border-slate-200 px-4 py-3 pr-12 text-slate-600 focus:border-indigo-500 focus:ring-indigo-500/10 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%2364758b%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat md:col-span-1 xl:col-span-4">
                        <option value="latest" @selected(request('sort', 'latest') === 'latest')>Terbaru</option>
                        <option value="rating" @selected(request('sort') === 'rating')>Rating Tertinggi</option>
                        <option value="popular" @selected(request('sort') === 'popular')>Paling Populer</option>
                        <option value="az" @selected(request('sort') === 'az')>A-Z</option>
                    </select>

                    <div class="flex items-stretch gap-2 md:col-span-1 md:justify-end xl:col-span-8">
                        <button type="submit" class="rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition-all hover:bg-slate-950" :disabled="loading">
                            <span x-show="!loading">Terapkan</span>
                            <span x-show="loading" x-cloak>Memuat...</span>
                        </button>
                        <a href="{{ route('peminjam.books.index') }}" class="flex items-center justify-center rounded-2xl bg-slate-100 px-4 py-3 text-slate-600 transition-all hover:bg-slate-200">
                            <i class="fas fa-rotate-left"></i>
                        </a>
                    </div>
                </form>
            </div>
{{--  --}}
            <div class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Aturan Peminjaman</p>
                <div class="mt-6 grid gap-4">
                    <div class="rounded-[1.5rem] border border-indigo-100 bg-indigo-50/70 px-5 py-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-indigo-500">Maksimal Pinjam</p>
                        <p class="mt-2 text-xl font-black leading-none text-indigo-700">{{ $borrowEligibility['max_borrowings'] }} transaksi aktif</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-amber-100 bg-amber-50/70 px-5 py-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-amber-500">Durasi Pinjam</p>
                        <p class="mt-2 text-xl font-black leading-none text-amber-700">{{ $durationDays }} hari</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-rose-100 bg-rose-50/70 px-5 py-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-rose-500">Denda Tertunggak</p>
                        <p class="mt-2 text-xl font-black leading-none text-rose-700">Rp {{ number_format($borrowEligibility['unpaid_fines'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="!ready" x-cloak class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @for($i = 0; $i < 10; $i++)
                <div class="overflow-hidden rounded-[1.5rem] border border-slate-100 bg-white p-4 shadow-sm">
                    <div class="animate-pulse">
                        <div class="mb-4 h-4 w-24 rounded bg-slate-200"></div>
                        <div class="aspect-[3/4] rounded-2xl bg-slate-200"></div>
                        <div class="mt-4 h-4 w-3/4 rounded bg-slate-200"></div>
                        <div class="mt-2 h-3 w-1/2 rounded bg-slate-200"></div>
                        <div class="mt-4 h-3 w-full rounded bg-slate-200"></div>
                        <div class="mt-2 h-3 w-5/6 rounded bg-slate-200"></div>
                    </div>
                </div>
            @endfor
        </div>

        <div x-show="ready" x-cloak class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 items-stretch">
            @forelse ($books as $book)
                @php
                    $isFavorite = in_array($book->id, $favoriteBookIds, true);
                    $statusMap = [
                        'available' => ['label' => 'Tersedia', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                        'low' => ['label' => 'Stok Sedikit', 'class' => 'bg-amber-50 text-amber-700 border-amber-200'],
                        'out' => ['label' => 'Tidak Tersedia', 'class' => 'bg-rose-50 text-rose-700 border-rose-200'],
                    ];
                    $statusConfig = $statusMap[$book->inventory_status];
                @endphp
                <article
                    x-data="favoriteToggle({ initial: {{ $isFavorite ? 'true' : 'false' }}, url: '{{ route('peminjam.books.favorite', $book) }}', enabled: {{ $favoritesEnabled ? 'true' : 'false' }} })"
                    class="group flex h-full min-h-[470px] w-full flex-col overflow-hidden rounded-[1.35rem] border border-slate-100 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl hover:shadow-slate-200/80"
                >
                    <div class="p-4 pb-0">
                        <div class="mb-3 flex items-start justify-between">
                            <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-[9px] font-bold uppercase tracking-[0.16em] text-indigo-600 border border-indigo-100">
                                {{ $book->category->name ?? 'Umum' }}
                            </span>
                            <button type="button" @click="toggle()" :disabled="busy || !enabled" :class="isFavorite ? 'bg-rose-50 border-rose-200 text-rose-500' : 'bg-white border-rose-100 text-rose-300 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50'" class="flex h-9 w-9 items-center justify-center rounded-full border shadow-sm transition-all duration-300 {{ $favoritesEnabled ? '' : 'cursor-not-allowed opacity-50' }}">
                                <i :class="isFavorite ? 'fas fa-heart' : 'far fa-heart'" class="text-sm"></i>
                            </button>
                        </div>
                        <a href="{{ route('peminjam.books.show', $book) }}" class="flex aspect-[3/4] items-center justify-center overflow-hidden rounded-[1.1rem] border border-slate-100 bg-gradient-to-b from-white to-slate-100/90 p-3 sm:p-4">
                            @if($book->cover_image)
                                <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="h-full w-full rounded-2xl object-cover object-center shadow-[0_12px_30px_-16px_rgba(15,23,42,0.45)] transition-all duration-500 group-hover:scale-[1.04]">
                            @else
                                <div class="flex h-full w-full items-center justify-center rounded-2xl bg-white text-slate-300">
                                    <i class="fas fa-image text-4xl"></i>
                                </div>
                            @endif
                        </a>
                    </div>

                    <div class="flex flex-1 flex-col p-4 pt-4">
                        <div class="min-h-[3.4rem]">
                            <a href="{{ route('peminjam.books.show', $book) }}" class="line-clamp-2 text-base font-bold leading-snug text-slate-800 transition-colors group-hover:text-indigo-700">{{ $book->title }}</a>
                            <p class="mt-1 line-clamp-1 text-xs italic text-slate-500">{{ $book->author }}</p>
                        </div>

                        <div class="mt-3 flex items-center justify-between gap-3 text-[11px] text-slate-500">
                            <span class="inline-flex items-center font-bold text-amber-500">
                                <i class="fas fa-star mr-1"></i>
                                {{ number_format((float) ($book->rating ?? 4.0), 1) }}
                            </span>
                            <span class="text-right font-medium">{{ $book->favored_by_users_count }} favorit</span>
                        </div>

                        <div class="mt-3 grid grid-cols-2 gap-x-4 gap-y-2 text-[11px] text-slate-500">
                            <div class="min-w-0">
                                <p class="mb-1 text-[10px] uppercase tracking-wide text-slate-400">Penerbit</p>
                                <p class="line-clamp-1 font-semibold text-slate-700">{{ $book->publisher ?: '-' }}</p>
                            </div>
                            <div class="min-w-0 text-right">
                                <p class="mb-1 text-[10px] uppercase tracking-wide text-slate-400">Halaman</p>
                                <p class="font-semibold text-slate-700">{{ $book->page_count ? $book->page_count . ' hlm' : '-' }}</p>
                            </div>
                        </div>

                        <p class="mt-3 min-h-[3.2rem] line-clamp-2 text-[11px] leading-5 text-slate-500">
                            {{ $book->description ?: 'Belum ada deskripsi singkat untuk buku ini.' }}
                        </p>

                        <div class="mt-3 flex items-center justify-between gap-3 text-[11px]">
                            <span class="inline-flex items-center rounded-full border px-2.5 py-1 font-bold uppercase tracking-wide {{ $statusConfig['class'] }}">
                                <span class="mr-2 h-1.5 w-1.5 rounded-full bg-current"></span>
                                {{ $statusConfig['label'] }}
                            </span>
                            <span class="shrink-0 text-right font-semibold {{ $book->stock > 0 ? 'text-slate-600' : 'text-rose-600' }}">{{ $book->stock }} stok</span>
                        </div>

                        <div class="mt-auto grid grid-cols-2 gap-2 pt-4">
                            <a href="{{ route('peminjam.books.show', $book) }}" class="inline-flex items-center justify-center rounded-xl bg-slate-100 px-3 py-2.5 text-[11px] font-bold text-slate-600 transition-all hover:bg-slate-200">
                                <i class="fas fa-eye mr-2"></i>
                                Detail
                            </a>
                            <a href="{{ route('peminjam.books.show', $book) }}" class="inline-flex items-center justify-center rounded-xl {{ $book->stock > 0 && $borrowEligibility['can_borrow'] ? 'bg-gradient-to-r from-indigo-500 to-violet-600 text-white hover:from-indigo-600 hover:to-violet-700' : 'bg-slate-100 text-slate-400' }} px-3 py-2.5 text-[11px] font-bold transition-all">
                                <i class="fas fa-book-reader mr-2"></i>
                                Pinjam
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-[2rem] border border-slate-100 bg-white px-8 py-16 text-center shadow-sm">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50">
                        <i class="fas fa-book text-2xl text-slate-300"></i>
                    </div>
                    <p class="font-medium text-slate-500">Tidak ada buku yang cocok dengan filter saat ini.</p>
                </div>
            @endforelse
        </div>

        <div class="pt-2">
            {{ $books->links() }}
        </div>
    </div>

    <script>
        function favoriteToggle({ initial, url, enabled }) {
            return {
                isFavorite: initial,
                busy: false,
                enabled,
                async toggle() {
                    if (this.busy || !this.enabled) return;
                    this.busy = true;

                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                        });

                        const data = await response.json();
                        this.isFavorite = !!data.is_favorite;
                    } finally {
                        this.busy = false;
                    }
                }
            }
        }
    </script>
</x-peminjam-layout>
