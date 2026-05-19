<?php

namespace App\Http\Controllers\Performance;

use App\Http\Controllers\Controller;
use App\Models\Performance\PerformanceFocusArea;
use App\Models\Performance\PerformanceGoal;
use App\Models\User;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function index($focusAreaId)
    {
        $focusArea = PerformanceFocusArea::findOrFail($focusAreaId);
        $results = PerformanceGoal::where('focus_area_id', $focusAreaId)->orderBy('sort_order')->get();
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.performance.goal.index', [
            'focusArea' => $focusArea,
            'results' => $results,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function create($focusAreaId)
    {
        $focusArea = PerformanceFocusArea::findOrFail($focusAreaId);
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.performance.goal.form', [
            'focusArea' => $focusArea,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function store(Request $request, $focusAreaId)
    {
        $input = $request->validate([
            'strategic_objective' => 'required|string|max:255',
            'performance_metric' => 'required|string|max:255',
            'performance_target' => 'required|string',
            'key_initiatives' => 'nullable|string',
            'itemized_weighting' => 'required|numeric|min:0|max:1',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $input['focus_area_id'] = $focusAreaId;
        $input['is_active'] = $request->has('is_active') ? 1 : 0;

        try {
            PerformanceGoal::create($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('performance.goal.index', $focusAreaId)->with('success', 'Goal saved successfully.');
        } else {
            return redirect()->route('performance.goal.index', $focusAreaId)->with('error', 'An error occurred: ' . $bug);
        }
    }

    public function edit($id)
    {
        $editModeData = PerformanceGoal::findOrFail($id);
        $focusArea = PerformanceFocusArea::findOrFail($editModeData->focus_area_id);
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.performance.goal.form', [
            'editModeData' => $editModeData,
            'focusArea' => $focusArea,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function update(Request $request, $id)
    {
        $goal = PerformanceGoal::findOrFail($id);

        $input = $request->validate([
            'strategic_objective' => 'required|string|max:255',
            'performance_metric' => 'required|string|max:255',
            'performance_target' => 'required|string',
            'key_initiatives' => 'nullable|string',
            'itemized_weighting' => 'required|numeric|min:0|max:1',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $input['is_active'] = $request->has('is_active') ? 1 : 0;

        try {
            $goal->update($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('performance.goal.index', $goal->focus_area_id)->with('success', 'Goal updated successfully.');
        } else {
            return redirect()->route('performance.goal.index', $goal->focus_area_id)->with('error', 'An error occurred: ' . $bug);
        }
    }

    public function destroy($id)
    {
        try {
            $goal = PerformanceGoal::findOrFail($id);
            $focusAreaId = $goal->focus_area_id;
            $goal->delete();
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            echo "success";
        } else {
            echo 'error';
        }
    }
}
