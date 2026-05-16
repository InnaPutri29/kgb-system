<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistem KGB') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased bg-slate-50 selection:bg-blue-500 selection:text-white">
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden bg-gradient-to-br from-blue-50 via-slate-50 to-indigo-50">
        <!-- Abstract Background Shapes -->
        <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-blue-400/20 rounded-full mix-blend-multiply filter blur-3xl animate-blob"></div>
        <div class="absolute top-[20%] right-[-10%] w-[400px] h-[400px] bg-indigo-400/20 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-[-20%] left-[20%] w-[600px] h-[600px] bg-cyan-400/20 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-4000"></div>

        <div class="w-full sm:max-w-md md:max-w-lg z-10 px-4 py-10">
            <div class="bg-white/80 backdrop-blur-xl shadow-2xl shadow-blue-900/5 rounded-3xl overflow-hidden border border-white/60">
                <div class="px-8 pt-10 pb-8 sm:px-12 sm:pt-12 sm:pb-10">
                    <div class="flex justify-center mb-8">
                        <a href="/" class="flex items-center gap-3 group">
                            <div class="w-14 h-14 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-600/30 group-hover:scale-105 transition-transform duration-300">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">KGB<span class="text-blue-600">System</span></h1>
                                <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">RSD Sidawangi</p>
                            </div>
                        </a>
                    </div>

                    {{ $slot }}

                </div>
                <div class="bg-slate-50/80 px-8 py-5 sm:px-12 border-t border-slate-100/60 text-center">
                    <p class="text-xs text-slate-500 font-medium">
                        &copy; {{ date('Y') }} RSD Sidawangi.<br>Sistem Informasi Kenaikan Gaji Berkala.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
