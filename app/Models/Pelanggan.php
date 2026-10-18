<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    protected $table = 'pelanggan';

    protected $primaryKey = 'pelanggan_id';

    public $incrementing = true;

    protected $fillable = [
        'user_id',
        'saldo_poin',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'pelanggan_id', 'pelanggan_id');
    }

    public function berkasDokumen(): HasMany
    {
        return $this->hasMany(BerkasDokumen::class, 'pelanggan_id', 'pelanggan_id');
    }

    public function programLoyalitas(): HasMany
    {
        return $this->hasMany(ProgramLoyalitas::class, 'pelanggan_id', 'pelanggan_id');
    }
}
