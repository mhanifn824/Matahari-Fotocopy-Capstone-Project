<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
