<?php

namespace App\Console\Commands;

use App\Mail\TaskOverdueMail;
use App\Models\Notification;
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

        Task::with(['assignee', 'assignees'])
            ->whereNotNull('deadline')
            ->whereDate('deadline', '<', now()->toDateString())
            ->whereNull('overdue_email_sent_at')
            ->whereNotIn('status', $this->completedStatuses())
            ->where(function ($query) {
                $query->whereNull('progress')->orWhere('progress', '<', 100);
            })
            ->chunkById(100, function ($tasks) use (&$sent, &$failed) {
                foreach ($tasks as $task) {
                    $recipients = $task->assignees->isNotEmpty()
                        ? $task->assignees
                        : collect([$task->assignee])->filter();
                    $recipients = $recipients
                        ->filter(fn ($user) => filled($user->email))
                        ->unique('id')
                        ->values();

                    if ($recipients->isEmpty()) {
                        continue;
                    }

                    try {
                        foreach ($recipients as $recipient) {
                            Mail::to($recipient->email)->send(new TaskOverdueMail($task));

                            Notification::firstOrCreate(
                                [
                                    'user_id' => $recipient->id,
                                    'task_id' => $task->id,
                                    'title' => 'Công việc đã quá hạn',
                                ],
                                [
                                    'message' => 'WH-' . str_pad((string) $task->id, 3, '0', STR_PAD_LEFT)
                                        . ': ' . $task->task_name
                                        . ' - Đã quá hạn từ: ' . optional($task->deadline)->format('d/m/Y'),
                                    'is_read' => false,
                                ]
                            );

                            $sent++;
                        }

                        $task->forceFill([
                            'overdue_email_sent_at' => now(),
                        ])->save();
                    } catch (Throwable $exception) {
                        $failed++;

                        Log::warning('Failed to send overdue task email.', [
                            'task_id' => $task->id,
                            'assignee_ids' => $recipients->pluck('id')->all(),
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
            'Hoàn thành',
            'HoÃ n thÃ nh',
            'HoÃƒÂ n thÃƒÂ nh',
        ];
    }
}
