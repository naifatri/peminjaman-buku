@extends('layouts.admin')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Kategori Buku</h2>
        <p class="text-sm text-slate-500 mt-1">Kelola kategori untuk mengelompokkan koleksi buku Anda.</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold shadow-lg shadow-indigo-600/20 transition-all duration-300 group">
        <i class="fas fa-plus mr-2 group-hover:rotate-90 transition-transform"></i>
        Tambah Kategori
    </a>
</div>

<div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 mb-8">
    <form action="{{ route('admin.categories.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau slug kategori..." 
                class="w-full pl-12 pr-4 py-3 rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 text-slate-600 placeholder:text-slate-300">
        </div>
        <div class="flex gap-3">
            <button type="submit" class="px-8 py-3 bg-slate-800 hover:bg-slate-900 text-white rounded-2xl font-bold transition-all duration-300">
                Filter
            </button>
            <a href="{{ route('admin.categories.index') }}" class="px-8 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl font-bold transition-all duration-300">
                Reset
            </a>
        </div>
    </form>
</div>

<div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">ID</th>
                    <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Nama Kategori</th>
                    <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Slug</th>
                    <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Jumlah Buku</th>
                    <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse ($categories as $category)
                <tr class="group hover:bg-slate-50/50 transition-colors">
                    <td class="px-8 py-5 text-sm font-medium text-slate-400">#{{ $category->id }}</td>
                    <td class="px-8 py-5">
                        <span class="text-sm font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">{{ $category->name }}</span>
                    </td>
                    <td class="px-8 py-5">
                        <span class="px-3 py-1 rounded-lg bg-slate-100 text-slate-500 text-xs font-medium">{{ $category->slug }}</span>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex items-center">
                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-bold {{ $category->books_count > 0 ? 'bg-indigo-50 text-indigo-600 border border-indigo-100' : 'bg-slate-50 text-slate-400 border border-slate-100' }}">
                                <i class="fas fa-book mr-2 opacity-50"></i>
                                {{ $category->books_count }} Buku
                            </span>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex justify-end items-center gap-2">
                            <a href="{{ route('admin.categories.edit', $category) }}" 
                                class="p-2 text-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all duration-200"
                                title="Edit Kategori">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all duration-200" title="Hapus Kategori">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-8 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4">
                                <i class="fas fa-tags text-2xl text-slate-200"></i>
                            </div>
                            <p class="text-slate-400 font-medium">Tidak ada kategori ditemukan.</p>
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
@endsection
