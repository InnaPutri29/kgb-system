@extends('layouts.admin')
@section('title', 'Detail Riwayat KGB')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Detail Riwayat KGB</h2>
            <p class="text-sm text-gray-500">Informasi lengkap dokumen Surat Keputusan Kenaikan Gaji Berkala.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.kgb.index') }}" class="inline-flex items-center gap-1 text-sm bg-white text-gray-700 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition font-medium border border-gray-300">
                Kembali
            </a>
            <a href="{{ route('admin.kgb.download-pdf', $riwayat->id) }}" 
               onclick="window.dispatchEvent(new CustomEvent('show-toast', { detail: { message: 'Dokumen SK KGB pegawai berhasil digenerate dan sedang diunduh!', type: 'success' } }))"
               class="inline-flex items-center gap-1 text-sm bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg transition font-medium border border-transparent">
                Unduh PDF
            </a>
        </div>
    </div>

    <div class="bg-white/50 backdrop-blur-3xl lg:bg-white lg:backdrop-blur-none rounded-[1.5rem] border border-white/80 border-t-white shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] overflow-hidden p-6 md:p-8">
        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Informasi SK KGB</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8 text-sm">
            <div>
                <p class="text-gray-500 mb-1">Nomor SK</p>
                <p class="font-medium text-gray-800">{{ $riwayat->nomor_sk_baru }}</p>
            </div>
            <div>
                <p class="text-gray-500 mb-1">Pegawai</p>
                <p class="font-medium text-gray-800">{{ $riwayat->pegawai->nama_lengkap ?? '-' }} <br><span class="text-gray-400 text-xs">{{ $riwayat->pegawai->nip ?? '-' }}</span></p>
            </div>
            <div>
                <p class="text-gray-500 mb-1">Tgl Ditetapkan</p>
                <p class="font-medium text-gray-800">{{ $riwayat->tanggal_ditetapkan?->format('d/m/Y') }}</p>
            </div>
            <div>
                <p class="text-gray-500 mb-1">TMT Baru</p>
                <p class="font-medium text-gray-800">{{ $riwayat->tmt_baru?->format('d/m/Y') }}</p>
            </div>
            <div>
                <p class="text-gray-500 mb-1">Gaji Pokok Lama</p>
                <p class="font-medium text-gray-800">Rp {{ number_format($riwayat->gaji_pokok_lama, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-gray-500 mb-1">Gaji Pokok Baru</p>
                <p class="font-bold text-blue-600">Rp {{ number_format($riwayat->gaji_pokok_baru, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-gray-500 mb-1">Masa Kerja Golongan</p>
                <p class="font-medium text-gray-800">{{ $riwayat->masa_kerja_tahun_baru }} Tahun, {{ $riwayat->masa_kerja_bulan_baru }} Bulan</p>
            </div>
            <div>
                <p class="text-gray-500 mb-1">Jatuh Tempo YAD</p>
                <p class="font-medium text-gray-800">{{ $riwayat->tmt_yad?->format('d/m/Y') }}</p>
            </div>
            <div class="col-span-1 md:col-span-2">
                <p class="text-gray-500 mb-1">Pejabat Penetap SK</p>
                <p class="font-medium text-gray-800">{{ $riwayat->pejabat_penetap ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
