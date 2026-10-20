<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\RiwayatKgb;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    private function getPegawaiData()
    {
        $user = User::with([
            'pegawai.riwayatKgb' => function ($query) {
                $query->latest('tmt_baru');
            },
            'pegawai.pkpEvaluasi' => function ($query) {
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
        $pkpEvaluasi = $pegawai->pkpEvaluasi;
        $tahunBerjalan = now()->year;
        
        return view('pegawai.dashboard', compact('pegawai', 'riwayatKgb', 'pkpEvaluasi', 'tahunBerjalan'));
    }

    public function kgb()
    {
        $pegawai = $this->getPegawaiData();
        if (!$pegawai) return view('pegawai.kgb', ['pegawai' => null]);

        $riwayatKgb = $pegawai->riwayatKgb;
        return view('pegawai.kgb', compact('pegawai', 'riwayatKgb'));
    }

    /**
     * Download SK KGB Final (sudah ber-TTE) milik pegawai sendiri.
     * Draf SK tidak bisa diunduh pegawai — hanya file final yang diunggah Admin.
     */
    public function downloadSk(RiwayatKgb $riwayat)
    {
        // Pastikan SK yang diunduh benar-benar milik pegawai yang login
        $user = Auth::user();
        if (!$user->pegawai || $riwayat->pegawai_id !== $user->pegawai->id) {
            abort(403, 'Anda tidak diizinkan mengunduh dokumen ini.');
        }

        if ($riwayat->status !== 'Final' || !$riwayat->file_sk_final) {
            abort(404, 'SK KGB belum ditandatangani secara elektronik (TTE).');
        }

        if (!Storage::disk('public')->exists($riwayat->file_sk_final)) {
            abort(404, 'File SK KGB Final tidak ditemukan.');
        }

        $riwayat->load('pegawai');
        $namaClean = trim(preg_replace('/_+/', '_', str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9\s]/', '', $riwayat->pegawai->nama_lengkap))), '_');
        $filename = 'SK_KGB_FINAL_' . $namaClean . '_' . $riwayat->pegawai->nip . '.pdf';

        return Storage::disk('public')->download($riwayat->file_sk_final, $filename);
    }
}
