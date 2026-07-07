<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Task;
use App\Models\User;
use Tests\TestCase;

class AdminModuleViewsTest extends TestCase
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

    public function test_admin_task_module_pages_render(): void
    {
        $admin = User::factory()->create(['role_id' => User::ROLE_ADMIN]);
        $employee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE]);
        Task::create([
            'task_name' => 'Kiem tra module admin',
            'assigned_by' => $admin->id,
            'assigned_to' => $employee->id,
            'status' => 'Todo',
            'progress' => 0,
        ]);

        $this->actingAs($admin)
            ->get(route('congviec.danhsach'))
            ->assertOk()
            ->assertSee('Quản trị công việc');

        $this->actingAs($admin)
            ->get(route('congviec.taomoi'))
            ->assertOk()
            ->assertSee('Thông tin công việc');
    }

    public function test_admin_department_and_progress_module_pages_render(): void
    {
        $admin = User::factory()->create(['role_id' => User::ROLE_ADMIN]);
        Department::create(['TENPHONG' => 'Nhan su', 'name' => 'HR']);

        $this->actingAs($admin)
            ->get(route('phongban.danhsach'))
            ->assertOk()
            ->assertSee('Cấu trúc phòng ban');

        $this->actingAs($admin)
            ->get(route('tiendo.index'))
            ->assertOk()
            ->assertSee('Theo dõi tiến độ công việc');
    }
}
