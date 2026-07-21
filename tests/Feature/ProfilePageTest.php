<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProfilePageTest extends TestCase
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

    public function test_avatar_links_open_personal_profile_page(): void
    {
        Department::create(['id' => 1, 'TENPHONG' => 'Phap che', 'name' => 'Legal']);

        $manager = User::factory()->create([
            'name' => 'Khang',
            'email' => 'khang@mobifone.vn',
            'role_id' => User::ROLE_MANAGER,
            'department_id' => 1,
        ]);

        Task::create([
            'task_name' => 'Chuan bi phong hop',
            'assigned_to' => $manager->id,
            'assigned_by' => $manager->id,
            'deadline' => now()->addDay()->toDateString(),
            'status' => 'Đang làm',
            'progress' => 50,
        ]);

        $this->actingAs($manager)
            ->get(route('manager.dashboard'))
            ->assertOk()
            ->assertSee(route('profile.show'), false)
            ->assertSee('Trang cá nhân');

        $this->actingAs($manager)
            ->get(route('profile.show'))
            ->assertOk()
            ->assertSee('Trang cá nhân của tôi')
            ->assertSee('Khang')
            ->assertSee('khang@mobifone.vn')
            ->assertSee('Phap che')
            ->assertSee('1');
    }

    public function test_user_can_update_basic_profile_information(): void
    {
        $user = User::factory()->create([
            'name' => 'Khang',
            'email' => 'khang@mobifone.vn',
            'password' => Hash::make('old-password'),
            'role_id' => User::ROLE_MANAGER,
        ]);

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => 'Khang Nguyen',
                'email' => 'khang.nguyen@mobifone.vn',
            ])
            ->assertRedirect(route('profile.show'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Khang Nguyen',
            'email' => 'khang.nguyen@mobifone.vn',
        ]);
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }

    public function test_user_can_change_password_with_current_password(): void
    {
        $user = User::factory()->create([
            'name' => 'Khang',
            'email' => 'khang@mobifone.vn',
            'password' => Hash::make('old-password'),
            'role_id' => User::ROLE_MANAGER,
        ]);

        $this->actingAs($user)
            ->patch(route('profile.password.update'), [
                'current_password' => 'old-password',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])
            ->assertRedirect(route('profile.show'));

        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));
    }

    public function test_user_cannot_change_password_with_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'name' => 'Khang',
            'email' => 'khang@mobifone.vn',
            'password' => Hash::make('old-password'),
            'role_id' => User::ROLE_MANAGER,
        ]);

        $this->actingAs($user)
            ->from(route('profile.password.edit'))
            ->patch(route('profile.password.update'), [
                'current_password' => 'wrong-password',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])
            ->assertRedirect(route('profile.password.edit'))
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }

    public function test_user_can_upload_profile_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'name' => 'Khang',
            'email' => 'khang@mobifone.vn',
            'role_id' => User::ROLE_MANAGER,
        ]);

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => 'Khang',
                'email' => 'khang@mobifone.vn',
                'avatar' => UploadedFile::fake()->createWithContent(
                    'avatar.png',
                    base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII=')
                ),
            ])
            ->assertRedirect(route('profile.show'));

        $avatarPath = $user->fresh()->avatar_path;

        $this->assertNotNull($avatarPath);
        Storage::disk('public')->assertExists($avatarPath);
    }
}
