@extends('admin.master')
@section('content')
@section('title')
    @lang('recruitement.job_candidate_list')
@endsection
<style>
    .applicatioFontStyle {
        padding: 8px;
        border-radius: 50%;
        background: #757575;
        color: #fff;
    }
</style>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-7 col-md-7 col-sm-5 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
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
                            <table id="myTable1" class="table table-hover manage-u-table">
                                <thead>
                                    <tr>
                                        <th>@lang('common.serial')</th>
                                        <th>@lang('recruitement.job_title')</th>
                                        <th>@lang('recruitement.job_application')</th>
                                        <th>@lang('recruitement.short_listed_application')</th>
                                        <th>@lang('recruitement.reject_application')</th>
                                        <th>@lang('recruitement.job_interview')</th>
                                        <th>@lang('recruitement.job_hires')</th>
                                        {{-- <th>@lang('common.action')</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl = null !!}
                                    @if (count($results) > 0)
                                        @foreach ($results as $value)
                                            <tr class="{!! $value->job_id !!}">
                                                <td style="width: 70px;">{!! ++$sl !!}</td>
                                                <td>
                                                    {!! $value->job_title !!}
                                                    <br>
                                                    <span class="text-muted">Location: {!! $value->job_location !!}</span>
                                                </td>
                                                <td style="font-size: 16px; font-weight: 700; ">
                                                    <a href="{{ route('jobCandidate.applyCandidateList', $value->job_id) }}" 
                                                       class="badge bg-primary text-white" 
                                                       data-bs-toggle="tooltip" 
                                                       title="View all applicants for this job">
                                                        <i class="icon-briefcase me-1"></i>
                                                        {{ $value->totalApplication }} Applicants
                                                    </a>
                                                </td>
                                                <td style="font-size: 16px; font-weight: 700; color: #fff;">
                                                    <a href="{{ route('jobCandidate.shortListedApplicant', $value->job_id) }}" 
                                                       class="badge bg-warning text-white" 
                                                       data-bs-toggle="tooltip" 
                                                       title="View all applicants shortlisted for this job">
                                                        <i class="fa fa-star-o me-1"></i>
                                                        {{ $value->shortList }} Applicants
                                                    </a>
                                                </td>
                                                <td style="font-size: 16px; font-weight: 700;">
                                                    <a href="{{ route('jobCandidate.rejectedApplicant', $value->job_id) }}" 
                                                       class="badge bg-white text-white" 
                                                       title="View all rejected applicants for this job">
                                                        <i class="fa fa-eraser me-1 text-white"></i>
                                                        {{ $value->reject }} Applicants
                                                    </a>
                                                </td>
                                                <td style="font-size: 16px; font-weight: 700;">
                                                    <a href="{{ route('jobCandidate.jobInterviewList', $value->job_id) }}" 
                                                       class="badge bg-white text-white" 
                                                       title="View all interviewed applicants for this job">
                                                        <i class="fa fa-user me-1"></i>
                                                        {{ $value->interview }} Applicants
                                                    </a>
                                                </td>
                                                <td style="font-size: 16px; font-weight: 700;">
                                                    <a href="{{ route('jobCandidate.jobHireList', ['id' => $value->job_id]) }}" 
                                                       class="badge bg-success text-white" 
                                                       title="View all hired applicants for this job">
                                                        <i class="me-1 fa fa-user"></i>
                                                        {{ $value->hire }} Applicants
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7">@lang('common.no_data_available') !</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <div class="text-center">
                                {{ $results->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
