<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChangePasswordApiTest extends TestCase
{
    /** @test */
    public function password_change_options_returns_for_authenticated_user()
    {
        $user = User::where('user_name', 'smaloba3')->first();
        if (!$user) {
            $this->markTestSkipped('Test user smaloba3 not found in database.');
        }

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/auth/password-change-options');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'requires_otp',
            ]);
    }

    /** @test */
    public function change_password_rejects_invalid_old_password()
    {
        $user = User::where('user_name', 'smaloba3')->first();
        if (!$user) {
            $this->markTestSkipped('Test user smaloba3 not found in database.');
        }

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/auth/change-password', [
            'oldPassword' => 'WrongPassword123!',
            'password' => 'NewPass123!',
            'password_confirmation' => 'NewPass123!',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    /** @test */
    public function my_work_shift_returns_shift_payload_for_authenticated_user()
    {
        $user = User::where('user_name', 'smaloba3')->first();
        if (!$user) {
            $this->markTestSkipped('Test user smaloba3 not found in database.');
        }

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/my-work-shift');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'employee',
                'work_shift',
            ]);
    }
}
