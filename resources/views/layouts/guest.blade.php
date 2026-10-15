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
<body class="font-sans text-gray-900 antialiased bg-slate-50 selection:bg-blue-500 selection:text-white overflow-hidden h-screen">
    
    <!-- Full Screen Container -->
    <div class="h-screen overflow-hidden flex flex-col md:flex-row relative">
        
        <!-- Background Image -->
        <div class="absolute inset-0 z-0 overflow-hidden">
            <!-- Gambar RS Sidawangi. Pastikan gambar di-rename menjadi bg-rs.jpg dan diletakkan di public/images/ -->
            <img src="{{ asset('images/bg-rs.jpg') }}" alt="RS Paru Sidawangi" class="w-full h-full object-cover blur-[3px] scale-[1.02]" onerror="this.src='https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?q=80&w=2053&auto=format&fit=crop'"/>
            
            <!-- Overlay gradasi biru -->
            <div class="absolute inset-0 bg-gradient-to-br from-[#0B3E6A]/90 via-[#163375]/80 to-[#234A9F]/70 mix-blend-multiply"></div>
            <div class="absolute inset-0 bg-blue-900/30"></div>
        </div>

        <!-- Left Side: Welcome Text (Desktop Only) -->
        <div class="hidden md:flex flex-col justify-center items-center w-full md:w-[50%] p-8 lg:p-12 xl:p-16 z-10 relative h-full">

            <div class="max-w-[700px] w-full text-center flex flex-col items-center lg:ml-8 xl:ml-12">
                <h1 class="text-6xl lg:text-[4rem] xl:text-7xl font-extrabold tracking-tight mb-6 leading-tight drop-shadow-lg text-white flex flex-col gap-5 items-center">
                    <span>Selamat Datang</span>
                    <span class="text-blue-200">di Sistem KGB</span>
                    <span>RSD Sidawangi</span>
                </h1>
                
                <p class="text-lg xl:text-xl text-blue-50 font-medium leading-[1.8] drop-shadow-md">
                    Website untuk mempercepat, memantau, dan <br class="hidden xl:block">
                    mengelola administrasi Kenaikan Gaji Berkala secara efisien.
                </p>
            </div>

        </div>

        <!-- Right Side: Login Card with Glassmorphism -->
        <div class="w-full md:w-[50%] h-screen overflow-hidden z-10 relative flex justify-center items-center">
            
            <div class="w-full flex flex-col justify-center items-center p-4 sm:p-6 lg:p-10 relative z-10">
                
                <!-- Frosted White Card -->
                <div class="w-full max-w-md sm:max-w-[480px] lg:max-w-[500px] max-h-[95vh] overflow-y-auto scrollbar-hide bg-white/85 backdrop-blur-xl shadow-2xl rounded-[2.5rem] border border-white/50 transition-all duration-300 hover:shadow-xl relative">

                    <div class="px-8 py-6 sm:px-10 sm:py-8 relative z-10">
                        
                        <!-- Logo Header -->
                        <div class="flex justify-center mb-5">
                            <a href="/" class="flex flex-col items-center gap-2 group">
                                <div class="w-14 h-14 flex items-center justify-center group-hover:scale-105 transition-transform duration-300">
                                    <img src="{{ asset('images/logo-kgb-system.png') }}" alt="Logo KGB System" class="w-full h-full object-contain rounded-xl shadow-sm border border-blue-100 lg:border-slate-100 bg-white p-1">
                                </div>
                                <div class="text-center">
                                    <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Sistem <span class="text-blue-600">KGB</span></h1>
                                    <p class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">RSD Sidawangi</p>
                                </div>
                            </a>
                        </div>

                        <!-- Injected Form (login.blade.php) -->
                        {{ $slot }}

                    </div>
                    
                    <!-- Footer -->
                    <div class="bg-white px-8 py-5 sm:px-12 border-t border-white/60 text-center relative z-10">
                        <p class="text-[12px] text-slate-700 font-semibold drop-shadow-sm">
                            &copy; {{ date('Y') }} RSD Sidawangi.<br>Sistem Informasi Kenaikan Gaji Berkala.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

</body>
</html>
