<x-peminjam-layout page-title="Buku Favorit">
    <div class="space-y-8">
        @if(! $favoritesEnabled)
            <div class="rounded-[1.5rem] border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-800">
                Fitur favorit belum aktif karena tabel `book_favorites` belum tersedia. Jalankan `php artisan migrate` untuk mengaktifkannya.
            </div>
        @endif

        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Buku Favorit</h2>
                <p class="mt-1 text-sm text-slate-500">Simpan buku yang menarik untuk dibaca atau diajukan nanti.</p>
            </div>
            <a href="{{ route('peminjam.books.index') }}" class="inline-flex items-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition-all hover:bg-slate-950">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Katalog
            </a>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="rounded-[2rem] border border-rose-100 bg-rose-50/80 p-6 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-rose-500">Total Favorit</p>
                <p class="mt-4 text-3xl font-black text-rose-700">{{ number_format($favoriteBooks->total()) }}</p>
            </div>
            <div class="rounded-[2rem] border border-amber-100 bg-amber-50/80 p-6 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-amber-500">Durasi Pinjam</p>
                <p class="mt-4 text-3xl font-black text-amber-700">{{ (int) ($finePolicy['default_loan_duration_days'] ?? 7) }} hari</p>
            </div>
            <div class="rounded-[2rem] border {{ $borrowEligibility['can_borrow'] ? 'border-emerald-100 bg-emerald-50/80' : 'border-rose-100 bg-rose-50/80' }} p-6 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] {{ $borrowEligibility['can_borrow'] ? 'text-emerald-500' : 'text-rose-500' }}">Kelayakan Pinjam</p>
                <p class="mt-4 text-lg font-black {{ $borrowEligibility['can_borrow'] ? 'text-emerald-700' : 'text-rose-700' }}">
                    {{ $borrowEligibility['can_borrow'] ? 'Siap meminjam' : 'Belum bisa meminjam' }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4 2xl:grid-cols-5">
            @forelse($favoriteBooks as $book)
                @php
                    $isFavorite = in_array($book->id, $favoriteBookIds, true);
                @endphp
                <article
                    x-data="favoriteToggle({ initial: {{ $isFavorite ? 'true' : 'false' }}, url: '{{ route('peminjam.books.favorite', $book) }}', enabled: {{ $favoritesEnabled ? 'true' : 'false' }} })"
                    x-show="isFavorite"
                    class="group flex h-full min-h-[500px] w-full flex-col overflow-hidden rounded-[1.35rem] border border-slate-100 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl hover:shadow-slate-200/80"
                >
                    <div class="p-4 pb-0">
                        <div class="mb-3 flex items-start justify-between">
                            <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-[9px] font-bold uppercase tracking-[0.16em] text-indigo-600 border border-indigo-100">
                                {{ $book->category->name ?? 'Umum' }}
                            </span>
                            <button type="button" @click="toggle()" :disabled="!enabled" class="flex h-9 w-9 items-center justify-center rounded-full border border-rose-200 bg-rose-50 text-rose-500 shadow-sm transition-all duration-300 {{ $favoritesEnabled ? '' : 'cursor-not-allowed opacity-50' }}">
                                <i class="fas fa-heart text-sm"></i>
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
                    <div class="flex flex-1 flex-col p-4 pt-5">
                        <a href="{{ route('peminjam.books.show', $book) }}" class="line-clamp-2 text-base font-bold leading-snug text-slate-800 transition-colors group-hover:text-indigo-700">{{ $book->title }}</a>
                        <p class="mt-1 line-clamp-1 text-xs italic text-slate-500">{{ $book->author }}</p>
                        <p class="mt-3 line-clamp-3 text-[11px] leading-5 text-slate-500">{{ $book->description ?: 'Belum ada deskripsi singkat untuk buku ini.' }}</p>
                        <div class="mt-auto grid grid-cols-2 gap-2 pt-4">
                            <a href="{{ route('peminjam.books.show', $book) }}" class="inline-flex items-center justify-center rounded-xl bg-slate-100 px-3 py-2.5 text-[11px] font-bold text-slate-600 transition-all hover:bg-slate-200">
                                Detail
                            </a>
                            <a href="{{ route('peminjam.books.show', $book) }}" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-indigo-500 to-violet-600 px-3 py-2.5 text-[11px] font-bold text-white transition-all hover:from-indigo-600 hover:to-violet-700">
                                Pinjam
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-[2rem] border border-slate-100 bg-white px-8 py-16 text-center shadow-sm">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-rose-50 text-rose-300">
                        <i class="fas fa-heart text-2xl"></i>
                    </div>
                    <p class="font-medium text-slate-500">Belum ada buku favorit. Simpan buku dari katalog untuk melihatnya di sini.</p>
                </div>
            @endforelse
        </div>

        <div class="pt-2">
            {{ $favoriteBooks->links() }}
        </div>
    </div>

    <script>
        function favoriteToggle({ initial, url, enabled }) {
            return {
                isFavorite: initial,
                enabled,
                async toggle() {
                    if (!this.enabled) return;
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    });

                    const data = await response.json();
                    this.isFavorite = !!data.is_favorite;
                }
            }
        }
    </script>
</x-peminjam-layout>
