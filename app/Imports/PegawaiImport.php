<?php

namespace App\Imports;

use App\Models\Pegawai;
use App\Models\User;
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
    * masa_kerja_tahun, masa_kerja_bulan, gaji_pokok_terakhir
    *
    * @param array $row
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $nip = isset($row['nip']) ? trim((string) $row['nip']) : '';
        $nama = isset($row['nama_lengkap']) ? trim((string) $row['nama_lengkap']) : (isset($row['nama']) ? trim((string) $row['nama']) : '');
        $email = isset($row['email']) ? trim((string) $row['email']) : '';

        if (empty($nip) || empty($nama)) {
            $this->invalidCount++;
            return null;
        }

        if (empty($email)) {
            $email = Str::slug($nip) . '@kgb.internal';
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

        // Create pegawai record
        Pegawai::create([
            'user_id' => $user->id,
            'nip' => $nip,
            'nama_lengkap' => $nama,
            'pangkat' => $row['pangkat'] ?? null,
            'golongan' => $row['golongan'] ?? null,
            'jabatan' => $row['jabatan'] ?? null,
            'kantor_tempat_kerja' => $row['kantor_tempat_kerja'] ?? null,
            'tmt_gaji_terakhir' => $this->parseDate($row['tmt_gaji_terakhir'] ?? null),
            'masa_kerja_tahun' => (int) ($row['masa_kerja_tahun'] ?? 0),
            'masa_kerja_bulan' => (int) ($row['masa_kerja_bulan'] ?? 0),
            'gaji_pokok_terakhir' => (float) ($row['gaji_pokok_terakhir'] ?? 0),
            'is_sedang_hukuman_disiplin' => false,
        ]);

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
