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
}
