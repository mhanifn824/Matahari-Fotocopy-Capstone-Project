<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProdukLayanan extends Model
{
    protected $table = 'produk_layanan';

    protected $primaryKey = 'produk_id';

    public $incrementing = true;

    protected $fillable = [
        'nama_item',
        'kategori',
        'harga_dasar',
        'status_aktif',
    ];

    protected function casts(): array
    {
        return [
            'harga_dasar' => 'decimal:2',
            'status_aktif' => 'boolean',
        ];
    }

    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'produk_id', 'produk_id');
    }
}
