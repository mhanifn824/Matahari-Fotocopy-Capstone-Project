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
        'kode_member',
        'kategori_member',
        'instansi_detail',
        'badge_label',
        'saldo_poin',
        'total_kunjungan',
        'total_belanja',
        'loyalty_tier',
        'hadiah_terklaim_count',
        'terakhir_kunjungan',
    ];

    protected function casts(): array
    {
        return [
            'total_belanja' => 'decimal:2',
            'terakhir_kunjungan' => 'datetime',
            'total_kunjungan' => 'integer',
            'saldo_poin' => 'integer',
            'hadiah_terklaim_count' => 'integer',
        ];
    }

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

    public function logKlaimHadiah(): HasMany
    {
        return $this->hasMany(LogKlaimHadiah::class, 'pelanggan_id', 'pelanggan_id');
    }

    public function getInitialsAttribute(): string
    {
        $name = trim($this->user?->nama ?? 'Member');
        $parts = explode(' ', $name);
        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1).substr($parts[1], 0, 1));
        }

        return strtoupper(substr($name, 0, 2));
    }

    public function getDisplayKodeMemberAttribute(): string
    {
        return $this->kode_member ?: 'MTH-'.str_pad((string) $this->pelanggan_id, 4, '0', STR_PAD_LEFT);
    }

    public function getDisplayWhatsappAttribute(): string
    {
        $telp = $this->user?->nomor_telepon ?? '0812-9844-3109';

        // Clean display
        return $telp;
    }

    public function getProgressInfoAttribute(): array
    {
        $k = $this->total_kunjungan;
        if ($k >= 20) {
            return [
                'current' => $k,
                'target' => 20,
                'percent' => 100,
                'label' => "{$k}x Kunjungan",
                'sub' => 'Tembus Target 20x VIP',
                'target_badge' => 'VIP MAX',
                'is_claimable' => ($this->hadiah_terklaim_count < 4),
                'reward_name' => 'Flashdisk 32GB',
                'claim_type' => 'vip',
            ];
        }

        if ($k >= 10) {
            $isClaimed = ($this->hadiah_terklaim_count >= 3);

            return [
                'current' => $k,
                'target' => 20,
                'percent' => round(($k / 20) * 100),
                'label' => "{$k}x Kunjungan",
                'sub' => $isClaimed ? 'Kurang '.max(0, 20 - $k).'x lagi reward Flashdisk' : 'Mencapai Target Milestone!',
                'target_badge' => $isClaimed ? 'Next: 20x VIP' : '10/10 SELESAI',
                'is_claimable' => ! $isClaimed,
                'reward_name' => 'Voucher Rp 25k',
                'claim_type' => 'gold',
            ];
        }

        if ($k >= 5) {
            $isClaimed = ($this->hadiah_terklaim_count >= 2);

            return [
                'current' => $k,
                'target' => 10,
                'percent' => round(($k / 10) * 100),
                'label' => "{$k}x Kunjungan",
                'sub' => $isClaimed ? 'Tinggal '.max(0, 10 - $k).'x lagi: Voucher Rp 25k' : 'Mencapai Target 5x!',
                'target_badge' => $isClaimed ? "{$k}/10" : '5/5 SELESAI',
                'is_claimable' => ! $isClaimed,
                'reward_name' => '5 Halaman Warna',
                'claim_type' => 'regular',
            ];
        }

        // 1 to 4
        $isClaimed = ($this->hadiah_terklaim_count >= 1);

        return [
            'current' => $k,
            'target' => 5,
            'percent' => round(($k / 5) * 100),
            'label' => "{$k}x Kunjungan",
            'sub' => $isClaimed ? '1x lagi dapat Gratis 5 Hlm Warna' : 'Tercapai Kunjungan ke-3!',
            'target_badge' => $isClaimed ? "{$k}/5" : '3/3',
            'is_claimable' => ($k >= 3 && ! $isClaimed),
            'reward_name' => 'Jilid Lakban / Map',
            'claim_type' => 'starter',
        ];
    }
}
