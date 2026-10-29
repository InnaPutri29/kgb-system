<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kantor tempat kerja memakai nama lengkap "Rumah Sakit Daerah Sidawangi".
     */
    public function up(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->string('kantor_tempat_kerja')->nullable()->default('Rumah Sakit Daerah Sidawangi')->change();
        });

        DB::table('pegawai')
            ->whereIn('kantor_tempat_kerja', ['RSD Sidawangi', 'RSUD SIDAWANGI'])
            ->update(['kantor_tempat_kerja' => 'Rumah Sakit Daerah Sidawangi']);
    }

    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->string('kantor_tempat_kerja')->nullable()->default('RSD Sidawangi')->change();
        });

        DB::table('pegawai')
            ->where('kantor_tempat_kerja', 'Rumah Sakit Daerah Sidawangi')
            ->update(['kantor_tempat_kerja' => 'RSD Sidawangi']);
    }
};
