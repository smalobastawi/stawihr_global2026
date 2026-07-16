<?php

namespace App\Http\Controllers;

use App\Models\ApprovalLog;
use Illuminate\View\View;

class ApprovalLogsController extends Controller
{
    public function index(): View
    {
        $approvalLogs = ApprovalLog::query()
            ->with([
                'user.employeeDetails',
                'step.workflow',
                'approvable',
            ])
            ->orderByDesc('action_date')
            ->orderByDesc('created_at')
            ->take(500)
            ->get();

        return view('admin.approval-logs.index', ['data' => $approvalLogs]);
    }
}
