@extends('layouts.pegawai')
@section('title', 'Dashboard Pegawai')

@section('content')
<div class="space-y-6">

    @if(!$pegawai)
        {{-- ALERT JIKA DATA PEGAWAI KOSONG --}}
        <div class="bg-amber-50 border border-amber-200 text-amber-900 rounded-2xl p-6 sm:p-8 shadow-sm flex flex-col sm:flex-row gap-5 items-start">
            <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center shrink-0 text-2xl text-amber-600">⚠️</div>
            <div class="space-y-2">
                <h3 class="font-bold text-lg">Data Pegawai Tidak Ditemukan</h3>
                <p class="text-sm leading-relaxed">
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
                    ? "Nilai SKP memenuhi syarat (Minimal predikat 'Baik' 2 tahun terakhir)" 
                    : "Ada nilai SKP di bawah predikat 'Baik' dalam 2 tahun terakhir";
            }
            
            $isKgbEligible = $bebasHukuman && $skpLayak;
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

            {{-- KOLOM KIRI: PROFIL PEGAWAI --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden relative">
                <div class="h-3 bg-gradient-to-tr from-blue-700 to-blue-900 relative"></div>
                <div class="px-6 pb-6 relative">
                    <div class="pt-6 space-y-1">
                        <h2 class="text-xl font-bold text-gray-800 leading-tight">{{ $pegawai->nama_lengkap }}</h2>
                        <p class="text-blue-700 font-mono text-sm font-semibold">{{ $pegawai->nip }}</p>
                        <div class="pt-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                {{ $pegawai->jabatan ?? 'Jabatan Belum Diisi' }}
                            </span>
                        </div>
                    </div>

                    <hr class="my-5 border-gray-100">

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Pangkat / Golongan</p>
                                <p class="text-sm text-gray-800 font-bold">{{ $pegawai->pangkat ?? '-' }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-lg bg-gray-100 text-gray-700 font-mono text-xs font-extrabold border border-gray-200">
                                {{ $pegawai->golongan ?? '-' }}
                            </span>
                        </div>
                        
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase">Unit Kerja / Instansi</p>
                            <p class="text-sm text-gray-800 font-semibold">{{ $pegawai->kantor_tempat_kerja ?? '-' }}</p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Masa Kerja (Gol)</p>
                                <p class="text-sm text-gray-800 font-bold">
                                    {{ $pegawai->masa_kerja_tahun }} <span class="text-xs font-medium text-gray-500">Thn</span>, 
                                    {{ $pegawai->masa_kerja_bulan }} <span class="text-xs font-medium text-gray-500">Bln</span>
                                </p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Gaji Pokok Terakhir</p>
                                <p class="text-sm text-blue-700 font-extrabold">
                                    Rp {{ number_format($pegawai->gaji_pokok_terakhir, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: CARD PERSYARATAN & KELAYAKAN KGB --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-5">
                <div class="space-y-1">
                    <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                        <span class="text-blue-600"></span> Kriteria Kelayakan KGB
                    </h3>
                    <p class="text-xs text-gray-400">Verifikasi berkala kelayakan Kenaikan Gaji Berkala PNS.</p>
                </div>
                
                <div class="space-y-4">
                    {{-- Check Status Hukuman Disiplin --}}
                    <div class="flex gap-3 items-start p-3 rounded-2xl {{ $bebasHukuman ? 'bg-green-50 border border-green-100' : 'bg-red-50 border border-red-100' }}">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 text-sm font-bold {{ $bebasHukuman ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {!! $bebasHukuman ? '✓' : '✗' !!}
                        </div>
                        <div class="space-y-0.5 mt-0.5">
                            <p class="text-xs font-bold text-gray-700">Status Disiplin</p>
                            <p class="text-[11px] font-medium {{ $bebasHukuman ? 'text-green-700' : 'text-red-700' }}">
                                {{ $bebasHukuman ? 'Bebas dari hukuman disiplin' : 'Sedang dalam masa hukuman disiplin' }}
                            </p>
                        </div>
                    </div>

                    {{-- Check Status SKP 2 Tahun terakhir --}}
                    <div class="flex gap-3 items-start p-3 rounded-2xl {{ $skpLayak ? 'bg-green-50 border border-green-100' : 'bg-yellow-50 border border-yellow-100' }}">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 text-sm font-bold {{ $skpLayak ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {!! $skpLayak ? '✓' : '!' !!}
                        </div>
                        <div class="space-y-0.5 mt-0.5">
                            <p class="text-xs font-bold text-gray-700">Evaluasi Kinerja (SKP)</p>
                            <p class="text-[11px] font-medium leading-relaxed {{ $skpLayak ? 'text-green-700' : 'text-yellow-700' }}">
                                {{ $skpStatusMsg }}
                            </p>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100">

                {{-- KESIMPULAN KELAYAKAN --}}
                <div class="p-4 rounded-xl text-center space-y-2 {{ $isKgbEligible ? 'bg-gradient-to-tr from-emerald-500 to-teal-600 text-white shadow-md shadow-emerald-100' : 'bg-gray-50 border border-gray-200 text-gray-700' }}">
                    <p class="text-[10px] font-bold uppercase tracking-widest {{ $isKgbEligible ? 'text-emerald-100' : 'text-gray-400' }}">
                        Kesimpulan Kelayakan
                    </p>
                    <p class="text-lg font-extrabold">
                        {{ $isKgbEligible ? 'Memenuhi Syarat KGB' : 'Syarat Belum Terpenuhi' }}
                    </p>
                    <p class="text-[10px] font-medium leading-tight {{ $isKgbEligible ? 'text-emerald-50' : 'text-gray-500' }}">
                        {{ $isKgbEligible 
                            ? 'Sistem akan secara otomatis memproses usulan KGB Anda sesuai jadwal.' 
                            : 'Lengkapi data administrasi Anda atau hubungi bagian kepegawaian.' }}
                    </p>
                </div>
            </div>

        </div>
    @endif
</div>
@endsection
