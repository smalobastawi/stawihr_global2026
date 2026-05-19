@extends('admin.master')
@section('content')
@section('title')
    @if (isset($editModeData))
        @lang('job_requisition.edit_job_requisition')
    @else
        @lang('job_requisition.create_new_job_requisition')
    @endif
@endsection

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
            <a href="{{ route('jobRequisition.index') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-list-ul" aria-hidden="true"></i> @lang('job_requisition.view_requisitions')
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if (isset($editModeData))
                            <form action="{{ route('jobRequisition.update', $editModeData->job_requisition_id) }}" method="POST" id="jobRequisitionForm" class="form-horizontal">
                                @csrf
                                @method('PUT')
                        @else
                            <form action="{{ route('jobRequisition.store') }}" method="POST" id="jobRequisitionForm" class="form-horizontal">
                                @csrf
                        @endif

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-offset-2 col-md-8">
                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span></button>
                                            @foreach ($errors->all() as $error)
                                                <strong>{!! $error !!}</strong><br>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- 1. POSITION DETAILS --}}
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="box-title text-info"><i class="fa fa-briefcase"></i> 1. POSITION DETAILS</h4>
                                    <hr class="m-t-0 m-b-20">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Department <span class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <select name="department_id" id="department_id" class="form-control required">
                                                <option value="">-- Select Department --</option>
                                                @foreach ($departments as $department)
                                                    <option value="{{ $department->department_id }}"
                                                        {{ (isset($editModeData) && $editModeData->department_id == $department->department_id) || old('department_id') == $department->department_id ? 'selected' : '' }}>
                                                        {{ $department->department_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Job Title <span class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <input type="text" name="position_title" class="form-control required"
                                                value="{{ old('position_title', isset($editModeData) ? $editModeData->position_title : '') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">No. of Positions <span class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <input type="number" name="number_of_positions" class="form-control required" min="1"
                                                value="{{ old('number_of_positions', isset($editModeData) ? $editModeData->number_of_positions : 1) }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Employment Type <span class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <select name="employment_type" class="form-control required">
                                                <option value="">-- Select --</option>
                                                <option value="permanent" {{ old('employment_type', isset($editModeData) ? $editModeData->employment_type : '') == 'permanent' ? 'selected' : '' }}>Permanent</option>
                                                <option value="contract" {{ old('employment_type', isset($editModeData) ? $editModeData->employment_type : '') == 'contract' ? 'selected' : '' }}>Contract</option>
                                                <option value="casual" {{ old('employment_type', isset($editModeData) ? $editModeData->employment_type : '') == 'casual' ? 'selected' : '' }}>Casual</option>
                                                <option value="internship" {{ old('employment_type', isset($editModeData) ? $editModeData->employment_type : '') == 'internship' ? 'selected' : '' }}>Internship</option>
                                                <option value="full_time" {{ old('employment_type', isset($editModeData) ? $editModeData->employment_type : '') == 'full_time' ? 'selected' : '' }}>Full Time</option>
                                                <option value="part_time" {{ old('employment_type', isset($editModeData) ? $editModeData->employment_type : '') == 'part_time' ? 'selected' : '' }}>Part Time</option>
                                                <option value="temporary" {{ old('employment_type', isset($editModeData) ? $editModeData->employment_type : '') == 'temporary' ? 'selected' : '' }}>Temporary</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Work Location</label>
                                        <div class="col-md-8">
                                            <input type="text" name="work_location" class="form-control"
                                                value="{{ old('work_location', isset($editModeData) ? $editModeData->work_location : '') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Proposed Start Date</label>
                                        <div class="col-md-8">
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                <input type="text" name="proposed_start_date" class="form-control datepicker"
                                                    value="{{ old('proposed_start_date', isset($editModeData) && $editModeData->proposed_start_date ? dateConvertDBtoForm($editModeData->proposed_start_date) : '') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 2. REASON FOR REQUISITION --}}
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="box-title text-info"><i class="fa fa-question-circle"></i> 2. REASON FOR REQUISITION</h4>
                                    <hr class="m-t-0 m-b-20">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Requisition Type <span class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <div class="radio-list">
                                                <label class="radio-inline">
                                                    <input type="radio" name="requisition_type" value="new_position"
                                                        {{ old('requisition_type', isset($editModeData) ? $editModeData->requisition_type : 'new_position') == 'new_position' ? 'checked' : '' }}> New Position
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="requisition_type" value="replacement"
                                                        {{ old('requisition_type', isset($editModeData) ? $editModeData->requisition_type : '') == 'replacement' ? 'checked' : '' }}> Replacement
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="replacement_fields" style="display: {{ old('requisition_type', isset($editModeData) ? $editModeData->requisition_type : 'new_position') == 'replacement' ? 'block' : 'none' }};">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Employee Being Replaced</label>
                                            <div class="col-md-8">
                                                <input type="text" name="replaced_employee_name" class="form-control"
                                                    value="{{ old('replaced_employee_name', isset($editModeData) ? $editModeData->replaced_employee_name : '') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Reason for Replacement</label>
                                            <div class="col-md-8">
                                                <select name="replacement_reason" id="replacement_reason" class="form-control">
                                                    <option value="">-- Select --</option>
                                                    <option value="resignation" {{ old('replacement_reason', isset($editModeData) ? $editModeData->replacement_reason : '') == 'resignation' ? 'selected' : '' }}>Resignation</option>
                                                    <option value="termination" {{ old('replacement_reason', isset($editModeData) ? $editModeData->replacement_reason : '') == 'termination' ? 'selected' : '' }}>Termination</option>
                                                    <option value="transfer" {{ old('replacement_reason', isset($editModeData) ? $editModeData->replacement_reason : '') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                                    <option value="other" {{ old('replacement_reason', isset($editModeData) ? $editModeData->replacement_reason : '') == 'other' ? 'selected' : '' }}>Other</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="replacement_reason_other_row" style="display: {{ old('replacement_reason', isset($editModeData) ? $editModeData->replacement_reason : '') == 'other' ? 'block' : 'none' }};">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Other Reason</label>
                                            <div class="col-md-8">
                                                <input type="text" name="replacement_reason_other" class="form-control"
                                                    value="{{ old('replacement_reason_other', isset($editModeData) ? $editModeData->replacement_reason_other : '') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 3. JOB DETAILS --}}
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="box-title text-info"><i class="fa fa-tasks"></i> 3. JOB DETAILS</h4>
                                    <hr class="m-t-0 m-b-20">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Reporting To (Supervisor) <span class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <input type="text" name="reporting_manager" class="form-control required"
                                                value="{{ old('reporting_manager', isset($editModeData) ? $editModeData->reporting_manager : '') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Job Type <span class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <select name="job_type" class="form-control required">
                                                <option value="">-- Select --</option>
                                                @foreach([
                                                    'management' => 'Management',
                                                    'executive' => 'Executive',
                                                    'professional' => 'Professional',
                                                    'technical' => 'Technical',
                                                    'support' => 'Support',
                                                    'sales' => 'Sales',
                                                    'marketing' => 'Marketing',
                                                    'finance' => 'Finance',
                                                    'hr' => 'HR',
                                                    'it' => 'IT',
                                                ] as $__key => $__value)
                                                    <option value="{{ $__key }}" {{ old('job_type', isset($editModeData) ? $editModeData->job_type : '') == $__key ? 'selected' : '' }}>{{ $__value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label col-md-2">Key Responsibilities</label>
                                        <div class="col-md-10">
                                            <textarea name="key_responsibilities" class="form-control textarea_editor" rows="4">{{ old('key_responsibilities', isset($editModeData) ? $editModeData->key_responsibilities : '') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label col-md-2">Job Description <span class="validateRq">*</span></label>
                                        <div class="col-md-10">
                                            <textarea name="job_description" class="form-control textarea_editor" rows="4">{{ old('job_description', isset($editModeData) ? $editModeData->job_description : '') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 4. REQUIREMENTS --}}
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="box-title text-info"><i class="fa fa-graduation-cap"></i> 4. REQUIREMENTS</h4>
                                    <hr class="m-t-0 m-b-20">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Minimum Qualifications</label>
                                        <div class="col-md-8">
                                            <textarea name="minimum_qualifications" class="form-control" rows="3">{{ old('minimum_qualifications', isset($editModeData) ? $editModeData->minimum_qualifications : '') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Experience Required</label>
                                        <div class="col-md-8">
                                            <input type="text" name="experience_required" class="form-control"
                                                value="{{ old('experience_required', isset($editModeData) ? $editModeData->experience_required : '') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label col-md-2">Skills & Competencies</label>
                                        <div class="col-md-10">
                                            <textarea name="skills_competencies" class="form-control textarea_editor" rows="3">{{ old('skills_competencies', isset($editModeData) ? $editModeData->skills_competencies : '') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label col-md-2">Job Requirements <span class="validateRq">*</span></label>
                                        <div class="col-md-10">
                                            <textarea name="job_requirements" class="form-control textarea_editor" rows="4">{{ old('job_requirements', isset($editModeData) ? $editModeData->job_requirements : '') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 5. COMPENSATION DETAILS --}}
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="box-title text-info"><i class="fa fa-money"></i> 5. COMPENSATION DETAILS</h4>
                                    <hr class="m-t-0 m-b-20">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Proposed Salary Range</label>
                                        <div class="col-md-8">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <input type="number" name="minimum_salary" id="minimum_salary" class="form-control" placeholder="Min"
                                                        value="{{ old('minimum_salary', isset($editModeData) ? $editModeData->minimum_salary : '') }}">
                                                </div>
                                                <div class="col-md-2 text-center" style="padding-top: 6px;">to</div>
                                                <div class="col-md-5">
                                                    <input type="number" name="maximum_salary" id="maximum_salary" class="form-control" placeholder="Max"
                                                        value="{{ old('maximum_salary', isset($editModeData) ? $editModeData->maximum_salary : '') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Currency <span class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <select name="currency" class="form-control">
                                                <option value="KES" {{ old('currency', isset($editModeData) ? $editModeData->currency : 'KES') == 'KES' ? 'selected' : '' }}>KES - Kenyan Shilling</option>
                                                <option value="USD" {{ old('currency', isset($editModeData) ? $editModeData->currency : '') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                                <option value="EUR" {{ old('currency', isset($editModeData) ? $editModeData->currency : '') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                                <option value="GBP" {{ old('currency', isset($editModeData) ? $editModeData->currency : '') == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label col-md-2">Other Benefits (if any)</label>
                                        <div class="col-md-10">
                                            <textarea name="other_benefits" class="form-control" rows="2">{{ old('other_benefits', isset($editModeData) ? $editModeData->other_benefits : '') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 6. JUSTIFICATION FOR HIRE --}}
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="box-title text-info"><i class="fa fa-file-text-o"></i> 6. JUSTIFICATION FOR HIRE</h4>
                                    <hr class="m-t-0 m-b-20">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label col-md-2">Reason for Requisition <span class="validateRq">*</span></label>
                                        <div class="col-md-10">
                                            <textarea name="reason_for_requisition" class="form-control textarea_editor" rows="3">{{ old('reason_for_requisition', isset($editModeData) ? $editModeData->reason_for_requisition : '') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label col-md-2">Justification for Hire</label>
                                        <div class="col-md-10">
                                            <textarea name="justification_for_hire" class="form-control textarea_editor" rows="3" placeholder="Provide brief explanation on why this position is required">{{ old('justification_for_hire', isset($editModeData) ? $editModeData->justification_for_hire : '') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label col-md-2">Budget Justification</label>
                                        <div class="col-md-10">
                                            <textarea name="budget_justification" class="form-control textarea_editor" rows="3">{{ old('budget_justification', isset($editModeData) ? $editModeData->budget_justification : '') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- TIMING & SOURCE --}}
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="box-title text-info"><i class="fa fa-clock-o"></i> TIMING & RECRUITMENT SOURCE</h4>
                                    <hr class="m-t-0 m-b-20">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Required By Date <span class="validateRq">*</span></label>
                                        <div class="col-md-7">
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                <input type="text" name="required_by_date" class="form-control datepicker required"
                                                    value="{{ old('required_by_date', isset($editModeData) && $editModeData->required_by_date ? dateConvertDBtoForm($editModeData->required_by_date) : '') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Urgency Level <span class="validateRq">*</span></label>
                                        <div class="col-md-7">
                                            <select name="urgency_level" class="form-control required">
                                                <option value="low" {{ old('urgency_level', isset($editModeData) ? $editModeData->urgency_level : 'normal') == 'low' ? 'selected' : '' }}>Low</option>
                                                <option value="normal" {{ old('urgency_level', isset($editModeData) ? $editModeData->urgency_level : 'normal') == 'normal' ? 'selected' : '' }}>Normal</option>
                                                <option value="high" {{ old('urgency_level', isset($editModeData) ? $editModeData->urgency_level : '') == 'high' ? 'selected' : '' }}>High</option>
                                                <option value="critical" {{ old('urgency_level', isset($editModeData) ? $editModeData->urgency_level : '') == 'critical' ? 'selected' : '' }}>Critical</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Recruitment Source <span class="validateRq">*</span></label>
                                        <div class="col-md-7">
                                            <select name="recruitment_source" class="form-control required">
                                                <option value="internal" {{ old('recruitment_source', isset($editModeData) ? $editModeData->recruitment_source : '') == 'internal' ? 'selected' : '' }}>Internal</option>
                                                <option value="external" {{ old('recruitment_source', isset($editModeData) ? $editModeData->recruitment_source : '') == 'external' ? 'selected' : '' }}>External</option>
                                                <option value="both" {{ old('recruitment_source', isset($editModeData) ? $editModeData->recruitment_source : 'both') == 'both' ? 'selected' : '' }}>Both</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- LOCATION --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Branch / Location</label>
                                        <div class="col-md-8">
                                            <select name="location_id" id="location_id" class="form-control">
                                                <option value="">-- Select Location --</option>
                                                @foreach ($locations as $location)
                                                    <option value="{{ $location->location_id }}"
                                                        {{ (isset($editModeData) && $editModeData->location_id == $location->location_id) || old('location_id') == $location->location_id ? 'selected' : '' }}>
                                                        {{ $location->location_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    @if (isset($editModeData))
                                        <button type="submit" class="btn btn-info btn-lg">
                                            <i class="fa fa-pencil"></i> @lang('common.update')
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-info btn-lg">
                                            <i class="fa fa-check"></i> @lang('common.save')
                                        </button>
                                    @endif
                                    <a href="{{ route('jobRequisition.index') }}" class="btn btn-default btn-lg">
                                        @lang('common.cancel')
                                    </a>
                                </div>
                            </div>
                        </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page_scripts')
<link rel="stylesheet" href="{!! asset('admin_assets/plugins/bower_components/html5-editor/bootstrap-wysihtml5.css') !!}" />
<script src="{!! asset('admin_assets/js/cbpFWTabs.js') !!}"></script>
<script src="{!! asset('admin_assets/plugins/bower_components/html5-editor/wysihtml5-0.3.0.js') !!}"></script>
<script src="{!! asset('admin_assets/plugins/bower_components/html5-editor/bootstrap-wysihtml5.js') !!}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        // Ensure preloader is hidden
        $('.preloader').fadeOut();

        // Initialize wysihtml5 editor with error handling
        try {
            $('.textarea_editor').wysihtml5();
        } catch (e) {
            console.warn('wysihtml5 editor initialization failed:', e);
        }

        // Datepicker initialization
        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true
        });

        // Salary validation
        $('#minimum_salary, #maximum_salary').on('input', function() {
            var minSalary = parseFloat($('#minimum_salary').val()) || 0;
            var maxSalary = parseFloat($('#maximum_salary').val()) || 0;

            if (maxSalary > 0 && maxSalary < minSalary) {
                $('#maximum_salary').val(minSalary);
            }
        });

        // Requisition type toggle
        $('input[name="requisition_type"]').on('change', function() {
            if ($(this).val() === 'replacement') {
                $('#replacement_fields').slideDown();
            } else {
                $('#replacement_fields').slideUp();
                $('#replacement_reason_other_row').hide();
            }
        });

        // Replacement reason toggle
        $('#replacement_reason').on('change', function() {
            if ($(this).val() === 'other') {
                $('#replacement_reason_other_row').slideDown();
            } else {
                $('#replacement_reason_other_row').slideUp();
            }
        });
    });
</script>
@endsection
