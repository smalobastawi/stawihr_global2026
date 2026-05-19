<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Payroll\AllowanceType;
use Illuminate\Http\Request;

class AllowanceTypeController extends Controller
{
    /**
     * Display a listing of allowance types
     */
    public function index()
    {
        $allowanceTypes = AllowanceType::orderBy('name')->paginate(20);
        
        return view('admin.payroll.settings.allowance-types.index', compact('allowanceTypes'));
    }

    /**
     * Show the form for creating a new allowance type
     */
    public function create()
    {
        return view('admin.payroll.settings.allowance-types.create');
    }

    /**
     * Store a newly created allowance type
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:allowance_types,code',
            'description' => 'nullable|string',
            'default_calculation_type' => 'required|in:fixed,percentage,formula',
            'default_amount' => 'nullable|numeric|min:0',
            'default_percentage' => 'nullable|numeric|min:0|max:100',
            'is_taxable' => 'boolean',
            'is_pensionable' => 'boolean',
            'is_active' => 'boolean'
        ]);

        AllowanceType::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'default_calculation_type' => $request->default_calculation_type,
            'default_amount' => $request->default_amount,
            'default_percentage' => $request->default_percentage,
            'is_taxable' => $request->boolean('is_taxable', true),
            'is_pensionable' => $request->boolean('is_pensionable', false),
            'is_active' => $request->boolean('is_active', true),
            'created_by' => auth()->id()
        ]);

        return redirect()->route('payroll.settings.allowance-types.index')
                        ->with('success', 'Allowance type created successfully.');
    }

    /**
     * Display the specified allowance type
     */
    public function show(AllowanceType $allowanceType)
    {
        $allowanceType->load(['employeeAllowances.employeePayroll.employee']);
        
        return view('admin.payroll.settings.allowance-types.show', compact('allowanceType'));
    }

    /**
     * Show the form for editing the specified allowance type
     */
    public function edit(AllowanceType $allowanceType)
    {
        return view('admin.payroll.settings.allowance-types.edit', compact('allowanceType'));
    }

    /**
     * Update the specified allowance type
     */
    public function update(Request $request, AllowanceType $allowanceType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:allowance_types,code,' . $allowanceType->id,
            'description' => 'nullable|string',
            'default_calculation_type' => 'required|in:fixed,percentage,formula',
            'default_amount' => 'nullable|numeric|min:0',
            'default_percentage' => 'nullable|numeric|min:0|max:100',
            'is_taxable' => 'boolean',
            'is_pensionable' => 'boolean',
            'is_active' => 'boolean'
        ]);

        $allowanceType->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'default_calculation_type' => $request->default_calculation_type,
            'default_amount' => $request->default_amount,
            'default_percentage' => $request->default_percentage,
            'is_taxable' => $request->boolean('is_taxable'),
            'is_pensionable' => $request->boolean('is_pensionable'),
            'is_active' => $request->boolean('is_active'),
            'updated_by' => auth()->id()
        ]);

        return redirect()->route('payroll.settings.allowance-types.index')
                        ->with('success', 'Allowance type updated successfully.');
    }

    /**
     * Remove the specified allowance type
     */
    public function destroy(AllowanceType $allowanceType)
    {
        // Check if allowance type is being used
        if ($allowanceType->employeeAllowances()->count() > 0) {
            return redirect()->back()
                           ->with('error', 'Cannot delete allowance type that is being used by employees.');
        }

        $allowanceType->delete();

        return redirect()->route('payroll.settings.allowance-types.index')
                        ->with('success', 'Allowance type deleted successfully.');
    }

    /**
     * Toggle allowance type status
     */
    public function toggleStatus(AllowanceType $allowanceType)
    {
        $allowanceType->update([
            'is_active' => !$allowanceType->is_active,
            'updated_by' => auth()->id()
        ]);

        $status = $allowanceType->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
                        ->with('success', "Allowance type {$status} successfully.");
    }

    /**
     * Bulk create default allowance types
     */
    public function createDefaults()
    {
        $defaultAllowances = [
            [
                'name' => 'House Allowance',
                'code' => 'house_allowance',
                'description' => 'Monthly house allowance',
                'default_calculation_type' => 'fixed',
                'default_amount' => 15000,
                'is_taxable' => false,
                'is_pensionable' => false
            ],
            [
                'name' => 'Transport Allowance',
                'code' => 'transport_allowance',
                'description' => 'Monthly transport allowance',
                'default_calculation_type' => 'fixed',
                'default_amount' => 5000,
                'is_taxable' => false,
                'is_pensionable' => false
            ],
            [
                'name' => 'Medical Allowance',
                'code' => 'medical_allowance',
                'description' => 'Monthly medical allowance',
                'default_calculation_type' => 'fixed',
                'default_amount' => 3000,
                'is_taxable' => false,
                'is_pensionable' => false
            ],
            [
                'name' => 'Lunch Allowance',
                'code' => 'lunch_allowance',
                'description' => 'Daily lunch allowance',
                'default_calculation_type' => 'fixed',
                'default_amount' => 500,
                'is_taxable' => true,
                'is_pensionable' => false
            ],
            [
                'name' => 'Overtime Allowance',
                'code' => 'overtime_allowance',
                'description' => 'Overtime payment',
                'default_calculation_type' => 'percentage',
                'default_percentage' => 25,
                'is_taxable' => true,
                'is_pensionable' => true
            ],
            [
                'name' => 'Acting Allowance',
                'code' => 'acting_allowance',
                'description' => 'Acting position allowance',
                'default_calculation_type' => 'percentage',
                'default_percentage' => 20,
                'is_taxable' => true,
                'is_pensionable' => true
            ]
        ];

        $created = 0;
        foreach ($defaultAllowances as $allowanceData) {
            if (!AllowanceType::where('code', $allowanceData['code'])->exists()) {
                AllowanceType::create(array_merge($allowanceData, [
                    'is_active' => true,
                    'created_by' => auth()->id()
                ]));
                $created++;
            }
        }

        return redirect()->back()
                        ->with('success', "{$created} default allowance types created successfully.");
    }
}