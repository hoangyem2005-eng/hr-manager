<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;

class CheckTaskAcceptanceTimeout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:check-acceptance-timeout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra và cập nhật trạng thái các công việc Chờ xử lý bị hết hạn chờ nhận việc (2h/4h sau 17h)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $pendingTasks = Task::whereIn('status', ['Chờ xử lý', 'Todo'])->get();
        $updatedCount = 0;

        foreach ($pendingTasks as $task) {
            if ($task->checkAndUpdateAcceptanceTimeout()) {
                $updatedCount++;
            }
        }

        $this->info("Đã cập nhật {$updatedCount} công việc hết hạn chờ nhận việc.");

        return 0;
    }
}
