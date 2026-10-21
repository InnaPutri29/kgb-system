<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class MasterPejabat extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'master_pejabat';
    
    protected $fillable = [
        'nama_jabatan',
        'nama_pejabat',
    ];

    /**
     * Pegawai yang SK terakhirnya ditetapkan oleh pejabat ini.
     */
    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'master_pejabat_id');
    }
}
