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
        // Owner
        User::create([
            'nama' => 'Owner Matahari',
            'email' => 'owner@matahari.com',
            'password' => Hash::make('password123'),
            'nomor_telepon' => '081234567890',
            'role' => 'owner',
        ]);

        // Kasir
        $userKasir = User::create([
            'nama' => 'Kasir Matahari',
            'email' => 'kasir@matahari.com',
            'password' => Hash::make('password123'),
            'nomor_telepon' => '081234567891',
            'role' => 'kasir',
        ]);

        AdminKasir::create([
            'user_id' => $userKasir->user_id,
            'shift_kerja' => 'Pagi-Malam',
        ]);

        // Pelanggan Member
        $userPelanggan = User::create([
            'nama' => 'Budi Santoso',
            'email' => 'budi@gmail.com',
            'password' => Hash::make('password123'),
            'nomor_telepon' => '081234567892',
            'role' => 'pelanggan',
        ]);

        $pelanggan = Pelanggan::create([
            'user_id' => $userPelanggan->user_id,
            'saldo_poin' => 150,
        ]);

        ProgramLoyalitas::create([
            'pelanggan_id' => $pelanggan->pelanggan_id,
            'jumlah_poin_masuk' => 150,
            'jumlah_poin_keluar' => 0,
            'tanggal_transaksi' => now(),
        ]);

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
