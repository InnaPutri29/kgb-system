<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Perbaiki schema tabel riwayat_kgb agar sesuai spesifikasi.
     *
     * Kolom aktual DB:
     *   id, pegawai_id, nomor_sk_lama, tanggal_sk_lama, nomor_sk_baru,
     *   tanggal_sk_baru, tmt_kgb_baru, masa_kerja_tahun_baru,
     *   masa_kerja_bulan_baru, gaji_pokok_baru, pejabat_id, file_sk,
     *   created_at, updated_at, deleted_at
     *
     * Kolom yang diinginkan:
     *   id, pegawai_id, nomor_sk_baru, tanggal_ditetapkan, tmt_baru,
     *   gaji_pokok_lama, gaji_pokok_baru, masa_kerja_tahun_baru,
     *   masa_kerja_bulan_baru, tmt_yad, pejabat_penetap, file_pdf_path,
     *   timestamps, softDeletes
     */
    public function up(): void
    {
        // Step 1: Drop kolom yang tidak diperlukan
        Schema::table('riwayat_kgb', function (Blueprint $table) {
            if (Schema::hasColumn('riwayat_kgb', 'nomor_sk_lama')) {
                $table->dropColumn('nomor_sk_lama');
            }
            if (Schema::hasColumn('riwayat_kgb', 'tanggal_sk_lama')) {
                $table->dropColumn('tanggal_sk_lama');
            }
        });

        // Step 2: Rename kolom yang ada
        Schema::table('riwayat_kgb', function (Blueprint $table) {
            if (Schema::hasColumn('riwayat_kgb', 'tanggal_sk_baru')) {
                $table->renameColumn('tanggal_sk_baru', 'tanggal_ditetapkan');
            }
            if (Schema::hasColumn('riwayat_kgb', 'tmt_kgb_baru')) {
                $table->renameColumn('tmt_kgb_baru', 'tmt_baru');
            }
            if (Schema::hasColumn('riwayat_kgb', 'file_sk')) {
                $table->renameColumn('file_sk', 'file_pdf_path');
            }
        });

        // Step 3: Tambah kolom baru & drop/replace pejabat_id → pejabat_penetap
        Schema::table('riwayat_kgb', function (Blueprint $table) {
            if (!Schema::hasColumn('riwayat_kgb', 'gaji_pokok_lama')) {
                $table->bigInteger('gaji_pokok_lama')->default(0)->after('tmt_baru');
            }
            if (!Schema::hasColumn('riwayat_kgb', 'tmt_yad')) {
                $table->date('tmt_yad')->nullable()->after('masa_kerja_bulan_baru');
            }
            if (!Schema::hasColumn('riwayat_kgb', 'pejabat_penetap')) {
                $table->string('pejabat_penetap')->nullable()->after('tmt_yad');
            }
        });

        // Step 4: Drop kolom pejabat_id (foreign key mungkin perlu di-drop dulu)
        Schema::table('riwayat_kgb', function (Blueprint $table) {
            if (Schema::hasColumn('riwayat_kgb', 'pejabat_id')) {
                // Coba drop foreign key jika ada
                try {
                    $table->dropForeign(['pejabat_id']);
                } catch (\Exception $e) {
                    // FK mungkin tidak ada, lanjutkan
                }
                $table->dropColumn('pejabat_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riwayat_kgb', function (Blueprint $table) {
            // Kembalikan pejabat_id
            if (!Schema::hasColumn('riwayat_kgb', 'pejabat_id')) {
                $table->unsignedBigInteger('pejabat_id')->nullable();
            }
        });

        Schema::table('riwayat_kgb', function (Blueprint $table) {
            // Drop kolom baru
            if (Schema::hasColumn('riwayat_kgb', 'pejabat_penetap')) {
                $table->dropColumn('pejabat_penetap');
            }
            if (Schema::hasColumn('riwayat_kgb', 'tmt_yad')) {
                $table->dropColumn('tmt_yad');
            }
            if (Schema::hasColumn('riwayat_kgb', 'gaji_pokok_lama')) {
                $table->dropColumn('gaji_pokok_lama');
            }
        });

        Schema::table('riwayat_kgb', function (Blueprint $table) {
            // Kembalikan nama kolom
            if (Schema::hasColumn('riwayat_kgb', 'tanggal_ditetapkan')) {
                $table->renameColumn('tanggal_ditetapkan', 'tanggal_sk_baru');
            }
            if (Schema::hasColumn('riwayat_kgb', 'tmt_baru')) {
                $table->renameColumn('tmt_baru', 'tmt_kgb_baru');
            }
            if (Schema::hasColumn('riwayat_kgb', 'file_pdf_path')) {
                $table->renameColumn('file_pdf_path', 'file_sk');
            }
        });

        Schema::table('riwayat_kgb', function (Blueprint $table) {
            // Kembalikan kolom yang dihapus
            if (!Schema::hasColumn('riwayat_kgb', 'nomor_sk_lama')) {
                $table->string('nomor_sk_lama')->nullable();
            }
            if (!Schema::hasColumn('riwayat_kgb', 'tanggal_sk_lama')) {
                $table->date('tanggal_sk_lama')->nullable();
            }
        });
    }
};
