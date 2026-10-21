<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-gray-800">Selamat Datang!</h2>
        <p class="text-sm text-gray-500 mt-1">
            Ini adalah login pertama Anda. Silakan lengkapi data di bawah untuk melanjutkan.
        </p>
    </div>

    @if(session('warning'))
        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-300 text-yellow-800 rounded-lg text-sm">
            {{ session('warning') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.change.update') }}">
        @csrf

        {{-- Email --}}
        <div class="mb-4">
            <x-input-label for="email" :value="__('Email Aktif')" />
            @php
                $isDummyEmail = str_contains(Auth::user()->email, '@kgb.rsd-sidawangi.id');
            @endphp
            <x-text-input id="email" class="block mt-1 w-full"
                type="email"
                name="email"
                :value="$isDummyEmail ? old('email') : Auth::user()->email"
                required
                placeholder="Masukkan email Anda" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
            <p class="text-xs text-gray-400 mt-1">
                Email ini digunakan untuk menerima notifikasi KGB.
                @if(!$isDummyEmail)
                    <span class="text-green-600 font-medium">Email Anda sudah terdaftar, boleh diubah jika perlu.</span>
                @endif
            </p>
        </div>

        {{-- Password Baru --}}
        <div class="mb-4">
            <x-input-label for="password" :value="__('Password Baru')" />
            <x-text-input id="password" class="block mt-1 w-full"
                type="password"
                name="password"
                required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <p class="text-xs text-gray-400 mt-1">Minimal 8 karakter.</p>
        </div>

        {{-- Konfirmasi Password --}}
        <div class="mb-6">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                required autocomplete="new-password" />
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                {{ __('Simpan & Masuk') }}
            </button>
        </div>
    </form>
</x-guest-layout>
