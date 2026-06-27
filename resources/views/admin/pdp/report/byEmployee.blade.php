@extends('admin.master')
@section('content')
@section('title')
PDP Report by Employee
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
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <form method="GET" class="mb-3">
                            <div class="row">
                                <div class="col-md-2">
                                    <input type="number" name="plan_year" class="form-control" value="{{ $filters['plan_year'] }}" placeholder="Year">
                                </div>
                                <div class="col-md-3">
                                    <select name="department_id" class="form-control">
                                        <option value="">All Departments</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->department_id }}" {{ $filters['department_id'] == $dept->department_id ? 'selected' : '' }}>{{ $dept->department_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="employee_id" class="form-control">
                                        <option value="">All Employees / Select One</option>
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->employee_id }}" {{ $filters['employee_id'] == $emp->employee_id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="review_quarter" class="form-control">
                                        <option value="">All Quarters</option>
                                        @for($q = 1; $q <= 4; $q++)
                                            <option value="{{ $q }}" {{ $filters['review_quarter'] == $q ? 'selected' : '' }}>Q{{ $q }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-info btn-block">Generate</button>
                                </div>
                            </div>
                        </form>

                        @if($selectedPlan)
                            <h4>{{ $selectedPlan->employee->full_name }} - {{ $selectedPlan->plan_title }} ({{ $selectedPlan->plan_year }})</h4>
                            <p><strong>Status:</strong> {{ ucfirst($selectedPlan->status) }} | <strong>Progress:</strong> {{ $selectedPlan->averageProgress() }}%</p>
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="tr_header">
                                            <th>Period</th>
                                            <th>Goal</th>
                                            <th>Progress</th>
                                            <th>Summary</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($progressEntries as $entry)
                                            <tr>
                                                <td>{{ $entry->review_period_label }}</td>
                                                <td>{{ $entry->goal ? $entry->goal->goal_title : 'N/A' }}</td>
                                                <td>{{ $entry->progress_percentage }}%</td>
                                                <td>{{ $entry->achievement_summary }}</td>
                                                <td>{{ ucfirst($entry->status) }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="text-center">No progress entries for selected filters.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @elseif(count($results) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="tr_header">
                                            <th>Employee</th>
                                            <th>Plan</th>
                                            <th>Goals</th>
                                            <th>Progress</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($results as $result)
                                            <tr>
                                                <td>{{ $result['employee']->full_name }}</td>
                                                <td>{{ $result['plan']->plan_title }}</td>
                                                <td>{{ $result['goal_count'] }}</td>
                                                <td>{{ $result['average_progress'] }}%</td>
                                                <td>{{ ucfirst($result['status']) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
