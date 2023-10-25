<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const DEMO_ID = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'id',
        'brand_name'
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

    /**
     * @return HasMany
     */
    public function telegrams(): HasMany
    {
        return $this->hasMany(TgUser::class);
    }

    /**
     * @return HasMany
     */
    public function whatsApps(): HasMany
    {
        return $this->hasMany(WaUser::class);
    }

    /**
     * @return HasMany
     */
    public function messengers(): HasMany
    {
        return $this->hasMany(MeUser::class);
    }

    /**
     * @return HasMany
     */
    public function slacks(): HasMany
    {
        return $this->hasMany(SlUser::class);
    }

    /**
     * @return HasMany
     */
    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class);
    }

    /**
     * @return HasMany
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class)
            ->orderBy('id', 'desc');
    }
}
