@extends('admin.master')
@section('content')
@section('title')
Employee Report
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
                        <form action="{{ route('performance.report.employee') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Employee</label>
                                        <select name="employee_id" class="form-control">
                                            <option value="">Select Employee</option>
                                            @foreach($employees as $emp)
                                                <option value="{{ $emp->employee_id }}">{{ $emp->full_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
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

                        @if($appraisal)
                            <h3>{{ $appraisal->employee->full_name }} - {{ $appraisal->review_period }}</h3>
                            <p><strong>Total Review Score:</strong> {{ $appraisal->total_review_weighting }}</p>
                            <p><strong>Total Self Score:</strong> {{ $appraisal->total_self_weighting }}</p>

                            @foreach($focusAreaScores as $faScore)
                                <h4>{{ $faScore['focusArea'] ? $faScore['focusArea']->focus_area_name : 'Unknown' }}</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr class="tr_header">
                                                <th>Metric</th>
                                                <th>Self</th>
                                                <th>Review</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($faScore['scores'] as $score)
                                                <tr>
                                                    <td>{{ $score->goal ? $score->goal->performance_metric : '' }}</td>
                                                    <td>{{ $score->self_weighting }}</td>
                                                    <td>{{ $score->review_weighting }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
