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
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->bigIncrements('pelanggan_id');
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('kode_member', 50)->nullable()->unique();
            $table->string('kategori_member', 50)->default('Member Umum');
            $table->string('instansi_detail', 150)->nullable();
            $table->string('badge_label', 50)->nullable();
            $table->integer('saldo_poin')->default(0);
            $table->integer('total_kunjungan')->default(1);
            $table->decimal('total_belanja', 12, 2)->default(0);
            $table->string('loyalty_tier', 50)->default('Pemula');
            $table->integer('hadiah_terklaim_count')->default(0);
            $table->timestamp('terakhir_kunjungan')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggan');
    }
};
