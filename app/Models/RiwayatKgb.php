<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RiwayatKgb extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'riwayat_kgb';

    protected $fillable = [
        'pegawai_id',
        'nomor_sk_baru',
        'tanggal_ditetapkan',
        'tmt_baru',
        'gaji_pokok_lama',
        'gaji_pokok_baru',
        'masa_kerja_tahun_baru',
        'masa_kerja_bulan_baru',
        'tmt_yad',
        'pejabat_penetap',
        'file_pdf_path',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_ditetapkan' => 'date',
            'tmt_baru'           => 'date',
            'tmt_yad'            => 'date',
        ];
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}

