<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ForgotPasswordOtpTest extends TestCase
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

    public function test_password_reset_otp_has_expiry_and_is_marked_used_after_success(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'nhanvien@mobifone.vn',
            'password' => Hash::make('old-password'),
        ]);

        $this->post(route('password.email'), [
            'email' => $user->email,
        ])->assertRedirect(route('password.reset', ['email' => $user->email]));

        $reset = DB::table('password_resets')->where('email', $user->email)->first();

        $this->assertNotNull($reset);
        $this->assertMatchesRegularExpression('/^\d{6}$/', $reset->token);
        $this->assertNotNull($reset->expires_at);
        $this->assertNull($reset->used_at);

        $this->post(route('password.update'), [
            'email' => $user->email,
            'token' => $reset->token,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));
        $this->assertNotNull(DB::table('password_resets')->where('email', $user->email)->value('used_at'));

        $this->post(route('password.update'), [
            'email' => $user->email,
            'token' => $reset->token,
            'password' => 'another-password-123',
            'password_confirmation' => 'another-password-123',
        ])->assertSessionHasErrors('token');

        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));
    }

    public function test_expired_password_reset_otp_cannot_update_password(): void
    {
        $user = User::factory()->create([
            'email' => 'expired@mobifone.vn',
            'password' => Hash::make('old-password'),
        ]);

        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => '123456',
            'created_at' => now()->subMinutes(20),
            'expires_at' => now()->subMinutes(10),
            'used_at' => null,
        ]);

        $this->post(route('password.update'), [
            'email' => $user->email,
            'token' => '123456',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertSessionHasErrors('token');

        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }
}
