<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class TaskDeadlineSortingTest extends TestCase
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

    public function test_tasks_page_sorts_tasks_by_upcoming_deadlines_priority(): void
    {
        $department = Department::create(['id' => 1, 'TENPHONG' => 'Kỹ thuật', 'name' => 'ky-thuat']);

        $manager = User::factory()->create([
            'role_id' => User::ROLE_MANAGER,
            'department_id' => $department->id,
        ]);
        $employee = User::factory()->create([
            'role_id' => User::ROLE_EMPLOYEE,
            'department_id' => $department->id,
        ]);

        // Task 1: Due in 10 days
        $taskLater = Task::create([
            'task_name' => 'Viec han xa 10 ngay',
            'assigned_by' => $manager->id,
            'assigned_to' => $employee->id,
            'status' => 'Đang làm',
            'deadline' => Carbon::now()->addDays(10)->toDateString(),
        ]);
        $taskLater->assignees()->sync([$employee->id]);

        // Task 2: Overdue (yesterday)
        $taskOverdue = Task::create([
            'task_name' => 'Viec qua han',
            'assigned_by' => $manager->id,
            'assigned_to' => $employee->id,
            'status' => 'Quá hạn',
            'deadline' => Carbon::now()->subDays(1)->toDateString(),
        ]);
        $taskOverdue->assignees()->sync([$employee->id]);

        // Task 3: Due tomorrow (expiring soon)
        $taskSoon = Task::create([
            'task_name' => 'Viec sap het han ngay mai',
            'assigned_by' => $manager->id,
            'assigned_to' => $employee->id,
            'status' => 'Đang làm',
            'deadline' => Carbon::now()->addDays(1)->toDateString(),
        ]);
        $taskSoon->assignees()->sync([$employee->id]);

        // Task 4: Already completed
        $taskDone = Task::create([
            'task_name' => 'Viec da hoan thanh',
            'assigned_by' => $manager->id,
            'assigned_to' => $employee->id,
            'status' => 'Hoàn thành',
            'deadline' => Carbon::now()->subDays(2)->toDateString(),
        ]);
        $taskDone->assignees()->sync([$employee->id]);

        $response = $this->actingAs($manager)->get('/dashboard/tasks?view=list');
        $response->assertOk();

        $mappedTasksList = $response->viewData('mappedTasksList');
        $taskIds = array_column($mappedTasksList, 'id');

        // Expected order:
        // 1. $taskOverdue (earliest non-completed deadline, yesterday)
        // 2. $taskSoon (tomorrow)
        // 3. $taskLater (in 10 days)
        // 4. $taskDone (completed tasks at bottom)
        $this->assertEquals([$taskOverdue->id, $taskSoon->id, $taskLater->id, $taskDone->id], $taskIds);
    }

    public function test_manager_dashboard_sorts_assigned_tasks_by_deadline_urgency(): void
    {
        $department = Department::create(['id' => 1, 'TENPHONG' => 'Kinh doanh', 'name' => 'kinh-doanh']);

        $manager = User::factory()->create([
            'role_id' => User::ROLE_DEPT_HEAD_BIZ,
            'department_id' => $department->id,
        ]);
        $employee = User::factory()->create([
            'role_id' => User::ROLE_EMPLOYEE,
            'department_id' => $department->id,
        ]);

        $taskLater = Task::create([
            'task_name' => 'Bao cao thang sau',
            'assigned_by' => $manager->id,
            'assigned_to' => $employee->id,
            'status' => 'Đang làm',
            'deadline' => Carbon::now()->addDays(20)->toDateString(),
        ]);

        $taskUrgent = Task::create([
            'task_name' => 'Bao cao hom nay',
            'assigned_by' => $manager->id,
            'assigned_to' => $employee->id,
            'status' => 'Đang làm',
            'deadline' => Carbon::now()->toDateString(),
        ]);

        $response = $this->actingAs($manager)->get('/manager/dashboard');
        $response->assertOk();

        $myAssignedTasks = $response->viewData('myAssignedTasks')->toArray();
        $taskIds = array_column($myAssignedTasks, 'id');

        $this->assertEquals([$taskUrgent->id, $taskLater->id], $taskIds);
    }

    public function test_director_dashboard_sorts_recent_tasks_by_deadline_urgency(): void
    {
        $department = Department::create(['id' => 1, 'TENPHONG' => 'Ban Giám Đốc', 'name' => 'ban-giam-doc']);

        $director = User::factory()->create([
            'role_id' => User::ROLE_ADMIN,
            'department_id' => $department->id,
        ]);
        $employee = User::factory()->create([
            'role_id' => User::ROLE_EMPLOYEE,
            'department_id' => $department->id,
        ]);

        $taskLater = Task::create([
            'task_name' => 'Du an nam sau',
            'assigned_by' => $director->id,
            'assigned_to' => $employee->id,
            'status' => 'Đang làm',
            'deadline' => Carbon::now()->addDays(30)->toDateString(),
        ]);

        $taskUrgent = Task::create([
            'task_name' => 'Nhiem vu gap tuan nay',
            'assigned_by' => $director->id,
            'assigned_to' => $employee->id,
            'status' => 'Đang làm',
            'deadline' => Carbon::now()->addDays(2)->toDateString(),
        ]);

        $response = $this->actingAs($director)->get('/admin/dashboard');
        $response->assertOk();

        $recentTasks = $response->viewData('recentTasks')->toArray();
        // Check IDs (formatted as WH-xxx)
        $this->assertSame('WH-' . str_pad($taskUrgent->id, 3, '0', STR_PAD_LEFT), $recentTasks[0]['id']);
        $this->assertSame('WH-' . str_pad($taskLater->id, 3, '0', STR_PAD_LEFT), $recentTasks[1]['id']);
    }
}
