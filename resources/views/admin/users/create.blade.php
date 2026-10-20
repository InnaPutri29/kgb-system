@extends('layouts.admin')
@section('title', 'Tambah Pengguna')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tambah Pengguna Baru</h2>
            <p class="text-sm text-gray-500">Isi formulir berikut untuk mendaftarkan pengguna baru ke dalam sistem.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1 text-sm bg-white text-gray-700 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition font-medium border border-gray-300">Kembali</a>
    </div>

    <div class="bg-white/50 backdrop-blur-3xl lg:bg-white lg:backdrop-blur-none rounded-[1.5rem] border border-blue-100 lg:border-slate-100 shadow-xl shadow-blue-500/10 lg:shadow-sm lg:shadow-black/5 p-6 md:p-8">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Data Pribadi -->
                <div class="space-y-4">
                    <h3 class="font-semibold text-gray-700 border-b pb-2">Informasi Pengguna</h3>
                    
                    <div>
                        <x-input-label for="name" value="Nama Lengkap *" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    
                    <div>
                        <x-input-label for="email" value="Email *" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    
                    <div>
                        <x-input-label for="nip" value="NIP (Opsional)" />
                        <x-text-input id="nip" name="nip" type="text" class="mt-1 block w-full" :value="old('nip')" />
                        <x-input-error :messages="$errors->get('nip')" class="mt-2" />
                    </div>
                </div>

                <!-- Keamanan & Peran -->
                <div class="space-y-4">
                    <h3 class="font-semibold text-gray-700 border-b pb-2">Keamanan</h3>

                    <div class="rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                        Akun yang ditambahkan di sini otomatis berperan sebagai <strong>Admin</strong>.
                        Akun <strong>Pegawai</strong> dibuat otomatis melalui menu Data Pegawai atau Impor Excel.
                    </div>

                    <div>
                        <x-input-label for="password" value="Password *" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" value="Konfirmasi Password *" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-5 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-medium text-sm shadow-sm">
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
