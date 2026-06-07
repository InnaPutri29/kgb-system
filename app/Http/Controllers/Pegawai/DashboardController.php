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
    private function getPegawaiData()
    {
        $user = User::with([
            'pegawai.riwayatKgb' => function ($query) {
                $query->latest('tmt_baru');
            },
            'pegawai.skpEvaluasi' => function ($query) {
                $query->latest('tahun_penilaian');
            }
        ])->find(Auth::id());

        if (!$user) abort(403, 'Pengguna tidak ditemukan.');
        return $user->pegawai;
    }

    public function index()
    {
        $pegawai = $this->getPegawaiData();
        if (!$pegawai) return view('pegawai.dashboard', ['pegawai' => null]);

        $riwayatKgb = $pegawai->riwayatKgb;
        $skpEvaluasi = $pegawai->skpEvaluasi;
        $tahunBerjalan = now()->year;
        
        return view('pegawai.dashboard', compact('pegawai', 'riwayatKgb', 'skpEvaluasi', 'tahunBerjalan'));
    }

    public function kgb()
    {
        $pegawai = $this->getPegawaiData();
        if (!$pegawai) return view('pegawai.kgb', ['pegawai' => null]);

        $riwayatKgb = $pegawai->riwayatKgb;
        return view('pegawai.kgb', compact('pegawai', 'riwayatKgb'));
    }

    public function skp()
    {
        $pegawai = $this->getPegawaiData();
        if (!$pegawai) return view('pegawai.skp', ['pegawai' => null]);

        $skpEvaluasi = $pegawai->skpEvaluasi;
        $tahunBerjalan = now()->year;
        $skpPeriodeBerjalan = $skpEvaluasi->where('tahun_penilaian', $tahunBerjalan)->first();

        return view('pegawai.skp', compact('pegawai', 'skpEvaluasi', 'skpPeriodeBerjalan', 'tahunBerjalan'));
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
