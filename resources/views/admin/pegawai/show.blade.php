@extends('layouts.admin')
@section('title', 'Detail Pegawai')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">Detail Data Pegawai</h2>
        <div class="flex gap-2">
            <a href="{{ route('admin.pegawai.edit', $pegawai->id) }}" class="inline-flex items-center gap-1 text-sm bg-blue-50 text-blue-700 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition font-medium border border-blue-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Pegawai
            </a>
            <a href="{{ route('admin.pegawai.index') }}" class="inline-flex items-center gap-1 text-sm bg-white text-gray-700 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition font-medium border border-gray-300">
                Kembali
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden relative">
        <div class="h-24 bg-gradient-to-r from-blue-600 to-indigo-700"></div>
        <div class="px-6 pb-6 relative">
            <div class="w-20 h-20 bg-white rounded-2xl p-1 shadow-md absolute -top-10 left-6 flex items-center justify-center text-3xl font-bold text-blue-600 border border-gray-100">
                {{ substr($pegawai->nama, 0, 1) }}
            </div>
            
            <div class="pt-14 flex flex-col md:flex-row justify-between md:items-end gap-4">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 leading-tight">{{ $pegawai->nama }}</h3>
                    <p class="text-blue-600 font-mono text-sm mt-1">{{ $pegawai->nip }}</p>
                </div>
                <div class="flex gap-3">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                        {{ $pegawai->pangkat_golongan ?? 'Golongan -' }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                        {{ $pegawai->jabatan ?? 'Jabatan -' }}
                    </span>
                </div>
            </div>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                <!-- Data Pribadi -->
                <div>
                    <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3">Informasi Pribadi</h4>
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-50">
                            <tr>
                                <td class="py-2 text-gray-500 w-1/3">Tempat Lahir</td>
                                <td class="py-2 text-gray-800 font-medium">{{ $pegawai->tempat_lahir ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">Tanggal Lahir</td>
                                <td class="py-2 text-gray-800 font-medium">{{ $pegawai->tanggal_lahir ? $pegawai->tanggal_lahir->translatedFormat('d F Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">Pendidikan</td>
                                <td class="py-2 text-gray-800 font-medium">{{ $pegawai->pendidikan_terakhir ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">Hukuman Disiplin</td>
                                <td class="py-2 text-gray-800 font-medium">
                                    @if($pegawai->sedang_hukuman_disiplin)
                                        <span class="text-red-600 font-semibold">Ya (Sedang Menjalani)</span>
                                    @else
                                        <span class="text-green-600">Tidak</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Kepegawaian & Gaji -->
                <div>
                    <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3">Kepegawaian & Gaji</h4>
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-50">
                            <tr>
                                <td class="py-2 text-gray-500 w-1/3">Unit Kerja</td>
                                <td class="py-2 text-gray-800 font-medium">{{ $pegawai->unit_kerja ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">TMT CPNS</td>
                                <td class="py-2 text-gray-800 font-medium">{{ $pegawai->tmt_cpns ? $pegawai->tmt_cpns->translatedFormat('d F Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">TMT PNS</td>
                                <td class="py-2 text-gray-800 font-medium">{{ $pegawai->tmt_pns ? $pegawai->tmt_pns->translatedFormat('d F Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">Masa Kerja</td>
                                <td class="py-2 text-gray-800 font-medium">{{ $pegawai->masa_kerja_tahun }} Tahun, {{ $pegawai->masa_kerja_bulan }} Bulan</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">Gaji Pokok Terakhir</td>
                                <td class="py-2 text-gray-800 font-medium">Rp {{ number_format($pegawai->gaji_pokok_terakhir, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">TMT Gaji Terakhir</td>
                                <td class="py-2 text-gray-800 font-medium">{{ $pegawai->tmt_gaji_terakhir ? $pegawai->tmt_gaji_terakhir->translatedFormat('d F Y') : '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat SK KGB -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Riwayat KGB Pegawai</h3>
            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">{{ $pegawai->riwayatKgb->count() }} Dokumen</span>
        </div>
        
        @if($pegawai->riwayatKgb->isEmpty())
            <div class="p-8 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p>Belum ada riwayat SK KGB untuk pegawai ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3 text-left">Nomor SK Baru</th>
                            <th class="px-6 py-3 text-left">Tanggal SK</th>
                            <th class="px-6 py-3 text-left">TMT KGB</th>
                            <th class="px-6 py-3 text-left">Masa Kerja</th>
                            <th class="px-6 py-3 text-left">Gaji Pokok Baru</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($pegawai->riwayatKgb as $riwayat)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-3 font-medium text-gray-800">{{ $riwayat->nomor_sk_baru }}</td>
                                <td class="px-6 py-3 text-gray-600">{{ \Carbon\Carbon::parse($riwayat->tanggal_sk_baru)->format('d/m/Y') }}</td>
                                <td class="px-6 py-3 text-gray-600 font-medium">{{ \Carbon\Carbon::parse($riwayat->tmt_kgb_baru)->format('d/m/Y') }}</td>
                                <td class="px-6 py-3 text-gray-600">{{ $riwayat->masa_kerja_tahun_baru }} Thn, {{ $riwayat->masa_kerja_bulan_baru }} Bln</td>
                                <td class="px-6 py-3 text-gray-600">Rp {{ number_format($riwayat->gaji_pokok_baru, 0, ',', '.') }}</td>
                                <td class="px-6 py-3 text-right">
                                    <a href="{{ route('admin.kgb.download-pdf', $riwayat->id) }}" class="inline-flex items-center gap-1 text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg transition font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        Unduh
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
