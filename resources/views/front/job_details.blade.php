@extends('front.master')

@section('title', $job->job_title)

@php
    $front_setting = getFrontData();
    $jobSlug = str_replace(' ', '-', strtolower($job->job_title ?? $job->post ?? ''));
    $employmentLabels = [
        'full_time' => 'Full Time',
        'part_time' => 'Part Time',
        'contract' => 'Contract',
        'temporary' => 'Temporary',
        'internship' => 'Internship',
    ];
    $employmentLabel = $employmentLabels[$job->employment_type] ?? ($job->employment_type ? ucwords(str_replace('_', ' ', $job->employment_type)) : null);
    $jobTypeLabel = is_numeric($job->job_type)
        ? \App\Lib\Enumerations\JobTypes::getName((int) $job->job_type)
        : $job->job_type;
@endphp

@section('meta')
    <meta name="og:title" content="{{ $job->job_title }}" />
    <meta name="og:image" content="{{ asset('storage/uploads/front/' . $front_setting->logo) }}" />
    <meta name="og:url"
        content="{{ route('job.details', ['id' => $job->job_id, 'slug' => $jobSlug]) }}" />
    <meta name="og:description" content="{{ Str::limit(strip_tags($job->job_description ?? ''), 160) }}" />
    <meta name="description" content="{{ Str::limit(strip_tags($job->job_description ?? ''), 160) }}" />
@endsection

@push('styles')
<style>
    .job-page {
        --job-ink: #0f172a;
        --job-muted: #64748b;
        --job-line: #e2e8f0;
        --job-surface: #ffffff;
        --job-accent: #2F70FF;
        --job-accent-dark: #1e55d4;
        padding: 1.5rem 0 3rem;
    }

    .job-page .job-breadcrumb {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.8125rem;
        color: var(--job-muted);
        margin-bottom: 1.25rem;
        list-style: none;
        padding: 0;
    }

    .job-page .job-breadcrumb a {
        color: var(--job-muted);
        text-decoration: none;
    }

    .job-page .job-breadcrumb a:hover {
        color: var(--job-accent);
    }

    .job-page .job-breadcrumb .sep {
        opacity: 0.5;
    }

    .job-page .job-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 300px;
        gap: 1.5rem;
        align-items: start;
    }

    .job-page .job-panel {
        background: var(--job-surface);
        border: 1px solid var(--job-line);
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
    }

    .job-page .job-hero {
        padding: 1.5rem 1.75rem;
    }

    .job-page .job-hero h1 {
        font-size: clamp(1.5rem, 2.5vw, 1.875rem);
        font-weight: 750;
        letter-spacing: -0.02em;
        color: var(--job-ink);
        margin: 0 0 0.75rem;
        line-height: 1.25;
    }

    .job-page .job-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        margin-bottom: 0.9rem;
    }

    .job-page .job-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.28rem 0.65rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        background: #eff6ff;
        color: var(--job-accent-dark);
        border: 1px solid #dbeafe;
    }

    .job-page .job-tag.is-soft {
        background: #f8fafc;
        color: #475569;
        border-color: var(--job-line);
    }

    .job-page .job-meta-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.85rem 1.25rem;
        color: var(--job-muted);
        font-size: 0.875rem;
    }

    .job-page .job-meta-row span {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .job-page .job-meta-row i {
        color: var(--job-accent);
        font-size: 0.95rem;
    }

    .job-page .job-body {
        padding: 0 1.75rem 1.5rem;
    }

    .job-page .job-section + .job-section {
        margin-top: 1.35rem;
        padding-top: 1.35rem;
        border-top: 1px solid var(--job-line);
    }

    .job-page .job-section h2 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--job-ink);
        margin: 0 0 0.65rem;
        display: flex;
        align-items: center;
        gap: 0.45rem;
    }

    .job-page .job-section h2 i {
        color: var(--job-accent);
        font-size: 1.05rem;
    }

    .job-page .job-section-content {
        color: #334155;
        font-size: 0.9375rem;
        line-height: 1.65;
    }

    .job-page .job-section-content p {
        margin-bottom: 0.65rem;
    }

    .job-page .job-section-content p:last-child,
    .job-page .job-section-content ul:last-child {
        margin-bottom: 0;
    }

    .job-page .job-section-content ul,
    .job-page .job-section-content ol {
        padding-left: 1.15rem;
        margin-bottom: 0.65rem;
    }

    .job-page .job-section-content li + li {
        margin-top: 0.25rem;
    }

    .job-page .job-aside {
        position: sticky;
        top: calc(var(--front-header-h, 72px) + 1rem);
    }

    .job-page .job-aside-card {
        padding: 1.25rem;
    }

    .job-page .job-aside-card h3 {
        font-size: 0.95rem;
        font-weight: 700;
        margin: 0 0 0.85rem;
        color: var(--job-ink);
    }

    .job-page .job-fact-list {
        list-style: none;
        padding: 0;
        margin: 0 0 1.1rem;
    }

    .job-page .job-fact-list li {
        display: flex;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.55rem 0;
        border-bottom: 1px solid var(--job-line);
        font-size: 0.8125rem;
    }

    .job-page .job-fact-list li:last-child {
        border-bottom: 0;
    }

    .job-page .job-fact-list .label {
        color: var(--job-muted);
    }

    .job-page .job-fact-list .value {
        color: var(--job-ink);
        font-weight: 600;
        text-align: right;
    }

    .job-page .job-apply-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        width: 100%;
        padding: 0.8rem 1rem;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--job-accent), var(--job-accent-dark));
        color: #fff !important;
        font-weight: 700;
        font-size: 0.9375rem;
        text-decoration: none !important;
        border: 0;
        box-shadow: 0 10px 20px rgba(47, 112, 255, 0.25);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .job-page .job-apply-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 12px 24px rgba(47, 112, 255, 0.32);
        color: #fff !important;
    }

    .job-page .job-download-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        width: 100%;
        margin-top: 0.65rem;
        padding: 0.7rem 1rem;
        border-radius: 12px;
        border: 1px solid var(--job-line);
        background: #f8fafc;
        color: #334155 !important;
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none !important;
    }

    .job-page .job-download-btn:hover {
        border-color: #cbd5e1;
        background: #f1f5f9;
        color: var(--job-ink) !important;
    }

    .job-page .job-aside-note {
        margin: 0.85rem 0 0;
        font-size: 0.75rem;
        color: var(--job-muted);
        text-align: center;
        line-height: 1.4;
    }

    .job-page .job-alert {
        margin-bottom: 1rem;
        border-radius: 12px;
    }

    @media (max-width: 991.98px) {
        .job-page .job-layout {
            grid-template-columns: 1fr;
        }

        .job-page .job-aside {
            position: static;
            order: -1;
        }

        .job-page .job-hero,
        .job-page .job-body {
            padding-left: 1.15rem;
            padding-right: 1.15rem;
        }
    }
