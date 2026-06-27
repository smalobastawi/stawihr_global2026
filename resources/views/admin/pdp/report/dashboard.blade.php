@extends('admin.master')
@section('content')
@section('title')
PDP Dashboard
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
                <form method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="number" name="plan_year" class="form-control" value="{{ $filters['plan_year'] }}" placeholder="Year">
                        </div>
                        <div class="col-md-4">
                            <select name="department_id" class="form-control">
                                <option value="">All Departments</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->department_id }}" {{ $filters['department_id'] == $dept->department_id ? 'selected' : '' }}>{{ $dept->department_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-info btn-block">Apply Filters</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        @foreach([
            ['Total Plans', $stats['total_plans'], 'mdi-file-document', 'info'],
            ['Active Plans', $stats['active_plans'], 'mdi-run', 'success'],
            ['Completed Plans', $stats['completed_plans'], 'mdi-check-circle', 'primary'],
            ['Progress Entries', $stats['progress_entries'], 'mdi-chart-line', 'warning'],
        ] as $card)
            <div class="col-lg-3 col-md-6">
                <div class="white-box">
                    <h3 class="box-title">{{ $card[0] }}</h3>
                    <ul class="list-inline two-part">
                        <li><i class="mdi {{ $card[2] }} text-{{ $card[3] }}"></i></li>
                        <li class="text-right"><span class="counter">{{ $card[1] }}</span></li>
                    </ul>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row">
        @foreach([
            ['Draft Plans', $stats['draft_plans']],
            ['Acknowledged', $stats['acknowledged']],
            ['Supervisor Approved', $stats['supervisor_approved']],
        ] as $card)
            <div class="col-lg-4 col-md-6">
                <div class="white-box">
                    <h3 class="box-title">{{ $card[0] }}</h3>
                    <ul class="list-inline two-part">
                        <li><i class="mdi mdi-account-check text-info"></i></li>
                        <li class="text-right"><span class="counter">{{ $card[1] }}</span></li>
                    </ul>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
