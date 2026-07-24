@extends('layouts.admin')
@section('title', 'Riwayat KGB')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Riwayat KGB</h2>
            <p class="text-sm text-gray-500">Daftar seluruh dokumen Surat Keputusan (SK) Kenaikan Gaji Berkala pegawai yang telah diterbitkan.</p>
        </div>
    </div>

    <!-- Export Section -->
    <div class="bg-white/50 backdrop-blur-3xl rounded-[1.5rem] p-4 border border-blue-100 shadow-xl shadow-blue-500/10 flex flex-col sm:flex-row items-center gap-4 transition hover:shadow-2xl hover:shadow-blue-500/20">
        <div class="text-sm font-medium text-gray-700">Ekspor Data (Excel):</div>
        <form action="{{ route('admin.kgb.export') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
            <div class="flex items-center gap-2">
                <label for="tahun_awal" class="text-xs text-gray-500">Tahun Awal</label>
                <input type="number" name="tahun_awal" id="tahun_awal" value="{{ date('Y') }}" class="text-sm border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 w-24">
            </div>
            <span class="text-gray-400 text-sm hidden sm:block">-</span>
            <div class="flex items-center gap-2">
                <label for="tahun_akhir" class="text-xs text-gray-500">Tahun Akhir</label>
                <input type="number" name="tahun_akhir" id="tahun_akhir" value="{{ date('Y') }}" class="text-sm border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 w-24">
            </div>
            <button type="submit" class="inline-flex items-center justify-center gap-1.5 text-sm bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition font-medium shadow-sm w-full sm:w-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                Ekspor Excel
            </button>
        </form>
    </div>

    <div class="bg-white/50 backdrop-blur-3xl rounded-[1.5rem] border border-blue-100 shadow-xl shadow-blue-500/10 overflow-hidden transition hover:shadow-2xl hover:shadow-blue-500/20">
        <div class="px-4 sm:px-6 py-4 border-b border-white/30 flex flex-wrap items-center justify-between gap-2">
            <div>
                <h3 class="text-base sm:text-lg font-semibold text-gray-800">Tabel Riwayat SK KGB</h3>
                <p class="text-sm text-gray-500">Menampilkan data terakhir berdasarkan tanggal penetapan SK.</p>
            </div>
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-100 border border-blue-200 text-blue-800">Total {{ $riwayatKgb->total() }} Dokumen</span>
        </div>

        @if($riwayatKgb->isEmpty())
            <div class="p-8 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="font-medium">Belum ada riwayat SK KGB.</p>
                <p class="text-sm text-gray-400">Data riwayat akan muncul setelah proses KGB disimpan.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-blue-200/60 text-xs text-blue-900 uppercase tracking-wider border-b border-white/30">
                        <tr>
                            <th class="px-3 py-3 w-16 text-center">No</th>
                            <th class="px-3 py-3 text-left">Nomor SK</th>
                            <th class="px-3 py-3 text-left">Pegawai</th>
                            <th class="px-3 py-3 text-center">TMT Baru</th>
                            <th class="px-3 py-3 text-center">Gaji Baru</th>
                            <th class="px-3 py-3 text-center">Jatuh Tempo YAD</th>
                            <th class="px-3 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($riwayatKgb as $index => $riwayat)
                        <tr class="hover:bg-white/40 transition">
                            <td class="px-3 py-3 text-center text-gray-500 font-medium text-xs">{{ $riwayatKgb->firstItem() + $index }}</td>
                            <td class="px-3 py-3 font-medium text-gray-800">{{ $riwayat->nomor_sk_baru }}</td>
                            <td class="px-3 py-3">
                                <p class="font-medium text-gray-900">{{ $riwayat->pegawai->nama_lengkap ?? '-' }}</p>
                                <p class="text-xs text-gray-600 mt-0.5">{{ $riwayat->pegawai->nip ?? '-' }}</p>
                            </td>
                            <td class="px-3 py-3 text-gray-700 text-center">{{ $riwayat->tmt_baru?->format('d/m/Y') }}</td>
                            <td class="px-3 py-3 text-gray-700 text-center font-medium whitespace-nowrap">Rp {{ number_format($riwayat->gaji_pokok_baru, 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-gray-700 text-center font-medium">{{ $riwayat->tmt_yad?->format('d/m/Y') }}</td>
                            <td class="px-3 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.kgb.show', $riwayat->id) }}" class="inline-flex justify-center items-center p-1.5 bg-blue-500/10 text-blue-600 hover:bg-blue-600 hover:text-white rounded-md transition" title="Detail Riwayat KGB">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.kgb.download-pdf', $riwayat->id) }}" 
                                       onclick="window.dispatchEvent(new CustomEvent('show-toast', { detail: { message: 'Dokumen SK KGB pegawai berhasil digenerate dan sedang diunduh!', type: 'success' } }))"
                                       class="inline-flex justify-center items-center gap-1.5 text-xs font-medium bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md transition whitespace-nowrap">
                                        Unduh PDF
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-white/30">
                {{ $riwayatKgb->links() }}
            </div>
        @endif
    </div>


</div>
@endsection
