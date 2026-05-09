@extends('layouts.admin')
@section('title', 'Data Pegawai')

@section('content')
<div class="space-y-5">
    <div class="flex justify-between items-center">
        <p class="text-sm text-gray-500">Total {{ $pegawai->total() }} data pegawai terdaftar.</p>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.pegawai.create') }}"
               class="inline-flex items-center gap-1.5 text-sm bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg transition font-medium shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Data
            </a>
            <a href="{{ route('admin.pegawai.import') }}"
               class="inline-flex items-center gap-1.5 text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition font-medium shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
                Import Excel
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        @if($pegawai->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-gray-400">
                <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <p class="font-medium">Belum ada data pegawai</p>
                <p class="text-sm">Silakan impor data dari file Excel terlebih dahulu.</p>
                <a href="{{ route('admin.pegawai.import') }}" class="mt-4 text-sm text-blue-600 hover:underline">Import sekarang →</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3 text-left">NIP</th>
                            <th class="px-4 py-3 text-left">Nama</th>
                            <th class="px-4 py-3 text-left">Jabatan</th>
                            <th class="px-4 py-3 text-left">Gol.</th>
                            <th class="px-4 py-3 text-left">TMT Gaji Terakhir</th>
                            <th class="px-4 py-3 text-left">Gaji Pokok</th>
                            <th class="px-4 py-3 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($pegawai as $p)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $p->nip }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $p->nama_lengkap }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $p->jabatan ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $p->pangkat_golongan ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $p->tmt_gaji_terakhir ? \Carbon\Carbon::parse($p->tmt_gaji_terakhir)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $p->gaji_pokok_terakhir ? 'Rp ' . number_format($p->gaji_pokok_terakhir, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.pegawai.show', $p) }}" class="p-1.5 bg-gray-50 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition" title="Detail Pegawai">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.pegawai.edit', $p) }}" class="p-1.5 bg-gray-50 text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-md transition" title="Edit Pegawai">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('admin.pegawai.destroy', $p) }}" method="POST"
                                          onsubmit="return confirm('Hapus data {{ $p->nama_lengkap }}? User login terkait juga akan terhapus.')" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-gray-50 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-md transition" title="Hapus Pegawai">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $pegawai->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
