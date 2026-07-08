<?php

namespace Tests\Feature;

use App\Events\TaskCreated;
use App\Models\Department;
use App\Models\Document;
use App\Models\Notification;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WorkHubIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Cần đảm bảo có sẵn các Role cơ bản
        Role::firstOrCreate(['id' => 1], ['name' => 'Admin']);
        Role::firstOrCreate(['id' => 2], ['name' => 'Quản lý']);
        Role::firstOrCreate(['id' => 3], ['name' => 'Nhân viên']);
        
        Department::firstOrCreate(['id' => 1], ['TENPHONG' => 'Nhân sự', 'name' => 'Human Resources']);
    }

    /**
     * Test Event & Listener tự động tạo Notification khi tạo công việc mới.
     */
    public function test_task_creation_triggers_notification()
    {
        // 1. Tạo người giao việc (Quản lý) và người nhận (Nhân viên)
        $manager = User::factory()->create(['role_id' => 2, 'department_id' => 1]);
        $employee = User::factory()->create(['role_id' => 3, 'department_id' => 1]);

        // 2. Gọi lưu công việc thông qua route hoặc Controller
        $response = $this->actingAs($manager)->post(route('dashboard.tasks.save'), [
            'task_name' => 'Công việc kiểm thử tích hợp',
            'description' => 'Mô tả công việc',
            'deadline' => now()->addDays(5)->toDateString(),
            'assigned_to' => $employee->id,
            'priority' => 'Cao',
            'status' => 'Chờ xử lý',
        ]);

        $response->assertRedirect(route('dashboard.tasks'));

        // Kiểm tra record trong bảng notifications
        $this->assertDatabaseHas('notifications', [
            'user_id' => $employee->id,
            'title' => 'Bạn vừa được giao công việc mới',
        ]);
    }

    /**
     * Test Middleware phân quyền bảo vệ nghiêm ngặt.
     */
    public function test_employee_cannot_access_manager_routes()
    {
        $employee = User::factory()->create(['role_id' => 3, 'department_id' => 1]);

        // Thử truy cập trang quản lý thành viên (chỉ dành cho Leader/Admin)
        $response = $this->actingAs($employee)->get(route('dashboard.members'));
        $response->assertRedirect(route('dashboard.index'));
        $response->assertSessionHas('error');

        // Thử lưu việc mới
        $responsePost = $this->actingAs($employee)->post(route('dashboard.tasks.save'), [
            'task_name' => 'Hack task',
            'deadline' => now()->addDays(2)->toDateString(),
            'assigned_to' => 2,
            'priority' => 'Cao',
        ]);
        $responsePost->assertRedirect(route('dashboard.index'));
        $responsePost->assertSessionHas('error');
    }

    /**
     * Test hàng rào bảo mật file đính kèm.
     */
    public function test_document_download_security_barrier()
    {
        Storage::fake('public');

        $manager = User::factory()->create(['role_id' => 2, 'department_id' => 1]);
        $employee = User::factory()->create(['role_id' => 3, 'department_id' => 1]);
        $stranger = User::factory()->create(['role_id' => 3, 'department_id' => 2]); // Nhân viên phòng ban khác

        // Tạo task
        $task = Task::create([
            'task_name' => 'Công việc bảo mật tài liệu',
            'assigned_by' => $manager->id,
            'assigned_to' => $employee->id,
            'status' => 'Đang làm',
        ]);

        // Tạo document giả lập
        $file = UploadedFile::fake()->create('contract.pdf', 100);
        $storedPath = $file->storeAs('tasks/' . $task->id, 'uuid.pdf', 'public');

        $document = Document::create([
            'task_id' => $task->id,
            'user_id' => $manager->id,
            'file_name' => 'contract.pdf',
            'file_path' => $storedPath,
            'file_type' => 'pdf',
            'disk' => 'public',
        ]);

        // 1. Người liên quan (Employee được giao việc) tải xuống được
        $responseOk = $this->actingAs($employee)->get(route('document.download', $document->id));
        $responseOk->assertStatus(200);

        // 2. Người không liên quan bị chặn
        $responseBlocked = $this->actingAs($stranger)->get(route('document.download', $document->id));
        $responseBlocked->assertStatus(403);
    }
}
