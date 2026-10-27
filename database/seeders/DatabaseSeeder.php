<?php

namespace Database\Seeders;

use App\Models\AdminKasir;
use App\Models\InventarisStok;
use App\Models\Pelanggan;
use App\Models\ProdukLayanan;
use App\Models\ProgramLoyalitas;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->ensureSchemaSynced();

        // 1. Akun Pengguna
        // Admin Uji (Kredensial Uji: admin@123 / 12345)
        $adminUji = User::updateOrCreate(
            ['email' => 'admin@123'],
            [
                'nama' => 'Admin Matahari (Uji)',
                'password' => Hash::make('12345'),
                'nomor_telepon' => '081234567890',
                'role' => 'admin',
            ]
        );

        AdminKasir::updateOrCreate(
            ['user_id' => $adminUji->user_id],
            ['shift_kerja' => 'Pagi-Sore']
        );

        // Pelanggan Uji (Kredensial Uji: user@321 / 12345)
        $userUji = User::updateOrCreate(
            ['email' => 'user@321'],
            [
                'nama' => 'Pelanggan Setia (Uji)',
                'password' => Hash::make('12345'),
                'nomor_telepon' => '081234567899',
                'role' => 'user',
            ]
        );

        $pelangganUji = Pelanggan::updateOrCreate(
            ['user_id' => $userUji->user_id],
            ['saldo_poin' => 250]
        );

        ProgramLoyalitas::firstOrCreate(
            ['pelanggan_id' => $pelangganUji->pelanggan_id],
            [
                'jumlah_poin_masuk' => 250,
                'jumlah_poin_keluar' => 0,
                'tanggal_transaksi' => now(),
            ]
        );

        // Repair any existing customer user whose role was truncated to empty string before schema sync
        User::where('role', '')->orWhereNull('role')->update(['role' => 'user']);

        // 2. Produk Layanan
        $produkList = [
            [
                'nama_item' => 'Cetak A4 Hitam Putih',
                'kategori' => 'Jasa Cetak',
                'harga_dasar' => 300.00,
                'status_aktif' => true,
            ],
            [
                'nama_item' => 'Cetak A4 Warna',
                'kategori' => 'Jasa Cetak',
                'harga_dasar' => 1000.00,
                'status_aktif' => true,
            ],
            [
                'nama_item' => 'Cetak F4 Hitam Putih',
                'kategori' => 'Jasa Cetak',
                'harga_dasar' => 350.00,
                'status_aktif' => true,
            ],
            [
                'nama_item' => 'Jilid Lakban',
                'kategori' => 'Jilid',
                'harga_dasar' => 3000.00,
                'status_aktif' => true,
            ],
            [
                'nama_item' => 'Jilid Spiral Kawat',
                'kategori' => 'Jilid',
                'harga_dasar' => 8000.00,
                'status_aktif' => true,
            ],
        ];

        foreach ($produkList as $produk) {
            ProdukLayanan::updateOrCreate(
                ['nama_item' => $produk['nama_item']],
                $produk
            );
        }

        // 3. Inventaris Bahan Baku Awal
        $inventarisList = [
            [
                'nama_bahan' => 'Kertas HVS A4 70gr',
                'jumlah_stok' => 50,
                'batas_minimum' => 5,
                'satuan' => 'Rim',
            ],
            [
                'nama_bahan' => 'Kertas HVS F4 70gr',
                'jumlah_stok' => 40,
                'batas_minimum' => 5,
                'satuan' => 'Rim',
            ],
            [
                'nama_bahan' => 'Tinta Botol Hitam',
                'jumlah_stok' => 10,
                'batas_minimum' => 2,
                'satuan' => 'Botol',
            ],
        ];

        foreach ($inventarisList as $stok) {
            InventarisStok::updateOrCreate(
                ['nama_bahan' => $stok['nama_bahan']],
                $stok
            );
        }
    }

    /**
     * Ensure existing database tables match the latest schema definition
     * (handles upgrading older MySQL tables where users.role was an ENUM).
     */
    protected function ensureSchemaSynced(): void
    {
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('users')) {
            DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` VARCHAR(20) NOT NULL DEFAULT 'user'");
        }

        if (Schema::hasTable('pelanggan') && ! Schema::hasColumn('pelanggan', 'kode_member')) {
            Schema::table('pelanggan', function (Blueprint $table) {
                $table->string('kode_member', 50)->nullable()->unique();
                $table->string('kategori_member', 50)->default('Member Umum');
                $table->string('instansi_detail', 150)->nullable();
                $table->string('badge_label', 50)->nullable();
                $table->integer('total_kunjungan')->default(1);
                $table->decimal('total_belanja', 12, 2)->default(0);
                $table->string('loyalty_tier', 50)->default('Pemula');
                $table->integer('hadiah_terklaim_count')->default(0);
                $table->timestamp('terakhir_kunjungan')->nullable();
            });
        }

        if (Schema::hasTable('inventaris_stok') && ! Schema::hasColumn('inventaris_stok', 'kode_bahan')) {
            Schema::table('inventaris_stok', function (Blueprint $table) {
                $table->string('kode_bahan', 50)->nullable();
                $table->string('gramatur_detail', 150)->nullable();
                $table->string('kategori', 50)->default('Kertas');
                $table->integer('kapasitas_maksimal')->default(50);
                $table->timestamp('terakhir_restock')->nullable();
            });
        }

        if (Schema::hasTable('pesanan') && ! Schema::hasColumn('pesanan', 'nomor_nota_kustom')) {
            Schema::table('pesanan', function (Blueprint $table) {
                $table->string('nomor_nota_kustom', 50)->nullable();
                $table->string('nama_pemesan', 150)->nullable();
                $table->string('tipe_pelanggan', 50)->nullable()->default('Walk-in • Reguler');
                $table->string('judul_dokumen', 200)->nullable();
                $table->text('spesifikasi')->nullable();
                $table->string('tenggat_waktu', 50)->nullable();
                $table->string('kasir_nama', 100)->nullable()->default('Feri Anto');
                $table->integer('volume_halaman')->default(1);
                $table->string('kategori_layanan', 100)->default('Fotokopi & Print B/W');
            });
        }

        if (Schema::hasTable('antrean_mesin') && ! Schema::hasColumn('antrean_mesin', 'nama_mesin')) {
            Schema::table('antrean_mesin', function (Blueprint $table) {
                $table->string('nama_mesin', 100)->nullable()->default('Mesin Konica 1');
                $table->string('nama_operator', 100)->nullable()->default('Feri');
                $table->string('status_alur', 50)->nullable()->default('Diterima');
                $table->string('status_pembayaran', 50)->nullable()->default('lunas');
                $table->string('keterangan_tenggat', 100)->nullable()->default('Sesuai Jadwal');
            });
        }

        if (! Schema::hasTable('riwayat_bahan_masuk')) {
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

        if (! Schema::hasTable('log_klaim_hadiah')) {
            Schema::create('log_klaim_hadiah', function (Blueprint $table) {
                $table->bigIncrements('klaim_id');
                $table->unsignedBigInteger('pelanggan_id')->nullable();
                $table->string('nama_member', 150);
                $table->string('nama_hadiah', 150);
                $table->string('tipe_klaim', 100);
                $table->string('nama_kasir', 100)->default('Feri Anto');
                $table->string('no_nota', 50)->nullable();
                $table->timestamp('waktu_klaim')->useCurrent();
                $table->timestamps();
            });
        }
    }
}
