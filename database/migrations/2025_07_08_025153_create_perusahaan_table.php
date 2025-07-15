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
        Schema::create('A03DmPerusahaan', function (Blueprint $table) {
            $table->id(); // Handel komputer (primary key)
            $table->string('IdKode')->unique(); // Kode unik untuk identifikasi innerjoin (A020725001)
            $table->string('NamaPrsh'); // Nama Perusahaan
            $table->text('AlamatPrsh'); // Alamat Perusahaan
            $table->string('TelpPrsh'); // Telpon Perusahaan
            $table->string('TelpPrsh2'); // Telpon Perusahaan
            $table->string('EmailPrsh'); // Email Perusahaan
            $table->string('EmailPrsh2'); // Email Perusahaan
            $table->string('WebPrsh')->nullable(); // Web Perusahaan
            $table->date('TglBerdiri')->nullable(); // Tanggal Berdiri
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('A03DmPerusahaan');
    }
};