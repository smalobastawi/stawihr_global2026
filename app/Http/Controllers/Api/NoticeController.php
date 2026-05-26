<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    /**
     * Get published notices targeted at the authenticated employee.
     */
    public function index(Request $request)
    {
        $employee = Employee::where('user_id', $request->user()->id)->first();

        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee not found.',
            ], 404);
        }

        $notices = Notice::with(['departments', 'regions', 'locations', 'employees'])
            ->where('status', 'Published')
            ->whereDate('publish_date', '<=', now()->toDateString())
            ->orderBy('publish_date', 'desc')
            ->orderBy('notice_id', 'desc')
            ->get()
            ->filter(fn (Notice $notice) => $notice->targetsEmployee($employee))
            ->values()
            ->map(fn (Notice $notice) => [
                'notice_id' => $notice->notice_id,
                'title' => $notice->title,
                'description' => $notice->description,
                'publish_date' => $notice->publish_date,
                'status' => $notice->status,
                'attach_file' => $notice->attach_file,
                'targeted_audience_summary' => $notice->targeted_audience_summary,
            ]);

        return response()->json([
            'status' => 'success',
            'count' => $notices->count(),
            'data' => $notices,
        ]);
    }
}
