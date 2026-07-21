@extends('layouts.admin')
@section('title', 'Master Gaji (PP No. 5 Tahun 2024)')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Master Tarif Gaji</h2>
            <p class="text-sm text-gray-500">Kelola data acuan gaji pokok berdasarkan golongan dan masa kerja.</p>
        </div>
        <button x-data @click="$dispatch('open-modal', 'add-gaji')"
           class="inline-flex items-center gap-1.5 text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition font-medium shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Data
        </button>
    </div>

    {{-- Filter & Pencarian --}}
    <div class="bg-white/40 backdrop-blur-xl p-4 rounded-xl border border-white/50">
        <form method="GET" action="{{ route('admin.master-gaji.index') }}" class="flex flex-col sm:flex-row gap-3 items-end">
            <div class="w-full sm:w-auto flex-1">
                <x-input-label for="search" value="Cari Data" class="mb-1" />
                <x-text-input id="search" name="search" type="text" class="block w-full text-sm" placeholder="Cari Golongan / MKG / Nominal..." value="{{ request('search') }}" />
            </div>
            
            <div class="w-full sm:w-48">
                <x-input-label for="kategori" value="Kategori Golongan" class="mb-1" />
                <select id="kategori" name="kategori" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full text-sm">
                    <option value="">Semua Golongan</option>
                    <option value="I" {{ request('kategori') == 'I' ? 'selected' : '' }}>Golongan I</option>
                    <option value="II" {{ request('kategori') == 'II' ? 'selected' : '' }}>Golongan II</option>
                    <option value="III" {{ request('kategori') == 'III' ? 'selected' : '' }}>Golongan III</option>
                    <option value="IV" {{ request('kategori') == 'IV' ? 'selected' : '' }}>Golongan IV</option>
                </select>
            </div>

            <div class="flex gap-2 w-full sm:w-auto">
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition w-full sm:w-auto shadow-sm">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'kategori']))
                    <a href="{{ route('admin.master-gaji.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition text-center w-full sm:w-auto">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white/40 backdrop-blur-xl rounded-xl border border-white/50 overflow-hidden">
        @if($gaji->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                @if(request()->hasAny(['search', 'kategori']))
                    <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <p class="font-medium">Data master gaji tidak ditemukan</p>
                    <p class="text-sm">Tidak ada hasil pencarian yang cocok dengan kriteria filter Anda.</p>
                    <a href="{{ route('admin.master-gaji.index') }}" class="mt-4 text-sm text-blue-600 hover:underline">Reset filter & pencarian →</a>
                @else
                    <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="font-medium">Belum ada data master gaji</p>
                    <p class="text-sm">Silakan tambahkan data acuan gaji pokok terlebih dahulu.</p>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-white/20 text-xs text-gray-500 uppercase tracking-wider border-b border-white/50">
                        <tr>
                            <th class="px-6 py-4 text-left font-medium">Golongan Ruang</th>
                            <th class="px-6 py-4 text-center font-medium">Masa Kerja Golongan (MKG)</th>
                            <th class="px-6 py-4 text-right font-medium">Nominal Gaji Pokok (Rp)</th>
                            <th class="px-6 py-4 text-center font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($gaji as $g)
                        <tr class="hover:bg-white/20 transition">
                            <td class="px-6 py-3 font-semibold text-indigo-700 bg-indigo-50/30">{{ $g->golongan }}</td>
                            <td class="px-6 py-3 text-center text-gray-600 font-medium">{{ $g->masa_kerja }} Tahun</td>
                            <td class="px-6 py-3 text-right text-gray-800 font-mono font-medium">{{ number_format($g->nominal_gaji, 0, ',', '.') }}</td>
                            <td class="px-6 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <button x-data @click="$dispatch('open-modal-edit', {{ $g->toJson() }})" class="p-1.5 bg-white/20 text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-md transition" title="Edit Gaji">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button type="button" 
                                            @click="$dispatch('confirm-delete', {
                                                action: '{{ route('admin.master-gaji.destroy', $g) }}',
                                                title: 'Hapus Gaji Pokok',
                                                description: 'Hapus acuan gaji Golongan {{ $g->golongan }} MKG {{ $g->masa_kerja }} Tahun?'
                                            })"
                                            class="p-1.5 bg-white/20 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-md transition" title="Hapus Gaji">
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

    @if($gaji->hasPages())
        <div class="mt-4">
            {{ $gaji->links() }}
        </div>
    @endif

    {{-- MODAL TAMBAH --}}
    <x-modal name="add-gaji" :show="$errors->has('golongan') && !old('id')" focusable>
        <form method="post" action="{{ route('admin.master-gaji.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-bold text-gray-900 mb-4">Tambah Acuan Gaji Pokok</h2>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="golongan" value="Golongan Ruang *" />
                        <x-text-input id="golongan" name="golongan" type="text" class="mt-1 block w-full" :value="old('golongan')" required placeholder="Cth: III/c" maxlength="5" />
                        <x-input-error :messages="$errors->get('golongan')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="masa_kerja" value="Masa Kerja (Tahun) *" />
                        <x-text-input id="masa_kerja" name="masa_kerja" type="number" min="0" class="mt-1 block w-full" :value="old('masa_kerja')" required placeholder="0" />
                        <x-input-error :messages="$errors->get('masa_kerja')" class="mt-2" />
                    </div>
                </div>
                <div>
                    <x-input-label for="nominal_gaji" value="Nominal Gaji Pokok (Rp) *" />
                    <x-text-input id="nominal_gaji" name="nominal_gaji" type="number" min="0" class="mt-1 block w-full" :value="old('nominal_gaji')" required placeholder="Cth: 2700000" />
                    <p class="text-xs text-gray-500 mt-1">Input angka bulat tanpa pemisah ribuan (titik/koma).</p>
                    <x-input-error :messages="$errors->get('nominal_gaji')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                <x-primary-button>Simpan Data</x-primary-button>
            </div>
        </form>
    </x-modal>

    {{-- MODAL EDIT --}}
    <div x-data="{ gaji: {} }" @open-modal-edit.window="gaji = $event.detail; $dispatch('open-modal', 'edit-gaji')">
        <x-modal name="edit-gaji" focusable>
            <form method="post" :action="`{{ route('admin.master-gaji.index') }}/${gaji.id}`" class="p-6">
                @csrf @method('PUT')
                <h2 class="text-lg font-bold text-gray-900 mb-4">Edit Acuan Gaji Pokok</h2>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="edit_golongan" value="Golongan Ruang *" />
                            <x-text-input id="edit_golongan" name="golongan" type="text" class="mt-1 block w-full bg-white/20" x-model="gaji.golongan" required maxlength="5" />
                        </div>
                        <div>
                            <x-input-label for="edit_masa_kerja" value="Masa Kerja (Tahun) *" />
                            <x-text-input id="edit_masa_kerja" name="masa_kerja" type="number" min="0" class="mt-1 block w-full bg-white/20" x-model="gaji.masa_kerja" required />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="edit_nominal_gaji" value="Nominal Gaji Pokok (Rp) *" />
                        <x-text-input id="edit_nominal_gaji" name="nominal_gaji" type="number" min="0" class="mt-1 block w-full" x-model="gaji.nominal_gaji" required />
                        <p class="text-xs text-gray-500 mt-1">Input angka bulat tanpa pemisah ribuan (titik/koma).</p>
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
