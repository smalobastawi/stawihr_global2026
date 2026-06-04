@php
    use App\Lib\Enumerations\JobStatus;

    $sortLink = function (string $column) use ($sort, $direction, $jobId) {
        $nextDirection = ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';

        return route('jobPost.show', [
            'jobPostID' => $jobId,
            'tab' => 'applicants',
            'sort' => $column,
            'direction' => $nextDirection,
        ]);
    };

    $sortIcon = function (string $column) use ($sort, $direction) {
        if ($sort !== $column) {
            return '<i class="fa fa-sort text-muted"></i>';
        }

        return $direction === 'asc'
            ? '<i class="fa fa-sort-asc"></i>'
            : '<i class="fa fa-sort-desc"></i>';
    };
@endphp

<div class="m-b-15">
    <a href="{{ route('jobCandidate.index', ['view' => 'pipeline', 'job_id' => $jobId, 'stage' => 'applications']) }}"
        class="btn btn-info btn-sm">
        <i class="fa fa-external-link"></i> Open full hiring pipeline
    </a>
</div>

<div class="table-responsive">
    <table id="jobPostApplicantsTable" class="table table-bordered table-striped table-hover">
        <thead class="tr_header">
            <tr>
                <th style="width: 50px;">@lang('common.serial')</th>
                <th><a href="{{ $sortLink('applicant_name') }}">Name {!! $sortIcon('applicant_name') !!}</a></th>
                <th>Email</th>
                <th>Phone</th>
                <th><a href="{{ $sortLink('highest_qualification') }}">Qualification {!! $sortIcon('highest_qualification') !!}</a></th>
                <th><a href="{{ $sortLink('years_of_experience') }}">Experience {!! $sortIcon('years_of_experience') !!}</a></th>
                <th><a href="{{ $sortLink('application_date') }}">Applied {!! $sortIcon('application_date') !!}</a></th>
                <th>Resume</th>
                <th><a href="{{ $sortLink('status') }}">Status {!! $sortIcon('status') !!}</a></th>
                <th>@lang('common.action')</th>
            </tr>
        </thead>
        <tbody>
            @forelse($applicants as $applicant)
                <tr>
                    <td>{{ ($applicants->currentPage() - 1) * $applicants->perPage() + $loop->iteration }}</td>
                    <td><strong>{{ $applicant->applicant_name }}</strong></td>
                    <td>{{ $applicant->applicant_email }}</td>
                    <td>{{ $applicant->phone }}</td>
                    <td>{{ $applicant->highest_qualification ?? '-' }}</td>
                    <td>{{ $applicant->years_of_experience ?? '-' }}</td>
                    <td>{{ $applicant->application_date ? date('d M Y', strtotime($applicant->application_date)) : '-' }}</td>
                    <td>
                        <a href="{{ route('view.CV', $applicant->job_applicant_id) }}" target="_blank">
                            <i class="fa fa-eye"></i> View
                        </a>
                    </td>
                    <td>
                        @if ($applicant->status == JobStatus::$SHORTLIST)
                            <span class="label label-info">Shortlisted</span>
                        @elseif($applicant->status == JobStatus::$REJECT)
                            <span class="label label-danger">Rejected</span>
                        @elseif($applicant->status == JobStatus::$CALL_FOR_INTERVIEW)
                            <span class="label label-success">Interview</span>
                        @elseif($applicant->status == JobStatus::$HIRE)
                            <span class="label label-primary">Hired</span>
                        @else
                            <span class="label label-warning">Under review</span>
                        @endif
                    </td>
                    <td class="text-nowrap">
                        @if ($applicant->status == JobStatus::$Apply)
                            <a href="{{ route('applicant.shortlist', $applicant->job_applicant_id) }}"
                                class="btn btn-success btn-xs"
                                onclick="return confirm('Shortlist this applicant?')">
                                <i class="fa fa-check"></i> Shortlist
                            </a>
                            <a href="{{ route('applicant.reject', $applicant->job_applicant_id) }}"
                                class="btn btn-danger btn-xs"
                                onclick="return confirm('Reject this applicant?')">
                                <i class="fa fa-eraser"></i> Reject
                            </a>
                        @elseif($applicant->status == JobStatus::$SHORTLIST)
                            <a href="{{ route('applicant.jobInterview', $applicant->job_applicant_id) }}"
                                class="btn btn-info btn-xs">
                                <i class="fa fa-calendar"></i> Interview
                            </a>
                            <a href="{{ route('applicant.reject', $applicant->job_applicant_id) }}"
                                class="btn btn-danger btn-xs"
                                onclick="return confirm('Reject this applicant?')">
                                <i class="fa fa-eraser"></i> Reject
                            </a>
                        @elseif($applicant->status == JobStatus::$CALL_FOR_INTERVIEW)
                            <a href="{{ route('applicant.hire', $applicant->job_applicant_id) }}"
                                class="btn btn-primary btn-xs"
                                onclick="return confirm('Mark this applicant as hired?')">
                                <i class="fa fa-user"></i> Hire
                            </a>
                            <a href="{{ route('applicant.reject', $applicant->job_applicant_id) }}"
                                class="btn btn-danger btn-xs"
                                onclick="return confirm('Reject this applicant?')">
                                <i class="fa fa-eraser"></i> Reject
                            </a>
                        @elseif($applicant->status == JobStatus::$HIRE)
                            <span class="text-muted">Completed</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">@lang('common.no_data_available')</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($applicants->hasPages())
    <div class="text-center">{{ $applicants->links() }}</div>
@endif
