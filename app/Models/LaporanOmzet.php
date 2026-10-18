<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanOmzet extends Model
{
    protected $table = 'laporan_omzet';

    protected $primaryKey = 'laporan_id';

    public $incrementing = true;

    protected $fillable = [
        'periode',
        'total_pendapatan_online',
        'total_pendapatan_offline',
        'tanggal_generate',
    ];

    protected function casts(): array
    {
        return [
            'total_pendapatan_online' => 'decimal:2',
            'total_pendapatan_offline' => 'decimal:2',
            'tanggal_generate' => 'datetime',
        ];
    }
}
