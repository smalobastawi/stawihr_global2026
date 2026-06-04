<?php

namespace Tests\Unit\Services;

use App\Lib\Enumerations\PayrollCountry;
use App\Services\Payroll\BurundiPayrollCalculationService;
use App\Services\Payroll\Concerns\CalculatesProgressivePaye;
use App\Services\Payroll\PayrollCalculationServiceResolver;
use App\Services\Payroll\RwandaPayrollCalculationService;
use App\Services\Payroll\SouthAfricaPayrollCalculationService;
use App\Services\Payroll\SouthSudanPayrollCalculationService;
use App\Services\Payroll\TanzaniaPayrollCalculationService;
use App\Services\Payroll\TaxRules\CountryPayrollTaxRules;
use App\Services\Payroll\UgandaPayrollCalculationService;
use Tests\TestCase;

class PayrollCountryPayeCalculationTest extends TestCase
{
    use CalculatesProgressivePaye;

    /** @test */
    public function payroll_country_enum_lists_all_supported_jurisdictions(): void
    {
        $countries = PayrollCountry::toArray();

        $this->assertCount(8, $countries);
        $this->assertArrayHasKey(PayrollCountry::KENYA, $countries);
        $this->assertArrayHasKey(PayrollCountry::SOUTH_AFRICA, $countries);
    }

    /** @test */
    public function resolver_returns_country_specific_service(): void
    {
        $resolver = app(PayrollCalculationServiceResolver::class);

        $this->assertInstanceOf(RwandaPayrollCalculationService::class, $resolver->resolveByCountryId(PayrollCountry::RWANDA));
        $this->assertInstanceOf(UgandaPayrollCalculationService::class, $resolver->resolveByCountryId(PayrollCountry::UGANDA));
        $this->assertInstanceOf(SouthAfricaPayrollCalculationService::class, $resolver->resolveByCountryId(PayrollCountry::SOUTH_AFRICA));
    }

    /** @test */
    public function rwanda_paye_uses_rra_monthly_bands(): void
    {
        $tax = $this->calculateMarginalPaye(250000, CountryPayrollTaxRules::rwandaPayeBands());

        // 40k @ 10% + 100k @ 20% + 50k @ 30% = 4000 + 20000 + 15000 = 39000
        $this->assertEqualsWithDelta(39000.0, $tax, 0.5);
    }

    /** @test */
    public function uganda_paye_applies_surtax_above_ten_million(): void
    {
        $belowThreshold = $this->calculateUgandaPaye(9500000);
        $aboveThreshold = $this->calculateUgandaPaye(10500000);

        $this->assertGreaterThan($belowThreshold, $aboveThreshold);
    }

    /** @test */
    public function tanzania_paye_uses_tra_cumulative_steps(): void
    {
        $tax = $this->calculateStepPaye(600000, CountryPayrollTaxRules::tanzaniaPayeSteps());

        $this->assertEquals(36000.0, $tax);
    }

    /** @test */
    public function south_sudan_paye_uses_nra_bands(): void
    {
        $tax = $this->calculateMarginalPaye(100000, CountryPayrollTaxRules::southSudanPayeBands());

        // 20k@0 + 20k@5% + 17k@10% + 33k@15% + 10k@20% = 0+1000+1700+4950+2000 = 9650
        $this->assertEqualsWithDelta(9650.0, $tax, 0.5);
    }

    /** @test */
    public function burundi_paye_uses_obr_monthly_bands(): void
    {
        $tax = $this->calculateMarginalPaye(400000, CountryPayrollTaxRules::burundiPayeBands());

        // 150k@0 + 150k@20% + 100k@30% = 0 + 30000 + 30000 = 60000
        $this->assertEqualsWithDelta(60000.0, $tax, 0.5);
    }

    /** @test */
    public function south_africa_paye_applies_primary_rebate(): void
    {
        $annualTaxable = 300000;
        $annualTax = $this->calculateSouthAfricaAnnualTax($annualTaxable);
        $monthly = $this->calculateSouthAfricaPaye($annualTaxable / 12, null, 30);

        $rebate = CountryPayrollTaxRules::southAfricaTaxRebates()['primary'];
        $expectedMonthly = max(0, $annualTax - $rebate) / 12;

        $this->assertEquals(round($expectedMonthly, 2), $monthly);
    }
}
