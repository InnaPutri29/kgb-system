<?php

namespace App\Imports;

use App\Models\Pegawai;
use App\Models\User;
use App\Models\PkpEvaluasi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Spatie\Permission\Models\Role;

class PegawaiImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    private $successCount = 0;
    private $skippedCount = 0;
    private $invalidCount = 0;

    /**
    * Proses setiap baris dari file Excel menjadi data Pegawai + User.
    *
    * Kolom yang diharapkan dari file Excel (case-insensitive):
    * nip, nama, email, pangkat, golongan,
    * jabatan, kantor_tempat_kerja, tmt_gaji_terakhir,
    * masa_kerja_tahun, masa_kerja_bulan, gaji_pokok_terakhir,
    * nomor_sk_terakhir, tanggal_sk_terakhir,
    * pkp_tahun_1, pkp_predikat_1, pkp_tahun_2, pkp_predikat_2
    *
    * @param array $row
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $nip = isset($row['nip']) ? trim((string) $row['nip']) : '';
        $nama = isset($row['nama_lengkap']) ? trim((string) $row['nama_lengkap']) : (isset($row['nama']) ? trim((string) $row['nama']) : '');
        // Nama yang ditulis kapital semua dirapikan agar layak dicantumkan di SK
        $nama = Pegawai::rapikanNama($nama);
        $email = isset($row['email']) ? trim((string) $row['email']) : '';

        if (empty($nip) || empty($nama)) {
            $this->invalidCount++;
            return null;
        }

        if (empty($email)) {
            // Format sama dengan tambah pegawai manual (PegawaiController)
            $email = strtolower(str_replace(' ', '', $nip)) . '@kgb.rsd-sidawangi.id';
        }

        // Cek apakah data Pegawai sudah ada
        $existingPegawai = Pegawai::where('nip', $nip)->first();
        if ($existingPegawai) {
            $this->skippedCount++;
            return null;
        }

        // Cek apakah User dengan NIP ini sudah ada
        $user = User::where('nip', $nip)->first();
        if (!$user) {
            $user = User::create([
                'name' => $nama,
                'nip' => $nip,
                'email' => $email,
                'password' => Hash::make(substr($nip, 0, 8)), // Password default = 8 digit awal NIP (Tanggal Lahir)
                'is_first_login' => true,
            ]);
        }

        // Assign role 'pegawai'
        if (!$user->hasRole('pegawai')) {
            $pegawaiRole = Role::where('name', 'pegawai')->first();
            if ($pegawaiRole) {
                $user->assignRole($pegawaiRole);
            }
        }

        // Golongan dirapikan sesuai daftar resmi, dan pangkat mengikuti golongan
        $golongan = Pegawai::normalisasiGolongan($row['golongan'] ?? null) ?? ($row['golongan'] ?? null);
        $pangkat = Pegawai::GOLONGAN_PANGKAT[$golongan] ?? ($row['pangkat'] ?? null);

        // Create pegawai record
        $pegawai = Pegawai::create([
            'user_id' => $user->id,
            'nip' => $nip,
            'nama_lengkap' => $nama,
            'pangkat' => $pangkat,
            'golongan' => $golongan,
            'jabatan' => Pegawai::rapikanJabatan($row['jabatan'] ?? null),
            'kantor_tempat_kerja' => ($row['kantor_tempat_kerja'] ?? null) ?: 'Rumah Sakit Daerah Sidawangi',
            'tmt_gaji_terakhir' => $this->parseDate($row['tmt_gaji_terakhir'] ?? null),
            'masa_kerja_tahun' => (int) ($row['masa_kerja_tahun'] ?? 0),
            'masa_kerja_bulan' => (int) ($row['masa_kerja_bulan'] ?? 0),
            'gaji_pokok_terakhir' => (float) ($row['gaji_pokok_terakhir'] ?? 0),
            'nomor_sk_terakhir' => $row['nomor_sk_terakhir'] ?? null,
            'tanggal_sk_terakhir' => $this->parseDate($row['tanggal_sk_terakhir'] ?? null),
            'is_sedang_hukuman_disiplin' => false,
        ]);

        // Import data PKP jika ada
        foreach ([1, 2] as $i) {
            $tahun    = $row["pkp_tahun_{$i}"] ?? null;
            $predikat = $this->normalisasiPredikat($row["pkp_predikat_{$i}"] ?? null);

            if (!empty($tahun) && $predikat) {
                PkpEvaluasi::create([
                    'pegawai_id'      => $pegawai->id,
                    'tahun_penilaian' => $tahun,
                    'predikat'        => $predikat,
                ]);
            }
        }

        $this->successCount++;
        return null; // Kita sudah handle insert manual di atas
    }

    /**
     * Ambil statistik hasil impor.
     */
    public function getStats()
    {
        return [
            'success' => $this->successCount,
            'skipped' => $this->skippedCount,
            'invalid' => $this->invalidCount,
        ];
    }

    /**
     * Samakan penulisan predikat dengan daftar resmi, tanpa memedulikan huruf besar/kecil.
     * Contoh: "sangat baik" -> "Sangat Baik". Predikat yang tidak dikenal diabaikan (null).
     */
    private function normalisasiPredikat($value): ?string
    {
        if (empty($value)) return null;

        $input = strtolower(trim((string) $value));
        foreach (PkpEvaluasi::PREDIKAT as $predikat) {
            if (strtolower($predikat) === $input) {
                return $predikat;
            }
        }

        return null;
    }

    /**
     * Parse tanggal dari berbagai format umum.
     */
    private function parseDate($value): ?string
    {
        if (empty($value)) return null;

        // Jika berupa angka serial Excel
        if (is_numeric($value)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
                    ->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
