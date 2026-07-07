<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Document;
use App\Models\Notification;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RoleTaskCreationTest extends TestCase
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

    public function test_director_can_assign_a_task_to_any_user(): void
    {
        $director = User::factory()->create(['role_id' => User::ROLE_ADMIN, 'department_id' => 1]);
        $employee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 2]);

        $this->actingAs($director)->post(route('dashboard.tasks.save'), [
            'task_name' => 'Kiem tra tien do toan cong ty',
            'assigned_to' => $employee->id,
            'deadline' => now()->addWeek()->toDateString(),
            'description' => 'Bao cao tong hop theo phong ban',
            'status' => 'Chờ xử lý',
        ])->assertRedirect(route('dashboard.tasks'));

        $this->assertDatabaseHas('tasks', [
            'task_name' => 'Kiem tra tien do toan cong ty',
            'assigned_by' => $director->id,
            'assigned_to' => $employee->id,
        ]);
    }

    public function test_manager_can_only_assign_tasks_inside_their_department(): void
    {
        Department::create(['id' => 1, 'TENPHONG' => 'Nhan su', 'name' => 'HR']);
        Department::create(['id' => 2, 'TENPHONG' => 'Dao tao', 'name' => 'Training']);

        $manager = User::factory()->create(['role_id' => User::ROLE_MANAGER, 'department_id' => 1]);
        $outsideEmployee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 2]);

        $this->actingAs($manager)->from(route('dashboard.tasks'))->post(route('dashboard.tasks.save'), [
            'task_name' => 'Giao viec sai phong',
            'assigned_to' => $outsideEmployee->id,
            'deadline' => now()->addWeek()->toDateString(),
            'status' => 'Chờ xử lý',
        ])->assertRedirect(route('dashboard.tasks'));

        $this->assertDatabaseMissing('tasks', [
            'task_name' => 'Giao viec sai phong',
        ]);
    }

    public function test_employee_task_submission_is_for_themselves_only(): void
    {
        $employee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 1]);
        $otherEmployee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 1]);

        $this->actingAs($employee)->post(route('dashboard.tasks.save'), [
            'task_name' => 'De xuat viec can ho tro',
            'assigned_to' => $otherEmployee->id,
            'deadline' => now()->addDays(3)->toDateString(),
            'description' => 'Can quan ly xem xet',
            'status' => 'Hoàn thành',
        ])->assertRedirect(route('dashboard.tasks'));

        $task = Task::where('task_name', 'De xuat viec can ho tro')->firstOrFail();

        $this->assertSame($employee->id, $task->assigned_by);
        $this->assertSame($employee->id, $task->assigned_to);
        $this->assertSame('Chờ xử lý', $task->status);
    }

    public function test_task_page_renders_director_experience(): void
    {
        $director = User::factory()->create(['role_id' => User::ROLE_ADMIN, 'department_id' => 1]);

        $this->actingAs($director)
            ->get(route('dashboard.tasks'))
            ->assertOk()
            ->assertSee('Điều phối mục tiêu cấp công ty')
            ->assertSee('Giao mục tiêu');
    }

    public function test_task_page_renders_manager_experience(): void
    {
        Department::create(['id' => 1, 'TENPHONG' => 'Nhan su', 'name' => 'HR']);
        $manager = User::factory()->create(['role_id' => User::ROLE_MANAGER, 'department_id' => 1]);

        $this->actingAs($manager)
            ->get(route('dashboard.tasks'))
            ->assertOk()
            ->assertSee('Giao việc trong phòng')
            ->assertSee('Giao việc cho đội');
    }

    public function test_task_page_renders_employee_experience(): void
    {
        $employee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 1]);

        $this->actingAs($employee)
            ->get(route('dashboard.tasks'))
            ->assertOk()
            ->assertSee('Việc của tôi')
            ->assertSee('Gửi đề xuất việc');
    }

    public function test_director_dashboard_renders_new_command_interface(): void
    {
        $director = User::factory()->create(['role_id' => User::ROLE_ADMIN, 'department_id' => 1]);

        $this->actingAs($director)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('MobiFone HR operations')
            ->assertSee('Trung tâm điều hành');
    }

    public function test_manager_dashboard_renders_new_dispatch_interface(): void
    {
        Department::create(['id' => 1, 'TENPHONG' => 'Nhan su', 'name' => 'HR']);
        $manager = User::factory()->create(['role_id' => User::ROLE_MANAGER, 'department_id' => 1]);

        $this->actingAs($manager)
            ->get(route('manager.dashboard'))
            ->assertOk()
            ->assertSee('Department dispatch')
            ->assertSee('Team Dispatch');
    }

    public function test_manager_sees_director_assigned_task_and_can_delegate_it_to_department_employee(): void
    {
        Department::create(['id' => 1, 'TENPHONG' => 'Phap che', 'name' => 'Legal']);

        $director = User::factory()->create(['role_id' => User::ROLE_ADMIN, 'department_id' => 1]);
        $manager = User::factory()->create([
            'name' => 'Khang',
            'role_id' => User::ROLE_MANAGER,
            'department_id' => 1,
        ]);
        $employee = User::factory()->create([
            'name' => 'Nhan vien Phap che',
            'role_id' => User::ROLE_EMPLOYEE,
            'department_id' => 1,
        ]);

        $task = Task::create([
            'task_name' => 'Ra soat hop dong phap ly',
            'assigned_by' => $director->id,
            'assigned_to' => $manager->id,
            'deadline' => now()->addWeek()->toDateString(),
            'status' => 'Chờ xử lý',
            'progress' => 0,
        ]);

        $this->actingAs($manager)
            ->get(route('manager.dashboard'))
            ->assertOk()
            ->assertSee('Việc cấp trên giao')
            ->assertSee('Ra soat hop dong phap ly')
            ->assertSee('Phân công');

        $this->actingAs($manager)->patch(route('manager.task.delegate', $task), [
            'assigned_to' => $employee->id,
        ])->assertRedirect(route('manager.dashboard'));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'assigned_by' => $manager->id,
            'assigned_to' => $employee->id,
            'status' => 'Chờ xử lý',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $employee->id,
            'task_id' => $task->id,
            'title' => 'Bạn vừa được phân công công việc mới',
            'is_read' => false,
        ]);

        $this->assertSame(1, Notification::where('task_id', $task->id)
            ->where('user_id', $employee->id)
            ->count());
    }

    public function test_employee_dashboard_renders_new_personal_workbench(): void
    {
        $employee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 1]);

        $this->actingAs($employee)
            ->get(route('employee.dashboard'))
            ->assertOk()
            ->assertSee('Personal Execution')
            ->assertSee('Danh sách công việc cá nhân');
    }

    public function test_manager_can_update_member_information_from_actions(): void
    {
        Department::create(['id' => 1, 'TENPHONG' => 'Nhan su', 'name' => 'HR']);
        Department::create(['id' => 2, 'TENPHONG' => 'Dao tao', 'name' => 'Training']);

        $manager = User::factory()->create(['role_id' => User::ROLE_MANAGER, 'department_id' => 1]);
        $member = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 1]);

        $this->actingAs($manager)->patch(route('dashboard.members.update', $member), [
            'name' => 'Nhan vien da sua',
            'email' => 'member.updated@mobifone.vn',
            'department_id' => 2,
        ])->assertRedirect(route('dashboard.members'));

        $this->assertDatabaseHas('users', [
            'id' => $member->id,
            'name' => 'Nhan vien da sua',
            'email' => 'member.updated@mobifone.vn',
            'department_id' => 2,
        ]);
    }

    public function test_manager_can_update_member_role_from_actions(): void
    {
        Role::create(['id' => User::ROLE_MANAGER, 'name' => 'Quản lý']);
        Role::create(['id' => User::ROLE_EMPLOYEE, 'name' => 'Nhân viên']);

        $manager = User::factory()->create(['role_id' => User::ROLE_MANAGER, 'department_id' => 1]);
        $member = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 1]);

        $this->actingAs($manager)->patch(route('dashboard.members.role', $member), [
            'role_id' => User::ROLE_MANAGER,
        ])->assertRedirect(route('dashboard.members'));

        $this->assertDatabaseHas('users', [
            'id' => $member->id,
            'role_id' => User::ROLE_MANAGER,
        ]);
    }

    public function test_manager_can_toggle_member_status_from_actions(): void
    {
        $manager = User::factory()->create(['role_id' => User::ROLE_MANAGER, 'department_id' => 1]);
        $member = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 1, 'is_active' => true]);

        $this->actingAs($manager)
            ->patch(route('dashboard.members.status', $member))
            ->assertRedirect(route('dashboard.members'));

        $this->assertDatabaseHas('users', [
            'id' => $member->id,
            'is_active' => false,
        ]);
    }

    public function test_employee_uploaded_file_reaches_director_only_after_manager_forwarding(): void
    {
        Storage::fake('public');

        Department::create(['id' => 1, 'TENPHONG' => 'Nhan su', 'name' => 'HR']);

        $director = User::factory()->create(['role_id' => User::ROLE_ADMIN, 'department_id' => 1]);
        $manager = User::factory()->create(['role_id' => User::ROLE_MANAGER, 'department_id' => 1]);
        $employee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 1]);

        $task = Task::create([
            'task_name' => 'Nop bao cao thu viec',
            'assigned_by' => $manager->id,
            'assigned_to' => $employee->id,
            'deadline' => now()->addWeek()->toDateString(),
            'status' => 'Chá» xá»­ lÃ½',
            'progress' => 0,
        ]);

        $this->actingAs($employee)->post(route('employee.task.upload', $task), [
            'attachments' => [
                UploadedFile::fake()->create('bao-cao.pdf', 120, 'application/pdf'),
            ],
        ])->assertRedirect(route('employee.task.detail', $task));

        $document = Document::where('task_id', $task->id)->firstOrFail();
        $this->assertSame(Document::STATUS_MANAGER_REVIEW, $document->review_status);

        $this->actingAs($manager)
            ->get(route('congviec.chitiet', $task))
            ->assertOk()
            ->assertSee('bao-cao.pdf')
            ->assertSee('Gửi Giám đốc');

        $this->actingAs($director)
            ->get(route('congviec.chitiet', $task))
            ->assertOk()
            ->assertDontSee('bao-cao.pdf');

        $this->actingAs($director)
            ->get(route('congviec.file.download', $document))
            ->assertForbidden();

        $this->actingAs($manager)
            ->patch(route('manager.file.forward', $document))
            ->assertRedirect();

        $document->refresh();
        $this->assertSame(Document::STATUS_DIRECTOR_VISIBLE, $document->review_status);
        $this->assertSame($manager->id, $document->forwarded_by);
        $this->assertNotNull($document->forwarded_at);

        $this->actingAs($director)
            ->get(route('congviec.chitiet', $task))
            ->assertOk()
            ->assertSee('bao-cao.pdf')
            ->assertSee('Đã gửi Giám đốc');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $director->id,
            'task_id' => $task->id,
            'title' => 'Trưởng phòng đã chuyển file lên Giám đốc',
        ]);
    }
}
