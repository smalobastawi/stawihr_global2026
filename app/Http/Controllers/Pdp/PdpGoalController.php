<?php

namespace App\Http\Controllers\Pdp;

use App\Http\Controllers\Controller;
use App\Models\Pdp\PdpGoal;
use App\Models\Pdp\PdpPlan;
use App\Models\User;
use Illuminate\Http\Request;

class PdpGoalController extends Controller
{
    public function index($planId)
    {
        $plan = PdpPlan::with('goals')->findOrFail($planId);
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.pdp.goal.index', [
            'plan' => $plan,
            'results' => $plan->goals,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function store(Request $request, $planId)
    {
        $plan = PdpPlan::findOrFail($planId);

        if (!$plan->canBeEdited()) {
            return redirect()->route('pdp.plan.show', $planId)->with('error', 'This plan is locked or closed.');
        }

        $input = $request->validate([
            'goal_title' => 'required|string|max:255',
            'smart_objective' => 'required|string',
            'competency_area' => 'nullable|string|max:255',
            'success_criteria' => 'nullable|string',
            'development_actions' => 'nullable|string',
            'resources_needed' => 'nullable|string',
            'target_completion_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
        ]);

        $input['pdp_plan_id'] = $planId;
        $input['status'] = 'not_started';
        $input['overall_progress'] = 0;
        $input['sort_order'] = $plan->goals()->count() + 1;

        try {
            PdpGoal::create($input);
            return redirect()->route('pdp.goal.index', $planId)->with('success', 'Development goal added successfully.');
        } catch (\Exception $e) {
            return redirect()->route('pdp.goal.index', $planId)->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $editModeData = PdpGoal::findOrFail($id);
        $plan = PdpPlan::findOrFail($editModeData->pdp_plan_id);
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.pdp.goal.edit', [
            'editModeData' => $editModeData,
            'plan' => $plan,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function update(Request $request, $id)
    {
        $goal = PdpGoal::findOrFail($id);
        $plan = PdpPlan::findOrFail($goal->pdp_plan_id);

        if (!$plan->canBeEdited()) {
            return redirect()->route('pdp.goal.index', $goal->pdp_plan_id)->with('error', 'This plan is locked or closed.');
        }

        $input = $request->validate([
            'goal_title' => 'required|string|max:255',
            'smart_objective' => 'required|string',
            'competency_area' => 'nullable|string|max:255',
            'success_criteria' => 'nullable|string',
            'development_actions' => 'nullable|string',
            'resources_needed' => 'nullable|string',
            'target_completion_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:not_started,in_progress,on_track,at_risk,completed,deferred',
            'overall_progress' => 'nullable|integer|min:0|max:100',
        ]);

        try {
            $goal->update($input);
            return redirect()->route('pdp.goal.index', $goal->pdp_plan_id)->with('success', 'Development goal updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('pdp.goal.index', $goal->pdp_plan_id)->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            PdpGoal::findOrFail($id)->delete();
            echo 'success';
        } catch (\Exception $e) {
            echo 'error';
        }
    }
}
