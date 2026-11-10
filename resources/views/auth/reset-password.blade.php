<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-slate-800">Atur Ulang Password</h2>
        <p class="text-sm text-slate-500 mt-2 leading-relaxed">
            Silakan buat password baru untuk akun Anda.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat Email</label>
            <input id="email" class="block w-full rounded-xl border-slate-200 bg-slate-50/50 shadow-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors text-sm px-4 py-2.5" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">Password Baru</label>
            <input id="password" class="block w-full rounded-xl border-slate-200 bg-slate-50/50 shadow-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors text-sm px-4 py-2.5" type="password" name="password" required autocomplete="new-password" />
            <p class="mt-1.5 text-xs text-slate-400">Minimal 8 karakter.</p>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1.5">Konfirmasi Password Baru</label>
            <input id="password_confirmation" class="block w-full rounded-xl border-slate-200 bg-slate-50/50 shadow-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors text-sm px-4 py-2.5" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-xs" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                Simpan Password Baru
            </button>
        </div>

        <div class="text-center mt-6 pt-4 border-t border-gray-100">
            <a href="{{ route('login') }}" class="text-sm font-bold text-slate-500 hover:text-blue-600 transition-colors">Kembali ke halaman Login</a>
        </div>
    </form>
</x-guest-layout>
