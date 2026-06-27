@extends('admin.master')
@section('content')
@section('title')
Personal Development Plans
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
            @can('pdp.plan.create')
            <a href="{{ route('pdp.plan.create') }}" class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> Create Plan
            </a>
            @endcan
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @include('admin.partials.alert')

                        <form method="GET" class="mb-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <input type="number" name="plan_year" class="form-control" placeholder="Year" value="{{ request('plan_year') }}">
                                </div>
                                <div class="col-md-3">
                                    <select name="department_id" class="form-control">
                                        <option value="">All Departments</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->department_id }}" {{ request('department_id') == $dept->department_id ? 'selected' : '' }}>{{ $dept->department_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-control">
                                        <option value="">All Statuses</option>
                                        @foreach(['draft','active','completed','cancelled'] as $status)
                                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-info btn-block">Filter</button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        <th>Employee</th>
                                        <th>Plan Title</th>
                                        <th>Year</th>
                                        <th>Review Frequency</th>
                                        <th>Goals</th>
                                        <th>Progress</th>
                                        <th>Status</th>
                                        <th style="text-align: center;">@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $sl = 0; @endphp
                                    @foreach($results as $value)
                                        <tr>
                                            <td>{{ ++$sl }}</td>
                                            <td>{{ $value->employee ? $value->employee->full_name : '' }}</td>
                                            <td>{{ $value->plan_title }}</td>
                                            <td>{{ $value->plan_year }}</td>
                                            <td>{{ ucfirst(str_replace('_', '-', $value->review_frequency)) }}</td>
                                            <td>{{ $value->goals->count() }}</td>
                                            <td>{{ $value->averageProgress() }}%</td>
                                            <td><span class="label label-{{ $value->status == 'active' ? 'info' : ($value->status == 'completed' ? 'success' : 'default') }}">{{ ucfirst($value->status) }}</span></td>
                                            <td style="width: 180px;">
                                                <a href="{{ route('pdp.plan.show', $value->pdp_plan_id) }}" class="btn btn-primary btn-xs btnColor"><i class="fa fa-eye"></i></a>
                                                @if($value->canBeEdited())
                                                    <a href="{{ route('pdp.plan.edit', $value->pdp_plan_id) }}" class="btn btn-success btn-xs btnColor"><i class="fa fa-pencil-square-o"></i></a>
                                                @endif
                                                <a href="{{ route('pdp.plan.delete', $value->pdp_plan_id) }}" data-token="{{ csrf_token() }}" data-id="{{ $value->pdp_plan_id }}" class="delete btn btn-danger btn-xs deleteBtn btnColor"><i class="fa fa-trash-o"></i></a>
                                            </td>
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
