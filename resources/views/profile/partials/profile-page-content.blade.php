@php
    $currentUser = $user ?? auth()->user();
    $isAdmin = $currentUser->role === 'admin';
    $roleLabel = $isAdmin ? 'Administrator' : 'Peminjam';
    $pageDescription = $isAdmin
        ? 'Kelola informasi akun admin, foto profil, dan keamanan akses sistem.'
        : 'Perbarui identitas akun, foto profil, dan keamanan password dengan cepat.';
    $avatarUrl = old('avatar_preview', $currentUser->avatar_url);
    $initial = strtoupper(substr($currentUser->name ?? 'U', 0, 1));
@endphp

<div
    x-data="profilePage({
        initialAvatar: @js($avatarUrl),
        profileUpdated: @js(session('status') === 'profile-updated'),
        passwordUpdated: @js(session('status') === 'password-updated')
    })"
    class="mx-auto max-w-7xl space-y-8"
>
    <div class="relative overflow-hidden rounded-[2rem] border border-slate-200 bg-white px-6 pb-6 pt-20 shadow-sm md:px-8 md:pb-8 md:pt-24">
        <div class="absolute inset-x-0 top-0 h-36 bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-700"></div>
        <div class="relative grid gap-6 xl:grid-cols-[minmax(0,1.3fr)_320px] xl:items-end">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-end">
                <div class="relative">
                    <div class="flex h-28 w-28 items-center justify-center overflow-hidden rounded-full border-4 border-white bg-slate-100 shadow-lg shadow-slate-900/10 md:h-32 md:w-32">
                        <template x-if="avatarPreview">
                            <img :src="avatarPreview" alt="Foto profil" class="h-full w-full object-cover">
                        </template>
                        <template x-if="!avatarPreview">
                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-indigo-500 to-cyan-500 text-4xl font-black text-white">
                                {{ $initial }}
                            </div>
                        </template>
                    </div>
                    <span class="absolute bottom-2 right-1 flex h-7 w-7 items-center justify-center rounded-full border-2 border-white bg-emerald-500 text-[11px] text-white shadow">
                        <i class="fas fa-check"></i>
                    </span>
                </div>

                <div class="space-y-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.3em] text-slate-400">{{ $roleLabel }}</p>
                        <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900">Profil Saya</h1>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">{{ $pageDescription }}</p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <div class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3.5 py-2 text-xs font-semibold text-indigo-700">
                            <i class="fas fa-id-card text-indigo-400"></i>
                            <span>NISN {{ $currentUser->nisn ?? '-' }}</span>
                        </div>
                        <div class="inline-flex max-w-full items-center gap-2 rounded-full bg-slate-100 px-3.5 py-2 text-xs font-semibold text-slate-600">
                            <i class="fas fa-envelope text-slate-400"></i>
                            <span class="truncate">{{ $currentUser->email }}</span>
                        </div>
                        <div class="inline-flex items-center gap-2 rounded-full bg-sky-50 px-3.5 py-2 text-xs font-semibold text-sky-700">
                            <i class="fas fa-phone text-sky-400"></i>
                            <span>{{ $currentUser->phone ?? '-' }}</span>
                        </div>
                        <div class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3.5 py-2 text-xs font-semibold text-emerald-700">
                            <i class="fas fa-shield-alt"></i>
                            <span>Akun aman & terverifikasi</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                    <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-slate-400">Status</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">{{ ucfirst($currentUser->account_status ?? 'aktif') }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                    <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-slate-400">Terakhir Login</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">{{ optional($currentUser->last_login_at)->translatedFormat('d M Y H:i') ?? 'Belum tersedia' }}</p>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="flex items-start gap-3 rounded-3xl border border-rose-200 bg-rose-50 px-5 py-4 text-rose-700 shadow-sm">
            <i class="fas fa-circle-exclamation mt-0.5 text-lg"></i>
            <div>
                <p class="text-sm font-bold">Periksa kembali data yang dimasukkan.</p>
                <p class="mt-1 text-sm">Beberapa field masih perlu diperbaiki sebelum disimpan.</p>
            </div>
        </div>
    @endif

    <div class="grid gap-8 xl:grid-cols-[minmax(0,1.35fr)_minmax(320px,0.75fr)] xl:items-start">
        <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm md:p-8">
            <div class="mb-8 flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-indigo-500">Profile Information</p>
                    <h2 class="mt-2 text-2xl font-black tracking-tight text-slate-900">Informasi Profil</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500">Perbarui nama, email, dan foto profil agar akun mudah dikenali dan tetap up to date.</p>
                </div>
                <div class="hidden h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 sm:flex">
                    <i class="fas fa-user text-lg"></i>
                </div>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6" @submit="profileSubmitting = true">
                @csrf
                @method('PATCH')

                <div class="rounded-[1.75rem] border border-slate-200 bg-slate-50/80 p-5 md:p-6">
                    <div class="grid gap-6 lg:grid-cols-[auto_minmax(0,1fr)] lg:items-center">
                        <div class="mx-auto flex h-24 w-24 items-center justify-center overflow-hidden rounded-full border-4 border-white bg-slate-200 shadow-sm lg:mx-0 lg:h-28 lg:w-28">
                            <template x-if="avatarPreview">
                                <img :src="avatarPreview" alt="Preview avatar" class="h-full w-full object-cover">
                            </template>
                            <template x-if="!avatarPreview">
                                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-indigo-500 to-cyan-500 text-3xl font-black text-white">
                                    {{ $initial }}
                                </div>
                            </template>
                        </div>

                        <div class="min-w-0 text-center lg:text-left">
                            <h3 class="text-base font-bold text-slate-800">Foto Profil</h3>
                            <p class="mt-1 text-sm leading-6 text-slate-500">Gunakan foto berformat JPG, PNG, atau WEBP. Maksimal 2MB.</p>
                            <div class="mt-4 flex flex-wrap justify-center gap-3 lg:justify-start">
                                <label for="avatar" class="inline-flex cursor-pointer items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                                    <i class="fas fa-upload text-xs"></i>
                                    <span x-text="avatarPreview ? 'Ganti Foto' : 'Upload Foto'"></span>
                                </label>
                                <button type="button" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600" @click="removeAvatar()">
                                    <i class="fas fa-trash-can text-xs"></i>
                                    Hapus Foto
                                </button>
                            </div>
                            <input id="avatar" name="avatar" type="file" accept="image/png,image/jpeg,image/jpg,image/webp" class="hidden" @change="previewAvatar">
                            <input type="hidden" name="remove_avatar" :value="avatarMarkedForRemoval ? 1 : 0">
                            @error('avatar')
                                <p class="mt-3 text-sm font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div>
                        <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">Nama</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $currentUser->name) }}" required autocomplete="name" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                        @error('name')
                            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $currentUser->email) }}" required autocomplete="username" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                        <p class="mt-2 text-xs text-slate-400">Gunakan email aktif untuk notifikasi sistem dan peminjaman.</p>
                        @error('email')
                            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nisn" class="mb-2 block text-sm font-semibold text-slate-700">NISN</label>
                        <input id="nisn" name="nisn" type="text" value="{{ old('nisn', $currentUser->nisn) }}" required maxlength="10" inputmode="numeric" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                        @error('nisn')
                            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="mb-2 block text-sm font-semibold text-slate-700">Nomor Telepon</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone', $currentUser->phone) }}" required autocomplete="tel" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                        @error('phone')
                            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col gap-4 border-t border-slate-100 pt-6 lg:flex-row lg:items-center lg:justify-between">
                    <div class="max-w-xl text-sm leading-6 text-slate-500">
                        Pastikan data profil selalu valid agar aktivitas akun lebih aman dan mudah dikelola.
                    </div>
                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center lg:justify-end">
                        <span x-show="profileUpdated" x-transition class="rounded-full bg-emerald-50 px-3 py-2 text-xs font-bold uppercase tracking-[0.2em] text-emerald-600">Perubahan tersimpan</span>
                        <button type="submit" class="inline-flex w-full min-w-[190px] items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-500 disabled:cursor-not-allowed disabled:opacity-70 sm:w-auto" :disabled="profileSubmitting">
                            <i class="fas fa-spinner animate-spin text-xs" x-show="profileSubmitting"></i>
                            <i class="fas fa-save text-xs" x-show="!profileSubmitting"></i>
                            <span x-text="profileSubmitting ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                        </button>
                    </div>
                </div>
            </form>
        </section>

        <div class="space-y-8 xl:sticky xl:top-8">
            <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm md:p-8">
                <div class="mb-8 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.3em] text-amber-500">Security</p>
                        <h2 class="mt-2 text-2xl font-black tracking-tight text-slate-900">Update Password</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500">Gunakan password baru minimal 8 karakter agar akses akun tetap aman.</p>
                    </div>
                    <div class="hidden h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 sm:flex">
                        <i class="fas fa-lock text-lg"></i>
                    </div>
                </div>

                <form method="POST" action="{{ route('password.update') }}" class="space-y-5" @submit="passwordSubmitting = true">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="mb-2 block text-sm font-semibold text-slate-700">Current password</label>
                        <div class="relative">
                            <input id="current_password" name="current_password" :type="showCurrentPassword ? 'text' : 'password'" autocomplete="current-password" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 pr-12 text-sm text-slate-800 shadow-sm outline-none transition focus:border-amber-400 focus:ring-4 focus:ring-amber-100">
                            <button type="button" class="absolute inset-y-0 right-0 flex w-12 items-center justify-center text-slate-400 transition hover:text-slate-600" @click="showCurrentPassword = !showCurrentPassword" :aria-label="showCurrentPassword ? 'Sembunyikan password saat ini' : 'Tampilkan password saat ini'">
                                <i class="fas" :class="showCurrentPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        @if ($errors->updatePassword->has('current_password'))
                            <p class="mt-2 text-sm font-medium text-rose-600">{{ $errors->updatePassword->first('current_password') }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">New password</label>
                        <div class="relative">
                            <input id="password" name="password" :type="showNewPassword ? 'text' : 'password'" minlength="8" autocomplete="new-password" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 pr-12 text-sm text-slate-800 shadow-sm outline-none transition focus:border-amber-400 focus:ring-4 focus:ring-amber-100">
                            <button type="button" class="absolute inset-y-0 right-0 flex w-12 items-center justify-center text-slate-400 transition hover:text-slate-600" @click="showNewPassword = !showNewPassword" :aria-label="showNewPassword ? 'Sembunyikan password baru' : 'Tampilkan password baru'">
                                <i class="fas" :class="showNewPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-slate-400">Minimal 8 karakter, kombinasi huruf dan angka lebih disarankan.</p>
                        @if ($errors->updatePassword->has('password'))
                            <p class="mt-2 text-sm font-medium text-rose-600">{{ $errors->updatePassword->first('password') }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-slate-700">Confirm password</label>
                        <div class="relative">
                            <input id="password_confirmation" name="password_confirmation" :type="showConfirmPassword ? 'text' : 'password'" minlength="8" autocomplete="new-password" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 pr-12 text-sm text-slate-800 shadow-sm outline-none transition focus:border-amber-400 focus:ring-4 focus:ring-amber-100">
                            <button type="button" class="absolute inset-y-0 right-0 flex w-12 items-center justify-center text-slate-400 transition hover:text-slate-600" @click="showConfirmPassword = !showConfirmPassword" :aria-label="showConfirmPassword ? 'Sembunyikan konfirmasi password' : 'Tampilkan konfirmasi password'">
                                <i class="fas" :class="showConfirmPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        @if ($errors->updatePassword->has('password_confirmation'))
                            <p class="mt-2 text-sm font-medium text-rose-600">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                        @endif
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500">
                        Password lama wajib diisi sebelum memperbarui password baru.
                    </div>

                    <div class="flex flex-col gap-3 pt-2">
                        <span x-show="passwordUpdated" x-transition class="rounded-full bg-emerald-50 px-3 py-2 text-xs font-bold uppercase tracking-[0.2em] text-emerald-600">Password berhasil diperbarui</span>
                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-900/10 transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-70 sm:w-auto" :disabled="passwordSubmitting">
                            <i class="fas fa-spinner animate-spin text-xs" x-show="passwordSubmitting"></i>
                            <i class="fas fa-key text-xs" x-show="!passwordSubmitting"></i>
                            <span x-text="passwordSubmitting ? 'Memperbarui...' : 'Update Password'"></span>
                        </button>
                    </div>
                </form>
            </section>

            <section class="rounded-[2rem] border border-slate-200 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-6 text-white shadow-sm md:p-8">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-slate-400">Tips</p>
                <h2 class="mt-2 text-2xl font-black tracking-tight">Profil yang nyaman dipakai</h2>
                <ul class="mt-5 space-y-3 text-sm leading-6 text-slate-300">
                    <li class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-cyan-400"></span>
                        Gunakan foto yang jelas agar identitas akun mudah dikenali.
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-cyan-400"></span>
                        Pastikan email aktif untuk menerima update penting dari sistem.
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-cyan-400"></span>
                        Ganti password secara berkala untuk menjaga keamanan akses.
                    </li>
                </ul>
            </section>
        </div>
    </div>
</div>

<script>
    function profilePage(config) {
        return {
            avatarPreview: config.initialAvatar,
            avatarMarkedForRemoval: false,
            profileSubmitting: false,
            passwordSubmitting: false,
            profileUpdated: config.profileUpdated,
            passwordUpdated: config.passwordUpdated,
            showCurrentPassword: false,
            showNewPassword: false,
            showConfirmPassword: false,
            previewAvatar(event) {
                const file = event.target.files[0];

                if (!file) {
                    return;
                }

                this.avatarMarkedForRemoval = false;
                this.avatarPreview = URL.createObjectURL(file);
            },
            removeAvatar() {
                this.avatarPreview = null;
                this.avatarMarkedForRemoval = true;

                const input = document.getElementById('avatar');
                if (input) {
                    input.value = '';
                }
            }
        }
    }
</script>
