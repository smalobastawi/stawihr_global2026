<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EssBootstrapApiTest extends TestCase
{
    /** @test */
    public function ess_bootstrap_returns_critical_mobile_payload()
    {
        $user = User::where('user_name', 'smaloba3')->first();
        if (!$user) {
            $this->markTestSkipped('Test user smaloba3 not found in database.');
        }

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/ess/bootstrap');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'data' => [
                    'user',
                    'profile',
                    'leave_balances',
                    'leave_requests',
                    'payslips' => ['data', 'summary'],
                    'clock_status',
                    'notifications',
                    'notices',
                ],
            ]);

        $this->assertNotEmpty($response->json('data.leave_balances'));
    }

    /** @test */
    public function ess_leave_balances_returns_all_types_in_one_request()
    {
        $user = User::where('user_name', 'smaloba3')->first();
        if (!$user) {
            $this->markTestSkipped('Test user smaloba3 not found in database.');
        }

        $employee = Employee::where('user_id', $user->id)->first();
        $expectedCount = $employee?->applicableLeaveTypes()->count() ?? 0;
        if ($expectedCount === 0) {
            $this->markTestSkipped('No applicable leave types for smaloba3.');
        }

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/ess/leave/balances');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'leave_type',
                        'leave_type_id',
                        'balance',
                        'total_available',
                    ],
                ],
            ]);

        $this->assertCount($expectedCount, $response->json('data'));
    }

    /** @test */
    public function ess_supervisor_uses_authenticated_employee_record()
    {
        $user = User::where('user_name', 'smaloba3')->first();
        if (!$user) {
            $this->markTestSkipped('Test user smaloba3 not found in database.');
        }

        $employee = Employee::where('user_id', $user->id)->first();
        if (!$employee || !$employee->supervisor_id) {
            $this->markTestSkipped('No supervisor assigned for smaloba3.');
        }

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/ess/employee/supervisor');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'data' => ['supervisor_id', 'first_name', 'last_name'],
            ]);
    }
}
