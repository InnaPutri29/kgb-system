@php
    $layout = auth()->user()->hasRole('admin') ? 'layouts.admin' : 'layouts.pegawai';
@endphp

@extends($layout)
@section('title', 'Pengaturan Profil')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">Pengaturan Profil</h2>
    </div>

    {{-- Alert Success from Session --}}
    @if (session('status') === 'profile-updated' || session('status') === 'password-updated')
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
             class="bg-green-50 text-green-800 border border-green-200 rounded-xl p-4 flex items-center gap-3 shadow-sm transition-all duration-500">
            <svg class="w-5 h-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span class="text-sm font-semibold">Perubahan berhasil disimpan.</span>
        </div>
    @endif

    <div class="bg-white/50 backdrop-blur-3xl rounded-[1.5rem] border border-white/80 border-t-white shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] p-6 md:p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            {{-- INFORMASI PROFIL --}}
            <div class="space-y-4">
                <h3 class="font-semibold text-gray-700 border-b border-white/30 pb-2">Data Pribadi</h3>
                
                <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                    @csrf
                </form>

                <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('patch')

                    <div>
                        <x-input-label for="name" value="Nama Lengkap *" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" value="Alamat Email *" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="mt-2 p-3 bg-yellow-50 rounded-lg border border-yellow-100">
                                <p class="text-xs text-yellow-800">
                                    Alamat email Anda belum diverifikasi.
                                    <button form="send-verification" class="underline font-bold text-yellow-600 hover:text-yellow-900 transition focus:outline-none">
                                        Kirim ulang verifikasi.
                                    </button>
                                </p>
                                @if (session('status') === 'verification-link-sent')
                                    <p class="mt-2 font-bold text-xs text-green-600">
                                        Tautan verifikasi baru telah dikirim ke alamat email Anda.
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="mt-8 pt-5 flex justify-end">
                        <x-primary-button>Simpan Profil</x-primary-button>
                    </div>
                </form>
            </div>

            {{-- UBAH KATA SANDI --}}
            <div class="space-y-4">
                <h3 class="font-semibold text-gray-700 border-b border-white/30 pb-2">Ubah Kata Sandi</h3>

                <form method="post" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    @method('put')

                    <div>
                        <x-input-label for="update_password_current_password" value="Kata Sandi Saat Ini" />
                        <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
                        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="update_password_password" value="Kata Sandi Baru" />
                        <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="update_password_password_confirmation" value="Konfirmasi Kata Sandi Baru" />
                        <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="mt-8 pt-5 flex justify-end">
                        <x-primary-button>Perbarui Kata Sandi</x-primary-button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
