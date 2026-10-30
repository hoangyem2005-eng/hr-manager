<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaderPendingTasksAndFormValidationTest extends TestCase
{
    use RefreshDatabase;

    protected User $director;
    protected User $deptHead;
    protected User $employee;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['id' => User::ROLE_ADMIN], ['name' => 'Admin']);
        $deptHeadRole = Role::firstOrCreate(['id' => User::ROLE_DEPT_HEAD_BIZ], ['name' => 'Trưởng phòng Kinh doanh']);
        $empRole = Role::firstOrCreate(['id' => User::ROLE_EMPLOYEE], ['name' => 'Nhân viên']);

        $dept = Department::firstOrCreate(
            ['id' => 1],
            ['TENPHONG' => 'Phòng Kinh doanh', 'name' => 'Sales']
        );

        $this->director = User::factory()->create([
            'role_id' => User::ROLE_ADMIN,
            'department_id' => $dept->id,
            'is_active' => true,
        ]);

        $this->deptHead = User::factory()->create([
            'role_id' => User::ROLE_DEPT_HEAD_BIZ,
            'department_id' => $dept->id,
            'is_active' => true,
        ]);

        $this->employee = User::factory()->create([
            'role_id' => User::ROLE_EMPLOYEE,
            'department_id' => $dept->id,
            'is_active' => true,
        ]);
    }

    public function test_director_pending_column_only_includes_tasks_assigned_to_department_heads(): void
    {
        // 1. Task assigned to Dept Head by Director (Pending)
        $deptHeadTask = Task::create([
            'task_name' => 'Chiến lược kinh doanh Q4',
            'description' => 'Lập kế hoạch doanh số',
            'priority' => 'Cao',
            'assigned_by' => $this->director->id,
            'assigned_to' => $this->deptHead->id,
            'deadline' => now()->addDays(7),
            'status' => 'Chờ xử lý',
            'progress' => 0,
        ]);

        // 2. Task assigned to Employee by Dept Head (Pending)
        $employeeTask = Task::create([
            'task_name' => 'Báo cáo bán hàng tuần',
            'description' => 'Tổng hợp số liệu',
            'priority' => 'Trung bình',
            'assigned_by' => $this->deptHead->id,
            'assigned_to' => $this->employee->id,
            'deadline' => now()->addDays(3),
            'status' => 'Chờ xử lý',
            'progress' => 0,
        ]);

        $response = $this->actingAs($this->director)->get(route('admin.tasks'));

        $response->assertOk();

        // Get view data
        $cols = $response->viewData('cols');
        $pendingTasks = collect($cols['pending']['tasks']);

        // Assert pending column has the custom label
        $this->assertEquals('Chờ TP nhận việc', $cols['pending']['label']);

        // Assert pending column has the Dept Head task
        $this->assertTrue($pendingTasks->contains('id', $deptHeadTask->id));

        // Assert pending column DOES NOT have the regular Employee task
        $this->assertFalse($pendingTasks->contains('id', $employeeTask->id));
    }

    public function test_dept_head_can_accept_task_from_director(): void
    {
        $task = Task::create([
            'task_name' => 'Mở rộng thị trường miền Trung',
            'description' => 'Khảo sát và mở điểm bán',
            'priority' => 'Cao',
            'assigned_by' => $this->director->id,
            'assigned_to' => $this->deptHead->id,
            'deadline' => now()->addDays(14),
            'status' => 'Chờ xử lý',
            'progress' => 0,
        ]);

        $response = $this->actingAs($this->deptHead)
            ->post(route('employee.task.accept', $task->id));

        $response->assertSessionHasNoErrors();
        $this->assertEquals('Đang làm', $task->fresh()->status);

        // When director views tasks, it now appears in Doing column
        $directorResponse = $this->actingAs($this->director)->get(route('admin.tasks'));
        $cols = $directorResponse->viewData('cols');
        $doingTasks = collect($cols['doing']['tasks']);
        $pendingTasks = collect($cols['pending']['tasks']);

        $this->assertTrue($doingTasks->contains('id', $task->id));
        $this->assertFalse($pendingTasks->contains('id', $task->id));
    }

    public function test_manager_task_detail_view_shows_accept_and_decline_buttons_for_pending_task(): void
    {
        $task = Task::create([
            'task_name' => 'Rà soát hợp đồng đối tác',
            'description' => 'Kiểm tra pháp lý hợp đồng',
            'priority' => 'Trung bình',
            'assigned_by' => $this->director->id,
            'assigned_to' => $this->deptHead->id,
            'deadline' => now()->addDays(5),
            'status' => 'Chờ xử lý',
            'progress' => 0,
        ]);

        $response = $this->actingAs($this->deptHead)
            ->get(route('manager.tasks.show', $task->id));

        $response->assertOk()
            ->assertSee('Nhận việc')
            ->assertSee('Không nhận')
            ->assertSee('Công việc đang chờ bạn tiếp nhận');
    }
}
