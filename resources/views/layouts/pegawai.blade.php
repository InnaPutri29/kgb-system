<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="KGB System - Sistem Administrasi Kenaikan Gaji Berkala RSD Sidawangi">
    <title>@yield('title', 'Dashboard') - KGB System RSD Sidawangi</title>
    <link rel="icon" href="{{ asset('images/logo-kgb-system.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased relative min-h-screen overflow-hidden"
      x-data="{ sidebarOpen: false }">

    <!-- Pastel Blobs -->
    <div class="fixed top-[-10%] left-[-10%] w-[600px] h-[600px] bg-blue-200/60 rounded-full mix-blend-multiply filter blur-[100px] animate-pulse" style="animation-duration:8s;z-index:0;"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[600px] h-[600px] bg-indigo-200/60 rounded-full mix-blend-multiply filter blur-[100px] animate-pulse" style="animation-duration:10s;animation-delay:2s;z-index:0;"></div>
    <div class="fixed top-[20%] left-[40%] w-[800px] h-[800px] bg-sky-200/50 rounded-full mix-blend-multiply filter blur-[120px] animate-pulse" style="animation-duration:12s;animation-delay:4s;z-index:0;"></div>

    <div class="flex h-screen overflow-hidden relative z-10">

        {{-- SIDEBAR (desktop lg+ only) --}}
        <aside class="hidden lg:flex flex-col w-64 bg-gradient-to-b from-[#0B3E6A]/95 to-[#234A9F]/95 backdrop-blur-2xl border-r border-white/10 shadow-[4px_0_24px_rgba(35,74,159,0.4)] lg:shadow-none text-blue-50 shrink-0">
            {{-- Logo --}}
            <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10 bg-[#072C4C]/40">
                <div class="w-10 h-10 flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/logo-kgb-system.png') }}" alt="Logo" class="w-full h-full object-contain rounded-xl shadow-sm bg-white p-1">
                </div>
                <div>
                    <p class="text-xs font-bold text-white leading-none tracking-wider">Sistem <span class="text-white">KGB</span></p>
                    <p class="font-bold text-sm text-white leading-tight">RSD Sidawangi</p>
                </div>
            </div>

            {{-- Nav Links --}}
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <a href="{{ route('pegawai.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('pegawai.dashboard') ? 'bg-white/20 shadow-sm text-white border border-white/30' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>

                <p class="px-3 pt-4 pb-1 text-[11px] font-bold text-white/70 uppercase tracking-widest">Layanan Kepegawaian</p>

                <a href="{{ route('pegawai.kgb') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('pegawai.kgb') ? 'bg-white/20 shadow-sm text-white border border-white/30' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Riwayat KGB
                </a>

                <a href="{{ route('pegawai.skp') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('pegawai.skp') ? 'bg-white/20 shadow-sm text-white border border-white/30' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Evaluasi SKP
                </a>

                <p class="px-3 pt-4 pb-1 text-[11px] font-bold text-white/70 uppercase tracking-widest">Pengaturan</p>

                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('profile.edit') ? 'bg-white/20 shadow-sm text-white border border-white/30' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profil Saya
                </a>
            </nav>

            {{-- User info --}}
            <div class="p-4 border-t border-white/10 bg-[#163375]/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold text-white shadow-inner">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] font-semibold text-white/70">Pegawai</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Keluar" class="p-1.5 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- MAIN CONTENT --}}
        <div class="flex-1 flex flex-col overflow-hidden relative z-10">

            {{-- Top Header --}}
            <header class="relative z-50 bg-white/70 backdrop-blur-2xl border-b border-white/80 px-4 sm:px-6 py-3.5 flex items-center justify-between shadow-[0_4px_20px_rgba(11,62,106,0.08)] lg:shadow-sm lg:shadow-black/5">
                <div class="flex items-center gap-3 min-w-0">
                    {{-- Logo kecil hanya di mobile --}}
                    <a href="{{ route('pegawai.dashboard') }}" class="flex items-center gap-2 lg:hidden hover:opacity-80 transition-opacity">
                        <img src="{{ asset('images/logo-kgb-system.png') }}" alt="Logo" class="w-7 h-7 object-contain rounded-lg bg-white p-1 shadow-md border border-gray-200/60 ring-1 ring-black/5">
                    </a>
                    <h1 class="text-base font-bold text-slate-800 truncate lg:text-lg">@yield('title', 'Dashboard')</h1>
                </div>

                <div class="flex items-center gap-4">
                    {{-- Notifikasi --}}
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

                        <div x-show="open" @click.outside="open = false" x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2.5 w-72 sm:w-80 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50 origin-top-right">
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
                                    <div class="px-4 py-6 text-center text-gray-400 text-xs">Tidak ada notifikasi.</div>
                                @else
                                    @foreach(auth()->user()->notifications->take(10) as $notification)
                                        <div class="p-3.5 hover:bg-gray-50 transition flex items-start justify-between gap-2.5 {{ $notification->unread() ? 'bg-blue-50/20' : '' }}">
                                            <div class="space-y-1">
                                                <p class="text-xs text-gray-700 leading-relaxed font-medium">{{ $notification->data['message'] }}</p>
                                                <p class="text-[10px] text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
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

                    <div class="text-xs sm:text-sm text-gray-500 hidden sm:block">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </div>

                    {{-- User Dropdown (Mobile Only) --}}
                    <div class="relative lg:hidden ml-1 sm:ml-2" x-data="{ userOpen: false }">
                        <button @click="userOpen = !userOpen" class="flex items-center focus:outline-none">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center text-sm font-bold text-blue-800 shadow-sm border border-blue-300 transition hover:shadow-md">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        </button>

                        <div x-show="userOpen" @click.outside="userOpen = false" x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2.5 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-50 origin-top-right">
                             
                             <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50 rounded-t-xl">
                                 <p class="text-sm font-bold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                                 <p class="text-[11px] text-gray-500 truncate mt-0.5">Pegawai</p>
                             </div>
                             
                             <form method="POST" action="{{ route('logout') }}">
                                 @csrf
                                 <button type="submit" class="w-full flex items-center gap-2 text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition font-medium">
                                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                     Keluar
                                 </button>
                             </form>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Page Content — extra bottom padding on mobile for bottom nav --}}
            <main class="flex-1 overflow-y-auto px-3 sm:px-6 py-4 sm:py-6 pb-24 lg:pb-6">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- =============================================
         BOTTOM NAVIGATION BAR — Mobile only (hidden lg+)
         ============================================= --}}
    <nav class="lg:hidden fixed bottom-0 inset-x-0 z-50 bg-gradient-to-r from-[#0B3E6A] to-[#1a3f8f] border-t border-white/10 shadow-[0_-4px_20px_rgba(11,62,106,0.4)]">
        <div class="flex h-16 safe-area-inset-bottom">

            {{-- Dashboard --}}
            <a href="{{ route('pegawai.dashboard') }}"
               class="flex-1 flex flex-col items-center justify-center gap-1 relative transition-all duration-150
                      {{ request()->routeIs('pegawai.dashboard') ? 'text-white' : 'text-white/45 active:text-white/80' }}">
                @if(request()->routeIs('pegawai.dashboard'))
                    <span class="absolute top-0 inset-x-3 h-0.5 bg-white rounded-full"></span>
                @endif
                <svg class="w-5 h-5" fill="{{ request()->routeIs('pegawai.dashboard') ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="{{ request()->routeIs('pegawai.dashboard') ? '0' : '1.8' }}" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="text-[10px] font-semibold leading-none">Dashboard</span>
            </a>

            {{-- Riwayat KGB --}}
            <a href="{{ route('pegawai.kgb') }}"
               class="flex-1 flex flex-col items-center justify-center gap-1 relative transition-all duration-150
                      {{ request()->routeIs('pegawai.kgb') ? 'text-white' : 'text-white/45 active:text-white/80' }}">
                @if(request()->routeIs('pegawai.kgb'))
                    <span class="absolute top-0 inset-x-3 h-0.5 bg-white rounded-full"></span>
                @endif
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="{{ request()->routeIs('pegawai.kgb') ? '2.5' : '1.8' }}" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-[10px] font-semibold leading-none">KGB</span>
            </a>

            {{-- Evaluasi SKP --}}
            <a href="{{ route('pegawai.skp') }}"
               class="flex-1 flex flex-col items-center justify-center gap-1 relative transition-all duration-150
                      {{ request()->routeIs('pegawai.skp') ? 'text-white' : 'text-white/45 active:text-white/80' }}">
                @if(request()->routeIs('pegawai.skp'))
                    <span class="absolute top-0 inset-x-3 h-0.5 bg-white rounded-full"></span>
                @endif
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="{{ request()->routeIs('pegawai.skp') ? '2.5' : '1.8' }}" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="text-[10px] font-semibold leading-none">Evaluasi SKP</span>
            </a>

            {{-- Profil --}}
            <a href="{{ route('profile.edit') }}"
               class="flex-1 flex flex-col items-center justify-center gap-1 relative transition-all duration-150
                      {{ request()->routeIs('profile.edit') ? 'text-white' : 'text-white/45 active:text-white/80' }}">
                @if(request()->routeIs('profile.edit'))
                    <span class="absolute top-0 inset-x-3 h-0.5 bg-white rounded-full"></span>
                @endif
                <svg class="w-5 h-5" fill="{{ request()->routeIs('profile.edit') ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="{{ request()->routeIs('profile.edit') ? '0' : '1.8' }}" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-[10px] font-semibold leading-none">Profil</span>
            </a>

        </div>
    </nav>

    <x-toast />
    <script>
        document.addEventListener('alpine:init', () => {})
    </script>
    <x-global-delete-modal />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>
</html>
