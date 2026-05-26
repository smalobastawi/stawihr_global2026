@extends('admin.master')

@section('title', $notice->title)

@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor">
                        <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                    </li>
                    <li>
                        <a href="{{ route('ess.notices.index') }}">Notices & Announcements</a>
                    </li>
                    <li>@yield('title')</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-bullhorn fa-fw"></i> Notice Details
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <h3>{{ $notice->title }}</h3>
                            <p class="text-muted">
                                <i class="fa fa-calendar"></i>
                                Published: {{ dateConvertDBtoForm($notice->publish_date) }}
                            </p>

                            @if ($notice->targeted_audience_summary)
                                <p><strong>Audience:</strong> {{ $notice->targeted_audience_summary }}</p>
                            @endif

                            <hr>

                            <div class="notice-content">
                                {!! nl2br(e($notice->description)) !!}
                            </div>

                            @if ($notice->attach_file)
                                <hr>
                                <a href="{{ asset('uploads/notice/' . $notice->attach_file) }}"
                                    class="btn btn-primary" target="_blank" rel="noopener">
                                    <i class="fa fa-paperclip"></i> View Attachment
                                </a>
                            @endif

                            <div class="m-t-20">
                                <a href="{{ route('ess.notices.index') }}" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Back to Notices
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
