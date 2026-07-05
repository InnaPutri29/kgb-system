<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-slate-800">Buat Akun Baru</h2>
        <p class="text-sm text-slate-500 mt-1">Daftarkan diri Anda untuk mengakses sistem KGB.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                <input id="name" class="block w-full rounded-xl border-slate-200 bg-slate-50/50 shadow-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors text-sm px-4 py-2.5" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Budi Santoso" />
                <x-input-error :messages="$errors->get('name')" class="mt-2 text-xs" />
            </div>

            <!-- NIP -->
            <div>
                <label for="nip" class="block text-sm font-semibold text-slate-700 mb-1.5">NIP</label>
                <input id="nip" class="block w-full rounded-xl border-slate-200 bg-slate-50/50 shadow-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors text-sm px-4 py-2.5" type="text" name="nip" :value="old('nip')" required autocomplete="username" placeholder="18 digit" />
                <x-input-error :messages="$errors->get('nip')" class="mt-2 text-xs" />
            </div>
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat Email</label>
            <input id="email" class="block w-full rounded-xl border-slate-200 bg-slate-50/50 shadow-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors text-sm px-4 py-2.5" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                <input id="password" class="block w-full rounded-xl border-slate-200 bg-slate-50/50 shadow-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors text-sm px-4 py-2.5"
                                type="password"
                                name="password"
                                required autocomplete="new-password" placeholder="Min 8 karakter" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs" />
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1.5">Konfirmasi</label>
                <input id="password_confirmation" class="block w-full rounded-xl border-slate-200 bg-slate-50/50 shadow-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors text-sm px-4 py-2.5"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-xs" />
            </div>
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                {{ __('Daftar Sekarang') }}
            </button>
        </div>

        <div class="text-center mt-4 pt-2">
            <p class="text-sm text-slate-500">Sudah punya akun? <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:text-blue-500 transition-colors">Masuk di sini</a></p>
        </div>
    </form>
</x-guest-layout>
