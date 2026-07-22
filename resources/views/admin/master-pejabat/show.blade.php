@extends('layouts.admin')
@section('title', 'Detail Pejabat')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">Detail Master Pejabat</h2>
        <a href="{{ route('admin.master-pejabat.index') }}" class="inline-flex items-center gap-1 text-sm bg-white text-gray-700 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition font-medium border border-gray-300">
            Kembali
        </a>
    </div>

    <div class="bg-white/50 backdrop-blur-3xl rounded-[1.5rem] border border-white/80 border-t-white shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] overflow-hidden relative mt-10">
        <div class="h-24 bg-gradient-to-r from-blue-600 to-indigo-700"></div>
        <div class="px-6 pb-6 relative">
            <div class="w-20 h-20 bg-white rounded-2xl p-1 shadow-md absolute -top-10 left-6 flex items-center justify-center text-3xl font-bold text-blue-600 border border-gray-100">
                {{ substr($masterPejabat->nama_pejabat, 0, 1) }}
            </div>
            
            <div class="pt-14 flex flex-col md:flex-row justify-between md:items-end gap-4">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 leading-tight">{{ $masterPejabat->nama_pejabat ?? '-' }}</h3>
                </div>
                <div class="flex gap-3">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                        {{ $masterPejabat->nama_jabatan ?? 'Jabatan -' }}
                    </span>
                </div>
            </div>

            <div class="mt-8 border-t border-gray-100 pt-6">
                <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Statistik Penandatanganan SK</h4>
                <div class="bg-white/20 p-5 rounded-xl border border-white/50 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Total SK KGB yang Telah Ditandatangani</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $masterPejabat->riwayatKgb->count() }} <span class="text-lg font-medium text-gray-500">Dokumen SK</span></p>
                    </div>
                    <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Dokumen -->
    @if($masterPejabat->riwayatKgb->isNotEmpty())
    <div class="bg-white/50 backdrop-blur-3xl rounded-[1.5rem] border border-white/80 border-t-white shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-white/20/50">
            <h3 class="text-lg font-bold text-gray-800">Daftar Pegawai (SK Terakhir)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-blue-200/60 text-xs text-blue-900 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3 text-left">Nomor SK Baru</th>
                        <th class="px-6 py-3 text-left">Tanggal SK</th>
                        <th class="px-6 py-3 text-left">Nama Pegawai</th>
                        <th class="px-6 py-3 text-left">NIP Pegawai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($masterPejabat->riwayatKgb->take(10) as $riwayat)
                        <tr class="hover:bg-white/40 transition">
                            <td class="px-6 py-3 font-medium text-gray-800">{{ $riwayat->nomor_sk_baru }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ \Carbon\Carbon::parse($riwayat->tanggal_ditetapkan)->format('d/m/Y') }}</td>
                            <td class="px-6 py-3 text-gray-800 font-medium">{{ $riwayat->pegawai->nama_lengkap ?? '-' }}</td>
                            <td class="px-6 py-3 text-gray-600 font-mono text-xs">{{ $riwayat->pegawai->nip ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($masterPejabat->riwayatKgb->count() > 10)
            <div class="px-6 py-3 text-center border-t border-gray-100">
                <p class="text-xs text-gray-500">Menampilkan 10 dokumen terbaru</p>
            </div>
        @endif
    </div>
    @endif
</div>
@endsection
