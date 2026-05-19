@extends('admin.master')
@section('content')
@section('title')
Review Periods
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
            <a href="{{ route('performance.reviewPeriod.create') }}" class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> Add Review Period
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if(session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                                <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if(session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                                <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif

                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> <strong>About Review Periods:</strong> These periods define the time ranges for performance appraisals. When creating an appraisal, HR will select from these predefined periods, and the dates will be automatically populated.
                        </div>

                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        <th>Period Name</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                        <th>Sort Order</th>
                                        <th>Description</th>
                                        <th style="text-align: center;">@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl=null !!}
                                    @foreach($results as $value)
                                        <tr class="{!! $value->period_id !!}">
                                            <td style="width: 50px;">{!! ++$sl !!}</td>
                                            <td><strong>{!! $value->period_name !!}</strong></td>
                                            <td>{!! $value->start_date->format('Y-m-d') !!}</td>
                                            <td>{!! $value->end_date->format('Y-m-d') !!}</td>
                                            <td>
                                                @if($value->is_active)
                                                    <span class="label label-success">Active</span>
                                                @else
                                                    <span class="label label-default">Inactive</span>
                                                @endif
                                            </td>
                                            <td>{!! $value->sort_order !!}</td>
                                            <td>{!! $value->description ?? '-' !!}</td>
                                            <td style="width: 100px;">
                                                <a href="{!! route('performance.reviewPeriod.edit', $value->period_id) !!}" class="btn btn-success btn-xs btnColor">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                </a>
                                                <a href="{!! route('performance.reviewPeriod.delete', $value->period_id) !!}" data-token="{!! csrf_token() !!}" data-id="{!! $value->period_id !!}" class="delete btn btn-danger btn-xs deleteBtn btnColor">
                                                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                </a>
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
