@extends('admin.master')
@section('content')
@section('title')
PDP Report by Department
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
                                <div class="col-md-3">
                                    <input type="number" name="plan_year" class="form-control" value="{{ $filters['plan_year'] }}" placeholder="Year">
                                </div>
                                <div class="col-md-3">
                                    <select name="review_quarter" class="form-control">
                                        <option value="">All Quarters</option>
                                        @for($q = 1; $q <= 4; $q++)
                                            <option value="{{ $q }}" {{ $filters['review_quarter'] == $q ? 'selected' : '' }}>Q{{ $q }}</option>
                                        @endfor
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
                                        <th>Department</th>
                                        <th>Plans</th>
                                        <th>Active</th>
                                        <th>Completed</th>
                                        <th>Avg Progress</th>
                                        <th>Progress Entries</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results as $result)
                                        <tr>
                                            <td>{{ $result['department']->department_name }}</td>
                                            <td>{{ $result['plan_count'] }}</td>
                                            <td>{{ $result['active_count'] }}</td>
                                            <td>{{ $result['completed_count'] }}</td>
                                            <td>{{ $result['average_progress'] }}%</td>
                                            <td>{{ $result['progress_entries'] }}</td>
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
</div>
@endsection
