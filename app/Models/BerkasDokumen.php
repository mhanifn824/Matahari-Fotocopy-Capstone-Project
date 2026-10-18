<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BerkasDokumen extends Model
{
    protected $table = 'berkas_dokumen';

    protected $primaryKey = 'berkas_id';

    public $incrementing = true;

    protected $fillable = [
        'pelanggan_id',
        'nama_file',
        'file_path',
        'ukuran_file',
        'jumlah_halaman',
        'status_validasi',
    ];

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id', 'pelanggan_id');
    }

    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'berkas_id', 'berkas_id');
    }
}
