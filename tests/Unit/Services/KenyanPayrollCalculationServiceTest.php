<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\Payroll\KenyanPayrollCalculationService;
use App\Models\Payroll\EmployeePayroll;
use App\Models\Payroll\PayrollPeriod;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KenyanPayrollCalculationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new KenyanPayrollCalculationService();
    }

    /** @test */
    public function it_calculates_employee_payroll_correctly()
    {
        $employeePayroll = EmployeePayroll::factory()->create([
            'basic_salary' => 50000,
            'housing_allowance' => 10000,
            'transport_allowance' => 5000
        ]);

        $period = PayrollPeriod::factory()->create();

        $result = $this->service->calculateEmployeePayroll($employeePayroll, $period);

        $this->assertNotNull($result);
        $this->assertGreaterThan(0, $result->gross_salary);
        $this->assertGreaterThan(0, $result->net_salary);
    }

    /** @test */
    public function it_calculates_tax_correctly_for_kenyan_brackets()
    {
        // Test different salary brackets for Kenyan tax calculation
        $testCases = [
            ['salary' => 20000, 'expected_tax' => 0], // Below taxable threshold
            ['salary' => 30000, 'expected_tax' => 850], // 10% bracket
            ['salary' => 60000, 'expected_tax' => 6850], // 15% bracket
            ['salary' => 100000, 'expected_tax' => 18850], // 20% bracket
            ['salary' => 200000, 'expected_tax' => 48850], // 25% bracket
        ];

        foreach ($testCases as $case) {
            $employeePayroll = EmployeePayroll::factory()->create([
                'basic_salary' => $case['salary']
            ]);

            $period = PayrollPeriod::factory()->create();

            $result = $this->service->calculateEmployeePayroll($employeePayroll, $period);
            
            $this->assertEquals($case['expected_tax'], $result->paye_tax);
        }
    }

    /** @test */
    public function it_calculates_nhif_correctly()
    {
        $testCases = [
            ['salary' => 5000, 'expected_nhif' => 150],
            ['salary' => 10000, 'expected_nhif' => 300],
            ['salary' => 20000, 'expected_nhif' => 600],
            ['salary' => 100000, 'expected_nhif' => 1700],
        ];

        foreach ($testCases as $case) {
            $employeePayroll = EmployeePayroll::factory()->create([
                'basic_salary' => $case['salary']
            ]);

            $period = PayrollPeriod::factory()->create();

            $result = $this->service->calculateEmployeePayroll($employeePayroll, $period);
            
            $this->assertEquals($case['expected_nhif'], $result->nhif_deductions);
        }
    }

    /** @test */
    public function it_calculates_nssf_correctly()
    {
        $employeePayroll = EmployeePayroll::factory()->create([
            'basic_salary' => 30000
        ]);

        $period = PayrollPeriod::factory()->create();

        $result = $this->service->calculateEmployeePayroll($employeePayroll, $period);
        
        // NSSF is 6% of pensionable earnings (capped at 18000)
        $expectedNssf = 18000 * 0.06;
        $this->assertEquals($expectedNssf, $result->nssf_deductions);
    }
}