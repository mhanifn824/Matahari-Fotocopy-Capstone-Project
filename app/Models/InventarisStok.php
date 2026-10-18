<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventarisStok extends Model
{
    protected $table = 'inventaris_stok';

    protected $primaryKey = 'stok_id';

    public $incrementing = true;

    protected $fillable = [
        'nama_bahan',
        'jumlah_stok',
        'batas_minimum',
        'satuan',
    ];

    public function isLowStock(): bool
    {
        return $this->jumlah_stok <= $this->batas_minimum;
    }
}
