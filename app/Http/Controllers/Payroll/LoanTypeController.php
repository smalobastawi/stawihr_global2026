<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoanType;
use App\Lib\Enumerations\GeneralStatus;
use Illuminate\Support\Facades\Log;

class LoanTypeController extends Controller
{
    public function index()
    {
        $results = LoanType::orderBy('name', 'asc')->get();
        return view('admin.payroll.loans.types.index', compact('results'));
    }

    public function create()
    {
        return view('admin.payroll.loans.types.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:loan_types,name',
            'description' => 'nullable|string|max:1000',
            'max_amount' => 'nullable|numeric|min:0',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'max_duration_months' => 'required|integer|min:1|max:120',
            'status' => 'required|in:0,1',
        ]);

        try {
            $validated['created_by'] = auth()->id();
            LoanType::create($validated);
            return redirect()->route('loans.types.index')->with('success', 'Loan type created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating loan type: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while creating the loan type.');
        }
    }

    public function edit($id)
    {
        $editModeData = LoanType::findOrFail($id);
        return view('admin.payroll.loans.types.form', compact('editModeData'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:loan_types,name,' . $id,
            'description' => 'nullable|string|max:1000',
            'max_amount' => 'nullable|numeric|min:0',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'max_duration_months' => 'required|integer|min:1|max:120',
            'status' => 'required|in:0,1',
        ]);

        try {
            $validated['updated_by'] = auth()->id();
            LoanType::findOrFail($id)->update($validated);
            return redirect()->route('loans.types.index')->with('success', 'Loan type updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating loan type: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating the loan type.');
        }
    }

    public function destroy($id)
    {
        try {
            LoanType::findOrFail($id)->delete();
            echo "success";
        } catch (\Exception $e) {
            Log::error('Error deleting loan type: ' . $e->getMessage());
            echo "error";
        }
    }
}
