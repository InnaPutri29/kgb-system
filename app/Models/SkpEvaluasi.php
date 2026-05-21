<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkpEvaluasi extends Model
{
    use HasFactory;
    
    protected $table = 'skp_evaluasi';

    protected $fillable = [
        'pegawai_id',
        'tahun',
        'nilai_kinerja'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}
