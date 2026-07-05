<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanInstansi extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_instansi';

    protected $fillable = [
        'nama_instansi',
        'logo',
        'alamat',
        'nama_direktur',
        'nip_direktur',
        'pangkat_direktur',
    ];
}
