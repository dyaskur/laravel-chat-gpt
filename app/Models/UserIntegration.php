<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserIntegration extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'platform', 'external_id', 'external_email', 'default_model', 'metadata'];

    protected $casts = [
        'metadata' => 'array',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    //
}
