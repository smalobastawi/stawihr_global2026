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

        $notices = $this->queryEmployeeNotices($employee)
            ->map(fn (Notice $notice) => $this->transformNotice($notice));

        return response()->json([
            'status' => 'success',
            'count' => $notices->count(),
            'data' => $notices,
        ]);
    }

    /**
     * Get a single notice if it is published and targeted at the employee.
     */
    public function show(Request $request, $id)
    {
        $employee = Employee::where('user_id', $request->user()->id)->first();

        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee not found.',
            ], 404);
        }

        $notice = Notice::with(['departments', 'regions', 'locations', 'employees'])
            ->where('notice_id', $id)
            ->where('status', 'Published')
            ->whereDate('publish_date', '<=', now()->toDateString())
            ->first();

        if (!$notice || !$notice->targetsEmployee($employee)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Notice not found.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $this->transformNotice($notice),
        ]);
    }

    private function queryEmployeeNotices(Employee $employee)
    {
        return Notice::with(['departments', 'regions', 'locations', 'employees'])
            ->where('status', 'Published')
            ->whereDate('publish_date', '<=', now()->toDateString())
            ->orderBy('publish_date', 'desc')
            ->orderBy('notice_id', 'desc')
            ->get()
            ->filter(fn (Notice $notice) => $notice->targetsEmployee($employee))
            ->values();
    }

    private function transformNotice(Notice $notice): array
    {
        return [
            'notice_id' => $notice->notice_id,
            'title' => $notice->title,
            'description' => $notice->description,
            'publish_date' => $notice->publish_date,
            'status' => $notice->status,
            'attach_file' => $notice->attach_file,
            'targeted_audience_summary' => $notice->targeted_audience_summary,
            'view_url' => url('/ess/notices/' . $notice->notice_id),
        ];
    }
}
