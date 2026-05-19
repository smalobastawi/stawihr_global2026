<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\TrainingInvitee;

class ValidateTrainingInvitation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $training = $request->route('training');
        $employee = $request->route('employee');

        // Check if training has started
        if (now() >= $training->start_date) {
            abort(410, 'This invitation is no longer active');
        }

         // Check if already responded
        $invite = TrainingInvitee::where([
            'training_id' => $training->id,
            'employee_id' => $employee->id
        ])->first();

        if ($invite && $invite->responded_at) {
            abort(410, 'You have already responded to this invitation');
        }
        return $next($request);
    }
}
