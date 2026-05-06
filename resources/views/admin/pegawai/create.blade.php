@extends('layouts.admin')
@section('title', 'Tambah Pegawai')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">Tambah Data Pegawai</h2>
        <a href="{{ route('admin.pegawai.index') }}" class="text-sm text-gray-500 hover:text-gray-700 font-medium">Kembali</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <form action="{{ route('admin.pegawai.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Data Pribadi -->
                <div class="space-y-4">
                    <h3 class="font-semibold text-gray-700 border-b pb-2">Data Pribadi</h3>
                    <div>
                        <x-input-label for="nip" value="NIP *" />
                        <x-text-input id="nip" name="nip" type="text" class="mt-1 block w-full" :value="old('nip')" required />
                        <x-input-error :messages="$errors->get('nip')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="nama" value="Nama Lengkap *" />
                        <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full" :value="old('nama')" required />
                        <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="tempat_lahir" value="Tempat Lahir" />
                            <x-text-input id="tempat_lahir" name="tempat_lahir" type="text" class="mt-1 block w-full" :value="old('tempat_lahir')" />
                        </div>
                        <div>
                            <x-input-label for="tanggal_lahir" value="Tanggal Lahir" />
                            <x-text-input id="tanggal_lahir" name="tanggal_lahir" type="date" class="mt-1 block w-full" :value="old('tanggal_lahir')" />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="pendidikan_terakhir" value="Pendidikan Terakhir" />
                        <x-text-input id="pendidikan_terakhir" name="pendidikan_terakhir" type="text" class="mt-1 block w-full" :value="old('pendidikan_terakhir')" />
                    </div>
                </div>

                <!-- Kepegawaian -->
                <div class="space-y-4">
                    <h3 class="font-semibold text-gray-700 border-b pb-2">Data Kepegawaian</h3>
                    <div>
                        <x-input-label for="pangkat_golongan" value="Pangkat / Golongan" />
                        <x-text-input id="pangkat_golongan" name="pangkat_golongan" type="text" class="mt-1 block w-full" :value="old('pangkat_golongan')" placeholder="Cth: Penata Muda (III/a)" />
                    </div>
                    <div>
                        <x-input-label for="jabatan" value="Jabatan" />
                        <x-text-input id="jabatan" name="jabatan" type="text" class="mt-1 block w-full" :value="old('jabatan')" />
                    </div>
                    <div>
                        <x-input-label for="unit_kerja" value="Unit Kerja" />
                        <x-text-input id="unit_kerja" name="unit_kerja" type="text" class="mt-1 block w-full" :value="old('unit_kerja')" />
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="tmt_cpns" value="TMT CPNS" />
                            <x-text-input id="tmt_cpns" name="tmt_cpns" type="date" class="mt-1 block w-full" :value="old('tmt_cpns')" />
                        </div>
                        <div>
                            <x-input-label for="tmt_pns" value="TMT PNS" />
                            <x-text-input id="tmt_pns" name="tmt_pns" type="date" class="mt-1 block w-full" :value="old('tmt_pns')" />
                        </div>
                    </div>
                </div>

                <!-- Gaji & KGB -->
                <div class="col-span-1 md:col-span-2 space-y-4 mt-4">
                    <h3 class="font-semibold text-gray-700 border-b pb-2">Riwayat Gaji & KGB</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="tmt_gaji_terakhir" value="TMT Gaji Terakhir" />
                            <x-text-input id="tmt_gaji_terakhir" name="tmt_gaji_terakhir" type="date" class="mt-1 block w-full" :value="old('tmt_gaji_terakhir')" />
                        </div>
                        <div>
                            <x-input-label for="gaji_pokok_terakhir" value="Gaji Pokok Terakhir (Rp)" />
                            <x-text-input id="gaji_pokok_terakhir" name="gaji_pokok_terakhir" type="number" class="mt-1 block w-full" :value="old('gaji_pokok_terakhir')" />
                        </div>
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <x-input-label for="masa_kerja_tahun" value="Masa Kerja (Thn) *" />
                                <x-text-input id="masa_kerja_tahun" name="masa_kerja_tahun" type="number" class="mt-1 block w-full" :value="old('masa_kerja_tahun', 0)" required min="0" />
                            </div>
                            <div class="flex-1">
                                <x-input-label for="masa_kerja_bulan" value="Bulan *" />
                                <x-text-input id="masa_kerja_bulan" name="masa_kerja_bulan" type="number" class="mt-1 block w-full" :value="old('masa_kerja_bulan', 0)" required min="0" max="11" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4">
                        <label for="sedang_hukuman_disiplin" class="inline-flex items-center">
                            <input id="sedang_hukuman_disiplin" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="sedang_hukuman_disiplin" value="1" {{ old('sedang_hukuman_disiplin') ? 'checked' : '' }}>
                            <span class="ms-2 text-sm text-gray-600">Pegawai sedang dalam masa hukuman disiplin</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-5 border-t border-gray-100 flex justify-end">
                <x-primary-button>Simpan Data Pegawai</x-primary-button>
            </div>
        </form>
    </div>
</div>
@endsection
