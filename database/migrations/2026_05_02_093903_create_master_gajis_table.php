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
        Schema::create('master_gaji', function (Blueprint $table) {
            $table->id();
            $table->string('golongan', 5);
            $table->integer('masa_kerja');
            $table->bigInteger('nominal_gaji');
            $table->timestamps();

            $table->unique(['golongan', 'masa_kerja']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_gaji');
    }
};
