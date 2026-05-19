@extends('front.master')

@section('title', 'Apply for ' . ($job->job_title ?? $job->post))

@php
    $front_setting = getFrontData();
@endphp

@section('meta')
    <meta name="og:title" content="Apply for {{ $job->job_title ?? $job->post }}" />
    <meta name="og:image" content="{{ asset('storage/uploads/front/' . $front_setting->logo) }}" />
    <meta name="description" content="Application form for {{ $job->job_title ?? $job->post }}" />
@endsection

@section('content')

    <!-- Start home -->
    <section class="" style="background: url('{{ url('front-assets/images/cover.png') }}') center center;">
        <div class=""></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="text-center text-white mb-3 pt-3">
                        <h4 class="text-uppercase title mb-4">
                            Job Application
                        </h4>
                        <ul class="page-next d-inline-flex align-items-center mb-0 list-unstyled p-0">
                            <li class="mx-2">
                                <a href="{{ url('/') }}" class="text-uppercase font-weight-bold text-white">
                                    Home
                                </a>
                            </li>
                            <li class="mx-2">
                                <a href="{{ route('job.details', ['id' => $job->job_id, 'slug' => str_replace(' ', '-', strtolower($job->job_title ?? $job->post))]) }}"
                                    class="text-uppercase font-weight-bold text-white">
                                    Job Details
                                </a>
                            </li>
                            <li class="mx-2">
                                <span class="text-uppercase text-white font-weight-bold text-light">
                                    Apply
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end home -->

    <!-- JOB APPLICATION FORM START -->
    <section class="section">
        <div class="container">
            <div class="row">
                <div class="mr-auto mx-auto col-lg-10 col-md-10">

                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible mb-4" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">×</span></button>
                            <strong>Please correct the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="alert alert-danger alert-dismissable mb-4">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <strong>{{ session()->get('error') }}</strong>
                        </div>
                    @endif

                    <!-- Job Summary Card -->
                    <div class="job-detail text-center job-single border rounded p-4 mb-4">
                        <h4 class="text-dark">{{ $job->job_title ?? $job->post }}</h4>
                        <p class="text-muted mb-2">
                            @if($job->department)
                                <span class="mr-3"><i class="fa fa-building mr-1"></i>{{ $job->department->department_name }}</span>
                            @endif
                            @if($job->location)
                                <span class="mr-3"><i class="fa fa-map-marker mr-1"></i>{{ $job->location->location_name }}</span>
                            @endif
                            <span><i class="fa fa-calendar-times mr-1"></i>Deadline: {{ date('d M Y', strtotime($job->application_end_date)) }}</span>
                        </p>
                    </div>

                    <!-- Application Form -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="job-detail border rounded p-4">
                                <h4 class="text-dark mb-4 pb-2 border-bottom">
                                    <i class="fa fa-user-plus mr-2"></i>Application Form
                                </h4>

                                <form action="{{ route('job.external.apply') }}" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="job_id" value="{{ $job->job_id }}">
                                    <input type="hidden" name="application_source" value="external">

                                    <!-- Personal Information Section -->
                                    <h5 class="text-primary mb-3 mt-4">
                                        <i class="fa fa-user mr-2"></i>Personal Information
                                    </h5>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group app-label">
                                                <label class="text-muted">Full Name <span class="text-danger">*</span></label>
                                                <input name="name" id="name" value="{{ old('name') }}"
                                                    type="text" class="form-control resume"
                                                    placeholder="Enter your full name" required>
                                                @error('name')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group app-label">
                                                <label class="text-muted">Email Address <span class="text-danger">*</span></label>
                                                <input name="email" value="{{ old('email') }}" type="email"
                                                    class="form-control resume" placeholder="Enter your email address" required>
                                                <small class="form-text text-muted">We'll never share your email with anyone else.</small>
                                                @error('email')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group app-label">
                                                <label class="text-muted">Phone Number <span class="text-danger">*</span></label>
                                                <input name="phone" value="{{ old('phone') }}" type="tel"
                                                    class="form-control resume" placeholder="Enter your phone number" required>
                                                @error('phone')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group app-label">
                                                <label class="text-muted">Date of Birth</label>
                                                <input name="date_of_birth" value="{{ old('date_of_birth') }}" type="date"
                                                    class="form-control resume">
                                                @error('date_of_birth')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group app-label">
                                                <label class="text-muted">Gender</label>
                                                <select name="gender" class="form-control">
                                                    <option value="">Select Gender</option>
                                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                                    <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                                    <option value="Prefer not to say" {{ old('gender') == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                                                </select>
                                                @error('gender')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group app-label">
                                                <label class="text-muted">Nationality</label>
                                                <input name="nationality" value="{{ old('nationality') }}" type="text"
                                                    class="form-control resume" placeholder="Enter your nationality">
                                                @error('nationality')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Address Section -->
                                    <h5 class="text-primary mb-3 mt-4">
                                        <i class="fa fa-home mr-2"></i>Address Information
                                    </h5>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group app-label">
                                                <label class="text-muted">Current Address</label>
                                                <textarea name="current_address" rows="2" class="form-control resume"
                                                    placeholder="Enter your current address">{{ old('current_address') }}</textarea>
                                                @error('current_address')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group app-label">
                                                <label class="text-muted">City</label>
                                                <input name="city" value="{{ old('city') }}" type="text"
                                                    class="form-control resume" placeholder="City">
                                                @error('city')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group app-label">
                                                <label class="text-muted">State/Province</label>
                                                <input name="state" value="{{ old('state') }}" type="text"
                                                    class="form-control resume" placeholder="State/Province">
                                                @error('state')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group app-label">
                                                <label class="text-muted">Country</label>
                                                <input name="country" value="{{ old('country') }}" type="text"
                                                    class="form-control resume" placeholder="Country">
                                                @error('country')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Professional Information Section -->
                                    <h5 class="text-primary mb-3 mt-4">
                                        <i class="fa fa-briefcase mr-2"></i>Professional Information
                                    </h5>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group app-label">
                                                <label class="text-muted" for="highest_qualification">Highest Qualification <span class="text-danger">*</span></label>
                                                <select name="highest_qualification" id="highest_qualification"
                                                    class="form-control" required>
                                                    <option value="" disabled selected>Select Highest Qualification</option>
                                                    <option value="None" {{ old('highest_qualification') == 'None' ? 'selected' : '' }}>None</option>
                                                    <option value="High School" {{ old('highest_qualification') == 'High School' ? 'selected' : '' }}>High School</option>
                                                    <option value="Associate Degree" {{ old('highest_qualification') == 'Associate Degree' ? 'selected' : '' }}>Associate Degree</option>
                                                    <option value="Bachelor's Degree" {{ old('highest_qualification') == "Bachelor's Degree" ? 'selected' : '' }}>Bachelor's Degree</option>
                                                    <option value="Master's Degree" {{ old('highest_qualification') == "Master's Degree" ? 'selected' : '' }}>Master's Degree</option>
                                                    <option value="PhD" {{ old('highest_qualification') == 'PhD' ? 'selected' : '' }}>PhD</option>
                                                </select>
                                                @error('highest_qualification')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group app-label">
                                                <label class="text-muted" for="years_of_experience">Years of Experience <span class="text-danger">*</span></label>
                                                <select name="years_of_experience" id="years_of_experience"
                                                    class="form-control" required>
                                                    <option value="" disabled selected>Select your years of experience</option>
                                                    @for ($i = 0; $i <= 20; $i++)
                                                        <option value="{{ $i }}" {{ old('years_of_experience') == $i ? 'selected' : '' }}>
                                                            {{ $i }} {{ $i == 1 ? 'year' : 'years' }}</option>
                                                    @endfor
                                                </select>
                                                @error('years_of_experience')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group app-label">
                                                <label class="text-muted">Current/Last Employer</label>
                                                <input name="current_employer" value="{{ old('current_employer') }}" type="text"
                                                    class="form-control resume" placeholder="Company name">
                                                @error('current_employer')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group app-label">
                                                <label class="text-muted">Current/Last Position</label>
                                                <input name="current_position" value="{{ old('current_position') }}" type="text"
                                                    class="form-control resume" placeholder="Job title">
                                                @error('current_position')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group app-label">
                                                <label class="text-muted">Notice Period</label>
                                                <select name="notice_period" class="form-control">
                                                    <option value="">Select Notice Period</option>
                                                    <option value="Immediate" {{ old('notice_period') == 'Immediate' ? 'selected' : '' }}>Immediate</option>
                                                    <option value="1 week" {{ old('notice_period') == '1 week' ? 'selected' : '' }}>1 week</option>
                                                    <option value="2 weeks" {{ old('notice_period') == '2 weeks' ? 'selected' : '' }}>2 weeks</option>
                                                    <option value="1 month" {{ old('notice_period') == '1 month' ? 'selected' : '' }}>1 month</option>
                                                    <option value="2 months" {{ old('notice_period') == '2 months' ? 'selected' : '' }}>2 months</option>
                                                    <option value="3 months" {{ old('notice_period') == '3 months' ? 'selected' : '' }}>3 months</option>
                                                    <option value="More than 3 months" {{ old('notice_period') == 'More than 3 months' ? 'selected' : '' }}>More than 3 months</option>
                                                </select>
                                                @error('notice_period')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group app-label">
                                                <label class="text-muted">Expected Salary ({{ $job->currency ?? 'USD' }})</label>
                                                <input name="expected_salary" value="{{ old('expected_salary') }}" type="number"
                                                    class="form-control resume" placeholder="Enter expected salary">
                                                @error('expected_salary')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Online Presence Section -->
                                    <h5 class="text-primary mb-3 mt-4">
                                        <i class="fa fa-globe mr-2"></i>Online Presence
                                    </h5>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group app-label">
                                                <label class="text-muted">LinkedIn Profile URL</label>
                                                <input name="linkedin_url" value="{{ old('linkedin_url') }}" type="url"
                                                    class="form-control resume" placeholder="https://linkedin.com/in/yourprofile">
                                                @error('linkedin_url')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group app-label">
                                                <label class="text-muted">Portfolio/Website URL</label>
                                                <input name="portfolio_url" value="{{ old('portfolio_url') }}" type="url"
                                                    class="form-control resume" placeholder="https://yourportfolio.com">
                                                @error('portfolio_url')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Documents Section -->
                                    <h5 class="text-primary mb-3 mt-4">
                                        <i class="fa fa-file-text mr-2"></i>Documents
                                    </h5>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group app-label">
                                                <label class="text-muted">Attach Resume/CV <span class="text-danger">*</span></label>
                                                <input name="resume" type="file" class="form-control resume" required
                                                    accept=".pdf,.doc,.docx">
                                                <small class="form-text text-muted">Accepted formats: PDF, DOC, DOCX (Max: 2MB)</small>
                                                @error('resume')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group app-label">
                                                <label class="text-muted">Cover Letter</label>
                                                <textarea name="cover_letter" rows="3" class="form-control resume"
                                                    placeholder="Tell us why you're a great fit for this role (min 50 characters)">{{ old('cover_letter') }}</textarea>
                                                <small class="form-text text-muted">Minimum 50 characters</small>
                                                @error('cover_letter')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Additional Information Section -->
                                    <h5 class="text-primary mb-3 mt-4">
                                        <i class="fa fa-info-circle mr-2"></i>Additional Information
                                    </h5>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group app-label">
                                                <label class="text-muted">How did you hear about this position?</label>
                                                <select name="referral_source" class="form-control">
                                                    <option value="">Select source</option>
                                                    <option value="Company Website" {{ old('referral_source') == 'Company Website' ? 'selected' : '' }}>Company Website</option>
                                                    <option value="LinkedIn" {{ old('referral_source') == 'LinkedIn' ? 'selected' : '' }}>LinkedIn</option>
                                                    <option value="Job Board" {{ old('referral_source') == 'Job Board' ? 'selected' : '' }}>Job Board</option>
                                                    <option value="Social Media" {{ old('referral_source') == 'Social Media' ? 'selected' : '' }}>Social Media</option>
                                                    <option value="Referral" {{ old('referral_source') == 'Referral' ? 'selected' : '' }}>Referral/Employee Recommendation</option>
                                                    <option value="Recruitment Agency" {{ old('referral_source') == 'Recruitment Agency' ? 'selected' : '' }}>Recruitment Agency</option>
                                                    <option value="Other" {{ old('referral_source') == 'Other' ? 'selected' : '' }}>Other</option>
                                                </select>
                                                @error('referral_source')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group app-label">
                                                <label class="text-muted">Additional Comments</label>
                                                <textarea name="additional_comments" rows="3" class="form-control resume"
                                                    placeholder="Any other information you'd like to share">{{ old('additional_comments') }}</textarea>
                                                @error('additional_comments')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Consent Section -->
                                    <div class="row mt-4">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="consent" name="consent" required>
                                                    <label class="custom-control-label" for="consent">
                                                        I confirm that the information provided is accurate and complete. I understand that any false statements may result in disqualification or termination of employment.
                                                    </label>
                                                </div>
                                                @error('consent')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit Buttons -->
                                    <div class="row mt-4">
                                        <div class="col-lg-12 text-center">
                                            <button type="submit" class="btn btn-primary btn-lg" style="padding: 12px 40px;">
                                                <i class="fa fa-paper-plane mr-2"></i> Submit Application
                                            </button>
                                            <a href="{{ route('job.details', ['id' => $job->job_id, 'slug' => str_replace(' ', '-', strtolower($job->job_title ?? $job->post))]) }}"
                                                class="btn btn-outline-secondary btn-lg ml-2" style="padding: 12px 40px;">
                                                <i class="fa fa-arrow-left mr-2"></i> Back to Job Details
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- JOB APPLICATION FORM END -->
@endsection
