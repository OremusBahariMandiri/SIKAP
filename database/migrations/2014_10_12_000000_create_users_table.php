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
        Schema::create('A01DmUser', function (Blueprint $table) {
            $table->id(); // Handel komputer (primary key)
            $table->string('IdKode')->unique(); // Kode unik untuk identifikasi innerjoin
            $table->string('NikKry')->unique(); // NIK Karyawan
            $table->string('NamaKry'); // Nama Karyawan
            $table->string('DepartemenKry'); // Departemen Karyawan
            $table->string('JabatanKry'); // Jabatan Karyawan
            $table->string('WilkerKry'); // Wilayah Kerja Karyawan
            $table->string('PasswordKry'); // Password Karyawan
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('A01DmUser');
    }
};