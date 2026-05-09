@extends('layouts.admin')
@section('title', 'Dashboard Admin')

@section('content')
<div class="space-y-6">

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        {{-- Total Pegawai --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Pegawai</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalPegawai }}</p>
            </div>
        </div>

        {{-- Jatuh Tempo Hari Ini --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center text-red-600 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500">KGB Jatuh Tempo Hari Ini</p>
                <p class="text-2xl font-bold text-gray-800">{{ $jatuhTempoHariIni }}</p>
            </div>
        </div>

        {{-- Nominatif (60 hari) --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center text-yellow-600 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500">Daftar Nominatif (H+60)</p>
                <p class="text-2xl font-bold text-gray-800">{{ $daftarNominatif->total() }}</p>
            </div>
        </div>
    </div>

    {{-- TABEL NOMINATIF --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-gray-800">📋 Daftar Nominatif KGB</h2>
                <p class="text-xs text-gray-500 mt-0.5">Pegawai yang KGB-nya jatuh tempo dalam 60 hari ke depan</p>
            </div>
            <a href="{{ route('admin.pegawai.import') }}"
               class="inline-flex items-center gap-1.5 text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
                Import Pegawai
            </a>
        </div>

        @if($daftarNominatif->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="font-medium">Tidak ada pegawai yang masuk nominatif KGB</p>
                <p class="text-sm">Semua data KGB sudah up-to-date.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3 text-left">NIP</th>
                            <th class="px-4 py-3 text-left">Nama Pegawai</th>
                            <th class="px-4 py-3 text-left">Gol.</th>
                            <th class="px-4 py-3 text-left">TMT Gaji Terakhir</th>
                            <th class="px-4 py-3 text-left">Jatuh Tempo KGB</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($daftarNominatif as $p)
                            @php
                                $jatuhTempo = \Carbon\Carbon::parse($p->tmt_gaji_terakhir)->addYears(2);
                                $selisih = now()->diffInDays($jatuhTempo, false);
                                $isLate = $selisih < 0;
                                $isUrgent = $selisih <= 7 && !$isLate;
                            @endphp
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $p->nip }}</td>
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $p->nama_lengkap }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $p->pangkat_golongan ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $p->tmt_gaji_terakhir ? \Carbon\Carbon::parse($p->tmt_gaji_terakhir)->format('d/m/Y') : '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $jatuhTempo->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    @if($isLate)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            ⚠ Terlambat {{ abs((int)$selisih) }}h
                                        </span>
                                    @elseif($isUrgent)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">
                                            🔔 H-{{ (int)$selisih }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                            H-{{ (int)$selisih }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <button x-data @click="$dispatch('open-modal-proses', {{ $p->id }})" class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg transition font-medium">
                                        Proses KGB
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-gray-100">
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
            nomor_sk_lama: '',
            tanggal_sk_lama: '',
            nomor_sk_baru: '',
            tanggal_sk_baru: '',
            pejabat_id: ''
        },
        openModal(id) {
            this.loading = true;
            this.pegawai = id;
            $dispatch('open-modal', 'proses-kgb');
            
            fetch(`/admin/kgb/${id}/data-modal`)
                .then(res => res.json())
                .then(data => {
                    this.dataModal = data;
                    if(data.pegawai.riwayat_kgb && data.pegawai.riwayat_kgb.length > 0) {
                        let lastKgb = data.pegawai.riwayat_kgb[0];
                        this.form.nomor_sk_lama = lastKgb.nomor_sk_baru;
                        this.form.tanggal_sk_lama = lastKgb.tanggal_ditetapkan;
                    }
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

                <div x-show="!loading && dataModal.validasi && !dataModal.validasi.lolos" class="bg-red-50 p-4 rounded-lg border border-red-200 text-red-800 text-sm mb-4">
                    <strong>Peringatan!</strong> Pegawai tidak memenuhi syarat KGB:
                    <ul class="list-disc list-inside mt-1">
                        <template x-for="alasan in dataModal.validasi?.alasan">
                            <li x-text="alasan"></li>
                        </template>
                    </ul>
                </div>

                <form x-show="!loading" method="POST" :action="`/admin/kgb/${pegawai}/proses`">
                    @csrf

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-gray-50 p-3 rounded-lg text-sm">
                            <p class="text-gray-500 mb-1">Nama Pegawai</p>
                            <p class="font-semibold text-gray-800" x-text="dataModal.pegawai?.nama_lengkap"></p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg text-sm">
                            <p class="text-gray-500 mb-1">TMT KGB Baru</p>
                            <p class="font-semibold text-gray-800" x-text="dataModal.tmt_baru"></p>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-lg text-sm">
                            <p class="text-blue-600 mb-1">Gaji Pokok Baru</p>
                            <p class="font-bold text-blue-800" x-text="'Rp ' + (new Intl.NumberFormat('id-ID').format(dataModal.gaji_pokok_baru || 0))"></p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg text-sm">
                            <p class="text-gray-500 mb-1">Masa Kerja Baru</p>
                            <p class="font-semibold text-gray-800" x-text="`${dataModal.masa_kerja_tahun_baru || 0} Tahun ${dataModal.masa_kerja_bulan_baru || 0} Bulan`"></p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="nomor_sk_lama" value="Nomor SK Lama (Opsional)" />
                                <x-text-input id="nomor_sk_lama" name="nomor_sk_lama" type="text" class="mt-1 block w-full text-sm" x-model="form.nomor_sk_lama" />
                            </div>
                            <div>
                                <x-input-label for="tanggal_sk_lama" value="Tanggal SK Lama (Opsional)" />
                                <x-text-input id="tanggal_sk_lama" name="tanggal_sk_lama" type="date" class="mt-1 block w-full text-sm" x-model="form.tanggal_sk_lama" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="nomor_sk_baru" value="Nomor SK Baru *" />
                                <x-text-input id="nomor_sk_baru" name="nomor_sk_baru" type="text" class="mt-1 block w-full text-sm" x-model="form.nomor_sk_baru" required />
                            </div>
                            <div>
                                <x-input-label for="tanggal_ditetapkan" value="Tanggal SK Baru *" />
                                <x-text-input id="tanggal_ditetapkan" name="tanggal_ditetapkan" type="date" class="mt-1 block w-full text-sm" x-model="form.tanggal_sk_baru" required />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="pejabat_id" value="Pejabat Penetap SK Terdahulu *" />
                            <select id="pejabat_id" name="pejabat_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full text-sm" x-model="form.pejabat_id" required>
                                <option value="">-- Pilih Pejabat --</option>
                                <template x-for="p in dataModal.pejabat_list" :key="p.id">
                                    <option :value="p.id" x-text="`${p.nama_pejabat} (${p.nama_jabatan})`"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <x-secondary-button x-on:click="showModal = false; $dispatch('close')">Batal</x-secondary-button>
                        <x-primary-button x-bind:disabled="dataModal.validasi && !dataModal.validasi.lolos">
                            Proses & Cetak SK
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </x-modal>
    </div>

</div>
@endsection
