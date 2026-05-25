<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PayslipApiTest extends TestCase
{
    /** @test */
    public function recent_payslips_returns_financial_year_summary_for_authenticated_employee()
    {
        $user = User::where('user_name', 'smaloba3')->first();
        if (!$user) {
            $this->markTestSkipped('Test user smaloba3 not found in database.');
        }

        $employee = Employee::where('user_id', $user->id)->first();
        $this->assertNotNull($employee, 'Employee record missing for smaloba3');

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/payroll/recent-payslips');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'data',
                'summary' => [
                    'net_earnings',
                    'gross_earnings',
                    'total_deductions',
                    'payslip_count',
                ],
                'financial_year' => [
                    'start_date',
                    'end_date',
                ],
            ]);

        $this->assertIsArray($response->json('data'));
    }

    /** @test */
    public function payslip_detail_returns_earnings_and_deductions_for_owned_record()
    {
        $user = User::where('user_name', 'smaloba3')->first();
        if (!$user) {
            $this->markTestSkipped('Test user smaloba3 not found in database.');
        }

        Sanctum::actingAs($user);

        $listResponse = $this->getJson('/api/payroll/recent-payslips');
        $listResponse->assertStatus(200);

        $payslips = $listResponse->json('data');
        if (empty($payslips)) {
            $this->markTestSkipped('No paid payslips in current financial year for smaloba3.');
        }

        $payslipId = $payslips[0]['id'];

        $detailResponse = $this->getJson('/api/payroll/payslip/' . $payslipId);
        $detailResponse->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'gross_salary',
                    'net_salary',
                    'earnings',
                    'deductions',
                ],
            ]);
    }
}
