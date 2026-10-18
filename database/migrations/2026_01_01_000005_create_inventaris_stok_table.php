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
            $table->string('nama_bahan', 100);
            $table->integer('jumlah_stok')->default(0);
            $table->integer('batas_minimum')->default(10);
            $table->string('satuan', 30);
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
