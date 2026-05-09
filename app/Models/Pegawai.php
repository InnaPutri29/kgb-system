<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MasterPejabat;

class Pegawai extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pegawai';

    protected $fillable = [
        'user_id', 'nip', 'nama_lengkap', 'tanggal_lahir',
        'pangkat_golongan', 'jabatan', 'unit_kerja',
        'tmt_pangkat_terakhir', 'tmt_gaji_terakhir',
        'masa_kerja_tahun', 'masa_kerja_bulan', 'gaji_pokok_terakhir',
        'master_pejabat_id', 'nomor_sk_terakhir', 'is_sedang_hukuman_disiplin'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tmt_pangkat_terakhir' => 'date',
        'tmt_gaji_terakhir' => 'date',
        'is_sedang_hukuman_disiplin' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function masterPejabat()
    {
        return $this->belongsTo(MasterPejabat::class, 'master_pejabat_id');
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
