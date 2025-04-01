<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Paddle\Billable;

class Team extends Model
{
    use Billable, HasFactory;

    protected $fillable = ['name', 'integration_id', 'integration_name', 'integration_metadata', 'description', 'coin_balance', 'last_coin_reset'];

    protected $casts = [
        'integration_metadata' => 'array',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps();
    }
}
