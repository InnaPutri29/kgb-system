@extends('layouts.pegawai')
@section('title', 'Riwayat Kenaikan Gaji Berkala')

@section('content')
<div class="space-y-6">

    {{-- ALERT JIKA DATA PEGAWAI KOSONG --}}
    @if(!$pegawai)
        <div class="bg-amber-50 border border-amber-200 text-amber-900 rounded-2xl p-6 shadow-sm flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center shrink-0 text-xl text-amber-600">⚠️</div>
            <div>
                <h3 class="font-bold text-lg">Data Pegawai Tidak Ditemukan</h3>
                <p class="text-sm">Akun Anda belum terhubung dengan data PNS. Silakan hubungi admin kepegawaian.</p>
            </div>
        </div>
    @else

    @php
        $tmtGajiTerakhir = $pegawai->tmt_gaji_terakhir ? \Carbon\Carbon::parse($pegawai->tmt_gaji_terakhir) : null;
        $jatuhTempo = $tmtGajiTerakhir ? $tmtGajiTerakhir->copy()->addYears(2) : null;
        $selisihHari = $jatuhTempo ? now()->diffInDays($jatuhTempo, false) : null;
    @endphp

    {{-- CARD JADWAL TMT KGB NEXT --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 max-w-lg">
        <h3 class="font-bold text-gray-800 flex items-center gap-2 mb-4">
            <span class="text-indigo-600">⏱️</span> Estimasi Jadwal KGB
        </h3>
        
        @if(!$tmtGajiTerakhir)
            <p class="text-gray-400 text-sm italic">Data TMT Gaji Terakhir belum terisi.</p>
        @else
            <div class="space-y-2">
                <p class="text-xs font-bold text-gray-400 uppercase">TMT KGB Berikutnya</p>
                <p class="text-2xl font-black text-gray-800">{{ $jatuhTempo->translatedFormat('d F Y') }}</p>
                <p class="text-xs text-gray-500">TMT Terakhir: {{ $tmtGajiTerakhir->translatedFormat('d F Y') }}</p>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-100">
                @if($selisihHari < 0)
                    <span class="inline-flex px-3 py-1 bg-red-50 text-red-700 font-bold text-xs rounded-xl border border-red-100">
                        Terlewat {{ abs((int)$selisihHari) }} hari
                    </span>
                @elseif($selisihHari <= 60)
                    <span class="inline-flex px-3 py-1 bg-yellow-50 text-yellow-700 font-bold text-xs rounded-xl border border-yellow-100">
                        Jatuh Tempo (H-{{ (int)$selisihHari }})
                    </span>
                @else
                    <span class="inline-flex px-3 py-1 bg-blue-50 text-blue-700 font-bold text-xs rounded-xl border border-blue-100">
                        Akan Datang (H-{{ (int)$selisihHari }})
                    </span>
                @endif
            </div>
        @endif
    </div>

    {{-- ARSIP SK KGB --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-800">Riwayat SK Kenaikan Gaji Berkala</h3>
                <p class="text-xs text-gray-500">Daftar SK KGB yang telah diterbitkan untuk Anda.</p>
            </div>
            <span class="bg-indigo-50 text-indigo-700 text-xs font-bold px-2.5 py-1 rounded-full">{{ $riwayatKgb->count() }} Data</span>
        </div>

        @if($riwayatKgb->isEmpty())
            <div class="p-10 text-center text-gray-400 space-y-2">
                <div class="text-3xl mb-2">📄</div>
                <p class="font-bold">Belum Ada Riwayat SK KGB</p>
                <p class="text-xs">Sistem belum mencatat riwayat KGB Anda.</p>
            </div>
        @else
            <div class="divide-y divide-gray-100">
                @foreach($riwayatKgb as $index => $riwayat)
                    <div class="p-5 hover:bg-gray-50 transition flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">📄</div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm">
                                    No. {{ $riwayat->nomor_sk_baru }}
                                    @if($index === 0) <span class="ml-2 px-2 py-0.5 text-[9px] bg-green-100 text-green-700 rounded-md">Terbaru</span> @endif
                                </h4>
                                <p class="text-xs text-gray-500 mt-0.5">Ditetapkan: {{ \Carbon\Carbon::parse($riwayat->tanggal_ditetapkan)->translatedFormat('d F Y') }}</p>
                                
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span class="text-[10px] font-bold bg-gray-100 px-2 py-0.5 rounded-md">📅 TMT: {{ \Carbon\Carbon::parse($riwayat->tmt_baru)->format('d/m/Y') }}</span>
                                    <span class="text-[10px] font-bold bg-green-50 text-green-700 px-2 py-0.5 rounded-md">💰 Rp {{ number_format($riwayat->gaji_pokok_baru, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('pegawai.sk.download', $riwayat->id) }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 border border-gray-200 text-xs font-bold rounded-lg hover:bg-blue-50 hover:text-blue-600 transition">
                           Unduh PDF
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    @endif
</div>
@endsection
