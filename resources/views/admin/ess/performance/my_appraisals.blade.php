@extends('admin.master')
@section('content')
@section('title')
    My Performance Appraisals
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ess.leave.index') }}">Self Service</a></li>
                <li class="breadcrumb-item active">My Performance</li>
            </ol>
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

                        <div class="row" style="margin-bottom: 20px;">
                            <div class="col-sm-12">
                                <a href="{{ route('ess.performance.selfEvaluation') }}" class="btn btn-info btn-lg">
                                    <i class="fa fa-clipboard-check" aria-hidden="true"></i> &nbsp;<strong>Go to Self-Evaluation Form</strong>
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        <th>Period</th>
                                        <th>Status</th>
                                        <th>Total Self</th>
                                        <th>Total Review</th>
                                        <th>Supervisor</th>
                                        <th style="text-align: center;">@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl=null !!}
                                    @foreach($results as $value)
                                        <tr class="{!! $value->appraisal_id !!}">
                                            <td style="width: 50px;">{!! ++$sl !!}</td>
                                            <td>{!! $value->review_period !!}</td>
                                            <td>
                                                @if($value->status == 'draft')
                                                    <span class="label label-default">Draft</span>
                                                @elseif($value->status == 'self_review')
                                                    <span class="label label-info">Self Review Pending</span>
                                                @elseif($value->status == 'supervisor_review')
                                                    <span class="label label-warning">Supervisor Review</span>
                                                @elseif($value->status == 'hod_review')
                                                    <span class="label label-primary">HOD Review</span>
                                                @elseif($value->status == 'finalized')
                                                    <span class="label label-success">Finalized</span>
                                                @else
                                                    <span class="label label-primary">Closed</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($value->total_self_weighting > 0)
                                                    <strong>{!! $value->total_self_weighting !!}</strong>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($value->total_review_weighting > 0)
                                                    <strong>{!! $value->total_review_weighting !!}</strong>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                {!! $value->supervisor ? $value->supervisor->full_name : '-' !!}
                                                @if($value->review_start_date && $value->review_end_date)
                                                    <br><small class="text-muted">
                                                        <i class="fa fa-calendar"></i> {{ $value->review_start_date->format('M d') }} - {{ $value->review_end_date->format('M d, Y') }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td style="width: 200px; text-align: center;">
                                                <a href="{!! route('ess.performance.show', $value->appraisal_id) !!}" class="btn btn-primary btn-xs btnColor" title="View Details">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                                </a>
                                                @if(in_array($value->status, ['draft', 'self_review']))
                                                    <a href="{!! route('ess.performance.selfReview', $value->appraisal_id) !!}" class="btn btn-warning btn-sm btnColor" title="Complete Self Review" style="margin-left: 5px;">
                                                        <i class="fa fa-edit" aria-hidden="true"></i> <strong>Self Review</strong>
                                                    </a>
                                                @endif
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
