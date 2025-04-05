<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Models\User;
use App\Services\CoinResetService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class ResetCoins extends Command
{
    protected $signature = 'coins:reset {entity}'; // Usage: php artisan coins:reset user OR php artisan coins:reset team

    protected $description = 'Reset coin credits for users or teams';

    protected CoinResetService $coinResetService;

    public function __construct(CoinResetService $coinResetService)
    {
        parent::__construct();
        $this->coinResetService = $coinResetService;
    }

    public function handle(): void
    {
        $entityType = $this->argument('entity');
        $model = $this->getModel($entityType);
        $interval = $entityType === 'user' ? 1 : 7;

        if (! $model) {
            $this->error('Invalid entity type. Use "user" or "team".');

            return;
        }

        $count = 0;
        $model->chunk(100, function ($entities) use (&$count, $interval) {
            foreach ($entities as $entity) {
                if ($this->coinResetService->resetCoins($entity, $interval)) {
                    $count++;
                }
            }
        });

        $this->info("{$count} {$entityType} credits have been reset.");
    }

    private function getModel($entityType): ?Builder
    {
        return match ($entityType) {
            'user' => User::query(),
            'team' => Team::query(),
            default => null,
        };
    }
}
