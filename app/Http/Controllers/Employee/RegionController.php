<?php

namespace App\Http\Controllers\Employee;

use App\Models\Region;
use App\Models\Employee;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRegionRequest;
use App\Http\Requests\UpdateRegionRequest;

class RegionController extends Controller
{
    public function index()
    {
        // Load regions with counts
        $regions = Region::withCount([
            'locations',
            'employees',
            'leaveApprovers'
        ])->with([
            'locations',
            'leaveApprovers'
        ])->get();

        // Add branch employee counts to each region
        $regions->each(function ($region) {
            $region->employees_count = $region->getTotalEmployeesThroughLocations();
        });

        return view('admin.employee.region.index', compact('regions'));
    }

    public function create()
    {
        $employees = Employee::with('department')
            ->get()
            ->mapWithKeys(function ($employee) {
                return [
                    $employee->employee_id => $employee->full_name .
                        ($employee->department ? ' (' . $employee->department->department_name . ')' : '')
                ];
            });

        return view('admin.employee.region.form', compact('employees'));
    }

    public function store(StoreRegionRequest $request)
    {
        $region = Region::create($request->validated());

        return redirect()->route('region.index')
            ->with('success', 'Region created successfully');
    }

    public function edit(Region $region)
    {
        $employees = Employee::with('department')
            ->get()
            ->mapWithKeys(function ($employee) {
                return [
                    $employee->employee_id => $employee->full_name .
                        ($employee->department ? ' (' . $employee->department->department_name . ')' : '')
                ];
            });

        return view('admin.employee.region.form', compact('region', 'employees'));
    }

    public function update(UpdateRegionRequest $request, Region $region)
    {
        $region->update($request->validated());

        return redirect()->route('region.index')
            ->with('success', 'Region updated successfully');
    }

    public function destroy(Region $region)
    {
        try {
            $region->delete();

            return response()->json([
                'success' => true,
                'message' => 'Region deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
