<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamCoinTransaction extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'amount', 'type', 'description'];

    public $timestamps = false; // Disable timestamps

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
