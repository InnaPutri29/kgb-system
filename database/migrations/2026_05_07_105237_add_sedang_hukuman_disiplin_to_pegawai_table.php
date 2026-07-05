<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('pegawai', 'sedang_hukuman_disiplin') && !Schema::hasColumn('pegawai', 'is_sedang_hukuman_disiplin')) {
            Schema::table('pegawai', function (Blueprint $table) {
                $table->boolean('sedang_hukuman_disiplin')
                    ->default(false)
                    ->after('tmt_gaji_terakhir');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            //
        });
    }
};
