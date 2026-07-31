@extends('layouts.admin')
@section('title', 'Tambah Pengguna')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
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
                        <x-input-label for="nip" value="NIP (Opsional - Jika Pegawai)" />
                        <x-text-input id="nip" name="nip" type="text" class="mt-1 block w-full" :value="old('nip')" />
                        <x-input-error :messages="$errors->get('nip')" class="mt-2" />
                    </div>
                </div>

                <!-- Keamanan & Peran -->
                <div class="space-y-4">
                    <h3 class="font-semibold text-gray-700 border-b pb-2">Keamanan & Peran</h3>
                    
                    <div class="relative" x-data="{ open: false, selected: '{{ old('role') }}', label: '{{ old('role') ? ucfirst(old('role')) : '' }}' }">
                        <x-input-label for="role" value="Role/Peran *" />
                        
                        <select id="role" name="role" x-model="selected" class="opacity-0 absolute inset-0 w-full h-full pointer-events-none" required>
                            <option value=""></option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}"></option>
                            @endforeach
                        </select>

                        <button type="button" @click="open = !open" @click.away="open = false" 
                            class="mt-1 w-full flex items-center justify-between text-left px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all h-[42px]">
                            <span x-text="label || 'Pilih Role...'" :class="!selected ? 'text-gray-500' : 'text-gray-900'" class="truncate pr-4"></span>
                            <svg class="w-4 h-4 text-gray-400 transition-transform shrink-0" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="open" 
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-xl overflow-hidden" x-cloak>
                            <ul class="max-h-60 overflow-y-auto py-1">
                                <li @click="selected = ''; label = 'Pilih Role...'; open = false" class="px-4 py-2 text-sm text-gray-500 hover:bg-gray-100 cursor-pointer">
                                    Pilih Role...
                                </li>
                                @foreach($roles as $role)
                                    <li @click="selected = '{{ $role->name }}'; label = '{{ ucfirst($role->name) }}'; open = false" 
                                        class="px-4 py-2 text-sm hover:bg-blue-50 cursor-pointer border-l-2 transition-colors"
                                        :class="selected === '{{ $role->name }}' ? 'bg-blue-50 border-blue-500 font-medium text-blue-900' : 'border-transparent text-gray-700'">
                                        {{ ucfirst($role->name) }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
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
