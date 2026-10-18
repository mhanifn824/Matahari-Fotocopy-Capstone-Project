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
        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->bigIncrements('detail_id');
            $table->unsignedBigInteger('pesanan_id');
            $table->unsignedBigInteger('produk_id');
            $table->unsignedBigInteger('berkas_id')->nullable();
            $table->integer('kuantitas')->default(1);
            $table->decimal('sub_total', 10, 2);
            $table->timestamps();

            $table->foreign('pesanan_id')
                ->references('pesanan_id')
                ->on('pesanan')
                ->onDelete('cascade');

            $table->foreign('produk_id')
                ->references('produk_id')
                ->on('produk_layanan');

            $table->foreign('berkas_id')
                ->references('berkas_id')
                ->on('berkas_dokumen')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pesanan');
    }
};
