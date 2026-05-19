@extends('admin.master')

@section('title', trans('recruitement.job_post_list'))

@section('content')

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
                <a href="{{ route('jobPost.create') }}"
                    class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i
                        class="fa fa-plus-circle" aria-hidden="true"></i> @lang('recruitement.create_new_job_post')</a>
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
                                <table id="myTable" class="table table-hover manage-u-table">
                                    <thead>
                                        <tr>
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
                                                {{-- <td>
													@if (isset($value->createdBy->first_name)) 
														{{$value->createdBy->first_name}} {{$value->createdBy->last_name}}
													@endif
													<br/>
													<span class="text-muted">
														Published Date: {{date("d M Y", strtotime($value->created_at))}} 
													</span>
												</td> --}}
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


                                                <td style="width: 100px;">
                                                    <a title="View Job Details"
                                                        href="{{ route('jobPost.show', $value->job_id) }}"
                                                        class="btn btn-primary btn-xs btnColor">
                                                        <i class="glyphicon glyphicon-th-large" aria-hidden="true"></i>
                                                    </a>
                                                    <a href="{!! route('jobPost.edit', $value->job_id) !!}"
                                                        class="btn btn-success btn-xs btnColor">
                                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                    </a>
                                                    <a href="{!! route('jobPost.delete', $value->job_id) !!}" data-token="{!! csrf_token() !!}"
                                                        data-id="{!! $value->job_id !!}"
                                                        class="delete btn btn-danger btn-xs deleteBtn btnColor"><i
                                                            class="fa fa-trash-o" aria-hidden="true"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{-- <div class="text-center">
									{{$results->links()}}
								</div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