</style>
@endpush

@section('content')
<section class="job-page">
    <div class="container">
        <ul class="job-breadcrumb">
            <li><a href="{{ url('/') }}">Home</a></li>
            <li class="sep">/</li>
            <li><a href="{{ url('/') }}#careers">Careers</a></li>
            <li class="sep">/</li>
            <li>{{ $job->job_title ?? $job->post }}</li>
        </ul>

        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible job-alert" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                {{ session()->get('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible job-alert" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                {{ session()->get('error') }}
            </div>
        @endif

        <div class="job-layout">
            <div class="job-panel">
                <div class="job-hero">
                    <h1>{{ $job->job_title ?? $job->post }}</h1>

                    <div class="job-tags">
                        @if($employmentLabel)
                            <span class="job-tag">{{ $employmentLabel }}</span>
                        @endif
                        @if($jobTypeLabel && $jobTypeLabel !== 'UNKNOWN')
                            <span class="job-tag is-soft">{{ $jobTypeLabel }}</span>
                        @endif
                        @if($job->department)
                            <span class="job-tag is-soft">{{ $job->department->department_name }}</span>
                        @endif
                        @if($job->number_of_positions)
                            <span class="job-tag is-soft">{{ $job->number_of_positions }} Position{{ $job->number_of_positions > 1 ? 's' : '' }}</span>
                        @endif
                    </div>

                    <div class="job-meta-row">
                        @if($job->location)
                            <span><i class="bi bi-geo-alt"></i>{{ $job->location->location_name }}</span>
                        @endif
                        <span><i class="bi bi-calendar3"></i>Posted {{ date('d M Y', strtotime($job->publish_date ?? $job->created_at)) }}</span>
                        <span><i class="bi bi-clock"></i>Apply by {{ date('d M Y', strtotime($job->application_end_date)) }}</span>
                        @if($job->minimum_salary || $job->maximum_salary)
                            <span>
                                <i class="bi bi-cash-stack"></i>
                                @if($job->minimum_salary && $job->maximum_salary)
                                    {{ number_format($job->minimum_salary) }} – {{ number_format($job->maximum_salary) }}
                                @elseif($job->minimum_salary)
                                    From {{ number_format($job->minimum_salary) }}
                                @else
                                    Up to {{ number_format($job->maximum_salary) }}
                                @endif
                            </span>
                        @endif
                    </div>
                </div>

                <div class="job-body">
                    @if($job->job_description)
                        <section class="job-section">
                            <h2><i class="bi bi-file-text"></i> Job Description</h2>
                            <div class="job-section-content">{!! $job->job_description !!}</div>
                        </section>
                    @endif

                    @if($job->key_responsibilities)
                        <section class="job-section">
                            <h2><i class="bi bi-list-check"></i> Key Responsibilities</h2>
                            <div class="job-section-content">{!! $job->key_responsibilities !!}</div>
                        </section>
                    @endif

                    @if($job->job_requirements)
                        <section class="job-section">
                            <h2><i class="bi bi-clipboard-check"></i> Requirements</h2>
                            <div class="job-section-content">{!! $job->job_requirements !!}</div>
                        </section>
                    @endif

                    @if($job->minimum_qualifications)
                        <section class="job-section">
                            <h2><i class="bi bi-mortarboard"></i> Qualifications</h2>
                            <div class="job-section-content">{!! $job->minimum_qualifications !!}</div>
                        </section>
                    @endif

                    @if($job->experience_required)
                        <section class="job-section">
                            <h2><i class="bi bi-briefcase"></i> Experience</h2>
                            <div class="job-section-content">{!! $job->experience_required !!}</div>
                        </section>
                    @endif

                    @if($job->skills_competencies)
                        <section class="job-section">
                            <h2><i class="bi bi-stars"></i> Skills & Competencies</h2>
                            <div class="job-section-content">{!! $job->skills_competencies !!}</div>
                        </section>
                    @endif

                    @if($job->other_benefits)
                        <section class="job-section">
                            <h2><i class="bi bi-gift"></i> Benefits</h2>
                            <div class="job-section-content">{!! $job->other_benefits !!}</div>
                        </section>
                    @endif
                </div>
            </div>

            <aside class="job-aside">
                <div class="job-panel job-aside-card">
                    <h3>Role summary</h3>
                    <ul class="job-fact-list">
                        @if($job->department)
                            <li>
                                <span class="label">Department</span>
                                <span class="value">{{ $job->department->department_name }}</span>
                            </li>
                        @endif
                        @if($job->location)
                            <li>
                                <span class="label">Location</span>
                                <span class="value">{{ $job->location->location_name }}</span>
                            </li>
                        @endif
                        @if($employmentLabel)
                            <li>
                                <span class="label">Employment</span>
                                <span class="value">{{ $employmentLabel }}</span>
                            </li>
                        @endif
                        @if($jobTypeLabel && $jobTypeLabel !== 'UNKNOWN')
                            <li>
                                <span class="label">Work type</span>
                                <span class="value">{{ $jobTypeLabel }}</span>
                            </li>
                        @endif
                        @if($job->number_of_positions)
                            <li>
                                <span class="label">Openings</span>
                                <span class="value">{{ $job->number_of_positions }}</span>
                            </li>
                        @endif
                        <li>
                            <span class="label">Deadline</span>
                            <span class="value">{{ date('d M Y', strtotime($job->application_end_date)) }}</span>
                        </li>
                        @if($job->minimum_salary || $job->maximum_salary)
                            <li>
                                <span class="label">Salary</span>
                                <span class="value">
                                    @if($job->minimum_salary && $job->maximum_salary)
                                        {{ number_format($job->minimum_salary) }} – {{ number_format($job->maximum_salary) }}
                                    @elseif($job->minimum_salary)
                                        From {{ number_format($job->minimum_salary) }}
                                    @else
                                        Up to {{ number_format($job->maximum_salary) }}
                                    @endif
                                </span>
                            </li>
                        @endif
                    </ul>

                    <a href="{{ route('job.apply.form', ['id' => $job->job_id, 'slug' => $jobSlug]) }}"
                        class="job-apply-btn">
                        <i class="bi bi-send"></i> Apply now
                    </a>

                    @if ($job->jd_file)
                        <a href="{{ route('jobPost.downloadDescription', $job->job_id) }}"
                            class="job-download-btn">
                            <i class="bi bi-download"></i> Download JD
                        </a>
                    @endif

                    <p class="job-aside-note">
                        Your application details are kept confidential and reviewed by HR.
                    </p>
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection
