<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Task;
use App\Models\User;
use Tests\TestCase;

class ManagerEditTaskTest extends TestCase
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

    public function test_manager_can_edit_doing_task_assigned_to_department_employee(): void
    {
        $department = Department::create(['id' => 1, 'TENPHONG' => 'Kinh doanh', 'name' => 'kinh-doanh']);

        $manager = User::factory()->create([
            'role_id' => User::ROLE_MANAGER,
            'department_id' => $department->id,
        ]);
        $employee = User::factory()->create([
            'role_id' => User::ROLE_EMPLOYEE,
            'department_id' => $department->id,
        ]);

        $task = Task::create([
            'task_name' => 'Nhan vien dang lam viec nay',
            'description' => 'Mota ban dau',
            'assigned_by' => $manager->id,
            'assigned_to' => $employee->id,
            'status' => 'Đang làm',
            'progress' => 30,
            'deadline' => now()->addDays(3)->toDateString(),
            'priority' => 'Trung bình',
        ]);
        $task->assignees()->sync([$employee->id]);

        $this->actingAs($manager)->post("/dashboard/tasks/{$task->id}/update", [
            'task_name' => 'Ten cong viec da duoc manager chinh sua',
            'description' => 'Mota moi do truong phong cap nhat',
            'deadline' => now()->addDays(5)->toDateString(),
            'assigned_to' => [$employee->id],
            'priority' => 'Cao',
            'status' => 'Đang làm',
            'progress' => 60,
        ])->assertRedirect();

        $updatedTask = $task->fresh();
        $this->assertSame('Ten cong viec da duoc manager chinh sua', $updatedTask->task_name);
        $this->assertSame('Mota moi do truong phong cap nhat', $updatedTask->description);
        $this->assertSame('Cao', $updatedTask->priority);
        $this->assertSame(60, $updatedTask->progress);
        $this->assertSame('Đang làm', $updatedTask->status);
    }

    public function test_director_can_edit_any_doing_task(): void
    {
        $director = User::factory()->create(['role_id' => User::ROLE_ADMIN, 'department_id' => 1]);
        $employee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 2]);

        $task = Task::create([
            'task_name' => 'Task phong khac dang lam',
            'assigned_by' => 999,
            'assigned_to' => $employee->id,
            'status' => 'Đang làm',
            'progress' => 40,
            'deadline' => now()->addDays(2)->toDateString(),
        ]);
        $task->assignees()->sync([$employee->id]);

        $this->actingAs($director)->post("/dashboard/tasks/{$task->id}/update", [
            'task_name' => 'Giam doc da sua task nay',
            'description' => 'Giam doc dieu chinh tien do va uu tien',
            'deadline' => now()->addDays(10)->toDateString(),
            'assigned_to' => [$employee->id],
            'priority' => 'Cao',
            'status' => 'Đang làm',
            'progress' => 75,
        ])->assertRedirect();

        $updatedTask = $task->fresh();
        $this->assertSame('Giam doc da sua task nay', $updatedTask->task_name);
        $this->assertSame(75, $updatedTask->progress);
    }
}
