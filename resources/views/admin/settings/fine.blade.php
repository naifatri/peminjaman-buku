@extends('layouts.admin')

@php
    $historyItems = ($setting->histories ?? collect())->take(8);
@endphp

@section('content')
<div
    class="space-y-8"
    x-data="fineSettingsForm({
        lateFeePerDay: @js((int) old('late_fee_per_day', $setting->late_fee_per_day)),
        maxFineAmount: @js(old('max_fine_amount', $setting->max_fine_amount !== null ? (int) $setting->max_fine_amount : null)),
        gracePeriodDays: @js((int) old('grace_period_days', $setting->grace_period_days)),
        defaultLoanDurationDays: @js((int) old('default_loan_duration_days', $setting->default_loan_duration_days)),
    })"
>
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Pengaturan Denda</h2>
            <p class="mt-1 text-sm text-slate-500">Kelola rule denda global, preview simulasi, dan riwayat perubahan konfigurasi.</p>
        </div>
        <div class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-4 py-2 text-xs font-bold uppercase tracking-[0.2em] text-white">
            <i class="fas fa-sliders"></i>
            <span>Rule Aktif</span>
        </div>
    </div>

    @if(!empty($needsMigration))
        <div class="rounded-[1.5rem] border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-800">
            Beberapa tabel/kolom baru untuk pengaturan denda belum tersedia. Halaman tetap dibuka dengan mode kompatibilitas, tetapi fitur history dan penyimpanan penuh memerlukan `php artisan migrate`.
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-4">
        <div class="rounded-[2rem] border border-indigo-100 bg-indigo-50/80 p-6 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-indigo-500">Tarif Per Hari</p>
            <p class="mt-4 text-2xl font-black text-indigo-700" x-text="formatCurrency(lateFeePerDay)"></p>
        </div>
        <div class="rounded-[2rem] border border-rose-100 bg-rose-50/80 p-6 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-rose-500">Maksimal Denda</p>
            <p class="mt-4 text-2xl font-black text-rose-700" x-text="maxFineAmount ? formatCurrency(maxFineAmount) : 'Tanpa Batas'"></p>
        </div>
        <div class="rounded-[2rem] border border-amber-100 bg-amber-50/80 p-6 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-amber-500">Grace Period</p>
            <p class="mt-4 text-2xl font-black text-amber-700"><span x-text="gracePeriodDays"></span> hari</p>
        </div>
        <div class="rounded-[2rem] border border-emerald-100 bg-emerald-50/80 p-6 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-emerald-500">Durasi Pinjam Default</p>
            <p class="mt-4 text-2xl font-black text-emerald-700"><span x-text="defaultLoanDurationDays"></span> hari</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="rounded-[2rem] border border-slate-100 bg-white p-8 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Konfigurasi</p>
                    <h3 class="mt-2 text-xl font-black text-slate-800">Atur aturan denda global</h3>
                </div>
                <div class="rounded-2xl bg-slate-100 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">
                    Berlaku untuk transaksi baru
                </div>
            </div>

            <form action="{{ route('admin.settings.fine.update') }}" method="POST" class="mt-8 space-y-6" @submit.prevent="openConfirm($event)">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <div class="flex items-center gap-2">
                            <label for="late_fee_per_day_display" class="ml-1 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Denda Per Hari</label>
                            <span title="Tarif yang dikenakan untuk setiap hari keterlambatan setelah grace period." class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-slate-100 text-[10px] font-bold text-slate-500">i</span>
                        </div>
                        <div class="relative mt-2">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-bold text-slate-400">Rp</span>
                            <input id="late_fee_per_day_display" type="text" inputmode="numeric" x-model="lateFeeDisplay" @input="syncCurrency('lateFeePerDay', 'lateFeeDisplay')" class="block w-full rounded-2xl border-slate-200 py-4 pl-12 pr-4 text-lg font-black text-slate-700 shadow-sm transition-all focus:border-indigo-500 focus:ring-indigo-500/10">
                            <input type="hidden" name="late_fee_per_day" :value="lateFeePerDay">
                        </div>
                        @error('late_fee_per_day')
                            <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center gap-2">
                            <label for="max_fine_amount_display" class="ml-1 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Maksimal Denda</label>
                            <span title="Batas maksimum akumulasi denda keterlambatan per transaksi." class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-slate-100 text-[10px] font-bold text-slate-500">i</span>
                        </div>
                        <div class="relative mt-2">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-bold text-slate-400">Rp</span>
                            <input id="max_fine_amount_display" type="text" inputmode="numeric" x-model="maxFineDisplay" @input="syncCurrency('maxFineAmount', 'maxFineDisplay', true)" class="block w-full rounded-2xl border-slate-200 py-4 pl-12 pr-4 text-lg font-black text-slate-700 shadow-sm transition-all focus:border-indigo-500 focus:ring-indigo-500/10" placeholder="50000">
                            <input type="hidden" name="max_fine_amount" :value="maxFineAmount">
                        </div>
                        <p class="mt-2 text-xs text-slate-400">Kosongkan jika tidak ingin memberi batas maksimal.</p>
                        @error('max_fine_amount')
                            <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center gap-2">
                            <label for="grace_period_days" class="ml-1 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Grace Period</label>
                            <span title="Hari toleransi awal yang tidak dikenai denda walau melewati jatuh tempo." class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-slate-100 text-[10px] font-bold text-slate-500">i</span>
                        </div>
                        <input id="grace_period_days" type="number" min="0" name="grace_period_days" x-model.number="gracePeriodDays" class="mt-2 block w-full rounded-2xl border-slate-200 px-4 py-4 text-lg font-black text-slate-700 shadow-sm transition-all focus:border-indigo-500 focus:ring-indigo-500/10">
                        @error('grace_period_days')
                            <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center gap-2">
                            <label for="default_loan_duration_days" class="ml-1 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Durasi Pinjam Default</label>
                            <span title="Dipakai untuk menentukan tanggal jatuh tempo otomatis saat transaksi baru dibuat." class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-slate-100 text-[10px] font-bold text-slate-500">i</span>
                        </div>
                        <input id="default_loan_duration_days" type="number" min="1" name="default_loan_duration_days" x-model.number="defaultLoanDurationDays" class="mt-2 block w-full rounded-2xl border-slate-200 px-4 py-4 text-lg font-black text-slate-700 shadow-sm transition-all focus:border-indigo-500 focus:ring-indigo-500/10">
                        @error('default_loan_duration_days')
                            <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="rounded-[1.5rem] border border-amber-100 bg-amber-50 p-5">
                    <p class="text-sm font-semibold text-amber-800">Rumus aktif</p>
                    <p class="mt-2 text-sm leading-7 text-amber-700">
                        (<span x-text="previewLateDays"></span> hari keterlambatan - <span x-text="gracePeriodDays"></span> hari grace period) x
                        <span x-text="formatCurrency(lateFeePerDay)"></span>
                        dengan batas maksimum
                        <span x-text="maxFineAmount ? formatCurrency(maxFineAmount) : 'tanpa batas'"></span>.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="resetToInitial()" class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-600 transition-all hover:bg-slate-200">
                        Reset
                    </button>
                    <button type="submit" class="rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition-all hover:bg-indigo-700" :disabled="saving">
                        <span x-show="!saving">Simpan Perubahan</span>
                        <span x-show="saving" x-cloak>Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>

        <div class="space-y-8">
            <div class="rounded-[2rem] border border-slate-100 bg-white p-8 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Preview</p>
                        <h3 class="mt-2 text-xl font-black text-slate-800">Simulasi realtime</h3>
                    </div>
                    <select x-model.number="previewLateDays" class="rounded-2xl border-slate-200 px-4 py-2 text-sm text-slate-600 focus:border-indigo-500 focus:ring-indigo-500/10">
                        <option value="1">1 hari</option>
                        <option value="2">2 hari</option>
                        <option value="3">3 hari</option>
                        <option value="7">7 hari</option>
                        <option value="14">14 hari</option>
                        <option value="30">30 hari</option>
                    </select>
                </div>

                <div class="mt-6 rounded-[1.75rem] border border-slate-100 bg-slate-50 p-6">
                    <div class="flex items-center justify-between gap-4 text-sm">
                        <span class="text-slate-500">Hari terlambat</span>
                        <span class="font-bold text-slate-800"><span x-text="previewLateDays"></span> hari</span>
                    </div>
                    <div class="mt-4 flex items-center justify-between gap-4 text-sm">
                        <span class="text-slate-500">Hari kena denda</span>
                        <span class="font-bold text-slate-800"><span x-text="chargedDays"></span> hari</span>
                    </div>
                    <div class="mt-4 flex items-center justify-between gap-4 text-sm">
                        <span class="text-slate-500">Denda sebelum cap</span>
                        <span class="font-bold text-slate-800" x-text="formatCurrency(beforeCap)"></span>
                    </div>
                    <div class="mt-4 border-t border-slate-200 pt-4">
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-sm font-bold text-slate-800">Total denda</span>
                            <span class="text-xl font-black text-rose-600" x-text="formatCurrency(totalFine)"></span>
                        </div>
                        <p class="mt-2 text-xs text-slate-500" x-show="isCapped" x-cloak>
                            Nilai dibatasi oleh maksimal denda.
                        </p>
                    </div>
                </div>

                <div class="mt-6 overflow-hidden rounded-[1.5rem] border border-slate-100">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Terlambat</th>
                                <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Kena Denda</th>
                                <th class="px-4 py-3 text-right text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($previewRows as $row)
                                <tr>
                                    <td class="px-4 py-3 text-slate-700">{{ $row['late_days'] }} hari</td>
                                    <td class="px-4 py-3 text-slate-500">{{ $row['charged_days'] }} hari</td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-800">Rp {{ number_format($row['total'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-[2rem] border border-slate-100 bg-white p-8 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Riwayat Perubahan</p>
                <div class="mt-6 space-y-4">
                    @forelse($historyItems as $history)
                        <div class="rounded-[1.5rem] border border-slate-100 bg-slate-50 p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-bold text-slate-800">{{ $history->admin?->name ?? 'Admin tidak diketahui' }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $history->changed_at?->translatedFormat('d M Y H:i') }}</p>
                                </div>
                                <span class="rounded-full bg-indigo-50 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.18em] text-indigo-600">Update</span>
                            </div>
                            <div class="mt-4 space-y-2 text-sm">
                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-slate-500">Tarif per hari</span>
                                    <span class="font-semibold text-slate-800">
                                        Rp {{ number_format((float) $history->old_late_fee_per_day, 0, ',', '.') }}
                                        →
                                        Rp {{ number_format((float) $history->new_late_fee_per_day, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-slate-500">Maksimal denda</span>
                                    <span class="font-semibold text-slate-800">
                                        {{ $history->old_max_fine_amount ? 'Rp ' . number_format((float) $history->old_max_fine_amount, 0, ',', '.') : 'Tanpa batas' }}
                                        →
                                        {{ $history->new_max_fine_amount ? 'Rp ' . number_format((float) $history->new_max_fine_amount, 0, ',', '.') : 'Tanpa batas' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-slate-500">Grace period</span>
                                    <span class="font-semibold text-slate-800">{{ (int) $history->old_grace_period_days }} → {{ (int) $history->new_grace_period_days }} hari</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[1.5rem] border border-dashed border-slate-200 bg-slate-50 p-5 text-sm text-slate-500">
                            Belum ada riwayat perubahan pengaturan denda.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div x-show="confirmOpen" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center px-4">
        <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm" @click="confirmOpen = false"></div>
        <div class="relative w-full max-w-lg rounded-[2rem] bg-white p-8 shadow-2xl">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-100 text-amber-600">
                <i class="fas fa-circle-exclamation text-xl"></i>
            </div>
            <h3 class="mt-6 text-2xl font-black text-slate-800">Konfirmasi Perubahan</h3>
            <p class="mt-3 text-sm leading-7 text-slate-500">
                Perubahan tarif hanya berlaku untuk transaksi baru. Lanjutkan?
            </p>
            <div class="mt-8 flex gap-3">
                <button type="button" @click="confirmOpen = false" class="flex-1 rounded-2xl bg-slate-100 px-4 py-3 font-bold text-slate-600 transition-all hover:bg-slate-200">
                    Batal
                </button>
                <button type="button" @click="submitConfirmed($el)" class="flex-1 rounded-2xl bg-indigo-600 px-4 py-3 font-bold text-white transition-all hover:bg-indigo-700" :disabled="saving">
                    <span x-show="!saving">Lanjutkan Simpan</span>
                    <span x-show="saving" x-cloak>Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function fineSettingsForm(initial) {
        return {
            lateFeePerDay: initial.lateFeePerDay,
            maxFineAmount: initial.maxFineAmount,
            gracePeriodDays: initial.gracePeriodDays,
            defaultLoanDurationDays: initial.defaultLoanDurationDays,
            lateFeeDisplay: '',
            maxFineDisplay: '',
            previewLateDays: 7,
            confirmOpen: false,
            saving: false,
            form: null,
            init() {
                this.lateFeeDisplay = this.formatPlainNumber(this.lateFeePerDay);
                this.maxFineDisplay = this.maxFineAmount ? this.formatPlainNumber(this.maxFineAmount) : '';
            },
            formatCurrency(value) {
                const amount = Number(value || 0);
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    maximumFractionDigits: 0,
                }).format(amount);
            },
            formatPlainNumber(value) {
                return new Intl.NumberFormat('id-ID', {
                    maximumFractionDigits: 0,
                }).format(Number(value || 0));
            },
            parseNumber(value) {
                const normalized = String(value ?? '').replace(/[^\d]/g, '');
                return normalized === '' ? null : Number(normalized);
            },
            syncCurrency(target, display, nullable = false) {
                const parsed = this.parseNumber(this[display]);

                if (nullable && parsed === null) {
                    this[target] = null;
                    this[display] = '';
                    return;
                }

                this[target] = parsed ?? 0;
                this[display] = parsed === null ? '' : this.formatPlainNumber(parsed);
            },
            get chargedDays() {
                return Math.max(0, Number(this.previewLateDays) - Number(this.gracePeriodDays || 0));
            },
            get beforeCap() {
                return this.chargedDays * Number(this.lateFeePerDay || 0);
            },
            get totalFine() {
                if (! this.maxFineAmount) {
                    return this.beforeCap;
                }

                return Math.min(this.beforeCap, Number(this.maxFineAmount));
            },
            get isCapped() {
                return Boolean(this.maxFineAmount) && this.beforeCap > this.totalFine;
            },
            openConfirm(event) {
                this.form = event.target;
                this.confirmOpen = true;
            },
            submitConfirmed(button) {
                if (! this.form) {
                    return;
                }

                this.saving = true;
                this.confirmOpen = false;
                this.form.submit();
            },
            resetToInitial() {
                this.lateFeePerDay = initial.lateFeePerDay;
                this.maxFineAmount = initial.maxFineAmount;
                this.gracePeriodDays = initial.gracePeriodDays;
                this.defaultLoanDurationDays = initial.defaultLoanDurationDays;
                this.lateFeeDisplay = this.formatPlainNumber(this.lateFeePerDay);
                this.maxFineDisplay = this.maxFineAmount ? this.formatPlainNumber(this.maxFineAmount) : '';
            },
        };
    }
</script>
@endsection
