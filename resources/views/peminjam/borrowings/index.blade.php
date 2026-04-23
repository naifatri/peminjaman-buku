<x-peminjam-layout page-title="Riwayat Peminjaman">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Status Peminjaman</h2>
        <p class="text-sm text-slate-500 mt-1">Pantau seluruh proses peminjamanmu dari pengajuan hingga pengembalian.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-2xl bg-amber-500 text-white shadow-lg shadow-amber-200">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Sedang Dipinjam</p>
                    <p class="text-3xl font-black text-slate-800">{{ $activeCount }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-2xl bg-rose-500 text-white shadow-lg shadow-rose-200">
                    <i class="fas fa-triangle-exclamation text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Terlambat</p>
                    <p class="text-3xl font-black text-slate-800">{{ $lateCount }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-2xl bg-indigo-500 text-white shadow-lg shadow-indigo-200">
                    <i class="fas fa-hourglass-half text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Sedang Proses</p>
                    <p class="text-3xl font-black text-slate-800">{{ $pendingCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden" x-data="{ 
        returnModal: false, 
        payModal: false,
        activeBorrowing: null,
        activeBookTitle: '',
        activeFineAmount: 0,
        selectedPayment: ''
    }">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Buku</th>
                        <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Waktu Pinjam</th>
                        <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Status</th>
                        <th class="px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($borrowings as $borrowing)
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="px-8 py-5">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">{{ $borrowing->book->title }}</span>
                                    <span class="text-xs text-slate-400 mt-1 uppercase tracking-tighter">Batas Kembali: {{ \Carbon\Carbon::parse($borrowing->due_date)->format('d M Y') }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-sm text-slate-500 font-medium">
                                {{ \Carbon\Carbon::parse($borrowing->borrow_date)->format('d M Y') }}
                            </td>
                            <td class="px-8 py-5">
                                @php
                                    $statusClasses = [
                                        'diajukan' => 'bg-slate-50 text-slate-500 border-slate-100',
                                        'dipinjam' => 'bg-amber-50 text-amber-600 border-amber-100',
                                        'terlambat' => 'bg-rose-50 text-rose-600 border-rose-100',
                                        'dikembalikan' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                        'verifikasi_denda' => 'bg-rose-100 text-rose-700 border-rose-200',
                                        'proses_bayar' => 'bg-amber-100 text-amber-700 border-amber-200',
                                        'selesai' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                        'ditolak' => 'bg-slate-200 text-slate-700 border-slate-300',
                                    ];
                                    $statusLabels = [
                                        'diajukan' => 'Menunggu Approval',
                                        'dipinjam' => 'Sedang Dipinjam',
                                        'terlambat' => 'Terlambat',
                                        'dikembalikan' => 'Verifikasi Petugas',
                                        'verifikasi_denda' => 'Menunggu Pembayaran',
                                        'proses_bayar' => 'Verifikasi Bayar',
                                        'selesai' => 'Selesai',
                                        'ditolak' => 'Ditolak',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tighter border {{ $statusClasses[$borrowing->status] ?? 'bg-slate-50 text-slate-500' }}">
                                    @if(in_array($borrowing->status, ['diajukan', 'dikembalikan', 'proses_bayar']))
                                        <span class="w-1.5 h-1.5 rounded-full bg-current mr-2 animate-pulse"></span>
                                    @endif
                                    {{ $statusLabels[$borrowing->status] ?? $borrowing->status }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('peminjam.borrowings.show', $borrowing) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-slate-50 rounded-xl transition-all" title="Lihat Detail">
                                        <i class="fas fa-eye text-lg"></i>
                                    </a>
                                    
                                    @if(in_array($borrowing->status, ['dipinjam', 'terlambat']))
                                        <button @click="returnModal = true; activeBorrowing = {{ $borrowing->id }}; activeBookTitle = '{{ $borrowing->book->title }}'" 
                                            class="px-4 py-2 bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-xl text-xs font-bold transition-all duration-300">
                                            Kembalikan Buku
                                        </button>
                                    @elseif($borrowing->status === 'verifikasi_denda')
                                        <button @click="payModal = true; activeBorrowing = {{ $borrowing->id }}; activeBookTitle = '{{ $borrowing->book->title }}'; activeFineAmount = '{{ number_format($borrowing->fine_amount, 0, ',', '.') }}'; selectedPayment = ''" 
                                            class="px-4 py-2 bg-rose-600 text-white hover:bg-rose-700 rounded-xl text-xs font-bold transition-all duration-300 shadow-lg shadow-rose-200">
                                            Bayar Denda
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4">
                                        <i class="fas fa-exchange-alt text-2xl text-slate-200"></i>
                                    </div>
                                    <p class="text-slate-400 font-medium">Belum ada riwayat peminjaman.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Return Modal -->
        <div x-show="returnModal" class="fixed inset-0 z-[60] flex items-center justify-center px-4" x-cloak>
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="returnModal = false"></div>
            <div class="relative bg-white rounded-[2rem] shadow-2xl w-full max-w-md p-8 overflow-hidden" x-transition>
                <div class="mb-6">
                    <h3 class="text-xl font-bold text-slate-800">Kembalikan Buku</h3>
                    <p class="text-sm text-slate-500 mt-1" x-text="'Anda akan mengembalikan: ' + activeBookTitle"></p>
                </div>
                <form :action="'/peminjam/borrowings/' + activeBorrowing + '/return'" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label class="text-xs font-bold uppercase tracking-widest text-slate-400 ml-1">Kondisi & Catatan Buku</label>
                        <textarea name="return_notes" required class="mt-2 block w-full px-4 py-3 rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/10 transition-all duration-300 text-sm h-32 placeholder:text-slate-300" placeholder="Jelaskan kondisi buku saat ini (Contoh: Mulus, ada coretan, atau halaman robek)"></textarea>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" @click="returnModal = false" class="flex-1 px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl font-bold transition-all">Batal</button>
                        <button type="submit" class="flex-1 px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold shadow-lg shadow-indigo-200 transition-all">Konfirmasi</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Pay Modal -->
        <div x-show="payModal" class="fixed inset-0 z-[60] flex items-center justify-center px-4" x-cloak x-transition>
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="payModal = false"></div>
            <div class="relative bg-white rounded-[2.4rem] shadow-2xl w-full max-w-md p-8 md:p-9 overflow-hidden flex flex-col max-h-[90vh]">
                <div class="mb-7">
                    <div class="w-14 h-14 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center mb-6 shadow-[0_16px_34px_-24px_rgba(244,63,94,0.55)]">
                        <i class="fas fa-wallet text-xl"></i>
                    </div>
                    <h3 class="text-[2.05rem] leading-tight font-black tracking-tight text-slate-800">Pembayaran Denda</h3>
                    <p class="text-[1.05rem] text-slate-500 mt-3" x-text="'Buku: ' + activeBookTitle"></p>
                    <p class="text-[2.35rem] leading-none font-black text-rose-500 mt-5" x-text="'Rp ' + activeFineAmount"></p>
                </div>
                
                <form :action="'/peminjam/borrowings/' + activeBorrowing + '/pay-fine'" method="POST" class="flex-1 overflow-y-auto custom-scrollbar pr-1">
                    @csrf
                    <input type="hidden" name="payment_method" :value="selectedPayment">

                    <div x-show="selectedPayment !== 'qris'" x-transition.opacity.duration.200ms class="mb-8">
                        <label class="text-sm font-black uppercase tracking-[0.22em] text-slate-400 ml-1">Pilih Metode Pembayaran</label>
                        <div class="grid grid-cols-1 gap-3 mt-3">
                            <label class="relative flex items-center p-4 border rounded-[1.4rem] cursor-pointer transition-all duration-300 shadow-sm shadow-slate-100/80" :class="selectedPayment === 'tunai' ? 'border-indigo-400 bg-white ring-2 ring-indigo-400/20' : 'border-slate-200/80 bg-white hover:border-slate-300 hover:bg-slate-50/60'">
                                <input type="radio" name="payment_method_picker" value="tunai" x-model="selectedPayment" class="w-5 h-5 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                                <div class="ml-4">
                                    <span class="block text-[1.05rem] font-black text-slate-700 leading-tight">Bayar Tunai</span>
                                    <span class="block text-[11px] text-slate-400 uppercase tracking-tight">Serahkan ke petugas perpustakaan</span>
                                </div>
                            </label>
                            
                            <label class="relative flex items-center p-4 border rounded-[1.4rem] cursor-pointer transition-all duration-300 shadow-sm shadow-slate-100/80" :class="selectedPayment === 'qris' ? 'border-indigo-500 bg-white ring-2 ring-indigo-500/15 shadow-[0_16px_36px_-28px_rgba(79,70,229,0.7)]' : 'border-slate-200/80 bg-white hover:border-slate-300 hover:bg-slate-50/60'">
                                <input type="radio" name="payment_method_picker" value="qris" x-model="selectedPayment" class="w-5 h-5 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                                <div class="ml-4 flex-1">
                                    <span class="block text-[1.05rem] font-black text-slate-700 leading-tight">QRIS / E-Wallet</span>
                                    <span class="block text-[11px] text-slate-400 uppercase tracking-tight">Scan kode QR yang muncul</span>
                                </div>
                                <div class="w-11 h-11 bg-white rounded-xl shadow-sm border border-slate-100 flex items-center justify-center">
                                    <i class="fas fa-qrcode text-indigo-500 text-lg"></i>
                                </div>
                            </label>

                            <label class="relative flex items-center p-4 border rounded-[1.4rem] cursor-pointer transition-all duration-300 shadow-sm shadow-slate-100/80" :class="selectedPayment === 'ganti_buku' ? 'border-indigo-400 bg-white ring-2 ring-indigo-400/20' : 'border-slate-200/80 bg-white hover:border-slate-300 hover:bg-slate-50/60'">
                                <input type="radio" name="payment_method_picker" value="ganti_buku" x-model="selectedPayment" class="w-5 h-5 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                                <div class="ml-4">
                                    <span class="block text-[1.05rem] font-black text-slate-700 leading-tight">Ganti Buku Baru</span>
                                    <span class="block text-[11px] text-slate-400 uppercase tracking-tight">Membeli buku fisik yang sama</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div x-show="selectedPayment === 'qris'" x-transition.opacity.duration.250ms class="mb-6 rounded-[2.1rem] border border-dashed border-slate-200 bg-gradient-to-b from-slate-50 via-white to-white px-5 py-8 md:px-6 md:py-9 flex flex-col items-center overflow-hidden shadow-[inset_0_1px_0_rgba(255,255,255,0.85)]">
                        <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.34em] text-center leading-[2]">
                            Scan QRIS SIPBUK<br><span class="text-slate-300">Silakan scan dan bayar denda</span>
                        </p>
                        
                        <div class="mt-6 bg-white p-8 rounded-[2rem] shadow-[0_26px_60px_-34px_rgba(37,99,235,0.2)]">
                            <svg width="132" height="132" viewBox="0 0 100 100" class="text-slate-800">
                                    <rect width="100" height="100" fill="white"/>
                                    <rect x="10" y="10" width="25" height="25" fill="currentColor"/>
                                    <rect x="15" y="15" width="15" height="15" fill="white"/>
                                    <rect x="18" y="18" width="9" height="9" fill="currentColor"/>
                                    
                                    <rect x="65" y="10" width="25" height="25" fill="currentColor"/>
                                    <rect x="70" y="15" width="15" height="15" fill="white"/>
                                    <rect x="73" y="18" width="9" height="9" fill="currentColor"/>
                                    
                                    <rect x="10" y="65" width="25" height="25" fill="currentColor"/>
                                    <rect x="15" y="70" width="15" height="15" fill="white"/>
                                    <rect x="18" y="73" width="9" height="9" fill="currentColor"/>
                                    
                                    <rect x="40" y="40" width="20" height="20" fill="currentColor"/>
                                    <rect x="45" y="45" width="10" height="10" fill="white"/>
                                    
                                    <rect x="70" y="70" width="5" height="5" fill="currentColor"/>
                                    <rect x="80" y="80" width="10" height="10" fill="currentColor"/>
                                    <rect x="40" y="10" width="15" height="5" fill="currentColor"/>
                                    <rect x="10" y="40" width="5" height="15" fill="currentColor"/>
                                    <rect x="85" y="40" width="5" height="20" fill="currentColor"/>
                                    <rect x="40" y="85" width="20" height="5" fill="currentColor"/>
                            </svg>
                        </div>
                        
                        <div class="mt-6 inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-indigo-500 to-violet-500 px-4 py-2 text-[10px] font-black uppercase tracking-wide text-white shadow-[0_18px_36px_-22px_rgba(79,70,229,0.95)]">
                            <i class="fas fa-shield-alt text-[11px]"></i>
                            <span>Pembayaran Terenkripsi</span>
                        </div>
                    </div>
                    
                    <div class="flex gap-3 sticky bottom-0 bg-white pt-4">
                        <button type="button" @click="selectedPayment === 'qris' ? selectedPayment = '' : payModal = false" class="flex-1 px-4 py-4 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-[1.45rem] font-black text-[1.05rem] transition-all shadow-[inset_0_1px_0_rgba(255,255,255,0.9)]" x-text="selectedPayment === 'qris' ? 'Kembali' : 'Batal'"></button>
                        <button type="submit" :disabled="!selectedPayment" :class="selectedPayment ? 'bg-gradient-to-r from-indigo-500 to-violet-600 hover:from-indigo-600 hover:to-violet-700 text-white shadow-[0_24px_42px_-24px_rgba(79,70,229,0.95)]' : 'bg-slate-200 text-slate-400 cursor-not-allowed'" class="flex-1 px-4 py-4 rounded-[1.45rem] font-black text-[1.05rem] transition-all" x-text="selectedPayment === 'qris' ? 'Konfirmasi Bayar' : 'Lanjutkan'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="mt-8">
        {{ $borrowings->links() }}
    </div>
</x-peminjam-layout>
