<?php

namespace App\Console;

use App\Console\Commands\ExecuteCustomCodeCommand;
use App\Console\Commands\SyncPackiyoSubscriptionPlans;
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
        Commands\RotateCompressLogs::class,
        Commands\RecalculateOrderReadyToShip::class,
        SyncPackiyoSubscriptionPlans::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('log-rotate-compress')->daily();

        $schedule->command('recalculate-ready-to-ship')->everyMinute()->withoutOverlapping();

        $schedule->command('sync:batch-orders')->everyThirtyMinutes()->withoutOverlapping();

        $schedule->command('get-carriers')->daily();

        $schedule->command('order-priority-updater')->dailyAt('02:00');

        $schedule->command(ExecuteCustomCodeCommand::class)->everyMinute()->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
