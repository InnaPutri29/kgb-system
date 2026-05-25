<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Pegawai - KGB System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
        
        .shimmer {
            background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>
</head>
<body class="bg-slate-50/50 font-sans antialiased flex flex-col min-h-screen"
      x-data="{ 
          toast: { show: false, message: '', type: 'success' }, 
          showToast(msg, type = 'success') { 
              this.toast.message = msg; 
              this.toast.type = type; 
              this.toast.show = true; 
              setTimeout(() => { this.toast.show = false; }, 4000); 
          } 
      }" 
      @show-toast.window="showToast($event.detail.message, $event.detail.type || 'success')">

    {{-- NAVBAR --}}
    <nav class="bg-white/80 backdrop-blur-md border-b border-slate-100 shadow-sm sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-600 to-blue-700 text-white flex items-center justify-center text-xl shadow-md shadow-indigo-100">
                        💼
                    </div>
                    <div>
                        <span class="font-extrabold text-slate-800 block leading-tight tracking-tight">Portal Pegawai</span>
                        <span class="text-xs font-semibold text-indigo-600 block leading-tight">RSD Sidawangi</span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-slate-800 leading-tight">{{ auth()->user()->name }}</p>
                        <div class="flex items-center gap-1.5 justify-end mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">PNS Aktif</span>
                        </div>
                    </div>
                    
                    <div class="h-8 w-px bg-slate-100 hidden sm:block"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition duration-200" title="Keluar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        {{-- WELCOME BANNER --}}
        @if($pegawai)
            <div class="relative rounded-3xl overflow-hidden bg-gradient-to-r from-indigo-600 via-blue-600 to-indigo-700 text-white p-6 sm:p-8 shadow-xl shadow-indigo-100">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-white/10 via-transparent to-transparent pointer-events-none"></div>
                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="space-y-1.5">
                        <span class="px-3 py-1 rounded-full bg-white/15 text-white text-xs font-semibold uppercase tracking-wider backdrop-blur-sm">
                            Dashboard Kepegawaian
                        </span>
                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Selamat Datang, {{ $pegawai->nama_lengkap }}</h1>
                        <p class="text-indigo-100 text-sm sm:text-base max-w-2xl font-medium">
                            Pantau secara mandiri nilai evaluasi SKP tahun berjalan, estimasi jadwal KGB Anda berikutnya, serta unduh arsip SK KGB secara langsung.
                        </p>
                    </div>
                    <div class="flex items-center gap-3 bg-white/10 px-5 py-3 rounded-2xl backdrop-blur-md border border-white/10 self-start md:self-auto">
                        <span class="text-2xl">📅</span>
                        <div>
                            <span class="text-[10px] font-bold text-indigo-200 block uppercase tracking-wider leading-none">Periode Berjalan</span>
                            <span class="text-sm font-bold block mt-1">Tahun {{ $tahunBerjalan }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(!$pegawai)
            {{-- ALERT JIKA DATA PEGAWAI KOSONG --}}
            <div class="bg-amber-50 border border-amber-200 text-amber-900 rounded-3xl p-6 sm:p-8 shadow-sm flex flex-col sm:flex-row gap-5 items-start">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center shrink-0 text-2xl text-amber-600">
                    ⚠️
                </div>
                <div class="space-y-2">
                    <h3 class="font-bold text-lg text-amber-900">Data Pegawai Tidak Ditemukan</h3>
                    <p class="text-amber-800 text-sm leading-relaxed">
                        Akun pengguna Anda saat ini belum terhubung atau belum disinkronisasikan dengan database Data Pegawai PNS. 
                        Silakan laporkan masalah ini ke bagian Administrator Kepegawaian (HRD) RSD Sidawangi dengan menyertakan NIP Anda untuk proses pemetaan akun.
                    </p>
                </div>
            </div>
        @else
            @php
                // Hitung data KGB Mendatang
                $tmtGajiTerakhir = $pegawai->tmt_gaji_terakhir ? \Carbon\Carbon::parse($pegawai->tmt_gaji_terakhir) : null;
                $jatuhTempo = $tmtGajiTerakhir ? $tmtGajiTerakhir->copy()->addYears(2) : null;
                $selisihHari = $jatuhTempo ? now()->diffInDays($jatuhTempo, false) : null;

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
                        ? "Nilai SKP memenuhi syarat (Minimal predikat 'Baik' 2 tahun terakhir)" 
                        : "Ada nilai SKP di bawah predikat 'Baik' dalam 2 tahun terakhir";
                }
                
                $isKgbEligible = $bebasHukuman && $skpLayak;
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

                {{-- KOLOM KIRI: PROFIL PEGAWAI & CHECKLIST KGB --}}
                <div class="space-y-6 lg:col-span-1">
                    
                    {{-- CARD PROFIL --}}
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden relative">
                        <div class="h-28 bg-gradient-to-tr from-indigo-500 to-indigo-800 relative">
                            <div class="absolute inset-0 bg-white/10 opacity-30 pattern-grid-lg"></div>
                        </div>
                        <div class="px-6 pb-6 relative">
                            <div class="w-24 h-24 bg-white rounded-2xl p-1.5 shadow-lg absolute -top-12 left-6 flex items-center justify-center border border-slate-100 overflow-hidden">
                                <div class="w-full h-full rounded-xl bg-gradient-to-tr from-indigo-50 to-indigo-100 text-indigo-600 flex items-center justify-center font-black text-4xl shadow-inner">
                                    {{ substr($pegawai->nama_lengkap, 0, 1) }}
                                </div>
                            </div>
                            
                            <div class="pt-16 space-y-1">
                                <h2 class="text-xl font-bold text-slate-800 leading-tight">{{ $pegawai->nama_lengkap }}</h2>
                                <p class="text-indigo-600 font-mono text-sm font-semibold tracking-wider">{{ $pegawai->nip }}</p>
                                <div class="pt-2">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ $pegawai->jabatan ?? 'Jabatan Belum Diisi' }}
                                    </span>
                                </div>
                            </div>

                            <hr class="my-5 border-slate-100">

                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Pangkat / Golongan</p>
                                        <p class="text-sm text-slate-800 font-bold">{{ $pegawai->pangkat ?? '-' }}</p>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-mono text-xs font-extrabold border border-slate-200">
                                        {{ $pegawai->golongan ?? '-' }}
                                    </span>
                                </div>
                                
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Unit Kerja / Instansi</p>
                                    <p class="text-sm text-slate-800 font-semibold">{{ $pegawai->kantor_tempat_kerja ?? '-' }}</p>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Masa Kerja (Gol)</p>
                                        <p class="text-sm text-slate-800 font-bold">
                                            {{ $pegawai->masa_kerja_tahun }} <span class="text-xs font-medium text-slate-500">Thn</span>, 
                                            {{ $pegawai->masa_kerja_bulan }} <span class="text-xs font-medium text-slate-500">Bln</span>
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Gaji Pokok Terakhir</p>
                                        <p class="text-sm text-indigo-600 font-extrabold">
                                            Rp {{ number_format($pegawai->gaji_pokok_terakhir, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- CARD PERSYARATAN & KELAYAKAN KGB --}}
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 space-y-5">
                        <div class="space-y-1">
                            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                                🛡️ Kriteria Kelayakan KGB
                            </h3>
                            <p class="text-xs text-slate-400">Verifikasi berkala kelayakan Kenaikan Gaji Berkala PNS.</p>
                        </div>
                        
                        <div class="space-y-4">
                            {{-- Check Status Hukuman Disiplin --}}
                            <div class="flex gap-3 items-start p-3 rounded-2xl {{ $bebasHukuman ? 'bg-emerald-50/50 border border-emerald-100' : 'bg-rose-50/50 border border-rose-100' }}">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 text-sm font-bold shadow-inner {{ $bebasHukuman ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {!! $bebasHukuman ? '✓' : '✗' !!}
                                </div>
                                <div class="space-y-0.5">
                                    <p class="text-xs font-bold text-slate-700">Status Disiplin</p>
                                    <p class="text-[11px] font-medium {{ $bebasHukuman ? 'text-emerald-700' : 'text-rose-700' }}">
                                        {{ $bebasHukuman ? 'Bebas dari hukuman disiplin' : 'Sedang dalam masa hukuman disiplin' }}
                                    </p>
                                </div>
                            </div>

                            {{-- Check Status SKP 2 Tahun terakhir --}}
                            <div class="flex gap-3 items-start p-3 rounded-2xl {{ $skpLayak ? 'bg-emerald-50/50 border border-emerald-100' : 'bg-amber-50/50 border border-amber-100' }}">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 text-sm font-bold shadow-inner {{ $skpLayak ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {!! $skpLayak ? '✓' : 'i' !!}
                                </div>
                                <div class="space-y-0.5">
                                    <p class="text-xs font-bold text-slate-700">Evaluasi Kinerja (SKP)</p>
                                    <p class="text-[11px] font-medium leading-relaxed {{ $skpLayak ? 'text-emerald-700' : 'text-amber-700' }}">
                                        {{ $skpStatusMsg }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <hr class="border-slate-100">

                        {{-- KESIMPULAN KELAYAKAN --}}
                        <div class="p-4 rounded-2xl text-center space-y-2 {{ $isKgbEligible ? 'bg-gradient-to-tr from-emerald-500 to-teal-600 text-white shadow-md shadow-emerald-100' : 'bg-slate-50 border border-slate-150 text-slate-700' }}">
                            <p class="text-[10px] font-bold uppercase tracking-widest {{ $isKgbEligible ? 'text-emerald-100' : 'text-slate-400' }}">
                                Kesimpulan Kelayakan
                            </p>
                            <p class="text-base font-extrabold">
                                {{ $isKgbEligible ? 'Memenuhi Syarat KGB' : 'Syarat Belum Terpenuhi' }}
                            </p>
                            <p class="text-[10px] font-medium leading-tight {{ $isKgbEligible ? 'text-emerald-100/90' : 'text-slate-400' }}">
                                {{ $isKgbEligible 
                                    ? 'Sistem akan secara otomatis memproses usulan KGB Anda sesuai jadwal.' 
                                    : 'Lengkapi data administrasi Anda atau hubungi bagian kepegawaian.' }}
                            </p>
                        </div>
                    </div>

                </div>

                {{-- KOLOM KANAN: JADWAL KGB, SKP PERIODE BERJALAN & REKAP --}}
                <div class="space-y-6 lg:col-span-2">
                    
                    {{-- GRID 2 CARD: KGB MENDATANG & SKP PERIODE BERJALAN --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- CARD JADWAL TMT KGB NEXT --}}
                        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 relative overflow-hidden flex flex-col justify-between min-h-[220px] group hover:shadow-md transition duration-300">
                            <div class="absolute -right-8 -top-8 w-28 h-28 bg-indigo-50 rounded-full blur-2xl group-hover:bg-indigo-100/60 transition duration-300 pointer-events-none"></div>
                            
                            <div class="space-y-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-semibold">
                                        ⏱️
                                    </div>
                                    <span class="text-sm font-bold text-slate-800">TMT KGB Berikutnya</span>
                                </div>

                                @if(!$tmtGajiTerakhir)
                                    <div class="py-4 text-slate-400 text-xs italic">Data TMT Gaji Terakhir belum terisi.</div>
                                @else
                                    <div class="space-y-1">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Estimasi TMT KGB</p>
                                        <p class="text-xl font-black text-slate-800 leading-tight">
                                            {{ $jatuhTempo->translatedFormat('d F Y') }}
                                        </p>
                                        <p class="text-xs text-slate-400">
                                            Terakhir: {{ $tmtGajiTerakhir->translatedFormat('d F Y') }}
                                        </p>
                                    </div>
                                @endif
                            </div>

                            @if($tmtGajiTerakhir)
                                <div class="pt-4 border-t border-slate-50 mt-4">
                                    @if($selisihHari < 0)
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-rose-50 text-rose-700 font-bold text-xs border border-rose-100 animate-pulse">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            KGB Terlewat {{ abs((int)$selisihHari) }} hari
                                        </div>
                                    @elseif($selisihHari <= 60)
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-amber-50 text-amber-700 font-bold text-xs border border-amber-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                            Jatuh Tempo (H-{{ (int)$selisihHari }})
                                        </div>
                                    @else
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-indigo-50 text-indigo-700 font-bold text-xs border border-indigo-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                            Akan Datang (H-{{ (int)$selisihHari }})
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- CARD SKP PERIODE BERJALAN --}}
                        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 relative overflow-hidden flex flex-col justify-between min-h-[220px] group hover:shadow-md transition duration-300">
                            <div class="absolute -right-8 -top-8 w-28 h-28 bg-emerald-50 rounded-full blur-2xl group-hover:bg-emerald-100/60 transition duration-300 pointer-events-none"></div>
                            
                            <div class="space-y-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-semibold">
                                        📊
                                    </div>
                                    <span class="text-sm font-bold text-slate-800">SKP Periode Berjalan</span>
                                </div>

                                @if(!$skpPeriodeBerjalan)
                                    <div class="space-y-2">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tahun {{ $tahunBerjalan }}</p>
                                        <div class="flex items-center gap-2 text-slate-500">
                                            <div class="w-1.5 h-1.5 rounded-full bg-amber-400"></div>
                                            <span class="text-xs font-semibold text-slate-500">Proses penilaian oleh Atasan</span>
                                        </div>
                                        <p class="text-[10px] text-slate-400 leading-normal">
                                            Penilaian biasanya diterbitkan di akhir tahun penilaian berjalan.
                                        </p>
                                    </div>
                                @else
                                    <div class="space-y-2.5">
                                        <div>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Predikat SKP {{ $tahunBerjalan }}</p>
                                            @php
                                                $colors = [
                                                    'Sangat Baik' => 'from-emerald-500 to-teal-600 text-emerald-50 border-emerald-200',
                                                    'Baik'        => 'from-blue-500 to-indigo-600 text-blue-50 border-blue-200',
                                                    'Cukup'       => 'from-amber-400 to-yellow-500 text-amber-950 border-amber-200',
                                                    'Kurang'      => 'from-orange-500 to-amber-600 text-orange-50 border-orange-200',
                                                    'Sangat Kurang' => 'from-rose-500 to-red-600 text-rose-50 border-rose-200',
                                                ];
                                                $colorClass = $colors[$skpPeriodeBerjalan->predikat] ?? 'from-slate-400 to-slate-500 text-white';
                                            @endphp
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-black bg-gradient-to-r {{ $colorClass }} border shadow-sm">
                                                ★ {{ $skpPeriodeBerjalan->predikat }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if($skpPeriodeBerjalan)
                                <div class="pt-4 border-t border-slate-50 mt-4 flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase">Dokumen Bukti</span>
                                    @if($skpPeriodeBerjalan->file_bukti_skp)
                                        <a href="{{ Storage::url($skpPeriodeBerjalan->file_bukti_skp) }}" target="_blank"
                                           class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600 hover:text-emerald-800 transition duration-150 hover:underline">
                                            📄 Lihat File Bukti
                                        </a>
                                    @else
                                        <span class="text-[10px] font-semibold text-slate-400 italic">Belum diunggah</span>
                                    @endif
                                </div>
                            @else
                                <div class="pt-4 border-t border-slate-50 mt-4 flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase">Arsip Bukti</span>
                                    <span class="text-[10px] font-semibold text-slate-400 italic">Tidak tersedia</span>
                                </div>
                            @endif
                        </div>

                    </div>

                    {{-- RIWAYAT PENILAIAN SKP (MEMANTAU SKP) --}}
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/40 flex justify-between items-center">
                            <div>
                                <h3 class="text-base font-bold text-slate-800">Rekapitulasi Evaluasi SKP</h3>
                                <p class="text-xs text-slate-400 mt-0.5">Daftar nilai predikat Sasaran Kinerja Pegawai tahun sebelumnya.</p>
                            </div>
                            <span class="bg-slate-100 text-slate-700 text-xs font-bold px-2.5 py-1 rounded-full border border-slate-200">
                                {{ $skpEvaluasi->count() }} Data
                            </span>
                        </div>

                        @if($skpEvaluasi->isEmpty())
                            <div class="p-8 text-center text-slate-400 space-y-2">
                                <div class="text-3xl">📊</div>
                                <p class="font-bold text-sm">Belum Ada Rekap SKP</p>
                                <p class="text-xs text-slate-400">Data penilaian kinerja tahunan Anda belum diunggah oleh Admin.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-slate-50 text-[10px] text-slate-400 font-bold uppercase tracking-wider border-b border-slate-100">
                                        <tr>
                                            <th class="px-6 py-4">Tahun Penilaian</th>
                                            <th class="px-6 py-4">Predikat Penilaian Kinerja</th>
                                            <th class="px-6 py-4">Status Syarat KGB</th>
                                            <th class="px-6 py-4 text-right">Berkas SKP</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50">
                                        @foreach($skpEvaluasi as $skp)
                                            <tr class="hover:bg-slate-50/50 transition">
                                                <td class="px-6 py-4 font-bold text-slate-800">
                                                    Tahun {{ $skp->tahun_penilaian }}
                                                    @if($skp->tahun_penilaian == $tahunBerjalan)
                                                        <span class="ml-1.5 px-2 py-0.5 text-[9px] font-bold bg-indigo-50 text-indigo-600 rounded-md border border-indigo-100">Aktif</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4">
                                                    @php
                                                        $colors = [
                                                            'Sangat Baik' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                                            'Baik'        => 'bg-blue-50 text-blue-700 border-blue-100',
                                                            'Cukup'       => 'bg-yellow-50 text-yellow-700 border-yellow-100',
                                                            'Kurang'      => 'bg-orange-50 text-orange-700 border-orange-100',
                                                            'Sangat Kurang' => 'bg-rose-50 text-rose-700 border-rose-100',
                                                        ];
                                                        $badgeColor = $colors[$skp->predikat] ?? 'bg-slate-50 text-slate-600';
                                                    @endphp
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $badgeColor }}">
                                                        {{ $skp->predikat }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-xs font-medium">
                                                    @if(in_array($skp->predikat, ['Baik', 'Sangat Baik']))
                                                        <span class="text-emerald-600 flex items-center gap-1">✓ Memenuhi syarat minimal</span>
                                                    @else
                                                        <span class="text-rose-600 flex items-center gap-1">✗ Di bawah syarat minimal</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-right">
                                                    @if($skp->file_bukti_skp)
                                                        <a href="{{ Storage::url($skp->file_bukti_skp) }}" target="_blank"
                                                           class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 hover:text-indigo-800 transition duration-150 hover:underline">
                                                            Download Berkas
                                                        </a>
                                                    @else
                                                        <span class="text-xs text-slate-400 italic">Berkas belum ada</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    {{-- ARSIP SK KGB (RIWAYAT KGB & DOWNLOAD ARSIP SENDIRI) --}}
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/40 flex justify-between items-center">
                            <div>
                                <h3 class="text-base font-bold text-slate-800">Riwayat Surat Keputusan (SK) KGB</h3>
                                <p class="text-xs text-slate-400 mt-0.5">Daftar kenaikan gaji berkala yang pernah diterbitkan beserta unduh berkas digital.</p>
                            </div>
                            <span class="bg-indigo-50 text-indigo-700 text-xs font-bold px-2.5 py-1 rounded-full border border-indigo-100">
                                {{ $riwayatKgb->count() }} Surat Keputusan
                            </span>
                        </div>

                        @if($riwayatKgb->isEmpty())
                            <div class="p-12 text-center text-slate-400 space-y-3">
                                <div class="text-4xl">📄</div>
                                <p class="font-bold text-sm">Belum Ada Riwayat SK KGB</p>
                                <p class="text-xs text-slate-400 max-w-sm mx-auto">
                                    Sistem belum mencatat adanya riwayat Surat Keputusan (SK) Kenaikan Gaji Berkala untuk profil kepegawaian Anda.
                                </p>
                            </div>
                        @else
                            <div class="divide-y divide-slate-100">
                                @foreach($riwayatKgb as $index => $riwayat)
                                    <div class="p-6 sm:p-8 hover:bg-slate-50/30 transition duration-150 flex flex-col sm:flex-row gap-5 items-start sm:items-center justify-between group">
                                        <div class="flex gap-4 items-start">
                                            <div class="w-12 h-12 rounded-2xl bg-indigo-50/60 border border-indigo-100 text-indigo-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform duration-200 shadow-sm">
                                                📄
                                            </div>
                                            <div class="space-y-1">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <h4 class="font-extrabold text-slate-800 text-sm sm:text-base leading-snug">
                                                        Nomor: {{ $riwayat->nomor_sk_baru }}
                                                    </h4>
                                                    @if($index === 0)
                                                        <span class="px-2 py-0.5 text-[9px] font-bold bg-emerald-50 text-emerald-700 rounded-md border border-emerald-100">Terbaru / Aktif</span>
                                                    @endif
                                                </div>
                                                <p class="text-xs text-slate-400">
                                                    Ditetapkan oleh: <span class="font-semibold text-slate-600">{{ $riwayat->pejabat_penetap ?? 'Pejabat Kepegawaian' }}</span> pada {{ \Carbon\Carbon::parse($riwayat->tanggal_ditetapkan)->translatedFormat('d F Y') }}
                                                </p>
                                                
                                                <div class="flex flex-wrap gap-2 pt-2">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                        📅 TMT: {{ \Carbon\Carbon::parse($riwayat->tmt_baru)->format('d/m/Y') }}
                                                    </span>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                        💰 Gaji Baru: Rp {{ number_format($riwayat->gaji_pokok_baru, 0, ',', '.') }}
                                                    </span>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold bg-indigo-50 text-indigo-800 border border-indigo-200">
                                                        💼 Masa Kerja: {{ $riwayat->masa_kerja_tahun_baru }} Thn, {{ $riwayat->masa_kerja_bulan_baru }} Bln
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w-full sm:w-auto mt-4 sm:mt-0 shrink-0">
                                            <a href="{{ route('pegawai.sk.download', $riwayat->id) }}" 
                                               onclick="window.dispatchEvent(new CustomEvent('show-toast', { detail: { message: 'Dokumen SK KGB Anda berhasil digenerate dan sedang diunduh!', type: 'success' } }))"
                                               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 border border-slate-200 shadow-sm text-xs font-bold rounded-xl text-slate-700 bg-white hover:bg-slate-50 hover:text-indigo-600 transition duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                                Unduh SK KGB (PDF)
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>

            </div>
        @endif
    </main>

    {{-- FOOTER --}}
    <footer class="bg-white border-t border-slate-100 mt-auto py-6 text-center">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">
            © {{ date('Y') }} Sistem Kenaikan Gaji Berkala - RSD Sidawangi
        </p>
        <p class="text-[10px] text-slate-400 mt-1">
            Hak Cipta Dilindungi Undang-Undang. Portal khusus Pegawai Negeri Sipil (PNS).
        </p>
    {{-- Global Toast --}}
    <div x-show="toast.show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2 transform sm:translate-y-0 sm:translate-x-2"
         x-transition:enter-end="opacity-100 translate-y-0 transform sm:translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak
         class="fixed bottom-5 right-5 z-50 max-w-sm w-full bg-white rounded-xl shadow-lg border border-slate-100 p-4 flex items-start gap-3 border-l-4"
         :class="toast.type === 'success' ? 'border-l-emerald-500' : (toast.type === 'warning' ? 'border-l-yellow-500' : 'border-l-rose-500')">
        
        <!-- Icon -->
        <div class="shrink-0 mt-0.5">
            <template x-if="toast.type === 'success'">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </template>
            <template x-if="toast.type === 'warning'">
                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </template>
            <template x-if="toast.type === 'error'">
                <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </template>
        </div>

        <div class="flex-1">
            <p class="text-sm font-semibold text-slate-800" x-text="toast.type === 'success' ? 'Berhasil' : (toast.type === 'warning' ? 'Perhatian' : 'Gagal')"></p>
            <p class="text-xs text-slate-500 mt-0.5" x-text="toast.message"></p>
        </div>

        <button @click="toast.show = false" class="text-slate-400 hover:text-slate-600 transition shrink-0 ml-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

</body>
</html>
