@extends('layouts.admin')
@section('title', 'Detail Pegawai')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">Detail Data Pegawai</h2>
        <div class="flex gap-2">
            <a href="{{ route('admin.pegawai.edit', $pegawai->id) }}" class="inline-flex items-center gap-1 text-sm bg-blue-50 text-blue-700 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition font-medium border border-blue-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Pegawai
            </a>
            <a href="{{ route('admin.pegawai.index') }}" class="inline-flex items-center gap-1 text-sm bg-white text-gray-700 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition font-medium border border-gray-300">
                Kembali
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden relative">
        <div class="h-24 bg-gradient-to-r from-blue-600 to-indigo-700"></div>
        <div class="px-6 pb-6 relative">
            <div class="w-20 h-20 bg-white rounded-2xl p-1 shadow-md absolute -top-10 left-6 flex items-center justify-center text-3xl font-bold text-blue-600 border border-gray-100">
                {{ substr($pegawai->nama, 0, 1) }}
            </div>
            
            <div class="pt-14 flex flex-col md:flex-row justify-between md:items-end gap-4">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 leading-tight">{{ $pegawai->nama }}</h3>
                    <p class="text-blue-600 font-mono text-sm mt-1">{{ $pegawai->nip }}</p>
                </div>
                <div class="flex gap-3">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                        {{ $pegawai->pangkat_golongan ?? 'Golongan -' }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                        {{ $pegawai->jabatan ?? 'Jabatan -' }}
                    </span>
                </div>
            </div>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                <!-- Data Pribadi -->
                <div>
                    <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3">Informasi Pribadi</h4>
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-50">
                            <tr>
                                <td class="py-2 text-gray-500 w-1/3">Tempat Lahir</td>
                                <td class="py-2 text-gray-800 font-medium">{{ $pegawai->tempat_lahir ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">Tanggal Lahir</td>
                                <td class="py-2 text-gray-800 font-medium">{{ $pegawai->tanggal_lahir ? $pegawai->tanggal_lahir->translatedFormat('d F Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">Pendidikan</td>
                                <td class="py-2 text-gray-800 font-medium">{{ $pegawai->pendidikan_terakhir ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">Hukuman Disiplin</td>
                                <td class="py-2 text-gray-800 font-medium">
                                    @if($pegawai->sedang_hukuman_disiplin)
                                        <span class="text-red-600 font-semibold">Ya (Sedang Menjalani)</span>
                                    @else
                                        <span class="text-green-600">Tidak</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Kepegawaian & Gaji -->
                <div>
                    <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3">Kepegawaian & Gaji</h4>
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-50">
                            <tr>
                                <td class="py-2 text-gray-500 w-1/3">Unit Kerja</td>
                                <td class="py-2 text-gray-800 font-medium">{{ $pegawai->unit_kerja ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">TMT CPNS</td>
                                <td class="py-2 text-gray-800 font-medium">{{ $pegawai->tmt_cpns ? $pegawai->tmt_cpns->translatedFormat('d F Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">TMT PNS</td>
                                <td class="py-2 text-gray-800 font-medium">{{ $pegawai->tmt_pns ? $pegawai->tmt_pns->translatedFormat('d F Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">Masa Kerja</td>
                                <td class="py-2 text-gray-800 font-medium">{{ $pegawai->masa_kerja_tahun }} Tahun, {{ $pegawai->masa_kerja_bulan }} Bulan</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">Gaji Pokok Terakhir</td>
                                <td class="py-2 text-gray-800 font-medium">Rp {{ number_format($pegawai->gaji_pokok_terakhir, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">TMT Gaji Terakhir</td>
                                <td class="py-2 text-gray-800 font-medium">{{ $pegawai->tmt_gaji_terakhir ? $pegawai->tmt_gaji_terakhir->translatedFormat('d F Y') : '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat SK KGB -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Riwayat KGB Pegawai</h3>
            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">{{ $pegawai->riwayatKgb->count() }} Dokumen</span>
        </div>
        
        @if($pegawai->riwayatKgb->isEmpty())
            <div class="p-8 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p>Belum ada riwayat SK KGB untuk pegawai ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3 text-left">Nomor SK</th>
                            <th class="px-6 py-3 text-left">Tgl Ditetapkan</th>
                            <th class="px-6 py-3 text-left">TMT Baru</th>
                            <th class="px-6 py-3 text-left">Masa Kerja</th>
                            <th class="px-6 py-3 text-left">Gaji Pokok Baru</th>
                            <th class="px-6 py-3 text-left">Jatuh Tempo YAD</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($pegawai->riwayatKgb as $riwayat)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-3 font-medium text-gray-800">{{ $riwayat->nomor_sk_baru }}</td>
                                <td class="px-6 py-3 text-gray-600">{{ \Carbon\Carbon::parse($riwayat->tanggal_ditetapkan)->format('d/m/Y') }}</td>
                                <td class="px-6 py-3 text-gray-600 font-medium">{{ \Carbon\Carbon::parse($riwayat->tmt_baru)->format('d/m/Y') }}</td>
                                <td class="px-6 py-3 text-gray-600">{{ $riwayat->masa_kerja_tahun_baru }} Thn, {{ $riwayat->masa_kerja_bulan_baru }} Bln</td>
                                <td class="px-6 py-3 text-gray-600">Rp {{ number_format($riwayat->gaji_pokok_baru, 0, ',', '.') }}</td>
                                <td class="px-6 py-3 text-gray-600 text-xs">{{ \Carbon\Carbon::parse($riwayat->tmt_yad)->format('d/m/Y') }}</td>
                                <td class="px-6 py-3 text-right">
                                    <a href="{{ route('admin.kgb.download-pdf', $riwayat->id) }}" class="inline-flex items-center gap-1 text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg transition font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        Unduh
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- SKP Evaluasi --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden" x-data="{ openEdit: false, editSkp: {} }">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Rekap SKP (Penilaian Kinerja)</h3>
                <p class="text-xs text-gray-400 mt-0.5">Syarat KGB: predikat minimal <strong>Baik</strong> selama 2 tahun berturut-turut.</p>
            </div>
            <button x-data @click="$dispatch('open-modal', 'add-skp')"
                class="inline-flex items-center gap-1.5 text-sm bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg transition font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah SKP
            </button>
        </div>

        @if($pegawai->skpEvaluasi->isEmpty())
            <div class="p-8 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <p class="font-medium">Belum ada data SKP</p>
                <p class="text-sm">Tambahkan data penilaian kinerja tahunan untuk pegawai ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left">Tahun Penilaian</th>
                            <th class="px-6 py-3 text-left">Predikat</th>
                            <th class="px-6 py-3 text-left">Bukti SKP</th>
                            <th class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($pegawai->skpEvaluasi as $skp)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-3 font-bold text-gray-800">{{ $skp->tahun_penilaian }}</td>
                            <td class="px-6 py-3">
                                @php
                                    $colors = [
                                        'Sangat Baik' => 'bg-green-100 text-green-800',
                                        'Baik'        => 'bg-blue-100 text-blue-800',
                                        'Cukup'       => 'bg-yellow-100 text-yellow-800',
                                        'Kurang'      => 'bg-orange-100 text-orange-800',
                                        'Sangat Kurang' => 'bg-red-100 text-red-800',
                                    ];
                                    $color = $colors[$skp->predikat] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $color }}">
                                    {{ $skp->predikat }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                @if($skp->file_bukti_skp)
                                    <a href="{{ Storage::url($skp->file_bukti_skp) }}" target="_blank"
                                        class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 hover:underline">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        Lihat File
                                    </a>
                                @else
                                    <span class="text-gray-400 text-xs italic">Tidak ada</span>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Edit --}}
                                    <button @click="editSkp = {{ $skp->toJson() }}; openEdit = true"
                                        class="p-1.5 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-md transition" title="Edit SKP">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    {{-- Hapus --}}
                                    <form action="{{ route('admin.pegawai.skp.destroy', [$pegawai, $skp]) }}" method="POST"
                                        onsubmit="return confirm('Hapus data SKP tahun {{ $skp->tahun_penilaian }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-md transition" title="Hapus SKP">
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
        @endif

        {{-- MODAL EDIT SKP (inline Alpine) --}}
        <div x-show="openEdit" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/40" @click="openEdit = false"></div>
                <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Edit Data SKP</h2>
                    <form :action="`{{ url('admin/pegawai/'.$pegawai->id.'/skp') }}/${editSkp.id}`" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="edit_tahun" value="Tahun Penilaian *" />
                                <x-text-input id="edit_tahun" name="tahun_penilaian" type="number" min="2000" :max="date('Y')" class="mt-1 block w-full" x-model="editSkp.tahun_penilaian" required />
                            </div>
                            <div>
                                <x-input-label for="edit_predikat" value="Predikat SKP *" />
                                <select id="edit_predikat" name="predikat" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full text-sm" x-model="editSkp.predikat" required>
                                    <option value="">-- Pilih Predikat --</option>
                                    @foreach(['Sangat Baik','Baik','Cukup','Kurang','Sangat Kurang'] as $p)
                                        <option value="{{ $p }}">{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="edit_file_skp" value="Ganti File Bukti SKP (Opsional)" />
                                <input id="edit_file_skp" name="file_bukti_skp" type="file" accept=".pdf,.jpg,.jpeg,.png"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                                <p class="text-xs text-gray-400 mt-1">Biarkan kosong jika tidak ingin mengganti file.</p>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <x-secondary-button type="button" @click="openEdit = false">Batal</x-secondary-button>
                            <x-primary-button>Simpan Perubahan</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH SKP --}}
    <x-modal name="add-skp" focusable>
        <form method="POST" action="{{ route('admin.pegawai.skp.store', $pegawai) }}" enctype="multipart/form-data" class="p-6">
            @csrf
            <h2 class="text-lg font-bold text-gray-900 mb-4">Tambah Data SKP</h2>
            <div class="space-y-4">
                <div>
                    <x-input-label for="tahun_penilaian" value="Tahun Penilaian *" />
                    <x-text-input id="tahun_penilaian" name="tahun_penilaian" type="number" min="2000" max="{{ date('Y') }}" class="mt-1 block w-full" :value="old('tahun_penilaian', date('Y') - 1)" required />
                    <x-input-error :messages="$errors->get('tahun_penilaian')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="predikat" value="Predikat SKP *" />
                    <select id="predikat" name="predikat" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full text-sm" required>
                        <option value="">-- Pilih Predikat --</option>
                        @foreach(['Sangat Baik','Baik','Cukup','Kurang','Sangat Kurang'] as $p)
                            <option value="{{ $p }}" {{ old('predikat') == $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('predikat')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="file_bukti_skp" value="File Bukti SKP (Opsional)" />
                    <input id="file_bukti_skp" name="file_bukti_skp" type="file" accept=".pdf,.jpg,.jpeg,.png"
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                    <p class="text-xs text-gray-400 mt-1">Maks. 5 MB. Format: PDF, JPG, PNG.</p>
                    <x-input-error :messages="$errors->get('file_bukti_skp')" class="mt-2" />
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                <x-primary-button>Simpan</x-primary-button>
            </div>
        </form>
    </x-modal>

</div>
@endsection
