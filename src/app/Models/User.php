<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    public const STATUS_INACTIVE = 1;
    public const STATUS_ACTIVE = 2;
    public const STATUS_INACTIVE_LABEL = 'Inactive';
    public const STATUS_ACTIVE_LABEL = 'Active';

    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'first_name',
        'last_name',
        'profile',
        'status',
        'location',
        'provider',
        'provider_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getStatus()
    {
        return $this->getUserStatuses()[$this->status];
    }

    protected function getUserStatuses(): array
    {
        return [
            self::STATUS_INACTIVE => self::STATUS_INACTIVE_LABEL,
            self::STATUS_ACTIVE => self::STATUS_ACTIVE_LABEL,
        ];
    }
}
