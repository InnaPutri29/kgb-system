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

    public function riwayatKgb()
    {
        return $this->hasMany(RiwayatKgb::class, 'pejabat_id');
    }
}
