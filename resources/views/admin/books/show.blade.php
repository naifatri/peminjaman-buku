@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8 flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
        <div class="min-w-0">
            <a href="{{ route('admin.books.index') }}" class="inline-flex items-center text-sm font-semibold text-slate-400 hover:text-indigo-600 transition-colors mb-4 group">
                <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
                Kembali ke Daftar Buku
            </a>
            <h2 class="text-2xl md:text-3xl font-bold text-slate-800 tracking-tight break-words">{{ $book->title }}</h2>
            <p class="text-sm text-slate-500 mt-2 max-w-3xl">Detail lengkap buku, lokasi penyimpanan, dan riwayat aktivitas peminjaman terbaru.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.books.edit', $book) }}" class="inline-flex items-center px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-all duration-300">
                <i class="fas fa-pen mr-2"></i>
                Edit Buku
            </a>
            <a href="{{ route('admin.books.index') }}" class="inline-flex items-center px-5 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-lg shadow-indigo-600/20 transition-all duration-300">
                <i class="fas fa-table mr-2"></i>
                Kembali ke Tabel
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 2xl:grid-cols-[380px_minmax(0,1fr)] gap-8 items-start">
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-5 md:p-6 2xl:sticky 2xl:top-28">
            <div class="max-w-[280px] sm:max-w-[320px] mx-auto rounded-[1.75rem] bg-gradient-to-b from-white to-slate-100 border border-slate-100 p-4">
                <div class="aspect-[3/4] rounded-[1.5rem] overflow-hidden bg-slate-50 flex items-center justify-center">
                    @if($book->cover_image)
                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="h-full w-full object-cover object-center">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-300">
                            <i class="fas fa-image text-5xl mb-3"></i>
                            <span class="text-xs font-bold uppercase tracking-widest">No Cover</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-6 space-y-4">
                <div class="flex items-center justify-between gap-3 flex-wrap">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $book->inventory_status_color }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-current mr-2"></span>
                        {{ $book->inventory_status_label }}
                    </span>
                    <span class="text-sm font-black {{ $book->stock > 0 ? 'text-slate-700' : 'text-rose-600' }}">{{ $book->stock }} stok</span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Total Dipinjam</p>
                        <p class="mt-2 text-2xl font-black text-slate-800">{{ $relatedStats['borrowed_count'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Aktif Saat Ini</p>
                        <p class="mt-2 text-2xl font-black text-slate-800">{{ $relatedStats['active_count'] }}</p>
                    </div>
                </div>

                <form action="{{ route('admin.books.update-stock', $book) }}" method="POST" class="rounded-[1.5rem] border border-slate-100 bg-slate-50/70 p-4 md:p-5" x-data="{ submitting: false }" @submit="submitting = true">
                    @csrf
                    @method('PATCH')
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div>
                            <p class="text-sm font-bold text-slate-700">Update Stok Cepat</p>
                            <span class="text-[11px] text-slate-400">Tanpa buka form edit</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <select name="mode" class="rounded-2xl border-slate-200 px-4 py-3 text-sm text-slate-600">
                            <option value="increment">Tambah</option>
                            <option value="decrement">Kurangi</option>
                            <option value="set">Set Langsung</option>
                        </select>
                        <input type="number" name="quantity" min="1" value="1" class="rounded-2xl border-slate-200 px-4 py-3 text-sm text-slate-600">
                    </div>
                    <button type="submit" :disabled="submitting" :class="submitting ? 'opacity-70 cursor-wait' : ''" class="mt-3 w-full px-4 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition-all duration-300">
                        <span x-show="!submitting">Update Stok</span>
                        <span x-show="submitting" x-cloak>Mengupdate...</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="space-y-8 min-w-0">
            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6 md:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-2">Penulis</p>
                    <p class="text-sm font-semibold text-slate-700">{{ $book->author }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-2">ISBN</p>
                    <p class="text-sm font-semibold text-slate-700">{{ $book->isbn ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-2">Kategori</p>
                    <p class="text-sm font-semibold text-slate-700">{{ $book->category->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-2">Penerbit</p>
                    <p class="text-sm font-semibold text-slate-700">{{ $book->publisher ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-2">Tahun Terbit</p>
                    <p class="text-sm font-semibold text-slate-700">{{ $book->published_year ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-2">Jumlah Halaman</p>
                    <p class="text-sm font-semibold text-slate-700">{{ $book->page_count ? $book->page_count . ' hlm' : '-' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-2">Lokasi Rak</p>
                    <p class="text-sm font-semibold text-slate-700">{{ $book->rack_location ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-2">Rating</p>
                    <p class="text-sm font-semibold text-slate-700">{{ $book->rating ? number_format((float) $book->rating, 1) : '-' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-2">Terakhir Diupdate</p>
                    <p class="text-sm font-semibold text-slate-700">{{ $book->updated_at?->format('d M Y H:i') ?: '-' }}</p>
                </div>
            </div>

            <div class="mt-8">
                <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-2">Tag / Genre</p>
                <div class="flex flex-wrap gap-2">
                    @forelse(collect(explode(',', (string) $book->genre_tags))->map(fn($tag) => trim($tag))->filter() as $tag)
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-[11px] font-bold uppercase tracking-wide">{{ $tag }}</span>
                    @empty
                        <span class="text-sm text-slate-400">Belum ada tag tambahan.</span>
                    @endforelse
                </div>
            </div>

                <div class="mt-8">
                    <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-2">Deskripsi</p>
                    <div class="rounded-[1.5rem] bg-slate-50 p-5 text-sm leading-7 text-slate-600 break-words">
                        {{ $book->description ?: 'Belum ada deskripsi untuk buku ini.' }}
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 md:px-8 py-6 border-b border-slate-100 flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Riwayat Peminjaman</h3>
                        <p class="text-sm text-slate-500 mt-1">10 transaksi terbaru untuk buku ini.</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[720px]">
                        <thead>
                            <tr class="bg-slate-50/70">
                                <th class="px-6 md:px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Peminjam</th>
                                <th class="px-6 md:px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Tanggal</th>
                                <th class="px-6 md:px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100 text-center">Qty</th>
                                <th class="px-6 md:px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($book->borrowings as $borrowing)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="px-6 md:px-8 py-5">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-slate-700">{{ $borrowing->user->name ?? '-' }}</span>
                                            <span class="text-xs text-slate-400 mt-1">{{ $borrowing->user->email ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 md:px-8 py-5 text-sm text-slate-500">
                                        {{ \Carbon\Carbon::parse($borrowing->borrow_date)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 md:px-8 py-5 text-center text-sm font-bold text-slate-700">{{ $borrowing->quantity }}</td>
                                    <td class="px-6 md:px-8 py-5">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border
                                            {{ in_array($borrowing->status, ['dipinjam', 'terlambat']) ? 'bg-amber-50 text-amber-700 border-amber-200' : '' }}
                                            {{ in_array($borrowing->status, ['diajukan', 'dikembalikan', 'proses_bayar']) ? 'bg-slate-50 text-slate-600 border-slate-200' : '' }}
                                            {{ in_array($borrowing->status, ['selesai']) ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}
                                            {{ in_array($borrowing->status, ['ditolak', 'verifikasi_denda']) ? 'bg-rose-50 text-rose-700 border-rose-200' : '' }}">
                                            {{ str_replace('_', ' ', $borrowing->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4">
                                                <i class="fas fa-clock-rotate-left text-2xl text-slate-200"></i>
                                            </div>
                                            <p class="text-slate-400 font-medium">Belum ada riwayat peminjaman untuk buku ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
