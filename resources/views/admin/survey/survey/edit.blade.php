@extends('admin.master')
@php
    if (isset($data)) {
        $title = $data->title . ' Update Survey';
    } else {
        $title = 'Create New Survey';
    }

@endphp
@section('title', $title)
@section('content')

    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor">
                        <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                    </li>
                    <li>@yield('title')</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @if (session()->has('error'))
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <i
                                        class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                                </div>
                            @endif
                            <!-- Display validation errors -->
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @isset($data)
                                <form method="POST" action="{{ route('survey.forms.update', $data->id) }}"
                                    class="form-horizontal">
                                    @method('put')
                                @else
                                    <form method="POST" action="{{ route('survey.forms.create') }}" class="form-horizontal">
                                    @endisset

                                    @csrf
                                    <div class="row">
                                        <div class="col-12 col-md-12">
                                            <div class="form-group">
                                                <label for="title">Survey Title </label>
                                                <input type="text" name="title" class="form-control" required
                                                    value="@isset($data) {{ $data->title }}@endisset"
                                                    placeholder="Enter survey title (e.g., Employee Satisfaction Survey 2023)">
                                                <small class="text-muted">This will be the title of your Google Form</small>

                                            </div>
                                        </div>
                                    </div>

                                    <!-- Department Selection -->
                                    <div class="row">
                                        <div class="col-12 col-md-12">
                                            <div class="form-group">
                                                <label for="departments">Target Departments</label>
                                                <select class="form-control select2" id="departments" name="departments[]"
                                                    multiple required style="width: 100%;">
                                                    @foreach ($departments as $department)
                                                        <option value="{{ $department->department_id }}"
                                                            @isset($data) @if ($data->departments->contains('department_id', $department->department_id)) selected @endif @endisset>
                                                            {{ $department->department_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Target Gender Selection -->
                                    <div class="row">
                                        <div class="col-12 col-md-12">
                                            <div class="form-group">
                                                <label>Target Gender</label>
                                                @foreach (Gender::toArray() as $key => $value)
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="target_gender"
                                                            id="targetGender{{ $key }}" value="{{ $key }}"
                                                            @isset($data) @if ($data->target_gender == $key) checked @endif @endisset>
                                                        <label class="form-check-label"
                                                            for="targetGender{{ $key }}">
                                                            {{ $value }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>

                                        </div>
                                    </div>

                                    <!-- Target Regions -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Target Regions</label>
                                                <select name="regions[]" id="regions" class="form-control select2"
                                                    multiple>
                                                    @foreach ($regions as $region)
                                                        <option value="{{ $region->id }}"
                                                            @isset($data) @if ($data->regions->contains('id', $region->id)) selected @endif @endisset>
                                                            {{ $region->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Target Locations -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Target Locations</label>
                                                <select name="locations[]" id="locations" class="form-control select2"
                                                    multiple>
                                                    @isset($data)
                                                        @foreach ($data->getAllBranchesAttribute() as $location)
                                                            <option value="{{ $location->location_id }}" selected>
                                                                {{ $location->location_name }}
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        @foreach ($locations as $location)
                                                            <option value="{{ $location->location_id }}">
                                                                {{ $location->location_name }}
                                                            </option>
                                                        @endforeach
                                                    @endisset
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <button type="submit"
                                                        class="btn btn-success waves-effect waves-light m-r-10">
                                                        @isset($data)
                                                            <i class="fa fa-plus-circle"></i> Update Survey
                                                        @else
                                                            <i class="fa fa-plus-circle"></i> Create Survey
                                                        @endisset

                                                    </button>
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
    </div>

@endsection

@section('page_scripts')
    <style>
        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--multiple {
            min-height: 38px;
            border: 1px solid #ced4da;
        }
    </style>

    <script>
        $(document).ready(function() {
            $('#departments').select2({
                placeholder: "Select departments",
                allowClear: true,
                dropdownAutoWidth: true,
                width: '100%',
            });

            $('#locations').select2({
                placeholder: "Select locations",
                allowClear: true,
                dropdownAutoWidth: true,
                width: '100%',
            });

            $('#regions').select2({
                placeholder: "Select regions",
                allowClear: true,
                dropdownAutoWidth: true,
                width: '100%',
            });

            // Function to load branche
            // Function to load locations
            function loadBranches(regionIds) {
                var branchesSelect = $('#locations');

                // Clear current options
                branchesSelect.empty();

                if (regionIds && regionIds.length > 0) {
                    // Show loading state
                    branchesSelect.prop('disabled', true);

                    // Fetch locations for selected regions
                    $.ajax({
                        url: '{{ route('survey.getBranchesByRegions') }}',
                        type: 'POST',
                        data: {
                            region_ids: regionIds,
                            _token: '{{ csrf_token() }}',
                            @isset($data)
                                survey_id: {{ $data->id }},
                            @endisset
                        },
                        success: function(response) {
                            // Add new options
                            $.each(response.locations, function(key, location) {
                                var option = $('<option></option>')
                                    .attr('value', location.location_id)
                                    .text(location.location_name);

                                // Mark as selected if this is an edit and location should be selected
                                @isset($data)
                                    if (response.selected_branches.includes(location
                                        .location_id)) {
                                        option.attr('selected', 'selected');
                                    }
                                @endisset

                                branchesSelect.append(option);
                            });

                            // Refresh select2 and enable the dropdown
                            branchesSelect.prop('disabled', false).trigger('change');
                        },
                        error: function() {
                            branchesSelect.prop('disabled', false);
                            alert('Failed to load locations. Please try again.');
                        }
                    });
                } else {
                    // If no regions selected, ensure dropdown is enabled
                    branchesSelect.prop('disabled', false);
                }
            }

            // When regions change (works for both create and edit)
            $('#regions').on('change', function() {
                loadBranches($(this).val());
            });

            // On page load for edit case
            @isset($data)
                @if ($data->regions->isNotEmpty())
                    // Initial load with selected regions
                    loadBranches($('#regions').val());
                @endif
            @endisset

        });
    </script>

@endsection
