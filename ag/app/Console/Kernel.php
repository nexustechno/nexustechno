<?php

namespace App\Console;

use App\Console\Commands\UpdateMatchWinner;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
		Commands\DemoCron::class,
        UpdateMatchWinner::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
		$schedule->command('match:update-winner-result')->everyMinute();

        $seconds1 = 30;
        $schedule->call(function () use ($seconds1) {
            $dt = Carbon::now();
            $x=60/$seconds1;
            do{
                Artisan::call("casino:update-results");

                time_sleep_until($dt->addSeconds($seconds1)->timestamp);

            } while($x-- > 0);

        })->everyMinute();
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
