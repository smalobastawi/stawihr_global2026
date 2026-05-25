<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\LeaveGroupSetting;
use App\Models\User;
use App\Repositories\LeaveRepository;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LeaveApiCalculateDaysTest extends TestCase
{
    /** @test */
    public function calculate_days_api_matches_repository_for_working_days_leave_type()
    {
        $user = User::where('user_name', 'smaloba3')->first();
        if (!$user) {
            $this->markTestSkipped('Test user smaloba3 not found in database.');
        }

        $employee = Employee::where('user_id', $user->id)->first();
        if (!$employee || !$employee->leaveGroup) {
            $this->markTestSkipped('Employee or leave group not found for smaloba3.');
        }

        $leaveType = $employee->applicableLeaveTypes()->first();
        if (!$leaveType) {
            $this->markTestSkipped('No applicable leave types for smaloba3.');
        }

        $setting = LeaveGroupSetting::where('leave_group_id', $employee->leaveGroup->id)
            ->where('leave_type_id', $leaveType->leave_type_id)
            ->first();

        if (!$setting || $setting->applicable_on !== 'working_days') {
            $this->markTestSkipped('No working_days leave type configured for smaloba3.');
        }

        Sanctum::actingAs($user);

        $fromDate = '2026-05-28';
        $toDate = '2026-05-31';

        $repository = app(LeaveRepository::class);
        $expectedDays = $repository->calculateTotalNumberOfLeaveDays(
            $fromDate,
            $toDate,
            $leaveType->leave_type_id,
            $employee->employee_id
        );

        $response = $this->postJson('/api/leave/calculate-days', [
            'leave_type_id' => $leaveType->leave_type_id,
            'application_from_date' => '28/05/2026',
            'application_to_date' => '31/05/2026',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.applicable_on', 'working_days');

        $this->assertSame(
            (float) $expectedDays,
            (float) $response->json('data.number_of_days')
        );
    }

    /** @test */
    public function apply_leave_uses_recalculated_working_days_not_calendar_span()
    {
        $user = User::where('user_name', 'smaloba3')->first();
        if (!$user) {
            $this->markTestSkipped('Test user smaloba3 not found in database.');
        }

        $employee = Employee::where('user_id', $user->id)->first();
        if (!$employee || !$employee->leaveGroup) {
            $this->markTestSkipped('Employee or leave group not found for smaloba3.');
        }

        $leaveType = $employee->applicableLeaveTypes()->first();
        if (!$leaveType) {
            $this->markTestSkipped('No applicable leave types for smaloba3.');
        }

        $setting = LeaveGroupSetting::where('leave_group_id', $employee->leaveGroup->id)
            ->where('leave_type_id', $leaveType->leave_type_id)
            ->first();

        if (!$setting || $setting->applicable_on !== 'working_days') {
            $this->markTestSkipped('No working_days leave type configured for smaloba3.');
        }

        Sanctum::actingAs($user);

        $start = now()->addMonths(8)->startOfMonth()->addDays(10);
        while ($start->isWeekend()) {
            $start = $start->addDay();
        }
        $fromDate = $start->format('Y-m-d');
        $toDate = $start->copy()->addDays(3)->format('Y-m-d');

        $repository = app(LeaveRepository::class);
        $expectedDays = $repository->calculateTotalNumberOfLeaveDays(
            $fromDate,
            $toDate,
            $leaveType->leave_type_id,
            $employee->employee_id
        );

        $response = $this->postJson('/api/leave/apply', [
            'leave_type_id' => $leaveType->leave_type_id,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'purpose' => 'Working days calculation API test',
        ]);

        if ($response->status() !== 200) {
            if ($response->status() === 422 && ($response->json('message') === 'Insufficient leave balance')) {
                $this->markTestSkipped('Insufficient leave balance for apply leave test.');
            }
            if ($response->status() === 400 && str_contains((string) $response->json('message'), 'leave application within this period')) {
                $this->markTestSkipped('Overlapping leave period in database for apply leave test.');
            }

            $this->fail(
                'Expected HTTP 200 but received '
                . $response->status()
                . ': '
                . ($response->getContent() ?: 'empty response')
            );
        }

        $response->assertJsonPath('status', 'success');

        $this->assertSame(
            (float) $expectedDays,
            (float) $response->json('data.requested_days')
        );
    }
}
