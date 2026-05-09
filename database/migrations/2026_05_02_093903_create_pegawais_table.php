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
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nip')->unique();
            $table->string('nama_lengkap');
            $table->date('tanggal_lahir')->nullable();
            $table->string('pangkat_golongan')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('unit_kerja')->nullable()->default('RSD SIDAWANGI');
            $table->date('tmt_pangkat_terakhir')->nullable();
            $table->date('tmt_gaji_terakhir')->nullable();
            $table->integer('masa_kerja_tahun')->default(0);
            $table->integer('masa_kerja_bulan')->default(0);
            $table->decimal('gaji_pokok_terakhir', 15, 2)->nullable();
            $table->foreignId('master_pejabat_id')->nullable()->constrained('master_pejabat')->nullOnDelete();
            $table->string('nomor_sk_terakhir')->nullable();
            $table->boolean('is_sedang_hukuman_disiplin')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
