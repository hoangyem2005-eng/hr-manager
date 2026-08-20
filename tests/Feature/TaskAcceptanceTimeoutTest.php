<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class TaskAcceptanceTimeoutTest extends TestCase
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

    public function test_task_assigned_before_17h_has_2_hour_acceptance_deadline(): void
    {
        // Assigned at 10:00 AM
        Carbon::setTestNow(Carbon::parse('2026-08-20 10:00:00'));

        $manager = User::factory()->create(['role_id' => User::ROLE_MANAGER, 'department_id' => 1]);
        $employee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 1]);

        $task = Task::create([
            'task_name' => 'Cong viec sang',
            'assigned_by' => $manager->id,
            'assigned_to' => $employee->id,
            'status' => 'Chờ xử lý',
            'deadline' => now()->addDays(2)->toDateString(),
        ]);

        $this->assertNotNull($task->acceptance_deadline);
        $this->assertSame('2026-08-20 12:00:00', $task->acceptance_deadline->format('Y-m-d H:i:s'));
        $this->assertFalse($task->is_acceptance_expired);

        // Advance time to 1.5 hours later (11:30) -> should NOT be expired
        Carbon::setTestNow(Carbon::parse('2026-08-20 11:30:00'));
        $this->assertFalse($task->fresh()->is_acceptance_expired);

        // Advance time past 2 hours (12:01) -> SHOULD be expired
        Carbon::setTestNow(Carbon::parse('2026-08-20 12:01:00'));
        $this->assertTrue($task->fresh()->is_acceptance_expired);
    }

    public function test_task_assigned_after_17h_has_4_hour_acceptance_deadline(): void
    {
        // Assigned at 17:30 (5:30 PM)
        Carbon::setTestNow(Carbon::parse('2026-08-20 17:30:00'));

        $manager = User::factory()->create(['role_id' => User::ROLE_MANAGER, 'department_id' => 1]);
        $employee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 1]);

        $task = Task::create([
            'task_name' => 'Cong viec chieu toi',
            'assigned_by' => $manager->id,
            'assigned_to' => $employee->id,
            'status' => 'Chờ xử lý',
            'deadline' => now()->addDays(2)->toDateString(),
        ]);

        $this->assertNotNull($task->acceptance_deadline);
        // Should be 17:30 + 4h = 21:30
        $this->assertSame('2026-08-20 21:30:00', $task->acceptance_deadline->format('Y-m-d H:i:s'));
        $this->assertFalse($task->is_acceptance_expired);

        // At 3 hours later (20:30) -> should NOT be expired
        Carbon::setTestNow(Carbon::parse('2026-08-20 20:30:00'));
        $this->assertFalse($task->fresh()->is_acceptance_expired);

        // Past 4 hours (21:31) -> SHOULD be expired
        Carbon::setTestNow(Carbon::parse('2026-08-20 21:31:00'));
        $this->assertTrue($task->fresh()->is_acceptance_expired);
    }

    public function test_artisan_command_updates_expired_acceptance_tasks_to_overdue(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-20 10:00:00'));

        $manager = User::factory()->create(['role_id' => User::ROLE_MANAGER, 'department_id' => 1]);
        $employee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 1]);

        $task = Task::create([
            'task_name' => 'Cong viec qua han nhan',
            'assigned_by' => $manager->id,
            'assigned_to' => $employee->id,
            'status' => 'Chờ xử lý',
            'deadline' => now()->addDays(2)->toDateString(),
        ]);

        // Advance 3 hours later
        Carbon::setTestNow(Carbon::parse('2026-08-20 13:00:00'));

        $this->artisan('tasks:check-acceptance-timeout')
            ->expectsOutput('Đã cập nhật 1 công việc hết hạn chờ nhận việc.')
            ->assertExitCode(0);

        $this->assertSame('Quá hạn', $task->fresh()->status);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $employee->id,
            'task_id' => $task->id,
            'title' => 'Hết hạn thời gian chờ nhận việc',
        ]);
    }

    public function test_task_accepted_to_doing_is_not_expired_by_acceptance_timeout(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-20 10:00:00'));

        $manager = User::factory()->create(['role_id' => User::ROLE_MANAGER, 'department_id' => 1]);
        $employee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 1]);

        $task = Task::create([
            'task_name' => 'Cong viec duoc nhan',
            'assigned_by' => $manager->id,
            'assigned_to' => $employee->id,
            'status' => 'Chờ xử lý',
            'deadline' => now()->addDays(2)->toDateString(),
        ]);

        // Employee starts working at 10:30 (updates status to Đang làm)
        Carbon::setTestNow(Carbon::parse('2026-08-20 10:30:00'));
        $task->update(['status' => 'Đang làm', 'progress' => 10]);

        // Advance 5 hours later (15:30)
        Carbon::setTestNow(Carbon::parse('2026-08-20 15:30:00'));

        $this->assertFalse($task->fresh()->is_acceptance_expired);

        $this->artisan('tasks:check-acceptance-timeout')
            ->expectsOutput('Đã cập nhật 0 công việc hết hạn chờ nhận việc.')
            ->assertExitCode(0);

        $this->assertSame('Đang làm', $task->fresh()->status);
    }
}
