<x-peminjam-layout page-title="Dashboard Peminjam">
    <div class="mb-8 flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Ringkasan Sistem</h2>
            <p class="text-sm text-slate-500 mt-1">Selamat datang kembali, berikut adalah statistik perpustakaan Anda hari ini.</p>
        </div>
        <a href="{{ route('peminjam.borrowings.index') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold shadow-lg shadow-indigo-600/20 transition-all duration-300">
            <i class="fas fa-history mr-2"></i>
            Lihat Riwayat
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
        <div class="relative overflow-hidden bg-white p-8 rounded-3xl shadow-sm border border-slate-100 group hover:shadow-xl hover:shadow-indigo-500/5 transition-all duration-300">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-indigo-50 rounded-full group-hover:scale-110 transition-transform duration-500 opacity-50"></div>
            <div class="relative flex items-center">
                <div class="p-4 rounded-2xl bg-indigo-500 text-white shadow-lg shadow-indigo-200 mr-6 group-hover:rotate-6 transition-transform">
                    <i class="fas fa-book text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-400 font-bold uppercase tracking-wider mb-1">Total Buku</p>
                    <p class="text-4xl font-black text-slate-800">{{ $totalBooks }}</p>
                </div>
            </div>
            <div class="mt-6 flex items-center text-xs font-semibold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full w-fit">
                <i class="fas fa-arrow-up mr-1.5"></i>
                <span>Terdata di sistem</span>
            </div>
        </div>

        <div class="relative overflow-hidden bg-white p-8 rounded-3xl shadow-sm border border-slate-100 group hover:shadow-xl hover:shadow-amber-500/5 transition-all duration-300">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-amber-50 rounded-full group-hover:scale-110 transition-transform duration-500 opacity-50"></div>
            <div class="relative flex items-center">
                <div class="p-4 rounded-2xl bg-amber-500 text-white shadow-lg shadow-amber-200 mr-6 group-hover:rotate-6 transition-transform">
                    <i class="fas fa-exchange-alt text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-400 font-bold uppercase tracking-wider mb-1">Peminjaman Aktif</p>
                    <p class="text-4xl font-black text-slate-800">{{ $activeBorrowings }}</p>
                </div>
            </div>
            <div class="mt-6 flex items-center text-xs font-semibold text-amber-600 bg-amber-50 px-3 py-1 rounded-full w-fit">
                <i class="fas fa-clock mr-1.5"></i>
                <span>Sedang dipinjam</span>
            </div>
        </div>

        <div class="relative overflow-hidden bg-white p-8 rounded-3xl shadow-sm border border-slate-100 group hover:shadow-xl hover:shadow-rose-500/5 transition-all duration-300">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-rose-50 rounded-full group-hover:scale-110 transition-transform duration-500 opacity-50"></div>
            <div class="relative flex items-center">
                <div class="p-4 rounded-2xl bg-rose-500 text-white shadow-lg shadow-rose-200 mr-6 group-hover:rotate-6 transition-transform">
                    <i class="fas fa-wallet text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-400 font-bold uppercase tracking-wider mb-1">Total Denda</p>
                    <p class="text-3xl font-black text-slate-800">Rp {{ number_format($unpaidFines, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="mt-6 flex items-center text-xs font-semibold text-rose-600 bg-rose-50 px-3 py-1 rounded-full w-fit">
                <i class="fas fa-exclamation-circle mr-1.5"></i>
                <span>Belum dibayar</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 mb-8">
        <form action="{{ route('peminjam.books.index') }}" method="GET" class="grid grid-cols-1 lg:grid-cols-[1.6fr_0.8fr_0.8fr_0.8fr_auto] gap-4">
            <div class="relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari judul, penulis, atau penerbit..."
                    class="w-full pl-12 pr-4 py-3 rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 text-slate-600 placeholder:text-slate-300"
                >
            </div>

            <select name="category_id" class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 text-slate-600 px-4 py-3 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <select name="status" class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 text-slate-600 px-4 py-3 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat">
                <option value="">Semua Status</option>
                <option value="tersedia" {{ request('status') === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                <option value="dipinjam" {{ request('status') === 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
            </select>

            <select name="sort" class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 text-slate-600 px-4 py-3 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat">
                <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Terbaru</option>
                <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Terpopuler</option>
                <option value="az" {{ request('sort') === 'az' ? 'selected' : '' }}>A-Z</option>
            </select>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-3 bg-slate-800 hover:bg-slate-900 text-white rounded-2xl font-bold transition-all duration-300">
                    Filter
                </button>
                <a href="{{ route('peminjam.books.index') }}" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl font-bold transition-all duration-300 flex items-center justify-center" title="Reset">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 2xl:grid-cols-5 gap-4 xl:gap-5 items-stretch">
        @forelse ($books as $book)
            @php
                $isAvailable = $book->stock > 0;
                $isLowStock = $book->stock > 0 && $book->stock <= 3;
                $statusClass = $isLowStock
                    ? 'bg-amber-50 text-amber-700 border-amber-200'
                    : ($isAvailable
                        ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                        : 'bg-rose-50 text-rose-700 border-rose-200');
                $statusLabel = $isLowStock ? 'Stok Sedikit' : ($isAvailable ? 'Tersedia' : 'Dipinjam');
                $stockBarClass = $isLowStock ? 'bg-amber-400' : ($isAvailable ? 'bg-emerald-400' : 'bg-rose-400');
                $stockWidth = $book->stock <= 0 ? 'w-0' : ($book->stock <= 3 ? 'w-1/3' : ($book->stock <= 6 ? 'w-2/3' : 'w-full'));
                $categoryName = $book->category->name ?? 'Umum';
                $tags = collect(explode(',', (string) $book->genre_tags))
                    ->map(fn ($tag) => trim($tag))
                    ->filter(fn ($tag) => $tag !== '' && strcasecmp($tag, $categoryName) !== 0)
                    ->take(2);
            @endphp
            <article x-data="{ wished: false, popping: false }" class="bg-white rounded-[1.25rem] border border-slate-100 shadow-sm hover:-translate-y-1.5 hover:shadow-xl hover:shadow-slate-200/80 transition-all duration-300 overflow-hidden group max-w-[260px] w-full mx-auto h-full flex flex-col">
                <div class="p-4 pb-0">
                    <div class="flex items-start justify-between mb-3">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[9px] font-bold uppercase tracking-[0.16em] bg-indigo-50 text-indigo-600 border border-indigo-100">
                            {{ $categoryName }}
                        </span>
                        <button
                            type="button"
                            @click="wished = !wished; popping = true; setTimeout(() => popping = false, 220)"
                            :class="wished ? 'bg-rose-50 border-rose-200 text-rose-500' : 'bg-white border-rose-100 text-rose-300 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50'"
                            class="w-9 h-9 rounded-full border transition-all duration-300 flex items-center justify-center shadow-sm"
                            aria-label="Toggle wishlist"
                        >
                            <i :class="wished ? 'fas fa-heart' : 'far fa-heart'" class="text-sm transition-transform duration-200" :style="popping ? 'transform: scale(1.18)' : ''"></i>
                        </button>
                    </div>
                    <div class="aspect-[3/4] rounded-[1.1rem] bg-gradient-to-b from-white to-slate-100/90 border border-slate-100 p-3 sm:p-4 flex items-center justify-center overflow-hidden">
                        @if($book->cover_image)
                            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="h-full w-full rounded-2xl object-cover object-center shadow-[0_12px_30px_-16px_rgba(15,23,42,0.45)] group-hover:scale-[1.04] group-hover:shadow-[0_20px_40px_-18px_rgba(15,23,42,0.5)] transition-all duration-500">
                        @else
                            <div class="h-full w-full rounded-2xl bg-white shadow-[0_10px_24px_-18px_rgba(15,23,42,0.35)] flex items-center justify-center text-slate-300">
                                <i class="fas fa-image text-4xl"></i>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="p-4 pt-5 flex-1 flex flex-col text-left">
                    <div>
                        <h3 class="text-base font-bold text-slate-800 leading-snug line-clamp-2">{{ $book->title }}</h3>
                        <p class="text-xs text-slate-500 mt-1 italic line-clamp-1">{{ $book->author }}</p>
                    </div>

                    <div class="mt-4 flex items-center justify-between gap-3 text-[11px] text-slate-500">
                        <span class="inline-flex items-center font-bold text-amber-500 shrink-0">
                            <i class="fas fa-star mr-1"></i>
                            {{ number_format((float) ($book->rating ?? 4.0), 1) }}
                        </span>
                        <span class="truncate">{{ $book->published_year ?: '-' }}</span>
                    </div>

                    <div class="mt-3 grid grid-cols-2 gap-3 text-[11px] text-slate-500">
                        <div>
                            <p class="text-[10px] uppercase tracking-wide text-slate-400 mb-1">Penerbit</p>
                            <p class="font-semibold text-slate-700 line-clamp-1">{{ $book->publisher ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-wide text-slate-400 mb-1">Halaman</p>
                            <p class="font-semibold text-slate-700">{{ $book->page_count ? $book->page_count . ' hlm' : '-' }}</p>
                        </div>
                    </div>

                    <p class="mt-3 text-[11px] leading-5 text-slate-500 line-clamp-2">
                        {{ $book->description ?: 'Belum ada deskripsi singkat untuk buku ini.' }}
                    </p>

                    <div class="mt-3 flex flex-wrap gap-2 min-h-[28px]">
                        @foreach($tags as $tag)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 text-[9px] font-bold uppercase tracking-wide">
                                {{ $tag }}
                            </span>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        <div class="flex items-center justify-between gap-3 text-[11px] mb-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full border {{ $statusClass }} font-bold uppercase tracking-wide">
                                <span class="w-1.5 h-1.5 rounded-full bg-current mr-2"></span>
                                {{ $statusLabel }}
                            </span>
                            <span class="font-semibold {{ $isAvailable ? 'text-slate-600' : 'text-rose-600' }}">{{ $book->stock }} stok</span>
                        </div>
                        <div class="h-1.5 rounded-full bg-slate-200 overflow-hidden">
                            <div class="h-full rounded-full {{ $stockBarClass }} {{ $stockWidth }}"></div>
                        </div>
                    </div>

                    <div class="mt-auto pt-4 grid grid-cols-2 gap-2">
                        <a href="{{ route('peminjam.books.show', $book) }}" class="inline-flex items-center justify-center px-3 py-2.5 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 text-[11px] font-bold transition-all duration-300">
                            <i class="fas fa-eye mr-2"></i>
                            Detail
                        </a>
                        @if($isAvailable)
                            <a href="{{ route('peminjam.books.show', $book) }}" class="inline-flex items-center justify-center px-3 py-2.5 rounded-xl bg-gradient-to-r from-indigo-500 to-violet-600 text-white hover:from-indigo-600 hover:to-violet-700 text-[11px] font-bold transition-all duration-300 shadow-[0_16px_28px_-18px_rgba(79,70,229,0.8)]">
                                <i class="fas fa-book-reader mr-2"></i>
                                Pinjam
                            </a>
                        @else
                            <button type="button" disabled class="w-full inline-flex items-center justify-center px-3 py-2.5 rounded-xl bg-slate-100 text-slate-400 text-[11px] font-bold cursor-not-allowed">
                                Sedang Dipinjam
                            </button>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full bg-white rounded-[2rem] border border-slate-100 shadow-sm px-8 py-16 text-center">
                <div class="w-16 h-16 mx-auto bg-slate-50 rounded-2xl flex items-center justify-center mb-4">
                    <i class="fas fa-book text-2xl text-slate-300"></i>
                </div>
                <p class="text-slate-500 font-medium">Tidak ada buku yang cocok dengan filter saat ini.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $books->links() }}
    </div>
</x-peminjam-layout>
