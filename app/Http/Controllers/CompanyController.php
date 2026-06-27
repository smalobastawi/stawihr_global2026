<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Lib\Enumerations\PayrollCountry;
use App\Support\CompanyContext;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::paginate(10);

        return view('admin.company.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $payrollCountries = PayrollCountry::toArray();

        return view('admin.company.create', compact('payrollCountries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyRequest $request)
    {
        $data = $this->prepareCompanyData($request->validated());
        $data['logo'] = $this->handleLogoUpload($request);

        Company::create($data);

        return redirect()->route('company.index')->with('success', 'Company created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        $employeeCount = $company->employees()->count();
        $departmentCount = $company->departments()->count();
        $activePayrollProfilesCount = $company->employeePayrollProfiles()->where('status', 'active')->count();

        return view('admin.company.show', compact('company', 'employeeCount', 'departmentCount', 'activePayrollProfilesCount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        $payrollCountries = PayrollCountry::toArray();

        return view('admin.company.edit', compact('company', 'payrollCountries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $data = $this->prepareCompanyData($request->validated());
        $logo = $this->handleLogoUpload($request, $company);

        if ($logo !== null) {
            $data['logo'] = $logo;
        }

        $company->update($data);

        return redirect()->route('company.index')->with('success', 'Company updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        try {
            // Check for linked records
            $employeeCount = $company->employees()->count();
            $departmentCount = $company->departments()->count();
            $payrollProfileCount = $company->employeePayrollProfiles()->count();
            $userCount = $company->users()->count();

            if ($employeeCount > 0 || $departmentCount > 0 || $payrollProfileCount > 0 || $userCount > 0) {
                $linkedRecords = [];
                if ($employeeCount > 0) {
                    $linkedRecords[] = "{$employeeCount} employee(s)";
                }
                if ($departmentCount > 0) {
                    $linkedRecords[] = "{$departmentCount} department(s)";
                }
                if ($payrollProfileCount > 0) {
                    $linkedRecords[] = "{$payrollProfileCount} payroll profile(s)";
                }
                if ($userCount > 0) {
                    $linkedRecords[] = "{$userCount} user(s)";
                }

                return redirect()->route('company.index')
                    ->with('error', 'Cannot delete company. It has linked records: ' . implode(', ', $linkedRecords) . '. Please remove or reassign these records before deleting.');
            }

            $company->delete();

            return redirect()->route('company.index')
                ->with('success', 'Company deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('company.index')
                ->with('error', 'Error deleting company: ' . $e->getMessage());
        }
    }

    /**
     * Switch the active company for SuperAdmin.
     */
    public function switch(Request $request)
    {
        $request->validate([
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $user = auth()->user();
        if (!CompanyContext::canSwitchCompanies()) {
            abort(403, 'Unauthorized');
        }

        $permittedCompanyIds = CompanyContext::permittedCompanyIds();

        // Read from the raw POST bag so middleware/request merges cannot override the selection.
        $submittedCompanyId = $request->request->has('company_id')
            ? $request->request->get('company_id')
            : null;

        if ($submittedCompanyId !== null && $submittedCompanyId !== '') {
            $companyId = (int) $submittedCompanyId;

            if (!in_array($companyId, $permittedCompanyIds, true)) {
                abort(403, 'You do not have access to this company.');
            }

            $request->session()->put([
                'active_company_id' => $companyId,
                'active_company_name' => Company::find($companyId)?->name,
            ]);
            $message = 'Company switched successfully.';
        } else {
            $request->session()->forget(['active_company_id', 'active_company_name']);
            $message = CompanyContext::isSuperAdmin()
                ? 'Switched to SuperAdmin mode - accessing all companies.'
                : 'Switched to all permitted companies.';
        }

        $request->session()->save();

        return redirect()
            ->route('home.dashboard')
            ->with('success', $message);
    }

    private function prepareCompanyData(array $data): array
    {
        if (isset($data['payroll_country'])) {
            $data['payroll_country'] = (int) $data['payroll_country'];
            $data['country'] = PayrollCountry::getName($data['payroll_country']);
        }

        if (array_key_exists('domain', $data) && $data['domain'] === '') {
            $data['domain'] = null;
        }

        if (isset($data['currency'])) {
            $data['currency'] = strtoupper($data['currency']);
        }

        foreach (['payroll_base_currency', 'default_payment_currency'] as $currencyField) {
            if (array_key_exists($currencyField, $data) && $data[$currencyField] === '') {
                $data[$currencyField] = null;
            } elseif (isset($data[$currencyField])) {
                $data[$currencyField] = strtoupper($data[$currencyField]);
            }
        }

        if (array_key_exists('allow_employee_payment_currency', $data)) {
            $data['allow_employee_payment_currency'] = (bool) $data['allow_employee_payment_currency'];
        }

        return $data;
    }

    private function handleLogoUpload(Request $request, ?Company $company = null): ?string
    {
        if (!$request->hasFile('logo')) {
            return $company?->logo;
        }

        $logo = $request->file('logo');
        $logoName = md5(time() . '_' . $logo->getClientOriginalName()) . '.' . $logo->getClientOriginalExtension();
        $uploadDir = public_path('uploads/company_logos');

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if ($company?->logo && file_exists($uploadDir . DIRECTORY_SEPARATOR . $company->logo)) {
            unlink($uploadDir . DIRECTORY_SEPARATOR . $company->logo);
        }

        $logo->move($uploadDir, $logoName);

        return $logoName;
    }
}