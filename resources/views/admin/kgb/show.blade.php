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

    <div class="bg-white/50 backdrop-blur-3xl lg:bg-white lg:backdrop-blur-none rounded-[1.5rem] border border-white/80 border-t-white shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] overflow-hidden relative mt-4">
        <div class="h-3 bg-gradient-to-r from-[#0B3E6A] to-[#234A9F] relative"></div>
        <div class="px-6 pb-6 relative">
            <div class="pt-6 flex flex-col md:flex-row justify-between md:items-end gap-4">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 leading-tight">SK KGB: {{ $riwayat->nomor_sk_baru }}</h3>
                    <p class="text-blue-600 font-medium mt-1">{{ $riwayat->pegawai->nama_lengkap ?? '-' }} <span class="font-mono text-sm ml-1">({{ $riwayat->pegawai->nip ?? '-' }})</span></p>
                </div>
                <div class="flex gap-3">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-green-50 text-green-700 border border-green-100 lg:border-slate-100">
                        Selesai Diproses
                    </span>
                </div>
            </div>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                <!-- KGB Baru -->
                <div>
                    <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3">Penetapan KGB Baru</h4>
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-50">
                            <tr>
                                <td class="py-2 text-gray-500 w-1/3">Nomor SK</td>
                                <td class="py-2 text-gray-800 font-medium">{{ $riwayat->nomor_sk_baru }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">Tgl Ditetapkan</td>
                                <td class="py-2 text-gray-800 font-medium">{{ $riwayat->tanggal_ditetapkan?->format('d/m/Y') ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">TMT Baru</td>
                                <td class="py-2 text-gray-800 font-medium">{{ $riwayat->tmt_baru?->format('d/m/Y') ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">Masa Kerja Golongan</td>
                                <td class="py-2 text-gray-800 font-medium">{{ $riwayat->masa_kerja_tahun_baru }} Tahun, {{ $riwayat->masa_kerja_bulan_baru }} Bulan</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">Jatuh Tempo YAD</td>
                                <td class="py-2 text-gray-800 font-medium">{{ $riwayat->tmt_yad?->format('d/m/Y') ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Dasar KGB & Keuangan -->
                <div>
                    <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3">Keuangan & Dasar SK</h4>
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-50">
                            <tr>
                                <td class="py-2 text-gray-500 w-1/3">Gaji Pokok Lama</td>
                                <td class="py-2 text-gray-800 font-medium">Rp {{ number_format($riwayat->gaji_pokok_lama, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">Gaji Pokok Baru</td>
                                <td class="py-2 text-blue-600 font-bold text-base">Rp {{ number_format($riwayat->gaji_pokok_baru, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">Pejabat Penetap SK</td>
                                <td class="py-2 text-gray-800 font-medium">{{ $riwayat->pejabat_penetap ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
