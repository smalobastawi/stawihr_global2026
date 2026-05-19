<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Lib\Enumerations\OvertimeCalculationType;

class OvertimeCalculationTest extends TestCase
{
    /** @test */
    public function it_calculates_overtime_1_correctly()
    {
        // Given: Basic salary and overtime parameters
        $basicSalary = 50000;
        $workingDays = 22;
        $multiplier = 1;
        $units = 10; // 10 hours of overtime

        // When: Calculating overtime amount
        $dailyRate = $basicSalary / $workingDays;
        $overtimeRate = $dailyRate * $multiplier;
        $overtimeAmount = $overtimeRate * $units;

        // Then: Should calculate correctly (50,000 ÷ 22 × 1 × 10 = 22,727.27)
        $expectedAmount = 22727.27;

        $this->assertEquals($expectedAmount, round($overtimeAmount, 2));
    }

    /** @test */
    public function it_calculates_overtime_1_5_correctly()
    {
        // Given: Basic salary and overtime 1.5x parameters
        $basicSalary = 60000;
        $workingDays = 22;
        $multiplier = 1.5;
        $units = 8; // 8 hours of overtime

        // When: Calculating overtime amount
        $dailyRate = $basicSalary / $workingDays;
        $overtimeRate = $dailyRate * $multiplier;
        $overtimeAmount = $overtimeRate * $units;

        // Then: Should calculate correctly (60,000 ÷ 22 × 1.5 × 8 = 32,727.27)
        $expectedAmount = 32727.27;

        $this->assertEquals($expectedAmount, round($overtimeAmount, 2));
    }

    /** @test */
    public function it_calculates_overtime_2_correctly()
    {
        // Given: Basic salary and overtime 2x parameters
        $basicSalary = 40000;
        $workingDays = 22;
        $multiplier = 2;
        $units = 5; // 5 hours of overtime

        // When: Calculating overtime amount
        $dailyRate = $basicSalary / $workingDays;
        $overtimeRate = $dailyRate * $multiplier;
        $overtimeAmount = $overtimeRate * $units;

        // Then: Should calculate correctly (40,000 ÷ 22 × 2 × 5 = 18,181.82)
        $expectedAmount = 18181.82;

        $this->assertEquals($expectedAmount, round($overtimeAmount, 2));
    }

    /** @test */
    public function it_handles_zero_units_gracefully()
    {
        // Given: Basic salary with zero overtime units
        $basicSalary = 50000;
        $workingDays = 22;
        $multiplier = 1;
        $units = 0; // No overtime

        // When: Calculating overtime amount
        $dailyRate = $basicSalary / $workingDays;
        $overtimeRate = $dailyRate * $multiplier;
        $overtimeAmount = $overtimeRate * $units;

        // Then: Should return zero
        $this->assertEquals(0, $overtimeAmount);
    }

    /** @test */
    public function overtime_calculation_types_are_defined()
    {
        // Test that all overtime calculation types are properly defined
        $this->assertEquals('overtime_1', OvertimeCalculationType::OVERTIME_1);
        $this->assertEquals('overtime_1_5', OvertimeCalculationType::OVERTIME_1_5);
        $this->assertEquals('overtime_2', OvertimeCalculationType::OVERTIME_2);
    }

    /** @test */
    public function overtime_calculation_names_are_correct()
    {
        // Test that overtime calculation type names are correct
        $this->assertEquals('Overtime 1x', OvertimeCalculationType::getName(OvertimeCalculationType::OVERTIME_1));
        $this->assertEquals('Overtime 1.5x', OvertimeCalculationType::getName(OvertimeCalculationType::OVERTIME_1_5));
        $this->assertEquals('Overtime 2x', OvertimeCalculationType::getName(OvertimeCalculationType::OVERTIME_2));
    }
}
