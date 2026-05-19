@extends('admin.master')

@section('title', $job->job_title)

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
            <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
                <a href="{{ route('ess.recruitment.job.posts') }}"
                    class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                        class="fa fa-list-ul" aria-hidden="true"></i>Job Posts List</a>
            </div>
        </div>
        <!--/.row bg-title -->

        <div class="row">
            <div class="col-sm-12 mr-auto mx-auto">
                <div class="panel panel-info">
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @if ($errors->any())
                                <!-- <div class="job-detail text-center job-single border rounded p-4 mb-20"> -->
                                <div class="alert alert-danger alert-dismissible mb-20" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                            aria-hidden="true">×</span></button>
                                    @foreach ($errors->all() as $error)
                                        <strong>{!! $error !!}</strong><br>
                                    @endforeach
                                </div>
                                <!-- </div> -->
                            @endif

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

                            <div class="job-detail text-center job-single border rounded p-4">
                                <div class="job-single-img mb-2">
                                    <img src="images/featured-job/img-1.png" alt=""
                                        class="img-fluid mx-auto d-block">
                                </div>
                                <h4 class=""><a href="#" class="text-dark">{{ $job->post }}</a></h4>
                                <ul class="list-inline mb-0">
                                    <li class="list-inline-item mr-3">
                                        <p class="text-muted mb-2"><i class="fa fa-calendar  mr-1"></i>Published at :
                                            {{ date('d M Y', strtotime($job->created_at)) }}</p>
                                    </li>

                                    <li class="list-inline-item">
                                        <p class="text-muted mb-2"><i class="fa fa-calendar-times mr-1"></i>Deadline :
                                            {{ date('d M Y', strtotime($job->application_end_date)) }}</p>
                                    </li>
                                </ul>

                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <h5 class="text-dark mt-4">Job Description :</h5>
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

                            <div class="row">
                                <div class="col-lg-12">
                                    <h4 class="text-dark mt-4">Apply for this Job:</h4>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="job-detail border rounded mt-2 p-4">
                                        <form action="{{ route('ess.recruitment.apply.job', $job->job_id) }}" method="post"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="job_id" value="{{ $job->job_id }}">
                                            <input type="hidden" name="application_source" value="internal">
                                            <input type="hidden" name="employee_id" value="{{ $employee->employee_id ?? '' }}">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group app-label">
                                                        <label class="text-muted">Name</label>
                                                        <input name="name" id="name"
                                                            value="@isset($employee){{ $employee->first_name }} {{ $employee->middle_name }} {{ $employee->last_name }}@else{{ old('name') }}@endisset"
                                                            type="text" class="form-control resume"
                                                            placeholder="Applicant Name" required>
                                                        @error('name')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group app-label">
                                                        <label class="text-muted">Email</label>
                                                        <input name="email" value="@isset($employee){{ $employee->email }}@else{{ old('email') }}@endisset" type="email"
                                                            class="form-control resume" placeholder="Applicant Email"
                                                            required>
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group app-label">
                                                        <label class="text-muted">Phone No</label>
                                                        <input name="phone" value="@isset($employee){{ $employee->phone }}@else{{ old('phone') }}@endisset" type="text"
                                                            class="form-control resume" placeholder="Phone No" required>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group app-label">
                                                        <label class="text-muted" for="highest_qualification">Highest
                                                            Qualification</label>

                                                        <select name="highest_qualification" id="highest_qualification"
                                                            class="form-control" required>
                                                            <option value="" disabled selected>Select Highest
                                                                Qualification
                                                            </option>
                                                            <option value="None"
                                                                {{ old('highest_qualification', $application->highest_qualification ?? 'None') == 'None' ? 'selected' : '' }}>
                                                                None</option>
                                                            <option value="High School"
                                                                {{ old('highest_qualification', $application->highest_qualification ?? '') == 'High School' ? 'selected' : '' }}>
                                                                High School</option>
                                                            <option value="Associate Degree"
                                                                {{ old('highest_qualification', $application->highest_qualification ?? '') == 'Associate Degree' ? 'selected' : '' }}>
                                                                Associate Degree</option>
                                                            <option value="Bachelor's Degree"
                                                                {{ old('highest_qualification', $application->highest_qualification ?? '') == "Bachelor's Degree" ? 'selected' : '' }}>
                                                                Bachelor's Degree</option>
                                                            <option value="Master's Degree"
                                                                {{ old('highest_qualification', $application->highest_qualification ?? '') == "Master's Degree" ? 'selected' : '' }}>
                                                                Master's Degree</option>
                                                            <option value="PhD"
                                                                {{ old('highest_qualification', $application->highest_qualification ?? '') == 'PhD' ? 'selected' : '' }}>
                                                                PhD</option>
                                                        </select>

                                                        @error('highest_qualification')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group app-label">
                                                        <label class="text-muted" for="years_of_experience">Years Of
                                                            Experience</label>

                                                        <select name="years_of_experience" id="years_of_experience"
                                                            class="form-control" required>
                                                            <option value="" disabled selected>Select your years of
                                                                experience
                                                            </option>
                                                            @for ($i = 1; $i <= 10; $i++)
                                                                <option value="{{ $i }}"
                                                                    {{ old('years_of_experience', $application->years_of_experience ?? '') == $i ? 'selected' : '' }}>
                                                                    {{ $i }} year(s)</option>
                                                            @endfor
                                                        </select>

                                                        @error('years_of_experience')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group app-label">
                                                        <label class="text-muted">Attach Resume</label>
                                                        <input name="resume" type="file" class="form-control resume"
                                                            placeholder="Resume" required>
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <div class="form-group app-label">
                                                        <label class="text-muted">Cover Letter :</label>
                                                        <textarea name="cover_letter" id="addition-information" rows="4" class="form-control resume"
                                                            placeholder="Write Something About You">{{ old('name') }}</textarea>
                                                    </div>

                                                    <input type="submit" id="submit"
                                                        class="submitBnt btn btn-primary" value="Apply">
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
