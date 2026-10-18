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
        Schema::create('pesanan', function (Blueprint $table) {
            $table->bigIncrements('pesanan_id');
            $table->unsignedBigInteger('pelanggan_id')->nullable();
            $table->string('jenis_pesanan', 30);
            $table->decimal('total_harga', 10, 2)->default(0);
            $table->string('status_pesanan', 50)->default('menunggu');
            $table->string('metode_pembayaran', 50);
            $table->timestamp('waktu_pesan')->useCurrent();
            $table->timestamps();

            $table->foreign('pelanggan_id')
                ->references('pelanggan_id')
                ->on('pelanggan')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
