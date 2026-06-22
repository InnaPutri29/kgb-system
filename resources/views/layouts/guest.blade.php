<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistem KGB') }}</title>
    <link rel="icon" href="{{ asset('images/logo-kgb-system.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased bg-slate-50 selection:bg-blue-500 selection:text-white">
    
    <!-- Full Screen Container -->
    <div class="min-h-screen md:h-screen md:overflow-hidden flex flex-col md:flex-row relative">
        
        <!-- Background Image -->
        <div class="absolute inset-0 z-0 overflow-hidden">
            <!-- Gambar RS Sidawangi. Pastikan gambar di-rename menjadi bg-rs.jpg dan diletakkan di public/images/ -->
            <img src="{{ asset('images/bg-rs.jpg') }}" alt="RS Paru Sidawangi" class="w-full h-full object-cover blur-[3px] scale-[1.02]" onerror="this.src='https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?q=80&w=2053&auto=format&fit=crop'"/>
            
            <!-- Overlay putih semi-transparan di sebelah kiri agar teks biru mudah dibaca -->
            <div class="absolute inset-0 bg-gradient-to-r from-white/80 via-white/40 to-transparent"></div>
        </div>

        <!-- Left Side: Welcome Text (Desktop Only) -->
        <div class="hidden md:flex flex-col justify-center items-center w-full md:w-[50%] p-8 lg:p-12 xl:p-16 z-10 relative h-full">

            <div class="max-w-[700px] w-full text-center flex flex-col items-center lg:ml-8 xl:ml-12">
                <h1 class="text-6xl lg:text-[4rem] xl:text-7xl font-extrabold tracking-tight mb-6 leading-tight drop-shadow-sm text-blue-700 flex flex-col gap-5 items-center">
                    <span>Selamat Datang</span>
                    <span>di Sistem KGB</span>
                    <span>RSD Sidawangi</span>
                </h1>
                
                <p class="text-lg xl:text-xl text-slate-800 font-medium leading-[1.8]">
                    Website untuk mempercepat, memantau, dan <br class="hidden xl:block">
                    mengelola administrasi Kenaikan Gaji Berkala secara efisien.
                </p>
            </div>

        </div>

        <!-- Right Side: Login Card with Glassmorphism -->
        <div class="w-full md:w-[50%] h-screen overflow-y-auto flex items-center justify-center p-8 lg:p-16 z-10 relative">
            
            <!-- Glassmorphism Card -->
            <div class="w-full max-w-[460px] bg-white/70 backdrop-blur-2xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] rounded-3xl overflow-hidden border border-white/60 transition-all duration-300 hover:shadow-[0_8px_40px_rgb(0,0,0,0.16)]">
                <div class="px-8 py-8 sm:px-10 sm:py-8">
                    
                    <!-- Logo Header -->
                    <div class="flex justify-center mb-6">
                        <a href="/" class="flex items-center gap-3 group">
                            <div class="w-14 h-14 flex items-center justify-center group-hover:scale-105 transition-transform duration-300">
                                <img src="{{ asset('images/logo-kgb-system.png') }}" alt="Logo KGB System" class="w-full h-full object-contain rounded-xl shadow-sm border border-blue-100 bg-white p-1">
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Sistem <span class="text-blue-600">KGB</span></h1>
                                <p class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">RSD Sidawangi</p>
                            </div>
                        </a>
                    </div>

                    <!-- Injected Form (login.blade.php) -->
                    {{ $slot }}

                </div>
                
                <!-- Footer -->
                <div class="bg-white/40 px-8 py-4 sm:px-10 border-t border-white/50 text-center backdrop-blur-md">
                    <p class="text-[11px] text-slate-600 font-medium">
                        &copy; {{ date('Y') }} RSD Sidawangi.<br>Sistem Informasi Kenaikan Gaji Berkala.
                    </p>
                </div>
            </div>
        </div>
        
    </div>

</body>
</html>
