<?php

namespace App\Http\Controllers\Pip;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Pip\PipPlan;
use App\Models\Pip\PipReviewSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PipReviewScheduleController extends Controller
{
    public function index($planId)
    {
        $plan = PipPlan::with(['reviewSchedules.conductor'])->findOrFail($planId);
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.pip.schedule.index', [
            'plan' => $plan,
            'results' => $plan->reviewSchedules,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function conduct(Request $request, $id)
    {
        $schedule = PipReviewSchedule::findOrFail($id);
        $plan = PipPlan::findOrFail($schedule->pip_id);

        $request->validate([
            'comments' => 'nullable|string',
            'findings' => 'nullable|string',
        ]);

        $signedInUser = Auth::user();
        $employee = Employee::where('user_id', $signedInUser->id)->first();

        $schedule->status = 'completed';
        $schedule->comments = $request->input('comments');
        $schedule->findings = $request->input('findings');
        $schedule->conducted_by = $employee ? $employee->employee_id : null;
        $schedule->conducted_at = now();
        $schedule->save();

        return redirect()->route('pip.schedule.index', $schedule->pip_id)->with('success', 'Review conducted successfully.');
    }

    public function reschedule(Request $request, $id)
    {
        $schedule = PipReviewSchedule::findOrFail($id);

        $request->validate([
            'scheduled_date' => 'required|date',
        ]);

        $schedule->scheduled_date = $request->input('scheduled_date');
        $schedule->status = 'rescheduled';
        $schedule->save();

        return redirect()->route('pip.schedule.index', $schedule->pip_id)->with('success', 'Review rescheduled successfully.');
    }
}
