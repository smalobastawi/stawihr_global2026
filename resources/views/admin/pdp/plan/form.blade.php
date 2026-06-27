@extends('admin.master')
@section('content')
@section('title')
{{ isset($editModeData) ? 'Edit' : 'Create' }} Personal Development Plan
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                @foreach (urlTree() as $item)
                    <li class="breadcrumb-item text-primary"><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
                @endforeach
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if(session()->has('error'))
                            <div class="alert alert-danger alert-dismissable"><strong>{{ session()->get('error') }}</strong></div>
                        @endif

                        @if(isset($editModeData))
                            <form action="{{ route('pdp.plan.update', $editModeData->pdp_plan_id) }}" method="POST">
                                @csrf
                                @method('PUT')
                        @else
                            <form action="{{ route('pdp.plan.store') }}" method="POST">
                                @csrf
                        @endif

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Employee<span class="validateRq">*</span></label>
                                        <select name="employee_id" id="employee_id" class="form-control required employee_id">
                                            <option value="">Select Employee</option>
                                            @foreach($employees as $emp)
                                                <option value="{{ $emp->employee_id }}" {{ old('employee_id', $editModeData->employee_id ?? '') == $emp->employee_id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Department</label>
                                        <select name="department_id" id="department_id" class="form-control">
                                            <option value="">Select Department</option>
                                            @foreach($departments as $dept)
                                                <option value="{{ $dept->department_id }}" {{ old('department_id', $editModeData->department_id ?? '') == $dept->department_id ? 'selected' : '' }}>{{ $dept->department_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Supervisor</label>
                                        <select name="supervisor_id" id="supervisor_id" class="form-control">
                                            <option value="">Select Supervisor</option>
                                            @foreach($employees as $emp)
                                                <option value="{{ $emp->employee_id }}" {{ old('supervisor_id', $editModeData->supervisor_id ?? '') == $emp->employee_id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Plan Title<span class="validateRq">*</span></label>
                                        <input type="text" name="plan_title" class="form-control required" value="{{ old('plan_title', $editModeData->plan_title ?? '') }}" placeholder="e.g. 2026 Leadership Development Plan">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Plan Year<span class="validateRq">*</span></label>
                                        <input type="number" name="plan_year" class="form-control required" value="{{ old('plan_year', $editModeData->plan_year ?? date('Y')) }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Start Date<span class="validateRq">*</span></label>
                                        <input type="date" name="start_date" class="form-control required" value="{{ old('start_date', isset($editModeData) && $editModeData->start_date ? $editModeData->start_date->format('Y-m-d') : '') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>End Date<span class="validateRq">*</span></label>
                                        <input type="date" name="end_date" class="form-control required" value="{{ old('end_date', isset($editModeData) && $editModeData->end_date ? $editModeData->end_date->format('Y-m-d') : '') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Review Frequency<span class="validateRq">*</span></label>
                                        @php $freq = old('review_frequency', $editModeData->review_frequency ?? $setting->default_review_frequency); @endphp
                                        <select name="review_frequency" class="form-control required">
                                            <option value="quarterly" {{ $freq == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                            <option value="bi_annually" {{ $freq == 'bi_annually' ? 'selected' : '' }}>Bi-Annually</option>
                                            <option value="annually" {{ $freq == 'annually' ? 'selected' : '' }}>Annually</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Development Focus</label>
                                        <textarea name="development_focus" class="form-control" rows="2" placeholder="Key areas of development for this period">{{ old('development_focus', $editModeData->development_focus ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Career Aspirations</label>
                                        <textarea name="career_aspirations" class="form-control" rows="2">{{ old('career_aspirations', $editModeData->career_aspirations ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            @if(isset($editModeData))
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Overall Summary</label>
                                        <textarea name="overall_summary" class="form-control" rows="3">{{ old('overall_summary', $editModeData->overall_summary ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Save Plan</button>
                            <a href="{{ route('pdp.plan.index') }}" class="btn btn-default">Cancel</a>
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
    $('#employee_id').change(function () {
        var employeeId = $(this).val();
        if (!employeeId) return;
        $.get("{{ route('pdp.plan.employeeDetails') }}", { employee_id: employeeId }, function (data) {
            $('#department_id').val(data.department_id);
            $('#supervisor_id').val(data.supervisor_id);
        });
    });
</script>
@endsection
