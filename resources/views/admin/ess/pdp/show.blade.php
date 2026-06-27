@extends('admin.master')
@section('content')
@section('title')
    Development Plan - {{ $plan->plan_title }}
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ess.pdp.myPlans') }}">My Development Plans</a></li>
                <li class="breadcrumb-item active">{{ $plan->plan_title }}</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="white-box">
                @include('admin.partials.alert')

                <h3>{{ $plan->plan_title }} ({{ $plan->plan_year }})</h3>
                <p><strong>Period:</strong> {{ $plan->start_date?->format('Y-m-d') }} - {{ $plan->end_date?->format('Y-m-d') }}</p>
                <p><strong>Review Frequency:</strong> {{ ucfirst(str_replace('_', '-', $plan->review_frequency)) }}</p>
                <p><strong>Status:</strong> {{ ucfirst($plan->status) }} | <strong>Progress:</strong> {{ $plan->averageProgress() }}%</p>

                @if($plan->development_focus)
                    <p><strong>Development Focus:</strong> {{ $plan->development_focus }}</p>
                @endif

                <div class="row" style="margin-top: 15px;">
                    <div class="col-md-12">
                        <a href="{{ route('ess.pdp.pdf', $plan->pdp_plan_id) }}" class="btn btn-default btn-sm" target="_blank">
                            <i class="fa fa-file-pdf-o"></i> Print PDF
                        </a>
                        @if($plan->canBeEdited())
                            <a href="{{ route('pdp.goal.index', $plan->pdp_plan_id) }}" class="btn btn-success btn-sm"><i class="fa fa-bullseye"></i> Manage Goals</a>
                            <a href="{{ route('pdp.progress.index', $plan->pdp_plan_id) }}" class="btn btn-info btn-sm"><i class="fa fa-line-chart"></i> Record Progress</a>
                        @endif
                        @if(!$plan->employee_acknowledged)
                            <form action="{{ route('pdp.plan.employeeAcknowledge', $plan->pdp_plan_id) }}" method="POST" style="display:inline-block; margin-top:8px;">
                                @csrf
                                <input type="text" name="comments" class="form-control input-sm" placeholder="Your comments (optional)" style="display:inline-block; width:220px; vertical-align:middle;">
                                <button type="submit" class="btn btn-warning btn-sm">Acknowledge Plan</button>
                            </form>
                        @endif
                        @if($plan->status == 'draft')
                            <form action="{{ route('pdp.plan.activate', $plan->pdp_plan_id) }}" method="POST" style="display:inline">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">Activate Plan</button>
                            </form>
                        @endif
                    </div>
                </div>

                <hr>
                <h4>Goals</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="tr_header">
                                <th>Goal</th>
                                <th>Status</th>
                                <th>Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($plan->goals as $goal)
                                <tr>
                                    <td><strong>{{ $goal->goal_title }}</strong><br><small>{{ $goal->smart_objective }}</small></td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $goal->status)) }}</td>
                                    <td>{{ $goal->overall_progress }}%</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center">No goals added yet. Use Manage Goals to add your development objectives.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
