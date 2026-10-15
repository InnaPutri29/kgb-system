<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PkpEvaluasi extends Model
{
    use HasFactory, \Illuminate\Database\Eloquent\SoftDeletes;
    
    protected $table = 'pkp_evaluasi';

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
