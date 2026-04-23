@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <a href="{{ route('admin.books.index') }}" class="inline-flex items-center text-sm font-semibold text-slate-400 hover:text-indigo-600 transition-colors mb-4 group">
        <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
        Kembali ke Daftar
    </a>
    <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Tambah Buku Baru</h2>
    <p class="text-sm text-slate-500 mt-1">Lengkapi informasi di bawah ini untuk menambahkan koleksi buku baru.</p>
</div>

<div class="max-w-4xl">
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
        <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data" x-data="{ submitting: false }" @submit="submitting = true">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">Judul Buku</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" 
                        class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 px-4 py-3 text-slate-600 placeholder:text-slate-300" 
                        placeholder="Masukkan judul lengkap buku" required>
                    @error('title') <p class="mt-2 text-xs text-rose-500 font-medium ml-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="author" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">Penulis</label>
                    <input type="text" name="author" id="author" value="{{ old('author') }}" 
                        class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 px-4 py-3 text-slate-600 placeholder:text-slate-300" 
                        placeholder="Nama penulis" required>
                    @error('author') <p class="mt-2 text-xs text-rose-500 font-medium ml-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="isbn" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">ISBN</label>
                    <input type="text" name="isbn" id="isbn" value="{{ old('isbn') }}" 
                        class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 px-4 py-3 text-slate-600 placeholder:text-slate-300" 
                        placeholder="Nomor ISBN">
                    @error('isbn') <p class="mt-2 text-xs text-rose-500 font-medium ml-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="rack_location" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">Lokasi Rak</label>
                    <input type="text" name="rack_location" id="rack_location" value="{{ old('rack_location') }}"
                        class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 px-4 py-3 text-slate-600 placeholder:text-slate-300"
                        placeholder="Contoh: A1, B2, C-03">
                    @error('rack_location') <p class="mt-2 text-xs text-rose-500 font-medium ml-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">Kategori</label>
                    <select name="category_id" id="category_id" 
                        class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 px-4 py-3 text-slate-600 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="mt-2 text-xs text-rose-500 font-medium ml-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="stock" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">Stok Awal</label>
                    <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}" min="0" 
                        class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 px-4 py-3 text-slate-600" required>
                    @error('stock') <p class="mt-2 text-xs text-rose-500 font-medium ml-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="published_year" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">Tahun Terbit</label>
                    <input type="number" name="published_year" id="published_year" value="{{ old('published_year') }}" 
                        class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 px-4 py-3 text-slate-600" 
                        placeholder="Contoh: 2023">
                    @error('published_year') <p class="mt-2 text-xs text-rose-500 font-medium ml-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="publisher" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">Penerbit</label>
                    <input type="text" name="publisher" id="publisher" value="{{ old('publisher') }}"
                        class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 px-4 py-3 text-slate-600"
                        placeholder="Nama penerbit">
                    @error('publisher') <p class="mt-2 text-xs text-rose-500 font-medium ml-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="page_count" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">Jumlah Halaman</label>
                    <input type="number" name="page_count" id="page_count" value="{{ old('page_count') }}" min="1"
                        class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 px-4 py-3 text-slate-600"
                        placeholder="Contoh: 320">
                    @error('page_count') <p class="mt-2 text-xs text-rose-500 font-medium ml-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="rating" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">Rating</label>
                    <input type="number" name="rating" id="rating" value="{{ old('rating', 4.5) }}" min="0" max="5" step="0.1"
                        class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 px-4 py-3 text-slate-600"
                        placeholder="0.0 - 5.0">
                    @error('rating') <p class="mt-2 text-xs text-rose-500 font-medium ml-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="cover_image" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">Cover Buku</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-200 border-dashed rounded-[2rem] hover:border-indigo-400 transition-colors group">
                        <div class="space-y-1 text-center">
                            <i class="fas fa-cloud-upload-alt text-4xl text-slate-300 group-hover:text-indigo-400 transition-colors mb-3"></i>
                            <div class="flex text-sm text-slate-600">
                                <label for="cover_image" class="relative cursor-pointer bg-white rounded-md font-bold text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
                                    <span>Upload a file</span>
                                    <input id="cover_image" name="cover_image" type="file" class="sr-only" accept="image/*">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-slate-400">PNG, JPG, GIF up to 2MB</p>
                        </div>
                    </div>
                    @error('cover_image') <p class="mt-2 text-xs text-rose-500 font-medium ml-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="genre_tags" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">Tag / Genre</label>
                    <input type="text" name="genre_tags" id="genre_tags" value="{{ old('genre_tags') }}"
                        class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 px-4 py-3 text-slate-600 placeholder:text-slate-300"
                        placeholder="Contoh: Inspiratif, Sejarah, Teknologi">
                    <p class="mt-2 text-[10px] text-slate-400 ml-1">Pisahkan tag dengan koma agar tampil sebagai genre tambahan di katalog.</p>
                    @error('genre_tags') <p class="mt-2 text-xs text-rose-500 font-medium ml-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">Deskripsi / Sinopsis</label>
                    <textarea name="description" id="description" rows="5" 
                        class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 px-4 py-3 text-slate-600 placeholder:text-slate-300"
                        placeholder="Berikan ringkasan atau deskripsi buku...">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-2 text-xs text-rose-500 font-medium ml-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 mt-10 pt-6 border-t border-slate-50">
                <a href="{{ route('admin.books.index') }}" class="px-8 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl font-bold transition-all duration-300">
                    Batal
                </a>
                <button type="submit" :disabled="submitting" :class="submitting ? 'opacity-70 cursor-wait' : ''" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold shadow-lg shadow-indigo-600/20 transition-all duration-300">
                    <span x-show="!submitting">Simpan Buku</span>
                    <span x-show="submitting" x-cloak>Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
