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
        'nomor_nota_kustom',
        'pelanggan_id',
        'nama_pemesan',
        'tipe_pelanggan',
        'judul_dokumen',
        'spesifikasi',
        'tenggat_waktu',
        'jenis_pesanan',
        'total_harga',
        'status_pesanan',
        'metode_pembayaran',
        'kasir_nama',
        'volume_halaman',
        'kategori_layanan',
        'waktu_pesan',
    ];

    protected function casts(): array
    {
        return [
            'total_harga' => 'decimal:2',
            'volume_halaman' => 'integer',
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

    public function getNomorNotaAttribute(): string
    {
        if (! empty($this->nomor_nota_kustom)) {
            return $this->nomor_nota_kustom;
        }

        $datePrefix = ($this->waktu_pesan ?? $this->created_at ?? now())->format('ym');

        return '#MF-'.$datePrefix.'-'.str_pad((string) $this->pesanan_id, 3, '0', STR_PAD_LEFT);
    }

    public function getDisplayNameAttribute(): string
    {
        if (! empty($this->nama_pemesan)) {
            return $this->nama_pemesan;
        }

        return $this->pelanggan?->user?->nama ?? 'Pelanggan Gerai';
    }

    public function getDisplayTipeAttribute(): string
    {
        if (! empty($this->tipe_pelanggan)) {
            return $this->tipe_pelanggan;
        }

        return $this->jenis_pesanan === 'online' ? 'Order Web (WA)' : 'Walk-in • Reguler';
    }

    public function getDisplayJudulAttribute(): string
    {
        if (! empty($this->judul_dokumen)) {
            return $this->judul_dokumen;
        }

        $detailFirst = $this->detailPesanan->first();
        if ($detailFirst && $detailFirst->produkLayanan) {
            return $detailFirst->produkLayanan->nama_item.' ('.$detailFirst->kuantitas.' Eks)';
        }

        return 'Dokumen Fotokopi & Cetak';
    }

    public function getDisplaySpesifikasiAttribute(): string
    {
        if (! empty($this->spesifikasi)) {
            return $this->spesifikasi;
        }

        return 'A4 70g • Standard Print';
    }

    public function getDisplayTenggatAttribute(): string
    {
        if (! empty($this->tenggat_waktu)) {
            return $this->tenggat_waktu;
        }

        return ($this->waktu_pesan ?? $this->created_at ?? now())->addMinutes(30)->format('H:i').' WIB';
    }

    public function getKodeNotaWalkInAttribute(): string
    {
        return '#W-'.(1000 + (int) $this->pesanan_id);
    }

    public function getWaktuTransaksiFormatAttribute(): string
    {
        return ($this->waktu_pesan ?? $this->created_at ?? now())->format('H:i').' WIB';
    }

    public function getMetodeBadgeClassAttribute(): string
    {
        $metode = strtolower((string) $this->metode_pembayaran);

        return match ($metode) {
            'qris' => 'bg-sky-50 text-sky-700 border border-sky-200',
            'tunai' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            'bca', 'edc' => 'bg-indigo-50 text-indigo-700 border border-indigo-200',
            'transfer' => 'bg-slate-100 text-slate-700 border border-slate-200',
            default => 'bg-amber-50 text-amber-700 border border-amber-200',
        };
    }
}
