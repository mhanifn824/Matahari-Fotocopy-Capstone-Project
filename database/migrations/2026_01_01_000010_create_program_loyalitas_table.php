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
        Schema::create('program_loyalitas', function (Blueprint $table) {
            $table->bigIncrements('poin_id');
            $table->unsignedBigInteger('pelanggan_id');
            $table->integer('jumlah_poin_masuk')->default(0);
            $table->integer('jumlah_poin_keluar')->default(0);
            $table->timestamp('tanggal_transaksi')->useCurrent();
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
        Schema::dropIfExists('program_loyalitas');
    }
};
