<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-gray-800">Ganti Password</h2>
        <p class="text-sm text-gray-500 mt-1">
            Ini adalah login pertama Anda. Demi keamanan, silakan ganti password Anda sekarang.
        </p>
    </div>

    @if(session('warning'))
        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-300 text-yellow-800 rounded-lg text-sm">
            {{ session('warning') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.change.update') }}">
        @csrf

        <!-- Password Baru -->
        <div class="mb-4">
            <x-input-label for="password" :value="__('Password Baru')" />
            <x-text-input id="password" class="block mt-1 w-full"
                type="password"
                name="password"
                required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <p class="text-xs text-gray-400 mt-1">Min. 8 karakter, harus mengandung huruf kapital, huruf kecil, dan angka.</p>
        </div>

        <!-- Konfirmasi Password -->
        <div class="mb-6">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                required autocomplete="new-password" />
        </div>

        <x-primary-button class="w-full justify-center">
            {{ __('Simpan Password') }}
        </x-primary-button>
    </form>
</x-guest-layout>
