@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center text-sm font-semibold text-slate-400 hover:text-indigo-600 transition-colors mb-4 group">
        <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
        Kembali ke Daftar
    </a>
    <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Tambah Kategori Baru</h2>
    <p class="text-sm text-slate-500 mt-1">Buat kategori baru untuk mengelompokkan koleksi buku.</p>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">Nama Kategori</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" 
                        class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 px-4 py-3 text-slate-600 placeholder:text-slate-300" 
                        placeholder="Contoh: Fiksi, Sains, Sejarah" required>
                    @error('name') <p class="mt-2 text-xs text-rose-500 font-medium ml-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="slug" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">Slug (URL)</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug') }}" 
                        class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 px-4 py-3 text-slate-600 placeholder:text-slate-300" 
                        placeholder="contoh-kategori-fiksi" required>
                    @error('slug') <p class="mt-2 text-xs text-rose-500 font-medium ml-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">Deskripsi (Opsional)</label>
                    <textarea name="description" id="description" rows="4" 
                        class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 px-4 py-3 text-slate-600 placeholder:text-slate-300"
                        placeholder="Berikan penjelasan singkat tentang kategori ini...">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-2 text-xs text-rose-500 font-medium ml-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 mt-10 pt-6 border-t border-slate-50">
                <a href="{{ route('admin.categories.index') }}" class="px-8 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl font-bold transition-all duration-300">
                    Batal
                </a>
                <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold shadow-lg shadow-indigo-600/20 transition-all duration-300">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
