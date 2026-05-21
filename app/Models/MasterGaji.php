<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterGaji extends Model
{
    use HasFactory;
    
    protected $table = 'master_gaji';
    
    protected $fillable = [
        'golongan',
        'masa_kerja',
        'gaji_pokok',
        'peraturan_referensi',
    ];
}
