<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'pegawai']);

        // Buat akun Admin default
        $admin = User::firstOrCreate(
            ['email' => 'admin@kgb.rsd-sidawangi.id'],
            [
                'name' => 'Administrator',
                'nip' => '000000000000000000',
                'password' => Hash::make('Admin@1234'),
                'is_first_login' => false,
            ]
        );
        $admin->assignRole($adminRole);

        $this->call([
            MasterGajiSeeder::class,
        ]);

        // Buat Dummy Pegawai yang masuk nominatif KGB (Jatuh tempo hari ini / terlewat)
        $dummyUser = User::firstOrCreate(
            ['email' => 'pegawai@kgb.rsd-sidawangi.id'],
            [
                'name' => 'Dr. Pegawai Testing',
                'nip' => '198001012010011001',
                'password' => Hash::make('19800101'),
                'is_first_login' => true,
            ]
        );
        $dummyUser->assignRole('pegawai');

        $pegawai = \App\Models\Pegawai::firstOrCreate(
            ['nip' => '198001012010011001'],
            [
                'user_id' => $dummyUser->id,
                'nama_lengkap' => 'Dr. Pegawai Testing',
                'pangkat_golongan' => 'III/a',
                'jabatan' => 'Dokter Muda',
                'unit_kerja' => 'RSD Sidawangi',
                'tmt_gaji_terakhir' => now()->subYears(2)->subDays(5),
                'masa_kerja_tahun' => 2,
                'masa_kerja_bulan' => 0,
                'gaji_pokok_terakhir' => 2700000,
                'is_sedang_hukuman_disiplin' => false
            ]
        );

        // Buat Dummy SKP 2 tahun terakhir
        \App\Models\SkpEvaluasi::firstOrCreate(
            ['pegawai_id' => $pegawai->id, 'tahun_penilaian' => now()->year - 1],
            ['predikat' => 'Baik']
        );
        \App\Models\SkpEvaluasi::firstOrCreate(
            ['pegawai_id' => $pegawai->id, 'tahun_penilaian' => now()->year - 2],
            ['predikat' => 'Sangat Baik']
        );

        // Buat Dummy Master Pejabat
        \App\Models\MasterPejabat::firstOrCreate([
            'nama_jabatan' => 'Gubernur Jawa Barat',
            'nama_pejabat' => 'H. Mochamad Ridwan Kamil, S.T., M.U.D.'
        ]);
    }
}
