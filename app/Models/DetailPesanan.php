<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPesanan extends Model
{
    protected $table = 'detail_pesanan';

    protected $primaryKey = 'detail_id';

    public $incrementing = true;

    protected $fillable = [
        'pesanan_id',
        'produk_id',
        'berkas_id',
        'kuantitas',
        'sub_total',
    ];

    protected function casts(): array
    {
        return [
            'sub_total' => 'decimal:2',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id', 'pesanan_id');
    }

    public function produkLayanan(): BelongsTo
    {
        return $this->belongsTo(ProdukLayanan::class, 'produk_id', 'produk_id');
    }

    public function berkasDokumen(): BelongsTo
    {
        return $this->belongsTo(BerkasDokumen::class, 'berkas_id', 'berkas_id');
    }
}
