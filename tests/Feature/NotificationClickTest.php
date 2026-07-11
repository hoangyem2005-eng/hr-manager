<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class NotificationClickTest extends TestCase
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

    public function test_user_clicking_their_notification_marks_it_read_and_redirects_to_task_detail(): void
    {
        $user = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE]);
        $task = Task::create([
            'task_name' => 'Hoan thien bao cao tuan',
            'assigned_to' => $user->id,
            'status' => 'Todo',
        ]);
        $notification = Notification::create([
            'user_id' => $user->id,
            'task_id' => $task->id,
            'title' => 'Ban vua duoc giao viec',
            'message' => 'WH-001: Hoan thien bao cao tuan',
            'is_read' => false,
        ]);

        $response = $this->actingAs($user)->post(route('dashboard.notifications.open', $notification));

        $response->assertRedirect(route('employee.task.detail', $task));
        $this->assertTrue($notification->fresh()->is_read);

        $this->get(route('employee.task.detail', $task))
            ->assertOk()
            ->assertSee('Hoan thien bao cao tuan');
    }

    public function test_user_cannot_mark_someone_elses_notification_as_read(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $notification = Notification::create([
            'user_id' => $owner->id,
            'title' => 'Thong bao rieng',
            'message' => 'Noi dung rieng',
            'is_read' => false,
        ]);

        $response = $this->actingAs($otherUser)->post(route('dashboard.notifications.open', $notification));

        $response->assertForbidden();
        $this->assertFalse($notification->fresh()->is_read);
    }

    public function test_manager_can_broadcast_a_notification_to_all_active_users(): void
    {
        $manager = User::factory()->create(['role_id' => User::ROLE_MANAGER, 'is_active' => true]);
        $firstEmployee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'is_active' => true]);
        $secondEmployee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'is_active' => true]);
        $inactiveEmployee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'is_active' => false]);

        $response = $this->actingAs($manager)->post(route('dashboard.notifications.broadcast'), [
            'title' => 'Thong bao bao tri he thong',
            'message' => 'He thong se bao tri luc 22h hom nay.',
        ]);

        $response->assertRedirect(route('dashboard.notifications'));

        foreach ([$manager, $firstEmployee, $secondEmployee] as $recipient) {
            $this->assertDatabaseHas('notifications', [
                'user_id' => $recipient->id,
                'title' => 'Thong bao bao tri he thong',
                'message' => 'He thong se bao tri luc 22h hom nay.',
                'is_read' => false,
            ]);
        }

        $this->assertDatabaseMissing('notifications', [
            'user_id' => $inactiveEmployee->id,
            'title' => 'Thong bao bao tri he thong',
        ]);
        $this->assertSame(3, Notification::where('title', 'Thong bao bao tri he thong')->count());
    }

    public function test_employee_cannot_broadcast_a_system_notification(): void
    {
        $employee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'is_active' => true]);

        $response = $this->actingAs($employee)
            ->from(route('dashboard.notifications'))
            ->post(route('dashboard.notifications.broadcast'), [
                'title' => 'Thong bao khong hop le',
                'message' => 'Nhan vien thuong khong duoc phat thong bao.',
            ]);

        $response->assertRedirect(route('dashboard.notifications'));
        $this->assertDatabaseMissing('notifications', [
            'title' => 'Thong bao khong hop le',
        ]);
    }

    public function test_notifications_page_generates_deadline_reminders_for_upcoming_tasks(): void
    {
        Carbon::setTestNow('2026-07-05 09:00:00');

        $employee = User::factory()->create(['role_id' => User::ROLE_MANAGER, 'is_active' => true]);
        $upcomingTask = Task::create([
            'task_name' => 'Nop bao cao deadline',
            'assigned_to' => $employee->id,
            'deadline' => Carbon::now()->addDay()->toDateString(),
            'status' => 'Đang làm',
        ]);
        Task::create([
            'task_name' => 'Cong viec con xa deadline',
            'assigned_to' => $employee->id,
            'deadline' => Carbon::now()->addDays(5)->toDateString(),
            'status' => 'Đang làm',
        ]);

        $this->actingAs($employee)
            ->get(route('dashboard.notifications'))
            ->assertOk()
            ->assertSee('Nhắc deadline công việc sắp đến')
            ->assertSee('Nop bao cao deadline');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $employee->id,
            'task_id' => $upcomingTask->id,
            'title' => 'Nhắc deadline công việc sắp đến',
            'is_read' => false,
        ]);

        $this->actingAs($employee)->get(route('dashboard.notifications'))->assertOk();

        $this->assertSame(1, Notification::where('user_id', $employee->id)
            ->where('task_id', $upcomingTask->id)
            ->where('title', 'Nhắc deadline công việc sắp đến')
            ->count());

        Carbon::setTestNow();
    }

    public function test_director_sidebar_keeps_director_role_on_notifications_page(): void
    {
        $director = User::factory()->create([
            'name' => 'Tuan Anh',
            'role_id' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $this->actingAs($director)
            ->get(route('dashboard.notifications'))
            ->assertOk()
            ->assertSee('Giám đốc')
            ->assertDontSee('Trưởng phòng');
    }

    public function test_employee_workbench_shows_recent_notifications_even_after_they_are_read(): void
    {
        $employee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE]);

        Notification::create([
            'user_id' => $employee->id,
            'title' => 'Thong bao da doc',
            'message' => 'Noi dung da doc van can hien thi',
            'is_read' => true,
        ]);
        Notification::create([
            'user_id' => $employee->id,
            'title' => 'Thong bao chua doc',
            'message' => 'Noi dung chua doc',
            'is_read' => false,
        ]);

        $this->actingAs($employee)
            ->get(route('employee.dashboard'))
            ->assertOk()
            ->assertSee('Thong bao da doc')
            ->assertSee('Thong bao chua doc')
            ->assertSee('Thông báo (1)');
    }
}
