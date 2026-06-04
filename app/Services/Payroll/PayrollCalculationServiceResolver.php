<?php

namespace App\Services\Payroll;

use App\Lib\Enumerations\PayrollCountry;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Payroll\EmployeePayroll;
use InvalidArgumentException;

class PayrollCalculationServiceResolver
{
    /** @var array<int, KenyanPayrollCalculationService> */
    protected array $instances = [];

    public function resolveForCompany(?Company $company): KenyanPayrollCalculationService
    {
        $countryId = $company?->payroll_country ?? PayrollCountry::KENYA;

        return $this->resolveByCountryId((int) $countryId);
    }

    public function resolveForEmployee(Employee $employee): KenyanPayrollCalculationService
    {
        $company = $employee->company_id
            ? Company::find($employee->company_id)
            : null;

        return $this->resolveForCompany($company);
    }

    public function resolveForEmployeePayroll(EmployeePayroll $employeePayroll): KenyanPayrollCalculationService
    {
        $employee = Employee::find($employeePayroll->employee_id);

        if (!$employee) {
            return $this->resolveByCountryId(PayrollCountry::KENYA);
        }

        return $this->resolveForEmployee($employee);
    }

    public function resolveByCountryId(int $countryId): KenyanPayrollCalculationService
    {
        if (!PayrollCountry::isSupported($countryId)) {
            throw new InvalidArgumentException(
                'Unsupported payroll country. Configure a supported country on the company record.'
            );
        }

        if (isset($this->instances[$countryId])) {
            return $this->instances[$countryId];
        }

        $this->instances[$countryId] = match ($countryId) {
            PayrollCountry::KENYA => app(KenyanPayrollCalculationService::class),
            PayrollCountry::RWANDA => app(RwandaPayrollCalculationService::class),
            PayrollCountry::UGANDA => app(UgandaPayrollCalculationService::class),
            PayrollCountry::TANZANIA => app(TanzaniaPayrollCalculationService::class),
            PayrollCountry::SOUTH_SUDAN => app(SouthSudanPayrollCalculationService::class),
            PayrollCountry::SOMALIA => app(SomaliaPayrollCalculationService::class),
            PayrollCountry::BURUNDI => app(BurundiPayrollCalculationService::class),
            PayrollCountry::SOUTH_AFRICA => app(SouthAfricaPayrollCalculationService::class),
            default => app(KenyanPayrollCalculationService::class),
        };

        return $this->instances[$countryId];
    }
}
