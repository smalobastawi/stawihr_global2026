<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EmployeeProfileApiTest extends TestCase
{
    /** @test */
    public function employee_profile_returns_for_authenticated_user()
    {
        $user = User::where('user_name', 'smaloba3')->first();
        if (!$user) {
            $this->markTestSkipped('Test user smaloba3 not found in database.');
        }

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/employee/profile');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'employee_id',
                'first_name',
                'last_name',
            ]);
    }
}
