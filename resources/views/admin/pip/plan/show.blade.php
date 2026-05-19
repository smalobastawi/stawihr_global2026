@extends('admin.master')
@section('content')
@section('title')
PIP Details - {{ $plan->employee ? $plan->employee->full_name : '' }}
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                @foreach (urlTree() as $item)
                    <li class="breadcrumb-item text-primary"><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
                @endforeach
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="white-box">
                <div class="row">
                    <div class="col-md-6">
                        <h3 class="box-title">Performance Improvement Plan</h3>
                        <p><strong>Employee:</strong> {{ $plan->employee ? $plan->employee->full_name : '' }}</p>
                        <p><strong>Position:</strong> {{ $plan->position ?? 'N/A' }}</p>
                        <p><strong>Department:</strong> {{ $plan->department ? $plan->department->department_name : 'N/A' }}</p>
                        <p><strong>Period:</strong> {{ $plan->plan_period_start ? $plan->plan_period_start->format('Y-m-d') : '' }} - {{ $plan->plan_period_end ? $plan->plan_period_end->format('Y-m-d') : '' }}</p>
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
                        <p><strong>Trigger:</strong> {{ ucfirst(str_replace('_', ' ', $plan->trigger_type)) }} {{ $plan->trigger_score ? '(' . $plan->trigger_score . ')' : '' }}</p>
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
                            @if(!$plan->supervisor_signed && $plan->status != 'completed')
                                <form action="{{ route('pip.plan.supervisorSign', $plan->pip_id) }}" method="POST" style="display:inline;margin-left:10px;">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-success">Sign</button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert {{ $plan->hr_validated ? 'alert-success' : 'alert-warning' }}">
                            <strong>HR Validated:</strong> {{ $plan->hr_validated ? 'Yes (' . $plan->hr_validation_date . ')' : 'No' }}
                            @if(!$plan->hr_validated && $plan->status != 'completed')
                                <form action="{{ route('pip.plan.hrValidate', $plan->pip_id) }}" method="POST" style="display:inline;margin-left:10px;">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-success">Validate</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-top: 15px;">
                    <div class="col-md-12">
                        @if($plan->status == 'draft')
                            <form action="{{ route('pip.plan.activate', $plan->pip_id) }}" method="POST" style="display:inline">
                                @csrf
                                <button type="submit" class="btn btn-info">Activate PIP</button>
                            </form>
                        @endif
                        @if($plan->status == 'active' || $plan->status == 'in_review')
                            <button class="btn btn-warning" data-toggle="modal" data-target="#outcomeModal">Finalize Outcome</button>
                        @endif
                        @if(!$plan->is_locked && $plan->outcome != 'pending')
                            <form action="{{ route('pip.plan.lock', $plan->pip_id) }}" method="POST" style="display:inline">
                                @csrf
                                <button type="submit" class="btn btn-danger">Lock PIP</button>
                            </form>
                        @endif
                        <a href="{{ route('pip.goal.index', $plan->pip_id) }}" class="btn btn-primary">Goals</a>
                        <a href="{{ route('pip.support.index', $plan->pip_id) }}" class="btn btn-primary">Support</a>
                        <a href="{{ route('pip.schedule.index', $plan->pip_id) }}" class="btn btn-primary">Reviews</a>
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
                <div class="panel-heading"><i class="mdi mdi-target fa-fw"></i> Improvement Goals</div>
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
                <div class="panel-heading"><i class="mdi mdi-handshake fa-fw"></i> Support & Resources</div>
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
                <div class="panel-heading"><i class="mdi mdi-calendar-check fa-fw"></i> Review Schedule</div>
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
</div>

<div class="modal fade" id="outcomeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('pip.plan.finalizeOutcome', $plan->pip_id) }}" method="POST">
                @csrf
            <div class="modal-header">
                <h4 class="modal-title">Finalize PIP Outcome</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Outcome<span class="validateRq">*</span></label>
                    <select name="outcome" class="form-control" required>
                        <option value="successful_completion">Successful Completion</option>
                        <option value="partial_improvement">Partial Improvement</option>
                        <option value="failure">Failure to Improve</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Outcome Notes</label>
                    <textarea name="outcome_notes" class="form-control" rows="3" placeholder="Notes on the outcome"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning">Finalize</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection
