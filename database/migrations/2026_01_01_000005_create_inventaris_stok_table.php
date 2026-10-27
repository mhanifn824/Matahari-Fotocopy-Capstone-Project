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
        Schema::create('inventaris_stok', function (Blueprint $table) {
            $table->bigIncrements('stok_id');
            $table->string('kode_bahan', 50)->nullable();
            $table->string('nama_bahan', 100);
            $table->string('gramatur_detail', 150)->nullable();
            $table->string('kategori', 50)->default('Kertas');
            $table->integer('jumlah_stok')->default(0);
            $table->integer('batas_minimum')->default(10);
            $table->integer('kapasitas_maksimal')->default(50);
            $table->string('satuan', 30);
            $table->timestamp('terakhir_restock')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventaris_stok');
    }
};
