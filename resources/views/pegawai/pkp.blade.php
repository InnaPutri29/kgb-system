@extends('layouts.pegawai')
@section('title', 'Syarat Kelayakan KGB')

@section('content')
<div class="space-y-6">
    


    @if(!$pegawai)
        <div class="bg-amber-50/50 backdrop-blur-3xl border border-amber-200/80 border-t-amber-100 text-amber-900 rounded-[1.5rem] p-6 shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-amber-100/80 shadow-sm flex items-center justify-center shrink-0 text-xl text-amber-600">⚠️</div>
            <div>
                <h3 class="font-bold text-lg">Data Pegawai Tidak Ditemukan</h3>
                <p class="text-sm text-amber-800/80">Akun Anda belum terhubung dengan data PNS. Silakan hubungi admin kepegawaian.</p>
            </div>
        </div>
    @else


    {{-- INFORMASI SYARAT MUTLAK KGB --}}
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row gap-4 mb-6 border-b border-gray-100 pb-5">
            <div class="w-12 h-12 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
            <h3 class="font-bold text-lg text-gray-800">Informasi Persyaratan Mutlak</h3>
            <p class="text-sm text-gray-500 mt-1">4 Syarat mutlak kelayakan Kenaikan Gaji Berkala sesuai dengan peraturan yang berlaku.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
            {{-- Masa Kerja --}}
            <div class="flex gap-4">
                <div class="mt-1 shrink-0">
                    <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800 text-sm mb-1">Masa Kerja Golongan (MKG)</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">Telah mencapai masa kerja sekurang-kurangnya <strong class="font-semibold text-gray-900">2 (dua) tahun</strong> secara terus-menerus.</p>
                </div>
            </div>

            {{-- Predikat Kinerja --}}
            <div class="flex gap-4">
                <div class="mt-1 shrink-0">
                    <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800 text-sm mb-1">Predikat Kinerja</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">Memiliki nilai/predikat kinerja tahunan minimal <strong class="font-semibold text-gray-900">"Cukup" / "Butuh Perbaikan"</strong>.</p>
                </div>
            </div>

            {{-- Kedisiplinan --}}
            <div class="flex gap-4">
                <div class="mt-1 shrink-0">
                    <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800 text-sm mb-1">Kedisiplinan</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">Tidak sedang menjalani <strong class="font-semibold text-gray-900">hukuman disiplin</strong> tingkat sedang atau berat.</p>
                </div>
            </div>

            {{-- Administrasi --}}
            <div class="flex gap-4">
                <div class="mt-1 shrink-0">
                    <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800 text-sm mb-1">Administrasi Lengkap</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">Telah melengkapi dokumen dasar berupa <strong class="font-semibold text-gray-900">SK Pangkat</strong> dan <strong class="font-semibold text-gray-900">SK KGB Terakhir</strong>.</p>
                </div>
            </div>
        </div>
    </div>



    @endif
</div>
@endsection
