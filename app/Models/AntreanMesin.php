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
        'nama_mesin',
        'nama_operator',
        'status_alur',
        'status_pembayaran',
        'keterangan_tenggat',
    ];

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id', 'pesanan_id');
    }

    public function getKodeAntreanAttribute(): string
    {
        return 'A-'.str_pad((string) $this->nomor_tiket, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Generate kode tiket antrean digital (Gambar 4.4 Class Diagram - UC-06 & UC-07).
     */
    public function generateTiket(): string
    {
        if (empty($this->nomor_tiket)) {
            $next = (int) (self::whereDate('created_at', now()->toDateString())->max('nomor_tiket') ?? 0) + 1;
            $this->nomor_tiket = $next;
        }

        return $this->kode_antrean;
    }

    public function getStatusLabelAttribute(): string
    {
        return match (strtolower((string) $this->status_antrean)) {
            'cetak', 'mesin 01', 'mesin_01' => 'Mesin 01 (Cetak)',
            'salin', 'mesin 02', 'mesin_02' => 'Mesin 02 (Salin)',
            'menunggu' => 'Menunggu Mesin',
            'finishing', 'jilid' => 'Meja Jilid (Finishing)',
            'siap', 'siap_pickup' => 'Siap di Rak Loket',
            'selesai' => 'Selesai',
            default => ucfirst((string) $this->status_antrean),
        };
    }

    public function getStatusColorAttribute(): array
    {
        return match (strtolower((string) $this->status_antrean)) {
            'cetak', 'mesin 01', 'mesin_01' => [
                'bg' => 'bg-blue-50',
                'text' => 'text-blue-700',
                'border' => 'border-blue-200',
                'dot' => 'bg-blue-600',
            ],
            'salin', 'mesin 02', 'mesin_02' => [
                'bg' => 'bg-sky-50',
                'text' => 'text-sky-700',
                'border' => 'border-sky-200',
                'dot' => 'bg-sky-600',
            ],
            'menunggu' => [
                'bg' => 'bg-slate-100',
                'text' => 'text-slate-600',
                'border' => 'border-slate-200',
                'dot' => 'bg-slate-400',
            ],
            'finishing', 'jilid' => [
                'bg' => 'bg-amber-50',
                'text' => 'text-amber-700',
                'border' => 'border-amber-200',
                'dot' => 'bg-amber-500',
            ],
            'siap', 'siap_pickup' => [
                'bg' => 'bg-purple-50',
                'text' => 'text-purple-700',
                'border' => 'border-purple-200',
                'dot' => 'bg-purple-600',
            ],
            'selesai' => [
                'bg' => 'bg-emerald-50',
                'text' => 'text-emerald-700',
                'border' => 'border-emerald-200',
                'dot' => 'bg-emerald-600',
            ],
            default => [
                'bg' => 'bg-slate-50',
                'text' => 'text-slate-700',
                'border' => 'border-slate-200',
                'dot' => 'bg-slate-400',
            ],
        };
    }

    public function getBadgeAlurColorAttribute(): array
    {
        return match (strtolower((string) ($this->status_alur ?? 'diterima'))) {
            'sedang cetak', 'cetak' => [
                'bg' => 'bg-blue-50',
                'text' => 'text-blue-700',
                'border' => 'border-blue-200',
                'dot' => 'bg-blue-600',
            ],
            'sedang jilid', 'finishing' => [
                'bg' => 'bg-amber-50',
                'text' => 'text-amber-800',
                'border' => 'border-amber-200',
                'dot' => 'bg-amber-600',
            ],
            'siap diambil', 'siap', 'siap_pickup' => [
                'bg' => 'bg-indigo-50',
                'text' => 'text-indigo-800',
                'border' => 'border-indigo-200',
                'dot' => 'bg-indigo-600',
            ],
            'menunggu bahan', 'menunggu' => [
                'bg' => 'bg-orange-50',
                'text' => 'text-orange-800',
                'border' => 'border-orange-200',
                'dot' => 'bg-orange-500',
            ],
            'diterima' => [
                'bg' => 'bg-slate-100',
                'text' => 'text-slate-700',
                'border' => 'border-slate-200',
                'dot' => 'bg-slate-500',
            ],
            default => [
                'bg' => 'bg-slate-100',
                'text' => 'text-slate-600',
                'border' => 'border-slate-200',
                'dot' => 'bg-slate-400',
            ],
        };
    }
}
