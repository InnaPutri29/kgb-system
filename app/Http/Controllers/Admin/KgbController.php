<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterGaji;
use App\Models\MasterPejabat;
use App\Models\Pegawai;
use App\Models\PengaturanInstansi;
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
        $pegawai->load(['riwayatKgb' => function ($query) {
            $query->latest('tmt_baru');
        }]);

        $pejabatList     = MasterPejabat::orderBy('nama_jabatan')->get();
        $tmtGajiTerakhir = Carbon::parse($pegawai->tmt_gaji_terakhir);
        $tmtBaru         = $tmtGajiTerakhir->copy()->addYears(2);

        $masaKerjaTahunBaru = $pegawai->masa_kerja_tahun + 2;
        $masaKerjaBulanBaru = $pegawai->masa_kerja_bulan;

        $gajiPokokBaru = $this->lookupGaji($pegawai->golongan, $masaKerjaTahunBaru);
        $validasi      = $this->validasiKgb($pegawai);

        $pegawaiArray = $pegawai->toArray();
        $pegawaiArray['tanggal_sk_terakhir'] = $pegawai->tanggal_sk_terakhir ? $pegawai->tanggal_sk_terakhir->format('Y-m-d') : null;

        return response()->json([
            'pegawai'               => $pegawaiArray,
            'tmt_baru'              => $tmtBaru->format('Y-m-d'),
            'masa_kerja_tahun_baru' => $masaKerjaTahunBaru,
            'masa_kerja_bulan_baru' => $masaKerjaBulanBaru,
            'gaji_pokok_baru'       => $gajiPokokBaru,
            'pejabat_list'          => $pejabatList,
            'validasi'              => $validasi,
        ]);
    }

    /**
     * Tampilkan daftar riwayat KGB.
     */
    public function index()
    {
        $riwayatKgb = RiwayatKgb::with('pegawai')
            ->latest()
            ->paginate(20);

        return view('admin.kgb.index', compact('riwayatKgb'));
    }

    /**
     * Tampilkan Daftar Nominatif KGB — pegawai yang KGB-nya jatuh tempo dalam 60 hari ke depan.
     */
    public function nominatif()
    {
        $today = Carbon::today();
        $batasTanggal = $today->copy()->addDays(60);

        $daftarNominatif = Pegawai::whereNotNull('tmt_gaji_terakhir')
            ->where('is_sedang_hukuman_disiplin', false)
            ->whereRaw('DATE_ADD(tmt_gaji_terakhir, INTERVAL 2 YEAR) <= ?', [$batasTanggal])
            ->whereDoesntHave('riwayatKgb', function ($q) use ($today) {
                $q->whereYear('riwayat_kgb.tmt_baru', $today->year)
                  ->whereMonth('riwayat_kgb.tmt_baru', $today->month);
            })
            ->with('riwayatKgb')
            ->orderByRaw('DATE_ADD(tmt_gaji_terakhir, INTERVAL 2 YEAR) ASC')
            ->paginate(15);

        // Hitung yang sudah jatuh tempo hari ini
        $jatuhTempoHariIni = Pegawai::whereNotNull('tmt_gaji_terakhir')
            ->whereRaw('DATE_ADD(tmt_gaji_terakhir, INTERVAL 2 YEAR) = ?', [$today])
            ->count();

        $pejabatList = MasterPejabat::orderBy('nama_jabatan')->get();

        return view('admin.kgb.nominatif', compact(
            'daftarNominatif',
            'jatuhTempoHariIni',
            'pejabatList'
        ));
    }

    /**
     * Proses KGB: simpan ke riwayat_kgb dan generate PDF.
     */
    public function proses(Request $request, Pegawai $pegawai)
    {
        $request->validate([
            'nomor_sk_baru'       => 'required|string|max:255',
            'nomor_sk_terakhir'   => 'required|string|max:255',
            'tanggal_sk_terakhir' => 'required|date',
            'tanggal_ditetapkan'  => 'required|date',
            'master_pejabat_id'   => 'required|exists:master_pejabat,id',
        ], [
            'nomor_sk_baru.required'       => 'Nomor SK Baru wajib diisi.',
            'nomor_sk_terakhir.required'   => 'Nomor SK Sebelumnya wajib diisi.',
            'tanggal_sk_terakhir.required' => 'Tanggal SK Sebelumnya wajib diisi.',
            'tanggal_ditetapkan.required'  => 'Tanggal penetapan SK Baru wajib diisi.',
            'master_pejabat_id.required'   => 'Pejabat penetap SK wajib dipilih.',
        ]);

        // Validasi rule-based
        $validasi = $this->validasiKgb($pegawai);
        if (!$validasi['lolos']) {
            return back()->with('error', 'Pegawai tidak memenuhi syarat KGB: ' . implode(', ', $validasi['alasan']));
        }

        // Kalkulasi
        $tmtGajiTerakhir    = Carbon::parse($pegawai->tmt_gaji_terakhir);
        $tmtBaru            = $tmtGajiTerakhir->copy()->addYears(2);
        $tmtYad             = $tmtBaru->copy()->addYears(2);
        $masaKerjaTahunBaru = $pegawai->masa_kerja_tahun + 2;
        $masaKerjaBulanBaru = $pegawai->masa_kerja_bulan;
        $gajiPokokBaru      = $this->lookupGaji($pegawai->golongan, $masaKerjaTahunBaru);
        $gajiPokokLama      = (int) $pegawai->gaji_pokok_terakhir;

        // Snapshot pejabat penetap (Direktur saat ini)
        $instansi       = PengaturanInstansi::first();
        $pejabatPenetap = $instansi ? $instansi->nama_direktur : '-';

        // Pejabat terdahulu (untuk referensi di PDF)
        $pejabatTerdahulu = MasterPejabat::find($request->master_pejabat_id);

        $nomorSkBaruLengkap = $request->nomor_sk_baru . '/KPG.14/Kepegumas/RSP';

        // Simpan ke riwayat_kgb — snapshot mati (tidak akan berubah meski data pegawai diubah)
        $riwayat = RiwayatKgb::create([
            'pegawai_id'            => $pegawai->id,
            'nomor_sk_baru'         => $nomorSkBaruLengkap,
            'tanggal_ditetapkan'    => $request->tanggal_ditetapkan,
            'tmt_baru'              => $tmtBaru,
            'gaji_pokok_lama'       => $gajiPokokLama,
            'gaji_pokok_baru'       => $gajiPokokBaru,
            'masa_kerja_tahun_baru' => $masaKerjaTahunBaru,
            'masa_kerja_bulan_baru' => $masaKerjaBulanBaru,
            'tmt_yad'               => $tmtYad,
            'pejabat_penetap'       => $pejabatPenetap,
        ]);

        // Update data live pegawai
        $pegawai->update([
            'tmt_gaji_terakhir'   => $tmtBaru,
            'masa_kerja_tahun'    => $masaKerjaTahunBaru,
            'masa_kerja_bulan'    => $masaKerjaBulanBaru,
            'gaji_pokok_terakhir' => $gajiPokokBaru,
            'nomor_sk_terakhir'   => $request->nomor_sk_terakhir,
            'tanggal_sk_terakhir' => $request->tanggal_sk_terakhir,
            'master_pejabat_id'   => $request->master_pejabat_id,
        ]);

        // Generate & simpan PDF
        $filePath = $this->generateAndSavePdf($riwayat, $pegawai, $instansi, $pejabatTerdahulu);
        $riwayat->update(['file_pdf_path' => $filePath]);

        return redirect()->route('admin.dashboard')
            ->with('success', "KGB {$pegawai->nama_lengkap} berhasil diproses. SK siap diunduh.");
    }

    /**
     * Download PDF SK KGB.
     */
    public function downloadPdf(RiwayatKgb $riwayat)
    {
        $riwayat->load('pegawai');

        $instansi = PengaturanInstansi::first();
        $pejabatTerdahulu = $riwayat->pegawai->masterPejabat;

        $pdf = Pdf::loadView('admin.kgb.sk-pdf', [
            'riwayat'          => $riwayat,
            'pegawai'          => $riwayat->pegawai,
            'instansi'         => $instansi,
            'pejabatTerdahulu' => $pejabatTerdahulu,
        ])->setPaper('f4', 'portrait');

        $filename = 'SK_KGB_' . $riwayat->pegawai->nip . '_' . Carbon::parse($riwayat->tmt_baru)->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    // -----------------------------------------------------------------------
    // PRIVATE HELPERS
    // -----------------------------------------------------------------------

    private function lookupGaji(?string $golongan, int $masaKerjaTahun): int
    {
        if (empty($golongan)) {
            return 0;
        }

        $row = MasterGaji::where('golongan', $golongan)
            ->where('masa_kerja', '<=', $masaKerjaTahun)
            ->orderBy('masa_kerja', 'desc')
            ->first();

        return $row ? (int) $row->nominal_gaji : 0;
    }

    private function validasiKgb(Pegawai $pegawai): array
    {
        $lolos  = true;
        $alasan = [];

        if ($pegawai->is_sedang_hukuman_disiplin) {
            $lolos    = false;
            $alasan[] = 'Sedang menjalani hukuman disiplin';
        }

        $tahunSekarang = now()->year;
        $skpList = $pegawai->skpEvaluasi()
            ->whereIn('tahun_penilaian', [$tahunSekarang - 1, $tahunSekarang - 2])
            ->get();

        $nilaiTidakLulus = ['Cukup', 'Kurang', 'Sangat Kurang'];
        foreach ($skpList as $skp) {
            if (in_array($skp->predikat, $nilaiTidakLulus)) {
                $lolos    = false;
                $alasan[] = "Nilai SKP tahun {$skp->tahun_penilaian} adalah '{$skp->predikat}' (tidak memenuhi syarat)";
                break;
            }
        }

        return ['lolos' => $lolos, 'alasan' => $alasan];
    }

    private function generateAndSavePdf(
        RiwayatKgb $riwayat,
        Pegawai $pegawai,
        ?PengaturanInstansi $instansi,
        ?MasterPejabat $pejabatTerdahulu
    ): string {
        $pdf = Pdf::loadView('admin.kgb.sk-pdf', [
            'riwayat'          => $riwayat,
            'pegawai'          => $pegawai,
            'instansi'         => $instansi,
            'pejabatTerdahulu' => $pejabatTerdahulu,
        ])->setPaper('f4', 'portrait');

        $dir      = 'sk_kgb';
        $filename = "SK_KGB_{$pegawai->nip}_" . Carbon::parse($riwayat->tmt_baru)->format('Ymd') . ".pdf";
        $fullPath = "{$dir}/{$filename}";

        Storage::disk('public')->put($fullPath, $pdf->output());

        return $fullPath;
    }
}
