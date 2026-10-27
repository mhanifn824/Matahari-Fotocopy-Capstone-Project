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
            $table->string('nomor_nota_kustom', 50)->nullable();
            $table->unsignedBigInteger('pelanggan_id')->nullable();
            $table->string('nama_pemesan', 150)->nullable();
            $table->string('tipe_pelanggan', 50)->nullable()->default('Walk-in • Reguler');
            $table->string('judul_dokumen', 200)->nullable();
            $table->text('spesifikasi')->nullable();
            $table->string('tenggat_waktu', 50)->nullable();
            $table->string('jenis_pesanan', 30);
            $table->decimal('total_harga', 10, 2)->default(0);
            $table->string('status_pesanan', 50)->default('menunggu');
            $table->string('metode_pembayaran', 50);
            $table->string('kasir_nama', 100)->nullable()->default('Feri Anto');
            $table->integer('volume_halaman')->default(1);
            $table->string('kategori_layanan', 100)->default('Fotokopi & Print B/W');
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
