@extends('admin.master')
@section('content')
@section('title')
    @if (isset($editModeData))
        @lang('notice.edit_notice')
    @else
        @lang('notice.add_new_notice')
    @endif
@endsection

<style>
    .notice-form-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
    }
 
    .section-header h4 {
        margin: 0;
        font-weight: 600;
    }
    .target-audience-section {
        background: #f8f9fa;
        padding: 25px;
        border-radius: 8px;
        margin: 25px 0;
    }
    .form-group {
        margin-bottom: 20px;
    }
   
    .required-mark {
        color: #dc3545;
    }
    .checkbox-group {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 10px;
    }
    .checkbox-group label {
        display: flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
    }
    .checkbox-group input[type="checkbox"],
    .checkbox-group input[type="radio"] {
        margin-right: 5px;
    }
</style>

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
            <a href="{{ route('notice.index') }}" class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-list-ul" aria-hidden="true"></i> @lang('notice.view_notice')
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="notice-form-card">
                <div class="section-header">
                    <h4><i class="mdi mdi-clipboard-text fa-fw"></i> @yield('title')</h4>
                </div>

                <div class="panel-body" style="padding: 25px;">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            @foreach ($errors->all() as $error)
                                <strong>{!! $error !!}</strong><br>
                            @endforeach
                        </div>
                    @endif

                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;
                            <strong>{{ session()->get('success') }}</strong>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="alert alert-danger alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="glyphicon glyphicon-remove"></i>&nbsp;
                            <strong>{{ session()->get('error') }}</strong>
                        </div>
                    @endif

                    @if (isset($editModeData))
                        <form method="POST" action="{{ route('notice.update', $editModeData->notice_id) }}" class="form-horizontal" id="noticeForm" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                    @else
                        <form method="POST" action="{{ route('notice.store') }}" class="form-horizontal" id="noticeForm" enctype="multipart/form-data">
                            @csrf
                    @endif

                        <!-- Basic Notice Information -->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label col-md-4 form-label">
                                        @lang('notice.title')<span class="required-mark">*</span>
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" name="title" id="title" class="form-control required"
                                            value="{{ old('title', isset($editModeData) ? $editModeData->title : '') }}"
                                            placeholder="Enter notice title">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label col-md-4 form-label">
                                        @lang('notice.description')<span class="required-mark">*</span>
                                    </label>
                                    <div class="col-md-8">
                                        <textarea name="description" class="form-control textarea_editor" rows="6">{{ old('description', isset($editModeData) ? $editModeData->description : '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label col-md-4 form-label">
                                        @lang('common.status')<span class="required-mark">*</span>
                                    </label>
                                    <div class="col-md-8">
                                        <select name="status" class="form-control select2 required">
                                            <option value="Published" {{ old('status', isset($editModeData) ? $editModeData->status : '') == 'Published' ? 'selected' : '' }}>
                                                {{ __('recruitement.published') }}
                                            </option>
                                            <option value="Unpublished" {{ old('status', isset($editModeData) ? $editModeData->status : '') == 'Unpublished' ? 'selected' : '' }}>
                                                {{ __('recruitement.unpublished') }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label col-md-4 form-label">
                                        @lang('notice.publish_date')<span class="required-mark">*</span>
                                    </label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" name="publish_date" id="publish_date"
                                                class="form-control dateField"
                                                value="{{ isset($editModeData) ? dateConvertDBtoForm($editModeData->publish_date) : old('publish_date') }}"
                                                placeholder="DD/MM/YYYY" readonly="readonly">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label col-md-4 form-label">@lang('notice.attach_file')</label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-files-o"></i></span>
                                            <input type="file" name="attach_file" class="form-control">
                                        </div>
                                        @if (isset($editModeData) && $editModeData->attach_file)
                                            <small class="text-muted">
                                                Current file: <a href="{{ asset('uploads/notice/' . $editModeData->attach_file) }}" target="_blank">{{ $editModeData->attach_file }}</a>
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Target Audience Section -->
                        <div class="target-audience-section">
                            <h4 class="box-title" style="margin-top: 0; margin-bottom: 20px; color: #333;">
                                <i class="fa fa-users"></i> Targeted Audience
                            </h4>
                            <hr style="margin-bottom: 25px;">

                            <!-- Target Specific Employees -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">Target Specific Employees (Optional)</label>
                                        <select name="employees[]" id="employees" class="form-control select2" multiple style="width: 100%;">
                                            @foreach ($employees ?? [] as $employee)
                                                <option value="{{ $employee->employee_id }}"
                                                    @isset($editModeData)
                                                        @if ($editModeData->employees->contains('employee_id', $employee->employee_id)) selected @endif
                                                    @endisset>
                                                    {{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->employee_id }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">
                                            <i class="fa fa-info-circle"></i> If specific employees are selected, other targeting filters will be disabled.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Department Selection -->
                            <div class="row" id="department-row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">Target Departments</label>
                                        <select class="form-control select2 target-filter" id="departments"
                                            name="departments[]" multiple style="width: 100%;">
                                            @foreach ($departments ?? [] as $department)
                                                <option value="{{ $department->department_id }}"
                                                    @isset($editModeData)
                                                        @if ($editModeData->departments->contains('department_id', $department->department_id)) selected @endif
                                                    @endisset>
                                                    {{ $department->department_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Target Gender Selection -->
                            <div class="row" id="gender-row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">Target Gender</label>
                                        <div class="checkbox-group">
                                            @foreach (\App\Lib\Enumerations\Gender::toArray() as $key => $value)
                                                <label>
                                                    <input type="radio" name="target_gender"
                                                        value="{{ $value }}" class="target-filter"
                                                        @isset($editModeData)
                                                            @if ($editModeData->target_gender == $key) checked @endif
                                                        @else
                                                            @if ($key == \App\Lib\Enumerations\Gender::ALL) checked @endif
                                                        @endisset>
                                                    {{ $value }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Target Regions -->
                            <div class="row" id="region-row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">Target Regions</label>
                                        <select name="regions[]" id="regions" class="form-control select2 target-filter"
                                            multiple style="width: 100%;">
                                            @foreach ($regions ?? [] as $region)
                                                <option value="{{ $region->id }}"
                                                    @isset($editModeData)
                                                        @if ($editModeData->regions->contains('id', $region->id)) selected @endif
                                                    @endisset>
                                                    {{ $region->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Target Locations -->
                            <div class="row" id="location-row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">Target Locations</label>
                                        <select name="locations[]" id="locations"
                                            class="form-control select2 target-filter" multiple style="width: 100%;">
                                            @isset($editModeData)
                                                @foreach ($editModeData->getAllLocationsAttribute() as $location)
                                                    <option value="{{ $location->location_id }}" selected>
                                                        {{ $location->location_name }}
                                                    </option>
                                                @endforeach
                                            @else
                                                @foreach ($locations ?? [] as $location)
                                                    <option value="{{ $location->location_id }}">
                                                        {{ $location->location_name }}
                                                    </option>
                                                @endforeach
                                            @endisset
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notification Options -->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label col-md-4 form-label">Send Notification</label>
                                    <div class="col-md-8">
                                        <label>
                                            <input type="checkbox" name="send_notification" value="1"
                                                {{ old('send_notification') ? 'checked' : '' }}>
                                            Send email notification to all users
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label col-md-4 form-label">Send SMS</label>
                                    <div class="col-md-8">
                                        <label>
                                            <input type="checkbox" name="send_sms" value="1"
                                                {{ old('send_sms') ? 'checked' : '' }}>
                                            Send SMS notification to target staff
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="form-actions" style="margin-top: 30px;">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="col-md-offset-4 col-md-8">
                                        @if (isset($editModeData))
                                            <button type="submit" class="btn btn-info btn_style">
                                                <i class="fa fa-pencil"></i> @lang('common.update')
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-info btn_style">
                                                <i class="fa fa-check"></i> @lang('common.save')
                                            </button>
                                        @endif
                                        <a href="{{ route('notice.index') }}" class="btn btn-default" style="margin-left: 10px;">
                                            Cancel
                                        </a>
                                    </div>
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
<link rel="stylesheet" href="{!! asset('admin_assets/plugins/bower_components/html5-editor/bootstrap-wysihtml5.css') !!}" />
<script src="{!! asset('admin_assets/js/cbpFWTabs.js') !!}"></script>
<script src="{!! asset('admin_assets/plugins/bower_components/html5-editor/wysihtml5-0.3.0.js') !!}"></script>
<script src="{!! asset('admin_assets/plugins/bower_components/html5-editor/bootstrap-wysihtml5.js') !!}"></script>

<style>
    .select2-container {
        width: 100% !important;
    }
    .select2-container--default .select2-selection--multiple {
        min-height: 38px;
        border: 1px solid #ced4da;
    }
</style>

<script type="text/javascript">
    (function() {
        $('.textarea_editor').wysihtml5();

        // Initialize select2
        $('#departments').select2({
            placeholder: "Select departments",
            allowClear: true,
            width: '100%'
        });

        $('#locations').select2({
            placeholder: "Select locations",
            allowClear: true,
            width: '100%'
        });

        $('#regions').select2({
            placeholder: "Select regions",
            allowClear: true,
            width: '100%'
        });

        $('#employees').select2({
            placeholder: "Select specific employees (optional)",
            allowClear: true,
            width: '100%'
        });

        // Function to toggle targeting filters based on employee selection
        function toggleTargetingFilters() {
            var selectedEmployees = $('#employees').val();
            var hasEmployees = selectedEmployees && selectedEmployees.length > 0;

            var $departments = $('#departments');
            var $regions = $('#regions');
            var $locations = $('#locations');
            var $genderRadios = $('input[name="target_gender"]');

            var $departmentRow = $('#department-row');
            var $genderRow = $('#gender-row');
            var $regionRow = $('#region-row');
            var $locationRow = $('#location-row');

            if (hasEmployees) {
                $departments.prop('disabled', true).trigger('change');
                $regions.prop('disabled', true).trigger('change');
                $locations.prop('disabled', true).trigger('change');
                $genderRadios.prop('disabled', true);

                $departmentRow.css('opacity', '0.5');
                $genderRow.css('opacity', '0.5');
                $regionRow.css('opacity', '0.5');
                $locationRow.css('opacity', '0.5');
            } else {
                $departments.prop('disabled', false).trigger('change');
                $regions.prop('disabled', false).trigger('change');
                $locations.prop('disabled', false).trigger('change');
                $genderRadios.prop('disabled', false);

                $departmentRow.css('opacity', '1');
                $genderRow.css('opacity', '1');
                $regionRow.css('opacity', '1');
                $locationRow.css('opacity', '1');
            }
        }

        $('#employees').on('change', function() {
            toggleTargetingFilters();
        });

        toggleTargetingFilters();

        // Function to load locations
        function loadLocations(regionIds) {
            var locationsSelect = $('#locations');
            locationsSelect.empty();

            if (regionIds && regionIds.length > 0) {
                locationsSelect.prop('disabled', true);

                $.ajax({
                    url: '{{ route('survey.getLocationsByRegions') }}',
                    type: 'POST',
                    data: {
                        region_ids: regionIds,
                        _token: '{{ csrf_token() }}',
                        @isset($editModeData)
                            survey_id: {{ $editModeData->notice_id }},
                        @endisset
                    },
                    success: function(response) {
                        $.each(response.locations, function(key, location) {
                            var option = $('<option></option>')
                                .attr('value', location.location_id)
                                .text(location.location_name);

                            @isset($editModeData)
                                if (response.selected_locations.includes(location.location_id)) {
                                    option.attr('selected', 'selected');
                                }
                            @endisset

                            locationsSelect.append(option);
                        });

                        locationsSelect.prop('disabled', false).trigger('change');
                    },
                    error: function() {
                        locationsSelect.prop('disabled', false);
                        alert('Failed to load locations. Please try again.');
                    }
                });
            } else {
                locationsSelect.prop('disabled', false);
            }
        }

        $('#regions').on('change', function() {
            loadLocations($(this).val());
        });

        @isset($editModeData)
            @if ($editModeData->regions->isNotEmpty())
                loadLocations($('#regions').val());
            @endif
        @endisset
    })();
</script>
@endsection
