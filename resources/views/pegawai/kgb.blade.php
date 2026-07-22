@extends('layouts.pegawai')
@section('title', 'Riwayat Kenaikan Gaji Berkala')

@section('content')
<div class="space-y-6">

    {{-- ALERT JIKA DATA PEGAWAI KOSONG --}}
    @if(!$pegawai)
        <div class="bg-amber-50/50 backdrop-blur-3xl border border-amber-200/80 border-t-amber-100 text-amber-900 rounded-[1.5rem] p-6 shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-amber-100/80 shadow-sm flex items-center justify-center shrink-0 text-xl text-amber-600">⚠️</div>
            <div>
                <h3 class="font-bold text-lg">Data Pegawai Tidak Ditemukan</h3>
                <p class="text-sm text-amber-800/80">Akun Anda belum terhubung dengan data PNS. Silakan hubungi admin kepegawaian.</p>
            </div>
        </div>
    @else


    {{-- ARSIP SK KGB --}}
    <div class="bg-white/50 backdrop-blur-3xl rounded-[1.5rem] border border-white/80 border-t-white shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-white/20/50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Riwayat KGB Pegawai</h3>
            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">{{ $riwayatKgb->count() }} Dokumen</span>
        </div>

        @if($riwayatKgb->isEmpty())
            <div class="p-10 text-center text-gray-400 space-y-2">
                <div class="text-3xl mb-2"></div>
                <p class="font-bold">Belum Ada Riwayat SK KGB</p>
                <p class="text-xs">Sistem belum mencatat riwayat KGB Anda.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-blue-200/60 text-xs text-blue-900 uppercase tracking-wider border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left whitespace-nowrap">Nomor SK</th>
                            <th class="px-6 py-3 text-left whitespace-nowrap">Tgl Ditetapkan</th>
                            <th class="px-6 py-3 text-left whitespace-nowrap">TMT Baru</th>
                            <th class="px-6 py-3 text-left whitespace-nowrap">Masa Kerja</th>
                            <th class="px-6 py-3 text-left whitespace-nowrap">Gaji Pokok Baru</th>
                            <th class="px-6 py-3 text-left whitespace-nowrap">Jatuh Tempo YAD</th>
                            <th class="px-6 py-3 text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($riwayatKgb as $index => $riwayat)
                            <tr class="hover:bg-white/40 transition">
                                <td class="px-6 py-3 font-medium text-gray-800 whitespace-nowrap">
                                    {{ $riwayat->nomor_sk_baru }}
                                    @if($index === 0)
                                        <span class="ml-2 px-2 py-0.5 text-[9px] bg-green-100 text-green-700 rounded-md font-bold">Terbaru</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-gray-600 whitespace-nowrap">{{ \Carbon\Carbon::parse($riwayat->tanggal_ditetapkan)->format('d/m/Y') }}</td>
                                <td class="px-6 py-3 text-gray-600 whitespace-nowrap">{{ \Carbon\Carbon::parse($riwayat->tmt_baru)->format('d/m/Y') }}</td>
                                <td class="px-6 py-3 text-gray-600 whitespace-nowrap">{{ $riwayat->masa_kerja_tahun_baru }} Thn, {{ $riwayat->masa_kerja_bulan_baru }} Bln</td>
                                <td class="px-6 py-3 text-gray-600 whitespace-nowrap">Rp {{ number_format($riwayat->gaji_pokok_baru, 0, ',', '.') }}</td>
                                <td class="px-6 py-3 text-gray-600 whitespace-nowrap">{{ \Carbon\Carbon::parse($riwayat->tmt_yad)->format('d/m/Y') }}</td>
                                <td class="px-6 py-3 text-center whitespace-nowrap">
                                    <a href="{{ route('pegawai.sk.download', $riwayat->id) }}" 
                                       class="inline-flex items-center gap-1 text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg transition font-medium">
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
    @endif
</div>
@endsection
