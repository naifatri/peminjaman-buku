@extends('layouts.admin')

@section('content')
<div x-data="{
    selectedBooks: [],
    toggleAll(event) {
        this.selectedBooks = event.target.checked ? @js($books->pluck('id')->map(fn ($id) => (string) $id)->values()) : [];
    }
}">
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Koleksi Buku</h2>
            <p class="text-sm text-slate-500 mt-1">Kelola katalog, pantau stok, dan lakukan aksi cepat dari satu dashboard.</p>
        </div>
        <a href="{{ route('admin.books.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold shadow-lg shadow-indigo-600/20 transition-all duration-300 group">
            <i class="fas fa-plus mr-2 group-hover:rotate-90 transition-transform"></i>
            Tambah Buku Baru
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Total Buku</p>
            <p class="mt-3 text-4xl font-black text-slate-800">{{ $stats['total'] }}</p>
            <p class="mt-3 text-sm text-slate-500">Semua judul dalam katalog.</p>
        </div>
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Buku Tersedia</p>
            <p class="mt-3 text-4xl font-black text-emerald-600">{{ $stats['available'] }}</p>
            <p class="mt-3 text-sm text-slate-500">Stok aman lebih dari 3 eksemplar.</p>
        </div>
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Sedang Dipinjam</p>
            <p class="mt-3 text-4xl font-black text-amber-500">{{ $stats['borrowed'] }}</p>
            <p class="mt-3 text-sm text-slate-500">Transaksi aktif dan menunggu proses.</p>
        </div>
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Stok Habis</p>
            <p class="mt-3 text-4xl font-black text-rose-500">{{ $stats['out'] }}</p>
            <p class="mt-3 text-sm text-slate-500">Perlu restock atau nonaktifkan sementara.</p>
        </div>
    </div>

    @if($stockAlerts['out']->isNotEmpty() || $stockAlerts['low']->isNotEmpty())
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-8">
            <div class="bg-white rounded-[2rem] border border-rose-100 shadow-sm p-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-slate-800">Peringatan Stok Habis</h3>
                        <p class="text-sm text-slate-500 mt-1">Buku-buku ini tidak bisa dipinjam sampai stok diperbarui.</p>
                        <div class="mt-4 space-y-2">
                            @forelse($stockAlerts['out'] as $alertBook)
                                <a href="{{ route('admin.books.show', $alertBook) }}" class="flex items-center justify-between gap-3 rounded-2xl bg-rose-50/70 px-4 py-3 hover:bg-rose-50 transition-colors">
                                    <span class="text-sm font-semibold text-slate-700">{{ $alertBook->title }}</span>
                                    <span class="text-xs font-bold uppercase tracking-widest text-rose-600">0 stok</span>
                                </a>
                            @empty
                                <p class="text-sm text-slate-400">Tidak ada buku dengan stok habis.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[2rem] border border-amber-100 shadow-sm p-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-slate-800">Stok Menipis</h3>
                        <p class="text-sm text-slate-500 mt-1">Segera restock agar proses peminjaman tidak terhambat.</p>
                        <div class="mt-4 space-y-2">
                            @forelse($stockAlerts['low'] as $alertBook)
                                <a href="{{ route('admin.books.show', $alertBook) }}" class="flex items-center justify-between gap-3 rounded-2xl bg-amber-50/70 px-4 py-3 hover:bg-amber-50 transition-colors">
                                    <span class="text-sm font-semibold text-slate-700">{{ $alertBook->title }}</span>
                                    <span class="text-xs font-bold uppercase tracking-widest text-amber-600">{{ $alertBook->stock }} stok</span>
                                </a>
                            @empty
                                <p class="text-sm text-slate-400">Tidak ada buku dengan stok menipis.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 mb-8">
        <form action="{{ route('admin.books.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-[1.7fr_1fr_1fr_1fr_1fr_auto] gap-4" x-data="{ loading: false }" @submit="loading = true">
            <div class="relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, penulis, ISBN, atau rak..."
                    class="w-full pl-12 pr-4 py-3 rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 text-slate-600 placeholder:text-slate-300">
            </div>

            <select name="category" class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 text-slate-600 px-4 py-3 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>

            <select name="status" class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 text-slate-600 px-4 py-3 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat">
                <option value="">Semua Status</option>
                <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Tersedia</option>
                <option value="low" {{ request('status') === 'low' ? 'selected' : '' }}>Stok Menipis</option>
                <option value="out" {{ request('status') === 'out' ? 'selected' : '' }}>Habis</option>
            </select>

            <select name="stock" class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 text-slate-600 px-4 py-3 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat">
                <option value="">Semua Stok</option>
                <option value="zero" {{ request('stock') === 'zero' ? 'selected' : '' }}>0</option>
                <option value="lt3" {{ request('stock') === 'lt3' ? 'selected' : '' }}>< 3</option>
                <option value="gt3" {{ request('stock') === 'gt3' ? 'selected' : '' }}>> 3</option>
            </select>

            <select name="sort" class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 text-slate-600 px-4 py-3 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat">
                <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Terbaru</option>
                <option value="title_asc" {{ request('sort') === 'title_asc' ? 'selected' : '' }}>Judul A-Z</option>
                <option value="title_desc" {{ request('sort') === 'title_desc' ? 'selected' : '' }}>Judul Z-A</option>
                <option value="stock_desc" {{ request('sort') === 'stock_desc' ? 'selected' : '' }}>Stok Terbanyak</option>
                <option value="stock_asc" {{ request('sort') === 'stock_asc' ? 'selected' : '' }}>Stok Tersedikit</option>
            </select>

            <div class="flex gap-2">
                <button type="submit" :disabled="loading" :class="loading ? 'opacity-70 cursor-wait' : ''" class="flex-1 px-4 py-3 bg-slate-800 hover:bg-slate-900 text-white rounded-2xl font-bold transition-all duration-300">
                    <span x-show="!loading">Filter</span>
                    <span x-show="loading" x-cloak>Memuat...</span>
                </button>
                <a href="{{ route('admin.books.index') }}" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl font-bold transition-all duration-300 flex items-center justify-center" title="Reset">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Manajemen Koleksi</h3>
                <p class="text-sm text-slate-500 mt-1">Bulk action aktif saat ada minimal satu buku yang dipilih.</p>
            </div>

            <form action="{{ route('admin.books.bulk-action') }}" method="POST" class="grid grid-cols-1 md:grid-cols-[180px_220px_220px_auto] gap-3 items-center" x-data="{ submitting: false }" @submit="if (selectedBooks.length === 0) { alert('Pilih minimal satu buku terlebih dahulu.'); $event.preventDefault(); return; } submitting = true;">
                @csrf
                <template x-for="bookId in selectedBooks" :key="bookId">
                    <input type="hidden" name="book_ids[]" :value="bookId">
                </template>

                <select name="bulk_action" class="rounded-2xl border-slate-200 px-4 py-3 text-sm text-slate-600">
                    <option value="delete">Hapus Banyak Buku</option>
                    <option value="change_category">Ubah Kategori</option>
                    <option value="update_status">Update Status</option>
                </select>

                <select name="bulk_category_id" class="rounded-2xl border-slate-200 px-4 py-3 text-sm text-slate-600">
                    <option value="">Pilih kategori tujuan</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>

                <select name="bulk_status" class="rounded-2xl border-slate-200 px-4 py-3 text-sm text-slate-600">
                    <option value="">Pilih status target</option>
                    <option value="tersedia">Tersedia</option>
                    <option value="habis">Habis</option>
                </select>

                <button type="submit" :disabled="submitting || selectedBooks.length === 0" :class="submitting || selectedBooks.length === 0 ? 'opacity-60 cursor-not-allowed' : ''" class="px-5 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition-all duration-300">
                    <span x-show="!submitting">Jalankan Bulk Action</span>
                    <span x-show="submitting" x-cloak>Memproses...</span>
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1180px]">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-6 py-4 border-b border-slate-100">
                            <input type="checkbox" @change="toggleAll($event)" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th class="px-4 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Info Buku</th>
                        <th class="px-4 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">ISBN / Rak</th>
                        <th class="px-4 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Kategori</th>
                        <th class="px-4 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100 text-center">Stok</th>
                        <th class="px-4 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Status</th>
                        <th class="px-4 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Terakhir Diupdate</th>
                        <th class="px-4 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($books as $book)
                        <tr class="group hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-5 align-top">
                                <input type="checkbox" value="{{ $book->id }}" x-model="selectedBooks" class="mt-2 w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            </td>
                            <td class="px-4 py-5">
                                <div class="flex items-center gap-4">
                                    <a href="{{ route('admin.books.show', $book) }}" class="flex-shrink-0 w-16 rounded-2xl bg-gradient-to-b from-white to-slate-100 border border-slate-100 p-2 flex items-center justify-center hover:shadow-md transition-all duration-300">
                                        <div class="aspect-[3/4] w-full overflow-hidden rounded-[14px] bg-slate-50 flex items-center justify-center">
                                            @if($book->cover_image)
                                                <img src="{{ asset('storage/' . $book->cover_image) }}" alt="Cover {{ $book->title }}" class="h-full w-full object-cover object-center rounded-[14px]">
                                            @else
                                                <div class="w-full h-full bg-white rounded-[14px] flex items-center justify-center text-slate-300">
                                                    <i class="fas fa-image text-slate-300"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </a>
                                    <div class="min-w-0">
                                        <a href="{{ route('admin.books.show', $book) }}" class="text-sm font-bold text-slate-700 group-hover:text-indigo-600 transition-colors line-clamp-1">{{ $book->title }}</a>
                                        <p class="text-xs text-slate-400 mt-1 italic">{{ $book->author }}</p>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                                                {{ $book->active_borrowings_count }} aktif
                                            </span>
                                            @if($book->publisher)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-600 text-[10px] font-bold uppercase tracking-wider">
                                                    {{ $book->publisher }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-5">
                                <div class="space-y-2">
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">ISBN</p>
                                        <p class="text-sm font-semibold text-slate-700">{{ $book->isbn ?: '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Rak</p>
                                        <p class="text-sm font-semibold text-slate-700">{{ $book->rack_location ?: '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-5">
                                <span class="px-3 py-1 rounded-lg bg-indigo-50 text-indigo-600 text-[10px] font-bold uppercase tracking-wider">
                                    {{ $book->category->name ?? 'Uncategorized' }}
                                </span>
                            </td>
                            <td class="px-4 py-5 text-center">
                                <div class="inline-flex flex-col items-center gap-3">
                                    <span class="text-lg font-black {{ $book->stock > 0 ? 'text-slate-700' : 'text-rose-500' }}">{{ $book->stock }}</span>
                                    <div class="flex items-center gap-2">
                                        <form action="{{ route('admin.books.update-stock', $book) }}" method="POST" x-data="{ loading: false }" @submit="loading = true">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="mode" value="decrement">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" :disabled="loading" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition-all duration-300">-</button>
                                        </form>
                                        <form action="{{ route('admin.books.update-stock', $book) }}" method="POST" x-data="{ loading: false }" @submit="loading = true">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="mode" value="increment">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" :disabled="loading" class="w-8 h-8 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-600 transition-all duration-300">+</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-5">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tighter border {{ $book->inventory_status_color }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current mr-2"></span>
                                    {{ $book->inventory_status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-5">
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-slate-700">{{ $book->updated_at?->format('d M Y') }}</span>
                                    <span class="text-xs text-slate-400 mt-1">{{ $book->updated_at?->format('H:i') }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-5">
                                <div class="flex justify-end items-center gap-2">
                                    <a href="{{ route('admin.books.show', $book) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all duration-200" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.books.edit', $book) }}" class="p-2 text-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all duration-200" title="Edit Buku">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.books.destroy', $book) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all duration-200" title="Hapus Buku">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-8 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4">
                                        <i class="fas fa-book text-2xl text-slate-200"></i>
                                    </div>
                                    <p class="text-slate-500 font-medium">Belum ada data buku yang cocok dengan filter saat ini.</p>
                                    <p class="text-sm text-slate-400 mt-2">Coba ubah filter, reset pencarian, atau tambahkan buku baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-8">
        {{ $books->links() }}
    </div>
</div>
@endsection
