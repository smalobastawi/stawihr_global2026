@extends('admin.master')
@section('content')
@section('title')
Supervisor Evaluations
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
            <a href="{{ route('performance.appraisal.index') }}" class="btn btn-info pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-arrow-left" aria-hidden="true"></i> Back to All Appraisals
            </a>
        </div>
    </div>

    <!-- Status Summary for Supervisor -->
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="white-box" style="background: #d9edf7; border-left: 4px solid #31708f;">
                <h3 class="box-title" style="color: #31708f;">Pending Review</h3>
                <ul class="list-inline two-part">
                    <li><i class="fa fa-clock-o text-info"></i></li>
                    <li class="text-right"><span class="counter" style="font-size: 24px; font-weight: bold; color: #31708f;">{{ $results->where('status', 'self_review')->count() }}</span></li>
                </ul>
                <p class="text-muted small">Appraisals awaiting your review</p>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="white-box" style="background: #fcf8e3; border-left: 4px solid #8a6d3b;">
                <h3 class="box-title" style="color: #8a6d3b;">In Progress</h3>
                <ul class="list-inline two-part">
                    <li><i class="fa fa-user-secret text-warning"></i></li>
                    <li class="text-right"><span class="counter" style="font-size: 24px; font-weight: bold; color: #8a6d3b;">{{ $results->where('status', 'supervisor_review')->count() }}</span></li>
                </ul>
                <p class="text-muted small">Reviews you've started</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-warning">
                <div class="panel-heading"><i class="fa fa-user-secret fa-fw"></i> @yield('title') - Appraisals Requiring Your Review</div>
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
                        @if(session()->has('warning'))
                            <div class="alert alert-warning alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                                <i class="glyphicon glyphicon-exclamation-sign"></i>&nbsp;<strong>{{ session()->get('warning') }}</strong>
                            </div>
                        @endif

                        @if($results->count() > 0)
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> <strong>Action Required:</strong> You have {{ $results->count() }} appraisal(s) awaiting your review. Click the "Review" button to complete the supervisor evaluation.
                            </div>
                        @else
                            <div class="alert alert-success">
                                <i class="fa fa-check-circle"></i> <strong>All Caught Up!</strong> No appraisals are currently awaiting your review.
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        <th>Employee</th>
                                        <th>Period</th>
                                        <th>Status</th>
                                        <th>Self Score</th>
                                        <th>Created Date</th>
                                        <th style="text-align: center;">@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl=null !!}
                                    @foreach($results as $value)
                                        <tr class="{!! $value->appraisal_id !!}">
                                            <td style="width: 50px;">{!! ++$sl !!}</td>
                                            <td>
                                                <strong>{!! $value->employee ? $value->employee->full_name : 'N/A' !!}</strong>
                                                <br><small class="text-muted">ID: {!! $value->employee_id !!}</small>
                                            </td>
                                            <td>{!! $value->review_period !!}</td>
                                            <td>
                                                @if($value->status == 'self_review')
                                                    <span class="label label-info">Self Review Complete</span>
                                                    <br><small class="text-muted">Ready for your review</small>
                                                @elseif($value->status == 'supervisor_review')
                                                    <span class="label label-warning">In Progress</span>
                                                    <br><small class="text-muted">You started reviewing</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($value->total_self_weighting > 0)
                                                    <strong>{!! $value->total_self_weighting !!}</strong>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{!! $value->created_at ? $value->created_at->format('Y-m-d') : '-' !!}</td>
                                            <td style="width: 150px; text-align: center;">
                                                <a href="{!! route('performance.appraisal.show', $value->appraisal_id) !!}" class="btn btn-primary btn-xs btnColor" title="View Details">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                                </a>
                                                @if(in_array($value->status, ['self_review', 'supervisor_review']))
                                                    <a href="{!! route('performance.supervisor.review', $value->appraisal_id) !!}" class="btn btn-warning btn-sm btnColor" title="Complete Supervisor Review" style="margin-left: 5px;">
                                                        <i class="fa fa-edit" aria-hidden="true"></i> <strong>Review</strong>
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
