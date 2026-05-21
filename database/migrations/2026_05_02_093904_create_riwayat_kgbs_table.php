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
        Schema::create('riwayat_kgb', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade');
            $table->string('nomor_sk_lama')->nullable();
            $table->date('tanggal_sk_lama')->nullable();
            $table->string('nomor_sk_baru');
            $table->date('tanggal_sk_baru');
            $table->date('tmt_kgb_baru');
            $table->integer('masa_kerja_tahun_baru');
            $table->integer('masa_kerja_bulan_baru');
            $table->decimal('gaji_pokok_baru', 15, 2);
            $table->foreignId('pejabat_id')->nullable()->constrained('master_pejabat')->onDelete('set null');
            $table->string('file_sk')->nullable(); // path to pdf
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_kgb');
    }
};
