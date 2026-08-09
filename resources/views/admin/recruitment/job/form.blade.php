@extends('admin.master')

@section('title')
    {{ isset($editModeData) ? __('recruitement.edit_job_post') : __('recruitement.create_new_job_post') }}
@endsection

@section('content')

<div class="container-fluid">

    {{-- PAGE HEADER --}}
    <div class="row bg-title">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('dashboard') }}">
                        <i class="fa fa-home"></i> @lang('dashboard.dashboard')
                    </a>
                </li>
                <li class="active">@yield('title')</li>
            </ol>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
            <a href="{{ route('jobPost.index') }}"
               class="btn btn-success waves-effect waves-light">
                <i class="fa fa-list"></i>
                @lang('recruitement.view_job_post')
            </a>
        </div>
    </div>

    {{-- FORM PANEL --}}
    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-info">

                <div class="panel-heading">
                    <i class="mdi mdi-clipboard-text fa-fw"></i>
                    @yield('title')
                </div>

                <div class="panel-body">

                    {{-- ALERTS --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert">×</button>

                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- FORM --}}
                    @if(isset($editModeData))
                        <form method="POST"
                              action="{{ route('jobPost.update', $editModeData->job_id) }}"
                              class="form-horizontal"
                              enctype="multipart/form-data"
                              id="jobPostForm">
                            @csrf
                            @method('PUT')
                    @else
                        <form method="POST"
                              action="{{ route('jobPost.store') }}"
                              class="form-horizontal"
                              enctype="multipart/form-data"
                              id="jobPostForm">
                            @csrf
                    @endif

                    <div class="form-body">

                        {{-- JOB REQUISITION --}}
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                @lang('recruitement.job_requisition')
                            </label>

                            <div class="col-md-7">
                                <select name="job_requisition_id"
                                        id="job_requisition_id"
                                        class="form-control">

                                    <option value="">
                                        -- @lang('recruitement.select_job_requisition') --
                                    </option>

                                    @foreach ($jobRequisitions as $requisition)

                                        <option value="{{ $requisition->job_requisition_id }}"
                                            {{ (isset($editModeData) &&
                                                $editModeData->job_requisition_id == $requisition->job_requisition_id)
                                                || old('job_requisition_id') == $requisition->job_requisition_id
                                                ? 'selected' : '' }}>

                                            {{ $requisition->requisition_number }}
                                            - {{ $requisition->position_title }}

                                            @if($requisition->department)
                                                ({{ $requisition->department->department_name }})
                                            @endif

                                            [{{ $requisition->status_label }}]

                                        </option>

                                    @endforeach
                                </select>

                                <small class="text-muted">
                                    @lang('recruitement.job_requisition_help')
                                </small>

                                <div id="requisition_status_alert"
                                     class="alert alert-warning m-t-10"
                                     style="display:none;">
                                    <i class="fa fa-info-circle"></i>
                                    <span id="requisition_status_text"></span>
                                </div>
                            </div>
                        </div>

                        {{-- JOB TITLE --}}
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                @lang('recruitement.job_title')
                                <span class="validateRq">*</span>
                            </label>

                            <div class="col-md-7">
                                <input type="text"
                                       name="job_title"
                                       id="job_title"
                                       class="form-control required"
                                       value="{{ isset($editModeData) ? $editModeData->job_title : old('job_title') }}"
                                       placeholder="{{ __('recruitement.job_title') }}">
                            </div>
                        </div>

                        {{-- JOB TYPE --}}
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                @lang('recruitement.job_type')
                                <span class="validateRq">*</span>
                            </label>

                            <div class="col-md-7">
                                <select name="job_type"
                                        id="job_type"
                                        class="form-control required">

                                    <option value="">
                                        @lang('recruitement.choose_job_type')
                                    </option>

                                    @foreach (\App\Lib\Enumerations\JobTypes::toArray() as $key => $value)

                                        <option value="{{ $key }}"
                                            {{ (isset($editModeData) &&
                                                $editModeData->job_type == $key)
                                                || old('job_type') == $key
                                                ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>

                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- EMPLOYMENT TYPE --}}
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                @lang('recruitement.employment_type')
                            </label>

                            <div class="col-md-7">
                                <select name="employment_type"
                                        id="employment_type"
                                        class="form-control">

                                    <option value="">
                                        @lang('recruitement.choose_employment_type')
                                    </option>

                                    @foreach ([
                                        'full_time' => 'Full Time',
                                        'part_time' => 'Part Time',
                                        'contract' => 'Contract',
                                        'temporary' => 'Temporary',
                                        'internship' => 'Internship',
                                    ] as $key => $value)

                                        <option value="{{ $key }}"
                                            {{ (isset($editModeData) &&
                                                $editModeData->employment_type == $key)
                                                || old('employment_type') == $key
                                                ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>

                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- DEPARTMENT --}}
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                @lang('recruitement.department')
                            </label>

                            <div class="col-md-7">
                                <select name="department_id"
                                        id="department_id"
                                        class="form-control">

                                    <option value="">
                                        @lang('recruitement.choose_department')
                                    </option>

                                    @foreach ($departments as $department)

                                        <option value="{{ $department->department_id }}"
                                            {{ (isset($editModeData) &&
                                                $editModeData->department_id == $department->department_id)
                                                || old('department_id') == $department->department_id
                                                ? 'selected' : '' }}>
                                            {{ $department->department_name }}
                                        </option>

                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- LOCATION --}}
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                @lang('recruitement.job_location')
                                <span class="validateRq">*</span>
                            </label>

                            <div class="col-md-7">
                                <select name="location_id"
                                        id="location_id"
                                        class="form-control required">

                                    <option value="">
                                        @lang('recruitement.choose_location')
                                    </option>

                                    @foreach ($locations as $location)

                                        <option value="{{ $location->location_id }}"
                                            {{ (isset($editModeData) &&
                                                $editModeData->location_id == $location->location_id)
                                                || old('location_id') == $location->location_id
                                                ? 'selected' : '' }}>
                                            {{ $location->location_name }}
                                        </option>

                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- NUMBER OF POSITIONS --}}
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                @lang('recruitement.number_of_positions')
                            </label>

                            <div class="col-md-3">
                                <input type="number"
                                       name="number_of_positions"
                                       id="number_of_positions"
                                       class="form-control"
                                       min="1"
                                       value="{{ isset($editModeData) ? $editModeData->number_of_positions : old('number_of_positions', 1) }}">
                            </div>
                        </div>

                        {{-- DESCRIPTION --}}
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                @lang('recruitement.description')
                                <span class="validateRq">*</span>
                            </label>

                            <div class="col-md-7">
                                <textarea name="job_description"
                                          id="job_description"
                                          rows="8"
                                          class="form-control textarea_editor">{{ isset($editModeData) ? $editModeData->job_description : old('job_description') }}</textarea>
                            </div>
                        </div>

                        {{-- REQUIREMENTS --}}
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                @lang('recruitement.job_requirements')
                            </label>

                            <div class="col-md-7">
                                <textarea name="job_requirements"
                                          id="job_requirements"
                                          rows="6"
                                          class="form-control textarea_editor">{{ isset($editModeData) ? $editModeData->job_requirements : old('job_requirements') }}</textarea>
                            </div>
                        </div>

                        {{-- QUALIFICATIONS (narrative) --}}
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                @lang('recruitement.minimum_qualifications')
                            </label>

                            <div class="col-md-7">
                                <textarea name="minimum_qualifications"
                                          id="minimum_qualifications"
                                          rows="5"
                                          class="form-control textarea_editor">{{ isset($editModeData) ? $editModeData->minimum_qualifications : old('minimum_qualifications') }}</textarea>
                            </div>
                        </div>

                        {{-- EXPERIENCE (narrative) --}}
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                @lang('recruitement.experience_required')
                            </label>

                            <div class="col-md-7">
                                <input type="text"
                                       name="experience_required"
                                       id="experience_required"
                                       class="form-control"
                                       placeholder="{{ __('recruitement.experience_required_placeholder') }}"
                                       value="{{ isset($editModeData) ? $editModeData->experience_required : old('experience_required') }}">
                            </div>
                        </div>

                        {{-- SKILLS NARRATIVE --}}
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                @lang('recruitement.skills_competencies')
                            </label>

                            <div class="col-md-7">
                                <textarea name="skills_competencies"
                                          id="skills_competencies"
                                          rows="4"
                                          class="form-control textarea_editor">{{ isset($editModeData) ? $editModeData->skills_competencies : old('skills_competencies') }}</textarea>
                            </div>
                        </div>

                        <hr>
                        <div class="form-group">
                            <div class="col-md-offset-3 col-md-7">
                                <h4 class="m-t-10 m-b-5">
                                    <i class="fa fa-filter"></i> @lang('recruitement.ats_screening_criteria')
                                </h4>
                                <p class="text-muted">
                                    @lang('recruitement.ats_screening_criteria_help')
                                </p>
                            </div>
                        </div>

                        {{-- REQUIRED QUALIFICATION (ATS) --}}
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                @lang('recruitement.required_qualification')
                            </label>

                            <div class="col-md-4">
                                @php
                                    $selectedRequiredQualification = old(
                                        'required_qualification',
                                        isset($editModeData) ? $editModeData->required_qualification : ''
                                    );
                                @endphp
                                <select name="required_qualification"
                                        id="required_qualification"
                                        class="form-control">
                                    <option value="">
                                        -- @lang('recruitement.choose_required_qualification') --
                                    </option>
                                    @foreach (\App\Lib\Enumerations\QualificationLevel::options() as $level)
                                        <option value="{{ $level }}"
                                            {{ $selectedRequiredQualification == $level ? 'selected' : '' }}>
                                            {{ $level }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">@lang('recruitement.required_qualification_help')</small>
                            </div>
                        </div>

                        {{-- MIN YEARS EXPERIENCE (ATS) --}}
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                @lang('recruitement.min_years_experience')
                            </label>

                            <div class="col-md-3">
                                <input type="number"
                                       name="min_years_experience"
                                       id="min_years_experience"
                                       class="form-control"
                                       min="0"
                                       max="50"
                                       value="{{ isset($editModeData) ? $editModeData->min_years_experience : old('min_years_experience') }}">
                                <small class="text-muted">@lang('recruitement.min_years_experience_help')</small>
                            </div>
                        </div>

                        {{-- REQUIRED SKILLS (ATS) --}}
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                @lang('recruitement.required_skills')
                            </label>

                            <div class="col-md-7">
                                <textarea name="required_skills"
                                          id="required_skills"
                                          rows="3"
                                          class="form-control"
                                          placeholder="{{ __('recruitement.required_skills_placeholder') }}">{{ isset($editModeData) ? $editModeData->required_skills : old('required_skills') }}</textarea>
                                <small class="text-muted">@lang('recruitement.required_skills_help')</small>
                            </div>
                        </div>

                        <hr>

                        {{-- SALARY --}}
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                @lang('recruitement.salary_range')
                            </label>

                            <div class="col-md-3">
                                <input type="number"
                                       name="minimum_salary"
                                       id="minimum_salary"
                                       class="form-control"
                                       placeholder="Minimum Salary"
                                       value="{{ isset($editModeData) ? $editModeData->minimum_salary : old('minimum_salary') }}">
                            </div>

                            <div class="col-md-3">
                                <input type="number"
                                       name="maximum_salary"
                                       id="maximum_salary"
                                       class="form-control"
                                       placeholder="Maximum Salary"
                                       value="{{ isset($editModeData) ? $editModeData->maximum_salary : old('maximum_salary') }}">
                            </div>
                        </div>

                        {{-- STATUS --}}
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                @lang('common.status')
                            </label>

                            <div class="col-md-7">
                                <select name="status"
                                        class="form-control">

                                    <option value="1"
                                        {{ (isset($editModeData) &&
                                            $editModeData->status == 1)
                                            || old('status') == '1'
                                            ? 'selected' : '' }}>
                                        @lang('recruitement.published')
                                    </option>

                                    <option value="0"
                                        {{ (isset($editModeData) &&
                                            $editModeData->status == 0)
                                            || old('status') == '0'
                                            ? 'selected' : '' }}>
                                        @lang('recruitement.unpublished')
                                    </option>

                                </select>
                            </div>
                        </div>

                        {{-- AUDIENCE --}}
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                @lang('recruitement.audience_type')
                            </label>

                            <div class="col-md-7">
                                @php
                                    $selectedAudience = old(
                                        'audience_type',
                                        isset($editModeData) ? $editModeData->audience_type : 'both'
                                    );
                                @endphp
                                <select name="audience_type"
                                        class="form-control">
                                    <option value="internal" {{ $selectedAudience == 'internal' ? 'selected' : '' }}>Internal</option>
                                    <option value="external" {{ $selectedAudience == 'external' ? 'selected' : '' }}>External</option>
                                    <option value="both" {{ $selectedAudience == 'both' ? 'selected' : '' }}>Both</option>
                                </select>
                            </div>
                        </div>

                        {{-- PUBLISH DATE --}}
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                @lang('recruitement.job_publish_date')
                            </label>

                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>

                                    <input type="text"
                                           name="job_publish_date"
                                           id="job_publish_date"
                                           class="form-control dateField"
                                           readonly
                                           value="{{ isset($editModeData) ? dateConvertDBtoForm($editModeData->publish_date) : date('d/m/Y') }}">
                                </div>
                            </div>
                        </div>

                        {{-- APPLICATION END DATE --}}
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                @lang('recruitement.application_end_date')
                                <span class="validateRq">*</span>
                            </label>

                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>

                                    <input type="text"
                                           name="application_end_date"
                                           id="application_end_date"
                                           class="form-control dateField"
                                           readonly
                                           value="{{ isset($editModeData) ? dateConvertDBtoForm($editModeData->application_end_date) : old('application_end_date') }}">
                                </div>
                            </div>
                        </div>

                        {{-- FILE UPLOAD --}}
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                @lang('recruitement.upload_job_description')
                            </label>

                            <div class="col-md-7">
                                <input type="file"
                                       name="jd_file"
                                       id="jd_file"
                                       class="form-control">

                                @if(isset($editModeData) && $editModeData->jd_file)

                                    <div class="m-t-10">
                                        <a href="{{ route('jobPost.viewJd', $editModeData->job_id) }}"
                                           target="_blank"
                                           class="btn btn-xs btn-info">
                                            View File
                                        </a>

                                        <a href="{{ route('jobPost.downloadJd', $editModeData->job_id) }}"
                                           class="btn btn-xs btn-primary">
                                            Download
                                        </a>
                                    </div>

                                @endif
                            </div>
                        </div>

                    </div>

                    {{-- FORM FOOTER --}}
                    <div class="form-actions">

                        <div class="row">
                            <div class="col-md-offset-3 col-md-7">

                                @if(isset($editModeData))

                                    <button type="submit"
                                            class="btn btn-info">
                                        <i class="fa fa-pencil"></i>
                                        @lang('common.update')
                                    </button>

                                @else

                                    <button type="submit"
                                            class="btn btn-success">
                                        <i class="fa fa-save"></i>
                                        @lang('common.save')
                                    </button>

                                @endif

                            </div>
                        </div>

                    </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('page_scripts')

<link rel="stylesheet"
      href="{!! asset('admin_assets/plugins/bower_components/html5-editor/bootstrap-wysihtml5.css') !!}" />

<script src="{!! asset('admin_assets/plugins/bower_components/html5-editor/wysihtml5-0.3.0.js') !!}"></script>

<script src="{!! asset('admin_assets/plugins/bower_components/html5-editor/bootstrap-wysihtml5.js') !!}"></script>

<script>
$(document).ready(function () {
    // Check if wysihtml5 is loaded properly
    if (typeof $.fn.wysihtml5 !== 'undefined') {
        $('.textarea_editor').each(function() {
            try {
                $(this).wysihtml5({
                    toolbar: {
                        'font-styles': true,
                        'emphasis': true,
                        'lists': true,
                        'html': false,
                        'link': true,
                        'image': false
                    }
                });
            } catch(e) {
                console.error('Wysihtml5 error:', e);
                // Fallback to textarea
                $(this).removeClass('textarea_editor').show();
            }
        });
    } else {
        console.warn('wysihtml5 not loaded');
        $('.textarea_editor').removeClass('textarea_editor').show();
    }

    // Fix AJAX endpoint - use proper route
    $('#job_requisition_id').on('change', function () {
        let requisitionId = $(this).val();

        if (!requisitionId) {
            $('#requisition_status_alert').hide();
            return;
        }

        // Show loading indicator
        $('#requisition_status_alert').show().html('<i class="fa fa-spinner fa-spin"></i> Loading...');

        // Use proper route helper instead of hardcoded URL
        let ajaxUrl = '{{ url("recruitment/jobPost/ajax/job-requisition") }}/' + requisitionId;
        
        $.ajax({
            url: ajaxUrl,
            type: 'GET',
            dataType: 'json',
            timeout: 10000, // 10 second timeout
            success: function (response) {
                if (!response.success) {
                    showRequisitionError('Failed to load requisition data.');
                    return;
                }

                let data = response.data;

                // Populate fields safely
                $('#job_title').val(data.job_title || '');
                $('#job_type').val(data.job_type || '');
                $('#employment_type').val(data.employment_type || '');
                $('#department_id').val(data.department_id || '');
                $('#location_id').val(data.location_id || '');
                $('#number_of_positions').val(data.number_of_positions || 1);
                $('#minimum_salary').val(data.minimum_salary || '');
                $('#maximum_salary').val(data.maximum_salary || '');
                $('#experience_required').val(data.experience_required || '');
                $('#required_skills').val(data.required_skills || data.skills_competencies || '');
                if (data.min_years_experience !== undefined && data.min_years_experience !== null) {
                    $('#min_years_experience').val(data.min_years_experience);
                }
                if (data.required_qualification) {
                    $('#required_qualification').val(data.required_qualification);
                }

                // Safely update wysihtml5 editors
                try {
                    if (data.job_description && $('#job_description').data('wysihtml5')) {
                        $('#job_description').data('wysihtml5').editor.setValue(data.job_description);
                    } else if (data.job_description) {
                        $('#job_description').val(data.job_description);
                    }
                } catch(e) { console.error('Error setting description:', e); }

                try {
                    if (data.job_requirements && $('#job_requirements').data('wysihtml5')) {
                        $('#job_requirements').data('wysihtml5').editor.setValue(data.job_requirements);
                    } else if (data.job_requirements) {
                        $('#job_requirements').val(data.job_requirements);
                    }
                } catch(e) { console.error('Error setting requirements:', e); }

                try {
                    if (data.minimum_qualifications && $('#minimum_qualifications').data('wysihtml5')) {
                        $('#minimum_qualifications').data('wysihtml5').editor.setValue(data.minimum_qualifications);
                    } else if (data.minimum_qualifications) {
                        $('#minimum_qualifications').val(data.minimum_qualifications);
                    }
                } catch(e) { console.error('Error setting qualifications:', e); }

                try {
                    if (data.skills_competencies && $('#skills_competencies').data('wysihtml5')) {
                        $('#skills_competencies').data('wysihtml5').editor.setValue(data.skills_competencies);
                    } else if (data.skills_competencies) {
                        $('#skills_competencies').val(data.skills_competencies);
                    }
                } catch(e) { console.error('Error setting skills:', e); }

                let alertBox = $('#requisition_status_alert');
                
                // Show requisition status message
                if (response.requisition_status !== 2) {
                    alertBox
                        .removeClass('alert-success')
                        .addClass('alert-warning')
                        .html('<i class="fa fa-info-circle"></i> ' +
                            'This requisition is currently "' +
                            (response.requisition_status_label || 'Unknown') +
                            '".');
                } else {
                    alertBox
                        .removeClass('alert-warning')
                        .addClass('alert-success')
                        .html('<i class="fa fa-check-circle"></i> ' +
                            'This requisition is approved.');
                }
                alertBox.show();
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                showRequisitionError('Error loading requisition data. Please check the console for details.');
            }
        });
    });

    function showRequisitionError(message) {
        $('#requisition_status_alert')
            .removeClass('alert-success alert-warning')
            .addClass('alert-danger')
            .html('<i class="fa fa-exclamation-triangle"></i> ' + message)
            .show();
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            $('#requisition_status_alert').fadeOut();
        }, 5000);
    }

    // Validate date fields are properly initialized
    if ($('.dateField').length && typeof $.fn.datepicker !== 'undefined') {
        $('.dateField').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true
        });
    }
});
</script>

@endsection