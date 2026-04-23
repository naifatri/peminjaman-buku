@props(['pageTitle' => 'Katalog Buku'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIPBUK') }} - Peminjam</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        <div
            x-show="sidebarOpen"
            x-transition.opacity
            class="fixed inset-0 z-40 bg-slate-900/60 lg:hidden backdrop-blur-sm"
            @click="sidebarOpen = false"
            x-cloak
        ></div>

        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-72 bg-[#0f172a] text-slate-300 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 flex flex-col shadow-2xl"
        >
            <div class="flex items-center px-8 h-20 bg-[#1e293b]/50 border-b border-slate-700/50">
                <a href="{{ route('peminjam.books.index') }}" class="flex items-center space-x-3 group">
                    <div class="p-2 bg-indigo-500 rounded-lg group-hover:rotate-12 transition-transform duration-300 shadow-lg shadow-indigo-500/30">
                        <i class="fas fa-book-open text-white text-xl"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xl font-bold text-white tracking-tight">SIP<span class="text-indigo-400">BUK</span></span>
                        <span class="text-[10px] uppercase tracking-[0.2em] text-slate-400 font-semibold leading-tight">Peminjam Portal</span>
                    </div>
                </a>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto custom-scrollbar">
                <div class="px-4 mb-2">
                    <span class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Menu Utama</span>
                </div>

                <a href="{{ route('peminjam.books.index') }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('peminjam.books.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i class="fas fa-book mr-4 text-lg {{ request()->routeIs('peminjam.books.*') ? 'text-white' : 'text-slate-500 group-hover:text-indigo-400' }}"></i>
                    <span>Katalog Buku</span>
                </a>

                <div class="px-4 mt-8 mb-2">
                    <span class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Aktivitas Saya</span>
                </div>

                <a href="{{ route('peminjam.borrowings.index') }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('peminjam.borrowings.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i class="fas fa-exchange-alt mr-4 text-lg {{ request()->routeIs('peminjam.borrowings.*') ? 'text-white' : 'text-slate-500 group-hover:text-indigo-400' }}"></i>
                    <span>Riwayat Peminjaman</span>
                </a>

                <a href="{{ route('peminjam.fines.index') }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('peminjam.fines.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i class="fas fa-wallet mr-4 text-lg {{ request()->routeIs('peminjam.fines.*') ? 'text-white' : 'text-slate-500 group-hover:text-indigo-400' }}"></i>
                    <span>Denda Saya</span>
                </a>
            </nav>

            <div class="p-4 bg-[#1e293b]/30 border-t border-slate-700/50">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center px-4 py-3 text-sm font-medium text-slate-400 hover:text-white hover:bg-red-500/20 rounded-xl transition-all duration-200 group">
                        <i class="fas fa-sign-out-alt mr-4 text-lg group-hover:text-red-400"></i>
                        <span>Keluar Sistem</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex flex-col flex-1 w-full bg-slate-50">
            <header class="flex items-center justify-between h-20 px-8 bg-white border-b border-slate-200 sticky top-0 z-10 shadow-sm">
                <div class="flex items-center">
                    <button @click="sidebarOpen = true" class="p-2 -ml-2 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition-colors lg:hidden focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div class="hidden lg:block">
                        <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-widest">Workspace</h2>
                        <p class="text-lg font-bold text-slate-800">{{ $pageTitle }}</p>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <div x-data="{ notificationOpen: false }" class="relative">
                        <button @click="notificationOpen = !notificationOpen" class="relative p-2 text-slate-400 hover:text-indigo-600 hover:bg-slate-100 rounded-xl transition-all duration-200">
                            <i class="far fa-bell text-xl"></i>
                            @if(($headerNotificationCount ?? 0) > 0)
                                <span class="absolute -top-0.5 -right-0.5 min-w-[1.1rem] h-[1.1rem] px-1 bg-rose-500 text-white text-[10px] font-bold rounded-full border-2 border-white flex items-center justify-center">
                                    {{ $headerNotificationCount > 9 ? '9+' : $headerNotificationCount }}
                                </span>
                            @endif
                        </button>

                        <div
                            x-show="notificationOpen"
                            @click.away="notificationOpen = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-3 w-96 max-w-[calc(100vw-2rem)] bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden z-50"
                            x-cloak
                        >
                            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-bold text-slate-800">Notifikasi Saya</p>
                                    <p class="text-[11px] text-slate-400">Perkembangan status peminjamanmu</p>
                                </div>
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-full text-[10px] font-bold uppercase tracking-wider">
                                    {{ $headerNotificationCount ?? 0 }} update
                                </span>
                            </div>

                            <div class="max-h-96 overflow-y-auto custom-scrollbar">
                                @forelse(($headerNotifications ?? collect()) as $notification)
                                    <a href="{{ $notification->url }}" class="flex gap-3 px-5 py-4 hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-b-0">
                                        <div class="mt-0.5 w-10 h-10 rounded-2xl flex items-center justify-center
                                            {{ $notification->accent === 'indigo' ? 'bg-indigo-50 text-indigo-600' : '' }}
                                            {{ $notification->accent === 'amber' ? 'bg-amber-50 text-amber-600' : '' }}
                                            {{ $notification->accent === 'rose' ? 'bg-rose-50 text-rose-600' : '' }}
                                            {{ $notification->accent === 'emerald' ? 'bg-emerald-50 text-emerald-600' : '' }}">
                                            <i class="fas fa-bell text-sm"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-slate-700 leading-6">{{ $notification->message }}</p>
                                            <p class="text-[11px] text-slate-400 mt-1">{{ $notification->time->diffForHumans() }}</p>
                                        </div>
                                    </a>
                                @empty
                                    <div class="px-5 py-10 text-center">
                                        <i class="far fa-bell-slash text-3xl text-slate-200"></i>
                                        <p class="text-sm text-slate-400 mt-3">Belum ada notifikasi untuk akun ini.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="h-8 w-[1px] bg-slate-200 mx-2"></div>

                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen" class="flex items-center space-x-3 p-1.5 hover:bg-slate-50 rounded-xl transition-all duration-200 focus:outline-none group">
                            <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center border border-indigo-200">
                                <span class="text-indigo-700 font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                            <div class="hidden md:flex flex-col items-start leading-tight">
                                <span class="text-sm font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">{{ Auth::user()->name }}</span>
                                <span class="text-[10px] font-medium text-slate-400 uppercase tracking-tighter">Peminjam</span>
                            </div>
                            <i class="fas fa-chevron-down text-[10px] text-slate-400 group-hover:text-indigo-600 transition-colors"></i>
                        </button>

                        <div
                            x-show="dropdownOpen"
                            @click.away="dropdownOpen = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-2xl py-2 z-50 border border-slate-100 overflow-hidden"
                            x-cloak
                        >
                            <div class="px-4 py-3 border-b border-slate-50 mb-1">
                                <p class="text-xs text-slate-400 font-medium">Signed in as</p>
                                <p class="text-sm font-bold text-slate-800 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2.5 text-sm text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 transition-all duration-200">
                                <i class="far fa-user-circle mr-3 text-lg"></i>
                                Profil Saya
                            </a>
                            <div class="px-2 mt-1 pt-1 border-t border-slate-50">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 rounded-xl transition-all duration-200 font-medium">
                                        <i class="fas fa-sign-out-alt mr-3 text-lg"></i>
                                        Keluar Sistem
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto custom-scrollbar bg-slate-50">
                <div class="container mx-auto px-8 py-10">
                    @if (session('success'))
                        <div class="mb-6 flex items-center p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-2xl shadow-sm" x-data="{ show: true }" x-show="show" x-transition>
                            <i class="fas fa-check-circle mr-3 text-xl text-emerald-500"></i>
                            <p class="flex-1 font-medium">{{ session('success') }}</p>
                            <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-600 focus:outline-none transition-colors">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 flex items-center p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-r-2xl shadow-sm" x-data="{ show: true }" x-show="show" x-transition>
                            <i class="fas fa-exclamation-circle mr-3 text-xl text-rose-500"></i>
                            <p class="flex-1 font-medium">{{ session('error') }}</p>
                            <button @click="show = false" class="ml-auto text-rose-400 hover:text-rose-600 focus:outline-none transition-colors">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</body>
</html>
