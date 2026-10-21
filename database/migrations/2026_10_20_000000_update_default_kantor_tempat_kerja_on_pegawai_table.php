<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ganti nilai bawaan kantor tempat kerja dari "RSUD SIDAWANGI" menjadi "RSD Sidawangi".
     */
    public function up(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->string('kantor_tempat_kerja')->nullable()->default('RSD Sidawangi')->change();
        });

        DB::table('pegawai')
            ->where('kantor_tempat_kerja', 'RSUD SIDAWANGI')
            ->update(['kantor_tempat_kerja' => 'RSD Sidawangi']);
    }

    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->string('kantor_tempat_kerja')->nullable()->default('RSUD SIDAWANGI')->change();
        });
    }
};
