<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AntreanMesin extends Model
{
    protected $table = 'antrean_mesin';

    protected $primaryKey = 'antrean_id';

    public $incrementing = true;

    protected $fillable = [
        'pesanan_id',
        'nomor_tiket',
        'status_antrean',
        'estimasi_waktu',
    ];

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id', 'pesanan_id');
    }
}
