@extends('layouts.pegawai')
@section('title', 'Evaluasi SKP')

@section('content')
<div class="space-y-6">
    
    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Evaluasi SKP</h2>
            <p class="text-sm text-gray-500 mt-1">Rekapitulasi penilaian Sasaran Kinerja Pegawai (SKP) Anda dari tahun ke tahun.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200 flex-1 sm:flex-none justify-center">
                {{ $skpEvaluasi->count() }} Data
            </span>
        </div>
    </div>

    @if(!$pegawai)
        <div class="bg-amber-50/50 backdrop-blur-3xl border border-amber-200/80 border-t-amber-100 text-amber-900 rounded-[1.5rem] p-6 shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-amber-100/80 shadow-sm flex items-center justify-center shrink-0 text-xl text-amber-600">⚠️</div>
            <div>
                <h3 class="font-bold text-lg">Data Pegawai Tidak Ditemukan</h3>
                <p class="text-sm text-amber-800/80">Akun Anda belum terhubung dengan data PNS. Silakan hubungi admin kepegawaian.</p>
            </div>
        </div>
    @else


    {{-- RIWAYAT PENILAIAN SKP --}}
    <div class="bg-white/50 backdrop-blur-3xl rounded-[1.5rem] border border-white/80 border-t-white shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] overflow-hidden">
        @if($skpEvaluasi->isEmpty())
            <div class="p-10 text-center text-gray-400 space-y-2">
                <div class="text-3xl mb-2">📊</div>
                <p class="font-bold">Belum Ada Rekap SKP</p>
                <p class="text-xs">Data penilaian kinerja tahunan Anda belum diunggah oleh Admin.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-blue-200/60 text-xs text-blue-900 uppercase tracking-wider border-b border-gray-100">
                        <tr>
                            <th class="px-4 lg:px-6 py-3 text-center whitespace-nowrap">Tahun</th>
                            <th class="px-4 lg:px-6 py-3 text-center whitespace-nowrap">Predikat</th>
                            <th class="px-4 lg:px-6 py-3 text-center whitespace-nowrap">Status Kelayakan KGB</th>
                            <th class="px-4 lg:px-6 py-3 text-center whitespace-nowrap">Berkas SKP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($skpEvaluasi as $skp)
                            <tr class="hover:bg-white/40 transition">
                                <td class="px-4 lg:px-6 py-3 lg:py-4 font-bold text-gray-800 text-center whitespace-nowrap">
                                    {{ $skp->tahun_penilaian }}
                                    @if($skp->tahun_penilaian == $tahunBerjalan)
                                        <span class="ml-2 px-2 py-0.5 text-[9px] bg-indigo-100 text-indigo-700 rounded-md">Aktif</span>
                                    @endif
                                </td>
                                <td class="px-4 lg:px-6 py-3 lg:py-4 text-center whitespace-nowrap">
                                    @php
                                        $badgeColor = $colors[$skp->predikat] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $badgeColor }}">
                                        {{ $skp->predikat }}
                                    </span>
                                </td>
                                <td class="px-4 lg:px-6 py-3 lg:py-4 text-center whitespace-nowrap">
                                    @if(in_array($skp->predikat, ['Baik', 'Sangat Baik']))
                                        <span class="text-emerald-600 text-xs font-bold">✓ Memenuhi</span>
                                    @else
                                        <span class="text-red-600 text-xs font-bold">✗ Di bawah syarat</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-center whitespace-nowrap">
                                    @if($skp->file_bukti_skp)
                                        <a href="{{ Storage::url($skp->file_bukti_skp) }}" target="_blank"
                                           class="inline-flex items-center gap-1 text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg transition font-medium">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Lihat
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Tidak ada</span>
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
