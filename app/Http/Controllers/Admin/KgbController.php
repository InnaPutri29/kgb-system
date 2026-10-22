<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterGaji;
use App\Models\MasterPejabat;
use App\Models\Pegawai;
use App\Models\PengaturanInstansi;
use App\Models\PkpEvaluasi;
use App\Models\RiwayatKgb;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Exports\KgbExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Services\KgbEligibilityService;

class KgbController extends Controller
{
    protected $rulesService;

    public function __construct(KgbEligibilityService $rulesService)
    {
        $this->rulesService = $rulesService;
    }

    /**
     * Tampilkan data untuk modal proses KGB (JSON/partial).
     */
    public function getDataForModal(Pegawai $pegawai)
    {
        return response()->json($this->dataModal($pegawai));
    }

    /**
     * Simpan PKP dua tahun terakhir yang belum diisi langsung dari pop-up Proses KGB,
     * lalu kembalikan data pop-up dengan hasil validasi terbaru.
     * Hanya mengisi tahun yang masih kosong; PKP yang sudah ada diubah lewat Detail Pegawai.
     */
    public function simpanPkpCepat(Request $request, Pegawai $pegawai)
    {
        $tahunWajib = [now()->year - 1, now()->year - 2];

        $request->validate([
            'pkp'            => ['required', 'array', 'min:1'],
            'pkp.*.tahun'    => ['required', 'integer', Rule::in($tahunWajib)],
            'pkp.*.predikat' => ['required', Rule::in(PkpEvaluasi::PREDIKAT)],
        ], [
            'pkp.required'            => 'Pilih predikat PKP terlebih dahulu.',
            'pkp.*.predikat.required' => 'Predikat PKP wajib dipilih.',
            'pkp.*.predikat.in'       => 'Predikat PKP tidak valid.',
            'pkp.*.tahun.in'          => 'Tahun PKP harus dua tahun terakhir.',
        ]);

        foreach ($request->pkp as $item) {
            PkpEvaluasi::firstOrCreate(
                ['pegawai_id' => $pegawai->id, 'tahun_penilaian' => $item['tahun']],
                ['predikat' => $item['predikat']]
            );
        }

        return response()->json($this->dataModal($pegawai->fresh()));
    }

    /**
     * Susun data untuk pop-up Proses KGB.
     */
    private function dataModal(Pegawai $pegawai): array
    {
        $pegawai->load(['riwayatKgb' => function ($query) {
            $query->latest('tmt_baru');
        }]);

        $pejabatList = MasterPejabat::orderBy('nama_jabatan')->get();
        $periode     = $this->rulesService->hitungPeriodeBaru($pegawai);

        $gajiPokokBaru = $this->rulesService->lookupGaji($pegawai->golongan, $periode['masa_kerja_tahun_baru']);
        $validasi      = $this->rulesService->validasiKgb($pegawai);

        // Status PKP dua tahun terakhir (predikat null = belum diisi)
        $pkpAda = $pegawai->pkpEvaluasi()
            ->whereIn('tahun_penilaian', [now()->year - 1, now()->year - 2])
            ->pluck('predikat', 'tahun_penilaian');
        $pkpDuaTahun = collect([now()->year - 1, now()->year - 2])->map(fn ($tahun) => [
            'tahun'    => $tahun,
            'predikat' => $pkpAda[$tahun] ?? null,
        ])->values();

        $pegawaiArray = $pegawai->toArray();
        $pegawaiArray['tanggal_sk_terakhir'] = $pegawai->tanggal_sk_terakhir ? $pegawai->tanggal_sk_terakhir->format('Y-m-d') : null;

        return [
            'pegawai'               => $pegawaiArray,
            'tmt_baru'              => $periode['tmt_baru']->format('Y-m-d'),
            'masa_kerja_tahun_baru' => $periode['masa_kerja_tahun_baru'],
            'masa_kerja_bulan_baru' => $periode['masa_kerja_bulan_baru'],
            'gaji_pokok_baru'       => $gajiPokokBaru,
            'pejabat_list'          => $pejabatList,
            'validasi'              => $validasi,
            'pkp_dua_tahun'         => $pkpDuaTahun,
            'predikat_list'         => PkpEvaluasi::PREDIKAT,
        ];
    }

    /**
     * Tampilkan daftar riwayat KGB.
     */
    public function index()
    {
        $riwayatKgb = RiwayatKgb::with('pegawai')
            ->latest()
            ->paginate(request('per_page', 20))->withQueryString();

        return view('admin.kgb.index', compact('riwayatKgb'));
    }

