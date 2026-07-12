<?php

namespace App\Console\Commands;

use App\Mail\TaskOverdueMail;
use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendOverdueTaskEmails extends Command
{
    protected $signature = 'tasks:send-overdue-emails';

    protected $description = 'Send overdue task emails to assignees once per task.';

    public function handle()
    {
        $sent = 0;
        $failed = 0;

        Task::with('assignee')
            ->whereNotNull('deadline')
            ->whereDate('deadline', '<', now()->toDateString())
            ->whereNull('overdue_email_sent_at')
            ->whereNotIn('status', $this->completedStatuses())
            ->where(function ($query) {
                $query->whereNull('progress')->orWhere('progress', '<', 100);
            })
            ->whereHas('assignee', function ($query) {
                $query->whereNotNull('email')->where('email', '!=', '');
            })
            ->chunkById(100, function ($tasks) use (&$sent, &$failed) {
                foreach ($tasks as $task) {
                    try {
                        Mail::to($task->assignee->email)->send(new TaskOverdueMail($task));

                        $task->forceFill([
                            'overdue_email_sent_at' => now(),
                        ])->save();

                        $sent++;
                    } catch (Throwable $exception) {
                        $failed++;

                        Log::warning('Failed to send overdue task email.', [
                            'task_id' => $task->id,
                            'assignee_id' => $task->assigned_to,
                            'error' => $exception->getMessage(),
                        ]);
                    }
                }
            });

        $this->info("Sent {$sent} overdue task email(s).");

        if ($failed > 0) {
            $this->warn("Failed to send {$failed} overdue task email(s).");
        }

        return self::SUCCESS;
    }

    private function completedStatuses(): array
    {
        return [
            'Done',
            'Hoàn thành',
            'HoÃ n thÃ nh',
            'HoÃƒÂ n thÃƒÂ nh',
        ];
    }
}
