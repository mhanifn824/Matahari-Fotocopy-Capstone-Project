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
        Schema::create('produk_layanan', function (Blueprint $table) {
            $table->bigIncrements('produk_id');
            $table->string('nama_item', 150);
            $table->string('kategori', 50);
            $table->decimal('harga_dasar', 10, 2);
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_layanan');
    }
};
