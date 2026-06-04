@extends('admin.master')

@section('title', trans('recruitement.description'))

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
            <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
                <a href="{{ route('jobPost.index') }}"
                    class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                        class="fa fa-list-ul" aria-hidden="true"></i> View Job Posts </a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-clipboard-text fa-fw"></i> {{ $result->job_title }}
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="{{ $tab === 'details' ? 'active' : '' }}">
                                    <a href="{{ route('jobPost.show', $result->job_id) }}?tab=details">
                                        <i class="fa fa-info-circle"></i> Job details
                                    </a>
                                </li>
                                <li class="{{ $tab === 'applicants' ? 'active' : '' }}">
                                    <a href="{{ route('jobPost.show', $result->job_id) }}?tab=applicants">
                                        <i class="fa fa-users"></i> Applications
                                        <span class="badge">{{ $applicantCount }}</span>
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content m-t-20">
                                @if ($tab === 'details')
                                    <div class="col-md-offset-1 col-md-11">
                                        <div class="white-box">
                                            <div class="comment-center p-t-10">
                                                <div class="comment-body">
                                                    <div class="user-img"> </div>
                                                    <div class="">
                                                        <p style="font-weight: 700"> {{ $result->job_title }}</p>
                                                        <span class="time">@lang('recruitement.job_title') : {{ $result->job_title }}</span><br>
                                                        <span class="time">@lang('recruitement.publish_by') :
                                                            @if (isset($employee))
                                                                {{ $employee->first_name }} {{ $employee->middle_name }}
                                                                {{ $employee->last_name }}
                                                            @else
                                                                @if (isset($result->createdBy->first_name))
                                                                    {{ $result->createdBy->first_name }}
                                                                    {{ $result->createdBy->last_name }}
                                                                @endif
                                                            @endif
                                                        </span><br>
                                                        <span class="time">@lang('recruitement.job_publish_date') :
                                                            {{ date(' d M Y', strtotime($result->publish_date)) }} </span><br>

                                                        <span class="time">@lang('recruitement.job_location') :
                                                            {{ $result->location?->location_name ?? '—' }}</span><br>
                                                        <p> Description:</p>
                                                        <hr>
                                                        <span class="mail-desc">
                                                            {!! $result->job_description !!}
                                                        </span>
                                                        <br>
                                                        <div class="test-center">
                                                            <p style="font-weight:400 ">Application Deadline :
                                                                {{ date(' d M Y ', strtotime($result->application_end_date)) }}</p>
                                                        </div>
                                                        <br />

                                                        @if($result->jd_file)
                                                            <div style="margin: 15px 0; text-align: center;">
                                                                <a href="{{ route('jobPost.downloadDescription', $result->job_id) }}"
                                                                    class="btn btn-primary"
                                                                    style="padding: 8px 20px; background-color: #3490dc; color: white; text-decoration: none; border-radius: 4px;">
                                                                    <i class="fa fa-download"></i> Download Job Description
                                                                </a>
                                                                <a href="{{ route('jobPost.viewDescription', $result->job_id) }}"
                                                                    target="_blank" class="btn btn-info"
                                                                    style="padding: 8px 20px; background-color: #17a2b8; color: white; text-decoration: none; border-radius: 4px;">
                                                                    <i class="fa fa-eye"></i> View Job Description
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <p style="font-size: 16px; color: #333; text-align: center; margin-bottom: 5px;">
                                                        Use the details below to preview the job post
                                                    </p>
                                                    <div style="border-top: 5px solid blue; margin: 20px 0;"></div>

                                                    <div style="text-align: center; margin-top: 20px;">
                                                        <a href="{{ route('job.details', ['id' => $result->job_id, 'slug' => $result->job_title]) }}"
                                                            target="_blank"
                                                            style="font-size: 16px; color: #007bff; text-decoration: none; margin-right: 20px; transition: color 0.3s;">
                                                            <i class="bi bi-globe" style="margin-right: 8px;"></i>
                                                            Public Job Link
                                                        </a>
                                                    </div>

                                                    <span class="mail-desc"
                                                        style="text-align: center; display: block; margin-bottom: 20px; margin-top: 20px;">
                                                        Public link: <a
                                                            href="{{ route('job.details', ['id' => $result->job_id, 'slug' => $result->job_title]) }}"
                                                            target="_blank"> {{ $result->job_title }}
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    @include('admin.recruitment.job.partials.applicants-tab', [
                                        'applicants' => $applicants,
                                        'sort' => $sort,
                                        'direction' => $direction,
                                        'jobId' => $jobId,
                                    ])
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
<script>
    $(document).ready(function() {
        var $table = $('#jobPostApplicantsTable');
        if (!$table.length) {
            return;
        }
        var hasDataRows = $table.find('tbody tr').not(':has(td[colspan])').length > 0;
        if (hasDataRows && !$.fn.DataTable.isDataTable($table)) {
            $table.DataTable({
                paging: false,
                searching: true,
                info: false,
                ordering: false
            });
        }
    });
</script>
@endsection
