<?php

namespace App\Http\Controllers\Performance;

use App\Http\Controllers\Controller;
use App\Models\Performance\PerformanceFocusArea;
use App\Models\Performance\PerformanceSetting;
use App\Models\Department;
use App\Models\Designation;
use App\Models\User;
use Illuminate\Http\Request;

class FocusAreaController extends Controller
{
    protected function ensureHrDefinedApproach()
    {
        if (PerformanceSetting::current()->isStaffDefined()) {
            return redirect()->route('performance.setting.index')
                ->with('error', 'HR focus area setup is disabled because your organization uses staff-defined goals. Change Appraisal Settings to enable HR-defined areas.');
        }

        return null;
    }

    public function index()
    {
        if ($redirect = $this->ensureHrDefinedApproach()) {
            return $redirect;
        }

        $results = PerformanceFocusArea::hrCatalog()->with(['department', 'designation', 'goals'])->get();
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.performance.focusArea.index', [
            'results' => $results,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function create()
    {
        if ($redirect = $this->ensureHrDefinedApproach()) {
            return $redirect;
        }

        $departments = Department::all();
        $designations = Designation::all();
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.performance.focusArea.form', [
            'departments' => $departments,
            'designations' => $designations,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function store(Request $request)
    {
        $input = $request->validate([
            'focus_area_name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:department,department_id',
            'designation_id' => 'nullable|exists:designation,designation_id',
            'is_active' => 'boolean',
        ]);

        $input['is_active'] = $request->has('is_active') ? 1 : 0;

        try {
            PerformanceFocusArea::create(array_merge($input, [
                'source' => 'hr',
                'employee_id' => null,
                'appraisal_id' => null,
            ]));
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('performance.focusArea.index')->with('success', 'Focus area saved successfully.');
        } else {
            return redirect()->route('performance.focusArea.index')->with('error', 'An error occurred: ' . $bug);
        }
    }

    public function edit($id)
    {
        $editModeData = PerformanceFocusArea::findOrFail($id);
        $departments = Department::all();
        $designations = Designation::all();
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.performance.focusArea.form', [
            'editModeData' => $editModeData,
            'departments' => $departments,
            'designations' => $designations,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function update(Request $request, $id)
    {
        $focusArea = PerformanceFocusArea::findOrFail($id);

        $input = $request->validate([
            'focus_area_name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:department,department_id',
            'designation_id' => 'nullable|exists:designation,designation_id',
            'is_active' => 'boolean',
        ]);

        $input['is_active'] = $request->has('is_active') ? 1 : 0;

        try {
            $focusArea->update($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('performance.focusArea.index')->with('success', 'Focus area updated successfully.');
        } else {
            return redirect()->route('performance.focusArea.index')->with('error', 'An error occurred: ' . $bug);
        }
    }

    public function destroy($id)
    {
        try {
            $focusArea = PerformanceFocusArea::findOrFail($id);
            $focusArea->delete();
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            echo "success";
        } elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
        }
    }
}
