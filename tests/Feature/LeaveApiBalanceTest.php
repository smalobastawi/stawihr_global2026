<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LeaveApiBalanceTest extends TestCase
{
    /** @test */
    public function leave_balance_returns_total_available_for_employee_leave_type()
    {
        $user = User::where('user_name', 'smaloba3')->first();
        if (!$user) {
            $this->markTestSkipped('Test user smaloba3 not found in database.');
        }

        $employee = Employee::where('user_id', $user->id)->first();
        $this->assertNotNull($employee, 'Employee record missing for smaloba3');

        $leaveType = $employee->applicableLeaveTypes()->first();
        if (!$leaveType) {
            $this->markTestSkipped('No applicable leave types for smaloba3.');
        }

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/leave/balance?leave_type_id=' . $leaveType->leave_type_id);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'data' => [
                    'leave_type',
                    'leave_type_id',
                    'balance',
                    'regular_balance',
                    'total_available',
                    'used_days',
                    'pending_days',
                    'annual_entitlement',
                ],
            ]);

        $this->assertGreaterThanOrEqual(
            0,
            (float) $response->json('data.used_days')
        );
    }

    /** @test */
    public function leave_balance_by_name_matches_id_lookup()
    {
        $user = User::where('user_name', 'smaloba3')->first();
        if (!$user) {
            $this->markTestSkipped('Test user smaloba3 not found in database.');
        }

        $employee = Employee::where('user_id', $user->id)->first();
        $leaveType = $employee?->applicableLeaveTypes()->first();
        if (!$leaveType) {
            $this->markTestSkipped('No applicable leave types for smaloba3.');
        }

        Sanctum::actingAs($user);

        $byId = $this->getJson('/api/leave/balance?leave_type_id=' . $leaveType->leave_type_id);
        $byName = $this->getJson('/api/leave/balance?leave_type=' . urlencode($leaveType->leave_type_name));

        $byId->assertStatus(200);
        $byName->assertStatus(200);

        $this->assertEquals(
            $byId->json('data.total_available'),
            $byName->json('data.total_available')
        );
    }

    /** @test */
    public function apply_leave_accepts_leave_type_id_without_leave_type_name()
    {
        $user = User::where('user_name', 'smaloba3')->first();
        if (!$user) {
            $this->markTestSkipped('Test user smaloba3 not found in database.');
        }

        $employee = Employee::where('user_id', $user->id)->first();
        $leaveType = $employee?->applicableLeaveTypes()->first();
        if (!$leaveType) {
            $this->markTestSkipped('No applicable leave types for smaloba3.');
        }

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/leave/apply', [
            'leave_type_id' => $leaveType->leave_type_id,
            'from_date' => now()->addMonths(2)->startOfWeek()->format('Y-m-d'),
            'to_date' => now()->addMonths(2)->startOfWeek()->format('Y-m-d'),
            'purpose' => 'API validation test',
        ]);

        $this->assertNotEquals(422, $response->status(), $response->json('message') ?? 'Unexpected validation failure');
        $this->assertFalse(
            collect($response->json('errors.leave_type') ?? [])->contains(fn ($msg) => str_contains(strtolower($msg), 'required')),
            'leave_type should not be required when leave_type_id is provided'
        );
    }

    /** @test */
    public function leave_index_returns_current_financial_year_applications_for_employee()
    {
        $user = User::where('user_name', 'smaloba3')->first();
        if (!$user) {
            $this->markTestSkipped('Test user smaloba3 not found in database.');
        }

        $employee = Employee::where('user_id', $user->id)->first();
        $this->assertNotNull($employee);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/leaves?per_page=50');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'data' => ['data'],
                'financial_year' => ['start_date', 'end_date'],
            ]);

        $items = $response->json('data.data') ?? [];
        $expectedCount = \App\Models\LeaveApplication::where('employee_id', $employee->employee_id)
            ->whereBetween('application_date', [
                $response->json('financial_year.start_date'),
                $response->json('financial_year.end_date'),
            ])
            ->count();

        $this->assertCount($expectedCount, $items);
    }

    /** @test */
    public function leave_types_endpoint_returns_applicable_types_for_employee()
    {
        $user = User::where('user_name', 'smaloba3')->first();
        if (!$user) {
            $this->markTestSkipped('Test user smaloba3 not found in database.');
        }

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/leave-types');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $this->assertNotEmpty($response->json('data'));
    }
}
