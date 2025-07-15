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
        Schema::create('A04DmKategoriDok', function (Blueprint $table) {
            $table->id(); // Handel komputer (primary key)
            $table->string('IdKode')->unique(); // Kode unik untuk identifikasi innerjoin (A030725001)
            $table->string('KategoriDok'); // Kategori Dokumen
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('A04DmKategoriDok');
    }
};