<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

/**
 * @property string $reset_type
 */
class UserCredit extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'balance', 'reset_type', 'reset_day', 'last_reset'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function addCredits(int $amount, string $description = 'Manual Credit'): void
    {
        $this->increment('balance', $amount);

        CreditTransaction::create([
            'user_id' => $this->user_id,
            'amount' => $amount,
            'type' => 'added',
            'description' => $description,
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
                'description' => $description,
            ]);

            return true;
        }

        return false;
    }

    public function shouldReset($now = null): bool
    {
        if (! $this->last_reset) {
            return true;
        }

        if (! $now) {
            $now = Carbon::now('GMT');
        }

        // add 8 minutes to compensate for delay
        $now->addMinutes(8);

        if ($this->reset_type === 'daily') {
            return $now->diffInDays($this->last_reset) >= 1;
        }

        if ($this->reset_type === 'weekly') {
            return $now->diffInWeeks($this->last_reset) >= 1;
        }

        return false;
    }

    public function resetCredits(): void
    {
        CreditTransaction::create([
            'user_id' => $this->user_id,
            'amount' => -$this->balance,
            'type' => 'reset',
            'description' => 'Credits reset to default',
        ]);

        $new_balance = 100;
        $this->update([
            'balance' => $new_balance,
            'last_reset' => Carbon::now('GMT'),
        ]);
        Cache::set('credits_'.$this->user_id, $new_balance);

        CreditTransaction::create([
            'user_id' => $this->user_id,
            'amount' => $new_balance,
            'type' => 'added',
            'description' => 'Credits reset to default',
        ]);
    }
}
