@extends('admin.master')
@section('content')

@section('title')
    @if (isset($editModeData))
        Edit Case
    @else
        Add Case
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
            <a href="{{ route('disciplinary.cases.index') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                    class="fa fa-list-ul" aria-hidden="true"></i> Back to List</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-offset-2 col-md-6">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                    @foreach ($errors->all() as $error)
                        <strong>{!! $error !!}</strong><br>
                    @endforeach
                </div>
            @endif
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                </div>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">

                        @php
                            $isEdit = isset($editModeData);
                            $formAction = $isEdit
                                ? route('disciplinary.cases.update', $editModeData->id)
                                : route('disciplinary.cases.store');

                            $caseNumber = old('case_number', $editModeData->case_number ?? '');
                            $locationId = (string) old('location_id', $editModeData->location_id ?? '');
                            $categoryId = (string) old('category_id', $editModeData->category_id ?? '');
                            $address = old('location', $editModeData->location ?? '');
                            $employeeId = (string) old('employee_id', $editModeData->employee_id ?? '');
                            $reporterId = (string) old('reporter_id', $editModeData->reporter_id ?? '');
                            $statusValue = (string) old('status', $editModeData->status ?? '');
                            $dateOfIncident = old('date_of_incident', $editModeData->date_of_incident ?? '');
                            $dateOfReport = old('date_of_report', $editModeData->date_of_report ?? '');
                            $assignedOfficer = (string) old('assigned_officer', $editModeData->assigned_officer ?? '');
                            $description = old('description', $editModeData->description ?? '');
                        @endphp

                        <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" id="branchForm"
                            class="form-horizontal">
                            @csrf
                            @if ($isEdit)
                                @method('PUT')
                            @endif


                        <div class="row">
                            <!-- First Column -->
                            <div class="col-md-4">
                                <div class="">
                                    <label class="control-label">Case Number (Leave empty to autogenerate)</label>
                                    <input type="text" name="case_number" class="form-control"
                                        value="{{ $caseNumber }}">
                                </div>

                            </div>
                            <div class="col-md-4">
                                <div class="">
                                    <label class="control-label">Location<span class="validateRq">*</span></label>
                                    <select name="location_id" class="form-control select2" required>
                                        <option value="">Select Location</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->location_id }}"
                                                {{ $locationId === (string) $location->location_id ? 'selected' : '' }}>
                                                {{ $location->location_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <div class="col-md-4">
                                <div class="">
                                    <label class="control-label">Category<span class="validateRq">*</span></label>
                                    <select name="category_id" class="form-control select2" required>
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ $categoryId === (string) $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="">
                                    <label class="control-label">Location/Address</label>
                                    <input type="text" name="location" class="form-control"
                                        value="{{ $address }}">
                                </div>
                            </div>
                            <!-- Second Column -->
                            <div class="col-md-4">
                                <div class="">
                                    <label class="control-label">Subject Employee<span
                                            class="validateRq">*</span></label>
                                    <select name="employee_id" class="form-control select2" required>
                                        <option value="">Select Employee</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->employee_id }}"
                                                {{ $employeeId === (string) $employee->employee_id ? 'selected' : '' }}>
                                                {{ $employee->first_name . ' ' . $employee->middle_name . ' ' . $employee->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="">
                                    <label class="control-label">Reporter</label>
                                    <select name="reporter_id" class="form-control select2">
                                        <option value="">Select Reporter</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->employee_id }}"
                                                {{ $reporterId === (string) $employee->employee_id ? 'selected' : '' }}>
                                                {{ $employee->first_name . ' ' . $employee->middle_name . ' ' . $employee->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="">
                                    <label class="control-label">Status<span class="validateRq">*</span></label>
                                    <select name="status" class="form-control select2" required>
                                        <option value="">Select Status</option>
                                        @foreach (\DisciplinaryCaseStatus::toArray() as $key => $value)
                                            <option value="{{ $value }}"
                                                {{ $statusValue === (string) $value ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="">
                                    <label class="control-label">Date of Incident<span
                                            class="validateRq">*</span></label>
                                    <input type="date" name="date_of_incident" class="form-control"
                                        value="{{ $dateOfIncident }}"
                                        required>
                                </div>

                            </div>
                            <div class="col-md-4">
                                <div class="">
                                    <label class="control-label">Date of Report</label>
                                    <input required type="date" name="date_of_report" class="form-control"
                                        value="{{ $dateOfReport }}">
                                </div>
                            </div>

                            <!-- Third Column -->
                            <div class="col-md-4">
                                <div class="">
                                    <label class="control-label">Assigned Officer</label>
                                    <select name="assigned_officer" class="form-control select2">
                                        <option value="">Select Assigned Officer</option>
                                        @foreach ($caseOfficers as $employee)
                                            <option value="{{ $employee->employee_id }}"
                                                {{ $assignedOfficer === (string) $employee->employee_id ? 'selected' : '' }}>
                                                {{ $employee->first_name . ' ' . $employee->middle_name . ' ' . $employee->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="">
                                    <label class="control-label">Description</label>
                                    <textarea name="description" class="form-control" rows="4">{{ $description }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="">
                                    <label class="control-label">Attachment</label>
                                    <input type="file" name="attachment" class="form-control">
                                </div>
                            </div>
                        </div>


                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    @if (isset($editModeData))
                                        <button type="submit" class="btn btn-info"><i class="fa fa-pencil"></i>
                                            Update</button>
                                    @else
                                        <button type="submit" class="btn btn-info"><i class="fa fa-check"></i>
                                            Save</button>
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
</div>
@endsection
@section('page_scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
@endsection

