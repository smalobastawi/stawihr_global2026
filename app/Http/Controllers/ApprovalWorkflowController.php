<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Lib\Enumerations\GeneralStatus;
use App\Models\ApprovalWorkflow;
use App\Models\ApprovalStep;
use App\Models\User;
use Illuminate\Http\Request;

class ApprovalWorkflowController extends Controller
{
    public function index()
    {
        $workflows = ApprovalWorkflow::with('steps.assignments.user')->get();
        return view('admin.approval-workflows.index', compact('workflows'));
    }

    public function create()
    {
        $models = [
            'Payroll\PayrollRecord',
            'Payroll\EmployeePayroll',
            'Payroll\PayrollPeriod',
            'EmployeeEarnings',
            'EmployeeDeductions',
            'Advances',
            'Recruitment\JobRequisition',
        ];

        $users = User::where('status', GeneralStatus::ACTIVE)->get();

        return view('admin.approval-workflows.create', compact('models', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'model_type' => 'required|string',
                'reviewer_levels' => 'required|integer|min:0|max:5',
                'reviewer_required_levels' => 'required|integer|lte:reviewer_levels',
                'approver_levels' => 'required|integer|min:0|max:5',
                'approver_required_levels' => 'required|integer|lte:approver_levels',
                'assignments' => 'array'
            ],
            [
                'reviewer_required_levels.lte' => 'Required reviewer levels cannot exceed total reviewer levels',
                'approver_required_levels.lte' => 'Required approver levels cannot exceed total approver levels'
            ]
        );

        $workflow = ApprovalWorkflow::create([
            'model_type' => 'App\\Models\\' . str_replace('_', '\\', $validated['model_type']),
            'reviewer_config' => [
                'levels' => $validated['reviewer_levels'],
                'required_levels' => $validated['reviewer_required_levels']
            ],
            'approver_config' => [
                'levels' => $validated['approver_levels'],
                'required_levels' => $validated['approver_required_levels']
            ]
        ]);


        $workflow->initializeWorkflow();

        if (!empty($validated['assignments'])) {
            $stepIds = $workflow->steps()->pluck('id');
            foreach ($validated['assignments'] as $stepId => $userIds) {
                if (!$stepIds->contains($stepId)) {
                    abort(422, 'Invalid step ID in assignments');
                }
            }
        }
        // Assign users to steps
        if (!empty($validated['assignments'])) {
            foreach ($validated['assignments'] as $stepId => $userIds) {
                $step = ApprovalStep::find($stepId);
                if ($step) {
                    foreach ($userIds as $userId) {
                        $step->assignments()->create(['user_id' => $userId]);
                    }
                }
            }
        }

        return redirect()->route('approval-workflows.edit', $workflow->id)
            ->with('success', 'Workflow created successfully');
    }

    public function edit(ApprovalWorkflow $workflow)
    {
        $workflow->load('steps.assignments.user');
        $users = User::where('status', GeneralStatus::ACTIVE)->where('id', '!=', Auth()->id())->get();

        return view('admin.approval-workflows.edit', compact('workflow', 'users'));
    }

    public function update(Request $request, ApprovalWorkflow $workflow)
    {
        $validated = $request->validate(
            [
                'reviewer_levels' => 'required|integer|min:0|max:5',
                'reviewer_required_levels' => 'required|integer|lte:reviewer_levels',
                'approver_levels' => 'required|integer|min:0|max:5',
                'approver_required_levels' => 'required|integer|lte:approver_levels',
                'is_active' => 'boolean',
                'assignments' => 'array'
            ],
            [
                'reviewer_required_levels.lte' => 'Required reviewer levels cannot exceed total reviewer levels',
                'approver_required_levels.lte' => 'Required approver levels cannot exceed total approver levels'
            ]
        );

        // Get existing steps with their assignments before deletion
        $oldSteps = $workflow->steps()->with('assignments')->get()->keyBy('id');

        // Update workflow config
        $workflow->update([
            'reviewer_config' => [
                'levels' => $validated['reviewer_levels'],
                'required_levels' => $validated['reviewer_required_levels']
            ],
            'approver_config' => [
                'levels' => $validated['approver_levels'],
                'required_levels' => $validated['approver_required_levels']
            ],
            'is_active' => $validated['is_active'] ?? false
        ]);

        // Delete all existing steps and recreate them
        $workflow->steps()->delete();
        $workflow->initializeWorkflow();

        // Get the newly created steps
        $newSteps = $workflow->steps()->get();

        // Create a mapping of old step IDs to new step IDs based on type and level
        $stepMapping = [];
        foreach ($oldSteps as $oldStep) {
            $newStep = $newSteps->where('type', $oldStep->type)
                ->where('level', $oldStep->level)
                ->first();
            if ($newStep) {
                $stepMapping[$oldStep->id] = $newStep->id;
            }
        }

        // Process assignments
        if (!empty($validated['assignments'])) {
            foreach ($validated['assignments'] as $oldStepId => $userIds) {
                if (isset($stepMapping[$oldStepId])) {
                    $newStepId = $stepMapping[$oldStepId];
                    $step = $workflow->steps()->find($newStepId);
                    if ($step) {
                        foreach ($userIds as $userId) {
                            $step->assignments()->create(['user_id' => $userId]);
                        }
                    }
                }
            }
        }

        return redirect()->route('approval-workflows.index')
            ->with('success', 'Workflow updated successfully');
    }


    public function destroy(ApprovalWorkflow $workflow)
    {
        $workflow->delete();
        return redirect()->route('approval-workflows.index')
            ->with('success', 'Workflow deleted successfully');
    }

    public function show(ApprovalWorkflow $workflow)
    {
        // Eager load steps with their assignments and users
        $workflow->load([
            'steps.assignments.user' => function ($query) {
                $query->get();
            },
            'steps' => function ($query) {
                $query->orderBy('type')
                    ->orderBy('level');
            }
        ]);

        return view('admin.approval-workflows.show', [
            'workflow' => $workflow,
            'title' => 'Approval Workflow Details'
        ]);
    }
}