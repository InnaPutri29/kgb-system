<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Total pegawai
        $totalPegawai = Pegawai::count();

        // Nominatif: KGB jatuh tempo dalam 60 hari ke depan
        // TMT gaji terakhir + 2 tahun harus <= hari ini + 60 hari
        $batasTanggal = $today->copy()->addDays(60);
        $daftarNominatif = Pegawai::whereNotNull('tmt_gaji_terakhir')
            ->where('is_sedang_hukuman_disiplin', false)
            ->whereRaw('DATE_ADD(tmt_gaji_terakhir, INTERVAL 2 YEAR) <= ?', [$batasTanggal])
            ->whereDoesntHave('riwayatKgb', function ($q) use ($today) {
                // Pastikan belum diproses bulan ini
                $q->whereYear('riwayat_kgb.tmt_baru', $today->year)
                  ->whereMonth('riwayat_kgb.tmt_baru', $today->month);
            })
            ->with('riwayatKgb')
            ->orderByRaw('DATE_ADD(tmt_gaji_terakhir, INTERVAL 2 YEAR) ASC')
            ->paginate(request('per_page', 10))->withQueryString();

        // Sudah jatuh tempo hari ini
        $jatuhTempoHariIni = Pegawai::whereNotNull('tmt_gaji_terakhir')
            ->whereRaw('DATE_ADD(tmt_gaji_terakhir, INTERVAL 2 YEAR) = ?', [$today])
            ->count();

        // Data Grafik Golongan
        $statistikGolongan = Pegawai::select('golongan', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->whereNotNull('golongan')
            ->where('golongan', '!=', '')
            ->groupBy('golongan')
            ->get();

        // Data Grafik Pangkat (Top 5)
        $statistikPangkat = Pegawai::select('pangkat', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->whereNotNull('pangkat')
            ->where('pangkat', '!=', '')
            ->groupBy('pangkat')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalPegawai',
            'daftarNominatif',
            'jatuhTempoHariIni',
            'statistikGolongan',
            'statistikPangkat'
        ));
    }
}
