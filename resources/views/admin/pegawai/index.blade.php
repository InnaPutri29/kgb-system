@extends('layouts.admin')
@section('title', 'Data Pegawai')

@section('content')
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Data Pegawai</h2>
            <p class="text-sm text-gray-500">Kelola informasi seluruh pegawai negeri sipil dan riwayat kepangkatan.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('admin.pegawai.create') }}"
               class="inline-flex items-center justify-center gap-1.5 text-sm bg-blue-600 border border-transparent hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition font-medium shadow-sm flex-1 sm:flex-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Data
            </a>
            <a href="{{ route('admin.pegawai.import') }}"
               class="inline-flex items-center justify-center gap-1.5 text-sm bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition font-medium shadow-sm flex-1 sm:flex-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
                Import Excel
            </a>
        </div>
    </div>

    {{-- Pencarian & Filter --}}
    <div class="bg-white/50 backdrop-blur-3xl rounded-[1.5rem] p-5 border border-white/80 border-t-white shadow-[0_8px_32px_0_rgba(31,38,135,0.07)]">
        <form method="GET" action="{{ route('admin.pegawai.index') }}" class="flex flex-col md:flex-row gap-3 items-end">
            <div class="w-full flex-1">
                <x-input-label for="search" value="Cari Pegawai" class="mb-1" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <x-text-input id="search" name="search" type="text" class="block w-full pl-9 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Cari NIP, Nama, Jabatan..." value="{{ request('search') }}" />
                </div>
            </div>
            
            <div class="w-full md:w-40">
                <x-input-label for="golongan" value="Golongan" class="mb-1" />
                <select id="golongan" name="golongan" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm block w-full text-sm">
                    <option value="">Semua</option>
                    <option value="I" {{ request('golongan') == 'I' ? 'selected' : '' }}>Gol. I</option>
                    <option value="II" {{ request('golongan') == 'II' ? 'selected' : '' }}>Gol. II</option>
                    <option value="III" {{ request('golongan') == 'III' ? 'selected' : '' }}>Gol. III</option>
                    <option value="IV" {{ request('golongan') == 'IV' ? 'selected' : '' }}>Gol. IV</option>
                    @if(isset($golonganList))
                        <optgroup label="Spesifik">
                            @foreach($golonganList as $gol)
                                <option value="{{ $gol }}" {{ request('golongan') == $gol ? 'selected' : '' }}>{{ $gol }}</option>
                            @endforeach
                        </optgroup>
                    @endif
                </select>
            </div>

            <div class="w-full md:w-32">
                <x-input-label for="tahun_tmt" value="Tahun TMT" class="mb-1" />
                <select id="tahun_tmt" name="tahun_tmt" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm block w-full text-sm">
                    <option value="">Semua</option>
                    @if(isset($tahunTmtList))
                        @foreach($tahunTmtList as $thn)
                            <option value="{{ $thn }}" {{ request('tahun_tmt') == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition w-full sm:w-auto shadow-sm">
                    Filter
                </button>
                @if(request()->filled('search') || request()->filled('golongan') || request()->filled('tahun_tmt'))
                    <a href="{{ route('admin.pegawai.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition text-center w-full sm:w-auto">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white/50 backdrop-blur-3xl rounded-[1.5rem] border border-white/80 border-t-white shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] overflow-hidden">
        @if($pegawai->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-gray-400">
                @if(request()->filled('search'))
                    <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <p class="font-medium">Data pegawai tidak ditemukan</p>
                    <p class="text-sm">Tidak ada hasil pencarian yang cocok untuk "{{ request('search') }}".</p>
                    <a href="{{ route('admin.pegawai.index') }}" class="mt-4 text-sm text-blue-600 hover:underline">Lihat semua data →</a>
                @else
                    <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <p class="font-medium">Belum ada data pegawai</p>
                    <p class="text-sm">Silakan impor data dari file Excel terlebih dahulu.</p>
                    <a href="{{ route('admin.pegawai.import') }}" class="mt-4 text-sm text-blue-600 hover:underline">Import sekarang →</a>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-blue-200/60 text-xs text-blue-900 uppercase tracking-wider border-b border-white/30">
                        <tr>
                            <th class="px-3 py-3 text-center">No</th>
                            <th class="px-3 py-3 text-left">NIP</th>
                            <th class="px-3 py-3 text-left">Nama Pegawai</th>
                            <th class="px-3 py-3 text-center">Gol.</th>
                            <th class="px-3 py-3 text-center">TMT Gaji Terakhir</th>
                            <th class="px-3 py-3 text-center">Gaji Pokok</th>
                            <th class="px-3 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($pegawai as $index => $p)
                        <tr class="hover:bg-white/40 transition">
                            <td class="px-3 py-3 text-center text-gray-500 font-medium text-xs">{{ $pegawai->firstItem() + $index }}</td>
                            <td class="px-3 py-3 font-mono text-xs text-gray-700">{{ $p->nip }}</td>
                            <td class="px-3 py-3">
                                <p class="font-medium text-gray-900">{{ $p->nama_lengkap }}</p>
                                <p class="text-xs text-gray-600 mt-0.5">{{ $p->jabatan ?? '-' }}</p>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-800">{{ $p->golongan ?? '-' }}</span>
                            </td>
                            <td class="px-3 py-3 text-center text-gray-700">
                                {{ $p->tmt_gaji_terakhir ? \Carbon\Carbon::parse($p->tmt_gaji_terakhir)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-3 py-3 text-gray-700 font-medium text-center whitespace-nowrap">
                                {{ $p->gaji_pokok_terakhir ? 'Rp ' . number_format($p->gaji_pokok_terakhir, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-3 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.pegawai.show', $p) }}" class="p-1.5 bg-blue-500/10 text-blue-600 hover:bg-blue-600 hover:text-white rounded-md transition" title="Detail Pegawai">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.pegawai.edit', $p) }}" class="p-1.5 bg-emerald-500/10 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-md transition" title="Edit Pegawai">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <button type="button" 
                                            @click="$dispatch('confirm-delete', {
                                                action: '{{ route('admin.pegawai.destroy', $p) }}',
                                                title: 'Hapus Pegawai',
                                                description: 'Hapus data {{ $p->nama_lengkap }}? Akun login terkait juga akan ikut terhapus permanen.'
                                            })"
                                            class="p-1.5 bg-red-500/10 text-red-600 hover:bg-red-600 hover:text-white rounded-md transition" title="Hapus Pegawai">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $pegawai->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
