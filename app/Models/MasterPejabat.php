<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterPejabat extends Model
{
    use HasFactory;
    
    protected $table = 'master_pejabat';
    
    protected $fillable = [
        'nama_jabatan',
        'nama_pejabat',
        'nip',
        'pangkat_golongan',
    ];

    public function riwayatKgb()
    {
        return $this->hasMany(RiwayatKgb::class, 'pejabat_id');
    }
}
