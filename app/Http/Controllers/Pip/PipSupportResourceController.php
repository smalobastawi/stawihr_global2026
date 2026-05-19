<?php

namespace App\Http\Controllers\Pip;

use App\Http\Controllers\Controller;
use App\Models\Pip\PipPlan;
use App\Models\Pip\PipSupportResource;
use App\Models\User;
use Illuminate\Http\Request;

class PipSupportResourceController extends Controller
{
    public function index($planId)
    {
        $plan = PipPlan::with('supportResources')->findOrFail($planId);
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.pip.support.index', [
            'plan' => $plan,
            'results' => $plan->supportResources,
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
            'support_type' => 'required|in:training,mentorship,tools,counseling,other',
            'description' => 'required|string',
            'provider' => 'required|in:hr,supervisor,external,peer',
            'scheduled_date' => 'nullable|date',
        ]);

        $input['pip_id'] = $planId;
        $input['status'] = 'planned';

        try {
            PipSupportResource::create($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('pip.support.index', $planId)->with('success', 'Support resource added successfully.');
        } else {
            return redirect()->route('pip.support.index', $planId)->with('error', 'An error occurred: ' . $bug);
        }
    }

    public function edit($id)
    {
        $editModeData = PipSupportResource::findOrFail($id);
        $plan = PipPlan::findOrFail($editModeData->pip_id);
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.pip.support.edit', [
            'editModeData' => $editModeData,
            'plan' => $plan,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function update(Request $request, $id)
    {
        $resource = PipSupportResource::findOrFail($id);
        $plan = PipPlan::findOrFail($resource->pip_id);

        if (!$plan->canBeEdited()) {
            return redirect()->route('pip.support.index', $resource->pip_id)->with('error', 'This PIP is locked or closed.');
        }

        $input = $request->validate([
            'support_type' => 'required|in:training,mentorship,tools,counseling,other',
            'description' => 'required|string',
            'provider' => 'required|in:hr,supervisor,external,peer',
            'scheduled_date' => 'nullable|date',
        ]);

        try {
            $resource->update($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('pip.support.index', $resource->pip_id)->with('success', 'Support resource updated successfully.');
        } else {
            return redirect()->route('pip.support.index', $resource->pip_id)->with('error', 'An error occurred: ' . $bug);
        }
    }

    public function destroy($id)
    {
        try {
            $resource = PipSupportResource::findOrFail($id);
            $resource->delete();
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
        $resource = PipSupportResource::findOrFail($id);
        $status = $request->input('status');

        if (in_array($status, ['planned', 'in_progress', 'completed', 'cancelled'])) {
            $resource->status = $status;
            $resource->save();
            return redirect()->route('pip.support.index', $resource->pip_id)->with('success', 'Support resource status updated.');
        }

        return redirect()->route('pip.support.index', $resource->pip_id)->with('error', 'Invalid status.');
    }
}
