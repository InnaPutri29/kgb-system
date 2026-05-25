<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\RiwayatKgb;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil data user beserta relasi pegawai, riwayat KGB, dan SKP evaluasi-nya
        $user = User::with([
            'pegawai.riwayatKgb' => function ($query) {
                $query->latest('tmt_baru');
            },
            'pegawai.skpEvaluasi' => function ($query) {
                $query->latest('tahun_penilaian');
            }
        ])->find(Auth::id());

        if (!$user) {
            abort(403, 'Pengguna tidak ditemukan.');
        }

        $pegawai = $user->pegawai;

        // Jika user ini entah kenapa tidak punya data pegawai yang terhubung
        if (!$pegawai) {
            return view('pegawai.dashboard', [
                'pegawai' => null,
                'riwayatKgb' => collect(),
                'skpEvaluasi' => collect(),
                'skpPeriodeBerjalan' => null,
                'tahunBerjalan' => now()->year,
            ]);
        }

        $riwayatKgb = $pegawai->riwayatKgb;
        $skpEvaluasi = $pegawai->skpEvaluasi;
        
        $tahunBerjalan = now()->year;
        $skpPeriodeBerjalan = $skpEvaluasi->where('tahun_penilaian', $tahunBerjalan)->first();

        return view('pegawai.dashboard', compact('pegawai', 'riwayatKgb', 'skpEvaluasi', 'skpPeriodeBerjalan', 'tahunBerjalan'));
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

        $riwayat->load('pegawai');

        $instansi = \App\Models\PengaturanInstansi::first();

        $pdf = Pdf::loadView('admin.kgb.sk-pdf', [
            'riwayat'          => $riwayat,
            'pegawai'          => $riwayat->pegawai,
            'instansi'         => $instansi,
            'pejabatTerdahulu' => $riwayat->pegawai->masterPejabat,
        ])->setPaper('f4', 'portrait');

        $namaClean = trim(preg_replace('/_+/', '_', str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9\s]/', '', $riwayat->pegawai->nama_lengkap))), '_');
        $filename = 'SK_KGB_' . $namaClean . '_' . $riwayat->pegawai->nip . '.pdf';

        return $pdf->download($filename);
    }
}
