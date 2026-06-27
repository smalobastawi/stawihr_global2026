@extends('admin.master')
@section('content')
@section('title')
Progress Updates - {{ $plan->plan_title }}
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
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            @if($plan->canBeEdited())
            <a href="{{ route('pdp.progress.create', $plan->pdp_plan_id) }}" class="btn btn-success pull-right m-l-20">
                <i class="fa fa-plus-circle"></i> Record Progress
            </a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @include('admin.partials.alert')
                        <p><strong>Employee:</strong> {{ $plan->employee ? $plan->employee->full_name : '' }} | <strong>Review Frequency:</strong> {{ ucfirst(str_replace('_', '-', $plan->review_frequency)) }}</p>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>Period</th>
                                        <th>Goal</th>
                                        <th>Progress</th>
                                        <th>Summary</th>
                                        <th>Status</th>
                                        <th>Entered By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($plan->progressEntries as $entry)
                                        <tr>
                                            <td>{{ $entry->review_period_label }}</td>
                                            <td>{{ $entry->goal ? $entry->goal->goal_title : 'Plan Level' }}</td>
                                            <td>{{ $entry->progress_percentage }}%</td>
                                            <td>{{ \Illuminate\Support\Str::limit($entry->achievement_summary, 80) }}</td>
                                            <td>{{ ucfirst($entry->status) }}</td>
                                            <td>{{ $entry->enteredBy ? $entry->enteredBy->full_name : 'N/A' }}</td>
                                            <td>
                                                @if($entry->status !== 'reviewed')
                                                <form action="{{ route('pdp.progress.review', $entry->pdp_progress_id) }}" method="POST">
                                                    @csrf
                                                    <input type="text" name="supervisor_comments" class="form-control input-sm" placeholder="Supervisor comments">
                                                    <button type="submit" class="btn btn-primary btn-xs mt-1">Review</button>
                                                </form>
                                                @else
                                                    {{ $entry->supervisor_comments ?? 'Reviewed' }}
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center">No progress entries recorded yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <a href="{{ route('pdp.plan.show', $plan->pdp_plan_id) }}" class="btn btn-default">Back to Plan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
