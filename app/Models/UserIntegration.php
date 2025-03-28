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

    public function googleChatSpaces()
    {
        return $this->belongsToMany(GoogleChatSpace::class, 'google_chat_spaces_users', 'user_external_id', 'google_chat_space_id', 'external_id', 'id')
            ->withTimestamps();
    }
}
