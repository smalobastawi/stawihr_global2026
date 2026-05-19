@extends('admin.master')
@section('content')
@section('title')
    Disciplinary Actions
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <a href="{{ route('disciplinary.cases.create') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i
                    class="fa fa-plus-circle" aria-hidden="true"></i> Add New Case</a>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i
                                    class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i
                                    class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        <th>Case No</th>
                                        <th>Category</th>
                                        <th>Location</th>
                                        <th>Employee </th>
                                        <th>Action Type</th>
                                        <th>Actioned By</th>
                                        <th>Action Date</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl = null !!}
                                    @foreach ($data as $value)
                                        <tr class="{!! $value->id !!}">
                                            <td style="width: 100px;">{!! ++$sl !!}</td>
                                            <td>{!! $value->case->case_number !!}</td>
                                            <td>{!! $value->case->category->name !!}</td>
                                            <td>
                                                @if ($value->case->location)
                                                    {!! $value->case->location->location_name !!}
                                                @endif
                                            </td>


                                            <td>{!! $value->case->employee->first_name . ' ' . $value->case->employee->last_name !!}</td>
                                            <td>{!! $value->actionedBy->first_name . ' ' . $value->actionedBy->last_name !!}</td>
                                            <td>{!! DisciplinaryActionTypes::getName($value->action_type) !!}</td>
                                            <td>{!! $value->action_date !!}</td>

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
