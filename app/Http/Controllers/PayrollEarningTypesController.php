<?php

namespace App\Http\Controllers;

use App\Models\PayrollEarningTypes;
use Illuminate\Http\Request;
use App\Lib\Enumerations\CalculationTypes;

class PayrollEarningTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $results = PayrollEarningTypes::all();
        return view('admin.payroll.setup.earning_types.index', compact('results'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $calculationTypes = CalculationTypes::toArray();
        return view('admin.payroll.setup.earning_types.form', compact('calculationTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'calculation_type' => 'required|in:' . implode(',', array_keys(CalculationTypes::toArray())),
            'status' => 'required|integer|in:0,1',
        ]);

        $validated['taxable'] = $request->has('taxable');
        $validated['is_pensionable'] = $request->has('is_pensionable');
        // Removed 'is_recurring' from here

        PayrollEarningTypes::create($validated);

        return redirect()->route('earning_types.index')->with('success', 'Earning type created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $editModeData = PayrollEarningTypes::findOrFail($id);
        $calculationTypes = CalculationTypes::toArray();
        return view('admin.payroll.setup.earning_types.form', compact('editModeData', 'calculationTypes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'calculation_type' => 'required|in:' . implode(',', array_keys(CalculationTypes::toArray())),
            'status' => 'required|integer|in:0,1',
        ]);

        $validated['taxable'] = $request->has('taxable');
        $validated['is_pensionable'] = $request->has('is_pensionable');
        // Removed 'is_recurring' from here

        $earningType = PayrollEarningTypes::findOrFail($id);
        $earningType->update($validated);

        return redirect()->route('earning_types.index')->with('success', 'Earning type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $earningType = PayrollEarningTypes::findOrFail($id);
            $earningType->delete();
            return response()->json(['status' => 'success', 'message' => 'Earning type deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to delete earning type.'], 500);
        }
    }

    public function getDetails($id)
    {
        $earningType = PayrollEarningTypes::findOrFail($id);
        return response()->json($earningType);
    }
}