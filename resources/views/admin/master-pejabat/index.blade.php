@extends('layouts.admin')
@section('title', 'Master Pejabat Penetap SK Terdahulu')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Master Pejabat</h2>
            <p class="text-sm text-gray-500">Kelola daftar pejabat yang menetapkan Surat Keputusan KGB sebelumnya sebagai referensi.</p>
        </div>
        <button x-data @click="$dispatch('open-modal', 'add-pejabat')"
           class="inline-flex items-center gap-1.5 text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Pejabat
        </button>
    </div>

    <div class="bg-white/50 backdrop-blur-3xl rounded-[1.5rem] border border-white/80 border-t-white shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] overflow-hidden">
        @if($pejabat->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <p class="font-medium">Belum ada data pejabat penetap SK</p>
                <p class="text-sm">Silakan tambahkan data pejabat terlebih dahulu.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-blue-200/60 text-xs text-blue-900 uppercase tracking-wider border-b border-white/30">
                        <tr>
                            <th class="px-4 py-3 text-left">Nama Jabatan (Sesuai SK)</th>
                            <th class="px-4 py-3 text-left">Nama Lengkap & Gelar (Opsional)</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($pejabat as $p)
                        <tr class="hover:bg-white/40 transition">
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $p->nama_jabatan }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $p->nama_pejabat ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">

                                    <button x-data @click="$dispatch('open-modal-edit', {{ $p->toJson() }})" class="p-1.5 bg-white/20 text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-md transition" title="Edit Pejabat">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button type="button" 
                                            @click="$dispatch('confirm-delete', {
                                                action: '{{ route('admin.master-pejabat.destroy', $p) }}',
                                                title: 'Hapus Pejabat',
                                                description: 'Hapus data pejabat {{ $p->nama_pejabat ?? $p->nama_jabatan }} permanen?'
                                            })"
                                            class="p-1.5 bg-white/20 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-md transition" title="Hapus Pejabat">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- MODAL TAMBAH --}}
    <x-modal name="add-pejabat" :show="$errors->has('nama_jabatan') && !old('id')" focusable>
        <form method="post" action="{{ route('admin.master-pejabat.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900 mb-4">Tambah Pejabat Penetap SK</h2>

            <div class="space-y-4">
                <div>
                    <x-input-label for="nama_jabatan" value="Nama Jabatan (Sesuai SK) *" />
                    <x-text-input id="nama_jabatan" name="nama_jabatan" type="text" class="mt-1 block w-full" :value="old('nama_jabatan')" required placeholder="Cth: Gubernur Jawa Barat" />
                    <x-input-error :messages="$errors->get('nama_jabatan')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="nama_pejabat" value="Nama Lengkap & Gelar (Opsional)" />
                    <x-text-input id="nama_pejabat" name="nama_pejabat" type="text" class="mt-1 block w-full" :value="old('nama_pejabat')" placeholder="Cth: Ridwan Kamil" />
                    <x-input-error :messages="$errors->get('nama_pejabat')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                <x-primary-button>Simpan</x-primary-button>
            </div>
        </form>
    </x-modal>

    {{-- MODAL EDIT --}}
    <div x-data="{ pejabat: {} }" @open-modal-edit.window="pejabat = $event.detail; $dispatch('open-modal', 'edit-pejabat')">
        <x-modal name="edit-pejabat" focusable>
            <form method="post" :action="`{{ route('admin.master-pejabat.index') }}/${pejabat.id}`" class="p-6">
                @csrf @method('PUT')
                <h2 class="text-lg font-medium text-gray-900 mb-4">Edit Pejabat Penetap SK</h2>

                <div class="space-y-4">
                    <div>
                        <x-input-label for="edit_nama_jabatan" value="Nama Jabatan (Sesuai SK) *" />
                        <x-text-input id="edit_nama_jabatan" name="nama_jabatan" type="text" class="mt-1 block w-full" x-model="pejabat.nama_jabatan" required />
                    </div>
                    <div>
                        <x-input-label for="edit_nama_pejabat" value="Nama Lengkap & Gelar (Opsional)" />
                        <x-text-input id="edit_nama_pejabat" name="nama_pejabat" type="text" class="mt-1 block w-full" x-model="pejabat.nama_pejabat" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                    <x-primary-button>Simpan Perubahan</x-primary-button>
                </div>
            </form>
        </x-modal>
    </div>
</div>
@endsection
