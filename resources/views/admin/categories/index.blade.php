@extends('layouts.admin')

@php
    $hasCreateErrors = $errors->any() && old('form_context', 'create') === 'create';
    $editErrorId = $errors->any() && old('form_context') === 'edit' ? (int) old('editing_id') : null;
    $deleteErrorId = $errors->any() && old('form_context') === 'delete' ? (int) old('deleting_id') : null;
    $topCategory = $stats['top_category'] ?? null;
@endphp

@section('content')
<div
    x-data="{
        isCreateOpen: {{ $hasCreateErrors ? 'true' : 'false' }},
        editOpenId: {{ $editErrorId ?: 'null' }},
        deleteOpenId: {{ $deleteErrorId ?: 'null' }},
        filterLoading: false,
        submitLoading: false,
        openCreate() {
            this.isCreateOpen = true;
            this.editOpenId = null;
            this.deleteOpenId = null;
        },
        openEdit(id) {
            this.editOpenId = id;
            this.isCreateOpen = false;
            this.deleteOpenId = null;
        },
        openDelete(id) {
            this.deleteOpenId = id;
            this.isCreateOpen = false;
            this.editOpenId = null;
        },
        closeModals() {
            this.isCreateOpen = false;
            this.editOpenId = null;
            this.deleteOpenId = null;
        }
    }"
    @keydown.escape.window="closeModals()"
    class="relative"
