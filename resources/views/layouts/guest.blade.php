<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center py-12 sm:py-20 bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800">
            <div class="mb-12 transform hover:scale-105 transition-transform duration-300">
                <a href="/" class="flex flex-col items-center group">
                    <div class="p-5 bg-white dark:bg-gray-800 rounded-2xl shadow-xl mb-4 transform group-hover:rotate-12 transition-all duration-300">
                        <x-application-logo class="text-indigo-600 dark:text-indigo-400 text-5xl" />
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white tracking-tight">SIP<span class="text-indigo-600 dark:text-indigo-400">BUK</span></h1>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-widest">Sistem Peminjaman Buku</p>
                </a>
            </div>

            <div class="w-full sm:max-w-md px-8 py-10 bg-white dark:bg-gray-800 shadow-[0_20px_50px_rgba(8,_112,_184,_0.1)] dark:shadow-[0_20px_50px_rgba(0,_0,_0,_0.3)] overflow-hidden sm:rounded-3xl border border-white/20 dark:border-gray-700/50 backdrop-blur-sm">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
