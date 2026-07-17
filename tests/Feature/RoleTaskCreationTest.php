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

    public function test_director_can_assign_one_task_to_multiple_users(): void
    {
        $director = User::factory()->create(['role_id' => User::ROLE_ADMIN, 'department_id' => 1]);
        $firstEmployee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 1]);
        $secondEmployee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 2]);

        $this->actingAs($director)->post(route('dashboard.tasks.save'), [
            'task_name' => 'Lap bao cao lien phong',
            'assigned_to' => [$firstEmployee->id, $secondEmployee->id],
            'deadline' => now()->addWeek()->toDateString(),
            'description' => 'Moi phong nop mot phan',
            'status' => 'Chờ xử lý',
        ])->assertRedirect(route('dashboard.tasks'));

        $this->assertDatabaseHas('tasks', [
            'task_name' => 'Lap bao cao lien phong',
            'assigned_by' => $director->id,
            'assigned_to' => $firstEmployee->id,
        ]);
        $this->assertDatabaseHas('tasks', [
            'task_name' => 'Lap bao cao lien phong',
            'assigned_by' => $director->id,
            'assigned_to' => $secondEmployee->id,
        ]);
        $this->assertSame(2, Task::where('task_name', 'Lap bao cao lien phong')->count());
        $this->assertSame(2, Notification::where('title', 'Bạn vừa được giao công việc mới')->count());
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

    public function test_manager_can_assign_one_task_to_multiple_department_employees(): void
    {
        Department::create(['id' => 1, 'TENPHONG' => 'Nhan su', 'name' => 'HR']);

        $manager = User::factory()->create(['role_id' => User::ROLE_MANAGER, 'department_id' => 1]);
        $firstEmployee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 1]);
        $secondEmployee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 1]);

        $this->actingAs($manager)->post(route('dashboard.tasks.save'), [
            'task_name' => 'Kiem tra ho so nhan su',
            'assigned_to' => [$firstEmployee->id, $secondEmployee->id],
            'deadline' => now()->addDays(5)->toDateString(),
            'status' => 'Đang làm',
        ])->assertRedirect(route('dashboard.tasks'));

        $this->assertDatabaseHas('tasks', [
            'task_name' => 'Kiem tra ho so nhan su',
            'assigned_by' => $manager->id,
            'assigned_to' => $firstEmployee->id,
            'status' => 'Đang làm',
        ]);
        $this->assertDatabaseHas('tasks', [
            'task_name' => 'Kiem tra ho so nhan su',
            'assigned_by' => $manager->id,
            'assigned_to' => $secondEmployee->id,
            'status' => 'Đang làm',
        ]);
        $this->assertSame(2, Task::where('task_name', 'Kiem tra ho so nhan su')->count());
    }

    public function test_manager_dashboard_assign_form_accepts_multiple_department_employees(): void
    {
        Department::create(['id' => 1, 'TENPHONG' => 'Nhan su', 'name' => 'HR']);

        $manager = User::factory()->create(['role_id' => User::ROLE_MANAGER, 'department_id' => 1]);
        $firstEmployee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 1]);
        $secondEmployee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 1]);

        $this->actingAs($manager)->post(route('manager.task.assign'), [
            'task_name' => 'Sap xep lich phong van',
            'assigned_to' => [$firstEmployee->id, $secondEmployee->id],
            'deadline' => now()->addDays(4)->toDateString(),
            'description' => 'Moi nguoi phu trach mot nhom ung vien',
            'status' => 'Chờ xử lý',
        ])->assertRedirect(route('manager.dashboard'));

        $this->assertDatabaseHas('tasks', [
            'task_name' => 'Sap xep lich phong van',
            'assigned_by' => $manager->id,
            'assigned_to' => $firstEmployee->id,
        ]);
        $this->assertDatabaseHas('tasks', [
            'task_name' => 'Sap xep lich phong van',
            'assigned_by' => $manager->id,
            'assigned_to' => $secondEmployee->id,
        ]);
        $this->assertSame(2, Task::where('task_name', 'Sap xep lich phong van')->count());
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

    public function test_employee_task_nav_uses_employee_task_route(): void
    {
        $employee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 1]);

        $this->actingAs($employee)
            ->get(route('employee.dashboard'))
            ->assertOk()
            ->assertSee(route('employee.tasks'), false)
            ->assertDontSee(route('dashboard.tasks'), false);

        $this->actingAs($employee)
            ->get(route('employee.tasks'))
            ->assertOk()
            ->assertSee('Việc của tôi')
            ->assertSee('Employee WorkHub')
            ->assertSee(route('employee.tasks.save'), false)
            ->assertDontSee('WorkHub</a>', false);

        $this->actingAs($employee)
            ->post(route('employee.tasks.save'), [
                'task_name' => 'De xuat tu route nhan vien',
                'deadline' => now()->addWeek()->toDateString(),
                'description' => 'Khong di qua route dashboard WorkHub chung.',
                'status' => 'Chờ xử lý',
            ])
            ->assertRedirect(route('employee.tasks'));
    }

    public function test_role_dashboards_use_role_specific_task_routes(): void
    {
        Department::create(['id' => 1, 'TENPHONG' => 'Nhan su', 'name' => 'HR']);

        $director = User::factory()->create(['role_id' => User::ROLE_ADMIN, 'department_id' => 1]);
        $manager = User::factory()->create(['role_id' => User::ROLE_MANAGER, 'department_id' => 1]);
        $employee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 1]);

        $this->actingAs($director)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.tasks'), false)
            ->assertDontSee(route('dashboard.tasks'), false);

        $this->actingAs($director)
            ->get(route('admin.tasks'))
            ->assertOk()
            ->assertSee(route('admin.tasks.save'), false);

        $this->actingAs($director)
            ->post(route('admin.tasks.save'), [
                'task_name' => 'Giao viec tu route admin',
                'assigned_to' => [$employee->id],
                'deadline' => now()->addWeek()->toDateString(),
                'status' => 'Chá» xá»­ lÃ½',
            ])
            ->assertRedirect(route('admin.tasks'));

        $this->actingAs($manager)
            ->get(route('manager.dashboard'))
            ->assertOk()
            ->assertSee(route('manager.tasks'), false)
            ->assertDontSee(route('dashboard.tasks'), false);

        $this->actingAs($manager)
            ->get(route('manager.tasks'))
            ->assertOk()
            ->assertSee(route('manager.tasks.save'), false);

        $this->actingAs($manager)
            ->post(route('manager.tasks.save'), [
                'task_name' => 'Giao viec tu route manager',
                'assigned_to' => [$employee->id],
                'deadline' => now()->addWeek()->toDateString(),
                'status' => 'Chá» xá»­ lÃ½',
            ])
            ->assertRedirect(route('manager.tasks'));
    }

    public function test_director_can_open_all_mobifone_members_from_admin_nav(): void
    {
        Department::create(['id' => 1, 'TENPHONG' => 'Nhan su', 'name' => 'HR']);

        $director = User::factory()->create(['role_id' => User::ROLE_ADMIN, 'department_id' => 1]);
        User::factory()->create([
            'name' => 'Nhan vien Mobifone',
            'email' => 'nhanvien@mobifone.vn',
            'role_id' => User::ROLE_EMPLOYEE,
            'department_id' => 1,
        ]);

        $this->actingAs($director)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.members'), false)
            ->assertSee('Tất cả nhân viên');

        $this->actingAs($director)
            ->get(route('admin.members'))
            ->assertOk()
            ->assertSee('Toàn bộ nhân viên MobiFone')
            ->assertSee('nhanvien@mobifone.vn')
            ->assertSee(route('admin.members'), false);
    }

    public function test_manager_nav_opens_department_members_page(): void
    {
        Department::create(['id' => 1, 'TENPHONG' => 'Phap che', 'name' => 'Legal']);
        Department::create(['id' => 2, 'TENPHONG' => 'Nhan su', 'name' => 'HR']);

        $manager = User::factory()->create([
            'role_id' => User::ROLE_MANAGER,
            'department_id' => 1,
            'name' => 'Khang',
        ]);
        User::factory()->create([
            'role_id' => User::ROLE_EMPLOYEE,
            'department_id' => 1,
            'name' => 'Nhan vien cung phong',
            'email' => 'team@mobifone.vn',
        ]);
        User::factory()->create([
            'role_id' => User::ROLE_EMPLOYEE,
            'department_id' => 2,
            'name' => 'Nhan vien khac phong',
            'email' => 'outside@mobifone.vn',
        ]);

        $this->actingAs($manager)
            ->get(route('manager.dashboard'))
            ->assertOk()
            ->assertSee(route('manager.members'), false);

        $this->actingAs($manager)
            ->get(route('manager.members'))
            ->assertOk()
            ->assertSee('Nhân viên phòng')
            ->assertSee('team@mobifone.vn')
            ->assertDontSee('outside@mobifone.vn');

        $this->actingAs($manager)
            ->get(route('manager.tasks'))
            ->assertOk()
            ->assertSee('Giao công việc')
            ->assertSee(str_replace('&', '&amp;', route('manager.tasks', ['mode' => 'progress', 'view' => 'list'])), false);
    }

    public function test_manager_task_detail_uses_manager_layout_from_progress_page(): void
    {
        Department::create(['id' => 1, 'TENPHONG' => 'Phap che', 'name' => 'Legal']);

        $manager = User::factory()->create([
            'role_id' => User::ROLE_MANAGER,
            'department_id' => 1,
            'name' => 'Khang',
        ]);
        $employee = User::factory()->create([
            'role_id' => User::ROLE_EMPLOYEE,
            'department_id' => 1,
            'name' => 'Tuyet Kha',
        ]);

        $task = Task::create([
            'task_name' => 'Chuan bi phong hop',
            'description' => 'Chuan bi phong hop cho ngay 13/7/2026',
            'assigned_by' => $manager->id,
            'assigned_to' => $employee->id,
            'deadline' => now()->addDay()->toDateString(),
            'status' => 'Đang review',
            'progress' => 80,
        ]);

        $this->actingAs($manager)
            ->get(route('manager.tasks', ['mode' => 'progress', 'view' => 'list']))
            ->assertOk()
            ->assertSee(route('manager.tasks.show', ['task' => $task->id, 'from' => 'progress']), false)
            ->assertDontSee(route('congviec.chitiet', $task->id), false);

        $this->actingAs($manager)
            ->get(route('manager.tasks.show', ['task' => $task->id, 'from' => 'progress']))
            ->assertOk()
            ->assertSee('MANAGER CONSOLE')
            ->assertSee('Chi tiết công việc')
            ->assertSee('Chuan bi phong hop')
            ->assertSee('Tiến độ công việc')
            ->assertDontSee('DIRECTOR CONSOLE')
            ->assertDontSee('Giám đốc / Điều hành');
    }

    public function test_manager_dispatch_and_progress_pages_have_distinct_interfaces(): void
    {
        Department::create(['id' => 1, 'TENPHONG' => 'Phap che', 'name' => 'Legal']);

        $manager = User::factory()->create([
            'role_id' => User::ROLE_MANAGER,
            'department_id' => 1,
            'name' => 'Khang',
        ]);
        $employee = User::factory()->create([
            'role_id' => User::ROLE_EMPLOYEE,
            'department_id' => 1,
            'name' => 'Tuyet Kha',
        ]);

        Task::create([
            'task_name' => 'Chuan bi phong hop',
            'assigned_by' => $manager->id,
            'assigned_to' => $employee->id,
            'deadline' => now()->addDay()->toDateString(),
            'status' => 'Đang làm',
            'progress' => 65,
        ]);

        $this->actingAs($manager)
            ->get(route('manager.tasks'))
            ->assertOk()
            ->assertSee('Bàn giao việc cho đội nhóm')
            ->assertSee('Luồng giao việc')
            ->assertDontSee('Progress Control');

        $this->actingAs($manager)
            ->get(route('manager.tasks', ['mode' => 'progress', 'view' => 'list']))
            ->assertOk()
            ->assertSee('Progress Control')
            ->assertSee('Bảng theo dõi tiến độ')
            ->assertSee('Radar tiến độ')
            ->assertDontSee('Luồng giao việc');
    }

    public function test_employee_can_update_their_task_status_from_workhub_tasks(): void
    {
        $employee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 1]);

        $task = Task::create([
            'task_name' => 'Cap nhat tien do ca nhan',
            'description' => 'Nhan vien cap nhat trang thai tu trang cong viec.',
            'assigned_to' => $employee->id,
            'assigned_by' => $employee->id,
            'deadline' => now()->addDay()->toDateString(),
            'status' => 'Chờ xử lý',
            'progress' => 0,
        ]);

        $this->actingAs($employee)
            ->patchJson(route('employee.task.progress', $task->id), [
                'status' => 'Đang review',
                'progress' => 80,
            ])
            ->assertOk()
            ->assertJson([
                'ok' => true,
                'status' => 'Đang review',
                'progress' => 80,
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'Đang review',
            'progress' => 80,
        ]);
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
            ->assertSee('Tổng quan hôm nay')
            ->assertSee('Mở trang công việc')
            ->assertDontSee('Danh sách công việc cá nhân');
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

    public function test_members_page_searches_by_employee_code_and_paginates_ten_per_page(): void
    {
        Department::create(['id' => 1, 'TENPHONG' => 'Nhan su', 'name' => 'HR']);
        Role::create(['id' => User::ROLE_ADMIN, 'name' => 'Admin']);
        Role::create(['id' => User::ROLE_EMPLOYEE, 'name' => 'Nhan vien']);

        $director = User::factory()->create([
            'id' => 100,
            'role_id' => User::ROLE_ADMIN,
            'department_id' => 1,
        ]);

        for ($i = 1; $i <= 12; $i++) {
            User::factory()->create([
                'id' => $i,
                'name' => "Member {$i}",
                'email' => "member{$i}@mobifone.vn",
                'role_id' => User::ROLE_EMPLOYEE,
                'department_id' => 1,
            ]);
        }

        $this->actingAs($director)
            ->get(route('dashboard.members'))
            ->assertOk()
            ->assertSee('NV010')
            ->assertDontSee('NV011');

        $this->actingAs($director)
            ->get(route('dashboard.members', ['page' => 2]))
            ->assertOk()
            ->assertSee('NV011')
            ->assertSee('NV012');

        $this->actingAs($director)
            ->get(route('dashboard.members', ['search' => 'NV012']))
            ->assertOk()
            ->assertSee('member12@mobifone.vn')
            ->assertDontSee('member11@mobifone.vn');
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

    public function test_task_detail_document_renderer_does_not_shadow_browser_document(): void
    {
        $viewSource = file_get_contents(resource_path('views/dashboard/tasks.blade.php'));

        $this->assertStringContainsString('documents.forEach((file) => {', $viewSource);
        $this->assertStringNotContainsString('documents.forEach((document) => {', $viewSource);
        $this->assertStringContainsString('const row = document.createElement', $viewSource);
    }

    public function test_workhub_report_uses_live_operational_data(): void
    {
        Department::create(['id' => 1, 'TENPHONG' => 'Phap che', 'name' => 'Legal']);
        Department::create(['id' => 2, 'TENPHONG' => 'Nhan su', 'name' => 'HR']);

        $employee = User::factory()->create([
            'role_id' => User::ROLE_EMPLOYEE,
            'department_id' => 1,
            'is_active' => true,
            'name' => 'Tuyet Kha',
        ]);
        $manager = User::factory()->create([
            'role_id' => User::ROLE_MANAGER,
            'department_id' => 2,
            'is_active' => true,
            'name' => 'Khang',
        ]);

        $completedTask = Task::create([
            'task_name' => 'Hoan thanh bao cao',
            'assigned_to' => $employee->id,
            'deadline' => now()->addDay()->toDateString(),
            'status' => 'Hoàn thành',
            'progress' => 100,
        ]);
        Task::create([
            'task_name' => 'Viec dang lam',
            'assigned_to' => $employee->id,
            'deadline' => now()->addDays(3)->toDateString(),
            'status' => 'Đang làm',
            'progress' => 50,
        ]);
        $managerTask = Task::create([
            'task_name' => 'Viec qua han',
            'assigned_to' => $manager->id,
            'deadline' => now()->subDay()->toDateString(),
            'status' => 'Đang làm',
            'progress' => 30,
        ]);

        Document::create([
            'task_id' => $completedTask->id,
            'user_id' => $employee->id,
            'file_name' => 'bao-cao-nhan-vien.pdf',
            'file_path' => 'tasks/bao-cao-nhan-vien.pdf',
            'file_type' => 'pdf',
            'disk' => 'public',
            'review_status' => Document::STATUS_MANAGER_REVIEW,
        ]);
        Document::create([
            'task_id' => $managerTask->id,
            'user_id' => $manager->id,
            'file_name' => 'tai-lieu-quan-ly.pdf',
            'file_path' => 'tasks/tai-lieu-quan-ly.pdf',
            'file_type' => 'pdf',
            'disk' => 'public',
            'review_status' => Document::STATUS_DIRECTOR_VISIBLE,
        ]);

        $this->actingAs($employee)
            ->get(route('dashboard.reports'))
            ->assertOk()
            ->assertSee('Báo cáo vận hành toàn WorkHub')
            ->assertSee('3')
            ->assertSee('33%')
            ->assertSee('Khối lượng theo phòng ban')
            ->assertSee('Tài liệu nhân viên tải lên')
            ->assertSee('bao-cao-nhan-vien.pdf')
            ->assertSee('Chờ trưởng phòng duyệt')
            ->assertDontSee('tai-lieu-quan-ly.pdf')
            ->assertSee('Phap che')
            ->assertSee('Nhan su')
            ->assertDontSee('Phân bổ theo ưu tiên')
            ->assertDontSee('4.2 ngày');
    }
}
