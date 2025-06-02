<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CheckLowQuantityItems::class,
        Commands\SyncUserRoles::class,
        Commands\TestNotification::class,
        Commands\TestLowQuantityNotification::class,
        Commands\CreateTestInventory::class,
        Commands\CheckUserLowItems::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Check for low quantity items daily at 8:00 AM
        $schedule->command('inventory:check-low-quantity')->dailyAt('08:00');
        
        // Check for user low items daily at 9:00 AM
        $schedule->command('app:check-user-low-items')->dailyAt('09:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
} 