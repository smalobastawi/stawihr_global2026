<?php

namespace Tests\Unit\Services;

use App\Lib\Enumerations\PayrollCountry;
use App\Services\Payroll\KenyanPayrollCalculationService;
use App\Services\Payroll\PayrollCalculationServiceResolver;
use App\Services\Payroll\RwandaPayrollCalculationService;
use Tests\TestCase;

class PayrollCalculationServiceResolverTest extends TestCase
{

    /** @test */
    public function it_resolves_kenyan_service_by_default(): void
    {
        $resolver = app(PayrollCalculationServiceResolver::class);

        $this->assertInstanceOf(KenyanPayrollCalculationService::class, $resolver->resolveByCountryId(PayrollCountry::KENYA));
        $this->assertInstanceOf(KenyanPayrollCalculationService::class, $resolver->resolveForCompany(null));
    }

    /** @test */
    public function it_maps_each_supported_country_to_a_service_class(): void
    {
        $resolver = app(PayrollCalculationServiceResolver::class);
        $expected = [
            PayrollCountry::KENYA => KenyanPayrollCalculationService::class,
            PayrollCountry::RWANDA => RwandaPayrollCalculationService::class,
            PayrollCountry::UGANDA => \App\Services\Payroll\UgandaPayrollCalculationService::class,
            PayrollCountry::TANZANIA => \App\Services\Payroll\TanzaniaPayrollCalculationService::class,
            PayrollCountry::SOUTH_SUDAN => \App\Services\Payroll\SouthSudanPayrollCalculationService::class,
            PayrollCountry::SOMALIA => \App\Services\Payroll\SomaliaPayrollCalculationService::class,
            PayrollCountry::BURUNDI => \App\Services\Payroll\BurundiPayrollCalculationService::class,
            PayrollCountry::SOUTH_AFRICA => \App\Services\Payroll\SouthAfricaPayrollCalculationService::class,
        ];

        foreach ($expected as $countryId => $class) {
            $this->assertInstanceOf($class, $resolver->resolveByCountryId($countryId));
        }
    }
}
