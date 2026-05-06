@extends('layouts.admin')
@section('title', 'Import Data Pegawai')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Info Card --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
        <h3 class="font-semibold text-blue-800 mb-2 flex items-center gap-2">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
            Petunjuk Import Excel
        </h3>
        <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
            <li>File harus berformat <strong>.xlsx</strong> atau <strong>.xls</strong></li>
            <li>Baris pertama harus berisi <strong>header kolom</strong></li>
            <li>Akun pegawai akan dibuat otomatis; <strong>password default = NIP</strong></li>
            <li>Pegawai yang sudah terdaftar (NIP sama) akan dilewati, tidak diduplikasi</li>
            <li>Ukuran file maksimal <strong>10 MB</strong></li>
        </ul>
    </div>

    {{-- Kolom yang Diharapkan --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h3 class="font-semibold text-gray-700 mb-3">Kolom yang Diharapkan pada File Excel</h3>
        <div class="grid grid-cols-2 gap-2 text-sm">
            @foreach(['nip','nama','email','tempat_lahir','tanggal_lahir','pangkat_golongan','jabatan','unit_kerja','pendidikan_terakhir','tmt_cpns','tmt_pns','tmt_pangkat_terakhir','tmt_gaji_terakhir','masa_kerja_tahun','masa_kerja_bulan','gaji_pokok_terakhir'] as $col)
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-400 shrink-0"></span>
                    <code class="text-xs bg-gray-100 px-1.5 py-0.5 rounded text-gray-700">{{ $col }}</code>
                </div>
            @endforeach
        </div>
        <p class="text-xs text-gray-400 mt-3">* Kolom email bersifat opsional. Kolom wajib: <code>nip</code> dan <code>nama</code>.</p>
    </div>

    {{-- Form Upload --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="font-semibold text-gray-800 mb-5">Unggah File Excel</h2>

        <form action="{{ route('admin.pegawai.import.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div x-data="{ fileName: '' }" class="mb-6">
                <label for="file"
                    class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed rounded-xl cursor-pointer transition
                           {{ $errors->has('file') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-gray-50 hover:bg-gray-100 hover:border-blue-400' }}">
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-10 h-10 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p x-show="!fileName" class="text-sm text-gray-500"><span class="font-medium text-blue-600">Klik untuk pilih file</span> atau drag & drop</p>
                        <p x-show="fileName" class="text-sm font-medium text-green-700" x-text="'✅ ' + fileName"></p>
                        <p class="text-xs text-gray-400 mt-1">.xlsx / .xls — Maks. 10 MB</p>
                    </div>
                    <input
                        id="file"
                        name="file"
                        type="file"
                        accept=".xlsx,.xls"
                        class="hidden"
                        @change="fileName = $event.target.files[0]?.name ?? ''"
                    />
                </label>
                @error('file')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg transition text-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
                    Proses Import
                </button>
                <a href="{{ route('admin.pegawai.index') }}"
                   class="flex-1 text-center border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2.5 px-6 rounded-lg transition text-sm">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