>
    <div
        x-show="filterLoading || submitLoading"
        x-cloak
        class="fixed inset-0 z-40 bg-slate-900/20 backdrop-blur-[1px]"
        aria-hidden="true"
    ></div>

    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Kategori Buku</h2>
            <p class="text-sm text-slate-500 mt-1">Kelola kategori, pantau distribusi buku, dan rapikan katalog dari satu halaman.</p>
        </div>
        <button
            type="button"
            @click="openCreate()"
            class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold shadow-lg shadow-indigo-600/20 transition-all duration-300 group"
        >
            <i class="fas fa-plus mr-2 group-hover:rotate-90 transition-transform"></i>
            Tambah Kategori
        </button>
    </div>

    <div class="metric-grid grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 mb-8">
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Total Kategori</p>
            <p class="mt-3 text-4xl font-black text-slate-800">{{ $stats['total'] }}</p>
            <p class="mt-3 text-sm text-slate-500">Semua kategori yang tersedia untuk pengelompokan buku.</p>
        </div>
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Kategori Terbanyak</p>
            <p class="mt-3 text-2xl font-black text-emerald-600">
                {{ $topCategory?->name ?: 'Belum ada kategori' }}
            </p>
            <p class="mt-3 text-sm text-slate-500">
                {{ $topCategory ? $topCategory->books_count . ' buku dalam kategori ini.' : 'Tambahkan kategori pertama untuk mulai mengelola katalog.' }}
            </p>
        </div>
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Kategori Tanpa Buku</p>
            <p class="mt-3 text-4xl font-black text-amber-500">{{ $stats['without_books'] }}</p>
            <p class="mt-3 text-sm text-slate-500">Kategori kosong yang mungkin perlu diisi atau dirapikan.</p>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 mb-8">
        <form
            action="{{ route('admin.categories.index') }}"
            method="GET"
            class="responsive-filter-form grid grid-cols-1 md:grid-cols-2 xl:grid-cols-[1.7fr_1fr_1fr_1fr_auto] gap-4"
            @submit="filterLoading = true"
        >
            <div class="relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama atau slug kategori..."
                    class="w-full pl-12 pr-4 py-3 rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 text-slate-600 placeholder:text-slate-300"
                >
            </div>

            <select name="book_filter" class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 text-slate-600 px-4 py-3 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat">
                <option value="">Semua Kategori</option>
                <option value="with_books" {{ request('book_filter') === 'with_books' ? 'selected' : '' }}>Kategori dengan Buku</option>
                <option value="without_books" {{ request('book_filter') === 'without_books' ? 'selected' : '' }}>Kategori tanpa Buku</option>
            </select>

            <select name="sort" class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 text-slate-600 px-4 py-3 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat">
                <option value="name_asc" {{ request('sort', 'name_asc') === 'name_asc' ? 'selected' : '' }}>Nama Kategori A-Z</option>
                <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Nama Kategori Z-A</option>
                <option value="books_desc" {{ request('sort') === 'books_desc' ? 'selected' : '' }}>Jumlah Buku Terbanyak</option>
                <option value="books_asc" {{ request('sort') === 'books_asc' ? 'selected' : '' }}>Jumlah Buku Tersedikit</option>
            </select>

            <select name="per_page" class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 text-slate-600 px-4 py-3 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat">
                <option value="10" {{ (string) $perPage === '10' ? 'selected' : '' }}>10 data / halaman</option>
                <option value="25" {{ (string) $perPage === '25' ? 'selected' : '' }}>25 data / halaman</option>
                <option value="50" {{ (string) $perPage === '50' ? 'selected' : '' }}>50 data / halaman</option>
            </select>

            <div class="responsive-filter-actions flex gap-2">
                <button type="submit" class="flex-1 px-4 py-3 bg-slate-800 hover:bg-slate-900 text-white rounded-2xl font-bold transition-all duration-300">
                    <span x-show="!filterLoading">Terapkan</span>
                    <span x-show="filterLoading" x-cloak>Memuat...</span>
                </button>
                <a href="{{ route('admin.categories.index') }}" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl font-bold transition-all duration-300 flex items-center justify-center" title="Reset">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="responsive-table-card bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Daftar Kategori</h3>
                <p class="text-sm text-slate-500 mt-1">
                    Menampilkan {{ $categories->count() }} dari {{ $categories->total() }} kategori.
                </p>
            </div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400">
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-100 text-slate-500">0 buku</span>
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200">1-5 buku</span>
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">6+ buku</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1120px]">
                <thead>
                    <tr class="bg-slate-50/60">
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">ID</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Kategori</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Slug</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Jumlah Buku</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Tanggal Dibuat</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Terakhir Diupdate</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($categories as $category)
                        @php
                            $bookBadgeClass = match (true) {
                                $category->books_count === 0 => 'bg-slate-100 text-slate-500 border border-slate-200',
                                $category->books_count <= 5 => 'bg-amber-50 text-amber-700 border border-amber-200',
                                default => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                            };
                        @endphp
                        <tr class="group hover:bg-slate-50/70 transition-colors">
                            <td class="px-6 py-5 text-sm font-medium text-slate-400">#{{ $category->id }}</td>
                            <td class="px-6 py-5">
                                <div class="min-w-0">
                                    <a href="{{ route('admin.books.index', ['category' => $category->id]) }}" class="text-sm font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">
                                        {{ $category->name }}
                                    </a>
                                    <p class="text-xs text-slate-400 mt-1">Klik untuk melihat daftar buku dalam kategori ini.</p>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="inline-flex items-center px-3 py-1 rounded-lg bg-slate-100 text-slate-500 text-xs font-semibold">
                                    {{ $category->slug }}
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-bold {{ $bookBadgeClass }}">
                                    <i class="fas fa-book mr-2 opacity-60"></i>
                                    {{ $category->books_count }} buku
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-slate-700">{{ $category->created_at?->format('d M Y') }}</span>
                                    <span class="text-xs text-slate-400 mt-1">{{ $category->created_at?->format('H:i') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-slate-700">{{ $category->updated_at?->format('d M Y') }}</span>
                                    <span class="text-xs text-slate-400 mt-1">{{ $category->updated_at?->format('H:i') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex justify-end items-center gap-2">
                                    <button
                                        type="button"
                                        @click="openEdit({{ $category->id }})"
                                        class="p-2 text-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all duration-200"
                                        title="Edit Kategori"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button
                                        type="button"
                                        @click="openDelete({{ $category->id }})"
                                        class="p-2 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all duration-200"
                                        title="Hapus Kategori"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="7" class="px-8 py-16 text-center">
                                <div class="flex flex-col items-center max-w-md mx-auto">
                                    <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4">
                                        <i class="fas fa-tags text-2xl text-slate-200"></i>
                                    </div>
                                    @if($stats['total'] === 0)
                                        <p class="text-slate-700 font-semibold text-lg">Belum ada kategori</p>
                                        <p class="text-sm text-slate-400 mt-2">Mulai tambahkan kategori agar katalog buku lebih rapi dan mudah dikelola.</p>
                                        <button
                                            type="button"
                                            @click="openCreate()"
                                            class="mt-6 inline-flex items-center px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold transition-all duration-300"
                                        >
                                            Tambah Kategori
                                        </button>
                                    @else
                                        <p class="text-slate-700 font-semibold text-lg">Tidak ada kategori ditemukan</p>
                                        <p class="text-sm text-slate-400 mt-2">Coba ubah pencarian, filter, atau urutan data yang sedang digunakan.</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-8">
        {{ $categories->links() }}
    </div>

    @foreach ($categories as $category)
        <div
            x-show="editOpenId === {{ $category->id }}"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            aria-modal="true"
            role="dialog"
        >
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeModals()"></div>
            <div class="responsive-modal relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl border border-slate-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-slate-800">Edit Kategori</h3>
                        <p class="text-sm text-slate-500 mt-1">Perbarui nama, slug, dan detail kategori tanpa meninggalkan halaman ini.</p>
                    </div>
                    <button type="button" @click="closeModals()" class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="p-6 space-y-5" @submit="submitLoading = true">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="form_context" value="edit">
                    <input type="hidden" name="editing_id" value="{{ $category->id }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Kategori</label>
                            <input
                                type="text"
                                name="name"
                                value="{{ $editErrorId === $category->id ? old('name') : $category->name }}"
                                class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 text-slate-700 px-4 py-3"
                                placeholder="Masukkan nama kategori"
                                required
                            >
                            @if($editErrorId === $category->id)
                                @error('name')
                                    <p class="text-sm text-rose-500 mt-2">{{ $message }}</p>
                                @enderror
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Slug</label>
                            <input
                                type="text"
                                name="slug"
                                value="{{ $editErrorId === $category->id ? old('slug') : $category->slug }}"
                                class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 text-slate-700 px-4 py-3"
                                placeholder="contoh-kategori"
                                required
                            >
                            @if($editErrorId === $category->id)
                                @error('slug')
                                    <p class="text-sm text-rose-500 mt-2">{{ $message }}</p>
                                @enderror
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Deskripsi</label>
                        <textarea
                            name="description"
                            rows="4"
                            class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 text-slate-700 px-4 py-3"
                            placeholder="Tambahkan deskripsi singkat kategori..."
                        >{{ $editErrorId === $category->id ? old('description') : $category->description }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="rounded-2xl bg-slate-50 px-4 py-3">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Jumlah Buku</p>
                            <p class="text-lg font-black text-slate-800 mt-2">{{ $category->books_count }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Dibuat</p>
                            <p class="text-sm font-semibold text-slate-700 mt-2">{{ $category->created_at?->format('d M Y H:i') }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Diupdate</p>
                            <p class="text-sm font-semibold text-slate-700 mt-2">{{ $category->updated_at?->format('d M Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="closeModals()" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl font-bold transition-all duration-300">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold transition-all duration-300">
                            <span x-show="!submitLoading">Simpan Perubahan</span>
                            <span x-show="submitLoading" x-cloak>Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div
            x-show="deleteOpenId === {{ $category->id }}"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            aria-modal="true"
            role="dialog"
        >
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeModals()"></div>
            <div class="responsive-modal relative w-full max-w-lg bg-white rounded-[2rem] shadow-2xl border border-slate-100 overflow-hidden">
                <div class="p-6">
                    <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center mb-4">
                        <i class="fas fa-trash-alt text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">Hapus Kategori</h3>
                    <p class="text-sm text-slate-500 mt-2">Apakah kamu yakin ingin menghapus kategori ini?</p>
                    <p class="text-sm text-slate-400 mt-2">
                        Kategori <span class="font-semibold text-slate-700">{{ $category->name }}</span>
                        @if($category->books_count > 0)
                            masih memiliki {{ $category->books_count }} buku dan tidak dapat dihapus.
                        @else
                            akan dihapus secara permanen dari daftar aktif.
                        @endif
                    </p>

                    <div class="mt-5 rounded-2xl {{ $category->books_count > 0 ? 'bg-amber-50 border border-amber-200 text-amber-800' : 'bg-slate-50 border border-slate-200 text-slate-600' }} px-4 py-3 text-sm">
                        @if($category->books_count > 0)
                            Pindahkan atau hapus buku dalam kategori ini terlebih dahulu sebelum menghapus kategori.
                        @else
                            Tindakan ini hanya tersedia untuk kategori yang tidak memiliki buku.
                        @endif
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" @click="closeModals()" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl font-bold transition-all duration-300">
                            Batal
                        </button>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" @submit="submitLoading = true">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="px-5 py-3 rounded-2xl font-bold transition-all duration-300 {{ $category->books_count > 0 ? 'bg-rose-100 text-rose-300 cursor-not-allowed' : 'bg-rose-500 hover:bg-rose-600 text-white' }}"
                                {{ $category->books_count > 0 ? 'disabled' : '' }}
                            >
                                <span x-show="!submitLoading">Ya, Hapus</span>
                                <span x-show="submitLoading" x-cloak>Memproses...</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div
        x-show="isCreateOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        aria-modal="true"
        role="dialog"
    >
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeModals()"></div>
        <div class="responsive-modal relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-xl font-bold text-slate-800">Tambah Kategori</h3>
                    <p class="text-sm text-slate-500 mt-1">Buat kategori baru untuk membantu pengelompokan koleksi buku.</p>
                </div>
                <button type="button" @click="closeModals()" class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('admin.categories.store') }}" method="POST" class="p-6 space-y-5" @submit="submitLoading = true">
                @csrf
                <input type="hidden" name="form_context" value="create">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Kategori</label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 text-slate-700 px-4 py-3"
                            placeholder="Masukkan nama kategori"
                            required
                        >
                        @if($hasCreateErrors)
                            @error('name')
                                <p class="text-sm text-rose-500 mt-2">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Slug</label>
                        <input
                            type="text"
                            name="slug"
                            value="{{ old('slug') }}"
                            class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 text-slate-700 px-4 py-3"
                            placeholder="contoh-kategori"
                            required
                        >
                        @if($hasCreateErrors)
                            @error('slug')
                                <p class="text-sm text-rose-500 mt-2">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Deskripsi</label>
                    <textarea
                        name="description"
                        rows="4"
                        class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 text-slate-700 px-4 py-3"
                        placeholder="Tambahkan deskripsi singkat kategori..."
                    >{{ old('description') }}</textarea>
                </div>

                <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-500">
                    Gunakan slug yang unik agar kategori mudah dipakai pada pencarian, URL, dan integrasi katalog.
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="closeModals()" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl font-bold transition-all duration-300">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold transition-all duration-300">
                        <span x-show="!submitLoading">Simpan Kategori</span>
                        <span x-show="submitLoading" x-cloak>Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
