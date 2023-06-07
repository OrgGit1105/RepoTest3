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
        //
      'App\Console\Commands\GetDataMailCommand',
      'App\Console\Commands\SendDigitacoFileCommand'
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
      $schedule->command('GetMail:import')->monthlyOn(3, '00:00')->withoutOverlapping();
      $schedule->command('DigitacoFile:sendFile')->monthlyOn(3, '03:00')->withoutOverlapping();
//      $schedule->command('GetMail:import')->dailyAt('16:00')->withoutOverlapping();
//      $schedule->command('DigitacoFile:sendFile')->dailyAt('16:30')->withoutOverlapping();
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
