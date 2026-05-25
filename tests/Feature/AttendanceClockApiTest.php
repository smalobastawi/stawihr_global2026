<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AttendanceClockApiTest extends TestCase
{
    /** @test */
    public function clock_status_returns_for_authenticated_user()
    {
        $user = User::where('user_name', 'smaloba3')->first();
        if (!$user) {
            $this->markTestSkipped('Test user smaloba3 not found in database.');
        }

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/attendance/clock-status');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'attendance_enabled',
                'ip_check_enabled',
                'has_work_shift',
                'can_check_in',
                'can_check_out',
                'is_checked_in',
                'is_checked_out',
            ]);
    }

    /** @test */
    public function checkin_requires_attendance_type()
    {
        $user = User::where('user_name', 'smaloba3')->first();
        if (!$user) {
            $this->markTestSkipped('Test user smaloba3 not found in database.');
        }

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/attendance/checkin', []);

        $response->assertStatus(422);
    }
}
