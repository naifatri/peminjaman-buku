@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Manajemen Transaksi</h2>
    <p class="text-sm text-slate-500 mt-1">Kelola seluruh alur peminjaman dari pengajuan hingga verifikasi denda.</p>
</div>

<!-- Search & Filter -->
<div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 mb-8">
    <form action="{{ route('admin.borrowings.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2 relative">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peminjam atau judul buku..." 
                class="w-full pl-12 pr-4 py-3 rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 text-slate-600 placeholder:text-slate-300">
        </div>

        <select name="status" class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 text-slate-600 px-4 py-3 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat">
            <option value="">Semua Status</option>
            <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
            <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
            <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
            <option value="verifikasi_denda" {{ request('status') == 'verifikasi_denda' ? 'selected' : '' }}>Verifikasi Denda</option>
            <option value="proses_bayar" {{ request('status') == 'proses_bayar' ? 'selected' : '' }}>Proses Bayar</option>
            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
        </select>

        <div class="flex gap-2">
            <button type="submit" class="flex-1 px-4 py-3 bg-slate-800 hover:bg-slate-900 text-white rounded-2xl font-bold transition-all duration-300">
                Filter
            </button>
            <a href="{{ route('admin.borrowings.index') }}" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl font-bold transition-all duration-300 flex items-center justify-center">
                <i class="fas fa-undo"></i>
            </a>
        </div>
    </form>
</div>

