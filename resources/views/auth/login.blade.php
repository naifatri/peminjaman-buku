<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">Selamat Datang Kembali</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Silakan masuk untuk melanjutkan akses perpustakaan.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="space-y-1">
            <x-input-label for="email" :value="__('Email')" class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500" />
            <x-text-input id="email" class="block w-full px-4 py-3 rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="name@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs" />
        </div>

        <!-- Password -->
        <div class="mt-5 space-y-1">
            <div class="flex justify-between items-center px-1">
                <x-input-label for="password" :value="__('Password')" class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500" />
                @if (Route::has('password.request'))
                    <a class="text-xs font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300" href="{{ route('password.request') }}">
                        {{ __('Lupa password?') }}
                    </a>
                @endif
            </div>
            <x-text-input id="password" class="block w-full px-4 py-3 rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200"
                            type="password"
                            name="password"
                            required autocomplete="current-password" placeholder="••••••••" />

            <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center mt-5">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded-lg w-5 h-5 border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200" name="remember">
                <span class="ms-3 text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Ingat saya') }}</span>
            </label>
        </div>

        <div class="mt-8">
            <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-2xl shadow-lg text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform hover:scale-[1.02] active:scale-[0.98] transition-all duration-200">
                {{ __('Masuk Sekarang') }}
            </button>
        </div>

        <div class="mt-10 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="font-bold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 underline-offset-4 hover:underline transition-all duration-200">
                    Daftar di sini
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
