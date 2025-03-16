<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCredit extends Model
{

    use HasFactory;

    protected $fillable = ['user_id', 'balance', 'reset_type', 'reset_day', 'last_reset'];

    public function user(): BelongsTo
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

    public function addCredits(int $amount, string $description = 'Manual Credit'): void
    {
        $this->increment('balance', $amount);

        CreditTransaction::create([
            'user_id' => $this->user_id,
            'amount' => $amount,
            'type' => 'added',
            'description' => $description
        ]);
    }

    public function useCredits(int $amount, string $description = 'Used Credit'): bool
    {
        if ($this->balance >= $amount) {
            $this->decrement('balance', $amount);

            CreditTransaction::create([
                'user_id' => $this->user_id,
                'amount' => -$amount,
                'type' => 'used',
                'description' => $description
            ]);

            return true;
        }

        return false;
    }

    public function resetCredits(): void
    {
        CreditTransaction::create([
            'user_id' => $this->user_id,
            'amount' => -$this->balance,
            'type' => 'reset',
            'description' => 'Credits reset to default'
        ]);

        $this->update([
            'balance' => 100,
            'last_reset' => Carbon::now('GMT')
        ]);
    }
}