<!-- Tabel Peminjaman -->
<div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden" x-data="{ 
    verifyModal: false,
    activeBorrowing: null,
    activeUserName: '',
    activeBookTitle: '',
    userNotes: ''
}">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Peminjam & Buku</th>
                    <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Jatuh Tempo</th>
                    <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Status</th>
                    <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($borrowings as $borrowing)
                <tr class="group hover:bg-slate-50/50 transition-colors">
                    <td class="px-8 py-5">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">{{ $borrowing->user->name }}</span>
                            <span class="text-xs text-slate-400 line-clamp-1 italic mt-0.5">{{ $borrowing->book->title }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex flex-col">
                            <span class="text-sm {{ $borrowing->status === 'terlambat' ? 'text-rose-500 font-bold' : 'text-slate-600 font-medium' }}">
                                {{ \Carbon\Carbon::parse($borrowing->due_date)->format('d M Y') }}
                            </span>
                            @if($borrowing->status === 'dikembalikan')
                                <span class="text-[10px] text-indigo-500 font-bold uppercase tracking-tighter italic">Buku dikembalikan user</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        @php
                            $statusClasses = [
                                'diajukan' => 'bg-slate-100 text-slate-600 border-slate-200',
                                'dipinjam' => 'bg-amber-50 text-amber-600 border-amber-100',
                                'terlambat' => 'bg-rose-50 text-rose-600 border-rose-100',
                                'dikembalikan' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                'verifikasi_denda' => 'bg-rose-100 text-rose-700 border-rose-200',
                                'proses_bayar' => 'bg-amber-100 text-amber-700 border-amber-200',
                                'selesai' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                'ditolak' => 'bg-slate-200 text-slate-700 border-slate-300',
                            ];
                            $statusLabels = [
                                'diajukan' => 'Pengajuan',
                                'dipinjam' => 'Dipinjam',
                                'terlambat' => 'Terlambat',
                                'dikembalikan' => 'Menunggu Verif',
                                'verifikasi_denda' => 'Denda User',
                                'proses_bayar' => 'Proses Bayar',
                                'selesai' => 'Selesai',
                                'ditolak' => 'Ditolak',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tighter border {{ $statusClasses[$borrowing->status] ?? 'bg-slate-50 text-slate-500' }}">
                            {{ $statusLabels[$borrowing->status] ?? $borrowing->status }}
                        </span>
                    </td>
                    <td class="px-8 py-5 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.borrowings.show', $borrowing) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-slate-50 rounded-xl transition-all" title="Lihat Detail">
                                <i class="fas fa-eye text-lg"></i>
                            </a>
                            
                            @if($borrowing->status === 'diajukan')
                                <form action="{{ route('admin.borrowings.approve', $borrowing) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-lg text-xs font-bold transition-all">Setuju</button>
                                </form>
                                <form action="{{ route('admin.borrowings.reject', $borrowing) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-lg text-xs font-bold transition-all">Tolak</button>
                                </form>
                            @elseif($borrowing->status === 'dikembalikan')
                                <button @click="verifyModal = true; activeBorrowing = {{ $borrowing->id }}; activeUserName = '{{ $borrowing->user->name }}'; activeBookTitle = '{{ $borrowing->book->title }}'; userNotes = '{{ $borrowing->return_notes }}'" 
                                    class="px-4 py-2 bg-indigo-600 text-white hover:bg-indigo-700 rounded-xl text-xs font-bold shadow-lg shadow-indigo-200 transition-all">
                                    Verifikasi Buku
                                </button>
                            @elseif($borrowing->status === 'proses_bayar')
                                <form action="{{ route('admin.borrowings.approve-payment', $borrowing) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-amber-600 text-white hover:bg-amber-700 rounded-xl text-xs font-bold shadow-lg shadow-amber-200 transition-all">
                                        @php
                                            $methodLabel = [
                                                'tunai' => 'Tunai',
                                                'ganti_buku' => 'Ganti Buku',
                                                'qris' => 'QRIS'
                                            ];
                                        @endphp
                                        Approve Bayar ({{ $methodLabel[$borrowing->fine->payment_method] ?? $borrowing->fine->payment_method }})
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-8 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-exchange-alt text-4xl text-slate-100 mb-4"></i>
                            <p class="text-slate-400 font-medium">Tidak ada data transaksi ditemukan.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Verify Return Modal -->
    <div x-show="verifyModal" class="fixed inset-0 z-[60] flex items-center justify-center px-4" x-cloak>
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="verifyModal = false"></div>
        <div class="relative bg-white rounded-[2rem] shadow-2xl w-full max-w-lg p-8 overflow-hidden" x-transition>
            <div class="mb-6">
                <h3 class="text-xl font-bold text-slate-800">Verifikasi Pengembalian</h3>
                <p class="text-sm text-slate-500 mt-1" x-text="'Peminjam: ' + activeUserName"></p>
                <p class="text-xs font-bold text-indigo-600 mt-1" x-text="'Buku: ' + activeBookTitle"></p>
            </div>

            <div class="mb-6 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Catatan User:</p>
                <p class="text-sm text-slate-600 italic" x-text="userNotes || 'Tidak ada catatan dari user.'"></p>
            </div>

            <form :action="'/admin/borrowings/' + activeBorrowing + '/verify-return'" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-bold uppercase tracking-widest text-slate-400 ml-1">Kondisi Buku (Hasil Cek)</label>
                        <select name="book_condition" required class="mt-2 block w-full px-4 py-3 rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/10 transition-all text-sm">
                            <option value="baik">Baik / Normal</option>
                            <option value="rusak_ringan">Rusak Ringan</option>
                            <option value="rusak_berat">Rusak Berat</option>
                            <option value="hilang">Hilang</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-bold uppercase tracking-widest text-slate-400 ml-1">Catatan Admin</label>
                        <textarea name="admin_notes" required class="mt-2 block w-full px-4 py-3 rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/10 transition-all text-sm h-24" placeholder="Jelaskan detail pengecekan..."></textarea>
                    </div>
                    <div>
                        <label class="text-xs font-bold uppercase tracking-widest text-slate-400 ml-1">Denda Tambahan (Rp)</label>
                        <input type="number" name="damage_fine" value="0" min="0" class="mt-2 block w-full px-4 py-3 rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/10 transition-all text-sm" placeholder="0">
                        <p class="text-[10px] text-slate-400 mt-1">*Masukkan nominal denda kerusakan/kehilangan di sini.</p>
                    </div>
                </div>

                <div class="flex gap-3 mt-8">
                    <button type="button" @click="verifyModal = false" class="flex-1 px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl font-bold transition-all">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold shadow-lg shadow-indigo-200 transition-all">Simpan Verifikasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="mt-8">
    {{ $borrowings->links() }}
</div>
@endsection
