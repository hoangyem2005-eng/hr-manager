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

        $response = $this->actingAs($user)->post(route('employee.notifications.open', $notification));

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

        $response = $this->actingAs($otherUser)->post(route('employee.notifications.open', $notification));

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
            ->get(route('employee.notifications'))
            ->assertOk()
            ->assertSee('Nhắc deadline công việc sắp đến')
            ->assertSee('Nop bao cao deadline');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $employee->id,
            'task_id' => $upcomingTask->id,
            'title' => 'Nhắc deadline công việc sắp đến',
            'is_read' => false,
        ]);

        $this->actingAs($employee)->get(route('employee.notifications'))->assertOk();

        $this->assertSame(1, Notification::where('user_id', $employee->id)
            ->where('task_id', $upcomingTask->id)
            ->where('title', 'Nhắc deadline công việc sắp đến')
            ->count());

        Carbon::setTestNow();
    }

    public function test_notifications_page_generates_overdue_task_notifications(): void
    {
        Carbon::setTestNow('2026-07-14 09:00:00');

        $employee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE, 'is_active' => true]);
        $overdueTask = Task::create([
            'task_name' => 'Chuan bi phong hop',
            'assigned_to' => $employee->id,
            'deadline' => Carbon::now()->subDay()->toDateString(),
            'status' => 'Đang làm',
            'progress' => 50,
        ]);
        Task::create([
            'task_name' => 'Viec da hoan thanh',
            'assigned_to' => $employee->id,
            'deadline' => Carbon::now()->subDays(2)->toDateString(),
            'status' => 'Hoàn thành',
            'progress' => 100,
        ]);

        $this->actingAs($employee)
            ->get(route('employee.notifications'))
            ->assertOk()
            ->assertSee('Công việc đã quá hạn')
            ->assertSee('Chuan bi phong hop');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $employee->id,
            'task_id' => $overdueTask->id,
            'title' => 'Công việc đã quá hạn',
            'is_read' => false,
        ]);

        $this->actingAs($employee)->get(route('employee.notifications'))->assertOk();

        $this->assertSame(1, Notification::where('user_id', $employee->id)
            ->where('task_id', $overdueTask->id)
            ->where('title', 'Công việc đã quá hạn')
            ->count());

        $this->assertSame(1, Notification::where('user_id', $employee->id)->count());

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

    public function test_employee_notification_nav_uses_employee_notification_route(): void
    {
        $employee = User::factory()->create(['role_id' => User::ROLE_EMPLOYEE]);
        $notification = Notification::create([
            'user_id' => $employee->id,
            'title' => 'Thong bao rieng cua nhan vien',
            'message' => 'Noi dung thong bao chi hien trong khong gian nhan vien.',
            'is_read' => false,
        ]);

        $this->actingAs($employee)
            ->get(route('employee.dashboard'))
            ->assertOk()
            ->assertSee(route('employee.notifications'), false)
            ->assertDontSee(route('dashboard.notifications'), false);

        $this->actingAs($employee)
            ->get(route('employee.notifications'))
            ->assertOk()
            ->assertSee('Thông báo của tôi')
            ->assertSee('Employee WorkHub')
            ->assertSee(route('employee.notifications.open', $notification), false)
            ->assertSee(route('employee.notifications.markAllRead'), false)
            ->assertDontSee(route('dashboard.notifications'), false);

        $this->actingAs($employee)
            ->post(route('employee.notifications.open', $notification))
            ->assertRedirect(route('employee.notifications'));

        $this->assertTrue($notification->fresh()->is_read);
    }

    public function test_employee_notifications_page_shows_recent_notifications_even_after_they_are_read(): void
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
            ->get(route('dashboard.notifications'))
            ->assertOk()
            ->assertSee('Thong bao da doc')
            ->assertSee('Thong bao chua doc')
            ->assertSee('Chưa đọc')
            ->assertSee('1');
    }
}
