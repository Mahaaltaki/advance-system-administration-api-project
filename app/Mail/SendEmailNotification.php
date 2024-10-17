<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendEmailNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $tasks;
    public $user;

    public function __construct($user, $tasks)
    {
        $this->user = $user;
        $this->tasks = $tasks;
    }

    public function build()
    {
        return $this->subject('التقرير اليومي للمهام')
                    ->view('emails.daily_task_report')
                    ->with([
                        'user' => $this->user,
                        'tasks' => $this->tasks,
                    ]);
    }}