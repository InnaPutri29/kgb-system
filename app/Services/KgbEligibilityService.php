<?php

namespace App\Services;

use App\Models\MasterGaji;
use App\Models\Pegawai;
use Carbon\Carbon;

/**
 * Service khusus untuk mengelola Logika Aturan Bisnis (Rule-Based Engine)
 * dan Pencarian Gaji Otomatis (Auto-Lookup Logic) pada Sistem KGB.
 */
class KgbEligibilityService
{
    /**
     * Daftar predikat PKP yang dinyatakan lolos kualifikasi KGB
     * Sesuai PermenPAN-RB No. 6 Tahun 2022 (Minimal berpredikat "Baik").
     */
    private const PREDIKAT_LOLOS = ['Sangat Baik', 'Baik'];

    /**
     * Rule 1 (R1): Pegawai tidak boleh sedang dalam status hukuman disiplin.
     * Dasar Regulasi: PP No. 94 Tahun 2021.
     */
    public function cekHukumanDisiplin(Pegawai $pegawai): ?string
    {
        return $pegawai->is_sedang_hukuman_disiplin
            ? 'Pegawai sedang dalam status hukuman disiplin.'
            : null;
    }

    /**
     * Rule 2 (R2): Pegawai wajib memiliki data evaluasi PKP minimal 2 tahun terakhir.
     * Dasar Regulasi: PermenPAN-RB No. 6 Tahun 2022.
     */
    public function cekKetersediaanPkp(Pegawai $pegawai): ?string
    {
        $tahunSekarang = now()->year;
        $pkpCount = $pegawai->pkpEvaluasi()
            ->whereIn('tahun_penilaian', [$tahunSekarang - 1, $tahunSekarang - 2])
            ->count();

        return $pkpCount < 2
            ? 'Data PKP belum lengkap / belum diinput (minimal 2 tahun terakhir).'
            : null;
    }

    /**
     * Rule 3 (R3): Predikat PKP pada 2 tahun terakhir sekurang-kurangnya berpredikat "Baik".
     * Dasar Regulasi: PermenPAN-RB No. 6 Tahun 2022.
     */
    public function cekPredikatPkp(Pegawai $pegawai): ?string
    {
        $tahunSekarang = now()->year;
        $pkpList = $pegawai->pkpEvaluasi()
            ->whereIn('tahun_penilaian', [$tahunSekarang - 1, $tahunSekarang - 2])
            ->get();

        foreach ($pkpList as $pkp) {
            if (!in_array($pkp->predikat, self::PREDIKAT_LOLOS, true)) {
                return "Nilai PKP tahun {$pkp->tahun_penilaian} adalah '{$pkp->predikat}' (minimal berpredikat 'Baik').";
            }
        }

        return null;
    }

    /**
     * Rule 4 (R4): Mengevaluasi seluruh aturan (R1, R2, R3) untuk menentukan keputusan kelayakan.
     */
    public function validasiKgb(Pegawai $pegawai): array
    {
        $alasan = array_filter([
            $this->cekHukumanDisiplin($pegawai),
            $this->cekKetersediaanPkp($pegawai),
            $this->cekPredikatPkp($pegawai),
        ]);

        return [
            'lolos'  => empty($alasan),
            'alasan' => array_values($alasan),
        ];
    }

    /**
     * Rule 5-7 (R5 - R7): Perhitungan otomatis periode TMT KGB Baru, Masa Kerja Baru, dan TMT YAD.
     * Dasar Regulasi: PP No. 5 Tahun 2024.
     */
    public function hitungPeriodeBaru(Pegawai $pegawai): array
    {
        $tmtGajiTerakhir    = Carbon::parse($pegawai->tmt_gaji_terakhir);
        $tmtBaru            = $tmtGajiTerakhir->copy()->addYears(2);
        $tmtYad             = $tmtBaru->copy()->addYears(2);
        $masaKerjaTahunBaru = $pegawai->masa_kerja_tahun + 2;
        $masaKerjaBulanBaru = $pegawai->masa_kerja_bulan;

        return [
            'tmt_baru'              => $tmtBaru,
            'tmt_yad'               => $tmtYad,
            'masa_kerja_tahun_baru' => $masaKerjaTahunBaru,
            'masa_kerja_bulan_baru' => $masaKerjaBulanBaru,
        ];
    }

    /**
     * Auto-Lookup Logic: Mencari nominal gaji pokok baru berdasarkan Golongan & Masa Kerja
     * dari tabel acuan MasterGaji.
     */
    public function lookupGaji(?string $golongan, int $masaKerjaTahun): int
    {
        if (!$golongan) return 0;

        $row = MasterGaji::where('golongan', $golongan)
            ->where('masa_kerja', '<=', $masaKerjaTahun)
            ->orderBy('masa_kerja', 'desc')
            ->first();

        return $row ? (int) $row->nominal_gaji : 0;
    }
}
