<?php

namespace App\Jobs;

use App\Mail\DailyTaskReport;
use App\Models\User;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendDailyTaskReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle()
    {
        // get the tasks by this user
        $tasks = Task::where('assigned_to', $this->user->id)
                      ->whereDate('due_date', today())
                      ->get();

        // send email
        Mail::to($this->user->email)->send(new DailyTaskReport($this->user, $tasks));
    }
}