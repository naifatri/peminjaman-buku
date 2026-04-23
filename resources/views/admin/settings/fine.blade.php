@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Pengaturan Denda</h2>
    <p class="text-sm text-slate-500 mt-1">Atur parameter denda keterlambatan sistem secara global.</p>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-50 bg-slate-50/30">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-sm">
                    <i class="fas fa-coins text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Tarif Denda Keterlambatan</h3>
                    <p class="text-xs text-slate-400 uppercase tracking-widest font-bold mt-0.5">Berlaku untuk semua peminjaman</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.settings.fine.update') }}" method="POST" class="p-8">
            @csrf
            @method('PATCH')
            
            <div class="space-y-6">
                <div>
                    <label for="late_fee_per_day" class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Denda Per Hari (Rupiah)</label>
                    <div class="relative mt-2">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-slate-400 font-bold text-sm">Rp</span>
                        </div>
                        <input type="number" name="late_fee_per_day" id="late_fee_per_day" 
                            value="{{ old('late_fee_per_day', (int)$setting->late_fee_per_day) }}" 
                            class="block w-full pl-12 pr-4 py-4 rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all text-lg font-black text-slate-700 shadow-sm"
                            placeholder="5000">
                    </div>
                    @error('late_fee_per_day')
                        <p class="text-rose-500 text-xs mt-2 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100 flex items-start gap-4">
                    <i class="fas fa-info-circle text-amber-500 mt-1"></i>
                    <p class="text-xs text-amber-700 leading-relaxed">
                        Perubahan tarif ini akan digunakan untuk menghitung denda pada pengembalian buku di masa mendatang. Data denda yang sudah tercatat sebelumnya tidak akan berubah secara otomatis.
                    </p>
                </div>
            </div>

            <div class="mt-10">
                <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold shadow-lg shadow-indigo-200 transition-all transform hover:scale-[1.01] active:scale-[0.99]">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
