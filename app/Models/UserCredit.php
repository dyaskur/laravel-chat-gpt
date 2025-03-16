<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserCredit extends Model
{

    use HasFactory;

    protected $fillable = ['user_id', 'credits_available', 'reset_type', 'reset_day', 'last_reset'];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Determines if the credit should be reset based on reset_type.
     */
    public function shouldReset(): bool
    {
        $now = Carbon::now('GMT');
        if (!$this->last_reset) {
            return true;
        }

        if ($this->reset_type === 'daily') {
            return $now->diffInDays($this->last_reset) >= 1;
        }

        if ($this->reset_type === 'weekly' && $this->reset_day) {
            return $now->format('l') === $this->reset_day && $now->diffInWeeks($this->last_reset) >= 1;
        }

        return false;
    }
}

