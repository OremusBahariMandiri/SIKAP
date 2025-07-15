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
        Schema::create('A02DmUserAccess', function (Blueprint $table) {
            $table->id();
            $table->foreignId('IdKodeA01')->constrained('A01DmUser', 'id')->onDelete('cascade');
            $table->string('MenuAcs');
            $table->boolean('TambahAcs')->default(false);
            $table->boolean('UbahAcs')->default(false);
            $table->boolean('HapusAcs')->default(false);
            $table->boolean('DownloadAcs')->default(false);
            $table->boolean('DetailAcs')->default(false);
            $table->boolean('MonitoringAcs')->default(false);
            $table->timestamps();

            // Unique constraint to prevent duplicate roles for the same user and module
            $table->unique(['IdKodeA01', 'MenuAcs']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('A02DmUserAccess');
    }
};