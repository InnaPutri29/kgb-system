@extends('layouts.admin')
@section('title', 'Pengaturan Instansi')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Pengaturan Instansi</h2>
            <p class="text-sm text-gray-500">Kelola identitas instansi dan data pejabat penetap utama.</p>
        </div>
    </div>
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
        <h3 class="font-semibold text-blue-800 mb-1 flex items-center gap-2">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
            Informasi
        </h3>
        <p class="text-sm text-blue-700">Data ini akan digunakan sebagai <strong>Kop Surat</strong> dan informasi pada dokumen cetak seperti SK Kenaikan Gaji Berkala (KGB).</p>
    </div>

    <div class="bg-white/50 backdrop-blur-3xl lg:bg-white lg:backdrop-blur-none rounded-[1.5rem] border border-blue-100 lg:border-slate-100 shadow-xl shadow-blue-500/10 lg:shadow-sm lg:shadow-black/5 overflow-hidden transition hover:shadow-2xl hover:shadow-blue-500/20 lg:hover:shadow-md lg:hover:shadow-black/10">
        <form action="{{ route('admin.pengaturan-instansi.update') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                {{-- Logo Section --}}
                <div class="col-span-1 flex flex-col items-center space-y-4">
                    <p class="text-sm font-semibold text-gray-700 text-center w-full">Logo Instansi</p>
                    
                    <div class="w-40 h-40 rounded-2xl border-2 border-dashed border-gray-300 flex items-center justify-center bg-white/20 overflow-hidden relative group">
                        @if($pengaturan->logo)
                            <img src="{{ asset('storage/' . $pengaturan->logo) }}" alt="Logo Instansi" class="w-full h-full object-contain p-2">
                        @else
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        @endif
                        
                        <div class="absolute inset-0 bg-black/50 hidden group-hover:flex items-center justify-center transition-all cursor-pointer" onclick="document.getElementById('logo').click()">
                            <span class="text-white text-xs font-medium">Ubah Logo</span>
                        </div>
                    </div>
                    <input type="file" id="logo" name="logo" class="hidden" accept="image/jpeg,image/png,image/jpg" onchange="document.getElementById('file-name').textContent = this.files[0]?.name">
                    <p id="file-name" class="text-xs text-center text-gray-500 truncate w-full px-4"></p>
                    <p class="text-xs text-gray-400 text-center">Format: JPG, PNG (Maks 2MB)</p>
                    <x-input-error :messages="$errors->get('logo')" class="mt-1" />
                </div>

                {{-- Text Info Section --}}
                <div class="col-span-1 md:col-span-2 space-y-5">
                    
                    <div>
                        <x-input-label for="nama_instansi" value="Nama Instansi" />
                        <x-text-input id="nama_instansi" name="nama_instansi" type="text" class="mt-1 block w-full" :value="old('nama_instansi', $pengaturan->nama_instansi)" required />
                        <x-input-error :messages="$errors->get('nama_instansi')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="alamat" value="Alamat Lengkap" />
                        <textarea id="alamat" name="alamat" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('alamat', $pengaturan->alamat) }}</textarea>
                        <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                    </div>

                    <hr class="border-gray-100 my-4">
                    <h4 class="text-sm font-semibold text-gray-700">Informasi Kepala Instansi / Direktur</h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="col-span-1 sm:col-span-2">
                            <x-input-label for="nama_direktur" value="Nama Lengkap & Gelar *" />
                            <x-text-input id="nama_direktur" name="nama_direktur" type="text" class="mt-1 block w-full" :value="old('nama_direktur', $pengaturan->nama_direktur)" required />
                            <x-input-error :messages="$errors->get('nama_direktur')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="nip_direktur" value="NIP *" />
                            <x-text-input id="nip_direktur" name="nip_direktur" type="text" class="mt-1 block w-full" :value="old('nip_direktur', $pengaturan->nip_direktur)" required />
                            <x-input-error :messages="$errors->get('nip_direktur')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="pangkat_direktur" value="Pangkat / Golongan *" />
                            <x-text-input id="pangkat_direktur" name="pangkat_direktur" type="text" class="mt-1 block w-full" :value="old('pangkat_direktur', $pengaturan->pangkat_direktur)" required placeholder="Cth: Pembina Utama Muda (IV/c)" />
                            <x-input-error :messages="$errors->get('pangkat_direktur')" class="mt-2" />
                        </div>
                    </div>

                </div>
            </div>

            <div class="mt-8 pt-5 border-t border-gray-100 flex justify-end">
                <x-primary-button class="gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Perubahan
                </x-primary-button>
            </div>
        </form>
    </div>
</div>
@endsection
