@extends('admin.master')
@section('content')
@section('title')
PDP Details - {{ $plan->employee ? $plan->employee->full_name : '' }}
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
                @include('admin.partials.alert')

                <div class="row">
                    <div class="col-md-6">
                        <h3 class="box-title">{{ $plan->plan_title }}</h3>
                        <p><strong>Employee:</strong> {{ $plan->employee ? $plan->employee->full_name : '' }}</p>
                        <p><strong>Department:</strong> {{ $plan->department ? $plan->department->department_name : 'N/A' }}</p>
                        <p><strong>Supervisor:</strong> {{ $plan->supervisor ? $plan->supervisor->full_name : 'N/A' }}</p>
                        <p><strong>Period:</strong> {{ $plan->start_date?->format('Y-m-d') }} - {{ $plan->end_date?->format('Y-m-d') }} ({{ $plan->plan_year }})</p>
                        <p><strong>Review Frequency:</strong> {{ ucfirst(str_replace('_', '-', $plan->review_frequency)) }}</p>
                        <p><strong>Overall Progress:</strong> {{ $plan->averageProgress() }}%</p>
                    </div>
                    <div class="col-md-6 text-right">
                        <p><strong>Status:</strong> <span class="label label-info">{{ ucfirst($plan->status) }}</span></p>
                        <p><strong>Acknowledged:</strong> {{ $plan->employee_acknowledged ? 'Yes' : 'No' }}</p>
                        <p><strong>Supervisor Approved:</strong> {{ $plan->supervisor_approved ? 'Yes' : 'No' }}</p>
                        <p><strong>HR Reviewed:</strong> {{ $plan->hr_reviewed ? 'Yes' : 'No' }}</p>
                    </div>
                </div>

                @if($plan->development_focus)
                    <p><strong>Development Focus:</strong> {{ $plan->development_focus }}</p>
                @endif
                @if($plan->career_aspirations)
                    <p><strong>Career Aspirations:</strong> {{ $plan->career_aspirations }}</p>
                @endif

                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <a href="{{ route('pdp.plan.pdf', $plan->pdp_plan_id) }}" class="btn btn-default btn-sm" target="_blank">
                            <i class="fa fa-file-pdf-o"></i> Print PDF
                        </a>
                        @if($plan->status == 'draft')
                            <form action="{{ route('pdp.plan.activate', $plan->pdp_plan_id) }}" method="POST" style="display:inline">
                                @csrf
                                <button type="submit" class="btn btn-info btn-sm">Activate Plan</button>
                            </form>
                        @endif
                        @if($plan->status == 'active')
                            <form action="{{ route('pdp.plan.complete', $plan->pdp_plan_id) }}" method="POST" style="display:inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">Mark Completed</button>
                            </form>
                        @endif
                        @if(!$plan->employee_acknowledged)
                            <form action="{{ route('pdp.plan.employeeAcknowledge', $plan->pdp_plan_id) }}" method="POST" style="display:inline-block; margin-right:8px;">
                                @csrf
                                <input type="text" name="comments" class="form-control input-sm" placeholder="Employee comments (optional)" style="display:inline-block; width:220px; vertical-align:middle;">
                                <button type="submit" class="btn btn-warning btn-sm">Acknowledge</button>
                            </form>
                        @endif
                        @if(!$plan->supervisor_approved)
                            <form action="{{ route('pdp.plan.supervisorApprove', $plan->pdp_plan_id) }}" method="POST" style="display:inline-block; margin-right:8px;">
                                @csrf
                                <input type="text" name="comments" class="form-control input-sm" placeholder="Supervisor comments (optional)" style="display:inline-block; width:220px; vertical-align:middle;">
                                <button type="submit" class="btn btn-primary btn-sm">Supervisor Approve</button>
                            </form>
                        @endif
                        @if(!$plan->hr_reviewed)
                            <form action="{{ route('pdp.plan.hrReview', $plan->pdp_plan_id) }}" method="POST" style="display:inline-block; margin-right:8px;">
                                @csrf
                                <input type="text" name="comments" class="form-control input-sm" placeholder="HR comments (optional)" style="display:inline-block; width:220px; vertical-align:middle;">
                                <button type="submit" class="btn btn-default btn-sm">HR Review</button>
                            </form>
                        @endif
                        <a href="{{ route('pdp.goal.index', $plan->pdp_plan_id) }}" class="btn btn-success btn-sm"><i class="fa fa-bullseye"></i> Manage Goals</a>
                        <a href="{{ route('pdp.progress.index', $plan->pdp_plan_id) }}" class="btn btn-info btn-sm"><i class="fa fa-line-chart"></i> Progress Updates</a>
                        @if($plan->canBeEdited())
                            <a href="{{ route('pdp.plan.edit', $plan->pdp_plan_id) }}" class="btn btn-default btn-sm"><i class="fa fa-pencil"></i> Edit Plan</a>
                        @endif
                    </div>
                </div>

                <hr>
                <h4>Development Goals</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="tr_header">
                                <th>Goal</th>
                                <th>Competency</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Progress</th>
                                <th>Target Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($plan->goals as $goal)
                                <tr>
                                    <td>
                                        <strong>{{ $goal->goal_title }}</strong><br>
                                        <small>{{ \Illuminate\Support\Str::limit($goal->smart_objective, 120) }}</small>
                                    </td>
                                    <td>{{ $goal->competency_area ?? 'N/A' }}</td>
                                    <td>{{ ucfirst($goal->priority) }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $goal->status)) }}</td>
                                    <td>{{ $goal->overall_progress }}%</td>
                                    <td>{{ $goal->target_completion_date ? $goal->target_completion_date->format('Y-m-d') : 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">No goals added yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
