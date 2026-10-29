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
