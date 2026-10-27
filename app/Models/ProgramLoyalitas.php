<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class ProgramLoyalitas extends Model
{
    protected $table = 'program_loyalitas';

    protected $primaryKey = 'poin_id';

    public $incrementing = true;

    protected $fillable = [
        'pelanggan_id',
        'jumlah_poin_masuk',
        'jumlah_poin_keluar',
        'tanggal_transaksi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_transaksi' => 'datetime',
        ];
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id', 'pelanggan_id');
    }

    /**
     * Tambah poin loyalitas pelanggan secara atomik di dalam transaksi DB (Gambar 4.4 Class Diagram - KF-15).
     */
    public static function tambahPoin(int $pelangganId, int $poin): ?self
    {
        if ($poin <= 0) {
            return null;
        }

        return DB::transaction(function () use ($pelangganId, $poin) {
            $pelanggan = Pelanggan::find($pelangganId);
            if (! $pelanggan) {
                return null;
            }

            $pelanggan->increment('saldo_poin', $poin);

            return self::create([
                'pelanggan_id' => $pelanggan->pelanggan_id,
                'jumlah_poin_masuk' => $poin,
                'jumlah_poin_keluar' => 0,
                'tanggal_transaksi' => now(),
            ]);
        });
    }

    /**
     * Tukar poin loyalitas pelanggan dengan reward/voucher secara atomik (Gambar 4.4 Class Diagram - UC-08 & UC-13).
     */
    public static function tukarVoucher(int $pelangganId, int $poinKeluar): ?self
    {
        if ($poinKeluar <= 0) {
            return null;
        }

        return DB::transaction(function () use ($pelangganId, $poinKeluar) {
            $pelanggan = Pelanggan::find($pelangganId);
            if (! $pelanggan || $pelanggan->saldo_poin < $poinKeluar) {
                return null;
            }

            $pelanggan->decrement('saldo_poin', $poinKeluar);

            return self::create([
                'pelanggan_id' => $pelanggan->pelanggan_id,
                'jumlah_poin_masuk' => 0,
                'jumlah_poin_keluar' => $poinKeluar,
                'tanggal_transaksi' => now(),
            ]);
        });
    }
}
