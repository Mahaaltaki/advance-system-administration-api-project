<?php

namespace App\Console;

use App\Models\User;
use App\Jobs\SendDailyTaskReport;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // تشغيل المهمة كل يوم عند الساعة 8 صباحاً
        $schedule->call(function () {
            // اجلب كل المستخدمين
            $users = User::all();

            // إرسال تقرير يومي لكل مستخدم
            foreach ($users as $user) {
                SendDailyTaskReport::dispatch($user);
            }
        })->dailyAt('08:00'); 
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
