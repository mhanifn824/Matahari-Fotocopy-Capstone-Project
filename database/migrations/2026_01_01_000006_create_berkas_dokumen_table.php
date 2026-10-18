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
        Schema::create('berkas_dokumen', function (Blueprint $table) {
            $table->bigIncrements('berkas_id');
            $table->unsignedBigInteger('pelanggan_id');
            $table->string('nama_file', 255);
            $table->text('file_path');
            $table->integer('ukuran_file');
            $table->integer('jumlah_halaman')->default(1);
            $table->string('status_validasi', 50)->default('valid');
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
        Schema::dropIfExists('berkas_dokumen');
    }
};
