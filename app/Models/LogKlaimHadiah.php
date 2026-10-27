<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogKlaimHadiah extends Model
{
    protected $table = 'log_klaim_hadiah';

    protected $primaryKey = 'klaim_id';

    public $incrementing = true;

    protected $fillable = [
        'pelanggan_id',
        'nama_member',
        'nama_hadiah',
        'tipe_klaim',
        'nama_kasir',
        'no_nota',
        'waktu_klaim',
    ];

    protected function casts(): array
    {
        return [
            'waktu_klaim' => 'datetime',
        ];
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id', 'pelanggan_id');
    }

    public function getWaktuFormattedAttribute(): string
    {
        $time = $this->waktu_klaim ?? $this->created_at ?? now();

        return $time->format('H:i').' WIB';
    }
}
