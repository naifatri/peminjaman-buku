@extends('layouts.admin')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Koleksi Buku</h2>
        <p class="text-sm text-slate-500 mt-1">Kelola data buku, stok, dan kategori dalam perpustakaan.</p>
    </div>
    <a href="{{ route('admin.books.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold shadow-lg shadow-indigo-600/20 transition-all duration-300 group">
        <i class="fas fa-plus mr-2 group-hover:rotate-90 transition-transform"></i>
        Tambah Buku Baru
    </a>
</div>

<div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 mb-8">
    <form action="{{ route('admin.books.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2 relative">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, penulis, atau ISBN..." 
                class="w-full pl-12 pr-4 py-3 rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 text-slate-600 placeholder:text-slate-300">
        </div>
        
        <select name="category" class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 text-slate-600 px-4 py-3 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat">
            <option value="">Semua Kategori</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>

        <div class="flex gap-2">
            <button type="submit" class="flex-1 px-4 py-3 bg-slate-800 hover:bg-slate-900 text-white rounded-2xl font-bold transition-all duration-300">
                Filter
            </button>
            <a href="{{ route('admin.books.index') }}" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl font-bold transition-all duration-300 flex items-center justify-center" title="Reset">
                <i class="fas fa-undo"></i>
            </a>
        </div>
    </form>
</div>

<div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Info Buku</th>
                    <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Kategori</th>
                    <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100 text-center">Stok</th>
                    <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Status</th>
                    <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse ($books as $book)
                <tr class="group hover:bg-slate-50/50 transition-colors">
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0 w-16 rounded-2xl bg-gradient-to-b from-white to-slate-100 border border-slate-100 p-2 flex items-center justify-center">
                                <div class="aspect-[3/4] w-full overflow-hidden rounded-[14px] bg-slate-50 flex items-center justify-center">
                                @if($book->cover_image)
                                    <img src="{{ asset('storage/' . $book->cover_image) }}" alt="Cover {{ $book->title }}" class="h-full w-full object-cover object-center rounded-[14px] shadow-[0_10px_22px_-14px_rgba(15,23,42,0.45)] group-hover:scale-[1.03] group-hover:shadow-[0_14px_28px_-16px_rgba(15,23,42,0.5)] transition-all duration-300">
                                @else
                                    <div class="w-full h-full bg-white rounded-[14px] flex items-center justify-center text-slate-300 shadow-[0_8px_18px_-16px_rgba(15,23,42,0.35)]">
                                        <i class="fas fa-image text-slate-300"></i>
                                    </div>
                                @endif
                                </div>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-700 group-hover:text-indigo-600 transition-colors line-clamp-1">{{ $book->title }}</h4>
                                <p class="text-xs text-slate-400 mt-1 italic">{{ $book->author }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <span class="px-3 py-1 rounded-lg bg-indigo-50 text-indigo-600 text-[10px] font-bold uppercase tracking-wider">
                            {{ $book->category->name ?? 'Uncategorized' }}
                        </span>
                    </td>
                    <td class="px-8 py-5 text-center">
                        <span class="text-sm font-bold {{ $book->stock > 0 ? 'text-slate-600' : 'text-rose-500' }}">
                            {{ $book->stock }}
                        </span>
                    </td>
                    <td class="px-8 py-5">
                        @if($book->status === 'tersedia' && $book->stock > 0)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tighter bg-emerald-50 text-emerald-600 border border-emerald-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-2"></span>
                                Tersedia
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tighter bg-rose-50 text-rose-600 border border-rose-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-2"></span>
                                Kosong
                            </span>
                        @endif
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex justify-end items-center gap-2">
                            <a href="{{ route('admin.books.edit', $book) }}" 
                                class="p-2 text-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all duration-200"
                                title="Edit Buku">
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
                    <td colspan="5" class="px-8 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4">
                                <i class="fas fa-book text-2xl text-slate-200"></i>
                            </div>
                            <p class="text-slate-400 font-medium">Tidak ada buku ditemukan.</p>
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
@endsection
