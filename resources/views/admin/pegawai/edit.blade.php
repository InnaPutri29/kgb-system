@extends('layouts.admin')
@section('title', 'Edit Pegawai')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">Edit Data Pegawai: {{ $pegawai->nama_lengkap }}</h2>
        <a href="{{ route('admin.pegawai.index') }}" class="text-sm text-gray-500 hover:text-gray-700 font-medium">Kembali</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <form action="{{ route('admin.pegawai.update', $pegawai->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Data Pribadi -->
                <div class="space-y-4">
                    <h3 class="font-semibold text-gray-700 border-b pb-2">Data Pribadi</h3>
                    <div>
                        <x-input-label for="nip" value="NIP *" />
                        <x-text-input id="nip" name="nip" type="text" class="mt-1 block w-full bg-gray-50" :value="old('nip', $pegawai->nip)" required readonly title="NIP tidak dapat diubah dari sini. Hapus dan buat baru jika salah." />
                        <x-input-error :messages="$errors->get('nip')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="nama_lengkap" value="Nama Lengkap *" />
                        <x-text-input id="nama_lengkap" name="nama_lengkap" type="text" class="mt-1 block w-full" :value="old('nama_lengkap', $pegawai->nama_lengkap)" required />
                        <x-input-error :messages="$errors->get('nama_lengkap')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="email" value="Email (Opsional)" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $pegawai->user?->email)" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                </div>

                <!-- Kepegawaian -->
                <div class="space-y-4">
                    <h3 class="font-semibold text-gray-700 border-b pb-2">Data Kepegawaian</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="pangkat" value="Pangkat" />
                            <x-text-input id="pangkat" name="pangkat" type="text" class="mt-1 block w-full" :value="old('pangkat', $pegawai->pangkat)" placeholder="Cth: Penata Tingkat I" />
                        </div>
                        <div>
                            <x-input-label for="golongan" value="Golongan" />
                            <x-text-input id="golongan" name="golongan" type="text" class="mt-1 block w-full" :value="old('golongan', $pegawai->golongan)" placeholder="Cth: IV/b" />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="jabatan" value="Jabatan" />
                        <x-text-input id="jabatan" name="jabatan" type="text" class="mt-1 block w-full" :value="old('jabatan', $pegawai->jabatan)" />
                    </div>
                    <div>
                        <x-input-label for="kantor_tempat_kerja" value="Kantor Tempat Kerja" />
                        <x-text-input id="kantor_tempat_kerja" name="kantor_tempat_kerja" type="text" class="mt-1 block w-full" :value="old('kantor_tempat_kerja', $pegawai->kantor_tempat_kerja)" />
                    </div>
                    
                </div>

                <!-- Gaji & KGB -->
                <div class="col-span-1 md:col-span-2 space-y-4 mt-4">
                    <h3 class="font-semibold text-gray-700 border-b pb-2">Riwayat Gaji & KGB</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="tmt_gaji_terakhir" value="TMT Gaji Terakhir" />
                            <x-text-input id="tmt_gaji_terakhir" name="tmt_gaji_terakhir" type="date" class="mt-1 block w-full" :value="old('tmt_gaji_terakhir', $pegawai->tmt_gaji_terakhir?->format('Y-m-d'))" />
                        </div>
                        <div>
                            <x-input-label for="gaji_pokok_terakhir" value="Gaji Pokok Terakhir (Rp)" />
                            <x-text-input id="gaji_pokok_terakhir" name="gaji_pokok_terakhir" type="number" class="mt-1 block w-full" :value="old('gaji_pokok_terakhir', (int)$pegawai->gaji_pokok_terakhir)" />
                        </div>
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <x-input-label for="masa_kerja_tahun" value="Masa Kerja (Thn) *" />
                                <x-text-input id="masa_kerja_tahun" name="masa_kerja_tahun" type="number" class="mt-1 block w-full" :value="old('masa_kerja_tahun', $pegawai->masa_kerja_tahun)" required min="0" />
                            </div>
                            <div class="flex-1">
                                <x-input-label for="masa_kerja_bulan" value="Bulan *" />
                                <x-text-input id="masa_kerja_bulan" name="masa_kerja_bulan" type="number" class="mt-1 block w-full" :value="old('masa_kerja_bulan', $pegawai->masa_kerja_bulan)" required min="0" max="11" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4">
                        <label for="is_sedang_hukuman_disiplin" class="inline-flex items-center">
                            <input id="is_sedang_hukuman_disiplin" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="is_sedang_hukuman_disiplin" value="1" {{ old('is_sedang_hukuman_disiplin', $pegawai->is_sedang_hukuman_disiplin) ? 'checked' : '' }}>
                            <span class="ms-2 text-sm text-gray-600">Pegawai sedang dalam masa hukuman disiplin</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-5 border-t border-gray-100 flex justify-end">
                <x-primary-button>Simpan Perubahan</x-primary-button>
            </div>
        </form>
    </div>
</div>
@endsection
