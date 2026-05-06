<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\RiwayatKgb;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil data user beserta relasi pegawai dan riwayat KGB-nya
        $user = Auth::user()->load(['pegawai.riwayatKgb' => function ($query) {
            $query->latest('tmt_kgb_baru');
        }]);

        $pegawai = $user->pegawai;

        // Jika user ini entah kenapa tidak punya data pegawai yang terhubung
        if (!$pegawai) {
            return view('pegawai.dashboard', [
                'pegawai' => null,
                'riwayatKgb' => collect(),
            ]);
        }

        $riwayatKgb = $pegawai->riwayatKgb;

        return view('pegawai.dashboard', compact('pegawai', 'riwayatKgb'));
    }

    /**
     * Download PDF SK KGB milik pegawai sendiri.
     */
    public function downloadSk(RiwayatKgb $riwayat)
    {
        // Pastikan SK yang didownload benar-benar milik pegawai yang login
        $user = Auth::user();
        if (!$user->pegawai || $riwayat->pegawai_id !== $user->pegawai->id) {
            abort(403, 'Anda tidak diizinkan mengunduh dokumen ini.');
        }

        $riwayat->load(['pegawai', 'pejabat']);

        $pdf = Pdf::loadView('admin.kgb.sk-pdf', [
            'riwayat' => $riwayat,
            'pegawai' => $riwayat->pegawai,
            'pejabat' => $riwayat->pejabat,
        ])->setPaper('a4', 'portrait');

        $filename = 'SK_KGB_' . $riwayat->pegawai->nip . '_' . $riwayat->tmt_kgb_baru->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }
}
