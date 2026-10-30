<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskAcceptanceAndSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_accept_task_from_employee_endpoint(): void
    {
        $manager = User::factory()->create(['role_id' => User::ROLE_MANAGER, 'department_id' => 1]);
        $employee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 1]);

        $task = Task::create([
            'task_name' => 'Công việc lập báo cáo tuần',
            'assigned_by' => $manager->id,
            'assigned_to' => $employee->id,
            'status' => 'Chờ xử lý',
            'progress' => 0,
            'deadline' => now()->addDays(3)->toDateString(),
        ]);

        $notif = Notification::create([
            'user_id' => $employee->id,
            'task_id' => $task->id,
            'title' => 'Giao việc mới',
            'message' => 'Bạn được giao việc mới',
            'is_read' => false,
        ]);

        $response = $this->actingAs($employee)
            ->post(route('employee.task.accept', $task->id));

        $response->assertSessionHas('success');
        $this->assertSame('Đang làm', $task->fresh()->status);
        $this->assertTrue((bool)$notif->fresh()->is_read);

        // Assigner gets notification
        $this->assertDatabaseHas('notifications', [
            'user_id' => $manager->id,
            'task_id' => $task->id,
            'title' => 'Nhân viên đã nhận việc',
        ]);
    }

    public function test_employee_can_accept_task_via_json_request(): void
    {
        $manager = User::factory()->create(['role_id' => User::ROLE_MANAGER, 'department_id' => 1]);
        $employee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 1]);

        $task = Task::create([
            'task_name' => 'Công việc thiết kế banner',
            'assigned_by' => $manager->id,
            'assigned_to' => $employee->id,
            'status' => 'Chờ xử lý',
            'progress' => 0,
        ]);

        $response = $this->actingAs($employee)
            ->postJson(route('employee.task.accept', $task->id));

        $response->assertOk()
            ->assertJson([
                'ok' => true,
                'task' => [
                    'id' => $task->id,
                    'status' => 'Đang làm',
                ]
            ]);

        $this->assertSame('Đang làm', $task->fresh()->status);
    }

    public function test_employee_can_decline_task_with_reason(): void
    {
        $manager = User::factory()->create(['role_id' => User::ROLE_MANAGER, 'department_id' => 1]);
        $employee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 1]);

        $task = Task::create([
            'task_name' => 'Nhiệm vụ trực ca đêm',
            'assigned_by' => $manager->id,
            'assigned_to' => $employee->id,
            'status' => 'Chờ xử lý',
            'progress' => 0,
        ]);

        $response = $this->actingAs($employee)
            ->post(route('employee.task.decline', $task->id), [
                'reason' => 'Trùng lịch công tác tại chi nhánh.',
            ]);

        $response->assertSessionHas('success');
        $this->assertSame('Từ chối', $task->fresh()->status);
        $this->assertStringContainsString('Trùng lịch công tác tại chi nhánh.', (string) $task->fresh()->employee_note);

        // Assigner gets decline notification
        $this->assertDatabaseHas('notifications', [
            'user_id' => $manager->id,
            'task_id' => $task->id,
            'title' => 'Nhân viên không nhận việc',
        ]);
    }

    public function test_employee_can_submit_task_for_supervisor_review_with_description(): void
    {
        $manager = User::factory()->create(['role_id' => User::ROLE_MANAGER, 'department_id' => 1]);
        $employee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 1]);

        $task = Task::create([
            'task_name' => 'Hoàn thiện hồ sơ nghiệm thu',
            'assigned_by' => $manager->id,
            'assigned_to' => $employee->id,
            'status' => 'Đang làm',
            'progress' => 50,
        ]);

        $submissionNote = "Đã hoàn thành đầy đủ biên bản và bảng kê. Kính gửi Trưởng phòng duyệt nghiệm thu.";

        $response = $this->actingAs($employee)
            ->post(route('employee.task.submit-review', $task->id), [
                'employee_note' => $submissionNote,
                'progress' => 100,
            ]);

        $response->assertSessionHas('success');
        $freshTask = $task->fresh();
        $this->assertSame('Chờ đánh giá', $freshTask->status);
        $this->assertSame(100, $freshTask->progress);
        $this->assertSame($submissionNote, $freshTask->employee_note);

        // Manager receives notification with note
        $this->assertDatabaseHas('notifications', [
            'user_id' => $manager->id,
            'task_id' => $task->id,
            'title' => 'Nhân viên nộp việc chờ duyệt',
        ]);
    }

    public function test_employee_notifications_view_has_accept_and_decline_buttons(): void
    {
        $manager = User::factory()->create(['role_id' => User::ROLE_MANAGER, 'department_id' => 1]);
        $employee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 1]);

        $task = Task::create([
            'task_name' => 'Rà soát hạ tầng BTS',
            'assigned_by' => $manager->id,
            'assigned_to' => $employee->id,
            'status' => 'Chờ xử lý',
            'deadline' => now()->addDays(2)->toDateString(),
        ]);

        Notification::create([
            'user_id' => $employee->id,
            'task_id' => $task->id,
            'title' => 'Giao việc: Rà soát hạ tầng BTS',
            'message' => 'Bạn vừa được giao việc mới',
            'is_read' => false,
        ]);

        $response = $this->actingAs($employee)
            ->get(route('employee.notifications'));

        $response->assertOk()
            ->assertSee('Nhận việc')
            ->assertSee('Không nhận');
    }

    public function test_supervisor_sees_employee_submission_description_in_task_detail(): void
    {
        $manager = User::factory()->create(['role_id' => User::ROLE_DEPT_HEAD_BIZ, 'department_id' => 1]);
        $employee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'department_id' => 1]);

        $submissionNote = "Báo cáo: Đã kiểm tra 12 trạm BTS, kết quả đều hoạt động bình thường.";

        $task = Task::create([
            'task_name' => 'Kiểm tra trạm phát sóng',
            'assigned_by' => $manager->id,
            'assigned_to' => $employee->id,
            'status' => 'Chờ đánh giá',
            'progress' => 100,
            'employee_note' => $submissionNote,
        ]);

        // Manager views task detail
        $response = $this->actingAs($manager)
            ->get(route('manager.tasks.show', $task->id));

        $response->assertOk()
            ->assertSee('Đoạn mô tả khi nộp duyệt của nhân viên')
            ->assertSee($submissionNote);
    }
}
