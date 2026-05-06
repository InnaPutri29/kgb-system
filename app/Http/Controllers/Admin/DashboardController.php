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
            ->where('sedang_hukuman_disiplin', false)
            ->whereRaw('DATE_ADD(tmt_gaji_terakhir, INTERVAL 2 YEAR) <= ?', [$batasTanggal])
            ->whereDoesntHave('riwayatKgb', function ($q) use ($today) {
                // Pastikan belum diproses bulan ini
                $q->whereYear('tmt_kgb_baru', $today->year)
                  ->whereMonth('tmt_kgb_baru', $today->month);
            })
            ->with('riwayatKgb')
            ->orderByRaw('DATE_ADD(tmt_gaji_terakhir, INTERVAL 2 YEAR) ASC')
            ->paginate(10);

        // Sudah jatuh tempo hari ini
        $jatuhTempoHariIni = Pegawai::whereNotNull('tmt_gaji_terakhir')
            ->whereRaw('DATE_ADD(tmt_gaji_terakhir, INTERVAL 2 YEAR) = ?', [$today])
            ->count();

        return view('admin.dashboard', compact(
            'totalPegawai',
            'daftarNominatif',
            'jatuhTempoHariIni'
        ));
    }
}
