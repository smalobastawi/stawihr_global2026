<?php

namespace App\Http\Controllers\Pdp;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Pdp\PdpGoal;
use App\Models\Pdp\PdpPlan;
use App\Models\Pdp\PdpProgressEntry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PdpProgressController extends Controller
{
    public function index($planId)
    {
        $plan = PdpPlan::with(['goals.progressEntries.enteredBy', 'progressEntries.goal'])->findOrFail($planId);
        $periodOptions = $this->buildPeriodOptions($plan);
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.pdp.progress.index', [
            'plan' => $plan,
            'periodOptions' => $periodOptions,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function create($planId, Request $request)
    {
        $plan = PdpPlan::with('goals')->findOrFail($planId);
        $periodOptions = $this->buildPeriodOptions($plan);
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.pdp.progress.form', [
            'plan' => $plan,
            'periodOptions' => $periodOptions,
            'selectedGoalId' => $request->input('goal_id'),
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function store(Request $request, $planId)
    {
        $plan = PdpPlan::findOrFail($planId);

        if (!$plan->canBeEdited()) {
            return redirect()->route('pdp.progress.index', $planId)->with('error', 'This plan is locked or closed.');
        }

        $input = $request->validate([
            'pdp_goal_id' => 'required|exists:pdp_goals,pdp_goal_id',
            'review_year' => 'required|integer|min:2000|max:2100',
            'review_quarter' => 'nullable|integer|min:1|max:4',
            'review_half' => 'nullable|integer|min:1|max:2',
            'progress_percentage' => 'required|integer|min:0|max:100',
            'achievement_summary' => 'required|string',
            'challenges' => 'nullable|string',
            'support_needed' => 'nullable|string',
            'next_steps' => 'nullable|string',
        ]);

        $goal = PdpGoal::where('pdp_plan_id', $planId)->findOrFail($input['pdp_goal_id']);
        $periodLabel = $this->resolvePeriodLabel($plan, $input);

        $loggedEmployee = Employee::where('user_id', Auth::id())->first();

        try {
            $entry = PdpProgressEntry::create([
                'pdp_plan_id' => $planId,
                'pdp_goal_id' => $goal->pdp_goal_id,
                'review_frequency' => $plan->review_frequency,
                'review_year' => $input['review_year'],
                'review_quarter' => $input['review_quarter'] ?? null,
                'review_half' => $input['review_half'] ?? null,
                'review_period_label' => $periodLabel,
                'progress_percentage' => $input['progress_percentage'],
                'achievement_summary' => $input['achievement_summary'],
                'challenges' => $input['challenges'] ?? null,
                'support_needed' => $input['support_needed'] ?? null,
                'next_steps' => $input['next_steps'] ?? null,
                'status' => 'submitted',
                'entered_by' => $loggedEmployee ? $loggedEmployee->employee_id : null,
                'submitted_at' => now(),
            ]);

            $goal->overall_progress = $input['progress_percentage'];
            if ($input['progress_percentage'] >= 100) {
                $goal->status = 'completed';
            } elseif ($input['progress_percentage'] >= 70) {
                $goal->status = 'on_track';
            } elseif ($input['progress_percentage'] >= 40) {
                $goal->status = 'in_progress';
            } else {
                $goal->status = 'at_risk';
            }
            $goal->save();

            return redirect()->route('pdp.progress.index', $planId)->with('success', 'Progress entry saved for ' . $entry->review_period_label . '.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function review(Request $request, $id)
    {
        $entry = PdpProgressEntry::findOrFail($id);
        $input = $request->validate([
            'supervisor_comments' => 'nullable|string',
        ]);

        $loggedEmployee = Employee::where('user_id', Auth::id())->first();

        $entry->supervisor_comments = $input['supervisor_comments'] ?? null;
        $entry->status = 'reviewed';
        $entry->reviewed_by = $loggedEmployee ? $loggedEmployee->employee_id : null;
        $entry->reviewed_at = now();
        $entry->save();

        return redirect()->route('pdp.progress.index', $entry->pdp_plan_id)->with('success', 'Progress entry reviewed.');
    }

    protected function buildPeriodOptions(PdpPlan $plan): array
    {
        $year = (int) $plan->plan_year;
        $options = [];

        if ($plan->review_frequency === 'quarterly') {
            for ($q = 1; $q <= 4; $q++) {
                $options[] = [
                    'label' => "Q{$q} {$year}",
                    'review_year' => $year,
                    'review_quarter' => $q,
                    'review_half' => null,
                ];
            }
        } elseif ($plan->review_frequency === 'bi_annually') {
            $options[] = ['label' => "H1 {$year}", 'review_year' => $year, 'review_quarter' => null, 'review_half' => 1];
            $options[] = ['label' => "H2 {$year}", 'review_year' => $year, 'review_quarter' => null, 'review_half' => 2];
        } else {
            $options[] = ['label' => (string) $year, 'review_year' => $year, 'review_quarter' => null, 'review_half' => null];
        }

        return $options;
    }

    protected function resolvePeriodLabel(PdpPlan $plan, array $input): string
    {
        if ($plan->review_frequency === 'quarterly') {
            return 'Q' . $input['review_quarter'] . ' ' . $input['review_year'];
        }

        if ($plan->review_frequency === 'bi_annually') {
            return ((int) $input['review_half'] === 1 ? 'H1' : 'H2') . ' ' . $input['review_year'];
        }

        return (string) $input['review_year'];
    }
}
