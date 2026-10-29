<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MasterPejabat;

class Pegawai extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pegawai';

    /**
     * Golongan ruang PNS beserta pangkatnya (PP Nomor 99 Tahun 2000).
     */
    public const GOLONGAN_PANGKAT = [
        'I/a'   => 'Juru Muda',
        'I/b'   => 'Juru Muda Tingkat I',
        'I/c'   => 'Juru',
        'I/d'   => 'Juru Tingkat I',
        'II/a'  => 'Pengatur Muda',
        'II/b'  => 'Pengatur Muda Tingkat I',
        'II/c'  => 'Pengatur',
        'II/d'  => 'Pengatur Tingkat I',
        'III/a' => 'Penata Muda',
        'III/b' => 'Penata Muda Tingkat I',
        'III/c' => 'Penata',
        'III/d' => 'Penata Tingkat I',
        'IV/a'  => 'Pembina',
        'IV/b'  => 'Pembina Tingkat I',
        'IV/c'  => 'Pembina Utama Muda',
        'IV/d'  => 'Pembina Utama Madya',
        'IV/e'  => 'Pembina Utama',
    ];

    /**
     * Rapikan nama yang ditulis kapital semua agar layak dicantumkan di SK.
     * Contoh: "dr. BUDI SANTOSO,S.Kep.Ners" menjadi "dr. Budi Santoso, S.Kep.Ners".
     * Nama yang sudah memakai huruf kecil tidak diubah. Gelar di belakang koma dibiarkan apa adanya.
     */
    public static function rapikanNama(?string $nama): ?string
    {
        if ($nama === null || trim($nama) === '') {
            return $nama;
        }

        $nama = trim(preg_replace('/\s+/', ' ', $nama));
        [$inti, $gelar] = array_pad(explode(',', $nama, 2), 2, '');

        $gelarDepan = ['dr.' => 'dr.', 'drg.' => 'drg.', 'dr' => 'dr.', 'drg' => 'drg.', 'hj.' => 'Hj.', 'h.' => 'H.', 'prof.' => 'Prof.', 'ir.' => 'Ir.', 'ns.' => 'Ns.'];
        $kata = explode(' ', trim($inti));
        $bukanGelar = array_filter($kata, fn ($k) => !isset($gelarDepan[mb_strtolower($k)]));
        $semuaKapital = $bukanGelar && implode('', $bukanGelar) === mb_strtoupper(implode('', $bukanGelar));

        if ($semuaKapital) {
            $kata = array_map(fn ($k) => $gelarDepan[mb_strtolower($k)] ?? mb_convert_case(mb_strtolower($k), MB_CASE_TITLE), $kata);
        }

        $hasil = implode(' ', $kata);
        $gelar = trim($gelar);

        return $gelar !== '' ? "{$hasil}, {$gelar}" : $hasil;
    }

    /**
     * Rapikan jabatan yang ditulis kapital semua, misalnya "PERAWAT AHLI MUDA" menjadi "Perawat Ahli Muda".
     * Kata sambung ditulis kecil dan singkatan unit tetap kapital.
     */
    public static function rapikanJabatan(?string $jabatan): ?string
    {
        if ($jabatan === null || trim($jabatan) === '') {
            return $jabatan;
        }

        $jabatan = trim(preg_replace('/\s+/', ' ', $jabatan));
        if ($jabatan !== mb_strtoupper($jabatan)) {
            return $jabatan;
        }

        $kataSambung = ['dan', 'di', 'pada', 'untuk', 'dengan', 'atau', 'yang', 'ke', 'dari', 'serta'];
        $singkatan = ['IGD', 'ICU', 'ICCU', 'NICU', 'PICU', 'HCU', 'UGD', 'RS', 'RSD', 'RSUD', 'IT', 'SDM', 'K3', 'K3RS', 'PPI', 'IPSRS', 'CSSD', 'VK', 'OK', 'TU', 'UPT', 'BLUD', 'TTE'];

        $kata = explode(' ', $jabatan);
        foreach ($kata as $i => $k) {
            $bersih = trim($k, ',.()');
            if (in_array($bersih, $singkatan, true)) {
                continue;
            }
            $kecil = mb_strtolower($k);
            $kata[$i] = ($i > 0 && in_array($kecil, $kataSambung, true)) ? $kecil : mb_convert_case($kecil, MB_CASE_TITLE);
        }

        return implode(' ', $kata);
    }

    /**
     * Samakan penulisan golongan dengan daftar resmi, misalnya "iii/a" atau "III / A" menjadi "III/a".
     */
    public static function normalisasiGolongan(?string $golongan): ?string
    {
        $bersih = strtolower(str_replace(' ', '', (string) $golongan));
        foreach (array_keys(self::GOLONGAN_PANGKAT) as $resmi) {
            if (strtolower($resmi) === $bersih) {
                return $resmi;
            }
        }

        return null;
    }

    protected $fillable = [
        'user_id', 'nip', 'nama_lengkap',
        'pangkat', 'golongan', 'jabatan', 'kantor_tempat_kerja',
        'tmt_gaji_terakhir',
        'masa_kerja_tahun', 'masa_kerja_bulan', 'gaji_pokok_terakhir',
        'master_pejabat_id', 'nomor_sk_terakhir', 'tanggal_sk_terakhir', 'is_sedang_hukuman_disiplin'
    ];

    protected $casts = [
        'tmt_gaji_terakhir' => 'date',
        'tanggal_sk_terakhir' => 'date',
        'is_sedang_hukuman_disiplin' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function masterPejabat()
    {
        return $this->belongsTo(MasterPejabat::class, 'master_pejabat_id');
    }

    public function pkpEvaluasi()
    {
        return $this->hasMany(PkpEvaluasi::class, 'pegawai_id');
    }

    public function riwayatKgb()
    {
        return $this->hasMany(RiwayatKgb::class, 'pegawai_id');
    }
}
