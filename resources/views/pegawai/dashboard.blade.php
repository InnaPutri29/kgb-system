<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Pegawai - KGB System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased flex flex-col min-h-screen">

    {{-- NAVBAR --}}
    <nav class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-blue-800 text-white flex items-center justify-center text-xl shadow-inner">
                        🏥
                    </div>
                    <div>
                        <span class="font-bold text-gray-800 block leading-tight">Portal Pegawai</span>
                        <span class="text-xs text-gray-500 block leading-tight">RSD Sidawangi</span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-medium text-gray-800 leading-tight">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 leading-tight">Pegawai</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition bg-gray-50 hover:bg-red-50 rounded-lg" title="Keluar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        @if(!$pegawai)
            {{-- ALERT JIKA DATA PEGAWAI KOSONG --}}
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-2xl p-6 shadow-sm flex gap-4">
                <svg class="w-8 h-8 text-yellow-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div>
                    <h3 class="font-bold text-lg">Data Pegawai Tidak Ditemukan</h3>
                    <p class="mt-1 text-yellow-700">Akun Anda belum terhubung dengan data kepegawaian. Silakan hubungi Administrator atau Bagian Kepegawaian untuk melakukan sinkronisasi data NIP Anda.</p>
                </div>
            </div>
        @else
            <div class="flex flex-col md:flex-row gap-8 align-start">

                {{-- KOLOM KIRI: PROFIL PEGAWAI --}}
                <div class="w-full md:w-1/3 shrink-0 space-y-6">
                    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden sticky top-24">
                        <div class="h-24 bg-gradient-to-r from-blue-600 to-indigo-700"></div>
                        <div class="px-6 pb-6 relative">
                            <div class="w-20 h-20 bg-white rounded-2xl p-1 shadow-md absolute -top-10 left-6 flex items-center justify-center text-3xl font-bold text-blue-600 border border-gray-100">
                                {{ substr($pegawai->nama_lengkap, 0, 1) }}
                            </div>
                            
                            <div class="pt-14 space-y-1">
                                <h2 class="text-xl font-bold text-gray-800 leading-tight">{{ $pegawai->nama_lengkap }}</h2>
                                <p class="text-blue-600 font-mono text-sm">{{ $pegawai->nip }}</p>
                                <p class="text-gray-500 text-sm mt-2">{{ $pegawai->jabatan ?? 'Jabatan belum diisi' }}</p>
                            </div>

                            <hr class="my-5 border-gray-100">

                            <div class="space-y-4">
                                <div>
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Pangkat / Golongan</p>
                                    <p class="text-sm text-gray-800 font-medium">{{ ($pegawai->pangkat ?? '-') . ' (' . ($pegawai->golongan ?? '-') . ')' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Kantor Tempat Kerja</p>
                                    <p class="text-sm text-gray-800 font-medium">{{ $pegawai->kantor_tempat_kerja ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Masa Kerja</p>
                                    <p class="text-sm text-gray-800 font-medium">{{ $pegawai->masa_kerja_tahun }} Thn, {{ $pegawai->masa_kerja_bulan }} Bln</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN: STATUS KGB & RIWAYAT --}}
                <div class="flex-1 space-y-6">

                    {{-- CARD STATUS KGB MENDATANG --}}
                    @php
                        $tmtGajiTerakhir = $pegawai->tmt_gaji_terakhir ? \Carbon\Carbon::parse($pegawai->tmt_gaji_terakhir) : null;
                        $jatuhTempo = $tmtGajiTerakhir ? $tmtGajiTerakhir->copy()->addYears(2) : null;
                        $selisihHari = $jatuhTempo ? now()->diffInDays($jatuhTempo, false) : null;
                    @endphp

                    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8 relative overflow-hidden">
                        <div class="absolute -right-10 -top-10 w-40 h-40 bg-blue-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
                        
                        <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Informasi KGB Mendatang
                        </h3>

                        @if(!$tmtGajiTerakhir)
                            <div class="text-gray-500 text-sm">Data TMT Gaji Terakhir belum tersedia.</div>
                        @else
                            <div class="grid sm:grid-cols-2 gap-6">
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">TMT Gaji Terakhir</p>
                                    <p class="font-semibold text-gray-800">{{ $tmtGajiTerakhir->translatedFormat('d F Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Estimasi Jatuh Tempo KGB Berikutnya</p>
                                    <p class="font-semibold text-gray-800">{{ $jatuhTempo->translatedFormat('d F Y') }}</p>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                @if($selisihHari < 0)
                                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-50 text-red-700 font-medium text-sm border border-red-100">
                                        <span class="relative flex h-3 w-3"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span><span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span></span>
                                        KGB Anda Terlambat {{ abs((int)$selisihHari) }} hari
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">Sedang dalam proses penerbitan SK oleh kepegawaian.</p>
                                @elseif($selisihHari <= 60)
                                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-yellow-50 text-yellow-700 font-medium text-sm border border-yellow-100">
                                        <span class="relative flex h-3 w-3"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span><span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span></span>
                                        Mendekati Jatuh Tempo (H-{{ (int)$selisihHari }})
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">Nama Anda sudah masuk daftar nominatif dan sedang diproses.</p>
                                @else
                                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-green-50 text-green-700 font-medium text-sm border border-green-100">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        KGB Anda masih jauh (H-{{ (int)$selisihHari }})
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- DAFTAR RIWAYAT SK KGB --}}
                    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-6 sm:px-8 py-5 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="text-lg font-bold text-gray-800">Riwayat Surat Keputusan (SK) KGB</h3>
                        </div>

                        @if($riwayatKgb->isEmpty())
                            <div class="p-8 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p>Belum ada riwayat SK KGB di sistem.</p>
                            </div>
                        @else
                            <ul class="divide-y divide-gray-100">
                                @foreach($riwayatKgb as $riwayat)
                                    <li class="p-6 sm:p-8 hover:bg-gray-50/50 transition duration-150 flex flex-col sm:flex-row gap-5 items-start sm:items-center justify-between group">
                                        <div class="flex gap-4 items-start">
                                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100 group-hover:scale-110 transition-transform">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-800 text-base mb-1">SK Nomor: {{ $riwayat->nomor_sk_baru }}</h4>
                                                <p class="text-sm text-gray-500 mb-2">Ditetapkan pada: {{ \Carbon\Carbon::parse($riwayat->tanggal_ditetapkan)->translatedFormat('d F Y') }}</p>
                                                <div class="flex flex-wrap gap-2">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        TMT: {{ \Carbon\Carbon::parse($riwayat->tmt_baru)->format('d/m/Y') }}
                                                    </span>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Rp {{ number_format($riwayat->gaji_pokok_baru, 0, ',', '.') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w-full sm:w-auto mt-4 sm:mt-0">
                                            <a href="{{ route('pegawai.sk.download', $riwayat->id) }}" 
                                               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 hover:text-blue-600 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                Unduh PDF
                                            </a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

            </div>
        @endif
    </main>

    {{-- FOOTER --}}
    <footer class="bg-white border-t border-gray-200 mt-auto py-6 text-center">
        <p class="text-xs text-gray-400">© {{ date('Y') }} Sistem Kenaikan Gaji Berkala - RSD Sidawangi. Hak Cipta Dilindungi.</p>
    </footer>

</body>
</html>
