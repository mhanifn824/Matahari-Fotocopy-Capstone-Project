<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $primaryKey = 'pesanan_id';

    public $incrementing = true;

    protected $fillable = [
        'pelanggan_id',
        'jenis_pesanan',
        'total_harga',
        'status_pesanan',
        'metode_pembayaran',
        'waktu_pesan',
    ];

    protected function casts(): array
    {
        return [
            'total_harga' => 'decimal:2',
            'waktu_pesan' => 'datetime',
        ];
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id', 'pelanggan_id');
    }

    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'pesanan_id', 'pesanan_id');
    }

    public function antreanMesin(): HasOne
    {
        return $this->hasOne(AntreanMesin::class, 'pesanan_id', 'pesanan_id');
    }
}
