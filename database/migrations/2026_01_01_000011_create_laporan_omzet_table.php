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
        Schema::create('laporan_omzet', function (Blueprint $table) {
            $table->bigIncrements('laporan_id');
            $table->string('periode', 50);
            $table->decimal('total_pendapatan_online', 12, 2)->default(0);
            $table->decimal('total_pendapatan_offline', 12, 2)->default(0);
            $table->timestamp('tanggal_generate')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_omzet');
    }
};
