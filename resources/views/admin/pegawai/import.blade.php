@extends('layouts.admin')
@section('title', 'Import Data Pegawai')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Info Card --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
        <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between mb-4">
            <h3 class="font-semibold text-blue-800 flex items-center gap-2 m-0">
                <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                Petunjuk Import Excel
            </h3>
            <a href="{{ route('admin.pegawai.import.template') }}" class="inline-flex items-center gap-2 bg-white/40 backdrop-blur-xl border border-blue-300 text-blue-700 hover:bg-blue-100 hover:text-blue-800 px-3 py-1.5 rounded-lg text-sm font-medium transition shadow-sm shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Unduh Template Excel
            </a>
        </div>
        <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
            <li>File harus berformat <strong>.xlsx</strong> atau <strong>.xls</strong></li>
            <li>Baris pertama harus berisi <strong>header kolom</strong></li>
            <li>Akun pegawai akan dibuat otomatis; <strong>password default = NIP</strong></li>
            <li>Pegawai yang sudah terdaftar (NIP sama) akan dilewati, tidak diduplikasi</li>
            <li>Ukuran file maksimal <strong>10 MB</strong></li>
        </ul>
    </div>

    {{-- Kolom yang Diharapkan --}}
    <div class="bg-white/40 backdrop-blur-xl rounded-xl border border-white/50 p-5">
        <h3 class="font-semibold text-gray-700 mb-3">Kolom yang Diharapkan pada File Excel</h3>
        <div class="grid grid-cols-2 gap-2 text-sm">
            @foreach(['nip','nama','email','pangkat','golongan','jabatan','kantor_tempat_kerja','tmt_gaji_terakhir','masa_kerja_tahun','masa_kerja_bulan','gaji_pokok_terakhir','nomor_sk_terakhir','tanggal_sk_terakhir', 'skp_tahun_1', 'skp_predikat_1', 'skp_tahun_2', 'skp_predikat_2'] as $col)
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-400 shrink-0"></span>
                    <code class="text-xs bg-gray-100 px-1.5 py-0.5 rounded text-gray-700">{{ $col }}</code>
                </div>
            @endforeach
        </div>
        <p class="text-xs text-gray-400 mt-3">* Kolom <code>email</code>, <code>nomor_sk_terakhir</code>, <code>tanggal_sk_terakhir</code>, dan ke-4 kolom <code>skp</code> bersifat opsional. Kolom wajib: <code>nip</code> dan <code>nama</code>.</p>
    </div>

    {{-- Form Upload --}}
    <div class="bg-white/40 backdrop-blur-xl rounded-xl border border-white/50 p-6">
        <h2 class="font-semibold text-gray-800 mb-5">Unggah File Excel</h2>

        <form action="{{ route('admin.pegawai.import.store') }}" method="POST" enctype="multipart/form-data" x-data="{ loading: false, fileName: '' }" @submit="if(fileName) { loading = true }">
            @csrf

            <div class="mb-6">
                <label for="file"
                    class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed rounded-xl cursor-pointer transition
                           {{ $errors->has('file') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white/20 hover:bg-gray-100 hover:border-blue-400' }}">
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-10 h-10 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p x-show="!fileName" class="text-sm text-gray-500"><span class="font-medium text-blue-600">Klik untuk pilih file</span> atau drag & drop</p>
                        <p x-show="fileName" class="text-sm font-medium text-green-700" x-cloak x-text="'✅ ' + fileName"></p>
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
                <button type="submit" :disabled="loading || !fileName"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg transition text-sm flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <!-- Spinner Loading -->
                    <svg x-show="loading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" x-cloak>
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    
                    <!-- Icon Biasa -->
                    <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                    </svg>
                    
                    <span x-text="loading ? 'Sedang memproses data...' : 'Proses Import'">Proses Import</span>
                </button>
                <a href="{{ route('admin.pegawai.index') }}" :class="loading ? 'pointer-events-none opacity-50' : ''"
                   class="flex-1 text-center border border-gray-300 hover:bg-white/20 text-gray-700 font-medium py-2.5 px-6 rounded-lg transition text-sm">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
