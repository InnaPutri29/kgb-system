<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            if (Schema::hasColumn('pegawai', 'nama')) {
                $table->renameColumn('nama', 'nama_lengkap');
            }

            if (Schema::hasColumn('pegawai', 'sedang_hukuman_disiplin')) {
                $table->renameColumn('sedang_hukuman_disiplin', 'is_sedang_hukuman_disiplin');
            }

            if (! Schema::hasColumn('pegawai', 'master_pejabat_id')) {
                $table->foreignId('master_pejabat_id')->nullable()->constrained('master_pejabat')->nullOnDelete();
            }

            if (! Schema::hasColumn('pegawai', 'nomor_sk_terakhir')) {
                $table->string('nomor_sk_terakhir')->nullable();
            }

            if (! Schema::hasColumn('pegawai', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        if (DB::getDriverName() !== 'sqlite' && Schema::hasColumn('pegawai', 'unit_kerja')) {
            DB::statement("ALTER TABLE pegawai MODIFY unit_kerja VARCHAR(255) NULL DEFAULT 'RSUD SIDAWANGI'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            if (Schema::hasColumn('pegawai', 'master_pejabat_id')) {
                $table->dropForeign(['master_pejabat_id']);
                $table->dropColumn('master_pejabat_id');
            }

            if (Schema::hasColumn('pegawai', 'nomor_sk_terakhir')) {
                $table->dropColumn('nomor_sk_terakhir');
            }

            if (Schema::hasColumn('pegawai', 'is_sedang_hukuman_disiplin')) {
                $table->renameColumn('is_sedang_hukuman_disiplin', 'sedang_hukuman_disiplin');
            }

            if (Schema::hasColumn('pegawai', 'nama_lengkap')) {
                $table->renameColumn('nama_lengkap', 'nama');
            }

            $table->dropSoftDeletes();
        });

        if (DB::getDriverName() !== 'sqlite' && Schema::hasColumn('pegawai', 'unit_kerja')) {
            DB::statement('ALTER TABLE pegawai MODIFY unit_kerja VARCHAR(255) NULL');
        }
    }
};
