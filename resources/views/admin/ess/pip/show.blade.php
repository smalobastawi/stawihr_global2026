@extends('admin.master')
@section('content')
@section('title')
    My PIP Details
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ess.pip.myPlans') }}">My PIP Plans</a></li>
                <li class="breadcrumb-item active">PIP Details</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="white-box">
                <div class="row">
                    <div class="col-md-6">
                        <h3 class="box-title">My Performance Improvement Plan</h3>
                        <p><strong>Period:</strong> {{ $plan->plan_period_start ? $plan->plan_period_start->format('Y-m-d') : '' }} - {{ $plan->plan_period_end ? $plan->plan_period_end->format('Y-m-d') : '' }}</p>
                        <p><strong>Department:</strong> {{ $plan->department ? $plan->department->department_name : 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 text-right">
                        <p><strong>Status:</strong>
                            @if($plan->status == 'draft')
                                <span class="label label-default">Draft</span>
                            @elseif($plan->status == 'active')
                                <span class="label label-info">Active</span>
                            @elseif($plan->status == 'in_review')
                                <span class="label label-warning">In Review</span>
                            @elseif($plan->status == 'completed')
                                <span class="label label-success">Completed</span>
                            @elseif($plan->status == 'extended')
                                <span class="label label-primary">Extended</span>
                            @else
                                <span class="label label-danger">Cancelled</span>
                            @endif
                        </p>
                        <p><strong>Outcome:</strong>
                            @if($plan->outcome == 'pending')
                                <span class="label label-default">Pending</span>
                            @elseif($plan->outcome == 'successful_completion')
                                <span class="label label-success">Success</span>
                            @elseif($plan->outcome == 'partial_improvement')
                                <span class="label label-warning">Partial</span>
                            @else
                                <span class="label label-danger">Failure</span>
                            @endif
                        </p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <div class="alert {{ $plan->employee_acknowledged ? 'alert-success' : 'alert-warning' }}">
                            <strong>Employee Acknowledged:</strong> {{ $plan->employee_acknowledged ? 'Yes (' . $plan->employee_ack_date . ')' : 'No' }}
                            @if(!$plan->employee_acknowledged && $plan->status != 'completed')
                                <form action="{{ route('pip.plan.employeeAcknowledge', $plan->pip_id) }}" method="POST" style="display:inline;margin-left:10px;">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-success">Acknowledge</button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert {{ $plan->supervisor_signed ? 'alert-success' : 'alert-warning' }}">
                            <strong>Supervisor Signed:</strong> {{ $plan->supervisor_signed ? 'Yes (' . $plan->supervisor_sign_date . ')' : 'No' }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert {{ $plan->hr_validated ? 'alert-success' : 'alert-warning' }}">
                            <strong>HR Validated:</strong> {{ $plan->hr_validated ? 'Yes (' . $plan->hr_validation_date . ')' : 'No' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="mdi mdi-alert fa-fw"></i> Purpose</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <p>{{ $plan->purpose }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($plan->concerns->count() > 0)
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-danger">
                <div class="panel-heading"><i class="mdi mdi-crosshairs fa-fw"></i> Areas of Concern</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>Goal</th>
                                        <th>Actual Score</th>
                                        <th>Target Score</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($plan->concerns as $concern)
                                        <tr>
                                            <td>{{ $concern->goal ? $concern->goal->performance_metric : '' }}</td>
                                            <td>{{ $concern->actual_score }}</td>
                                            <td>{{ $concern->target_score }}</td>
                                            <td>{{ $concern->description }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($plan->goals->count() > 0)
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-target fa-fw"></i> My Improvement Goals</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>Objective</th>
                                        <th>Action Required</th>
                                        <th>Target KPI</th>
                                        <th>Deadline</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($plan->goals as $goal)
                                        <tr>
                                            <td>{{ $goal->objective }}</td>
                                            <td>{{ $goal->action_required }}</td>
                                            <td>{{ $goal->target_kpi }}</td>
                                            <td>{{ $goal->deadline ? $goal->deadline->format('Y-m-d') : '' }}</td>
                                            <td><span class="label label-{{ $goal->status == 'completed' ? 'success' : ($goal->status == 'overdue' ? 'danger' : 'warning') }}">{{ ucfirst(str_replace('_', ' ', $goal->status)) }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($plan->supportResources->count() > 0)
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-success">
                <div class="panel-heading"><i class="mdi mdi-handshake fa-fw"></i> Support & Resources Available to Me</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Provider</th>
                                        <th>Scheduled Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($plan->supportResources as $resource)
                                        <tr>
                                            <td>{{ ucfirst($resource->support_type) }}</td>
                                            <td>{{ $resource->description }}</td>
                                            <td>{{ ucfirst($resource->provider) }}</td>
                                            <td>{{ $resource->scheduled_date ? $resource->scheduled_date->format('Y-m-d') : '' }}</td>
                                            <td><span class="label label-{{ $resource->status == 'completed' ? 'success' : ($resource->status == 'cancelled' ? 'danger' : 'info') }}">{{ ucfirst($resource->status) }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($plan->reviewSchedules->count() > 0)
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-warning">
                <div class="panel-heading"><i class="mdi mdi-calendar-check fa-fw"></i> My Review Schedule</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>Stage</th>
                                        <th>Scheduled Date</th>
                                        <th>Status</th>
                                        <th>Conducted By</th>
                                        <th>Comments</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($plan->reviewSchedules as $schedule)
                                        <tr>
                                            <td>{{ $schedule->review_stage }}</td>
                                            <td>{{ $schedule->scheduled_date ? $schedule->scheduled_date->format('Y-m-d') : '' }}</td>
                                            <td><span class="label label-{{ $schedule->status == 'completed' ? 'success' : ($schedule->status == 'missed' ? 'danger' : ($schedule->status == 'rescheduled' ? 'info' : 'warning')) }}">{{ ucfirst($schedule->status) }}</span></td>
                                            <td>{{ $schedule->conductor ? $schedule->conductor->full_name : '-' }}</td>
                                            <td>{{ $schedule->comments }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($plan->outcome_notes)
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info">
                <strong>Outcome Notes:</strong> {{ $plan->outcome_notes }}
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <a href="{{ route('ess.pip.myPlans') }}" class="btn btn-info btn_style"><i class="fa fa-arrow-left"></i> Back to My PIP Plans</a>
            @if(!$plan->employee_acknowledged && $plan->status != 'completed')
                <form action="{{ route('pip.plan.employeeAcknowledge', $plan->pip_id) }}" method="POST" style="display:inline">
                    @csrf
                    <button type="submit" class="btn btn-success btn_style"><i class="fa fa-check"></i> Acknowledge PIP</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
