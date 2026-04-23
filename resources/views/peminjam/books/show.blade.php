<x-peminjam-layout page-title="Detail Buku">
    <div class="mb-8">
        <a href="{{ route('peminjam.books.index') }}" class="inline-flex items-center text-sm font-semibold text-slate-400 hover:text-indigo-600 transition-colors mb-4 group">
            <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
            Kembali ke Katalog
        </a>
        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Informasi Buku</h2>
        <p class="text-sm text-slate-500 mt-1">Lihat detail buku sebelum melakukan peminjaman.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6">
                <div class="overflow-hidden rounded-2xl bg-slate-100 p-4 flex items-center justify-center">
                    @if($book->cover_image)
                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-[420px] object-contain">
                    @else
                        <div class="w-full h-[420px] flex items-center justify-center text-slate-300">
                            <i class="fas fa-image text-5xl"></i>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="lg:col-span-3">
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 h-full">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h3 class="text-3xl font-black text-slate-800 leading-tight">{{ $book->title }}</h3>
                        <p class="text-lg text-slate-500 mt-2 italic">{{ $book->author }}</p>
                    </div>
                    @if($book->stock > 0)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tighter bg-emerald-50 text-emerald-600 border border-emerald-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-2"></span>
                            Tersedia
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tighter bg-rose-50 text-rose-600 border border-rose-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-2"></span>
                            Habis
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-8">
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/50 px-4 py-3">
                        <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1">ISBN</p>
                        <p class="text-sm font-semibold text-slate-700">{{ $book->isbn }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/50 px-4 py-3">
                        <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1">Tahun Terbit</p>
                        <p class="text-sm font-semibold text-slate-700">{{ $book->published_year }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/50 px-4 py-3">
                        <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1">Kategori</p>
                        <p class="text-sm font-semibold text-slate-700">{{ $book->category->name ?? '-' }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/50 px-4 py-3">
                        <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1">Stok Tersisa</p>
                        <p class="text-sm font-semibold {{ $book->stock > 0 ? 'text-slate-700' : 'text-rose-500' }}">{{ $book->stock }} Buku</p>
                    </div>
                </div>

                <div class="mt-8">
                    <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-2">Deskripsi</p>
                    <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">
                        {{ $book->description ?: 'Belum ada deskripsi buku.' }}
                    </p>
                </div>

                <!-- Borrow Form -->
                <div class="mt-10 pt-10 border-t border-slate-100">
                    @if($book->stock > 0)
                        <h4 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
                            <div class="w-8 h-8 bg-indigo-50 text-indigo-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-file-signature text-sm"></i>
                            </div>
                            Form Peminjaman
                        </h4>
                        
                        <form action="{{ route('peminjam.borrowings.store', $book) }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label class="text-xs font-bold uppercase tracking-widest text-slate-400 ml-1">Jumlah Pinjam</label>
                                    <input type="number" name="quantity" value="1" min="1" max="{{ $book->stock }}" required class="mt-2 block w-full px-4 py-3 rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/10 transition-all text-sm" placeholder="Max {{ $book->stock }}">
                                </div>
                                <div>
                                    <label class="text-xs font-bold uppercase tracking-widest text-slate-400 ml-1">Alasan Meminjam</label>
                                    <input type="text" name="borrow_reason" required class="mt-2 block w-full px-4 py-3 rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/10 transition-all text-sm" placeholder="Contoh: Tugas Kuliah">
                                </div>
                                <div>
                                    <label class="text-xs font-bold uppercase tracking-widest text-slate-400 ml-1">Tanggal Mulai Pinjam</label>
                                    <input type="date" name="borrow_date" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required class="mt-2 block w-full px-4 py-3 rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/10 transition-all text-sm">
                                </div>
                                <div>
                                    <label class="text-xs font-bold uppercase tracking-widest text-slate-400 ml-1">Rencana Kembali</label>
                                    <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+7 days')) }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required class="mt-2 block w-full px-4 py-3 rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/10 transition-all text-sm">
                                </div>
                            </div>
                            
                            <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold shadow-lg shadow-indigo-600/20 transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]">
                                <i class="fas fa-book-reader mr-2"></i>
                                Ajukan Peminjaman Sekarang
                            </button>
                        </form>
                    @else
                        <div class="p-6 bg-rose-50 rounded-2xl border border-rose-100 flex items-center text-rose-600">
                            <i class="fas fa-exclamation-triangle mr-4 text-xl"></i>
                            <div>
                                <p class="font-bold">Maaf, Stok Habis</p>
                                <p class="text-sm opacity-80">Buku ini sedang tidak tersedia untuk dipinjam saat ini.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-peminjam-layout>
