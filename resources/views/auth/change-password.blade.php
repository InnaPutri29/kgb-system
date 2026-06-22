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
            <p class="text-xs text-gray-400 mt-1">Minimal 8 karakter.</p>
        </div>

        <!-- Konfirmasi Password -->
        <div class="mb-6">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                required autocomplete="new-password" />
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                {{ __('Simpan Password') }}
            </button>
        </div>
    </form>
</x-guest-layout>
