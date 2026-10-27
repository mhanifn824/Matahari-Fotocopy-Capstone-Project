<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatBahanMasuk extends Model
{
    protected $table = 'riwayat_bahan_masuk';

    protected $fillable = [
        'no_surat_jalan',
        'stok_id',
        'nama_item',
        'spesifikasi',
        'jumlah_masuk',
        'satuan',
        'mitra_supplier',
        'status_penerimaan',
        'waktu_masuk',
    ];

    protected function casts(): array
    {
        return [
            'waktu_masuk' => 'datetime',
            'jumlah_masuk' => 'integer',
        ];
    }

    public function inventarisStok(): BelongsTo
    {
        return $this->belongsTo(InventarisStok::class, 'stok_id', 'stok_id');
    }

    public function getWaktuFormattedAttribute(): string
    {
        $time = $this->waktu_masuk ?? $this->created_at ?? now();
        if ($time->isToday()) {
            return 'Hari ini • '.$time->format('H:i');
        } elseif ($time->isYesterday()) {
            return 'Kemarin • '.$time->format('H:i');
        }

        return $time->translatedFormat('d M').' • '.$time->format('H:i');
    }
}
