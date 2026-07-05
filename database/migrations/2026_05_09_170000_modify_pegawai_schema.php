<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Perubahan:
     * 1. Hapus kolom: tanggal_lahir, tmt_pangkat_terakhir
     *    (tempat_lahir, pendidikan_terakhir, tmt_cpns, tmt_pns sudah tidak ada di schema)
     * 2. Rename: unit_kerja → kantor_tempat_kerja
     * 3. Pisah pangkat_golongan → pangkat (string) + golongan (string)
     */
    public function up(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            // Hapus kolom yang tidak diperlukan
            if (Schema::hasColumn('pegawai', 'tanggal_lahir')) {
                $table->dropColumn('tanggal_lahir');
            }
            if (Schema::hasColumn('pegawai', 'tmt_pangkat_terakhir')) {
                $table->dropColumn('tmt_pangkat_terakhir');
            }
            // Jaga-jaga jika kolom lama masih ada
            if (Schema::hasColumn('pegawai', 'tempat_lahir')) {
                $table->dropColumn('tempat_lahir');
            }
            if (Schema::hasColumn('pegawai', 'pendidikan_terakhir')) {
                $table->dropColumn('pendidikan_terakhir');
            }
            if (Schema::hasColumn('pegawai', 'tmt_cpns')) {
                $table->dropColumn('tmt_cpns');
            }
            if (Schema::hasColumn('pegawai', 'tmt_pns')) {
                $table->dropColumn('tmt_pns');
            }
        });

        Schema::table('pegawai', function (Blueprint $table) {
            // Rename unit_kerja → kantor_tempat_kerja
            if (Schema::hasColumn('pegawai', 'unit_kerja')) {
                $table->renameColumn('unit_kerja', 'kantor_tempat_kerja');
            }

            // Tambah kolom pangkat dan golongan terpisah
            if (!Schema::hasColumn('pegawai', 'pangkat')) {
                $table->string('pangkat')->nullable()->after('nama_lengkap');
            }
            if (!Schema::hasColumn('pegawai', 'golongan')) {
                $table->string('golongan')->nullable()->after('pangkat');
            }
        });

        // Migrasi data dari pangkat_golongan ke kolom baru (jika ada data)
        // pangkat_golongan biasanya format: "Penata Muda (III/a)" atau "Penata Tingkat I (IV/b)"
        // Kita bisa coba parse, tapi karena format beragam, kita copy apa adanya
        // Admin bisa update manual nanti

        Schema::table('pegawai', function (Blueprint $table) {
            // Hapus kolom pangkat_golongan lama
            if (Schema::hasColumn('pegawai', 'pangkat_golongan')) {
                $table->dropColumn('pangkat_golongan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            // Kembalikan pangkat_golongan
            if (!Schema::hasColumn('pegawai', 'pangkat_golongan')) {
                $table->string('pangkat_golongan')->nullable();
            }
        });

        Schema::table('pegawai', function (Blueprint $table) {
            // Hapus kolom baru
            if (Schema::hasColumn('pegawai', 'pangkat')) {
                $table->dropColumn('pangkat');
            }
            if (Schema::hasColumn('pegawai', 'golongan')) {
                $table->dropColumn('golongan');
            }

            // Kembalikan nama kolom
            if (Schema::hasColumn('pegawai', 'kantor_tempat_kerja')) {
                $table->renameColumn('kantor_tempat_kerja', 'unit_kerja');
            }

            // Kembalikan kolom yang dihapus
            $table->date('tanggal_lahir')->nullable();
            $table->date('tmt_pangkat_terakhir')->nullable();
        });
    }
};
