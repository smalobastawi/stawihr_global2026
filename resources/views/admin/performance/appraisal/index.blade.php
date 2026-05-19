@extends('admin.master')
@section('content')
@section('title')
Performance Appraisals
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
            <a href="{{ route('performance.appraisal.create') }}" class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> Add Appraisal
            </a>
        </div>
    </div>

    <!-- Status Summary Cards -->
    <div class="row">
        <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
            <div class="white-box" style="background: #f0f0f0; border-left: 4px solid #777;">
                <h3 class="box-title" style="color: #777;">Draft</h3>
                <ul class="list-inline two-part">
                    <li><i class="fa fa-file-o text-muted"></i></li>
                    <li class="text-right"><span class="counter" style="font-size: 24px; font-weight: bold; color: #777;">{{ $results->where('status', 'draft')->count() }}</span></li>
                </ul>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
            <div class="white-box" style="background: #d9edf7; border-left: 4px solid #31708f;">
                <h3 class="box-title" style="color: #31708f;">Self Review</h3>
                <ul class="list-inline two-part">
                    <li><i class="fa fa-user text-info"></i></li>
                    <li class="text-right"><span class="counter" style="font-size: 24px; font-weight: bold; color: #31708f;">{{ $results->where('status', 'self_review')->count() }}</span></li>
                </ul>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
            <div class="white-box" style="background: #fcf8e3; border-left: 4px solid #8a6d3b;">
                <h3 class="box-title" style="color: #8a6d3b;">Supervisor Review</h3>
                <ul class="list-inline two-part">
                    <li><i class="fa fa-user-secret text-warning"></i></li>
                    <li class="text-right"><span class="counter" style="font-size: 24px; font-weight: bold; color: #8a6d3b;">{{ $results->where('status', 'supervisor_review')->count() }}</span></li>
                </ul>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
            <div class="white-box" style="background: #e8f4f8; border-left: 4px solid #1e88e5;">
                <h3 class="box-title" style="color: #1e88e5;">HOD Review</h3>
                <ul class="list-inline two-part">
                    <li><i class="fa fa-user-md text-primary"></i></li>
                    <li class="text-right"><span class="counter" style="font-size: 24px; font-weight: bold; color: #1e88e5;">{{ $results->where('status', 'hod_review')->count() }}</span></li>
                </ul>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
            <div class="white-box" style="background: #dff0d8; border-left: 4px solid #3c763d;">
                <h3 class="box-title" style="color: #3c763d;">Finalized</h3>
                <ul class="list-inline two-part">
                    <li><i class="fa fa-check-circle text-success"></i></li>
                    <li class="text-right"><span class="counter" style="font-size: 24px; font-weight: bold; color: #3c763d;">{{ $results->where('status', 'finalized')->count() }}</span></li>
                </ul>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
            <div class="white-box" style="background: #f9f9f9; border-left: 4px solid #333;">
                <h3 class="box-title">Total</h3>
                <ul class="list-inline two-part">
                    <li><i class="fa fa-files-o text-dark"></i></li>
                    <li class="text-right"><span class="counter" style="font-size: 24px; font-weight: bold;">{{ $results->count() }}</span></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Bulk Upload Section -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color: #f5f5f5;">
                    <i class="fa fa-upload"></i> <strong>Bulk Upload Appraisals (Stage 1 - Initial Setup)</strong>
                    <span class="pull-right">
                        <a href="#" data-toggle="collapse" data-target="#bulkUploadPanel" aria-expanded="true">
                            <i class="fa fa-chevron-down"></i>
                        </a>
                    </span>
                </div>
                <div id="bulkUploadPanel" class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5><i class="fa fa-info-circle text-info"></i> How to use bulk upload:</h5>
                                <ol>
                                    <li>Download the <strong>CSV Template</strong> using the button on the right</li>
                                    <li>Fill in employee IDs, supervisor IDs, review period, and dates</li>
                                    <li>Save as CSV file and upload using the form below</li>
                                    <li>The system will auto-populate goals and behavioral items based on department/designation</li>
                                    <li>Status will be set to <span class="label label-default">Draft</span> - employees can then complete self-evaluation</li>
                                </ol>
                                <form action="{{ route('performance.appraisal.bulkUpload') }}" method="POST" enctype="multipart/form-data" class="form-inline" style="margin-top: 15px;">
                                    @csrf
                                    <div class="form-group" style="margin-right: 10px;">
                                        <label for="csv_file" class="sr-only">Select CSV File</label>
                                        <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv,.txt" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-upload"></i> Upload & Create Appraisals
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-4" style="border-left: 1px solid #ddd;">
                                <h5>Download Template</h5>
                                <p class="text-muted small">Get the CSV template with employee reference data</p>
                                <a href="{{ route('performance.appraisal.template.download') }}" class="btn btn-info btn-block">
                                    <i class="fa fa-download"></i> Download CSV Template
                                </a>
                                <hr>
                                <h6>Template Includes:</h6>
                                <ul class="small text-muted">
                                    <li>Column headers and instructions</li>
                                    <li>Sample data row</li>
                                    <li>Employee reference list (ID, Name, Dept)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        <th>Employee</th>
                                        <th>Supervisor</th>
                                        <th>Period</th>
                                        <th>Status</th>
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
                                            <td>
                                                {!! $value->supervisor ? $value->supervisor->full_name : '<span class="text-muted">-</span>' !!}
                                            </td>
                                            <td>{!! $value->review_period !!}</td>
                                            <td>
                                                @if($value->status == 'draft')
                                                    <span class="label label-default">Draft</span>
                                                    <br><small class="text-muted">Awaiting self-evaluation</small>
                                                @elseif($value->status == 'self_review')
                                                    <span class="label label-info">Self Review</span>
                                                    <br><small class="text-muted">Employee completed</small>
                                                @elseif($value->status == 'supervisor_review')
                                                    <span class="label label-warning">Supervisor Review</span>
                                                    <br><small class="text-muted">Awaiting HOD</small>
                                                @elseif($value->status == 'hod_review')
                                                    <span class="label label-primary">HOD Review</span>
                                                    <br><small class="text-muted">Ready to finalize</small>
                                                @elseif($value->status == 'finalized')
                                                    <span class="label label-success">Finalized</span>
                                                    <br><small class="text-muted">Complete</small>
                                                @else
                                                    <span class="label label-primary">Closed</span>
                                                @endif
                                            </td>
                                            <td>{!! $value->created_at ? $value->created_at->format('Y-m-d') : '-' !!}</td>
                                            <td style="width: 220px;">
                                                <a href="{!! route('performance.appraisal.show', $value->appraisal_id) !!}" class="btn btn-primary btn-xs btnColor" title="View">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                                </a>
                                                {{-- Self Review - only available in draft or self_review status --}}
                                                @if(in_array($value->status, ['draft', 'self_review']))
                                                    <a href="{!! route('performance.appraisal.selfReview', $value->appraisal_id) !!}" class="btn btn-info btn-xs btnColor" title="Self Review">
                                                        <i class="fa fa-user" aria-hidden="true"></i>
                                                    </a>
                                                @endif
                                                {{-- Supervisor Review - uses separate supervisor route group --}}
                                                @if(in_array($value->status, ['self_review', 'supervisor_review']))
                                                    <a href="{!! route('performance.supervisor.review', $value->appraisal_id) !!}" class="btn btn-warning btn-xs btnColor" title="Supervisor Review">
                                                        <i class="fa fa-user-secret" aria-hidden="true"></i>
                                                    </a>
                                                @endif
                                                @if(in_array($value->status, ['supervisor_review', 'hod_review']))
                                                    <a href="{!! route('performance.appraisal.hodReview', $value->appraisal_id) !!}" class="btn btn-primary btn-xs btnColor" title="HOD Review">
                                                        <i class="fa fa-user-md" aria-hidden="true"></i>
                                                    </a>
                                                @endif
                                                @if($value->status == 'hod_review')
                                                    <form action="{{ route('performance.appraisal.finalize', $value->appraisal_id) }}" method="POST" style="display:inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-xs btnColor" title="Finalize"><i class="fa fa-check-circle" aria-hidden="true"></i></button>
                                                    </form>
                                                @endif
                                                <a href="{!! route('performance.appraisal.edit', $value->appraisal_id) !!}" class="btn btn-success btn-xs btnColor">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                </a>
                                                <a href="{!! route('performance.appraisal.delete', $value->appraisal_id) !!}" data-token="{!! csrf_token() !!}" data-id="{!! $value->appraisal_id !!}" class="delete btn btn-danger btn-xs deleteBtn btnColor">
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
