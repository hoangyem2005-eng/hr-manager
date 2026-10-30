<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Document;
use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TaskProposalTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        Storage::fake('public');
        $this->artisan('migrate:fresh')->run();
    }

    public function test_employee_can_submit_proposal_with_notes_attachments_and_direct_leader_approver(): void
    {
        $department = Department::create(['id' => 1, 'TENPHONG' => 'Kinh Doanh', 'name' => 'kinh-doanh']);

        $leader = User::factory()->create([
            'role_id' => User::ROLE_DEPT_HEAD_BIZ,
            'department_id' => $department->id,
            'name' => 'Trưởng Phòng Kinh Doanh',
        ]);

        $employee = User::factory()->create([
            'role_id' => User::ROLE_EMPLOYEE,
            'department_id' => $department->id,
            'name' => 'Nhân Viên A',
        ]);

        $file = UploadedFile::fake()->create('ke_hoach_kinh_doanh.pdf', 500, 'application/pdf');

        $response = $this->actingAs($employee)->post('/tasks/save', [
            'is_proposal' => 1,
            'task_name' => 'Đề xuất chiến dịch khuyến mãi Q3',
            'description' => 'Ghi chú lý do: Tăng doanh số quý 3 thông qua gói cước data mới.',
            'deadline' => Carbon::now()->addDays(5)->toDateString(),
            'priority' => 'Cao',
            'approver_id' => $leader->id,
            'attachments' => [$file],
        ]);

        $response->assertRedirect();

        $task = Task::where('task_name', 'Đề xuất chiến dịch khuyến mãi Q3')->first();
        $this->assertNotNull($task);
        $this->assertTrue((bool) $task->is_proposal);
        $this->assertEquals(1, $task->proposal_step);
        $this->assertEquals($employee->id, $task->assigned_by);
        $this->assertEquals('Ghi chú lý do: Tăng doanh số quý 3 thông qua gói cước data mới.', $task->description);

        // Verify document is attached
        $this->assertDatabaseHas('documents', [
            'task_id' => $task->id,
            'user_id' => $employee->id,
            'file_name' => 'ke_hoach_kinh_doanh.pdf',
        ]);

        // Verify notification sent directly to leader
        $this->assertDatabaseHas('notifications', [
            'user_id' => $leader->id,
            'task_id' => $task->id,
            'title' => 'Đề xuất công việc mới từ nhân viên',
        ]);

        // Leader sees the proposal in proposal mode
        $leaderView = $this->actingAs($leader)->get('/manager/tasks?mode=proposal&view=list');
        $leaderView->assertOk();
        $mappedList = $leaderView->viewData('mappedTasksList');
        $this->assertContains($task->id, array_column($mappedList, 'id'));
    }

    public function test_employee_can_submit_proposal_directly_to_director(): void
    {
        $department = Department::create(['id' => 1, 'TENPHONG' => 'Kinh Doanh', 'name' => 'kinh-doanh']);

        $director = User::factory()->create([
            'role_id' => User::ROLE_ADMIN,
            'department_id' => $department->id,
            'name' => 'Giám Đốc',
        ]);

        $employee = User::factory()->create([
            'role_id' => User::ROLE_EMPLOYEE,
            'department_id' => $department->id,
            'name' => 'Nhân Viên B',
        ]);

        $file = UploadedFile::fake()->create('to_trinh.docx', 300, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $response = $this->actingAs($employee)->post('/tasks/save', [
            'is_proposal' => 1,
            'task_name' => 'Đề xuất mở rộng chi nhánh miền Tây',
            'description' => 'Kính gửi Giám đốc phê duyệt tờ trình mở rộng mạng lưới.',
            'deadline' => Carbon::now()->addDays(15)->toDateString(),
            'priority' => 'Cao',
            'approver_id' => $director->id,
            'attachments' => [$file],
        ]);

        $response->assertRedirect();

        $task = Task::where('task_name', 'Đề xuất mở rộng chi nhánh miền Tây')->first();
        $this->assertNotNull($task);
        $this->assertTrue((bool) $task->is_proposal);
        $this->assertEquals(2, $task->proposal_step); // Directly step 2 for director

        // Director receives notification
        $this->assertDatabaseHas('notifications', [
            'user_id' => $director->id,
            'task_id' => $task->id,
        ]);

        // Director sees proposal in proposal mode
        $directorView = $this->actingAs($director)->get('/admin/tasks?mode=proposal&view=list');
        $directorView->assertOk();
        $mappedList = $directorView->viewData('mappedTasksList');
        $this->assertContains($task->id, array_column($mappedList, 'id'));
    }

    public function test_leader_can_approve_proposal_directly(): void
    {
        $department = Department::create(['id' => 1, 'TENPHONG' => 'Kỹ Thuật', 'name' => 'ky-thuat']);

        $leader = User::factory()->create([
            'role_id' => User::ROLE_DEPT_HEAD_TEL,
            'department_id' => $department->id,
        ]);

        $employee = User::factory()->create([
            'role_id' => User::ROLE_EMPLOYEE,
            'department_id' => $department->id,
        ]);

        $proposal = Task::create([
            'task_name' => 'Đề xuất nâng cấp trạm BTS',
            'description' => 'Nâng cấp thiết bị phát sóng tại trạm số 5',
            'assigned_by' => $employee->id,
            'assigned_to' => $employee->id,
            'deadline' => Carbon::now()->addDays(7)->toDateString(),
            'is_proposal' => true,
            'proposal_step' => 1,
            'status' => 'Chờ xử lý',
        ]);
        $proposal->assignees()->sync([$employee->id]);

        $response = $this->actingAs($leader)->post("/dashboard/tasks/{$proposal->id}/approve-proposal");
        $response->assertRedirect();

        $proposal->refresh();
        $this->assertFalse((bool) $proposal->is_proposal);
        $this->assertEquals('Chờ xử lý', $proposal->status);
        $this->assertEquals($employee->id, $proposal->assigned_to);

        // Check employee received approval notification
        $this->assertDatabaseHas('notifications', [
            'user_id' => $employee->id,
            'task_id' => $proposal->id,
            'title' => 'Đề xuất công việc đã được phê duyệt',
        ]);
    }

    public function test_leader_can_escalate_proposal_to_director(): void
    {
        $department = Department::create(['id' => 1, 'TENPHONG' => 'Kỹ Thuật', 'name' => 'ky-thuat']);

        $director = User::factory()->create([
            'role_id' => User::ROLE_ADMIN,
            'department_id' => $department->id,
        ]);

        $leader = User::factory()->create([
            'role_id' => User::ROLE_DEPT_HEAD_TEL,
            'department_id' => $department->id,
        ]);

        $employee = User::factory()->create([
            'role_id' => User::ROLE_EMPLOYEE,
            'department_id' => $department->id,
        ]);

        $proposal = Task::create([
            'task_name' => 'Đề xuất mua sắm máy chủ 500 triệu',
            'assigned_by' => $employee->id,
            'assigned_to' => $employee->id,
            'deadline' => Carbon::now()->addDays(14)->toDateString(),
            'is_proposal' => true,
            'proposal_step' => 1,
            'status' => 'Chờ xử lý',
        ]);

        $response = $this->actingAs($leader)->post("/dashboard/tasks/{$proposal->id}/escalate");
        $response->assertRedirect();

        $proposal->refresh();
        $this->assertEquals(2, $proposal->proposal_step);

        // Director receives escalation notification
        $this->assertDatabaseHas('notifications', [
            'user_id' => $director->id,
            'task_id' => $proposal->id,
            'title' => 'Trưởng phòng chuyển tiếp đề xuất công việc',
        ]);
    }

    public function test_leader_or_director_can_reject_proposal(): void
    {
        $department = Department::create(['id' => 1, 'TENPHONG' => 'Tổng Hợp', 'name' => 'tong-hop']);

        $leader = User::factory()->create([
            'role_id' => User::ROLE_DEPT_HEAD_GEN,
            'department_id' => $department->id,
        ]);

        $employee = User::factory()->create([
            'role_id' => User::ROLE_EMPLOYEE,
            'department_id' => $department->id,
        ]);

        $proposal = Task::create([
            'task_name' => 'Đề xuất không hợp lý',
            'assigned_by' => $employee->id,
            'assigned_to' => $employee->id,
            'deadline' => Carbon::now()->addDays(2)->toDateString(),
            'is_proposal' => true,
            'proposal_step' => 1,
            'status' => 'Chờ xử lý',
        ]);

        $proposalId = $proposal->id;

        $response = $this->actingAs($leader)->post("/dashboard/tasks/{$proposalId}/reject-proposal");
        $response->assertRedirect();

        $this->assertNull(Task::find($proposalId));

        // Employee receives rejection notification
        $this->assertDatabaseHas('notifications', [
            'user_id' => $employee->id,
            'title' => 'Đề xuất công việc bị từ chối',
        ]);
    }
}
