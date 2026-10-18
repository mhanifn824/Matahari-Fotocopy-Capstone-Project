<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminKasir extends Model
{
    protected $table = 'admin_kasir';

    protected $primaryKey = 'admin_id';

    public $incrementing = true;

    protected $fillable = [
        'user_id',
        'shift_kerja',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
