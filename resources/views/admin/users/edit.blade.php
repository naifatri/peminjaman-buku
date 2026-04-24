@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-sm font-semibold text-slate-400 hover:text-indigo-600 transition-colors mb-4 group">
        <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
        Kembali ke Daftar
    </a>
    <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Edit Profil Pengguna</h2>
    <p class="text-sm text-slate-500 mt-1">Perbarui informasi akun dan hak akses pengguna.</p>
</div>

<div class="max-w-4xl">
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" x-data="{ submitting: false }" @submit="submitting = true">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                        class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 px-4 py-3 text-slate-600 placeholder:text-slate-300" required>
                    @error('name') <p class="mt-2 text-xs text-rose-500 font-medium ml-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">Alamat Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                        class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 px-4 py-3 text-slate-600 placeholder:text-slate-300" required>
                    @error('email') <p class="mt-2 text-xs text-rose-500 font-medium ml-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="nisn" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">NISN</label>
                    <input type="text" name="nisn" id="nisn" value="{{ old('nisn', $user->nisn) }}"
                        class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 px-4 py-3 text-slate-600 placeholder:text-slate-300"
                        placeholder="Masukkan 10 digit NISN" maxlength="10" inputmode="numeric" required>
                    @error('nisn') <p class="mt-2 text-xs text-rose-500 font-medium ml-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">Password Baru (Opsional)</label>
                    <input type="password" name="password" id="password" 
                        class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 px-4 py-3 text-slate-600 placeholder:text-slate-300" 
                        placeholder="Minimal 8 karakter">
                    <p class="text-[10px] text-slate-400 mt-2 ml-1 italic">* Biarkan kosong jika tidak ingin mengubah password.</p>
                    @error('password') <p class="mt-2 text-xs text-rose-500 font-medium ml-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" 
                        class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 px-4 py-3 text-slate-600 placeholder:text-slate-300" 
                        placeholder="Ulangi password baru">
                </div>

                <div>
                    <label for="role" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">Role Akses</label>
                    <select name="role" id="role" 
                        class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 px-4 py-3 text-slate-600 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat" required>
                        <option value="peminjam" {{ old('role', $user->role) == 'peminjam' ? 'selected' : '' }}>Peminjam</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @error('role') <p class="mt-2 text-xs text-rose-500 font-medium ml-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="account_status" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">Status Akun</label>
                    <select name="account_status" id="account_status"
                        class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 px-4 py-3 text-slate-600 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat" required>
                        <option value="aktif" {{ old('account_status', $user->account_status ?? 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('account_status', $user->account_status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('account_status') <p class="mt-2 text-xs text-rose-500 font-medium ml-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">Nomor Telepon</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" 
                        class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 px-4 py-3 text-slate-600" required>
                    @error('phone') <p class="mt-2 text-xs text-rose-500 font-medium ml-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="address" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">Alamat Lengkap</label>
                    <textarea name="address" id="address" rows="3" 
                        class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-500/10 transition-all duration-300 px-4 py-3 text-slate-600 placeholder:text-slate-300">{{ old('address', $user->address) }}</textarea>
                    @error('address') <p class="mt-2 text-xs text-rose-500 font-medium ml-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 mt-10 pt-6 border-t border-slate-50">
                <a href="{{ route('admin.users.index') }}" class="px-8 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl font-bold transition-all duration-300">
                    Batal
                </a>
                <button type="submit" :disabled="submitting" :class="submitting ? 'opacity-70 cursor-wait' : ''" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold shadow-lg shadow-indigo-600/20 transition-all duration-300">
                    <span x-show="!submitting">Simpan Perubahan</span>
                    <span x-show="submitting" x-cloak>Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
