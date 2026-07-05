<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-slate-800">Lupa Password?</h2>
        <p class="text-sm text-slate-500 mt-2 leading-relaxed">
            Tidak masalah. Cukup masukkan alamat email Anda, dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.
        </p>
    </div>

    @php
        $statusMsg = session('status');
        if ($statusMsg === __('passwords.sent') || $statusMsg === 'We have emailed your password reset link.') {
            $statusMsg = 'Kami telah mengirimkan tautan reset kata sandi ke email Anda.';
        }
    @endphp

    @if ($statusMsg)
        <div class="mb-5 p-4 text-sm font-medium text-green-800 rounded-xl bg-green-50 border border-green-200 flex items-start gap-3">
            <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ $statusMsg }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat Email</label>
            <input id="email" class="block w-full rounded-xl border-slate-200 bg-slate-50/50 shadow-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors text-sm px-4 py-2.5" type="email" name="email" :value="old('email')" required autofocus placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                Kirim Tautan Reset Password
            </button>
        </div>
        
        <div class="text-center mt-6 pt-4 border-t border-gray-100">
            <a href="{{ route('login') }}" class="text-sm font-bold text-slate-500 hover:text-blue-600 transition-colors">Kembali ke halaman Login</a>
        </div>
    </form>
</x-guest-layout>
