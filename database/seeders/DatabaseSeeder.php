<?php

namespace Database\Seeders;

use App\Models\AdminKasir;
use App\Models\InventarisStok;
use App\Models\Pelanggan;
use App\Models\ProdukLayanan;
use App\Models\ProgramLoyalitas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
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

        // Remove old unused demo accounts so only designated admin and user accounts exist
        User::whereNotIn('email', ['admin@123', 'user@321'])->delete();

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
            ProdukLayanan::create($produk);
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
            InventarisStok::create($stok);
        }
    }
}
