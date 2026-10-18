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
        Schema::create('antrean_mesin', function (Blueprint $table) {
            $table->bigIncrements('antrean_id');
            $table->unsignedBigInteger('pesanan_id')->unique();
            $table->integer('nomor_tiket');
            $table->string('status_antrean', 50)->default('menunggu');
            $table->integer('estimasi_waktu');
            $table->timestamps();

            $table->foreign('pesanan_id')
                ->references('pesanan_id')
                ->on('pesanan')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antrean_mesin');
    }
};
