@extends('layouts.pegawai')
@section('title', 'Dashboard Pegawai')

@section('content')
<div class="space-y-6">

    @if(!$pegawai)
        {{-- ALERT JIKA DATA PEGAWAI KOSONG --}}
        <div class="bg-amber-50/50 backdrop-blur-3xl border border-amber-200/80 border-t-amber-100 text-amber-900 rounded-[1.5rem] p-6 sm:p-8 shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] flex flex-col sm:flex-row gap-5 items-start">
            <div class="w-12 h-12 rounded-xl bg-amber-100/80 flex items-center justify-center shrink-0 text-2xl text-amber-600 shadow-sm">⚠️</div>
            <div class="space-y-2">
                <h3 class="font-bold text-lg">Data Pegawai Tidak Ditemukan</h3>
                <p class="text-sm leading-relaxed text-amber-800/80">
                    Akun pengguna Anda saat ini belum terhubung atau belum disinkronisasikan dengan database Data Pegawai PNS. 
                    Silakan laporkan masalah ini ke bagian Administrator Kepegawaian (HRD) RSD Sidawangi dengan menyertakan NIP Anda untuk proses pemetaan akun.
                </p>
            </div>
        </div>
    @else
        @php
            // Cek Kelayakan KGB berdasarkan Hukuman Disiplin & Nilai SKP (2 tahun terakhir harus minimal "Baik")
            $bebasHukuman = !$pegawai->is_sedang_hukuman_disiplin;
            
            $skpTerakhir = $skpEvaluasi->take(2);
            $skpLayak = true;
            $skpCount = $skpTerakhir->count();
            
            if ($skpCount < 2) {
                $skpLayak = false;
                $skpStatusMsg = "Data SKP 2 tahun terakhir tidak lengkap";
            } else {
                foreach ($skpTerakhir as $s) {
                    if (!in_array($s->predikat, ['Baik', 'Sangat Baik'])) {
                        $skpLayak = false;
                    }
                }
                $skpStatusMsg = $skpLayak 
                    ? "Nilai SKP memenuhi syarat" 
                    : "Ada nilai SKP di bawah 'Baik'";
            }
            
            $isKgbEligible = $bebasHukuman && $skpLayak;

            // Logika Timeline KGB
            $tmtGajiTerakhir = $pegawai->tmt_gaji_terakhir ? \Carbon\Carbon::parse($pegawai->tmt_gaji_terakhir) : null;
            $jatuhTempo = $tmtGajiTerakhir ? $tmtGajiTerakhir->copy()->addYears(2) : null;
            $selisihHari = $jatuhTempo ? now()->diffInDays($jatuhTempo, false) : null;
            
            $progress = 0;
            if ($jatuhTempo) {
                $hariBerjalan = 730 - $selisihHari;
                $progress = max(0, min(100, ($hariBerjalan / 730) * 100));
            }
        @endphp

        <!-- HEADER WELCOME -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Selamat Datang, {{ explode(',', $pegawai->nama_lengkap)[0] }}</h2>
                <p class="text-sm text-gray-500">Berikut adalah ringkasan status Kenaikan Gaji Berkala Anda saat ini.</p>
            </div>
            @if($selisihHari !== null && $selisihHari <= 60 && $selisihHari >= 0)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 border border-yellow-200">
                    <span class="w-2 h-2 rounded-full bg-yellow-500 animate-pulse"></span>
                    Mendekati Jatuh Tempo
                </span>
            @endif
        </div>

        <!-- STATS CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-4">
            <!-- Waktu Menuju KGB -->
            <div class="bg-white/50 backdrop-blur-3xl rounded-[1.5rem] border border-blue-100 shadow-xl shadow-blue-500/10 p-5 flex flex-col gap-1 transition hover:shadow-2xl hover:shadow-blue-500/20 hover:-translate-y-0.5">
                <p class="text-sm text-gray-500 font-semibold mb-1">Sisa Waktu KGB</p>
                @if($selisihHari === null)
                    <p class="text-2xl font-bold text-gray-800">-</p>
                @elseif($selisihHari < 0)
                    <p class="text-2xl font-bold text-red-600">Terlewat {{ abs((int)$selisihHari) }} Hari</p>
                @else
                    <p class="text-2xl font-bold text-blue-600">H-{{ (int)$selisihHari }} Hari</p>
                @endif
                <p class="text-xs text-gray-400 mt-auto pt-2">Jatuh Tempo: <span class="font-medium text-gray-600">{{ $jatuhTempo ? $jatuhTempo->translatedFormat('d F Y') : '-' }}</span></p>
            </div>

            <!-- Gaji Pokok -->
            <div class="bg-white/50 backdrop-blur-3xl rounded-[1.5rem] border border-blue-100 shadow-xl shadow-blue-500/10 p-5 flex flex-col gap-1 transition hover:shadow-2xl hover:shadow-blue-500/20 hover:-translate-y-0.5">
                <p class="text-sm text-gray-500 font-semibold mb-1">Gaji Pokok Saat Ini</p>
                <p class="text-2xl font-bold text-emerald-600">Rp {{ number_format($pegawai->gaji_pokok_terakhir, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mt-auto pt-2">TMT Terakhir: <span class="font-medium text-gray-600">{{ $tmtGajiTerakhir ? $tmtGajiTerakhir->translatedFormat('d F Y') : '-' }}</span></p>
            </div>

            <!-- Predikat SKP Terakhir -->
            <div class="bg-white/50 backdrop-blur-3xl rounded-[1.5rem] border border-blue-100 shadow-xl shadow-blue-500/10 p-5 flex flex-col gap-1 transition hover:shadow-2xl hover:shadow-blue-500/20 hover:-translate-y-0.5">
                <p class="text-sm text-gray-500 font-semibold mb-1">Nilai SKP Terakhir</p>
                @php $lastSkp = $skpTerakhir->first(); @endphp
                @if($lastSkp)
                    <p class="text-2xl font-bold text-gray-800">{{ $lastSkp->predikat }}</p>
                    <p class="text-xs text-gray-400 mt-auto pt-2">Tahun Penilaian: <span class="font-medium text-gray-600">{{ $lastSkp->tahun_penilaian }}</span></p>
                @else
                    <p class="text-2xl font-bold text-gray-800">-</p>
                    <p class="text-xs text-gray-400 mt-auto pt-2">Data belum tersedia</p>
                @endif
            </div>
        </div>

        <!-- PROGRESS BAR & STATUS KELAYAKAN -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white/50 backdrop-blur-3xl rounded-[1.5rem] border border-blue-100 shadow-xl shadow-blue-500/10 p-6 transition hover:shadow-2xl hover:shadow-blue-500/20">
                <h3 class="font-semibold text-gray-800 mb-6">Progres Kenaikan Gaji Berkala (Siklus 2 Tahun)</h3>
                @if($tmtGajiTerakhir)
                    <div class="relative w-full bg-blue-100/50 rounded-full h-4 mb-2 overflow-hidden shadow-inner border border-blue-200/30">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-4 rounded-full transition-all duration-1000 ease-out relative" style="width: {{ $progress }}%">
                            <div class="absolute inset-0 bg-white/20 w-full animate-[shimmer_2s_infinite]"></div>
                        </div>
                    </div>
                    <div class="flex justify-between text-[11px] text-gray-500 font-medium mt-3">
                        <div class="flex flex-col items-start">
                            <span class="text-gray-700 font-bold">{{ $tmtGajiTerakhir->translatedFormat('M Y') }}</span>
                            <span>TMT Terakhir</span>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-gray-700 font-bold">{{ $jatuhTempo->translatedFormat('M Y') }}</span>
                            <span>Jatuh Tempo</span>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic text-center py-6">Data TMT belum tersedia untuk menghitung progres.</p>
                @endif
            </div>
            
            <div class="bg-white/50 backdrop-blur-3xl rounded-[1.5rem] border border-blue-100 shadow-xl shadow-blue-500/10 p-6 flex flex-col justify-center items-center text-center transition hover:shadow-2xl hover:shadow-blue-500/20">
                <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">Status Kelayakan</h3>
                @if($isKgbEligible)
                    <div class="w-16 h-16 bg-emerald-100/80 text-emerald-600 rounded-full flex items-center justify-center text-3xl mb-3 shadow-sm border border-emerald-200">✓</div>
                    <p class="text-lg font-bold text-emerald-700">Memenuhi Syarat</p>
                    <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">Sistem akan secara otomatis memproses usulan KGB Anda sesuai jadwal.</p>
                @else
                    <div class="w-16 h-16 bg-red-100/80 text-red-600 rounded-full flex items-center justify-center text-3xl mb-3 shadow-sm border border-red-200">✗</div>
                    <p class="text-lg font-bold text-red-700">Belum Memenuhi</p>
                    <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">Ada syarat yang kurang (Hukuman Disiplin atau nilai SKP).</p>
                @endif
            </div>
        </div>

        <!-- TABEL DATA PEGAWAI -->
        <div class="bg-white/50 backdrop-blur-3xl rounded-[1.5rem] border border-blue-100 shadow-xl shadow-blue-500/10 overflow-hidden mt-2 relative transition hover:shadow-2xl hover:shadow-blue-500/20">
            <div class="h-3 bg-gradient-to-r from-[#0B3E6A] to-[#234A9F] relative"></div>
            <div class="px-6 pb-6 relative">
                <div class="pt-6 flex flex-col md:flex-row justify-between md:items-end gap-4">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800 leading-tight">{{ $pegawai->nama_lengkap }}</h3>
                        <p class="text-blue-600 font-mono text-sm mt-1">{{ $pegawai->nip }}</p>
                    </div>
                    <div class="flex gap-3">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800 border border-white/50">
                            {{ ($pegawai->pangkat ?? '-') . ' (' . ($pegawai->golongan ?? '-') . ')' }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
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
                                    <td class="py-2 text-gray-500">Pangkat</td>
                                    <td class="py-2 text-gray-800 font-medium">{{ $pegawai->pangkat ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-500">Golongan</td>
                                    <td class="py-2 text-gray-800 font-medium">{{ $pegawai->golongan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-500">Hukuman Disiplin</td>
                                    <td class="py-2 text-gray-800 font-medium">
                                        @if($pegawai->is_sedang_hukuman_disiplin)
                                            <span class="text-red-600 font-semibold">Ya (Sedang Menjalani)</span>
                                        @else
                                            <span class="text-green-600">Tidak</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-500">Email</td>
                                    <td class="py-2 text-gray-800 font-medium">{{ $pegawai->user->email ?? '-' }}</td>
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
                                    <td class="py-2 text-gray-500 w-1/3">Kantor Tempat Kerja</td>
                                    <td class="py-2 text-gray-800 font-medium">{{ $pegawai->kantor_tempat_kerja ?? '-' }}</td>
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
                                    <td class="py-2 text-gray-800 font-medium">{{ $tmtGajiTerakhir ? $tmtGajiTerakhir->translatedFormat('d F Y') : '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    @endif
</div>
@endsection
