<?php

namespace App\Mail;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskOverdueMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Task $task)
    {
    }

    public function build()
    {
        $code = 'WH-' . str_pad((string) $this->task->id, 3, '0', STR_PAD_LEFT);

        return $this->subject('[MobiFone HR] Cong viec qua han ' . $code)
            ->view('emails.tasks.overdue')
            ->with([
                'task' => $this->task,
                'code' => $code,
            ]);
    }
}
