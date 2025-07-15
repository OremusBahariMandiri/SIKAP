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
        Schema::create('B01DokLegal', function (Blueprint $table) {
            $table->id();
            $table->string('IdKode');
            $table->string('NoRegDok');
            $table->string('DokPerusahaan');
            $table->string('KategoriDok');
            $table->string('JenisDok');
            $table->string('PeruntukanDok');
            $table->string('DokAtasNama');
            $table->text('KetDok');
            $table->enum('JnsMasaBerlaku', ['Tetap', 'Perpanjangan']);
            $table->date('TglTerbitDok');
            $table->date('TglBerakhirDok')->nullable();
            $table->string('MasaBerlaku');
            $table->date('TglPengingat')->nullable();
            $table->string('MasaPengingat');
            $table->string('FileDok');
            $table->enum('StsBerlakuDok', ['Berlaku', 'Tidak Berlaku']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('B01DokLegal');
    }
};