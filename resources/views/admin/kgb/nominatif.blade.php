@extends('layouts.admin')
@section('title', 'Proses KGB - Daftar Nominatif')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Daftar Nominatif KGB</h2>
            <p class="text-sm text-gray-500 mt-1">Pegawai yang KGB-nya jatuh tempo dalam 60 hari ke depan</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            @if($jatuhTempoHariIni > 0)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200 flex-1 sm:flex-none justify-center">
                    🔴 {{ $jatuhTempoHariIni }} jatuh tempo hari ini
                </span>
            @endif
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200 flex-1 sm:flex-none justify-center">
                {{ $daftarNominatif->total() }} Pegawai Nominatif 
            </span>
        </div>
    </div>

    {{-- TABEL NOMINATIF --}}
    <div class="bg-white/50 backdrop-blur-3xl lg:bg-white lg:backdrop-blur-none rounded-[1.5rem] border border-blue-100 lg:border-slate-100 shadow-xl shadow-blue-500/10 lg:shadow-sm lg:shadow-black/5 overflow-hidden transition hover:shadow-2xl hover:shadow-blue-500/20 lg:hover:shadow-md lg:hover:shadow-black/10">
        @if($daftarNominatif->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-gray-400">
                <svg class="w-16 h-16 mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="font-semibold text-gray-500 text-lg">Semua Aman!</p>
                <p class="text-sm mt-1">Tidak ada pegawai yang KGB-nya jatuh tempo dalam 60 hari ke depan.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-blue-200/60 text-xs text-blue-900 uppercase tracking-wider border-b border-white/30">
                        <tr>
                            <th class="px-3 py-3 text-center">No</th>
                            <th class="px-3 py-3 text-left">NIP</th>
                            <th class="px-3 py-3 text-left">Nama Pegawai</th>
                            <th class="px-3 py-3 text-center">TMT Gaji Terakhir</th>
                            <th class="px-3 py-3 text-center">Jatuh Tempo KGB</th>
                            <th class="px-3 py-3 text-center">Status</th>
                            <th class="px-3 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($daftarNominatif as $index => $p)
                            @php
                                $jatuhTempo = \Carbon\Carbon::parse($p->tmt_gaji_terakhir)->addYears(2);
                                $selisih = now()->diffInDays($jatuhTempo, false);
                                $isLate = $selisih < 0;
                                $isUrgent = $selisih <= 7 && !$isLate;
                            @endphp
                            <tr class="hover:bg-white/40 transition">
                                <td class="px-3 py-3 text-gray-500 text-xs font-medium text-center">{{ $daftarNominatif->firstItem() + $index }}</td>
                                <td class="px-3 py-3 font-mono text-xs text-gray-700">{{ $p->nip }}</td>
                                <td class="px-3 py-3">
                                    <p class="font-medium text-gray-900">{{ $p->nama_lengkap }}</p>
                                    <p class="text-xs text-gray-600 mt-0.5">{{ $p->jabatan ?? '-' }}</p>
                                </td>
                                <td class="px-3 py-3 text-gray-700 text-center">{{ \Carbon\Carbon::parse($p->tmt_gaji_terakhir)->format('d/m/Y') }}</td>
                                <td class="px-3 py-3 text-gray-700 font-medium text-center">{{ $jatuhTempo->format('d/m/Y') }}</td>
                                <td class="px-3 py-3 text-center">
                                    @if($isLate)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200 whitespace-nowrap">
                                            Terlambat {{ abs((int)$selisih) }}h
                                        </span>
                                    @elseif($isUrgent)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700 border border-orange-200 whitespace-nowrap">
                                            H-{{ (int)$selisih }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700 border border-yellow-200 whitespace-nowrap">
                                            H-{{ (int)$selisih }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <button x-data @click="$dispatch('open-modal-proses', {{ $p->id }})"
                                        class="inline-flex justify-center items-center gap-1.5 text-xs font-medium bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md transition shadow-sm whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Proses KGB
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-gray-100 bg-white/20/50">
                {{ $daftarNominatif->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL PROSES KGB --}}
    <div x-cloak x-data="{
        showModal: false,
        loading: false,
        pegawai: null,
        dataModal: {},
        form: {
            nomor_sk_baru: '',
            nomor_sk_terakhir: '',
            tanggal_sk_terakhir: '',
            tanggal_ditetapkan: '',
            master_pejabat_id: '',
            selectedPejabatText: ''
        },
        openModal(id) {
            this.loading = true;
            this.pegawai = id;
            this.form = { nomor_sk_baru: '', nomor_sk_terakhir: '', tanggal_sk_terakhir: '', tanggal_ditetapkan: '', master_pejabat_id: '', selectedPejabatText: '' };
            $dispatch('open-modal', 'proses-kgb');
            
            fetch(`/admin/kgb/${id}/data-modal`)
                .then(res => res.json())
                .then(data => {
                    this.dataModal = data;
                    this.loading = false;
                })
                .catch(err => {
                    console.error(err);
                    this.loading = false;
                });
        }
    }" @open-modal-proses.window="openModal($event.detail)">
        <x-modal name="proses-kgb" focusable>
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Proses Kenaikan Gaji Berkala (KGB)</h2>

                <div x-show="loading" class="flex justify-center py-8">
                    <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>

                {{-- Warning jika tidak lolos --}}
                <div x-show="!loading && dataModal.validasi && !dataModal.validasi.lolos" class="bg-red-50 p-4 rounded-lg border border-red-200 text-red-800 text-sm mb-4">
                    <strong>Peringatan!</strong> Pegawai tidak memenuhi syarat KGB:
                    <ul class="list-disc list-inside mt-1">
                        <template x-for="alasan in dataModal.validasi?.alasan">
                            <li x-text="alasan"></li>
                        </template>
                    </ul>
                </div>

                <form x-show="!loading" method="POST" :action="`/admin/kgb/${pegawai}/proses`"
                    @submit="
                        if (dataModal.validasi && !dataModal.validasi.lolos) {
                            $event.preventDefault();
                            Swal.fire({
                                icon: 'error',
                                title: 'SK Tidak Bisa Dicetak!',
                                html: 'Proses KGB tidak dapat dilanjutkan karena pegawai belum memenuhi syarat:<br><br><ul class=\'text-left list-disc pl-5 text-sm\'><li>' + dataModal.validasi.alasan.join('</li><li>') + '</li></ul>',
                                confirmButtonColor: '#3b82f6',
                                confirmButtonText: 'Mengerti'
                            });
                        }
                    ">
                    @csrf

                    {{-- Info Pegawai --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                        <div class="col-span-1 md:col-span-2">
                            <x-input-label value="Nama Pegawai" />
                            <div class="mt-1 w-full px-3 py-2 bg-white/50 backdrop-blur-md border border-white/80 rounded-lg text-sm text-gray-900 shadow-[inset_0_2px_4px_rgba(255,255,255,0.6)] flex items-center min-h-[38px]">
                                <span x-text="dataModal.pegawai?.nama_lengkap"></span>
                            </div>
                        </div>
                        
                        <div>
                            <x-input-label value="Pangkat / Golongan" />
                            <div class="mt-1 w-full px-3 py-2 bg-white/50 backdrop-blur-md border border-white/80 rounded-lg text-sm text-gray-900 shadow-[inset_0_2px_4px_rgba(255,255,255,0.6)] flex items-center min-h-[38px]">
                                <span x-text="`${dataModal.pegawai?.pangkat || '-'} (${dataModal.pegawai?.golongan || '-'})`"></span>
                            </div>
                        </div>
                        
                        <div>
                            <x-input-label value="Masa Kerja Baru" />
                            <div class="mt-1 w-full px-3 py-2 bg-white/50 backdrop-blur-md border border-white/80 rounded-lg text-sm text-gray-900 shadow-[inset_0_2px_4px_rgba(255,255,255,0.6)] flex items-center min-h-[38px]">
                                <span x-text="`${dataModal.masa_kerja_tahun_baru || 0} Tahun ${dataModal.masa_kerja_bulan_baru || 0} Bulan`"></span>
                            </div>
                        </div>
                        
                        <div>
                            <x-input-label value="TMT KGB Baru" />
                            <div class="mt-1 w-full px-3 py-2 bg-white/50 backdrop-blur-md border border-white/80 rounded-lg text-sm text-gray-900 shadow-[inset_0_2px_4px_rgba(255,255,255,0.6)] flex items-center min-h-[38px]">
                                <span x-text="dataModal.tmt_baru"></span>
                            </div>
                        </div>
                        
                        <div>
                            <x-input-label value="Gaji Pokok Lama" />
                            <div class="mt-1 w-full px-3 py-2 bg-white/50 backdrop-blur-md border border-white/80 rounded-lg text-sm text-gray-900 shadow-[inset_0_2px_4px_rgba(255,255,255,0.6)] flex items-center min-h-[38px]">
                                <span x-text="'Rp ' + (new Intl.NumberFormat('id-ID').format(dataModal.pegawai?.gaji_pokok_terakhir || 0))"></span>
                            </div>
                        </div>

                        <div class="col-span-1 md:col-span-2">
                            <x-input-label value="Gaji Pokok Baru" class="!text-blue-700" />
                            <div class="mt-1 w-full px-3 py-2 bg-blue-100/50 backdrop-blur-md border border-blue-200 rounded-lg text-sm font-semibold text-blue-800 shadow-[inset_0_2px_4px_rgba(255,255,255,0.8)] flex items-center min-h-[38px]">
                                <span x-text="'Rp ' + (new Intl.NumberFormat('id-ID').format(dataModal.gaji_pokok_baru || 0))"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Input Fields --}}
                    <div class="space-y-4">
                        <div x-data="{ open: false, search: '' }" class="relative">
                            <x-input-label value="Pejabat Penetap SK Baru *" />
                            
                            {{-- Native select hidden for form submission and validation --}}
                            <select id="master_pejabat_id" name="master_pejabat_id" 
                                x-model="form.master_pejabat_id" 
                                class="opacity-0 absolute inset-0 w-full h-full pointer-events-none" required>
                                <option value=""></option>
                                <template x-for="p in dataModal.pejabat_list" :key="p.id">
                                    <option :value="p.id"></option>
                                </template>
                            </select>

                            {{-- Custom Dropdown Trigger --}}
                            <button type="button" @click="open = !open" @click.away="open = false" 
                                class="mt-1 w-full flex items-center justify-between text-left px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all">
                                <span x-text="form.selectedPejabatText || '-- Pilih Pejabat --'" :class="!form.master_pejabat_id ? 'text-gray-500' : 'text-gray-900'" class="truncate pr-4"></span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform shrink-0" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            {{-- Dropdown Menu --}}
                            <div x-show="open" 
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-xl overflow-hidden" x-cloak>
                                
                                {{-- Search Input --}}
                                <div class="p-2 bg-gray-50 border-b border-gray-100">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                        </div>
                                        <input type="text" x-model="search" class="w-full pl-9 pr-3 py-1.5 text-sm border-gray-200 rounded-md focus:ring-indigo-500 focus:border-indigo-500" placeholder="Cari nama atau jabatan...">
                                    </div>
                                </div>
                                
                                {{-- Options List --}}
                                <ul class="max-h-60 overflow-y-auto py-1">
                                    <li @click="form.master_pejabat_id = ''; form.selectedPejabatText = '-- Pilih Pejabat --'; open = false" 
                                        class="px-4 py-2 text-sm text-gray-500 hover:bg-gray-100 cursor-pointer">
                                        -- Batal Pilih --
                                    </li>
                                    <template x-for="p in dataModal.pejabat_list.filter(i => (i.nama_pejabat || '').toLowerCase().includes(search.toLowerCase()) || (i.nama_jabatan || '').toLowerCase().includes(search.toLowerCase()))" :key="p.id">
                                        <li @click="form.master_pejabat_id = p.id; form.selectedPejabatText = `${p.nama_pejabat} (${p.nama_jabatan})`; open = false" 
                                            class="px-4 py-2 text-sm hover:bg-indigo-50 cursor-pointer flex flex-col gap-0.5 border-l-2 transition-colors"
                                            :class="form.master_pejabat_id == p.id ? 'bg-indigo-50 border-indigo-500' : 'border-transparent'">
                                            <span class="font-semibold text-gray-800" x-text="p.nama_pejabat"></span>
                                            <span class="text-xs text-gray-500" x-text="p.nama_jabatan"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                        
                        {{-- SMART HINTS --}}
                        <div x-show="form.selectedPejabatText.toLowerCase().includes('direktur')" class="bg-blue-50 p-3 rounded-md border border-blue-200 mt-2 text-sm flex items-start justify-between" x-cloak>
                            <div class="text-blue-800">
                                <strong>Saran:</strong> Pegawai ini memiliki data SK terakhir: <br>
                                Tanggal: <span class="font-bold" x-text="dataModal.pegawai?.tanggal_sk_terakhir || '-'"></span> / 
                                Nomor: <span class="font-bold font-mono" x-text="dataModal.pegawai?.nomor_sk_terakhir || '-'"></span>
                            </div>
                            <button type="button" @click="form.nomor_sk_terakhir = dataModal.pegawai?.nomor_sk_terakhir; form.tanggal_sk_terakhir = dataModal.pegawai?.tanggal_sk_terakhir" class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded transition whitespace-nowrap ml-3 shadow-sm font-medium">
                                Gunakan Data Ini
                            </button>
                        </div>

                        <div x-show="form.selectedPejabatText.toLowerCase().includes('gubernur')" class="bg-yellow-50 p-3 rounded-md border border-yellow-200 mt-2 text-sm text-yellow-800" x-cloak>
                            <strong>⚠ Peringatan:</strong> Silakan ketik manual Tanggal dan Nomor SK Provinsi yang lama secara lengkap.
                        </div>

                        <div x-show="form.master_pejabat_id" class="space-y-4 pt-2" x-transition x-cloak>
                            
                            {{-- B. Dasar SK Sebelumnya --}}
                            <div class="p-4 bg-white/60 backdrop-blur-md border border-white/80 rounded-lg shadow-[inset_0_2px_4px_rgba(255,255,255,0.6)]">
                                <h4 class="font-semibold text-gray-700 mb-3 text-sm">Dasar SK Sebelumnya</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="tanggal_sk_terakhir" value="Tanggal SK Sebelumnya *" />
                                        <x-text-input id="tanggal_sk_terakhir" name="tanggal_sk_terakhir" type="date" class="mt-1 block w-full text-sm" x-model="form.tanggal_sk_terakhir" required />
                                    </div>
                                    <div>
                                        <x-input-label for="nomor_sk_terakhir" value="Nomor SK Sebelumnya *" />
                                        <x-text-input id="nomor_sk_terakhir" name="nomor_sk_terakhir" type="text" class="mt-1 block w-full text-sm" x-model="form.nomor_sk_terakhir" required />
                                    </div>
                                </div>
                            </div>

                            {{-- C. SK Baru --}}
                            <div class="p-4 bg-white/60 backdrop-blur-md border border-white/80 rounded-lg shadow-[inset_0_2px_4px_rgba(255,255,255,0.6)]">
                                <h4 class="font-semibold text-gray-700 mb-3 text-sm">Pembuatan SK KGB Baru</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="nomor_sk_baru" value="Nomor SK Baru *" />
                                        <div class="mt-1 flex rounded-md shadow-sm">
                                            <input type="text" id="nomor_sk_baru" name="nomor_sk_baru" 
                                                class="flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" 
                                                x-model="form.nomor_sk_baru" required>
                                            <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-white/20 text-gray-500 sm:text-sm whitespace-nowrap">
                                                /KPG.14/Kepegumas/RSP
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <x-input-label for="tanggal_ditetapkan" value="Tanggal Ditetapkan SK Baru *" />
                                        <x-text-input id="tanggal_ditetapkan" name="tanggal_ditetapkan" type="date" class="mt-1 block w-full text-sm" x-model="form.tanggal_ditetapkan" required />
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                        <x-primary-button>
                            Proses & Cetak SK
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </x-modal>
    </div>

</div>
@endsection
