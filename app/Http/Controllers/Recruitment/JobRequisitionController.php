<?php

namespace App\Http\Controllers\Recruitment;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobRequisitionRequest;
use App\Models\Job;
use App\Models\JobRequisition;
use App\Models\Location;
use App\Models\Department;
use App\Models\RecruitmentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JobRequisitionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of job requisitions
     */
    public function index(Request $request)
    {
        $query = JobRequisition::with(['requestedBy', 'location', 'department', 'approvedBy'])
            ->orderBy('job_requisition_id', 'DESC');

        // Filter by status if provided
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by urgency level if provided
        if ($request->has('urgency_level') && $request->urgency_level !== '') {
            $query->where('urgency_level', $request->urgency_level);
        }

        // Filter by date range if provided
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('position_title', 'like', "%{$search}%")
                    ->orWhere('requisition_number', 'like', "%{$search}%")
                    ->orWhere('reporting_manager', 'like', "%{$search}%");
            });
        }

        $results = $query->paginate(15);

        // Get filter options
        $statusOptions = [
            '' => 'All Statuses',
            JobRequisition::STATUS_DRAFT => 'Draft',
            JobRequisition::STATUS_PENDING_APPROVAL => 'Pending Approval',
            JobRequisition::STATUS_APPROVED => 'Approved',
            JobRequisition::STATUS_REJECTED => 'Rejected',
            JobRequisition::STATUS_CANCELLED => 'Cancelled',
        ];

        $urgencyOptions = [
            '' => 'All Urgency Levels',
            JobRequisition::URGENCY_LOW => 'Low',
            JobRequisition::URGENCY_NORMAL => 'Normal',
            JobRequisition::URGENCY_HIGH => 'High',
            JobRequisition::URGENCY_CRITICAL => 'Critical',
        ];

        return view('admin.recruitment.jobRequisition.index', [
            'results' => $results,
            'statusOptions' => $statusOptions,
            'urgencyOptions' => $urgencyOptions,
            'filters' => $request->only(['status', 'urgency_level', 'date_from', 'date_to', 'search'])
        ]);
    }

    /**
     * Show the form for creating a new job requisition
     */
    public function create()
    {
        $locations = Location::where('status', 1)->orderBy('location_name')->get();
        $departments = Department::orderBy('department_name')->get();

        return view('admin.recruitment.jobRequisition.form', [
            'locations' => $locations,
            'departments' => $departments,
        ]);
    }

    /**
     * Store a newly created job requisition
     */
    public function store(JobRequisitionRequest $request)
    {
        try {
            DB::beginTransaction();

            $input = $request->all();
            $input['requested_by'] = Auth::id();
            $input['required_by_date'] = dateConvertFormtoDB($request->required_by_date);
            $input['proposed_start_date'] = $request->proposed_start_date ? dateConvertFormtoDB($request->proposed_start_date) : null;

            // Check if approval workflow is enabled
            $approvalWorkflowEnabled = RecruitmentSetting::isMultiLevelApprovalEnabled();

            // If no approval workflow configured or disabled, auto-approve
            if (!$approvalWorkflowEnabled) {
                $input['status'] = JobRequisition::STATUS_APPROVED;
                $input['approved_by'] = Auth::id();
                $input['approved_at'] = now();
                $input['hod_approval_signature'] = Auth::user()->name ?? 'System';
                $input['hod_approval_date'] = now();
                $input['hr_approval_signature'] = Auth::user()->name ?? 'System';
                $input['hr_approval_date'] = now();
            } else {
                // Set status to draft by default
                $input['status'] = JobRequisition::STATUS_DRAFT;
            }

            $jobRequisition = JobRequisition::create($input);

            DB::commit();

            if (!$approvalWorkflowEnabled) {
                return redirect()->route('jobRequisition.index')
                    ->with('success', 'Job requisition created and auto-approved successfully (no approval workflow configured).');
            }

            return redirect()->route('jobRequisition.index')
                ->with('success', 'Job requisition created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating job requisition: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the job requisition. Please try again.');
        }
    }

    /**
     * Display the specified job requisition
     */
    public function show($id)
    {
        $result = JobRequisition::with([
            'requestedBy',
            'approvedBy',
            'convertedBy',
            'location',
            'department',
            'job'
        ])->findOrFail($id);

        return view('admin.recruitment.jobRequisition.show', [
            'result' => $result
        ]);
    }

    /**
     * Show the form for editing the specified job requisition
     */
    public function edit($id)
    {
        $editModeData = JobRequisition::findOrFail($id);

        // Check if the requisition can be edited
        if (!$editModeData->canEdit()) {
            return redirect()->route('jobRequisition.index')
                ->with('error', 'This job requisition cannot be edited in its current status.');
        }

        $locations = Location::where('status', 1)->orderBy('location_name')->get();
        $departments = Department::orderBy('department_name')->get();

        return view('admin.recruitment.jobRequisition.form', [
            'editModeData' => $editModeData,
            'locations' => $locations,
            'departments' => $departments,
        ]);
    }

    /**
     * Update the specified job requisition
     */
    public function update(JobRequisitionRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $jobRequisition = JobRequisition::findOrFail($id);

            if (!$jobRequisition->canEdit()) {
                return redirect()->route('jobRequisition.index')
                    ->with('error', 'This job requisition cannot be edited in its current status.');
            }

            $input = $request->all();
            $input['required_by_date'] = dateConvertFormtoDB($request->required_by_date);
            $input['proposed_start_date'] = $request->proposed_start_date ? dateConvertFormtoDB($request->proposed_start_date) : null;

            $jobRequisition->update($input);

            DB::commit();

            return redirect()->route('jobRequisition.index')
                ->with('success', 'Job requisition updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating job requisition: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the job requisition. Please try again.');
        }
    }

    /**
     * Remove the specified job requisition
     */
    public function destroy($id)
    {
        try {
            $jobRequisition = JobRequisition::findOrFail($id);

            if (!$jobRequisition->canEdit()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This job requisition cannot be deleted in its current status.'
                ]);
            }

            $jobRequisition->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Job requisition deleted successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting job requisition: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the job requisition.'
            ]);
        }
    }

    /**
     * Submit job requisition for approval
     */
    public function submitForApproval($id)
    {
        try {
            DB::beginTransaction();

            $jobRequisition = JobRequisition::findOrFail($id);

            if (!$jobRequisition->canSubmitForApproval()) {
                return redirect()->route('jobRequisition.index')
                    ->with('error', 'This job requisition cannot be submitted for approval.');
            }

            $jobRequisition->status = JobRequisition::STATUS_PENDING_APPROVAL;
            $jobRequisition->save();

            DB::commit();

            return redirect()->route('jobRequisition.show', $id)
                ->with('success', 'Job requisition submitted for approval successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error submitting job requisition for approval: ' . $e->getMessage());

            return redirect()->route('jobRequisition.index')
                ->with('error', 'An error occurred while submitting for approval.');
        }
    }

    /**
     * Show approval form
     */
    public function showApprovalForm($id)
    {
        $jobRequisition = JobRequisition::findOrFail($id);

        if (!$jobRequisition->canApprove()) {
            return redirect()->route('jobRequisition.index')
                ->with('error', 'This job requisition cannot be approved in its current status.');
        }

        return view('admin.recruitment.jobRequisition.approve', [
            'result' => $jobRequisition
        ]);
    }

    /**
     * Approve job requisition
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'approval_comments' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $jobRequisition = JobRequisition::findOrFail($id);

            if (!$jobRequisition->canApprove()) {
                return redirect()->route('jobRequisition.index')
                    ->with('error', 'This job requisition cannot be approved in its current status.');
            }

            $jobRequisition->status = JobRequisition::STATUS_APPROVED;
            $jobRequisition->approved_by = Auth::id();
            $jobRequisition->approved_at = now();
            $jobRequisition->approval_comments = $request->approval_comments;
            $jobRequisition->save();

            DB::commit();

            return redirect()->route('jobRequisition.show', $id)
                ->with('success', 'Job requisition approved successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error approving job requisition: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while approving the job requisition.');
        }
    }

    /**
     * Show rejection form
     */
    public function showRejectionForm($id)
    {
        $jobRequisition = JobRequisition::findOrFail($id);

        if (!$jobRequisition->canReject()) {
            return redirect()->route('jobRequisition.index')
                ->with('error', 'This job requisition cannot be rejected in its current status.');
        }

        return view('admin.recruitment.jobRequisition.reject', [
            'result' => $jobRequisition
        ]);
    }

    /**
     * Reject job requisition
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $jobRequisition = JobRequisition::findOrFail($id);

            if (!$jobRequisition->canReject()) {
                return redirect()->route('jobRequisition.index')
                    ->with('error', 'This job requisition cannot be rejected in its current status.');
            }

            $jobRequisition->status = JobRequisition::STATUS_REJECTED;
            $jobRequisition->approved_by = Auth::id();
            $jobRequisition->approved_at = now();
            $jobRequisition->rejection_reason = $request->rejection_reason;
            $jobRequisition->save();

            DB::commit();

            return redirect()->route('jobRequisition.show', $id)
                ->with('success', 'Job requisition rejected successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error rejecting job requisition: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while rejecting the job requisition.');
        }
    }

    /**
     * Convert approved job requisition to job post
     */
    public function convertToJob($id)
    {
        try {
            DB::beginTransaction();

            $jobRequisition = JobRequisition::findOrFail($id);

            if (!$jobRequisition->canConvertToJob()) {
                return redirect()->route('jobRequisition.index')
                    ->with('error', 'This job requisition cannot be converted to a job post.');
            }

            // Create job post from requisition
            $jobData = [
                'job_title' => $jobRequisition->position_title,
                'job_description' => $jobRequisition->job_description,
                'job_requirements' => $jobRequisition->job_requirements,
                'job_type' => $jobRequisition->job_type,
                'location_id' => $jobRequisition->location_id,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'status' => 1, // Published by default
                'publish_date' => now(),
                'application_end_date' => $jobRequisition->required_by_date,
                'audience_type' => $jobRequisition->recruitment_source,
            ];

            $job = Job::create($jobData);

            // Update requisition
            $jobRequisition->is_converted_to_job = true;
            $jobRequisition->converted_job_id = $job->job_id;
            $jobRequisition->converted_at = now();
            $jobRequisition->converted_by = Auth::id();
            $jobRequisition->save();

            DB::commit();

            return redirect()->route('jobPost.edit', $job->job_id)
                ->with('success', 'Job requisition converted to job post successfully. You can now edit the job details.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error converting job requisition to job: ' . $e->getMessage());

            return redirect()->route('jobRequisition.show', $id)
                ->with('error', 'An error occurred while converting to job post.');
        }
    }

    /**
     * Cancel job requisition
     */
    public function cancel($id)
    {
        try {
            DB::beginTransaction();

            $jobRequisition = JobRequisition::findOrFail($id);

            if ($jobRequisition->status === JobRequisition::STATUS_APPROVED) {
                return redirect()->route('jobRequisition.index')
                    ->with('error', 'Approved job requisitions cannot be cancelled.');
            }

            $jobRequisition->status = JobRequisition::STATUS_CANCELLED;
            $jobRequisition->save();

            DB::commit();

            return redirect()->route('jobRequisition.show', $id)
                ->with('success', 'Job requisition cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error cancelling job requisition: ' . $e->getMessage());

            return redirect()->route('jobRequisition.index')
                ->with('error', 'An error occurred while cancelling the job requisition.');
        }
    }
}
