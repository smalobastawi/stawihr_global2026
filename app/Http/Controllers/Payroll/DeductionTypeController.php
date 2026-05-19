<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payroll\DeductionType;

class DeductionTypeController extends Controller
{
    /**
     * Display a listing of deduction types
     */
    public function index()
    {
        $results = DeductionType::orderBy('name')
            ->paginate(20);

        return view('admin.payroll.deduction.index', compact('results'));
    }

    /**
     * Show the form for creating a new deduction type
     */
    public function create()
    {
        $calculationTypes = [
            'fixed_amount' => 'Fixed Amount',
            'percentage_of_basic' => 'Percentage of Basic Income',
            'daily_rate' => 'Daily Rate'
        ];

        return view('admin.payroll.deduction.form', compact('calculationTypes'));
    }

    /**
     * Store a newly created deduction type
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:deduction_types,code',
            'description' => 'nullable|string',
            'default_calculation_type' => 'required|in:fixed_amount,percentage_of_basic,daily_rate',
            'is_statutory' => 'boolean',
            'is_active' => 'boolean'
        ]);

        DeductionType::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'default_calculation_type' => $request->default_calculation_type,
            'is_statutory' => $request->boolean('is_statutory', false),
            'is_active' => $request->boolean('is_active', true),
            'created_by' => auth()->id()
        ]);

        return redirect()->route('payroll.settings.deduction-types.index')
            ->with('success', 'DeductionType type created successfully.');
    }

    /**
     * Display the specified deduction type
     */
    public function show(DeductionType $deductionType)
    {
        $deductionType->load(['employeeDeductions.employeePayroll.employee']);

        return view('admin.payroll.settings.deduction-types.show', compact('deductionType'));
    }

    /**
     * Show the form for editing the specified deduction type
     */
    public function edit($deductionType)
    {
        $editModeData = DeductionType::findOrFail($deductionType);
        $calculationTypes = [
            'fixed_amount' => 'Fixed Amount',
            'percentage_of_basic' => 'Percentage of Basic Income',
            'daily_rate' => 'Daily Rate'
        ];

        return view('admin.payroll.deduction.form', compact('editModeData', 'calculationTypes'));
    }

    /**
     * Update the specified deduction type
     */
    public function update(Request $request, $deductionType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'description' => 'nullable|string',
            'default_calculation_type' => 'required|in:fixed_amount,percentage_of_basic,daily_rate',
            'is_statutory' => 'boolean',
            'is_active' => 'boolean'
        ]);
        $deductionType = DeductionType::findOrFail($deductionType);


        $deductionType->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'default_calculation_type' => $request->default_calculation_type,
            'is_statutory' => $request->boolean('is_statutory'),
            'is_active' => $request->boolean('is_active'),
            'updated_by' => auth()->id()
        ]);

        return redirect()->route('payroll.settings.deduction-types.index')
            ->with('success', 'DeductionType type updated successfully.');
    }

    /**
     * Remove the specified deduction type
     */
    public function destroy($deductionType)
    {
        $data = DeductionType::findOrFail($deductionType);

        try {

            if ($data->employeeDeductions()->count() > 0) {
                return response()->json(['status' => 'error', 'message' => 'This deduction type cannot be deleted because it has been assigned to one or more employees.'], 422);
            }
            $data->delete();
            return response()->json(['status' => 'success', 'message' => 'DeductionType type deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to delete deduction type.'], 500);
        }
    }

    /**
     * Toggle deduction type status
     */
    public function toggleStatus(DeductionType $deductionType)
    {
        $deductionType->update([
            'is_active' => !$deductionType->is_active,
            'updated_by' => auth()->id()
        ]);

        $status = $deductionType->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "DeductionType type {$status} successfully.");
    }

    /**
     * Bulk create default deduction types
     */
    public function getDeductionTypeDetails($id)
    {
        $deductionType = DeductionType::findOrFail($id);
        return response()->json($deductionType);
    }

    public function createDefaults()
    {
        $defaultDeductions = [
            [
                'name' => 'Loan Repayment',
                'code' => 'loan_repayment',
                'description' => 'Employee loan repayment',
                'default_calculation_type' => 'fixed',
                'default_amount' => 5000,
                'is_statutory' => false
            ],
            [
                'name' => 'Advance Salary',
                'code' => 'advance_salary',
                'description' => 'Salary advance deduction',
                'default_calculation_type' => 'fixed',
                'default_amount' => 10000,
                'is_statutory' => false
            ],
            [
                'name' => 'Insurance Premium',
                'code' => 'insurance_premium',
                'description' => 'Life/Medical insurance premium',
                'default_calculation_type' => 'fixed',
                'default_amount' => 2000,
                'is_statutory' => false
            ],
            [
                'name' => 'Union Dues',
                'code' => 'union_dues',
                'description' => 'Trade union membership dues',
                'default_calculation_type' => 'percentage',
                'default_percentage' => 1.0,
                'is_statutory' => false
            ],
            [
                'name' => 'Welfare Contribution',
                'code' => 'welfare_contribution',
                'description' => 'Employee welfare fund contribution',
                'default_calculation_type' => 'fixed',
                'default_amount' => 500,
                'is_statutory' => false
            ],
            [
                'name' => 'SACCO Contribution',
                'code' => 'sacco_contribution',
                'description' => 'SACCO membership contribution',
                'default_calculation_type' => 'percentage',
                'default_percentage' => 10.0,
                'is_statutory' => false
            ],
            [
                'name' => 'Disciplinary Fine',
                'code' => 'disciplinary_fine',
                'description' => 'Disciplinary action fine',
                'default_calculation_type' => 'fixed',
                'default_amount' => 1000,
                'is_statutory' => false
            ],
            [
                'name' => 'Uniform DeductionType',
                'code' => 'uniform_deduction',
                'description' => 'Company uniform cost deduction',
                'default_calculation_type' => 'fixed',
                'default_amount' => 3000,
                'is_statutory' => false
            ],
            // Statutory deductions
            [
                'name' => 'PAYE Tax',
                'code' => 'paye',
                'description' => 'Pay As You Earn tax',
                'default_calculation_type' => 'formula',
                'is_statutory' => true
            ],
            [
                'name' => 'NSSF Contribution',
                'code' => 'nssf',
                'description' => 'National Social Security Fund contribution',
                'default_calculation_type' => 'formula',
                'is_statutory' => true
            ],
            [
                'name' => 'SHIF Contribution',
                'code' => 'shif',
                'description' => 'Social Health Insurance Fund contribution',
                'default_calculation_type' => 'formula',
                'is_statutory' => true
            ],
            [
                'name' => 'Housing Levy',
                'code' => 'housing_levy',
                'description' => 'Affordable Housing Levy (1.5%)',
                'default_calculation_type' => 'formula',
                'is_statutory' => true
            ]
        ];

        $created = 0;
        foreach ($defaultDeductions as $deductionData) {
            if (!DeductionType::where('code', $deductionData['code'])->exists()) {
                DeductionType::create(array_merge($deductionData, [
                    'is_active' => true,
                    'created_by' => auth()->id()
                ]));
                $created++;
            }
        }

        return redirect()->back()
            ->with('success', "{$created} default deduction types created successfully.");
    }
}
