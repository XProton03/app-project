<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('followup:check')->hourly(); // Bisa diganti daily()
    }
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
    }
}
