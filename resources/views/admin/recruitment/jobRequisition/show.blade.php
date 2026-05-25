@extends('admin.master')

@section('title', trans('job_requisition.job_requisition_details'))

@section('content')

    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                            @lang('dashboard.dashboard')</a></li>
                    <li><a href="{{ route('jobRequisition.index') }}">@lang('job_requisition.job_requisition_list')</a></li>
                    <li>@yield('title')</li>
                </ol>
            </div>
            <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
                <a href="{{ route('jobRequisition.index') }}"
                    class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                    <i class="fa fa-list-ul" aria-hidden="true"></i> @lang('job_requisition.view_requisitions')
                </a>
                @if ($result->canEdit())
                    <a href="{{ route('jobRequisition.edit', $result->job_requisition_id) }}"
                        class="btn btn-info pull-right m-l-10 hidden-xs hidden-sm waves-effect waves-light">
                        <i class="fa fa-pencil" aria-hidden="true"></i> @lang('common.edit')
                    </a>
                @endif
                @if ($result->canSubmitForApproval())
                    <form method="POST" action="{{ route('jobRequisition.submit', $result->job_requisition_id) }}"
                        style="display: inline-block;">
                        @csrf
                        <button type="submit"
                            class="btn btn-warning pull-right m-l-10 hidden-xs hidden-sm waves-effect waves-light"
                            onclick="return confirm('@lang('job_requisition.confirm_submit')')">
                            <i class="fa fa-paper-plane" aria-hidden="true"></i> @lang('job_requisition.submit_for_approval')
                        </button>
                    </form>
                @endif
                @if ($result->canApprove())
                    <a href="{{ route('jobRequisition.approve.form', $result->job_requisition_id) }}"
                        class="btn btn-success pull-right m-l-10 hidden-xs hidden-sm waves-effect waves-light">
                        <i class="fa fa-check" aria-hidden="true"></i> @lang('common.approve')
                    </a>
                    <a href="{{ route('jobRequisition.reject.form', $result->job_requisition_id) }}"
                        class="btn btn-danger pull-right m-l-10 hidden-xs hidden-sm waves-effect waves-light">
                        <i class="fa fa-times" aria-hidden="true"></i> @lang('common.reject')
                    </a>
                @endif
                @if ($result->requiresApproval() && $result->status == $result::STATUS_PENDING_APPROVAL)
                    <span class="label label-warning pull-right m-l-10" style="margin-top: 6px;">
                        <i class="fa fa-sitemap"></i> Workflow Approval Active
                    </span>
                @endif
                @if ($result->canConvertToJob())
                    <form method="POST" action="{{ route('jobRequisition.convert', $result->job_requisition_id) }}"
                        style="display: inline-block;">
                        @csrf
                        <button type="submit"
                            class="btn btn-primary pull-right m-l-10 hidden-xs hidden-sm waves-effect waves-light"
                            onclick="return confirm('@lang('job_requisition.confirm_convert')')">
                            <i class="fa fa-briefcase" aria-hidden="true"></i> @lang('job_requisition.convert_to_job')
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @if (session()->has('success'))
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                    </div>
                </div>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-clipboard-text fa-fw"></i>
                        DAKAWOU TRANSPORT LIMITED - JOB REQUISITION FORM
                        <span class="pull-right">
                            <span class="label {{ $result->status_class }}">{{ $result->status_label }}</span>
                        </span>
                        <br><small>Requisition No: {{ $result->requisition_number }}</small>
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">

                            {{-- 1. POSITION DETAILS --}}
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="text-info"><i class="fa fa-briefcase"></i> 1. POSITION DETAILS</h4>
                                    <hr class="m-t-0 m-b-10">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td width="40%"><strong>Department</strong></td>
                                            <td>{{ $result->department ? $result->department->department_name : 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Job Title</strong></td>
                                            <td>{{ $result->position_title }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>No. of Positions</strong></td>
                                            <td>{{ $result->number_of_positions }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Employment Type</strong></td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $result->employment_type)) }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td width="40%"><strong>Work Location</strong></td>
                                            <td>{{ $result->work_location ?: ($result->location ? $result->location->location_name : 'Not specified') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Proposed Start Date</strong></td>
                                            <td>{{ $result->proposed_start_date ? date('d M Y', strtotime($result->proposed_start_date)) : 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Required By Date</strong></td>
                                            <td>{{ date('d M Y', strtotime($result->required_by_date)) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Urgency Level</strong></td>
                                            <td><span class="label {{ $result->urgency_class }}">{{ $result->urgency_label }}</span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            {{-- 2. REASON FOR REQUISITION --}}
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="text-info"><i class="fa fa-question-circle"></i> 2. REASON FOR REQUISITION</h4>
                                    <hr class="m-t-0 m-b-10">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td width="25%"><strong>Requisition Type</strong></td>
                                            <td>{{ $result->requisition_type_label }}</td>
                                        </tr>
                                        @if ($result->requisition_type === 'replacement')
                                            <tr>
                                                <td><strong>Employee Being Replaced</strong></td>
                                                <td>{{ $result->replaced_employee_name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Reason for Replacement</strong></td>
                                                <td>
                                                    {{ $result->replacement_reason_label }}
                                                    @if ($result->replacement_reason === 'other' && $result->replacement_reason_other)
                                                        - {{ $result->replacement_reason_other }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>

                            {{-- 3. JOB DETAILS --}}
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="text-info"><i class="fa fa-tasks"></i> 3. JOB DETAILS</h4>
                                    <hr class="m-t-0 m-b-10">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td width="40%"><strong>Reporting To</strong></td>
                                            <td>{{ $result->reporting_manager }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Job Type</strong></td>
                                            <td>{{ ucfirst($result->job_type) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Recruitment Source</strong></td>
                                            <td>{{ ucfirst($result->recruitment_source) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h5><strong>Key Responsibilities</strong></h5>
                                    <div class="well">
                                        {!! $result->key_responsibilities ?: 'Not provided.' !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5><strong>Job Description</strong></h5>
                                    <div class="well">
                                        {!! $result->job_description !!}
                                    </div>
                                </div>
                            </div>

                            {{-- 4. REQUIREMENTS --}}
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="text-info"><i class="fa fa-graduation-cap"></i> 4. REQUIREMENTS</h4>
                                    <hr class="m-t-0 m-b-10">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <h5><strong>Minimum Qualifications</strong></h5>
                                    <div class="well">
                                        {!! $result->minimum_qualifications ?: 'Not provided.' !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <h5><strong>Experience Required</strong></h5>
                                    <div class="well">
                                        {!! $result->experience_required ?: 'Not provided.' !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <h5><strong>Skills & Competencies</strong></h5>
                                    <div class="well">
                                        {!! $result->skills_competencies ?: 'Not provided.' !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h5><strong>Job Requirements</strong></h5>
                                    <div class="well">
                                        {!! $result->job_requirements !!}
                                    </div>
                                </div>
                            </div>

                            {{-- 5. COMPENSATION DETAILS --}}
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="text-info"><i class="fa fa-money"></i> 5. COMPENSATION DETAILS</h4>
                                    <hr class="m-t-0 m-b-10">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td width="40%"><strong>Proposed Salary Range</strong></td>
                                            <td>
                                                @if ($result->minimum_salary || $result->maximum_salary)
                                                    @if ($result->minimum_salary && $result->maximum_salary)
                                                        {{ number_format($result->minimum_salary, 2) }} - {{ number_format($result->maximum_salary, 2) }}
                                                    @elseif($result->minimum_salary)
                                                        Min: {{ number_format($result->minimum_salary, 2) }}
                                                    @elseif($result->maximum_salary)
                                                        Max: {{ number_format($result->maximum_salary, 2) }}
                                                    @endif
                                                    {{ $result->currency }}
                                                @else
                                                    Not specified
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Other Benefits</strong></td>
                                            <td>{!! $result->other_benefits ?: 'Not provided.' !!}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            {{-- 6. JUSTIFICATION FOR HIRE --}}
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="text-info"><i class="fa fa-file-text-o"></i> 6. JUSTIFICATION FOR HIRE</h4>
                                    <hr class="m-t-0 m-b-10">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <h5><strong>Reason for Requisition</strong></h5>
                                    <div class="well">
                                        {!! $result->reason_for_requisition !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <h5><strong>Justification for Hire</strong></h5>
                                    <div class="well">
                                        {!! $result->justification_for_hire ?: 'Not provided.' !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <h5><strong>Budget Justification</strong></h5>
                                    <div class="well">
                                        {!! $result->budget_justification ?: 'Not provided.' !!}
                                    </div>
                                </div>
                            </div>

                            {{-- 7. APPROVALS --}}
                            @if (in_array($result->status, [App\Models\JobRequisition::STATUS_APPROVED, App\Models\JobRequisition::STATUS_REJECTED]))
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4 class="text-info"><i class="fa fa-check-circle"></i> 7. APPROVALS</h4>
                                        <hr class="m-t-0 m-b-10">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Approver</th>
                                                    <th>Name</th>
                                                    <th>Date</th>
                                                    <th>Comments</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><strong>Final Approver</strong></td>
                                                    <td>
                                                        @isset($result->approvedBy)
                                                            {{ $result->approvedBy->first_name }} {{ $result->approvedBy->last_name }}
                                                        @endisset
                                                    </td>
                                                    <td>
                                                        @if ($result->approved_at)
                                                            {{ date('d M Y H:i', strtotime($result->approved_at)) }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $result->approval_comments ?: '-' }}</td>
                                                </tr>
                                                @if ($result->status == App\Models\JobRequisition::STATUS_REJECTED && $result->rejection_reason)
                                                    <tr class="danger">
                                                        <td><strong>Rejection Reason</strong></td>
                                                        <td colspan="3">{{ $result->rejection_reason }}</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            {{-- 8. HR USE ONLY --}}
                            @if ($result->date_received || $result->approved_salary_range || $result->hr_recruitment_method || $result->hr_remarks)
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4 class="text-info"><i class="fa fa-lock"></i> 8. HR USE ONLY</h4>
                                        <hr class="m-t-0 m-b-10">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-bordered">
                                            <tr>
                                                <td width="40%"><strong>Date Received</strong></td>
                                                <td>{{ $result->date_received ? date('d M Y', strtotime($result->date_received)) : 'Not recorded' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Approved Salary Range</strong></td>
                                                <td>{{ $result->approved_salary_range ?: 'Not recorded' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Recruitment Method</strong></td>
                                                <td>{{ $result->hr_recruitment_method ? ucfirst($result->hr_recruitment_method) : 'Not recorded' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Remarks</strong></td>
                                                <td>{!! $result->hr_remarks ?: 'Not recorded' !!}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            <!-- Conversion Information -->
                            @if ($result->is_converted_to_job && $result->job)
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4 class="text-info"><i class="fa fa-briefcase"></i> @lang('job_requisition.conversion_information')</h4>
                                        <hr class="m-t-0 m-b-10">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-bordered">
                                            <tr>
                                                <td width="25%"><strong>@lang('job_requisition.converted_to_job')</strong></td>
                                                <td>
                                                    <a href="{{ route('jobPost.edit', $result->converted_job_id) }}" target="_blank">
                                                        {{ $result->job->job_title }}
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('job_requisition.converted_at')</strong></td>
                                                <td>
                                                    @isset($result->convertedBy)
                                                        {{ $result->convertedBy->first_name }} {{ $result->convertedBy->last_name }} -
                                                    @endisset
                                                    @if ($result->converted_at)
                                                        {{ date('d M Y H:i', strtotime($result->converted_at)) }}
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            @if ($result->requiresApproval())
                                <!-- Workflow Approval Status -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4 class="text-info"><i class="fa fa-sitemap"></i> Approval Workflow Status</h4>
                                        <hr class="m-t-0 m-b-10">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-bordered">
                                            <tr class="bg-warning">
                                                <td colspan="3"><strong><i class="fa fa-info-circle"></i> This requisition uses workflow-based approval.</strong></td>
                                            </tr>
                                            @foreach ($result->approvalLogs()->with('approvalStep')->get() as $log)
                                                <tr>
                                                    <td width="30%"><strong>{{ $log->approvalStep->title ?? 'Step' }}</strong></td>
                                                    <td width="20%">
                                                        @if ($log->action == 'pending')
                                                            <span class="label label-warning"><i class="fa fa-clock-o"></i> Pending</span>
                                                        @elseif ($log->action == 'approved')
                                                            <span class="label label-success"><i class="fa fa-check"></i> Approved</span>
                                                        @elseif ($log->action == 'rejected')
                                                            <span class="label label-danger"><i class="fa fa-times"></i> Rejected</span>
                                                        @else
                                                            <span class="label label-default">{{ ucfirst($log->action) }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($log->action_date)
                                                            {{ date('d M Y H:i', strtotime($log->action_date)) }}
                                                            @if ($log->user)
                                                                by {{ $log->user->first_name }} {{ $log->user->last_name }}
                                                            @endif
                                                        @else
                                                            <span class="text-muted">Waiting...</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            @endif

                            <!-- Audit Information -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="text-info"><i class="fa fa-history"></i> @lang('job_requisition.audit_information')</h4>
                                    <hr class="m-t-0 m-b-10">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td width="40%"><strong>@lang('job_requisition.requested_by')</strong></td>
                                            <td>
                                                @isset($result->requestedBy)
                                                    {{ $result->requestedBy->first_name }} {{ $result->requestedBy->last_name }}
                                                    <br><small class="text-muted">{{ date('d M Y H:i', strtotime($result->created_at)) }}</small>
                                                @endisset
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('common.updated_at')</strong></td>
                                            <td>{{ date('d M Y H:i', strtotime($result->updated_at)) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    @if ($result->canSubmitForApproval())
                                        <form method="POST"
                                            action="{{ route('jobRequisition.submit', $result->job_requisition_id) }}"
                                            style="display: inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-lg"
                                                onclick="return confirm('@lang('job_requisition.confirm_submit')')">
                                                <i class="fa fa-paper-plane"></i> @lang('job_requisition.submit_for_approval')
                                            </button>
                                        </form>
                                    @endif
                                    @if ($result->canApprove())
                                        <a href="{{ route('jobRequisition.approve.form', $result->job_requisition_id) }}"
                                            class="btn btn-success btn-lg">
                                            <i class="fa fa-check"></i> @lang('common.approve')
                                        </a>
                                        <a href="{{ route('jobRequisition.reject.form', $result->job_requisition_id) }}"
                                            class="btn btn-danger btn-lg">
                                            <i class="fa fa-times"></i> @lang('common.reject')
                                        </a>
                                    @endif
                                    @if ($result->canConvertToJob())
                                        <form method="POST"
                                            action="{{ route('jobRequisition.convert', $result->job_requisition_id) }}"
                                            style="display: inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-lg"
                                                onclick="return confirm('@lang('job_requisition.confirm_convert')')">
                                                <i class="fa fa-briefcase"></i> @lang('job_requisition.convert_to_job')
                                            </button>
                                        </form>
                                    @endif
                                    @if ($result->status === App\Models\JobRequisition::STATUS_DRAFT || $result->status === App\Models\JobRequisition::STATUS_REJECTED)
                                        <a href="{{ route('jobRequisition.edit', $result->job_requisition_id) }}"
                                            class="btn btn-info btn-lg">
                                            <i class="fa fa-pencil"></i> @lang('common.edit')
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page_scripts')
<script type="text/javascript">
    $(document).ready(function() {
        // Ensure preloader is hidden
        $('.preloader').fadeOut();
    });
</script>
@endsection
