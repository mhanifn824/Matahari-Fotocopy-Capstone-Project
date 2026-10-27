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
        Schema::create('riwayat_bahan_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat_jalan', 50);
            $table->unsignedBigInteger('stok_id')->nullable();
            $table->string('nama_item', 150);
            $table->string('spesifikasi', 150)->nullable();
            $table->integer('jumlah_masuk');
            $table->string('satuan', 30);
            $table->string('mitra_supplier', 150)->default('CV Sinar Abadi Bintaro');
            $table->string('status_penerimaan', 50)->default('Diterima');
            $table->timestamp('waktu_masuk')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_bahan_masuk');
    }
};
