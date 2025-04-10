<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Paddle\Billable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Billable, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'coin_balance',
        'last_coin_reset',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function coinTransactions(): HasMany
    {
        return $this->hasMany(UserCoinTransaction::class);
    }

    public function instegrations(): HasMany
    {
        return $this->hasMany(UserIntegration::class);
    }

    public function googleChatIntegration(): HasOne
    {
        return $this->hasOne(UserIntegration::class)->where('platform', 'google_chat');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class)
            ->withTimestamps();
    }
}
