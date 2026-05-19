@extends('admin.master')
@section('content')
@section('title')
Summary Report
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
                        <form action="{{ route('performance.report.summary') }}" method="GET">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Review Period</label>
                                        <input type="text" name="review_period" class="form-control" placeholder="e.g. Jan - June 2026">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-info btn-block">Generate Report</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        @if(count($departmentScores) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="tr_header">
                                            <th>Department</th>
                                            <th>Average Score</th>
                                            <th>Appraisal Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($departmentScores as $deptScore)
                                            <tr>
                                                <td>{{ $deptScore['department']->department_name }}</td>
                                                <td>{{ $deptScore['average_score'] }}</td>
                                                <td>{{ $deptScore['appraisal_count'] }}</td>
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
