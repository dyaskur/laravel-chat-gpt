<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

return function (Schedule $schedule) {
    $schedule->command('credits:reset')->dailyAt('00:00')->timezone('UTC');
};
