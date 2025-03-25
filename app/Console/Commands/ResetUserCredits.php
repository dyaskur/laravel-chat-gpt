<?php

namespace App\Console\Commands;

use App\Models\UserCredit;
use Illuminate\Console\Command;

class ResetUserCredits extends Command
{
    protected $signature = 'credits:reset';

    protected $description = 'Reset user credits daily or weekly at 00:00 GMT';

    public function handle(): void
    {
        $count = 0;
        // todo: investigate whether use filter by last reset  and reset type, or keep this, which is more performance
        UserCredit::all()->each(function (UserCredit $user_credit) use (&$count) {
            if ($user_credit->shouldReset()) {
                $count++;
                $user_credit->resetCredits(); // Call the reset method
            }
        });

        $this->info($count.' user credits have been reset at 00:00 GMT.');
    }
}
