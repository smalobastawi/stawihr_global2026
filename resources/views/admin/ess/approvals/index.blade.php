@extends('admin.master')

@section('title')
    My Approvals & Submissions
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                            Dashboard</a></li>
                    <li>@yield('title')</li>
                </ol>
            </div>
        </div>

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#my_submissions" aria-controls="my_submissions" role="tab" data-toggle="tab">
                    My Submissions
                </a>
            </li>
            <li role="presentation">
                <a href="#my_pending_approvals" aria-controls="my_pending_approvals" role="tab" data-toggle="tab">
                    My Pending Approvals
                </a>
            </li>
            <li role="presentation">
                <a href="#payroll_approvals" aria-controls="payroll_approvals" role="tab" data-toggle="tab">
                    Payroll Output Approvals
                </a>
            </li>
            <!-- Add this new tab -->
            <li role="presentation">
                <a href="#approval_delegations" aria-controls="approval_delegations" role="tab" data-toggle="tab">
                    Approval Delegations
                </a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- My Submissions Section -->
            <div role="tabpanel" class="tab-pane fade in active" id="my_submissions">
                <div class="col-sm-12">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <i class="mdi mdi-file-document fa-fw"></i> My Submissions
                            <span class="badge badge-success pull-right">{{ $submissions->total() }} submitted</span>
                        </div>
                        <div class="panel-wrapper collapse in" aria-expanded="true">
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr class="tr_header">
                                                <th>#</th>
                                                <th>Item Type</th>
                                                <th>Date Submitted</th>
                                                <th>Current Stage</th>
                                                <th>Current Approvers</th>
                                                <th>Already Approved By</th>
                                                <th>Status</th>
                                                <!-- Dynamic columns will be added here -->
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($submissions as $index => $submission)
                                                @php
                                                    $model = $submission->approvable;
                                                    $currentStep = $model->currentApprovalStep();

                                                    // Get all approved/completed approval logs for this item
                                                    $approvedLogs = $model
                                                        ->approvalLogs()
                                                        ->with(['user.employeeDetails'])
                                                        ->whereIn('action', ['approved'])
                                                        ->orderBy('created_at', 'asc')
                                                        ->get();

                                                    // Get the latest status
                                                    $latestLog = $model
                                                        ->approvalLogs()
                                                        ->orderBy('updated_at', 'desc')
                                                        ->first();

                                                    $status = $latestLog
                                                        ? ($latestLog->action ?:
                                                        'approved')
                                                        : 'approved';

                                                    // Get approval details
                                                    $approvalDetails = method_exists($model, 'getApprovalDetails')
                                                        ? $model->getApprovalDetails()
                                                        : [];
                                                    $detailKeys = array_keys($approvalDetails);
                                                    $detailValues = array_values($approvalDetails);
                                                @endphp

                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        <span class="label label-primary">
                                                            {{ class_basename($model) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $model->created_at ? $model->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                                                    <td>
                                                        @if ($currentStep)
                                                            <span
                                                                class="label label-{{ $currentStep->type === 'reviewer' ? 'primary' : 'success' }}">
                                                                {{ ucfirst($currentStep->type) }} Level
                                                                {{ $currentStep->level }}
                                                            </span>
                                                        @else
                                                            <span class="label label-default">Completed/No active
                                                                step</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($currentStep && $currentStep->assignments->count())
                                                            <ul class="list-unstyled">
                                                                @foreach ($currentStep->assignments as $assignment)
                                                                    <li>
                                                                        <i class="fa fa-user"></i>
                                                                        {{ $assignment->user->email }}
                                                                        @if ($assignment->user->employeeDetails)
                                                                            ({{ $assignment->user->employeeDetails->fullName() ?? '' }})
                                                                        @endif
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <span class="text-muted">No approvers pending</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($approvedLogs->count() > 0)
                                                            <ul class="list-unstyled">
                                                                @foreach ($approvedLogs as $approvedLog)
                                                                    <li>
                                                                        <i class="fa fa-check-circle text-success"></i>
                                                                        {{ $approvedLog->user->name }}
                                                                        @if ($approvedLog->user->employeeDetails)
                                                                            ({{ $approvedLog->user->employeeDetails->fullName() ?? '' }})
                                                                        @endif
                                                                        <br><small
                                                                            class="text-muted">{{ $approvedLog->created_at ? $approvedLog->created_at->format('M d, Y H:i') : 'N/A' }}</small>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <span class="text-muted">None yet</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($status === 'approved')
                                                            <span class="label label-success">
                                                                <i class="fa fa-check"></i> Approved
                                                            </span>
                                                        @elseif($status === 'rejected')
                                                            <span class="label label-danger">
                                                                <i class="fa fa-times"></i> Rejected
                                                            </span>
                                                        @elseif($status === 'pending' || $status === 'submitted')
                                                            <span class="label label-warning">
                                                                <i class="fa fa-clock-o"></i> Pending
                                                            </span>
                                                        @else
                                                            <span class="label label-info">
                                                                {{ ucfirst($status) }}
                                                            </span>
                                                        @endif
                                                    </td>

                                                    <!-- Dynamic details columns -->
                                                    @if (count($approvalDetails) > 0)
                                                        @foreach ($approvalDetails as $label => $value)
                                                            <td class="detail-cell">
                                                                <strong>{{ $label }}:</strong> {{ $value }}
                                                            </td>
                                                        @endforeach
                                                    @else
                                                        <td class="detail-cell">
                                                            <a href="{{ route('ess.approval.show', [
                                                                'modelType' => str_replace('\\', '_', get_class($model)),
                                                                'modelId' => $model->id,
                                                            ]) }}"
                                                                class="btn btn-xs btn-default">
                                                                View Details
                                                            </a>
                                                        </td>
                                                    @endif

                                                    <td>
                                                        <a href="{{ route('ess.approval.show', [
                                                            'modelType' => str_replace('\\', '_', get_class($model)),
                                                            'modelId' => $model->id,
                                                        ]) }}"
                                                            class="btn btn-xs btn-primary" title="View Details">
                                                            <i class="fa fa-eye"></i> View
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center">
                                                        <div class="alert alert-info">
                                                            You have not submitted any items for approval yet.
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>

                                    @if ($submissions->total() > 0)
                                        <div class="text-center">
                                            {{ $submissions->links() }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Pending Approvals Section -->
            <div role="tabpanel" class="tab-pane fade" id="my_pending_approvals">
                <div class="col-sm-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <i class="mdi mdi-table fa-fw"></i> Pending Approvals
                            <span class="badge badge-primary pull-right">{{ $approvals->total() }} pending</span>
                        </div>
                        <div class="panel-wrapper collapse in" aria-expanded="true">
                            <div class="panel-body">
                                @include('admin.partials.alert')

                                <!-- Batch Approval Controls -->
                                <div id="batchControlsPending" class="row" style="display: none; margin-bottom: 15px;">
                                    <div class="col-md-12">
                                        <div class="panel panel-default">
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <span id="selectedCountPending">0</span> item(s) selected
                                                    </div>
                                                    <div class="col-md-4 text-right">
                                                        <button type="button" class="btn btn-success btn-sm"
                                                            id="batchApproveBtnPending">
                                                            <i class="fa fa-check"></i> Batch Approve Selected
                                                        </button>
                                                        <button type="button" class="btn btn-default btn-sm"
                                                            id="clearSelectionBtnPending">
                                                            <i class="fa fa-times"></i> Clear Selection
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr class="tr_header">
                                                <th width="50">
                                                    <input type="checkbox" id="selectAll" title="Select All">
                                                </th>
                                                <th>#</th>
                                                <th>Item Type</th>
                                                <th>Submitted By</th>
                                                <th>Date Submitted</th>
                                                <th>Current Stage</th>
                                                <th>Current Approvers</th>
                                                <th>Already Approved By</th>
                                                <!-- Dynamic columns will be added here -->
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($approvals as $index => $approval)
                                                @php
                                                    $model = $approval->approvable;
                                                    $currentStep = $model->currentApprovalStep();
                                                    $userHasApproved = $model
                                                        ->approvalLogs()
                                                        ->where('id', $approval->id)
                                                        ->where('user_id', auth()->id())
                                                        ->whereIn('action', ['approved', 'rejected'])
                                                        ->exists();

                                                    // Get all approved/completed approval logs for this item
                                                    $approvedLogs = $model
                                                        ->approvalLogs()
                                                        ->with(['user.employeeDetails'])
                                                        ->whereIn('action', ['approved'])
                                                        ->orderBy('created_at', 'asc')
                                                        ->get();

                                                    // Check if current user can take action on this item
                                                    // First, check if user is directly assigned
                                                    $isDirectApprover =
                                                        $currentStep &&
                                                        $currentStep->assignments->contains('user_id', auth()->id()) &&
                                                        !$userHasApproved;

                                                    // Check if user has delegation authority for this approval
                                                    $hasDelegationAuthority = false;
                                                    if ($currentStep && !$userHasApproved) {
                                                        // Check if current user has active delegations from any of the current approvers
                                                        $currentApproverIds = $currentStep->assignments
                                                            ->pluck('user_id')
                                                            ->toArray();
                                                        if (!empty($currentApproverIds)) {
                                                            $delegations = App\Models\ApprovalDelegation::active()
                                                                ->where('delegate_to_user_id', auth()->id())
                                                                ->whereIn('user_id', $currentApproverIds)
                                                                ->exists();
                                                            $hasDelegationAuthority = $delegations;
                                                        }

                                                        // Also check for model-specific delegations if any
                                                        if (!$hasDelegationAuthority) {
                                                            $delegations = App\Models\ApprovalDelegation::active()
                                                                ->where('delegate_to_user_id', auth()->id())
                                                                ->where(function ($query) use ($model) {
                                                                    $query
                                                                        ->where('delegation_type', 'all')
                                                                        ->orWhere(function ($q) use ($model) {
                                                                            $q->where(
                                                                                'delegation_type',
                                                                                'specific',
                                                                            )->where('model_type', get_class($model));
                                                                        });
                                                                })
                                                                ->exists();
                                                            $hasDelegationAuthority = $delegations;
                                                        }
                                                    }

                                                    $canTakeAction = $isDirectApprover || $hasDelegationAuthority;

                                                    // Get approval details
                                                    $approvalDetails = method_exists($model, 'getApprovalDetails')
                                                        ? $model->getApprovalDetails()
                                                        : [];
                                                    $detailKeys = array_keys($approvalDetails);
                                                    $detailValues = array_values($approvalDetails);
                                                @endphp

                                                <tr class="approval-row"
                                                    data-model="{{ str_replace('\\', '_', get_class($model)) }}"
                                                    data-id="{{ $model->id }}">

                                                    <td>
                                                        @if (isset($approval->is_delegated) && $approval->is_delegated)
                                                            <span class="label label-info pull-right">
                                                                (Delegated)
                                                            </span>
                                                        @endif
                                                        @if ($canTakeAction)
                                                            <input type="checkbox" class="approval-checkbox"
                                                                value="{{ str_replace('\\', '_', get_class($model)) }}_{{ $model->id }}"
                                                                data-model="{{ str_replace('\\', '_', get_class($model)) }}"
                                                                data-id="{{ $model->id }}">
                                                        @endif
                                                    </td>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        <span class="label label-info">
                                                            {{ class_basename($model) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        {{ $model->submitter->email ?? 'System' }}
                                                        @if ($model->submitter && $model->submitter->employeeDetails)
                                                            <br><small>{{ $model->submitter->employeeDetails->fullName() ?? '' }}</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ $model->created_at ? $model->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                                                    <td>
                                                        @if ($currentStep)
                                                            <span
                                                                class="label label-{{ $currentStep->type === 'reviewer' ? 'primary' : 'success' }}">
                                                                {{ ucfirst($currentStep->type) }} Level
                                                                {{ $currentStep->level }}
                                                            </span>
                                                        @else
                                                            <span class="label label-default">No active step</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($currentStep && $currentStep->assignments->count())
                                                            <ul class="list-unstyled">
                                                                @foreach ($currentStep->assignments as $assignment)
                                                                    <li>
                                                                        <i class="fa fa-user"></i>
                                                                        {{ $assignment->user->email }}
                                                                        @if ($assignment->user_id == auth()->id())
                                                                            <span class="label label-warning">Awaiting
                                                                                Your
                                                                                Action @if ($assignment->user->employeeDetails)
                                                                                    ({{ $assignment->user->employeeDetails->fullName() ?? '' }})
                                                                                @endif
                                                                            </span>
                                                                        @endif
                                                                        @if ($assignment->user->employeeDetails)
                                                                            ({{ $assignment->user->employeeDetails->fullName() ?? '' }})
                                                                        @endif
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <span class="text-muted">No approvers pending</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($approvedLogs->count() > 0)
                                                            <ul class="list-unstyled">
                                                                @foreach ($approvedLogs as $approvedLog)
                                                                    <li>
                                                                        <i class="fa fa-check-circle text-success"></i>
                                                                        {{ $approvedLog->user->name }}
                                                                        @if ($approvedLog->user->employeeDetails)
                                                                            ({{ $approvedLog->user->employeeDetails->fullName() ?? '' }})
                                                                        @endif
                                                                        <br><small
                                                                            class="text-muted">{{ $approvedLog->created_at ? $approvedLog->created_at->format('M d, Y H:i') : 'N/A' }}</small>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <span class="text-muted">None yet</span>
                                                        @endif
                                                    </td>

                                                    <!-- Dynamic details columns -->
                                                    @if (count($approvalDetails) > 0)
                                                        @foreach ($approvalDetails as $label => $value)
                                                            <td class="detail-cell">
                                                                <strong>{{ $label }}:</strong>
                                                                {{ $value }}
                                                            </td>
                                                        @endforeach
                                                    @else
                                                        <td class="detail-cell">
                                                            <a href="{{ route('ess.approval.show', [
                                                                'modelType' => str_replace('\\', '_', get_class($model)),
                                                                'modelId' => $model->id,
                                                            ]) }}"
                                                                class="btn btn-xs btn-default">
                                                                View Details
                                                            </a>
                                                        </td>
                                                    @endif

                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="{{ route('ess.approval.show', [
                                                                'modelType' => str_replace('\\', '_', get_class($model)),
                                                                'modelId' => $model->id,
                                                            ]) }}"
                                                                class="btn btn-xs btn-primary" title="Review">
                                                                <i class="fa fa-eye"></i> View
                                                            </a>

                                                            @if ($canTakeAction)
                                                                <button class="btn btn-xs btn-success approve-btn"
                                                                    data-model="{{ str_replace('\\', '_', get_class($model)) }}"
                                                                    data-id="{{ $model->id }}" title="Approve">
                                                                    <i class="fa fa-check"></i> Approve
                                                                </button>

                                                                <button class="btn btn-xs btn-danger reject-btn"
                                                                    data-model="{{ str_replace('\\', '_', get_class($model)) }}"
                                                                    data-id="{{ $model->id }}" title="Reject">
                                                                    <i class="fa fa-times"></i> Reject
                                                                </button>
                                                            @else
                                                                @if ($userHasApproved)
                                                                    <span class="label label-success">Already Actioned
                                                                        {{ $approval->id }}</span>
                                                                @else
                                                                    <span class="label label-info">View Only</span>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center">
                                                        <div class="alert alert-info">
                                                            There are no incomplete approval processes at this time.
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>

                                    @if ($approvals->total() > 0)
                                        <div class="text-center">
                                            {{ $approvals->links() }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Payroll Approvals Section -->
            <div role="tabpanel" class="tab-pane fade" id="payroll_approvals">
                <div class="col-sm-12">
                    <div class="panel panel-warning">
                        <div class="panel-heading">
                            <i class="mdi mdi-cash fa-fw"></i> Payroll Record Approvals
                            <div class="pull-right">
                                @if ($payrollRecordApprovals->total() > 0)
                                    <a href="{{ route('ess.approval.payroll.export') }}" class="btn btn-success btn-xs"
                                        title="Download All Approvals as Excel">
                                        <i class="fa fa-file-excel-o"></i> Download All Excel
                                    </a>
                                @endif
                                <span class="badge badge-warning">{{ $payrollRecordApprovals->total() }} pending</span>
                            </div>
                        </div>
                        <div class="panel-wrapper collapse in" aria-expanded="true">
                            <div class="panel-body">
                                <!-- Batch Approval Controls -->
                                <div id="batchControlsPayroll" class="row"
                                    style="display: none; margin-bottom: 15px;">
                                    <div class="col-md-12">
                                        <div class="panel panel-default">
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <span id="selectedCountPayroll">0</span> item(s) selected
                                                    </div>
                                                    <div class="col-md-4 text-right">
                                                        <button type="button" class="btn btn-success btn-sm"
                                                            id="batchApproveBtnPayroll">
                                                            <i class="fa fa-check"></i> Batch Approve Selected
                                                        </button>
                                                        <button type="button" class="btn btn-default btn-sm"
                                                            id="clearSelectionBtnPayroll">
                                                            <i class="fa fa-times"></i> Clear Selection
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr class="tr_header">
                                                <th width="50">
                                                    <input type="checkbox" id="selectAllPayroll" title="Select All">
                                                </th>
                                                <th>#</th>
                                                <th>Item Type</th>
                                                <th>Submitted By</th>
                                                <th>Date Submitted</th>
                                                <th>Current Stage</th>
                                                <th>Current Approvers</th>
                                                <th>Already Approved By</th>
                                                <!-- Dynamic payroll columns -->
                                                @foreach ($payrollColumns as $columnKey => $columnLabel)
                                                    <th class="payroll-column">{{ $columnLabel }}</th>
                                                @endforeach
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- TOTALS ROW - ADDED HERE WITH CORRECT COLUMN COUNT --}}
                                            @if (isset($totalsRow))
                                                <tr
                                                    style="background-color: #e8f4ff; font-weight: bold; border-top: 2px solid #000; border-bottom: 2px solid #000;">
                                                    {{-- 8 columns before payroll data --}}
                                                    <td style="background-color: #e8f4ff;"></td> {{-- Checkbox --}}
                                                    <td style="background-color: #e8f4ff;"></td> {{-- # --}}
                                                    <td style="background-color: #e8f4ff;">TOTALS</td>
                                                    {{-- Item Type --}}
                                                    <td style="background-color: #e8f4ff;"></td> {{-- Submitted By --}}
                                                    <td style="background-color: #e8f4ff;"></td> {{-- Date Submitted --}}
                                                    <td style="background-color: #e8f4ff;"></td> {{-- Current Stage --}}
                                                    <td style="background-color: #e8f4ff;"></td> {{-- Current Approvers --}}
                                                    <td style="background-color: #e8f4ff;"></td> {{-- Already Approved By --}}

                                                    {{-- Payroll data columns --}}
                                                    @foreach ($payrollColumns as $key => $label)
                                                        <td
                                                            style="@if (in_array($key, ['employee_code', 'job_title', 'status'])) background-color: #d4edda; @endif">
                                                            {{ $totalsRow[$key] ?? '' }}
                                                        </td>
                                                    @endforeach

                                                    {{-- Actions column --}}
                                                    <td style="background-color: #e8f4ff;"></td>
                                                </tr>
                                            @endif

                                            @forelse($payrollRecordApprovals as $index => $approval)
                                                @php
                                                    $model = $approval->approvable;
                                                    $currentStep = $model->currentApprovalStep();
                                                    $userHasApproved = $model
                                                        ->approvalLogs()
                                                        ->where('id', $approval->id)
                                                        ->where('user_id', auth()->id())
                                                        ->whereIn('action', ['approved', 'rejected'])
                                                        ->exists();

                                                    // Get all approved/completed approval logs for this item
                                                    $approvedLogs = $model
                                                        ->approvalLogs()
                                                        ->with(['user.employeeDetails'])
                                                        ->whereIn('action', ['approved'])
                                                        ->orderBy('created_at', 'asc')
                                                        ->get();

                                                    // Check if current user can take action on this item
                                                    // First, check if user is directly assigned
                                                    $isDirectApprover =
                                                        $currentStep &&
                                                        $currentStep->assignments->contains('user_id', auth()->id()) &&
                                                        !$userHasApproved;

                                                    // Check if user has delegation authority for this approval
                                                    $hasDelegationAuthority = false;
                                                    if ($currentStep && !$userHasApproved) {
                                                        // Check if current user has active delegations from any of the current approvers
                                                        $currentApproverIds = $currentStep->assignments
                                                            ->pluck('user_id')
                                                            ->toArray();
                                                        if (!empty($currentApproverIds)) {
                                                            $delegations = App\Models\ApprovalDelegation::active()
                                                                ->where('delegate_to_user_id', auth()->id())
                                                                ->whereIn('user_id', $currentApproverIds)
                                                                ->exists();
                                                            $hasDelegationAuthority = $delegations;
                                                        }

                                                        // Also check for model-specific delegations if any
                                                        if (!$hasDelegationAuthority) {
                                                            $delegations = App\Models\ApprovalDelegation::active()
                                                                ->where('delegate_to_user_id', auth()->id())
                                                                ->where(function ($query) use ($model) {
                                                                    $query
                                                                        ->where('delegation_type', 'all')
                                                                        ->orWhere(function ($q) use ($model) {
                                                                            $q->where(
                                                                                'delegation_type',
                                                                                'specific',
                                                                            )->where('model_type', get_class($model));
                                                                        });
                                                                })
                                                                ->exists();
                                                            $hasDelegationAuthority = $delegations;
                                                        }
                                                    }

                                                    $canTakeAction = $isDirectApprover || $hasDelegationAuthority;

                                                    // Get the processed payroll data for this record
                                                    $payrollData = $processedPayrollRecords[$approval->id] ?? [];
                                                @endphp

                                                <tr class="approval-row"
                                                    data-model="{{ str_replace('\\', '_', get_class($model)) }}"
                                                    data-id="{{ $model->id }}">
                                                    <td>
                                                        @if (isset($approval->is_delegated) && $approval->is_delegated)
                                                            <span class="label label-info pull-right">
                                                                (Delegated)
                                                            </span>
                                                        @endif
                                                        @if ($canTakeAction)
                                                            <input type="checkbox"
                                                                class="approval-checkbox payroll-checkbox"
                                                                value="{{ str_replace('\\', '_', get_class($model)) }}_{{ $model->id }}"
                                                                data-model="{{ str_replace('\\', '_', get_class($model)) }}"
                                                                data-id="{{ $model->id }}">
                                                        @endif
                                                    </td>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        <span class="label label-info">
                                                            {{ class_basename($model) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        {{ $model->submitter->email ?? 'System' }}
                                                        @if ($model->submitter && $model->submitter->employeeDetails)
                                                            <br><small>{{ $model->submitter->employeeDetails->fullName() ?? '' }}</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ $model->created_at ? $model->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                                                    <td>
                                                        @if ($currentStep)
                                                            <span
                                                                class="label label-{{ $currentStep->type === 'reviewer' ? 'primary' : 'success' }}">
                                                                {{ ucfirst($currentStep->type) }} Level
                                                                {{ $currentStep->level }}
                                                            </span>
                                                        @else
                                                            <span class="label label-default">No active step</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($currentStep && $currentStep->assignments->count())
                                                            <ul class="list-unstyled">
                                                                @foreach ($currentStep->assignments as $assignment)
                                                                    <li>
                                                                        <i class="fa fa-user"></i>
                                                                        {{ $assignment->user->email }}
                                                                        @if ($assignment->user_id == auth()->id())
                                                                            <span class="label label-warning">Awaiting Your
                                                                                Action @if ($assignment->user->employeeDetails)
                                                                                    ({{ $assignment->user->employeeDetails->fullName() ?? '' }})
                                                                                @endif
                                                                            </span>
                                                                        @endif
                                                                        @if ($assignment->user->employeeDetails)
                                                                            ({{ $assignment->user->employeeDetails->fullName() ?? '' }})
                                                                        @endif
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <span class="text-muted">No approvers pending</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($approvedLogs->count() > 0)
                                                            <ul class="list-unstyled">
                                                                @foreach ($approvedLogs as $approvedLog)
                                                                    <li>
                                                                        <i class="fa fa-check-circle text-success"></i>
                                                                        {{ $approvedLog->user->name }}
                                                                        @if ($approvedLog->user->employeeDetails)
                                                                            ({{ $approvedLog->user->employeeDetails->fullName() ?? '' }})
                                                                        @endif
                                                                        <br><small
                                                                            class="text-muted">{{ $approvedLog->created_at ? $approvedLog->created_at->format('M d, Y H:i') : 'N/A' }}</small>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <span class="text-muted">None yet</span>
                                                        @endif
                                                    </td>

                                                    <!-- Dynamic payroll data columns -->
                                                    @foreach ($payrollColumns as $columnKey => $columnLabel)
                                                        <td class="payroll-data-cell">
                                                            {{ $payrollData[$columnKey] ?? 'N/A' }}
                                                        </td>
                                                    @endforeach

                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="{{ route('payroll.show', $model->id) }}"
                                                                class="btn btn-xs btn-primary" title="Review">
                                                                <i class="fa fa-eye"></i> View
                                                            </a>

                                                            @if ($canTakeAction)
                                                                <button class="btn btn-xs btn-success approve-btn"
                                                                    data-model="{{ str_replace('\\', '_', get_class($model)) }}"
                                                                    data-id="{{ $model->id }}" title="Approve">
                                                                    <i class="fa fa-check"></i> Approve
                                                                </button>

                                                                <button class="btn btn-xs btn-danger reject-btn"
                                                                    data-model="{{ str_replace('\\', '_', get_class($model)) }}"
                                                                    data-id="{{ $model->id }}" title="Reject">
                                                                    <i class="fa fa-times"></i> Reject
                                                                </button>
                                                            @else
                                                                @if ($userHasApproved)
                                                                    <span class="label label-success">Already
                                                                        Actioned</span>
                                                                @else
                                                                    <span class="label label-info">View Only</span>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="{{ 9 + count($payrollColumns) }}" class="text-center">
                                                        <div class="alert alert-info">
                                                            There are no incomplete payroll approval processes at this time.
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>

                                    @if ($payrollRecordApprovals->total() > 0)
                                        <div class="text-center">
                                            {{ $payrollRecordApprovals->links() }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Add this new tab panel at the end of tab content -->
            <div role="tabpanel" class="tab-pane fade" id="approval_delegations">
                <div class="col-sm-12">
                    <div class="panel panel-warning">
                        <div class="panel-heading">
                            <i class="mdi mdi-account-switch fa-fw"></i> Manage Approval Delegations
                            <a href="{{ route('ess.approval.delegations.index') }}"
                                class="btn btn-xs btn-primary pull-right">
                                <i class="fa fa-cog"></i> Manage Delegations
                            </a>
                        </div>
                        <div class="panel-wrapper collapse in" aria-expanded="true">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <i class="fa fa-share"></i> Delegated From Me
                                            </div>
                                            <div class="panel-body">
                                                @if ($myDelegations->count() > 0)
                                                    <ul class="list-group">
                                                        @foreach ($myDelegations as $delegation)
                                                            <li class="list-group-item">
                                                                <div class="row">
                                                                    <div class="col-md-8">
                                                                        <strong>{{ $delegation->delegate->email }}</strong>
                                                                        @if ($delegation->delegate->employeeDetails)
                                                                            <br><small
                                                                                class="text-muted">{{ $delegation->delegate->employeeDetails->fullName() }}</small>
                                                                        @endif
                                                                        <br>
                                                                        <small>
                                                                            <strong>Scope:</strong>
                                                                            {{ $delegation->delegation_type == 'all' ? 'All Approvals' : class_basename($delegation->model_type) }}
                                                                            | <strong>Until:</strong>
                                                                            {{ $delegation->end_date ? $delegation->end_date->format('M d, Y') : 'Indefinite' }}
                                                                            | <strong>Status:</strong>
                                                                            <span
                                                                                class="label label-{{ $delegation->is_active ? 'success' : 'danger' }}">
                                                                                {{ $delegation->is_active ? 'Active' : 'Inactive' }}
                                                                            </span>
                                                                        </small>
                                                                    </div>
                                                                    <div class="col-md-4 text-right">
                                                                        <div class="btn-group btn-group-sm">
                                                                            <a href="{{ route('ess.approval.delegations.edit', $delegation->id) }}"
                                                                                class="btn btn-primary btn-sm"
                                                                                title="Edit Delegation">
                                                                                <i class="fa fa-edit"></i>
                                                                            </a>
                                                                            @if ($delegation->is_active)
                                                                                <button type="button"
                                                                                    class="btn btn-warning btn-sm"
                                                                                    onclick="confirmDeactivate({{ $delegation->id }})"
                                                                                    title="Deactivate Delegation">
                                                                                    <i class="fa fa-pause"></i>
                                                                                </button>
                                                                            @else
                                                                                <button type="button"
                                                                                    class="btn btn-success btn-sm"
                                                                                    onclick="confirmActivate({{ $delegation->id }})"
                                                                                    title="Activate Delegation">
                                                                                    <i class="fa fa-play"></i>
                                                                                </button>
                                                                            @endif
                                                                            <button type="button"
                                                                                class="btn btn-danger btn-sm"
                                                                                onclick="confirmDelete({{ $delegation->id }})"
                                                                                title="Delete Delegation">
                                                                                <i class="fa fa-trash"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <div class="alert alert-info">
                                                        You have not delegated any approval authority.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <i class="fa fa-users"></i> Delegated To Me
                                            </div>
                                            <div class="panel-body">
                                                @if ($delegatedToMe->count() > 0)
                                                    <ul class="list-group">
                                                        @foreach ($delegatedToMe as $delegation)
                                                            <li class="list-group-item">
                                                                <div class="row">
                                                                    <div class="col-md-8">
                                                                        <strong>{{ $delegation->delegator->email }}</strong>
                                                                        @if ($delegation->delegator->employeeDetails)
                                                                            <br><small
                                                                                class="text-muted">{{ $delegation->delegator->employeeDetails->fullName() }}</small>
                                                                        @endif
                                                                        <br>
                                                                        <small>
                                                                            <strong>Scope:</strong>
                                                                            {{ $delegation->delegation_type == 'all' ? 'All Approvals' : class_basename($delegation->model_type) }}
                                                                            | <strong>Until:</strong>
                                                                            {{ $delegation->end_date ? $delegation->end_date->format('M d, Y') : 'Indefinite' }}
                                                                            | <strong>Status:</strong>
                                                                            <span
                                                                                class="label label-{{ $delegation->is_active ? 'success' : 'danger' }}">
                                                                                {{ $delegation->is_active ? 'Active' : 'Inactive' }}
                                                                            </span>
                                                                        </small>
                                                                    </div>
                                                                    <div class="col-md-4 text-right">
                                                                        @if ($delegation->is_active)
                                                                            <span class="label label-info">
                                                                                <i class="fa fa-check-circle"></i> You can
                                                                                approve on behalf
                                                                            </span>
                                                                        @else
                                                                            <span class="label label-warning">
                                                                                <i class="fa fa-pause-circle"></i>
                                                                                Delegation inactive
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <div class="alert alert-info">
                                                        No one has delegated approval authority to you.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center mt-3">
                                    <a href="{{ route('ess.approval.delegations.index') }}" class="btn btn-success"
                                        style="color: white">
                                        <i class="fa fa-cog"></i> Manage Delegation Settings
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Batch Approval Modal -->
    <div class="modal fade" id="batchActionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Batch Approve Selected Items</h4>
                </div>
                <form id="batchActionForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            You are about to approve <strong id="batchItemCount">0</strong> selected items.
                        </div>

                        <div class="form-group">
                            <label for="batchComments">Comments (Optional)</label>
                            <textarea name="comments" id="batchComments" class="form-control" rows="3"
                                placeholder="Enter your comments for all selected items..."></textarea>
                        </div>

                        <div id="batchItemsList">
                            <!-- Selected items will be populated here -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" id="batchModalActionBtn">
                            <i class="fa fa-check"></i> Approve All Selected
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Individual Approval/Reject Modals -->
    <div class="modal fade" id="actionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="modalTitle">Approve/Reject Request</h4>
                </div>
                <form id="actionForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="comments">Comments</label>
                            <textarea name="comments" id="comments" class="form-control" rows="3"
                                placeholder="Enter your comments here..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn" id="modalActionBtn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page_css')
    <style>
        /* Dynamic table styling */
        .detail-cell {
            min-width: 150px;
            max-width: 250px;
            word-wrap: break-word;
            background-color: #f9f9f9;
            border-left: 1px solid #ddd;
        }

        .detail-cell:nth-child(even) {
            background-color: #f0f0f0;
        }

        .table th.detail-header {
            background-color: #e9ecef;
            font-weight: 600;
            text-align: center;
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .detail-cell {
                min-width: 120px;
                max-width: 200px;
                font-size: 12px;
            }
        }

        @media (max-width: 992px) {
            .detail-cell {
                min-width: 100px;
                max-width: 150px;
            }
        }

        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }

            .detail-cell {
                min-width: 80px;
                max-width: 120px;
                font-size: 11px;
            }
        }

        /* Other existing styles remain the same */
        .panel {
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }

        /* Earnings and Deductions Details Styling */
        .earnings-details,
        .deductions-details {
            max-width: 300px;
        }

        .earning-type,
        .deduction-type {
            border-left: 3px solid #337ab7;
            padding-left: 8px;
            background-color: #f8f9fa;
            padding: 5px;
            border-radius: 3px;
        }

        .earning-type strong,
        .deduction-type strong {
            color: #337ab7;
            font-size: 11px;
        }

        .earning-type ul,
        .deduction-type ul {
            margin-bottom: 0;
        }

        .earning-type li,
        .deduction-type li {
            padding: 1px 0;
            border-bottom: 1px dotted #ddd;
        }

        .earning-type li:last-child,
        .deduction-type li:last-child {
            border-bottom: none;
        }

        .company-contributions {
            max-width: 200px;
        }

        .company-contributions ul {
            margin-bottom: 5px;
        }

        .company-contributions li {
            padding: 1px 0;
            border-bottom: 1px dotted #5cb85c;
        }

        .company-contributions li:last-child {
            border-bottom: none;
        }

        .company-contributions strong {
            color: #5cb85c;
            font-size: 11px;
        }

        /* Responsive adjustments for new details columns */
        @media (max-width: 1200px) {

            .earnings-details,
            .deductions-details {
                max-width: 250px;
                font-size: 11px;
            }

            .company-contributions {
                max-width: 150px;
                font-size: 11px;
            }
        }

        @media (max-width: 992px) {

            .earning-type strong,
            .deduction-type strong {
                font-size: 10px;
            }

            .company-contributions strong {
                font-size: 10px;
            }
        }

        @media (max-width: 768px) {

            .earnings-details,
            .deductions-details {
                max-width: 200px;
            }

            .company-contributions {
                max-width: 120px;
            }
        }

        /* ===== PAYROLL APPROVALS STICKY HEADER STYLES ===== */

        /* Payroll column styling */
        .payroll-column {
            background-color: #f8f9fa;
            font-weight: 600;
            text-align: center;
            border-left: 2px solid #dee2e6;
        }

        .payroll-data-cell {
            text-align: right;
            font-family: 'Courier New', monospace;
            background-color: #fafafa;
            border-left: 1px solid #dee2e6;
            padding: 6px 8px;
        }

        .payroll-data-cell:nth-child(even) {
            background-color: #f0f0f0;
        }

        /* Main sticky header container */
        #payroll_approvals .panel-body {
            position: relative;
            padding: 0;
        }

        /* Table container with fixed height and scrolling */
        #payroll_approvals .table-responsive {
            max-height: 70vh;
            overflow: auto;
            border: 1px solid #ddd;
            position: relative;
        }

        /* Ensure table takes full width */
        #payroll_approvals .table {
            margin-bottom: 0;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        /* STICKY HEADER - This is the key fix */
        #payroll_approvals .table thead {
            position: sticky;
            top: 0;
            z-index: 100;
        }

        #payroll_approvals .table thead th {
            background-color: #f8f9fa !important;
            border-bottom: 2px solid #dee2e6;
            position: sticky;
            top: 0;
            z-index: 101;
            padding: 12px 8px;
            font-weight: 600;
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.1);
        }

        /* Ensure header cells have proper background */
        #payroll_approvals .table thead th.payroll-column {
            background-color: #f8f9fa !important;
        }

        /* Optional: Sticky first column for better navigation */
        #payroll_approvals .table th:first-child,
        #payroll_approvals .table td:first-child {
            position: sticky;
            left: 0;
            background-color: #f8f9fa;
            z-index: 99;
            border-right: 2px solid #dee2e6;
        }

        #payroll_approvals .table td:first-child {
            background-color: #fafafa;
        }

        /* Ensure the first header cell stays on top of other sticky elements */
        #payroll_approvals .table th:first-child {
            z-index: 102;
            left: 0;
        }

        /* Body cell styling */
        #payroll_approvals .table tbody td {
            padding: 8px;
            vertical-align: middle;
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            #payroll_approvals .table-responsive {
                max-height: 60vh;
            }

            .payroll-column,
            .payroll-data-cell {
                font-size: 12px;
                padding: 4px 6px;
            }
        }

        @media (max-width: 992px) {
            #payroll_approvals .table-responsive {
                max-height: 50vh;
            }

            .payroll-column,
            .payroll-data-cell {
                font-size: 11px;
                padding: 3px 5px;
            }
        }

        @media (max-width: 768px) {
            #payroll_approvals .table-responsive {
                max-height: 40vh;
            }

            /* On mobile, disable sticky first column for better performance */
            #payroll_approvals .table th:first-child,
            #payroll_approvals .table td:first-child {
                position: relative;
                left: auto;
            }
        }

        /* Fix for Bootstrap panel conflicts */
        #payroll_approvals .panel-wrapper.collapse.in {
            overflow: visible;
        }

        /* Ensure no parent elements interfere with sticky positioning */
        #payroll_approvals {
            position: relative;
        }
    </style>
