<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PkpEvaluasi extends Model
{
    use HasFactory, \Illuminate\Database\Eloquent\SoftDeletes;
    
    protected $table = 'pkp_evaluasi';

    /**
     * Daftar predikat kinerja sesuai PermenPAN-RB No. 6 Tahun 2022
     * (sama dengan enum kolom `predikat` di database).
     */
    public const PREDIKAT = ['Sangat Baik', 'Baik', 'Butuh Perbaikan', 'Kurang/Misconduct', 'Sangat Kurang'];

    protected $fillable = [
        'pegawai_id',
        'tahun_penilaian',
        'predikat',
        'file_bukti_pkp'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}
