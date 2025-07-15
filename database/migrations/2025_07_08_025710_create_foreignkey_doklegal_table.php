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
        // Pertama, tambahkan kolom baru untuk foreign key
        Schema::table('B01DokLegal', function (Blueprint $table) {
            // Tambahkan kolom untuk foreign key ke perusahaan
            $table->unsignedBigInteger('perusahaan_id')->nullable()->after('DokPerusahaan');

            // Tambahkan kolom untuk foreign key ke kategori dokumen
            $table->unsignedBigInteger('kategori_id')->nullable()->after('KategoriDok');

            // Tambahkan kolom untuk foreign key ke jenis dokumen
            $table->unsignedBigInteger('jenis_id')->nullable()->after('JenisDok');
        });

        // Kemudian, tambahkan foreign key constraints
        Schema::table('B01DokLegal', function (Blueprint $table) {
            // Foreign key ke tabel perusahaan
            $table->foreign('perusahaan_id')
                  ->references('id')
                  ->on('A03DmPerusahaan')
                  ->onDelete('set null');

            // Foreign key ke tabel kategori dokumen
            $table->foreign('kategori_id')
                  ->references('id')
                  ->on('A04DmKategoriDok')
                  ->onDelete('set null');

            // Foreign key ke tabel jenis dokumen
            $table->foreign('jenis_id')
                  ->references('id')
                  ->on('A05DmJenisDok')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('B01DokLegal', function (Blueprint $table) {
            // Hapus foreign key constraints
            $table->dropForeign(['perusahaan_id']);
            $table->dropForeign(['kategori_id']);
            $table->dropForeign(['jenis_id']);

            // Hapus kolom foreign key
            $table->dropColumn('perusahaan_id');
            $table->dropColumn('kategori_id');
            $table->dropColumn('jenis_id');
        });
    }
};