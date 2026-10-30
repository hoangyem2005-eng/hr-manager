<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class EmployeeFilterTest extends TestCase
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

    public function test_admin_employees_filter_by_department_id_and_name(): void
    {
        $adminRole = Role::create(['id' => User::ROLE_ADMIN, 'name' => 'Admin']);
        $employeeRole = Role::create(['id' => User::ROLE_EMPLOYEE, 'name' => 'Employee']);

        $dept1 = Department::create(['id' => 1, 'TENPHONG' => 'Trung tâm kinh doanh', 'name' => 'ttkd']);
        $dept2 = Department::create(['id' => 2, 'TENPHONG' => 'Phòng viễn thông', 'name' => 'pvt']);

        $admin = User::factory()->create([
            'id' => 1,
            'role_id' => $adminRole->id,
            'department_id' => $dept1->id,
            'name' => 'Hoàng Thị Em',
            'email' => 'em@test.com',
        ]);

        $user2 = User::factory()->create([
            'id' => 2,
            'role_id' => $employeeRole->id,
            'department_id' => $dept1->id,
            'name' => 'Nguyễn Văn An',
            'email' => 'an@test.com',
        ]);

        $user3 = User::factory()->create([
            'id' => 3,
            'role_id' => $employeeRole->id,
            'department_id' => $dept2->id,
            'name' => 'Lê Minh Châu',
            'email' => 'chau@test.com',
        ]);

        // Filter by dept numeric ID
        $response = $this->actingAs($admin)->get(route('admin.employees', ['dept' => 1]));
        $response->assertOk();
        $response->assertSee('Nguyễn Văn An');
        $response->assertDontSee('Lê Minh Châu');

        // Filter by dept name
        $response2 = $this->actingAs($admin)->get(route('admin.employees', ['dept' => 'Trung tâm kinh doanh']));
        $response2->assertOk();
        $response2->assertSee('Nguyễn Văn An');
        $response2->assertDontSee('Lê Minh Châu');

        // Filter by name
        $response3 = $this->actingAs($admin)->get(route('admin.employees', ['search' => 'Minh Châu']));
        $response3->assertOk();
        $response3->assertSee('Lê Minh Châu');
        $response3->assertDontSee('Nguyễn Văn An');

        // Filter by employee ID (numeric)
        $response4 = $this->actingAs($admin)->get(route('admin.employees', ['search' => '3']));
        $response4->assertOk();
        $response4->assertSee('Lê Minh Châu');
        $response4->assertDontSee('Nguyễn Văn An');

        // Filter by employee code (NV003)
        $response5 = $this->actingAs($admin)->get(route('admin.employees', ['search' => 'NV003']));
        $response5->assertOk();
        $response5->assertSee('Lê Minh Châu');
        $response5->assertDontSee('Nguyễn Văn An');

        // Filter by employee code (NV3)
        $response6 = $this->actingAs($admin)->get(route('admin.employees', ['search' => 'NV3']));
        $response6->assertOk();
        $response6->assertSee('Lê Minh Châu');
        $response6->assertDontSee('Nguyễn Văn An');
    }

    public function test_admin_members_filter_by_department_and_id(): void
    {
        $adminRole = Role::create(['id' => User::ROLE_ADMIN, 'name' => 'Admin']);
        $employeeRole = Role::create(['id' => User::ROLE_EMPLOYEE, 'name' => 'Employee']);

        $dept1 = Department::create(['id' => 1, 'TENPHONG' => 'Trung tâm kinh doanh', 'name' => 'ttkd']);
        $dept2 = Department::create(['id' => 2, 'TENPHONG' => 'Phòng viễn thông', 'name' => 'pvt']);

        $admin = User::factory()->create([
            'id' => 1,
            'role_id' => $adminRole->id,
            'department_id' => $dept1->id,
            'name' => 'Hoàng Thị Em',
            'email' => 'em@test.com',
        ]);

        $user2 = User::factory()->create([
            'id' => 2,
            'role_id' => $employeeRole->id,
            'department_id' => $dept1->id,
            'name' => 'Nguyễn Văn An',
            'email' => 'an@test.com',
        ]);

        $user3 = User::factory()->create([
            'id' => 3,
            'role_id' => $employeeRole->id,
            'department_id' => $dept2->id,
            'name' => 'Lê Minh Châu',
            'email' => 'chau@test.com',
        ]);

        // Filter by dept name
        $response = $this->actingAs($admin)->get(route('admin.members', ['dept' => 'Phòng viễn thông']));
        $response->assertOk();
        $response->assertSee('Lê Minh Châu');
        $response->assertDontSee('Nguyễn Văn An');

        // Filter by dept ID
        $response2 = $this->actingAs($admin)->get(route('admin.members', ['dept' => 2]));
        $response2->assertOk();
        $response2->assertSee('Lê Minh Châu');
        $response2->assertDontSee('Nguyễn Văn An');

        // Filter by employee code
        $response3 = $this->actingAs($admin)->get(route('admin.members', ['search' => 'NV002']));
        $response3->assertOk();
        $response3->assertSee('Nguyễn Văn An');
        $response3->assertDontSee('Lê Minh Châu');

        // Filter by employee numeric ID
        $response4 = $this->actingAs($admin)->get(route('admin.members', ['search' => '2']));
        $response4->assertOk();
        $response4->assertSee('Nguyễn Văn An');
        $response4->assertDontSee('Lê Minh Châu');
    }
}
