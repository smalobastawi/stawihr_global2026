<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Employee;
use App\Models\Payroll\PayrollPeriod;
use App\Models\Payroll\PayrollRecord;
use App\Models\Payroll\EmployeePayroll;
use App\Lib\Enumerations\GeneralStatus;
use App\Services\Payroll\PayrollCalculationServiceResolver;
use App\Services\Payroll\KenyanPayrollCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PayrollControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->employee = Employee::factory()->create([
            'status' => GeneralStatus::ACTIVE,
            'email' => 'test@example.com',
            'personal_email' => 'personal@example.com'
        ]);

        $this->employeePayroll = EmployeePayroll::factory()->create([
            'employee_id' => $this->employee->id
        ]);

        $this->payrollPeriod = PayrollPeriod::factory()->create([
            'start_date' => Carbon::now()->startOfMonth(),
            'end_date' => Carbon::now()->endOfMonth(),
            'status' => PayrollPeriod::STATUS_OPEN,
            'is_current' => true
        ]);

        $this->payrollRecord = PayrollRecord::factory()->create([
            'employee_payroll_id' => $this->employeePayroll->id,
            'payroll_period_id' => $this->payrollPeriod->id,
            'status' => PayrollRecord::STATUS_CALCULATED
        ]);
    }

    /** @test */
    public function it_can_display_payroll_dashboard()
    {
        $response = $this->get(route('payroll.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas(['stats', 'recentActivities', 'monthlyTrends', 'currentMonth']);
    }

    /** @test */
    public function it_can_display_payroll_index()
    {
        $response = $this->get(route('payroll.index'));

        $response->assertStatus(200);
        $response->assertViewHas(['payrollRecords', 'periods']);
    }

    /** @test */
    public function it_can_filter_payroll_index_by_period()
    {
        $response = $this->get(route('payroll.index', ['period_id' => $this->payrollPeriod->id]));

        $response->assertStatus(200);
        $response->assertViewHas('payrollRecords', function ($records) {
            return $records->count() > 0;
        });
    }

    /** @test */
    public function it_can_show_payroll_process_form()
    {
        $response = $this->get(route('payroll.process.form'));

        $response->assertStatus(200);
        $response->assertViewHas(['currentPeriod', 'periods', 'employees', 'totalEmployees']);
    }

    /** @test */
    public function it_can_process_payroll_for_period()
    {
        // Mock the payroll calculation service
        $mockService = $this->mock(KenyanPayrollCalculationService::class);
        $mockService->shouldReceive('calculatePeriodPayroll')
            ->once()
            ->andReturn([['status' => 'success']]);

        $mockResolver = $this->mock(PayrollCalculationServiceResolver::class);
        $mockResolver->shouldReceive('resolveByCountryId')->andReturn($mockService);

        $response = $this->post(route('payroll.process'), [
            'period_id' => $this->payrollPeriod->id
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /** @test */
    public function it_validates_period_id_when_processing_payroll()
    {
        $response = $this->post(route('payroll.process'), []);

        $response->assertSessionHasErrors('period_id');
    }

    /** @test */
    public function it_can_process_single_employee_payroll()
    {
        // Mock the payroll calculation service
        $mockService = $this->mock(KenyanPayrollCalculationService::class);
        $mockService->shouldReceive('calculatePeriodPayrollForOneEmployee')
            ->once()
            ->andReturn(['status' => 'success', 'payroll_record_id' => $this->payrollRecord->id]);

        $this->mock(PayrollCalculationServiceResolver::class)
            ->shouldReceive('resolveForEmployee')
            ->andReturn($mockService);

        $response = $this->get(route('payroll.process.single', [
            'period' => $this->payrollPeriod->id,
            'employeeID' => $this->employee->id
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /** @test */
    public function it_can_approve_payroll_records()
    {
        $response = $this->post(route('payroll.approve'), [
            'record_ids' => [$this->payrollRecord->id]
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('payroll_records', [
            'id' => $this->payrollRecord->id,
            'status' => PayrollRecord::STATUS_APPROVED
        ]);
    }

    /** @test */
    public function it_validates_record_ids_when_approving()
    {
        $response = $this->post(route('payroll.approve'), []);

        $response->assertSessionHasErrors('record_ids');
    }

    /** @test */
    public function it_can_mark_payroll_as_paid()
    {
        // First approve the record
        $this->payrollRecord->update(['status' => PayrollRecord::STATUS_APPROVED]);

        $response = $this->post(route('payroll.mark.paid'), [
            'record_ids' => [$this->payrollRecord->id],
            'payment_reference' => 'TEST123',
            'payment_date' => now()->format('Y-m-d')
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /** @test */
    public function it_can_show_payroll_record_details()
    {
        $response = $this->get(route('payroll.show', $this->payrollRecord));

        $response->assertStatus(200);
        $response->assertViewHas('payrollRecord');
    }

    /** @test */
    public function it_can_generate_payslip()
    {
        $response = $this->get(route('payroll.payslip', $this->payrollRecord));

        $response->assertStatus(200);
        $response->assertViewHas('payrollRecord');
    }

    /** @test */
    public function it_can_send_payslip_email()
    {
        Mail::fake();

        $response = $this->post(route('payroll.email.payslip', $this->payrollRecord), [
            'custom_message' => 'Test message'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        Mail::assertSent(SendPayslipEmail::class);
    }

    /** @test */
    public function it_handles_email_failure_when_sending_payslip()
    {
        // Create employee without email
        $employeeNoEmail = Employee::factory()->create([
            'email' => null,
            'personal_email' => null
        ]);
        
        $employeePayrollNoEmail = EmployeePayroll::factory()->create([
            'employee_id' => $employeeNoEmail->id
        ]);
        
        $payrollRecordNoEmail = PayrollRecord::factory()->create([
            'employee_payroll_id' => $employeePayrollNoEmail->id,
            'payroll_period_id' => $this->payrollPeriod->id
        ]);

        $response = $this->post(route('payroll.email.payslip', $payrollRecordNoEmail));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function it_can_send_mass_payslip_emails()
    {
        Mail::fake();

        $response = $this->post(route('payroll.email.payslips.mass'), [
            'record_ids' => [$this->payrollRecord->id],
            'custom_message' => 'Mass test message'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        Mail::assertSent(SendPayslipEmail::class);
    }

    /** @test */
    public function it_returns_api_stats()
    {
        $response = $this->get(route('payroll.api.stats'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'total_employees',
            'current_period',
            'total_processed',
            // ... other expected fields
        ]);
    }

    /** @test */
    public function it_calculates_employee_payroll_via_api()
    {
        $response = $this->post(route('payroll.api.calculate'), [
            'employee_payroll_id' => $this->employeePayroll->id,
            'period_id' => $this->payrollPeriod->id
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'payroll_record'
        ]);
    }

    /** @test */
    public function it_handles_errors_during_payroll_processing()
    {
        // Mock service to throw exception
        $mockService = $this->mock(KenyanPayrollCalculationService::class);
        $mockService->shouldReceive('calculatePeriodPayroll')
            ->once()
            ->andThrow(new \Exception('Test error'));

        $response = $this->post(route('payroll.process'), [
            'period_id' => $this->payrollPeriod->id
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}