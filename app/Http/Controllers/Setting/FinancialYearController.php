<?php

namespace App\Http\Controllers\Setting;

use App\Models\Company;
use App\Models\FinancialYear;
use App\Http\Requests\StoreFinancialYearRequest;
use App\Http\Requests\UpdateFinancialYearRequest;
use App\Http\Controllers\Controller;
use App\Models\LeaveApplication;
use App\Models\Payroll;
use App\Models\User;
use App\Support\CompanyContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinancialYearController extends Controller
{
    public function index()
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $showAllCompanies = CompanyContext::isAllCompaniesMode();

        $results = FinancialYear::with('company')
            ->when($showAllCompanies, fn ($query) => $query->withoutGlobalScope('company'))
            ->orderByDesc('start_date')
            ->get();

        return view('admin.setting.financial_years.index', [
            'results' => $results,
            'signed_in_user_role' => $signed_in_user_role,
            'showAllCompanies' => $showAllCompanies,
            'activeCompanyId' => CompanyContext::activeCompanyId(),
        ]);
    }

    public function create()
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.setting.financial_years.form', [
            'signed_in_user_role' => $signed_in_user_role,
            'companies' => $this->assignableCompanies(),
            'activeCompanyId' => CompanyContext::activeCompanyId(),
        ]);
    }

    public function store(StoreFinancialYearRequest $request)
    {
        $input = $request->all();
        $input['start_date'] = dateConvertFormtoDB($input['start_date']);
        $input['end_date'] = dateConvertFormtoDB($input['end_date']);
        $input['uuid'] = uniqid();
        $input['created_by'] = Auth::user()->id;
        $input['company_id'] = $this->resolveCompanyId($request);

        if (!$input['company_id']) {
            return redirect()->route('financial_year.create')
                ->withInput()
                ->with('error', 'Select a company before creating a financial year.');
        }

        try {
            FinancialYear::create($input);

            return redirect()->route('financial_year.index')->with('success', 'Financial Year successfully saved.');
        } catch (\Illuminate\Database\QueryException $e) {
            $errorMessage = 'Database error: ';
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $errorMessage .= 'A financial year with this name already exists for the selected company.';
            } elseif (str_contains($e->getMessage(), 'foreign key')) {
                $errorMessage .= 'This record is linked to other data and cannot be processed.';
            } else {
                $errorMessage .= $e->getMessage();
            }

            return redirect()->route('financial_year.index')->with('error', $errorMessage);
        } catch (\Exception $e) {
            return redirect()->route('financial_year.index')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $editModeData = FinancialYear::withoutGlobalScope('company')->findOrFail($id);

        return view('admin.setting.financial_years.form', [
            'editModeData' => $editModeData,
            'signed_in_user_role' => $signed_in_user_role,
            'companies' => $this->assignableCompanies(),
            'activeCompanyId' => CompanyContext::activeCompanyId(),
        ]);
    }

    public function update(UpdateFinancialYearRequest $request, $id)
    {
        $financialYear = FinancialYear::withoutGlobalScope('company')->findOrFail($id);
        $input = $request->all();
        $input['start_date'] = dateConvertFormtoDB($input['start_date']);
        $input['end_date'] = dateConvertFormtoDB($input['end_date']);
        $input['updated_by'] = Auth::user()->id;

        if ($request->filled('company_id')) {
            $input['company_id'] = $this->resolveCompanyId($request);
        }

        try {
            $financialYear->update($input);

            return redirect()->route('financial_year.index')->with('success', 'Financial Year successfully updated.');
        } catch (\Illuminate\Database\QueryException $e) {
            $errorMessage = 'Database error: ';
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $errorMessage .= 'A financial year with this name already exists for the selected company.';
            } elseif (str_contains($e->getMessage(), 'foreign key')) {
                $errorMessage .= 'This record is linked to other data and cannot be processed.';
            } else {
                $errorMessage .= $e->getMessage();
            }

            return redirect()->route('financial_year.index')->with('error', $errorMessage);
        } catch (\Exception $e) {
            return redirect()->route('financial_year.index')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $countPayroll = Payroll::where('financial_year_id', '=', $id)->count();
        $countLeaves = LeaveApplication::where('financial_year_id', '=', $id)->count();

        if ($countPayroll > 0 || $countLeaves > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete: This financial year is linked to payroll or leave records.',
            ]);
        }

        try {
            $financialYear = FinancialYear::withoutGlobalScope('company')->findOrFail($id);
            $financialYear->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Financial Year successfully deleted.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting financial year: ' . $e->getMessage(),
            ]);
        }
    }

    private function assignableCompanies()
    {
        $permittedIds = CompanyContext::permittedCompanyIds();

        if (empty($permittedIds)) {
            return collect();
        }

        return Company::whereIn('id', $permittedIds)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    private function resolveCompanyId(Request $request): ?int
    {
        $companyId = $request->input('company_id') ?: CompanyContext::defaultCompanyIdForNewRecord();

        if (!$companyId) {
            return null;
        }

        $permittedIds = CompanyContext::permittedCompanyIds();
        if (!empty($permittedIds) && !in_array((int) $companyId, $permittedIds, true)) {
            abort(403, 'You do not have access to this company.');
        }

        return (int) $companyId;
    }
}
