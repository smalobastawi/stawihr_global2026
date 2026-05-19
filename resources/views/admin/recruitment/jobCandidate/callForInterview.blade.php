@extends('admin.master')
@section('content')
@section('title', 'Job Interview')
<style>
    .downloadResume {
        font-size: 15px;
        color: #777;
        font-weight: 500;
    }

    .post {
        font-weight: 500;
        font-size: 16px;
    }

    .applicationDate {
        font-size: 13px;
        color: #98a6ad;
    }

    .coverLater {
        margin-top: 5px;
    }

    .panel .panel-heading {
        border-radius: 0;
        font-weight: 500;
        font-size: 16px;
        padding: 18px 16px;
    }
</style>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}">
                        <i class="fa fa-home"></i> Dashboard
                    </a>
                </li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
            <a href="{{ route('jobCandidate.index') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-list-ul" aria-hidden="true"></i> Job Candidates List
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-offset-2 col-md-7">
            @if ($results)
                <p class="box-title post">Job Name :
                    @isset($results->job)
                        {{ $results->job->job_title }}
                    @endisset

                </p>
            @endif
            <br>
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                </div>
            @endif
        </div>
        @if ($results)
            <form method="POST">
							@csrf

            <div class="col-md-offset-2 col-md-7 ">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                        @foreach ($errors->all() as $error)
                            <strong>{!! $error !!}</strong><br>
                        @endforeach
                    </div>
                @endif
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
                        <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                    </div>
                @endif
                <div class="panel panel-default">
                    <div class="panel-heading">{{ $results->applicant_name }}</div>
                    <div class="col-md-4" style="margin-top: 16px;">
                        <label for="exampleInput">Interview Date<span class="validateRq">*</span></label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" name="interview_date" value="{{ Request::old('interview_date') }}" class="form-control required dateField" id="interview_date" 0="readonly" placeholder="Enter interview date">
                        </div>
                    </div>
                    <div class="col-md-4" style="margin-top: 16px;">
                        <label for="exampleInput">Interview Time<span class="validateRq">*</span></label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-clock-o"></i>
                            </div>
                            <div class="bootstrap-timepicker">
                                <input type="text" name="interview_time" value="{{ Request::old('interview_time') }}" class="form-control required timePicker" id="timePicker" 0="readonly" placeholder="Enter time">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" style="margin-top: 16px;">
                        <div class="form-group">
                            <label for="exampleInput">Interview Type<span class="validateRq">*</span></label>
                            <select name="interview_type" class="form-control interview_type select2 required">
@foreach(['Email' => 'Email'] as $__key => $__value)
<option value="{{ $__key }}" {{ (string)Request::old('interview_type') == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="exampleInput">Comment<span class="validateRq">*</span></label>
                            <textarea name="comment" class="form-control textarea_editor required" rows="8" id="comment" placeholder="Enter comment...">{{ Request::old('comment') }}</textarea>

                        </div>
                    </div>
                    <div class="panel-footer">
                        <input type="submit" class="btn btn-info" style="width: 200px" value="Job Interview">
                    </div>
                </div>
            </div>
    </div>
    </form>
@else
    <div class="col-md-offset-2 col-md-7 ">
        <div style="background: #fff;padding: 2px 11px;">
            <h4>Job application not found....</h4>
        </div>
    </div>
    @endif
</div>
</div>
@endsection
@section('page_scripts')
<link rel="stylesheet" href="{!! asset('admin_assets/plugins/bower_components/html5-editor/bootstrap-wysihtml5.css') !!}" />
<script src="{!! asset('admin_assets/js/cbpFWTabs.js') !!}"></script>
<script src="{!! asset('admin_assets/plugins/bower_components/html5-editor/wysihtml5-0.3.0.js') !!}"></script>
<script src="{!! asset('admin_assets/plugins/bower_components/html5-editor/bootstrap-wysihtml5.js') !!}"></script>
<script type="text/javascript">
    (function() {
        $('.textarea_editor').wysihtml5();
        [].slice.call(document.querySelectorAll('.sttabs')).forEach(function(el) {
            new CBPFWTabs(el);
        });
    })();

    $(document).on("focus", ".timePicker", function() {
        $(this).timepicker({
            showInputs: false,
            minuteStep: 1,
            defaultTime: '09:00 AM',
            disableFocus: false,

        });
    });
</script>
@endsection

