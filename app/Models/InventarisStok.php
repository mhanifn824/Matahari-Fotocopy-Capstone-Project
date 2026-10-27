<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventarisStok extends Model
{
    protected $table = 'inventaris_stok';

    protected $primaryKey = 'stok_id';

    public $incrementing = true;

    protected $fillable = [
        'kode_bahan',
        'nama_bahan',
        'gramatur_detail',
        'kategori',
        'jumlah_stok',
        'batas_minimum',
        'kapasitas_maksimal',
        'satuan',
        'terakhir_restock',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_stok' => 'integer',
            'batas_minimum' => 'integer',
            'kapasitas_maksimal' => 'integer',
            'terakhir_restock' => 'datetime',
        ];
    }

    public function riwayatBahanMasuk(): HasMany
    {
        return $this->hasMany(RiwayatBahanMasuk::class, 'stok_id', 'stok_id');
    }

    public function isLowStock(): bool
    {
        return $this->jumlah_stok <= $this->batas_minimum;
    }

    public function getDisplayKodeAttribute(): string
    {
        return $this->kode_bahan ?: ('K-'.str_pad((string) $this->stok_id, 3, '0', STR_PAD_LEFT));
    }

    public function getStatusStokAttribute(): string
    {
        if ($this->jumlah_stok <= 0) {
            return 'Habis';
        }
        if ($this->jumlah_stok <= $this->batas_minimum) {
            return 'Menipis'; // In reference image: Menipis with red text
        }

        return 'Aman';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        $status = $this->status_stok;
        if ($status === 'Habis' || $status === 'Menipis') {
            return 'bg-rose-50 text-rose-700 border border-rose-200';
        }

        return 'bg-amber-50/80 text-amber-800 border border-amber-200';
    }

    public function getPersentaseKapasitasAttribute(): int
    {
        $max = max(1, $this->kapasitas_maksimal ?: ($this->batas_minimum * 3));

        return (int) min(100, round(($this->jumlah_stok / $max) * 100));
    }

    public function getIndikatorBarColorAttribute(): string
    {
        $status = $this->status_stok;
        if ($status === 'Habis' || $status === 'Menipis') {
            return 'bg-rose-500';
        }

        return 'bg-slate-700';
    }

    public function getTerakhirRestockFormattedAttribute(): string
    {
        if (! $this->terakhir_restock) {
            return ($this->updated_at ?? now())->translatedFormat('d M Y');
        }

        $dt = $this->terakhir_restock;
        if ($dt->isToday()) {
            return 'Hari ini';
        }
        if ($dt->isYesterday()) {
            return 'Kemarin';
        }

        return $dt->translatedFormat('d M Y');
    }

    public function getGambarUrlAttribute(): string
    {
        $code = strtolower(trim((string) $this->kode_bahan));
        $extensions = ['svg', 'png', 'jpg', 'jpeg', 'webp'];

        foreach ($extensions as $ext) {
            $filename = "images/stok/{$code}.{$ext}";
            if (file_exists(public_path($filename))) {
                return asset($filename);
            }
        }

        return asset('images/stok/default.svg');
    }

    public function getEstimasiHargaAttribute(): int
    {
        $code = strtoupper(trim((string) $this->kode_bahan));
        $specifics = [
            'K-A4-70' => 42500,
            'K-A4-80' => 45000,
            'K-HVS-A480' => 45000,
            'K-F4-75' => 48000,
            'K-A3-150' => 55000,
            'O-BNT-F4' => 29500,
            'P-ZBR-KOK' => 34000,
            'L-DMR-CKL' => 14500,
            'J-SPR-04' => 8500,
            'J-SPR-10' => 8000,
            'A-STP-KNG' => 95000,
            'J-LKB-BK' => 16000,
            'J-MIK-01' => 22000,
            'J-KTN-NV' => 18000,
        ];

        if (isset($specifics[$code])) {
            return $specifics[$code];
        }

        return match (strtolower(trim($this->satuan))) {
            'rim' => 45000,
            'pak', 'box' => 32000,
            'roll' => 14000,
            'batang' => 6000,
            'tabung', 'botol' => 95000,
            'lembar' => 3500,
            default => 25000,
        };
    }
}
