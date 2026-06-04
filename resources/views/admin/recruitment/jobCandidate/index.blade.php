@extends('admin.master')
@section('content')
@section('title')
    @lang('recruitement.job_candidate_list')
@endsection

<style>
    .pipeline-tabs > li > a {
        font-weight: 600;
    }

    .job-filter-bar {
        background: #f8f9fa;
        border: 1px solid #e4e7ea;
        border-radius: 4px;
        padding: 15px;
        margin-bottom: 15px;
    }
</style>

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-7 col-md-7 col-sm-5 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="mdi mdi-account-multiple fa-fw"></i> @yield('title')
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif

                        <ul class="nav nav-tabs" role="tablist">
                            <li class="{{ $view === 'pipeline' ? 'active' : '' }}">
                                <a href="{{ route('jobCandidate.index', ['view' => 'pipeline', 'job_id' => $jobId, 'stage' => $stage]) }}">
                                    <i class="fa fa-users"></i> Hiring pipeline
                                </a>
                            </li>
                            <li class="{{ $view === 'jobs' ? 'active' : '' }}">
                                <a href="{{ route('jobCandidate.index', ['view' => 'jobs']) }}">
                                    <i class="fa fa-briefcase"></i> Jobs overview
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content m-t-15">
                            @if ($view === 'pipeline')
                                @if ($jobs->isEmpty())
                                    <div class="alert alert-info m-t-15">
                                        No job posts found. Create a job post first to manage candidates.
                                    </div>
                                @else
                                    <div class="job-filter-bar m-t-15">
                                        <form method="GET" action="{{ route('jobCandidate.index') }}" class="form-inline">
                                            <input type="hidden" name="view" value="pipeline">
                                            <input type="hidden" name="stage" value="{{ $stage }}">
                                            <input type="hidden" name="sort" value="{{ $sort }}">
                                            <input type="hidden" name="direction" value="{{ $direction }}">
                                            <div class="form-group">
                                                <label for="job_id" class="m-r-10"><strong>Job position</strong></label>
                                                <select name="job_id" id="job_id" class="form-control select2" style="min-width: 320px;">
                                                    @foreach ($jobs as $job)
                                                        <option value="{{ $job->job_id }}"
                                                            {{ (int) $jobId === (int) $job->job_id ? 'selected' : '' }}>
                                                            {{ $job->job_title }}
                                                            @if ($job->location?->location_name)
                                                                — {{ $job->location->location_name }}
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-success m-l-10">
                                                <i class="fa fa-filter"></i> Load
                                            </button>
                                        </form>
                                    </div>

                                    @if ($selectedJob)
                                        <p class="text-muted m-b-15">
                                            Managing candidates for: <strong>{{ $selectedJob->job_title }}</strong>
                                        </p>

                                        <ul class="nav nav-tabs pipeline-tabs">
                                            @php
                                                $stages = [
                                                    'applications' => ['label' => 'All applications', 'count' => $stageCounts['applications'] ?? 0],
                                                    'shortlisted' => ['label' => 'Shortlisted', 'count' => $stageCounts['shortlisted'] ?? 0],
                                                    'interview' => ['label' => 'Interview', 'count' => $stageCounts['interview'] ?? 0],
                                                    'rejected' => ['label' => 'Rejected', 'count' => $stageCounts['rejected'] ?? 0],
                                                    'hired' => ['label' => 'Hired', 'count' => $stageCounts['hired'] ?? 0],
                                                ];
                                            @endphp
                                            @foreach ($stages as $stageKey => $stageMeta)
                                                <li class="{{ $stage === $stageKey ? 'active' : '' }}">
                                                    <a href="{{ route('jobCandidate.index', [
                                                        'view' => 'pipeline',
                                                        'job_id' => $jobId,
                                                        'stage' => $stageKey,
                                                        'sort' => $sort,
                                                        'direction' => $direction,
                                                    ]) }}">
                                                        {{ $stageMeta['label'] }}
                                                        <span class="badge">{{ $stageMeta['count'] }}</span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>

                                        <div class="m-t-15">
                                            @include('admin.recruitment.jobCandidate.partials.applicants-table', [
                                                'applicants' => $applicants,
                                                'sort' => $sort,
                                                'direction' => $direction,
                                                'view' => $view,
                                                'stage' => $stage,
                                                'jobId' => $jobId,
                                            ])
                                        </div>
                                    @endif
                                @endif
                            @else
                                <div class="table-responsive m-t-15">
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
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $sl = 0; @endphp
                                            @forelse($jobSummaries ?? [] as $value)
                                                <tr>
                                                    <td style="width: 70px;">{{ ++$sl }}</td>
                                                    <td>
                                                        <strong>{{ $value->job_title }}</strong><br>
                                                        <span class="text-muted">Location: {{ $value->location?->location_name ?? '—' }}</span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('jobCandidate.index', ['view' => 'pipeline', 'job_id' => $value->job_id, 'stage' => 'applications']) }}"
                                                            class="badge bg-primary text-white">
                                                            {{ $value->totalApplication }} Applicants
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('jobCandidate.index', ['view' => 'pipeline', 'job_id' => $value->job_id, 'stage' => 'shortlisted']) }}"
                                                            class="badge bg-warning text-white">
                                                            {{ $value->shortList }} Applicants
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('jobCandidate.index', ['view' => 'pipeline', 'job_id' => $value->job_id, 'stage' => 'rejected']) }}"
                                                            class="badge bg-danger text-white">
                                                            {{ $value->reject }} Applicants
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('jobCandidate.index', ['view' => 'pipeline', 'job_id' => $value->job_id, 'stage' => 'interview']) }}"
                                                            class="badge bg-info text-white">
                                                            {{ $value->interview }} Applicants
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('jobCandidate.index', ['view' => 'pipeline', 'job_id' => $value->job_id, 'stage' => 'hired']) }}"
                                                            class="badge bg-success text-white">
                                                            {{ $value->hire }} Applicants
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">@lang('common.no_data_available')</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @if ($jobSummaries && $jobSummaries->hasPages())
                                    <div class="text-center">{{ $jobSummaries->links() }}</div>
                                @endif
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
        $('#job_id').select2({ width: '100%', placeholder: 'Select a job position' });

        var $table = $('#jobCandidateApplicantsTable');
        var hasDataRows = $table.length && $table.find('tbody tr').not(':has(td[colspan])').length > 0;
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
