@extends('layouts.admin')
@section('title', 'Detail Riwayat KGB')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Detail Riwayat KGB</h2>
            <p class="text-sm text-gray-500">Informasi lengkap dokumen Surat Keputusan Kenaikan Gaji Berkala.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.kgb.index') }}" class="inline-flex items-center gap-1 text-sm bg-white text-gray-700 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition font-medium border border-gray-300">
                Kembali
            </a>
            <a href="{{ route('admin.kgb.download-pdf', $riwayat->id) }}" 
               onclick="window.dispatchEvent(new CustomEvent('show-toast', { detail: { message: 'Dokumen SK KGB pegawai berhasil digenerate dan sedang diunduh!', type: 'success' } }))"
               class="inline-flex items-center gap-1 text-sm bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg transition font-medium border border-transparent">
                Unduh PDF
            </a>
        </div>
    </div>

    <div class="bg-white/50 backdrop-blur-3xl lg:bg-white lg:backdrop-blur-none rounded-[1.5rem] border border-white/80 border-t-white shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] overflow-hidden relative mt-4">
        <div class="h-3 bg-gradient-to-r from-[#0B3E6A] to-[#234A9F] relative"></div>
        <div class="px-6 pb-6 relative">
            <div class="pt-6 flex flex-col md:flex-row justify-between md:items-end gap-4">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 leading-tight">SK KGB: {{ $riwayat->nomor_sk_baru }}</h3>
                    <p class="text-blue-600 font-medium mt-1">{{ $riwayat->pegawai->nama_lengkap ?? '-' }} <span class="font-mono text-sm ml-1">({{ $riwayat->pegawai->nip ?? '-' }})</span></p>
                </div>
                <div class="flex gap-3">
                    @if($riwayat->status === 'Final')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-green-50 text-green-700 border border-green-100 lg:border-slate-100">
                            Selesai TTE (Final)
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-100 lg:border-slate-100">
                            Draf (Menunggu TTE)
                        </span>
                    @endif
                </div>
            </div>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                <!-- KGB Baru -->
                <div>
                    <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3">Penetapan KGB Baru</h4>
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-50">
                            <tr>
                                <td class="py-2 text-gray-500 w-1/3">Nomor SK</td>
                                <td class="py-2 text-gray-800 font-medium">{{ $riwayat->nomor_sk_baru }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">Tgl Ditetapkan</td>
                                <td class="py-2 text-gray-800 font-medium">{{ $riwayat->tanggal_ditetapkan?->format('d/m/Y') ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">TMT Baru</td>
                                <td class="py-2 text-gray-800 font-medium">{{ $riwayat->tmt_baru?->format('d/m/Y') ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">Masa Kerja Golongan</td>
                                <td class="py-2 text-gray-800 font-medium">{{ $riwayat->masa_kerja_tahun_baru }} Tahun, {{ $riwayat->masa_kerja_bulan_baru }} Bulan</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">Jatuh Tempo YAD</td>
                                <td class="py-2 text-gray-800 font-medium">{{ $riwayat->tmt_yad?->format('d/m/Y') ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Dasar KGB & Keuangan -->
                <div>
                    <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3">Keuangan & Dasar SK</h4>
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-50">
                            <tr>
                                <td class="py-2 text-gray-500 w-1/3">Gaji Pokok Lama</td>
                                <td class="py-2 text-gray-800 font-medium">Rp {{ number_format($riwayat->gaji_pokok_lama, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">Gaji Pokok Baru</td>
                                <td class="py-2 text-blue-600 font-bold text-base">Rp {{ number_format($riwayat->gaji_pokok_baru, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">Pejabat Penetap SK</td>
                                <td class="py-2 text-gray-800 font-medium">{{ $riwayat->pejabat_penetap ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if($riwayat->status === 'Draf')
    <div class="bg-white rounded-[1.5rem] border border-gray-200 shadow-sm overflow-hidden mt-6 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Unggah SK KGB Final (TTE)</h3>
        <p class="text-sm text-gray-600 mb-6">Unggah dokumen SK KGB yang telah ditandatangani secara elektronik (TTE) melalui aplikasi SIDEBAR. Sistem otomatis akan merubah status dokumen menjadi Final dan mengirimkan notifikasi kepada Pegawai bersangkutan.</p>
        
        <form action="{{ route('admin.kgb.upload-final', $riwayat->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-4 items-end">
            @csrf
            <div class="flex-1 w-full">
                <label for="file_sk_final" class="block text-sm font-medium text-gray-700 mb-1">File SK Final (PDF, Maks 5MB) <span class="text-red-500">*</span></label>
                <input type="file" name="file_sk_final" id="file_sk_final" accept=".pdf" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded-lg focus:outline-none">
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2.5 rounded-lg transition shadow-sm w-full sm:w-auto text-sm">
                Unggah & Finalisasi
            </button>
        </form>
        @error('file_sk_final')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    @else
    <div class="bg-green-50 rounded-[1.5rem] border border-green-200 shadow-sm overflow-hidden mt-6 p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-lg font-bold text-green-800 mb-1">SK KGB Final Tersedia</h3>
                <p class="text-sm text-green-700">Dokumen SK KGB ini telah selesai di-TTE dan sudah diarsipkan. Pegawai juga telah menerima notifikasi.</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                @if($riwayat->file_sk_final)
                    <a href="{{ Storage::url($riwayat->file_sk_final) }}" target="_blank"
                       onclick="window.dispatchEvent(new CustomEvent('show-toast', { detail: { message: 'Dokumen SK KGB Final berhasil dibuka / diunduh!', type: 'success' } }))"
                       class="bg-green-600 hover:bg-green-700 text-white font-medium px-4 py-2 rounded-lg transition shadow-sm text-sm shrink-0 inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Lihat File Final
                    </a>
                @endif
                <button type="button" onclick="document.getElementById('reupload-form').classList.toggle('hidden')" class="bg-white hover:bg-green-100 text-green-800 font-medium px-4 py-2 rounded-lg transition border border-green-300 shadow-sm text-sm shrink-0 inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Ganti File SK
                </button>
            </div>
        </div>

        <!-- Form Edit / Re-upload File SK Final -->
        <div id="reupload-form" class="hidden mt-6 pt-6 border-t border-green-200/80">
            <h4 class="text-sm font-bold text-green-900 mb-1">Unggah Ulang / Ganti File SK Final (PDF)</h4>
            <p class="text-xs text-green-700 mb-4">Jika ada revisi atau kesalahan unggah file, Anda dapat mengunggah file baru di bawah ini. File PDF lama akan otomatis digantikan.</p>
            
            <form action="{{ route('admin.kgb.upload-final', $riwayat->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-4 items-end">
                @csrf
                <div class="flex-1 w-full">
                    <label for="file_sk_final_edit" class="block text-xs font-semibold text-green-800 mb-1">Pilih File SK Baru (PDF, Maks 5MB) <span class="text-red-500">*</span></label>
                    <input type="file" name="file_sk_final" id="file_sk_final_edit" accept=".pdf" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-100 file:text-green-800 hover:file:bg-green-200 border border-green-300 bg-white rounded-lg focus:outline-none">
                </div>
                <button type="submit" class="bg-green-700 hover:bg-green-800 text-white font-medium px-5 py-2.5 rounded-lg transition shadow-sm w-full sm:w-auto text-sm shrink-0">
                    Simpan & Perbarui File
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
