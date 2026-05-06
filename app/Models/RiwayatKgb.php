<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatKgb extends Model
{
    use HasFactory;
    
    protected $table = 'riwayat_kgb';

    protected $fillable = [
        'pegawai_id',
        'nomor_sk_lama',
        'tanggal_sk_lama',
        'nomor_sk_baru',
        'tanggal_sk_baru',
        'tmt_kgb_baru',
        'masa_kerja_tahun_baru',
        'masa_kerja_bulan_baru',
        'gaji_pokok_baru',
        'pejabat_id',
        'file_sk'
    ];

    protected function casts(): array
    {
        return [
            'tanggal_sk_lama' => 'date',
            'tanggal_sk_baru' => 'date',
            'tmt_kgb_baru' => 'date',
        ];
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function pejabat()
    {
        return $this->belongsTo(MasterPejabat::class, 'pejabat_id');
    }
}
