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

    /**
    * Proses setiap baris dari file Excel menjadi data Pegawai + User.
    *
    * Kolom yang diharapkan dari file Excel (case-insensitive):
    * nip, nama, email, tanggal_lahir, pangkat_golongan,
    * jabatan, unit_kerja, tmt_pangkat_terakhir, tmt_gaji_terakhir,
    * masa_kerja_tahun, masa_kerja_bulan, gaji_pokok_terakhir
    *
    * @param array $row
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $nip = (string) ($row['nip'] ?? '');
        $nama = $row['nama_lengkap'] ?? $row['nama'] ?? '';
        $email = $row['email'] ?? (Str::slug($nip) . '@kgb.internal');

        if (empty($nip) || empty($nama)) {
            return null;
        }

        // Cek apakah User dengan NIP ini sudah ada
        $user = User::firstOrCreate(
            ['nip' => $nip],
            [
                'name' => $nama,
                'email' => $email,
                'password' => Hash::make($nip), // Password default = NIP
                'is_first_login' => true,
            ]
        );

        // Assign role 'pegawai'
        if (!$user->hasRole('pegawai')) {
            $pegawaiRole = Role::where('name', 'pegawai')->first();
            if ($pegawaiRole) {
                $user->assignRole($pegawaiRole);
            }
        }

        // Cek apakah data Pegawai sudah ada
        $pegawai = Pegawai::firstOrCreate(
            ['nip' => $nip],
            [
                'user_id' => $user->id,
                'nama_lengkap' => $nama,
                'tanggal_lahir' => $this->parseDate($row['tanggal_lahir'] ?? null),
                'pangkat_golongan' => $row['pangkat_golongan'] ?? null,
                'jabatan' => $row['jabatan'] ?? null,
                'unit_kerja' => $row['unit_kerja'] ?? null,
                'tmt_pangkat_terakhir' => $this->parseDate($row['tmt_pangkat_terakhir'] ?? null),
                'tmt_gaji_terakhir' => $this->parseDate($row['tmt_gaji_terakhir'] ?? null),
                'masa_kerja_tahun' => (int) ($row['masa_kerja_tahun'] ?? 0),
                'masa_kerja_bulan' => (int) ($row['masa_kerja_bulan'] ?? 0),
                'gaji_pokok_terakhir' => (float) ($row['gaji_pokok_terakhir'] ?? 0),
                'is_sedang_hukuman_disiplin' => false,
            ]
        );

        return null; // Kita sudah handle insert manual di atas
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
