<?php

namespace Tests\Feature;

use App\Mail\TaskOverdueMail;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OverdueTaskEmailTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        $this->artisan('migrate:fresh')->run();
    }

    public function test_overdue_task_email_is_sent_to_assignee_once(): void
    {
        Mail::fake();

        $manager = User::factory()->create(['role_id' => User::ROLE_MANAGER, 'department_id' => 1]);
        $employee = User::factory()->create([
            'role_id' => User::ROLE_EMPLOYEE,
            'department_id' => 1,
            'email' => 'nhanvien@mobifone.vn',
        ]);

        $task = Task::create([
            'task_name' => 'Nop bao cao thang',
            'assigned_by' => $manager->id,
            'assigned_to' => $employee->id,
            'deadline' => now()->subDay()->toDateString(),
            'status' => 'In Progress',
            'progress' => 40,
        ]);

        $this->artisan('tasks:send-overdue-emails')
            ->expectsOutput('Sent 1 overdue task email(s).')
            ->assertExitCode(0);

        Mail::assertSent(TaskOverdueMail::class, function (TaskOverdueMail $mail) use ($employee, $task) {
            return $mail->hasTo($employee->email) && $mail->task->is($task);
        });

        $this->assertNotNull($task->fresh()->overdue_email_sent_at);
    }

    public function test_overdue_task_email_is_not_sent_again_after_marked_sent(): void
    {
        Mail::fake();

        $manager = User::factory()->create(['role_id' => User::ROLE_MANAGER, 'department_id' => 1]);
        $employee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 1]);

        Task::create([
            'task_name' => 'Da gui nhac qua han',
            'assigned_by' => $manager->id,
            'assigned_to' => $employee->id,
            'deadline' => now()->subDays(2)->toDateString(),
            'status' => 'In Progress',
            'progress' => 20,
            'overdue_email_sent_at' => now(),
        ]);

        $this->artisan('tasks:send-overdue-emails')
            ->expectsOutput('Sent 0 overdue task email(s).')
            ->assertExitCode(0);

        Mail::assertNothingSent();
    }
}
