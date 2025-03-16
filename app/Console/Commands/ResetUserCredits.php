<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserCredit;
use Carbon\Carbon;

class ResetUserCredits extends Command
{
    protected $signature = 'credits:reset';
    protected $description = 'Reset user credits daily or weekly at 00:00 GMT';

    public function handle(): void
    {
        $now = Carbon::now('GMT');

        UserCredit::all()->each(function ($userCredit) use ($now) {
            if ($userCredit->reset_type === 'daily' ||
                ($userCredit->reset_type === 'weekly' && $now->isSunday())) {

                $userCredit->resetCredits(); // Call the reset method
            }
        });

        $this->info('User credits have been reset at 00:00 GMT.');
    }
}
