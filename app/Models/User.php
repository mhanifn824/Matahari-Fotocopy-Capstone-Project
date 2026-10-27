<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'user_id';

    public $incrementing = true;

    protected $fillable = [
        'nama',
        'email',
        'password',
        'nomor_telepon',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function pelanggan(): HasOne
    {
        return $this->hasOne(Pelanggan::class, 'user_id', 'user_id');
    }

    public function adminKasir(): HasOne
    {
        return $this->hasOne(AdminKasir::class, 'user_id', 'user_id');
    }

    /**
     * Check if user is an admin or cashier/staff.
     */
    public function isAdminOrStaff(): bool
    {
        return in_array(strtolower((string) $this->role), ['admin', 'kasir', 'owner'], true);
    }

    /**
     * Check if user is a customer/pelanggan.
     */
    public function isCustomer(): bool
    {
        return in_array(strtolower((string) $this->role), ['user', 'pelanggan'], true);
    }

    /**
     * Return human-friendly role label.
     */
    public function getRoleLabelAttribute(): string
    {
        return match (strtolower((string) $this->role)) {
            'admin' => 'Administrator',
            'kasir' => 'Kasir / Operator',
            'owner' => 'Owner',
            'pelanggan', 'user' => 'Pelanggan / Member',
            default => ucfirst((string) $this->role),
        };
    }
}
