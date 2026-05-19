@extends('front.master')

@section('title', $job->job_title)

@php
    $front_setting = getFrontData();
@endphp

@section('meta')
    <meta name="og:title" content="{{ $job->job_title }}" />
    <meta name="og:image" content="{{ asset('storage/uploads/front/' . $front_setting->logo) }}" />
    <meta name="og:url"
        content="{{ route('job.details', ['id' => $job->job_id, 'slug' => str_replace(' ', '-', strtolower($job->job_title))]) }}" />
    <meta name="og:description" content="{{ $job->job_post }}" />
    <meta name="description" content="{{ $job->job_post }}" />
@endsection

@section('content')

    <!-- Start home -->
    <section class="" style="background: url('{{ url('front-assets/images/cover.png') }}') center center;">
        <div class=""></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="text-center text-white mb-3 pt-3">
                        <h4 class="text-uppercase title mb-4">
                            Job Details
                        </h4>
                        <ul class="page-next d-inline-flex align-items-center mb-0 list-unstyled p-0">
                            <li class="mx-2">
                                <a href="{{ url('/') }}" class="text-uppercase font-weight-bold text-white">
                                    Home
                                </a>
                            </li>
                            <li class="mx-2">
                                <a href="#" class="text-uppercase font-weight-bold text-white">
                                    Job
                                </a>
                            </li>
                            <li class="mx-2">
                                <span class="text-uppercase text-white font-weight-bold text-light">
                                    {{ $job->job_title }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end home -->

    <!-- JOB SINGLE START -->
    <section class="section">
        <div class="container">
            <div class="row">
                <div class="mr-auto mx-auto col-lg-10 col-md-10">

                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissable  mb-20">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i
                                class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="alert alert-danger alert-dismissable  mb-20">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <strong>{{ session()->get('error') }}</strong>
                        </div>
                    @endif

                    <!-- Job Header Card -->
                    <div class="job-detail text-center job-single border rounded p-4">
                        <div class="job-single-img mb-2">
                            <img src="{{ asset('images/featured-job/img-1.png') }}" alt="" class="img-fluid mx-auto d-block">
                        </div>
                        <h4 class=""><a href="#" class="text-dark">{{ $job->job_title ?? $job->post }}</a></h4>

                        <!-- Job Meta Tags -->
                        <div class="row mt-3 mb-3">
                            <div class="col-md-12">
                                @if($job->employment_type)
                                    <span class="badge badge-info mr-2">{{ $job->employment_type }}</span>
                                @endif
                                @if($job->job_type)
                                    <span class="badge badge-secondary mr-2">{{ $job->job_type }}</span>
                                @endif
                                @if($job->department)
                                    <span class="badge badge-primary mr-2">{{ $job->department->department_name }}</span>
                                @endif
                                @if($job->number_of_positions)
                                    <span class="badge badge-warning mr-2">{{ $job->number_of_positions }} Position(s)</span>
                                @endif
                            </div>
                        </div>

                        <ul class="list-inline mb-0">
                            <li class="list-inline-item mr-3">
                                <p class="text-muted mb-2"><i class="fa fa-calendar mr-1"></i>Published:
                                    {{ date('d M Y', strtotime($job->created_at)) }}</p>
                            </li>
                            <li class="list-inline-item">
                                <p class="text-muted mb-2"><i class="fa fa-calendar-times mr-1"></i>Deadline:
                                    {{ date('d M Y', strtotime($job->application_end_date)) }}</p>
                            </li>
                        </ul>

                        @if($job->location)
                            <p class="text-muted mb-2"><i class="fa fa-map-marker mr-1"></i>Location: {{ $job->location->location_name }}</p>
                        @endif

                        @if($job->minimum_salary || $job->maximum_salary)
                            <p class="text-muted mb-2">
                                <i class="fa fa-money mr-1"></i>Salary:
                                @if($job->minimum_salary && $job->maximum_salary)
                                    {{ number_format($job->minimum_salary) }} - {{ number_format($job->maximum_salary) }}
                                @elseif($job->minimum_salary)
                                    From {{ number_format($job->minimum_salary) }}
                                @elseif($job->maximum_salary)
                                    Up to {{ number_format($job->maximum_salary) }}
                                @endif
                            </p>
                        @endif
                    </div>

                    <!-- Job Description Section -->
                    @if($job->job_description)
                        <div class="row">
                            <div class="col-lg-12">
                                <h5 class="text-dark mt-4"><i class="fa fa-file-text-o mr-2"></i>Job Description</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="job-detail border rounded mt-2 p-4">
                                    <div class="job-detail-desc">
                                        {!! $job->job_description !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Key Responsibilities Section -->
                    @if($job->key_responsibilities)
                        <div class="row">
                            <div class="col-lg-12">
                                <h5 class="text-dark mt-4"><i class="fa fa-tasks mr-2"></i>Key Responsibilities</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="job-detail border rounded mt-2 p-4">
                                    <div class="job-detail-desc">
                                        {!! $job->key_responsibilities !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Job Requirements Section -->
                    @if($job->job_requirements)
                        <div class="row">
                            <div class="col-lg-12">
                                <h5 class="text-dark mt-4"><i class="fa fa-check-square-o mr-2"></i>Job Requirements</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="job-detail border rounded mt-2 p-4">
                                    <div class="job-detail-desc">
                                        {!! $job->job_requirements !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Minimum Qualifications Section -->
                    @if($job->minimum_qualifications)
                        <div class="row">
                            <div class="col-lg-12">
                                <h5 class="text-dark mt-4"><i class="fa fa-graduation-cap mr-2"></i>Minimum Qualifications</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="job-detail border rounded mt-2 p-4">
                                    <div class="job-detail-desc">
                                        {!! $job->minimum_qualifications !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Experience Required Section -->
                    @if($job->experience_required)
                        <div class="row">
                            <div class="col-lg-12">
                                <h5 class="text-dark mt-4"><i class="fa fa-briefcase mr-2"></i>Experience Required</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="job-detail border rounded mt-2 p-4">
                                    <div class="job-detail-desc">
                                        {!! $job->experience_required !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Skills & Competencies Section -->
                    @if($job->skills_competencies)
                        <div class="row">
                            <div class="col-lg-12">
                                <h5 class="text-dark mt-4"><i class="fa fa-star mr-2"></i>Skills & Competencies</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="job-detail border rounded mt-2 p-4">
                                    <div class="job-detail-desc">
                                        {!! $job->skills_competencies !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Other Benefits Section -->
                    @if($job->other_benefits)
                        <div class="row">
                            <div class="col-lg-12">
                                <h5 class="text-dark mt-4"><i class="fa fa-gift mr-2"></i>Benefits</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="job-detail border rounded mt-2 p-4">
                                    <div class="job-detail-desc">
                                        {!! $job->other_benefits !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Download Job Description -->
                    @if ($job->jd_file)
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="job-detail border rounded mt-4 p-4 text-center">
                                    <a href="{{ route('jobPost.downloadDescription', $job->job_id) }}"
                                        class="btn btn-outline-primary btn-lg"
                                        style="padding: 12px 30px;">
                                        <i class="fa fa-download mr-2"></i> Download Full Job Description
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Apply Button Section -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="job-detail border rounded mt-4 p-4 text-center" style="background-color: #f8f9fa;">
                                <h4 class="text-dark mb-3">Interested in this position?</h4>
                                <p class="text-muted mb-4">Click the button below to submit your application. Make sure you have your resume/CV ready.</p>
                                <a href="{{ route('job.apply.form', ['id' => $job->job_id, 'slug' => str_replace(' ', '-', strtolower($job->job_title ?? $job->post))]) }}"
                                    class="btn btn-primary btn-lg"
                                    style="padding: 15px 50px; font-size: 18px;">
                                    <i class="fa fa-paper-plane mr-2"></i> Apply for this Job
                                </a>
                                <p class="text-muted mt-3 small">
                                    <i class="fa fa-lock mr-1"></i> Your information will be kept confidential
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- JOB SINGLE END -->
@endsection