@endsection

@section('page_scripts')
    <script>
        $(document).ready(function() {
            // Multi-select functionality for both tabs
            let selectedItems = [];

            // Initialize batch controls for each tab
            function initBatchControls() {
                // Pending Approvals tab
                initTabBatchControls(
                    '#my_pending_approvals',
                    '#selectAll',
                    '.approval-checkbox',
                    '#batchControlsPending',
                    '#selectedCountPending',
                    '#batchApproveBtnPending',
                    '#clearSelectionBtnPending'
                );

                // Payroll Approvals tab
                initTabBatchControls(
                    '#payroll_approvals',
                    '#selectAllPayroll',
                    '.payroll-checkbox',
                    '#batchControlsPayroll',
                    '#selectedCountPayroll',
                    '#batchApproveBtnPayroll',
                    '#clearSelectionBtnPayroll'
                );
            }

            // Initialize batch controls for a specific tab
            function initTabBatchControls(tabSelector, selectAllSelector, checkboxSelector,
                controlsSelector, countSelector, approveBtnSelector, clearBtnSelector) {

                // Select All functionality
                $(tabSelector + ' ' + selectAllSelector).change(function() {
                    const isChecked = $(this).is(':checked');
                    $(tabSelector + ' ' + checkboxSelector).prop('checked', isChecked);
                    updateSelectedItems(tabSelector, checkboxSelector, controlsSelector, countSelector);
                });

                // Individual checkbox change
                $(document).on('change', tabSelector + ' ' + checkboxSelector, function() {
                    updateSelectedItems(tabSelector, checkboxSelector, controlsSelector, countSelector);

                    // Update select all checkbox state
                    const totalCheckboxes = $(tabSelector + ' ' + checkboxSelector).length;
                    const checkedCheckboxes = $(tabSelector + ' ' + checkboxSelector + ':checked').length;
                    const selectAllCheckbox = $(tabSelector + ' ' + selectAllSelector);

                    if (checkedCheckboxes === 0) {
                        selectAllCheckbox.prop('indeterminate', false).prop('checked', false);
                    } else if (checkedCheckboxes === totalCheckboxes) {
                        selectAllCheckbox.prop('indeterminate', false).prop('checked', true);
                    } else {
                        selectAllCheckbox.prop('indeterminate', true).prop('checked', false);
                    }
                });

                // Clear selection
                $(clearBtnSelector).click(function() {
                    $(tabSelector + ' ' + checkboxSelector).prop('checked', false);
                    $(tabSelector + ' ' + selectAllSelector).prop('checked', false).prop('indeterminate',
                        false);
                    updateSelectedItems(tabSelector, checkboxSelector, controlsSelector, countSelector);
                });

                // Batch approve button
                $(approveBtnSelector).click(function() {
                    const currentSelectedItems = getSelectedItems(tabSelector, checkboxSelector);

                    if (currentSelectedItems.length === 0) {
                        toastr.warning('Please select items to approve.');
                        return;
                    }

                    // Update modal content
                    $('#batchItemCount').text(currentSelectedItems.length);

                    // Show selected items list
                    let itemsList = '<ul class="list-unstyled">';
                    currentSelectedItems.forEach(function(item) {
                        const itemType = item.row.find('td:nth-child(3) .label').text();
                        const submitter = item.row.find('td:nth-child(4)').text().split('\n')[0];
                        itemsList +=
                            `<li><i class="fa fa-check-circle text-success"></i> <strong>${itemType}</strong> by ${submitter}</li>`;
                    });
                    itemsList += '</ul>';

                    // Store current selected items for batch processing
                    selectedItems = currentSelectedItems;
                    $('#batchActionModal').modal('show');
                });
            }

            // Update selected items array and UI for a specific tab
            function updateSelectedItems(tabSelector, checkboxSelector, controlsSelector, countSelector) {
                const currentSelectedItems = getSelectedItems(tabSelector, checkboxSelector);

                // Update selected count and show/hide batch controls
                $(countSelector).text(currentSelectedItems.length);
                if (currentSelectedItems.length > 0) {
                    $(controlsSelector).show();
                } else {
                    $(controlsSelector).hide();
                }
            }

            // Get selected items for a specific tab
            function getSelectedItems(tabSelector, checkboxSelector) {
                const items = [];
                $(tabSelector + ' ' + checkboxSelector + ':checked').each(function() {
                    items.push({
                        model: $(this).data('model'),
                        id: $(this).data('id'),
                        row: $(this).closest('tr')
                    });
                });
                return items;
            }

            // Initialize all batch controls
            initBatchControls();

            // Batch approval form submission
            $('#batchActionForm').submit(function(e) {
                e.preventDefault();

                if (selectedItems.length === 0) {
                    toastr.warning('No items selected.');
                    return;
                }

                const button = $('#batchModalActionBtn');
                const originalText = button.html();
                button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

                // Process each selected item
                let errors = [];
                let successes = [];

                const comments = $('#batchComments').val();

                // Group items by model type for batch processing
                const groupedItems = {};
                selectedItems.forEach(function(item) {
                    if (!groupedItems[item.model]) {
                        groupedItems[item.model] = [];
                    }
                    groupedItems[item.model].push(item.id);
                });

                let processedGroups = 0;
                const totalGroups = Object.keys(groupedItems).length;

                // Process each model type as a batch
                Object.keys(groupedItems).forEach(function(modelType) {
                    $.ajax({
                        url: `/approvals/${modelType}/batch-approve`,
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            model_ids: groupedItems[modelType],
                            comments: comments
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                successes = successes.concat(groupedItems[modelType]);
                            } else {
                                errors.push(`${modelType}: ${response.message}`);
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'An error occurred';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            errors.push(`${modelType}: ${errorMessage}`);
                        },
                        complete: function() {
                            processedGroups++;

                            // When all groups are processed
                            if (processedGroups === totalGroups) {
                                button.prop('disabled', false).html(originalText);
                                $('#batchActionModal').modal('hide');

                                // Show results
                                if (successes.length > 0) {
                                    toastr.success(
                                        `${successes.length} item(s) approved successfully in batch! Single notification email sent.`
                                    );
                                }

                                if (errors.length > 0) {
                                    toastr.error(
                                        `${errors.length} item(s) failed to process.`
                                    );
                                }

                                // Reload page after showing messages
                                setTimeout(function() {
                                    window.location.href = window.location.href;
                                }, 1500);
                            }
                        }
                    });
                });
            });

            // Handle approve button click
            $('.approve-btn').click(function() {
                const model = $(this).data('model');
                const id = $(this).data('id');

                $('#modalTitle').text('Approve Request');
                $('#modalActionBtn').removeClass('btn-danger').addClass('btn-success').text('Approve');
                $('#actionForm').attr('action', `/approvals/${model}/${id}/approve`);

                $('#actionModal').modal('show');
            });

            // Handle reject button click
            $('.reject-btn').click(function() {
                const model = $(this).data('model');
                const id = $(this).data('id');

                $('#modalTitle').text('Reject Request');
                $('#modalActionBtn').removeClass('btn-success').addClass('btn-danger').text('Reject');
                $('#actionForm').attr('action', `/approvals/${model}/${id}/reject`);

                $('#actionModal').modal('show');
            });

            // Handle form submission
            $('#actionForm').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const button = form.find('button[type="submit"]');

                button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    dataType: 'json', // Ensure we're expecting JSON
                    success: function(response) {
                        $('#actionModal').modal('hide');

                        if (response.success) {
                            toastr.success(response.message);

                            // Force page reload after toastr shows
                            setTimeout(function() {
                                window.location.href = window.location
                                    .href; // Full reload
                            }, 800); // Increased delay to ensure toast is visible
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        toastr.error(errorMessage);
                    },
                    complete: function() {
                        // Always re-enable button
                        button.prop('disabled', false)
                            .text(form.find('#modalActionBtn').hasClass('btn-success') ?
                                'Approve' : 'Reject');
                    }
                });
            });

            // Function to add detail column headers dynamically
            function addDetailHeaders() {
                // Find all detail cells in the first row of each table
                $('.table').each(function() {
                    const $table = $(this);
                    const $firstRow = $table.find('tbody tr:first');

                    if ($firstRow.length) {
                        const detailCells = $firstRow.find('.detail-cell');

                        if (detailCells.length) {
                            // Add detail headers before the Actions header
                            const $headerRow = $table.find('thead tr');
                            const $actionsHeader = $headerRow.find('th:last');

                            // Remove any existing detail headers
                            $headerRow.find('.detail-header').remove();

                            // Add new detail headers
                            detailCells.each(function(index) {
                                const headerText = $(this).find('strong').text().replace(':', '');
                                $('<th>')
                                    .addClass('detail-header')
                                    .text(headerText)
                                    .insertBefore($actionsHeader);
                            });
                        }
                    }
                });
            }

            // Call the function to add detail headers
            addDetailHeaders();

            // Initialize sticky header for payroll table
            function initStickyPayrollHeader() {
                const payrollTable = $('#payroll_approvals .table-responsive');
                if (payrollTable.length) {
                    console.log('Payroll table sticky header initialized');

                    // Force redraw to ensure sticky positioning works
                    payrollTable.hide().show(0);

                    // Add scroll event for visual enhancements
                    payrollTable.on('scroll', function() {
                        const scrollTop = $(this).scrollTop();
                        const thead = $(this).find('thead');

                        // Add shadow when scrolled
                        if (scrollTop > 0) {
                            thead.css('box-shadow', '0 2px 8px rgba(0,0,0,0.15)');
                        } else {
                            thead.css('box-shadow', '0 2px 3px rgba(0,0,0,0.1)');
                        }
                    });
                }
            }

            // Initialize when document is ready
            initStickyPayrollHeader();

            // Re-initialize when tab is shown (in case of dynamic loading)
            $('a[href="#payroll_approvals"]').on('shown.bs.tab', function() {
                setTimeout(initStickyPayrollHeader, 100);
            });
        });

        // Confirmation functions for delegation actions
        function confirmDelete(delegationId) {
            if (confirm('Are you sure you want to delete this delegation? This action cannot be undone.')) {
                document.getElementById('delete-form-' + delegationId).submit();
            }
        }

        function confirmDeactivate(delegationId) {
            if (confirm(
                    'Are you sure you want to deactivate this delegation? The delegate will no longer receive notifications.'
                )) {
                document.getElementById('deactivate-form-' + delegationId).submit();
            }
        }

        function confirmActivate(delegationId) {
            if (confirm(
                    'Are you sure you want to activate this delegation? The delegate will start receiving notifications again.'
                )) {
                document.getElementById('activate-form-' + delegationId).submit();
            }
        }
    </script>
    @include('admin.partials.drag-scroll-script')

    <!-- Hidden forms for delegation actions -->
    @if ($myDelegations->count() > 0)
        @foreach ($myDelegations as $delegation)
            <!-- Delete form -->
            <form id="delete-form-{{ $delegation->id }}"
                action="{{ route('ess.approval.delegations.destroy', $delegation->id) }}" method="POST"
                style="display: none;">
                @csrf
                @method('DELETE')
            </form>

            <!-- Deactivate form -->
            <form id="deactivate-form-{{ $delegation->id }}"
                action="{{ route('ess.approval.delegations.deactivate', $delegation->id) }}" method="POST"
                style="display: none;">
                @csrf
            </form>

            <!-- Activate form (toggle status) -->
            <form id="activate-form-{{ $delegation->id }}"
                action="{{ route('ess.approval.delegations.toggle-status', $delegation->id) }}" method="POST"
                style="display: none;">
                @csrf
            </form>
        @endforeach
    @endif
@endsection
