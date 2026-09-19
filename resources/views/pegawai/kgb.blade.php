@extends('layouts.pegawai')
@section('title', 'Riwayat Kenaikan Gaji Berkala')

@section('content')
<div class="space-y-6">

    {{-- TOAST NOTIFIKASI --}}
    <div id="toast-success"
         class="fixed bottom-6 right-6 z-50 hidden items-center gap-3 bg-white border border-green-200 text-green-800 shadow-lg rounded-xl px-4 py-3 transition-all duration-300">
        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-semibold">Unduhan Berhasil</p>
            <p class="text-xs text-green-600">SK KGB berhasil diunduh.</p>
        </div>
        <button onclick="hideToast()" class="ml-2 text-green-400 hover:text-green-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Riwayat Kenaikan Gaji Berkala</h2>
            <p class="text-sm text-gray-500 mt-1">Daftar dokumen Surat Keputusan (SK) KGB yang telah diterbitkan untuk Anda.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200 flex-1 sm:flex-none justify-center">
                {{ $riwayatKgb->count() }} Dokumen
            </span>
        </div>
    </div>

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
    <div class="bg-white/50 backdrop-blur-3xl lg:bg-white lg:backdrop-blur-none rounded-[1.5rem] border border-blue-100 lg:border-slate-100 shadow-xl shadow-blue-500/10 lg:shadow-sm lg:shadow-black/5 overflow-hidden transition hover:shadow-2xl hover:shadow-blue-500/20 lg:hover:shadow-md lg:hover:shadow-black/10">
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
                            <th class="px-6 py-3 text-center whitespace-nowrap">Tgl Ditetapkan</th>
                            <th class="px-6 py-3 text-center whitespace-nowrap">TMT Baru</th>
                            <th class="px-6 py-3 text-center whitespace-nowrap">Gaji Pokok Baru</th>
                            <th class="px-6 py-3 text-center whitespace-nowrap">Status</th>
                            <th class="px-6 py-3 text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($riwayatKgb as $index => $riwayat)
                            <tr class="hover:bg-white/40 transition">
                                <td class="px-6 py-3 font-medium text-gray-800 text-left whitespace-nowrap">
                                    {{ $riwayat->nomor_sk_baru }}
                                    @if($index === 0)
                                        <span class="ml-2 px-2 py-0.5 text-[9px] bg-green-100 text-green-700 rounded-md font-bold">Terbaru</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-gray-600 text-center whitespace-nowrap">{{ \Carbon\Carbon::parse($riwayat->tanggal_ditetapkan)->format('d/m/Y') }}</td>
                                <td class="px-6 py-3 text-gray-600 text-center whitespace-nowrap">{{ \Carbon\Carbon::parse($riwayat->tmt_baru)->format('d/m/Y') }}</td>
                                <td class="px-6 py-3 text-gray-600 text-center whitespace-nowrap">Rp {{ number_format($riwayat->gaji_pokok_baru, 0, ',', '.') }}</td>
                                <td class="px-6 py-3 text-gray-600 text-center whitespace-nowrap">
                                    @if($riwayat->status === 'Final')
                                        <span class="px-2 py-1 rounded-md text-xs font-medium bg-green-50 text-green-700 border border-green-100">Final (TTE)</span>
                                    @else
                                        <span class="px-2 py-1 rounded-md text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-100">Draf / Proses TTE</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-center whitespace-nowrap">
                                     @if($riwayat->status === 'Final' && $riwayat->file_sk_final)
                                        <a href="{{ Storage::url($riwayat->file_sk_final) }}" target="_blank"
                                           onclick="window.dispatchEvent(new CustomEvent('show-toast', { detail: { message: 'Dokumen SK KGB Final berhasil diunduh!', type: 'success' } }))"
                                           class="inline-flex items-center gap-1 text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg transition font-medium">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            Unduh Final
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Menunggu TTE</span>
                                    @endif
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