    /**
     * Tampilkan detail riwayat KGB.
     */
    public function show(RiwayatKgb $riwayat)
    {
        $riwayat->load('pegawai');
        return view('admin.kgb.show', compact('riwayat'));
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
            ->get();

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
            'nomor_sk_baru'       => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $lengkap = trim($value) . '/KPG.14/Kepegumas/RSDS';
                    if (\App\Models\RiwayatKgb::where('nomor_sk_baru', $lengkap)->exists()) {
                        $fail('Nomor SK Baru "' . $lengkap . '" sudah digunakan. Silakan gunakan nomor lain.');
                    }
                },
            ],
            'nomor_sk_terakhir'   => 'required|string|max:255',
            'tanggal_sk_terakhir' => 'required|date',
            'tanggal_ditetapkan'  => 'required|date',
            'master_pejabat_id'   => 'required|exists:master_pejabat,id',
        ], [
            'nomor_sk_baru.required'       => 'Nomor SK Baru wajib diisi.',
            'nomor_sk_terakhir.required'   => 'Nomor SK Sebelumnya wajib diisi.',
            'tanggal_sk_terakhir.required' => 'Tanggal SK Sebelumnya wajib diisi.',
            'tanggal_ditetapkan.required'  => 'Tanggal penetapan SK Baru wajib diisi.',
            'master_pejabat_id.required'   => 'Pejabat penetap SK sebelumnya wajib dipilih.',
        ]);

        // Validasi rule-based via Service
        $validasi = $this->rulesService->validasiKgb($pegawai);
        if (!$validasi['lolos']) {
            return back()->with('error', 'Pegawai tidak memenuhi syarat KGB: ' . implode(', ', $validasi['alasan']));
        }

        // Kalkulasi via Service
        $periode            = $this->rulesService->hitungPeriodeBaru($pegawai);
        $tmtBaru            = $periode['tmt_baru'];
        $tmtYad             = $periode['tmt_yad'];
        $masaKerjaTahunBaru = $periode['masa_kerja_tahun_baru'];
        $masaKerjaBulanBaru = $periode['masa_kerja_bulan_baru'];

        $gajiPokokBaru      = $this->rulesService->lookupGaji($pegawai->golongan, $masaKerjaTahunBaru);
        $gajiPokokLama      = (int) $pegawai->gaji_pokok_terakhir;

        // Snapshot pejabat penetap (Direktur saat ini)
        $instansi       = PengaturanInstansi::first();
        $pejabatPenetap = $instansi ? $instansi->nama_direktur : '-';

        // Pejabat terdahulu (untuk referensi di PDF)
        $pejabatTerdahulu = MasterPejabat::find($request->master_pejabat_id);

        $nomorSkBaruLengkap = $request->nomor_sk_baru . '/KPG.14/Kepegumas/RSDS';

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

        return redirect()->route('admin.kgb.index')
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

        $namaClean = trim(preg_replace('/_+/', '_', str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9\s]/', '', $riwayat->pegawai->nama_lengkap))), '_');
        $filename = 'SK_KGB_' . $namaClean . '_' . $riwayat->pegawai->nip . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Upload PDF SK KGB Final (Sudah di-TTE).
     */
    public function uploadFinal(Request $request, RiwayatKgb $riwayat)
    {
        $request->validate([
            'file_sk_final' => 'required|mimes:pdf|max:5120', // max 5MB
        ], [
            'file_sk_final.required' => 'File SK Final wajib diunggah.',
            'file_sk_final.mimes'    => 'File harus berupa PDF.',
            'file_sk_final.max'      => 'Ukuran file maksimal 5MB.',
        ]);

        if ($request->hasFile('file_sk_final')) {
            $file = $request->file('file_sk_final');
            $dir = 'sk_kgb_final';
            
            // Hapus file lama jika ada (fitur ganti/edit SK)
            if ($riwayat->file_sk_final && \Illuminate\Support\Facades\Storage::disk('public')->exists($riwayat->file_sk_final)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($riwayat->file_sk_final);
            }

            $riwayat->load('pegawai');
            $namaClean = trim(preg_replace('/_+/', '_', str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9\s]/', '', $riwayat->pegawai->nama_lengkap))), '_');
            $filename = "SK_KGB_FINAL_{$namaClean}_{$riwayat->pegawai->nip}_" . time() . ".pdf";
            
            // Simpan file
            $path = $file->storeAs($dir, $filename, 'public');

            $isUpdate = $riwayat->status === 'Final';

            // Update riwayat
            $riwayat->update([
                'status' => 'Final',
                'file_sk_final' => $path
            ]);

            // Kirim notifikasi in-app ke Pegawai PNS
            if ($riwayat->pegawai->user) {
                $riwayat->pegawai->user->notify(new \App\Notifications\KgbDiterbitkanNotification($riwayat));
            }

            $msg = $isUpdate ? 'File SK KGB Final berhasil diperbarui.' : 'SK KGB Final berhasil diunggah. Notifikasi telah dikirim ke Pegawai.';
            return redirect()->back()->with('success', $msg);
        }

        return redirect()->back()->with('error', 'Gagal mengunggah file.');
    }

    // Ekspor rekap KGB ke Excel
    public function export(Request $request)
    {
        $request->validate([
            'tahun_awal'  => 'nullable|integer|min:2000',
            'tahun_akhir' => 'nullable|integer|gte:tahun_awal',
        ]);

        $tahunAwal  = $request->tahun_awal ?? Carbon::now()->year;
        $tahunAkhir = $request->tahun_akhir ?? Carbon::now()->year;

        $filename = "Rekap_KGB_{$tahunAwal}_sampai_{$tahunAkhir}.xlsx";
        if ($tahunAwal == $tahunAkhir) {
            $filename = "Rekap_KGB_Tahun_{$tahunAwal}.xlsx";
        }

        return Excel::download(new KgbExport($tahunAwal, $tahunAkhir), $filename);
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
        $namaClean = trim(preg_replace('/_+/', '_', str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9\s]/', '', $pegawai->nama_lengkap))), '_');
        $filename = "SK_KGB_{$namaClean}_{$pegawai->nip}.pdf";
        $fullPath = "{$dir}/{$filename}";

        Storage::disk('public')->put($fullPath, $pdf->output());

        return $fullPath;
    }
}
