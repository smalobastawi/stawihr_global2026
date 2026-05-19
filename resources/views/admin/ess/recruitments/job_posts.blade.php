@extends('admin.master')

@section('title', 'Available Job Posts')

@section('content')


    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor">
                        <a href="{{ url('dashboard') }}">
                            <i class="fa fa-home"></i> @lang('dashboard.dashboard')
                        </a>
                    </li>
                    <li>@yield('title')</li>
                </ol>
            </div>
        </div>
        <!--/.row bg-title -->

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-table fa-fw"></i> @yield('title')
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @if (session()->has('success'))
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;
                                    <strong>
                                        {{ session()->get('success') }}
                                    </strong>
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
                                            <th>@lang('recruitement.job_title')</th>
                                            <th>@lang('recruitement.job_type')</th>
                                            <th>@lang('recruitement.job_location')</th>
                                            {{-- <th>@lang('recruitement.publish_by')</th> --}}
                                            <th>@lang('recruitement.application_date')</th>
                                            <th>@lang('common.status')</th>
                                            <th>@lang('common.action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {!! $sl = null !!}
                                        @foreach ($results as $value)
                                            <tr class="{!! $value->job_id !!}">
                                                <td style="width: 70px;">{!! ++$sl !!}</td>
                                                <td>
                                                    {!! $value->job_title !!}
                                                </td>
                                                <td>
                                                    {{ JobTypes::getName($value->job_type) }}
                                                </td>
                                                <td>
                                                    @isset($value->location)
                                                        {!! $value->location->location_name !!}
                                                    @endisset
                                                </td>
                                                <td>
                                                    From: {{ date('d M Y', strtotime($value->application_date)) }}
                                                    To: {{ date('d M Y', strtotime($value->application_end_date)) }}
                                                    <br />
                                                    <span class="text-muted">
                                                        Days To Expire:
                                                        {{ \Carbon\Carbon::parse($value->application_end_date)->diffInDays(\Carbon\Carbon::now()) }}
                                                        days
                                                    </span>
                                                </td>

                                                <td>
                                                    <span
                                                        class="label label-{{ $value->status == '1' ? 'success' : 'warning' }}">
                                                        {{ $value->status == '1' ? __('recruitement.published') : __('recruitement.unpublished') }}
                                                    </span>
                                                </td>

                                                <td style="width: 30px;">
                                                    <a title="View Job Details"
                                                        href="{{ route('ess.recruitment.job.details', $value->job_id) }}"
                                                        class="btn btn-primary btnColor">
                                                        <i class="glyphicon glyphicon-th-large" aria-hidden="true"></i>
                                                        Apply For This Job
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
                <!--/.panel panel-info -->
            </div>
            <!--/.col -->
        </div>
        <!--/.row -->

    </div>
    <!--/.container-fluid -->

@endsection
