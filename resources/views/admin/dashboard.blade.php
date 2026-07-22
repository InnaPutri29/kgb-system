@extends('layouts.admin')
@section('title', 'Dashboard Admin')

@section('content')
<div class="space-y-6">

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        {{-- Total Pegawai --}}
        <div class="bg-white/50 backdrop-blur-3xl rounded-[1.5rem] border border-white/80 border-t-white shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] p-5 flex items-center gap-4 hover:shadow-[0_8px_40px_0_rgba(31,38,135,0.12)] transition-all duration-300">
            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Pegawai</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalPegawai }}</p>
            </div>
        </div>

        {{-- Jatuh Tempo Hari Ini --}}
        <div class="bg-white/50 backdrop-blur-3xl rounded-[1.5rem] border border-white/80 border-t-white shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] p-5 flex items-center gap-4 hover:shadow-[0_8px_40px_0_rgba(31,38,135,0.12)] transition-all duration-300">
            <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center text-red-600 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500">KGB Jatuh Tempo Hari Ini</p>
                <p class="text-2xl font-bold text-gray-800">{{ $jatuhTempoHariIni }}</p>
            </div>
        </div>

        {{-- Nominatif (60 hari) - Clickable --}}
        <a href="{{ route('admin.kgb.nominatif') }}" class="bg-white/50 backdrop-blur-3xl rounded-[1.5rem] border border-white/80 border-t-white shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] p-5 flex items-center gap-4 hover:border-yellow-400 hover:shadow-[0_8px_40px_0_rgba(31,38,135,0.12)] transition-all duration-300 group">
            <div class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center text-yellow-600 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div class="flex-1">
                <p class="text-sm text-gray-500">Daftar Nominatif (H+60)</p>
                <p class="text-2xl font-bold text-gray-800">{{ $daftarNominatif->total() }}</p>
            </div>
            <svg class="w-5 h-5 text-gray-300 group-hover:text-yellow-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    {{-- GRAFIK --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Grafik Golongan --}}
        <div class="bg-white/50 backdrop-blur-3xl rounded-[1.5rem] border border-white/80 border-t-white shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] p-6">
            <h2 class="font-semibold text-gray-800 mb-4">Distribusi Pegawai (Golongan)</h2>
            <div class="h-64 relative flex items-center justify-center">
                <canvas id="chartGolongan"></canvas>
            </div>
        </div>

        {{-- Grafik Pangkat --}}
        <div class="bg-white/50 backdrop-blur-3xl rounded-[1.5rem] border border-white/80 border-t-white shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] p-6">
            <h2 class="font-semibold text-gray-800 mb-4">Top 5 Pangkat Pegawai</h2>
            <div class="h-64 relative flex items-center justify-center">
                <canvas id="chartPangkat"></canvas>
            </div>
        </div>
    </div>

    <div class="bg-white/50 backdrop-blur-3xl rounded-[1.5rem] border border-white/80 border-t-white shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-gray-800">Nominatif KGB Terkini</h2>
                <p class="text-xs text-gray-500 mt-0.5">5 pegawai teratas yang KGB-nya paling mendesak</p>
            </div>
            <a href="{{ route('admin.kgb.nominatif') }}"
               class="inline-flex items-center gap-1.5 text-sm text-blue-600 hover:text-blue-700 font-medium transition">
                Lihat Semua
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        @if($daftarNominatif->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="font-medium">Tidak ada pegawai yang masuk nominatif KGB</p>
                <p class="text-sm">Semua data KGB sudah up-to-date.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-blue-200/60 text-xs text-blue-900 uppercase tracking-wider border-b border-white/30">
                        <tr>
                            <th class="px-4 py-3 text-left">NIP</th>
                            <th class="px-4 py-3 text-left">Nama Pegawai</th>
                            <th class="px-4 py-3 text-left">Gol.</th>
                            <th class="px-4 py-3 text-center">TMT Gaji Terakhir</th>
                            <th class="px-4 py-3 text-center">Jatuh Tempo KGB</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($daftarNominatif->take(5) as $p)
                            @php
                                $jatuhTempo = \Carbon\Carbon::parse($p->tmt_gaji_terakhir)->addYears(2);
                                $selisih = now()->diffInDays($jatuhTempo, false);
                                $isLate = $selisih < 0;
                                $isUrgent = $selisih <= 7 && !$isLate;
                            @endphp
                            <tr class="hover:bg-white/40 transition">
                                <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $p->nip }}</td>
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $p->nama_lengkap }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $p->golongan ?? '-' }}</td>
                                <td class="px-4 py-3 text-center text-gray-600">{{ \Carbon\Carbon::parse($p->tmt_gaji_terakhir)->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-center text-gray-600">{{ $jatuhTempo->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($isLate)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            Terlambat {{ abs((int)$selisih) }}h
                                        </span>
                                    @elseif($isUrgent)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">
                                            H-{{ (int)$selisih }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                            H-{{ (int)$selisih }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($daftarNominatif->total() > 5)
                <div class="px-4 py-3 border-t border-gray-100 text-center">
                    <a href="{{ route('admin.kgb.nominatif') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                        + {{ $daftarNominatif->total() - 5 }} pegawai lainnya →
                    </a>
                </div>
            @endif
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const dataGolongan = @json($statistikGolongan);
        const dataPangkat = @json($statistikPangkat);

        // Chart Golongan (Doughnut)
        const ctxGolongan = document.getElementById('chartGolongan').getContext('2d');
        new Chart(ctxGolongan, {
            type: 'doughnut',
            data: {
                labels: dataGolongan.map(item => item.golongan),
                datasets: [{
                    data: dataGolongan.map(item => item.total),
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#6366f1', '#06b6d4', '#84cc16'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' }
                }
            }
        });

        // Chart Pangkat (Bar)
        const ctxPangkat = document.getElementById('chartPangkat').getContext('2d');
        new Chart(ctxPangkat, {
            type: 'bar',
            data: {
                labels: dataPangkat.map(item => item.pangkat.substring(0, 15) + (item.pangkat.length > 15 ? '...' : '')),
                datasets: [{
                    label: 'Jumlah Pegawai',
                    data: dataPangkat.map(item => item.total),
                    backgroundColor: '#3b82f6',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    });
</script>
@endpush
