@extends('layouts.admin')

@section('content')
<div x-data="{
    selectedUsers: [],
    toggleAll(event) {
        this.selectedUsers = event.target.checked ? @js($users->pluck('id')->map(fn ($id) => (string) $id)->values()) : [];
    }
}">
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Manajemen Pengguna</h2>
            <p class="text-sm text-slate-500 mt-1">Kelola akun admin dan peminjam, pantau status, dan akses riwayat peminjaman dengan cepat.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold shadow-lg shadow-indigo-600/20 transition-all duration-300 group">
            <i class="fas fa-user-plus mr-2 group-hover:scale-110 transition-transform"></i>
            Tambah Pengguna
        </a>
    </div>

    <div class="metric-grid grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Total Pengguna</p>
            <p class="mt-3 text-4xl font-black text-slate-800">{{ $stats['total'] }}</p>
            <p class="mt-3 text-sm text-slate-500">Semua akun yang terdaftar di sistem.</p>
        </div>
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Akun Aktif</p>
            <p class="mt-3 text-4xl font-black text-emerald-600">{{ $stats['active'] }}</p>
            <p class="mt-3 text-sm text-slate-500">Dapat login dan menggunakan sistem.</p>
        </div>
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Akun Nonaktif</p>
            <p class="mt-3 text-4xl font-black text-rose-500">{{ $stats['inactive'] }}</p>
            <p class="mt-3 text-sm text-slate-500">Diblokir sementara oleh admin.</p>
        </div>
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Total Peminjam</p>
            <p class="mt-3 text-4xl font-black text-indigo-600">{{ $stats['borrowers'] }}</p>
            <p class="mt-3 text-sm text-slate-500">Pengguna dengan role peminjam.</p>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 mb-8">
        <form action="{{ route('admin.users.index') }}" method="GET" class="responsive-filter-form grid grid-cols-1 md:grid-cols-2 xl:grid-cols-[1.8fr_1fr_1fr_1fr_auto] gap-4" x-data="{ loading: false }" @submit="loading = true">
            <div class="relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, NISN, atau telepon..."
                    class="w-full pl-12 pr-4 py-3 rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 text-slate-600 placeholder:text-slate-300">
            </div>

            <select name="role" class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 text-slate-600 px-4 py-3 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat">
                <option value="">Semua Role</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="peminjam" {{ request('role') == 'peminjam' ? 'selected' : '' }}>Peminjam</option>
            </select>

            <select name="status" class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 text-slate-600 px-4 py-3 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>

            <select name="sort" class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 text-slate-600 px-4 py-3 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat">
                <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>Terbaru Daftar</option>
                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama A-Z</option>
            </select>

            <div class="responsive-filter-actions flex gap-2">
                <button type="submit" :disabled="loading" :class="loading ? 'opacity-70 cursor-wait' : ''" class="flex-1 px-4 py-3 bg-slate-800 hover:bg-slate-900 text-white rounded-2xl font-bold transition-all duration-300">
                    <span x-show="!loading">Filter</span>
                    <span x-show="loading" x-cloak>Memuat...</span>
                </button>
                <a href="{{ route('admin.users.index') }}" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl font-bold transition-all duration-300 flex items-center justify-center" title="Reset">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="responsive-table-card bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Daftar Pengguna</h3>
                <p class="text-sm text-slate-500 mt-1">Gunakan bulk action untuk aktivasi/nonaktifkan atau hapus beberapa akun sekaligus.</p>
            </div>

            <form action="{{ route('admin.users.bulk-action') }}" method="POST" class="responsive-bulk-form grid grid-cols-1 md:grid-cols-[180px_180px_auto] gap-3 items-center" x-data="{ submitting: false }" @submit="if (selectedUsers.length === 0) { alert('Pilih minimal satu pengguna terlebih dahulu.'); $event.preventDefault(); return; } submitting = true;">
                @csrf
                <template x-for="userId in selectedUsers" :key="userId">
                    <input type="hidden" name="user_ids[]" :value="userId">
                </template>

                <select name="bulk_action" class="rounded-2xl border-slate-200 px-4 py-3 text-sm text-slate-600">
                    <option value="activate">Aktifkan</option>
                    <option value="deactivate">Nonaktifkan</option>
                    <option value="change_role">Ubah Role</option>
                    <option value="delete">Hapus Banyak User</option>
                </select>

                <select name="bulk_role" class="rounded-2xl border-slate-200 px-4 py-3 text-sm text-slate-600">
                    <option value="">Pilih role target</option>
                    <option value="admin">Admin</option>
                    <option value="peminjam">Peminjam</option>
                </select>

                <button type="submit" :disabled="submitting || selectedUsers.length === 0" :class="submitting || selectedUsers.length === 0 ? 'opacity-60 cursor-not-allowed' : ''" class="px-5 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition-all duration-300">
                    <span x-show="!submitting">Jalankan Bulk Action</span>
                    <span x-show="submitting" x-cloak>Memproses...</span>
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1280px]">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-6 py-4 border-b border-slate-100">
                            <input type="checkbox" @change="toggleAll($event)" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th class="px-4 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Pengguna</th>
                        <th class="px-4 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">NISN</th>
                        <th class="px-4 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Kontak</th>
                        <th class="px-4 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Role</th>
                        <th class="px-4 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Status</th>
                        <th class="px-4 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Tanggal Daftar</th>
                        <th class="px-4 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">Terakhir Login</th>
                        <th class="px-4 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100 text-center">Total Pinjam</th>
                        <th class="px-4 py-4 text-[11px] font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($users as $user)
                        @php
                            $roleClass = $user->role === 'admin'
                                ? 'bg-indigo-50 text-indigo-700 border-indigo-200'
                                : 'bg-sky-50 text-sky-700 border-sky-200';
                            $statusClass = ($user->account_status ?? 'aktif') === 'aktif'
                                ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                : 'bg-rose-50 text-rose-700 border-rose-200';
                        @endphp
                        <tr class="group hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-5 align-top">
                                <input type="checkbox" value="{{ $user->id }}" x-model="selectedUsers" class="mt-2 w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            </td>
                            <td class="px-4 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-2xl bg-indigo-50 flex items-center justify-center border border-indigo-100 group-hover:bg-indigo-600 group-hover:border-indigo-600 transition-all duration-300">
                                        <span class="text-indigo-600 font-bold group-hover:text-white transition-colors">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.users.show', $user) }}" class="text-sm font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">{{ $user->name }}</a>
                                        <p class="text-xs text-slate-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-5 text-sm font-semibold text-slate-600">
                                @if ($user->nisn)
                                    <span>{{ $user->nisn }}</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-[11px] font-bold text-amber-600">
                                        Belum sinkron
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-5 text-sm text-slate-500">
                                {{ $user->phone ?: '-' }}
                            </td>
                            <td class="px-4 py-5">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tighter border {{ $roleClass }}">
                                    {{ $user->role === 'admin' ? 'Admin' : 'Peminjam' }}
                                </span>
                            </td>
                            <td class="px-4 py-5">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tighter border {{ $statusClass }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current mr-2"></span>
                                    {{ ($user->account_status ?? 'aktif') === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-5 text-sm text-slate-500">
                                {{ $user->created_at?->format('d M Y') ?: '-' }}
                            </td>
                            <td class="px-4 py-5 text-sm text-slate-500">
                                {{ $user->last_login_at?->format('d M Y H:i') ?: 'Belum pernah login' }}
                            </td>
                            <td class="px-4 py-5 text-center">
                                <span class="text-sm font-black text-slate-700">{{ $user->borrowings_count }}</span>
                            </td>
                            <td class="px-4 py-5">
                                <div class="flex justify-end items-center gap-2 flex-wrap">
                                    <a href="{{ route('admin.users.show', $user) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all duration-200" title="Detail Pengguna">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" x-data="{ loading: false }" @submit="loading = true">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" :disabled="loading" class="p-2 {{ ($user->account_status ?? 'aktif') === 'aktif' ? 'text-amber-500 hover:text-amber-700 hover:bg-amber-50' : 'text-emerald-500 hover:text-emerald-700 hover:bg-emerald-50' }} rounded-xl transition-all duration-200" title="{{ ($user->account_status ?? 'aktif') === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <i class="fas {{ ($user->account_status ?? 'aktif') === 'aktif' ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.users.reset-password', $user) }}" method="POST" x-data="{ loading: false }" @submit="if (!confirm('Reset password pengguna ini? Password sementara akan ditampilkan di notifikasi.')) { $event.preventDefault(); return; } loading = true;">
                                            @csrf
                                            <button type="submit" :disabled="loading" class="p-2 text-violet-500 hover:text-violet-700 hover:bg-violet-50 rounded-xl transition-all duration-200" title="Reset Password">
                                                <i class="fas fa-key"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.users.edit', $user) }}" class="p-2 text-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all duration-200" title="Edit Pengguna">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all duration-200" title="Hapus Pengguna">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-8 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4">
                                        <i class="fas fa-users text-2xl text-slate-200"></i>
                                    </div>
                                    <p class="text-slate-500 font-medium">Tidak ada pengguna ditemukan.</p>
                                    <p class="text-sm text-slate-400 mt-2">Coba ubah filter pencarian atau tambahkan pengguna baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-8">
        {{ $users->links() }}
    </div>
</div>
@endsection
