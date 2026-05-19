<?php

namespace App\Http\Controllers\Pip;

use App\Http\Controllers\Controller;
use App\Models\Pip\PipPlan;
use App\Models\Pip\PipGoal;
use App\Models\User;
use Illuminate\Http\Request;

class PipGoalController extends Controller
{
    public function index($planId)
    {
        $plan = PipPlan::with('goals')->findOrFail($planId);
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.pip.goal.index', [
            'plan' => $plan,
            'results' => $plan->goals,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function store(Request $request, $planId)
    {
        $plan = PipPlan::findOrFail($planId);

        if (!$plan->canBeEdited()) {
            return redirect()->route('pip.plan.show', $planId)->with('error', 'This PIP is locked or closed.');
        }

        $input = $request->validate([
            'objective' => 'required|string',
            'action_required' => 'required|string',
            'target_kpi' => 'required|string',
            'deadline' => 'required|date',
        ]);

        $input['pip_id'] = $planId;
        $input['status'] = 'pending';

        try {
            PipGoal::create($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('pip.goal.index', $planId)->with('success', 'Goal added successfully.');
        } else {
            return redirect()->route('pip.goal.index', $planId)->with('error', 'An error occurred: ' . $bug);
        }
    }

    public function edit($id)
    {
        $editModeData = PipGoal::findOrFail($id);
        $plan = PipPlan::findOrFail($editModeData->pip_id);
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.pip.goal.edit', [
            'editModeData' => $editModeData,
            'plan' => $plan,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function update(Request $request, $id)
    {
        $goal = PipGoal::findOrFail($id);
        $plan = PipPlan::findOrFail($goal->pip_id);

        if (!$plan->canBeEdited()) {
            return redirect()->route('pip.goal.index', $goal->pip_id)->with('error', 'This PIP is locked or closed.');
        }

        $input = $request->validate([
            'objective' => 'required|string',
            'action_required' => 'required|string',
            'target_kpi' => 'required|string',
            'deadline' => 'required|date',
            'progress_notes' => 'nullable|string',
        ]);

        try {
            $goal->update($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('pip.goal.index', $goal->pip_id)->with('success', 'Goal updated successfully.');
        } else {
            return redirect()->route('pip.goal.index', $goal->pip_id)->with('error', 'An error occurred: ' . $bug);
        }
    }

    public function destroy($id)
    {
        try {
            $goal = PipGoal::findOrFail($id);
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

    public function updateStatus(Request $request, $id)
    {
        $goal = PipGoal::findOrFail($id);
        $status = $request->input('status');

        if (in_array($status, ['pending', 'in_progress', 'completed', 'overdue'])) {
            $goal->status = $status;
            $goal->save();
            return redirect()->route('pip.goal.index', $goal->pip_id)->with('success', 'Goal status updated.');
        }

        return redirect()->route('pip.goal.index', $goal->pip_id)->with('error', 'Invalid status.');
    }
}
