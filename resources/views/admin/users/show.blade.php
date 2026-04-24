@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8 flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
        <div class="min-w-0">
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-sm font-semibold text-slate-400 hover:text-indigo-600 transition-colors mb-4 group">
                <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
                Kembali ke Daftar Pengguna
            </a>
            <h2 class="text-2xl md:text-3xl font-bold text-slate-800 tracking-tight break-words">{{ $user->name }}</h2>
            <p class="text-sm text-slate-500 mt-2 max-w-3xl">Detail akun, status akses, kontak, dan aktivitas peminjaman terbaru pengguna.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-all duration-300">
                <i class="fas fa-pen mr-2"></i>
                Edit Pengguna
            </a>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-5 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-lg shadow-indigo-600/20 transition-all duration-300">
                <i class="fas fa-table mr-2"></i>
                Kembali ke Tabel
            </a>
        </div>
    </div>

    @php
        $roleClass = $user->role === 'admin'
            ? 'bg-indigo-50 text-indigo-700 border-indigo-200'
            : 'bg-sky-50 text-sky-700 border-sky-200';
        $statusClass = ($user->account_status ?? 'aktif') === 'aktif'
            ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
            : 'bg-rose-50 text-rose-700 border-rose-200';
    @endphp

    <div class="grid grid-cols-1 2xl:grid-cols-[360px_minmax(0,1fr)] gap-8 items-start">
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6 2xl:sticky 2xl:top-28">
            <div class="rounded-[1.75rem] bg-gradient-to-br from-slate-900 via-indigo-700 to-cyan-500 p-8 text-white">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-full border-4 border-white/70 bg-white/20 text-3xl font-black">
                        @if($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                        @else
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        @endif
                    </div>
                    <span class="inline-flex items-center rounded-full border border-white/30 bg-white/10 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.2em]">
                        {{ $user->role === 'admin' ? 'Administrator' : 'Peminjam' }}
                    </span>
                </div>
                <h3 class="mt-6 text-2xl font-black break-words">{{ $user->name }}</h3>
                <p class="mt-2 text-sm text-white/80 break-all">{{ $user->email }}</p>
            </div>

            <div class="mt-6 space-y-4">
                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-[0.18em] border {{ $roleClass }}">
                        {{ $user->role === 'admin' ? 'Admin' : 'Peminjam' }}
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-[0.18em] border {{ $statusClass }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-current mr-2"></span>
                        {{ ($user->account_status ?? 'aktif') === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Total Pinjam</p>
                        <p class="mt-2 text-2xl font-black text-slate-800">{{ $user->borrowings_count }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Pinjam Aktif</p>
                        <p class="mt-2 text-2xl font-black text-slate-800">{{ $userStats['active_borrowings'] }}</p>
                    </div>
                </div>

                <div class="rounded-[1.5rem] border border-slate-100 bg-slate-50/80 p-5 space-y-4">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">NISN</p>
                        <p class="mt-2 text-sm font-semibold text-slate-700">{{ $user->nisn ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Nomor Telepon</p>
                        <p class="mt-2 text-sm font-semibold text-slate-700">{{ $user->phone ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Tanggal Daftar</p>
                        <p class="mt-2 text-sm font-semibold text-slate-700">{{ $user->created_at?->format('d M Y H:i') ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Terakhir Login</p>
                        <p class="mt-2 text-sm font-semibold text-slate-700">{{ $user->last_login_at?->format('d M Y H:i') ?: 'Belum pernah login' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Alamat</p>
                        <p class="mt-2 text-sm leading-6 text-slate-700">{{ $user->address ?: 'Belum ada alamat tersimpan.' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-8 min-w-0">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Total Transaksi</p>
                    <p class="mt-3 text-4xl font-black text-slate-800">{{ $user->borrowings_count }}</p>
                    <p class="mt-3 text-sm text-slate-500">Semua riwayat peminjaman pengguna.</p>
                </div>
                <div class="bg-white rounded-[2rem] border border-amber-100 shadow-sm p-6">
                    <p class="text-xs font-bold uppercase tracking-widest text-amber-500">Pinjaman Aktif</p>
                    <p class="mt-3 text-4xl font-black text-amber-600">{{ $userStats['active_borrowings'] }}</p>
                    <p class="mt-3 text-sm text-slate-500">Masih berjalan atau menunggu proses admin.</p>
                </div>
                <div class="bg-white rounded-[2rem] border border-rose-100 shadow-sm p-6">
                    <p class="text-xs font-bold uppercase tracking-widest text-rose-500">Denda Belum Lunas</p>
                    <p class="mt-3 text-4xl font-black text-rose-600">Rp {{ number_format($userStats['unpaid_fines'], 0, ',', '.') }}</p>
                    <p class="mt-3 text-sm text-slate-500">Akumulasi denda yang masih outstanding.</p>
                </div>
            </div>

            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 md:px-8 py-6 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800">Riwayat Peminjaman Terbaru</h3>
                    <p class="text-sm text-slate-500 mt-1">10 aktivitas terakhir pengguna, lengkap dengan status dan denda.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[920px]">
                        <thead>
                            <tr class="bg-slate-50/70">
                                <th class="px-6 md:px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Buku</th>
                                <th class="px-6 md:px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Tanggal</th>
                                <th class="px-6 md:px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100 text-center">Qty</th>
                                <th class="px-6 md:px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Status</th>
                                <th class="px-6 md:px-8 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Denda</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($user->borrowings as $borrowing)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="px-6 md:px-8 py-5">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-slate-700">{{ $borrowing->book->title ?? '-' }}</span>
                                            <span class="text-xs text-slate-400 mt-1">{{ $borrowing->book->author ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 md:px-8 py-5 text-sm text-slate-500">
                                        <div>{{ $borrowing->borrow_date?->format('d M Y') ?: '-' }}</div>
                                        <div class="mt-1 text-xs text-slate-400">Jatuh tempo {{ $borrowing->due_date?->format('d M Y') ?: '-' }}</div>
                                    </td>
                                    <td class="px-6 md:px-8 py-5 text-center text-sm font-bold text-slate-700">{{ $borrowing->quantity }}</td>
                                    <td class="px-6 md:px-8 py-5">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $borrowing->admin_status_color }}">
                                            {{ $borrowing->admin_status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 md:px-8 py-5">
                                        @if($borrowing->fine)
                                            <div class="flex flex-col gap-2">
                                                <span class="text-sm font-bold text-slate-700">Rp {{ number_format($borrowing->fine->amount, 0, ',', '.') }}</span>
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $borrowing->fine->status_color }}">
                                                    {{ $borrowing->fine->status_label }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-sm text-slate-400">Tidak ada denda</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4">
                                                <i class="fas fa-clock-rotate-left text-2xl text-slate-200"></i>
                                            </div>
                                            <p class="text-slate-400 font-medium">Belum ada riwayat peminjaman untuk pengguna ini.</p>
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
