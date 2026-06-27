@extends('admin.master')
@section('content')
@section('title')
PDP Progress Summary Report
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
                                <div class="col-md-2">
                                    <select name="review_quarter" class="form-control">
                                        <option value="">All Quarters</option>
                                        @for($q = 1; $q <= 4; $q++)
                                            <option value="{{ $q }}" {{ $filters['review_quarter'] == $q ? 'selected' : '' }}>Q{{ $q }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="review_half" class="form-control">
                                        <option value="">All Halves</option>
                                        <option value="1" {{ $filters['review_half'] == 1 ? 'selected' : '' }}>H1</option>
                                        <option value="2" {{ $filters['review_half'] == 2 ? 'selected' : '' }}>H2</option>
                                    </select>
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
                                    <button type="submit" class="btn btn-info btn-block">Generate Report</button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>Period</th>
                                        <th>Employee</th>
                                        <th>Department</th>
                                        <th>Goal</th>
                                        <th>Progress</th>
                                        <th>Summary</th>
                                        <th>Status</th>
                                        <th>Submitted</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($entries as $entry)
                                        <tr>
                                            <td>{{ $entry->review_period_label }}</td>
                                            <td>{{ $entry->plan && $entry->plan->employee ? $entry->plan->employee->full_name : 'N/A' }}</td>
                                            <td>{{ $entry->plan && $entry->plan->department ? $entry->plan->department->department_name : 'N/A' }}</td>
                                            <td>{{ $entry->goal ? $entry->goal->goal_title : 'N/A' }}</td>
                                            <td>{{ $entry->progress_percentage }}%</td>
                                            <td>{{ \Illuminate\Support\Str::limit($entry->achievement_summary, 100) }}</td>
                                            <td>{{ ucfirst($entry->status) }}</td>
                                            <td>{{ $entry->submitted_at ? $entry->submitted_at->format('Y-m-d') : 'N/A' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="8" class="text-center">No progress entries found for the selected filters.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
