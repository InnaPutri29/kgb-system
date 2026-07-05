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
            $table->string('nomor_sk_baru');
            $table->date('tanggal_ditetapkan');
            $table->date('tmt_baru');
            $table->bigInteger('gaji_pokok_lama');
            $table->bigInteger('gaji_pokok_baru');
            $table->integer('masa_kerja_tahun_baru');
            $table->integer('masa_kerja_bulan_baru');
            $table->date('tmt_yad');                       // tmt_baru + 2 tahun
            $table->string('pejabat_penetap');             // snapshot nama pimpinan
            $table->string('file_pdf_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
