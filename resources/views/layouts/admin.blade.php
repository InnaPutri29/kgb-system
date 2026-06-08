<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="KGB System - Sistem Administrasi Kenaikan Gaji Berkala RSD Sidawangi">
    <title>@yield('title', 'Dashboard') — KGB System RSD Sidawangi</title>
    <link rel="icon" href="{{ asset('images/logo-kgb-system.png') }}" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased" 
      x-data="{ sidebarOpen: window.innerWidth >= 1024 }">

    {{-- SIDEBAR OVERLAY --}}
    <div x-show="sidebarOpen" 
         @click="sidebarOpen = false" 
         class="fixed inset-0 z-30 bg-black/40 lg:hidden"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak>
    </div>

    {{-- SIDEBAR --}}
    <div class="flex h-screen overflow-hidden">
        <aside
            class="fixed inset-y-0 left-0 z-40 flex flex-col w-64 bg-gradient-to-b from-blue-900 to-blue-800 text-white transition-all duration-300 transform lg:static lg:translate-x-0 shrink-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:-ml-64'"
        >
            {{-- Logo --}}
            <div class="flex items-center gap-3 px-5 py-5 border-b border-blue-700">
                <div class="w-10 h-10 flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/logo-kgb-system.png') }}" alt="Logo" class="w-full h-full object-contain rounded-lg">
                </div>
                <div>
                    <p class="text-xs text-blue-200 leading-none">Sistem KGB</p>
                    <p class="font-bold text-sm leading-tight">RSD Sidawangi</p>
                </div>
            </div>

            {{-- Nav Links --}}
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>

                <p class="px-3 pt-4 pb-1 text-xs font-semibold text-blue-300 uppercase tracking-wider">Manajemen</p>

                <a href="{{ route('admin.pegawai.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('admin.pegawai.*') ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Data Pegawai
                </a>



                <a href="{{ route('admin.kgb.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('admin.kgb.index') ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Riwayat KGB
                </a>

                <a href="{{ route('admin.kgb.nominatif') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('admin.kgb.nominatif') ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Proses KGB
                </a>

                <p class="px-3 pt-4 pb-1 text-xs font-semibold text-blue-300 uppercase tracking-wider">Pengaturan</p>

                <a href="{{ route('admin.master-pejabat.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('admin.master-pejabat.*') ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Master Pejabat
                </a>

                <a href="{{ route('admin.master-gaji.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('admin.master-gaji.*') ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Master Gaji
                </a>

                <a href="{{ route('admin.pengaturan-instansi.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('admin.pengaturan-instansi.*') ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Pengaturan Instansi
                </a>
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('profile.edit') ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profil Saya
                </a>
            </nav>

            {{-- User info --}}
            <div class="p-4 border-t border-blue-700">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-blue-300">Administrator</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Keluar" class="text-blue-300 hover:text-white transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- MAIN CONTENT --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Top Header --}}
            <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="text-lg font-semibold text-gray-800">@yield('title', 'Dashboard')</h1>
                </div>
                
                <div class="flex items-center gap-6">
                    {{-- Notifikasi Dropdown --}}
                    @php
                        $unreadNotifications = auth()->user()->unreadNotifications;
                        $unreadCount = $unreadNotifications->count();
                    @endphp
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="relative p-1.5 text-gray-400 hover:text-gray-600 transition focus:outline-none rounded-lg hover:bg-gray-50">
                            <span class="sr-only">Notifikasi</span>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @if($unreadCount > 0)
                                <span class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
                                </span>
                            @endif
                        </button>

                        {{-- Dropdown Menu --}}
                        <div x-show="open" @click.outside="open = false" x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2.5 w-80 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50 origin-top-right">
                            <div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between bg-gray-50/50 rounded-t-xl">
                                <span class="text-xs font-bold text-gray-700">Notifikasi ({{ $unreadCount }})</span>
                                @if($unreadCount > 0)
                                    <form method="POST" action="{{ route('notifications.read-all') }}">
                                        @csrf
                                        <button type="submit" class="text-[10px] text-blue-600 hover:text-blue-800 font-bold hover:underline">Tandai semua dibaca</button>
                                    </form>
                                @endif
                            </div>
                            <div class="max-h-64 overflow-y-auto divide-y divide-gray-50">
                                @if(auth()->user()->notifications->isEmpty())
                                    <div class="px-4 py-6 text-center text-gray-400 text-xs">
                                        Tidak ada notifikasi.
                                    </div>
                                @else
                                    @foreach(auth()->user()->notifications->take(10) as $notification)
                                        <div class="p-3.5 hover:bg-gray-50 transition flex items-start justify-between gap-2.5 {{ $notification->unread() ? 'bg-blue-50/20' : '' }}">
                                            <div class="space-y-1">
                                                <p class="text-xs text-gray-700 leading-relaxed font-medium">
                                                    {{ $notification->data['message'] }}
                                                </p>
                                                <p class="text-[10px] text-gray-400">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                            @if($notification->unread())
                                                <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="shrink-0">
                                                    @csrf
                                                    <button type="submit" class="p-1 hover:bg-gray-100 text-blue-500 hover:text-blue-700 rounded-md transition focus:outline-none" title="Tandai dibaca">
                                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="text-sm text-gray-500 hidden sm:block">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </div>
                </div>
            </header>

            {{-- Flash Messages replaced by Global Toast --}}

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto px-6 py-4">
                @yield('content')
            </main>
        </div>
    </div>

    <x-toast />
    <script>
        // Alpine.js sudah di-load lewat vite
        document.addEventListener('alpine:init', () => {})
    </script>

    <x-global-delete-modal />
</body>
</html>
