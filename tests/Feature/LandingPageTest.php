<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Document;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_renders_successfully(): void
    {
        $response = $this->get(route('landing'));
        $response->assertStatus(200);
    }

    public function test_landing_page_has_no_register_button_and_only_login(): void
    {
        $response = $this->get(route('landing'));
        $response->assertStatus(200);
        $response->assertDontSee('>Đăng ký<', false);
        $response->assertSee('Đăng nhập');
    }

    public function test_landing_page_uses_lanh_dao_instead_of_giam_doc(): void
    {
        $response = $this->get(route('landing'));
        $response->assertStatus(200);
        $response->assertSee('Lãnh đạo');
        $response->assertDontSee('<h3>Giám đốc</h3>', false);
        $response->assertDontSee('từ Giám đốc', false);
        $response->assertDontSee('gửi Giám đốc', false);
    }

    public function test_landing_page_displays_real_data_from_database(): void
    {
        $dept = Department::create(['id' => 10, 'TENPHONG' => 'Phòng Thử Nghiệm', 'name' => 'phong-thu-nghiem']);
        $user = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => $dept->id]);
        
        Task::create([
            'task_name' => 'Công việc test 123',
            'description' => 'Mô tả test',
            'assigned_by' => $user->id,
            'assigned_to' => $user->id,
            'status' => 'Hoàn thành',
            'progress' => 100,
        ]);

        $response = $this->get(route('landing'));
        $response->assertStatus(200);
        $response->assertSee('Phòng Thử Nghiệm');
    }
}
