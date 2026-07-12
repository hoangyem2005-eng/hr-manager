<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthLoginStatusTest extends TestCase
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

    public function test_active_user_can_login(): void
    {
        $user = User::create([
            'name' => 'Active User',
            'email' => 'active@mobifone.vn',
            'password' => Hash::make('secret123'),
            'role_id' => User::ROLE_EMPLOYEE,
            'department_id' => 1,
            'is_active' => true,
        ]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'secret123',
        ])->assertRedirect(route('employee.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_inactive_user_is_blocked_from_login(): void
    {
        $user = User::create([
            'name' => 'Inactive User',
            'email' => 'inactive@mobifone.vn',
            'password' => Hash::make('secret123'),
            'role_id' => User::ROLE_EMPLOYEE,
            'department_id' => 1,
            'is_active' => false,
        ]);

        $this->from(route('login'))->post(route('login'), [
            'email' => $user->email,
            'password' => 'secret123',
        ])->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
