@extends('layouts.admin')
@section('title', 'Riwayat KGB')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Riwayat KGB</h2>
            <p class="text-sm text-gray-500">Daftar seluruh dokumen Surat Keputusan (SK) Kenaikan Gaji Berkala pegawai yang telah diterbitkan.</p>
        </div>
    </div>

    <!-- Export Section -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col sm:flex-row items-center gap-4">
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

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/80 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Tabel Riwayat SK KGB</h3>
                <p class="text-sm text-gray-500">Menampilkan data terakhir berdasarkan tanggal penetapan SK.</p>
            </div>
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Total {{ $riwayatKgb->total() }} Dokumen</span>
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
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3">Nomor SK</th>
                            <th class="px-5 py-3">Pegawai</th>
                            <th class="px-5 py-3">Tgl Ditetapkan</th>
                            <th class="px-5 py-3">TMT Baru</th>
                            <th class="px-5 py-3">Gaji Lama</th>
                            <th class="px-5 py-3">Gaji Baru</th>
                            <th class="px-5 py-3">Masa Kerja</th>
                            <th class="px-5 py-3">TMT YAD</th>
                            <th class="px-5 py-3">Pejabat</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($riwayatKgb as $riwayat)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4 font-medium text-gray-800">{{ $riwayat->nomor_sk_baru }}</td>
                            <td class="px-5 py-4 text-gray-600">{{ $riwayat->pegawai->nama_lengkap ?? '-' }}<br><span class="text-xs text-gray-400">{{ $riwayat->pegawai->nip ?? '-' }}</span></td>
                            <td class="px-5 py-4 text-gray-600">{{ $riwayat->tanggal_ditetapkan?->format('d/m/Y') }}</td>
                            <td class="px-5 py-4 text-gray-600">{{ $riwayat->tmt_baru?->format('d/m/Y') }}</td>
                            <td class="px-5 py-4 text-gray-600">Rp {{ number_format($riwayat->gaji_pokok_lama, 0, ',', '.') }}</td>
                            <td class="px-5 py-4 text-gray-600">Rp {{ number_format($riwayat->gaji_pokok_baru, 0, ',', '.') }}</td>
                            <td class="px-5 py-4 text-gray-600">{{ $riwayat->masa_kerja_tahun_baru }} Thn, {{ $riwayat->masa_kerja_bulan_baru }} Bln</td>
                            <td class="px-5 py-4 text-gray-600">{{ $riwayat->tmt_yad?->format('d/m/Y') }}</td>
                            <td class="px-5 py-4 text-gray-600">{{ $riwayat->pejabat_penetap }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.kgb.download-pdf', $riwayat->id) }}" 
                                   onclick="window.dispatchEvent(new CustomEvent('show-toast', { detail: { message: 'Dokumen SK KGB pegawai berhasil digenerate dan sedang diunduh!', type: 'success' } }))"
                                   class="inline-flex items-center gap-2 text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg transition">
                                    Unduh PDF
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $riwayatKgb->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
