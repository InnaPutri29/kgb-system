<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ganti nilai bawaan nama instansi dari "UOBK Rumah Sakit Paru Provinsi Jawa Barat"
     * menjadi "Rumah Sakit Daerah Sidawangi".
     */
    public function up(): void
    {
        Schema::table('pengaturan_instansi', function (Blueprint $table) {
            $table->string('nama_instansi')->default('Rumah Sakit Daerah Sidawangi')->change();
        });

        DB::table('pengaturan_instansi')
            ->where('nama_instansi', 'UOBK Rumah Sakit Paru Provinsi Jawa Barat')
            ->update(['nama_instansi' => 'Rumah Sakit Daerah Sidawangi']);
    }

    public function down(): void
    {
        Schema::table('pengaturan_instansi', function (Blueprint $table) {
            $table->string('nama_instansi')->default('UOBK Rumah Sakit Paru Provinsi Jawa Barat')->change();
        });
    }
};
