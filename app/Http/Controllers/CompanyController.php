<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
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
        return view('admin.company.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyRequest $request)
    {
        $company = Company::create($request->validated());

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
        return view('admin.company.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $company->update($request->validated());

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
        if (!$user->hasRole('SuperAdmin')) {
            abort(403, 'Unauthorized');
        }

        if ($request->company_id) {
            session(['active_company_id' => $request->company_id]);
            $message = 'Company switched successfully.';
        } else {
            session()->forget('active_company_id');
            $message = 'Switched to SuperAdmin mode - accessing all companies.';
        }

        return redirect()->back()->with('success', $message);
    }
}