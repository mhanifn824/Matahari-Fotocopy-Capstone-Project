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
        Schema::create('log_klaim_hadiah', function (Blueprint $table) {
            $table->bigIncrements('klaim_id');
            $table->unsignedBigInteger('pelanggan_id')->nullable();
            $table->string('nama_member', 150);
            $table->string('nama_hadiah', 150);
            $table->string('tipe_klaim', 100);
            $table->string('nama_kasir', 100)->default('Feri Anto');
            $table->string('no_nota', 50)->nullable();
            $table->timestamp('waktu_klaim')->useCurrent();
            $table->timestamps();

            $table->foreign('pelanggan_id')
                ->references('pelanggan_id')
                ->on('pelanggan')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_klaim_hadiah');
    }
};
