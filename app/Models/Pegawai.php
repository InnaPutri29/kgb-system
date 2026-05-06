<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawai';

    protected $fillable = [
        'user_id', 'nip', 'nama', 'tempat_lahir', 'tanggal_lahir',
        'pangkat_golongan', 'jabatan', 'unit_kerja', 'pendidikan_terakhir',
        'tmt_cpns', 'tmt_pns', 'tmt_pangkat_terakhir', 'tmt_gaji_terakhir',
        'masa_kerja_tahun', 'masa_kerja_bulan', 'gaji_pokok_terakhir',
        'sedang_hukuman_disiplin'
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'tmt_cpns' => 'date',
            'tmt_pns' => 'date',
            'tmt_pangkat_terakhir' => 'date',
            'tmt_gaji_terakhir' => 'date',
            'sedang_hukuman_disiplin' => 'boolean'
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skpEvaluasi()
    {
        return $this->hasMany(SkpEvaluasi::class, 'pegawai_id');
    }

    public function riwayatKgb()
    {
        return $this->hasMany(RiwayatKgb::class, 'pegawai_id');
    }
}
