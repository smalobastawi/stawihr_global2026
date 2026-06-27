@extends('admin.master')
@section('content')
@section('title')
    My Development Plans
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ess.leave.index') }}">Self Service</a></li>
                <li class="breadcrumb-item active">My Development Plans</li>
            </ol>
        </div>
        @if($setting->allow_employee_self_service)
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <a href="{{ route('ess.pdp.create') }}" class="btn btn-success pull-right m-l-20">
                <i class="fa fa-plus-circle"></i> Create Plan
            </a>
        </div>
        @endif
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @include('admin.partials.alert')

                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
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
                                            <td>{{ $value->plan_title }}</td>
                                            <td>{{ $value->plan_year }}</td>
                                            <td>{{ ucfirst(str_replace('_', '-', $value->review_frequency)) }}</td>
                                            <td>{{ $value->goals->count() }}</td>
                                            <td>{{ $value->averageProgress() }}%</td>
                                            <td>{{ ucfirst($value->status) }}</td>
                                            <td style="text-align: center;">
                                                <a href="{{ route('ess.pdp.show', $value->pdp_plan_id) }}" class="btn btn-primary btn-xs btnColor"><i class="fa fa-eye"></i></a>
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
