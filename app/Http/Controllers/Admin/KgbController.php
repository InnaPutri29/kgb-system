<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterGaji;
use App\Models\MasterPejabat;
use App\Models\Pegawai;
use App\Models\RiwayatKgb;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KgbController extends Controller
{
    /**
     * Tampilkan data untuk modal proses KGB (JSON/partial).
     */
    public function getDataForModal(Pegawai $pegawai)
    {
        $pejabatList = MasterPejabat::orderBy('nama_pejabat')->get();
        $tmtGajiTerakhir = Carbon::parse($pegawai->tmt_gaji_terakhir);
        $tmtKgbBaru = $tmtGajiTerakhir->copy()->addYears(2);

        // Hitung masa kerja baru
        $masaKerjaTahunBaru = $pegawai->masa_kerja_tahun + 2;
        $masaKerjaBulanBaru = $pegawai->masa_kerja_bulan;

        // Lookup gaji baru di master_gaji
        $gajiPokok = $this->lookupGaji($pegawai->pangkat_golongan, $masaKerjaTahunBaru);

        // Validasi
        $validasi = $this->validasiKgb($pegawai);

        return response()->json([
            'pegawai'            => $pegawai,
            'tmt_kgb_baru'       => $tmtKgbBaru->format('Y-m-d'),
            'masa_kerja_tahun_baru' => $masaKerjaTahunBaru,
            'masa_kerja_bulan_baru' => $masaKerjaBulanBaru,
            'gaji_pokok_baru'    => $gajiPokok,
            'pejabat_list'       => $pejabatList,
            'validasi'           => $validasi,
        ]);
    }

    /**
     * Proses KGB: simpan ke riwayat_kgb dan generate PDF.
     */
    public function proses(Request $request, Pegawai $pegawai)
    {
        $request->validate([
            'nomor_sk_baru'    => 'required|string|max:255',
            'tanggal_sk_baru'  => 'required|date',
            'nomor_sk_lama'    => 'nullable|string|max:255',
            'tanggal_sk_lama'  => 'nullable|date',
            'pejabat_id'       => 'required|exists:master_pejabat,id',
        ], [
            'nomor_sk_baru.required'   => 'Nomor SK Baru wajib diisi.',
            'tanggal_sk_baru.required' => 'Tanggal SK Baru wajib diisi.',
            'pejabat_id.required'      => 'Pejabat penetap SK terdahulu wajib dipilih.',
        ]);

        // Validasi rule-based
        $validasi = $this->validasiKgb($pegawai);
        if (!$validasi['lolos']) {
            return back()->with('error', 'Pegawai tidak memenuhi syarat KGB: ' . implode(', ', $validasi['alasan']));
        }

        // Kalkulasi
        $tmtGajiTerakhir  = Carbon::parse($pegawai->tmt_gaji_terakhir);
        $tmtKgbBaru        = $tmtGajiTerakhir->copy()->addYears(2);
        $masaKerjaTahunBaru = $pegawai->masa_kerja_tahun + 2;
        $masaKerjaBulanBaru = $pegawai->masa_kerja_bulan;
        $gajiPokok          = $this->lookupGaji($pegawai->pangkat_golongan, $masaKerjaTahunBaru);

        // Simpan ke riwayat_kgb
        $riwayat = RiwayatKgb::create([
            'pegawai_id'            => $pegawai->id,
            'nomor_sk_lama'         => $request->nomor_sk_lama,
            'tanggal_sk_lama'       => $request->tanggal_sk_lama,
            'nomor_sk_baru'         => $request->nomor_sk_baru,
            'tanggal_sk_baru'       => $request->tanggal_sk_baru,
            'tmt_kgb_baru'          => $tmtKgbBaru,
            'masa_kerja_tahun_baru' => $masaKerjaTahunBaru,
            'masa_kerja_bulan_baru' => $masaKerjaBulanBaru,
            'gaji_pokok_baru'       => $gajiPokok,
            'pejabat_id'            => $request->pejabat_id,
        ]);

        // Update data pegawai dengan TMT & gaji yang baru
        $pegawai->update([
            'tmt_gaji_terakhir'   => $tmtKgbBaru,
            'masa_kerja_tahun'    => $masaKerjaTahunBaru,
            'masa_kerja_bulan'    => $masaKerjaBulanBaru,
            'gaji_pokok_terakhir' => $gajiPokok,
        ]);

        // Generate PDF SK
        $filePath = $this->generateAndSavePdf($riwayat, $pegawai);
        $riwayat->update(['file_sk' => $filePath]);

        return redirect()->route('admin.dashboard')
            ->with('success', "KGB {$pegawai->nama} berhasil diproses. SK siap diunduh.");
    }

    /**
     * Download/generate PDF SK KGB.
     */
    public function downloadPdf(RiwayatKgb $riwayat)
    {
        $riwayat->load(['pegawai', 'pejabat']);
        $instansi = \App\Models\PengaturanInstansi::first();

        $pdf = Pdf::loadView('admin.kgb.sk-pdf', [
            'riwayat' => $riwayat,
            'pegawai' => $riwayat->pegawai,
            'pejabat' => $riwayat->pejabat,
            'instansi' => $instansi,
        ])->setPaper('a4', 'portrait');

        $filename = 'SK_KGB_' . $riwayat->pegawai->nip . '_' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    // -----------------------------------------------------------------------
    // PRIVATE HELPERS
    // -----------------------------------------------------------------------

    /**
     * Lookup gaji pokok berdasarkan golongan dan masa kerja.
     * Mencari baris dengan masa_kerja <= masa_kerja_baru, ambil yang terbesar.
     */
    private function lookupGaji(string $golongan, int $masaKerjaTahun): float
    {
        $row = MasterGaji::where('golongan', $golongan)
            ->where('masa_kerja', '<=', $masaKerjaTahun)
            ->orderBy('masa_kerja', 'desc')
            ->first();

        return $row ? (float) $row->nominal_gaji : 0;
    }

    /**
     * Validasi syarat KGB:
     * 1. Tidak sedang hukuman disiplin
     * 2. SKP 2 tahun terakhir minimal "Baik"
     */
    private function validasiKgb(Pegawai $pegawai): array
    {
        $lolos  = true;
        $alasan = [];

        // Cek hukuman disiplin
        if ($pegawai->sedang_hukuman_disiplin) {
            $lolos    = false;
            $alasan[] = 'Sedang menjalani hukuman disiplin';
        }

        // Cek SKP 2 tahun terakhir
        $tahunSekarang = now()->year;
        $skpList = $pegawai->skpEvaluasi()
            ->whereIn('tahun_penilaian', [$tahunSekarang - 1, $tahunSekarang - 2])
            ->get();

        $nilaiTidakLulus = ['Cukup', 'Kurang', 'Sangat Kurang', 'Buruk'];
        foreach ($skpList as $skp) {
            if (in_array($skp->predikat, $nilaiTidakLulus)) {
                $lolos    = false;
                $alasan[] = "Nilai SKP tahun {$skp->tahun_penilaian} adalah '{$skp->predikat}' (tidak memenuhi syarat)";
                break;
            }
        }

        return ['lolos' => $lolos, 'alasan' => $alasan];
    }

    /**
     * Generate PDF dan simpan ke storage.
     */
    private function generateAndSavePdf(RiwayatKgb $riwayat, Pegawai $pegawai): string
    {
        $pejabat = MasterPejabat::find($riwayat->pejabat_id);
        $instansi = \App\Models\PengaturanInstansi::first();

        $pdf = Pdf::loadView('admin.kgb.sk-pdf', [
            'riwayat' => $riwayat,
            'pegawai' => $pegawai,
            'pejabat' => $pejabat,
            'instansi' => $instansi,
        ])->setPaper('a4', 'portrait');

        $dir      = 'sk_kgb';
        $filename = "SK_KGB_{$pegawai->nip}_{$riwayat->tmt_kgb_baru->format('Ymd')}.pdf";
        $fullPath = "{$dir}/{$filename}";

        Storage::disk('public')->put($fullPath, $pdf->output());

        return $fullPath;
    }
}
