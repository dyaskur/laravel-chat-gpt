<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Paddle\Billable;

class GoogleChatSpace extends Model
{
    use Billable, HasFactory;

    protected $fillable = ['name', 'display_name', 'space_url', 'is_thread', 'save_history', 'metadata'];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function users()
    {
        return $this->belongsToMany(UserIntegration::class, 'google_chat_spaces_users', 'google_chat_space_id', 'user_external_id', 'id', 'external_id')
            ->withTimestamps();
    }
}
