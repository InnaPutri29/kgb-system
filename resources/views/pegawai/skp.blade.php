@extends('layouts.pegawai')
@section('title', 'Evaluasi SKP')

@section('content')
<div class="space-y-6">

    @if(!$pegawai)
        <div class="bg-amber-50 border border-amber-200 text-amber-900 rounded-2xl p-6 shadow-sm flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center shrink-0 text-xl text-amber-600">⚠️</div>
            <div>
                <h3 class="font-bold text-lg">Data Pegawai Tidak Ditemukan</h3>
                <p class="text-sm">Akun Anda belum terhubung dengan data PNS. Silakan hubungi admin kepegawaian.</p>
            </div>
        </div>
    @else

    {{-- CARD SKP PERIODE BERJALAN --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 max-w-lg">
        <h3 class="font-bold text-gray-800 flex items-center gap-2 mb-4">
            <span class="text-emerald-600">📊</span> SKP Periode Berjalan (Tahun {{ $tahunBerjalan }})
        </h3>
        
        @if(!$skpPeriodeBerjalan)
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-yellow-400"></div>
                    <span class="text-sm font-semibold text-gray-600">Proses penilaian oleh Atasan</span>
                </div>
                <p class="text-xs text-gray-500">Penilaian biasanya diterbitkan di akhir tahun penilaian berjalan.</p>
            </div>
        @else
            <div class="space-y-3">
                @php
                    $colors = [
                        'Sangat Baik' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                        'Baik'        => 'bg-blue-100 text-blue-800 border-blue-200',
                        'Cukup'       => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                        'Kurang'      => 'bg-orange-100 text-orange-800 border-orange-200',
                        'Sangat Kurang' => 'bg-red-100 text-red-800 border-red-200',
                    ];
                    $colorClass = $colors[$skpPeriodeBerjalan->predikat] ?? 'bg-gray-100 text-gray-800';
                @endphp
                <p class="text-xs font-bold text-gray-400 uppercase">Predikat SKP</p>
                <span class="inline-flex items-center px-4 py-1.5 rounded-lg font-black border {{ $colorClass }}">
                    ★ {{ $skpPeriodeBerjalan->predikat }}
                </span>
                
                <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500">Dokumen Bukti:</span>
                    @if($skpPeriodeBerjalan->file_bukti_skp)
                        <a href="{{ Storage::url($skpPeriodeBerjalan->file_bukti_skp) }}" target="_blank"
                           class="text-sm font-bold text-emerald-600 hover:text-emerald-800 transition">
                            📄 Lihat File
                        </a>
                    @else
                        <span class="text-xs text-gray-400 italic">Belum diunggah</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- RIWAYAT PENILAIAN SKP --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-800">Rekapitulasi Evaluasi SKP</h3>
                <p class="text-xs text-gray-500">Daftar nilai predikat Sasaran Kinerja Pegawai tahun sebelumnya.</p>
            </div>
            <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2.5 py-1 rounded-full">{{ $skpEvaluasi->count() }} Data</span>
        </div>

        @if($skpEvaluasi->isEmpty())
            <div class="p-10 text-center text-gray-400 space-y-2">
                <div class="text-3xl mb-2">📊</div>
                <p class="font-bold">Belum Ada Rekap SKP</p>
                <p class="text-xs">Data penilaian kinerja tahunan Anda belum diunggah oleh Admin.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Tahun</th>
                            <th class="px-6 py-3">Predikat</th>
                            <th class="px-6 py-3">Status Kelayakan KGB</th>
                            <th class="px-6 py-3 text-right">Berkas SKP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($skpEvaluasi as $skp)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-bold text-gray-800">
                                    {{ $skp->tahun_penilaian }}
                                    @if($skp->tahun_penilaian == $tahunBerjalan)
                                        <span class="ml-2 px-2 py-0.5 text-[9px] bg-indigo-100 text-indigo-700 rounded-md">Aktif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $badgeColor = $colors[$skp->predikat] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $badgeColor }}">
                                        {{ $skp->predikat }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if(in_array($skp->predikat, ['Baik', 'Sangat Baik']))
                                        <span class="text-emerald-600 text-xs font-bold">✓ Memenuhi</span>
                                    @else
                                        <span class="text-red-600 text-xs font-bold">✗ Di bawah syarat</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($skp->file_bukti_skp)
                                        <a href="{{ Storage::url($skp->file_bukti_skp) }}" target="_blank"
                                           class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">Lihat Berkas</a>
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
