@extends('layouts.admin')
@section('title', 'Tambah Pegawai')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">Tambah Data Pegawai</h2>
        <a href="{{ route('admin.pegawai.index') }}" class="inline-flex items-center gap-1 text-sm bg-white text-gray-700 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition font-medium border border-gray-300">Kembali</a>
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
                        <x-input-label for="nama_lengkap" value="Nama Lengkap *" />
                        <x-text-input id="nama_lengkap" name="nama_lengkap" type="text" class="mt-1 block w-full" :value="old('nama_lengkap')" required />
                        <x-input-error :messages="$errors->get('nama_lengkap')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="email" value="Email (Opsional)" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                </div>

                <!-- Kepegawaian -->
                <div class="space-y-4">
                    <h3 class="font-semibold text-gray-700 border-b pb-2">Data Kepegawaian</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="pangkat" value="Pangkat" />
                            <x-text-input id="pangkat" name="pangkat" type="text" class="mt-1 block w-full" :value="old('pangkat')" placeholder="Cth: Penata Tingkat I" />
                        </div>
                        <div>
                            <x-input-label for="golongan" value="Golongan" />
                            <x-text-input id="golongan" name="golongan" type="text" class="mt-1 block w-full" :value="old('golongan')" placeholder="Cth: IV/b" />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="jabatan" value="Jabatan" />
                        <x-text-input id="jabatan" name="jabatan" type="text" class="mt-1 block w-full" :value="old('jabatan')" />
                    </div>
                    <div>
                        <x-input-label for="kantor_tempat_kerja" value="Kantor Tempat Kerja" />
                        <x-text-input id="kantor_tempat_kerja" name="kantor_tempat_kerja" type="text" class="mt-1 block w-full" :value="old('kantor_tempat_kerja')" />
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
                        <label for="is_sedang_hukuman_disiplin" class="inline-flex items-center">
                            <input id="is_sedang_hukuman_disiplin" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="is_sedang_hukuman_disiplin" value="1" {{ old('is_sedang_hukuman_disiplin') ? 'checked' : '' }}>
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
