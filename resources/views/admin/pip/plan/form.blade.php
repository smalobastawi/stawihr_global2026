@extends('admin.master')
@section('content')
@section('title')
{{ isset($editModeData) ? 'Edit' : 'Create' }} Performance Improvement Plan
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
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                                <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif

                        @if(isset($editModeData))
                            <form action="{{ route('pip.plan.update', $editModeData->pip_id) }}" method="POST" id="pipForm">
                                @csrf
                                @method('PUT')
                        @else
                            <form action="{{ route('pip.plan.store') }}" method="POST" id="pipForm">
                                @csrf
                        @endif

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="employee_id">Employee<span class="validateRq">*</span></label>
                                        <select name="employee_id" id="employee_id" class="form-control required employee_id">
                                            <option value="">Select Employee</option>
                                            @foreach($employees->pluck('full_name', 'employee_id') as $id => $name)
                                                <option value="{{ $id }}" {{ (isset($preselectedEmployee) ? $preselectedEmployee->employee_id : old('employee_id')) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="designation_id">Designation</label>
                                        <input type="text" name="designation_name" id="designation_name" class="form-control designation_name" placeholder="Designation" readonly>
                                        <input type="hidden" name="designation_id" id="designation_id">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="department_id">Department</label>
                                        <select name="department_id" id="department_id" class="form-control department_id">
                                            <option value="">Select Department</option>
                                            @foreach($departments->pluck('department_name', 'department_id') as $id => $name)
                                                <option value="{{ $id }}" {{ old('department_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="appraisal_id">Linked Appraisal</label>
                                        <select name="appraisal_id" id="appraisal_id" class="form-control appraisal_id">
                                            <option value="">Select Appraisal</option>
                                            @foreach($appraisals as $a)
                                                <option value="{{ $a->appraisal_id }}" {{ (isset($preselectedAppraisal) ? $preselectedAppraisal->appraisal_id : old('appraisal_id')) == $a->appraisal_id ? 'selected' : '' }}>{{ ($a->employee ? $a->employee->full_name : 'Unknown') . ' - ' . $a->review_period }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="supervisor_id">Supervisor</label>
                                        <select name="supervisor_id" id="supervisor_id" class="form-control supervisor_id">
                                            <option value="">Select Supervisor</option>
                                            @foreach($employees->pluck('full_name', 'employee_id') as $id => $name)
                                                <option value="{{ $id }}" {{ old('supervisor_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="hr_manager_id">HR Manager</label>
                                        <select name="hr_manager_id" id="hr_manager_id" class="form-control hr_manager_id">
                                            <option value="">Select HR</option>
                                            @foreach($employees->pluck('full_name', 'employee_id') as $id => $name)
                                                <option value="{{ $id }}" {{ old('hr_manager_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="plan_period_start">Start Date<span class="validateRq">*</span></label>
                                        <input type="date" name="plan_period_start" id="plan_period_start" class="form-control required plan_period_start" value="{{ old('plan_period_start') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="plan_period_end">End Date<span class="validateRq">*</span></label>
                                        <input type="date" name="plan_period_end" id="plan_period_end" class="form-control required plan_period_end" value="{{ old('plan_period_end') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="trigger_type">Trigger Type<span class="validateRq">*</span></label>
                                        <select name="trigger_type" id="trigger_type" class="form-control required trigger_type">
                                            <option value="automatic" {{ old('trigger_type', 'automatic') == 'automatic' ? 'selected' : '' }}>Automatic</option>
                                            <option value="manual_supervisor" {{ old('trigger_type', 'automatic') == 'manual_supervisor' ? 'selected' : '' }}>Manual (Supervisor)</option>
                                            <option value="manual_hr" {{ old('trigger_type', 'automatic') == 'manual_hr' ? 'selected' : '' }}>Manual (HR)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="trigger_score">Trigger Score</label>
                                        <input type="number" name="trigger_score" id="trigger_score" class="form-control trigger_score" step="0.01" min="0" max="100" placeholder="e.g. 65.5" value="{{ old('trigger_score') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="purpose">Purpose / Reason<span class="validateRq">*</span></label>
                                        <textarea name="purpose" id="purpose" class="form-control required purpose" rows="3" placeholder="Describe the reason for this PIP">{{ old('purpose') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            @if(isset($lowScores) && count($lowScores) > 0)
                            <hr>
                            <h4>Auto-detected Concerns from Appraisal</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="tr_header">
                                            <th>Focus Area</th>
                                            <th>Goal</th>
                                            <th>Self Score</th>
                                            <th>Review Score</th>
                                            <th>Select</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lowScores as $score)
                                            <tr>
                                                <td>{{ $score->goal && $score->goal->focusArea ? $score->goal->focusArea->focus_area_name : '' }}</td>
                                                <td>{{ $score->goal ? $score->goal->performance_metric : '' }}</td>
                                                <td>{{ $score->self_weighting }}</td>
                                                <td>{{ $score->review_weighting }}</td>
                                                <td>
                                                    <input type="checkbox" name="concerns[{{ $loop->index }}][selected]" value="1" checked>
                                                    <input type="hidden" name="concerns[{{ $loop->index }}][goal_id]" value="{{ $score->goal_id }}">
                                                    <input type="hidden" name="concerns[{{ $loop->index }}][appraisal_score_id]" value="{{ $score->score_id }}">
                                                    <input type="hidden" name="concerns[{{ $loop->index }}][actual_score]" value="{{ $score->review_weighting }}">
                                                    <input type="hidden" name="concerns[{{ $loop->index }}][description]" value="Low score on {{ $score->goal ? $score->goal->performance_metric : 'Goal' }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif

                            <hr>
                            <h4>Initial Improvement Goals</h4>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Objective</label>
                                        <input type="text" name="goals[0][objective]" class="form-control" placeholder="What needs to improve">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Action Required</label>
                                        <input type="text" name="goals[0][action_required]" class="form-control" placeholder="Steps to take">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Target KPI</label>
                                        <input type="text" name="goals[0][target_kpi]" class="form-control" placeholder="e.g. >= 90%">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Deadline</label>
                                        <input type="date" name="goals[0][deadline]" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-info btn_style"><i class="fa fa-check"></i> Save</button>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ route('pip.plan.index') }}" class="btn btn-info btn_style pull-right"><i class="fa fa-times"></i> Cancel</a>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    var employeeSelect = document.getElementById('employee_id');
    var designationName = document.getElementById('designation_name');
    var designationId = document.getElementById('designation_id');
    var departmentSelect = document.getElementById('department_id');
    var supervisorSelect = document.getElementById('supervisor_id');

    function fetchEmployeeDetails(employeeId) {
        if (!employeeId) {
            designationName.value = '';
            designationId.value = '';
            departmentSelect.value = '';
            supervisorSelect.value = '';
            return;
        }

        fetch('{{ route("pip.plan.employeeDetails") }}?employee_id=' + encodeURIComponent(employeeId), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function (response) { return response.json(); })
        .then(function (data) {
            if (data) {
                designationName.value = data.designation_name || '';
                designationId.value = data.designation_id || '';
                if (data.department_id) {
                    departmentSelect.value = data.department_id;
                }
                if (data.supervisor_id) {
                    supervisorSelect.value = data.supervisor_id;
                }
            }
        })
        .catch(function (error) {
            console.error('Error fetching employee details:', error);
        });
    }

    employeeSelect.addEventListener('change', function () {
        fetchEmployeeDetails(this.value);
    });

    if (employeeSelect.value) {
        fetchEmployeeDetails(employeeSelect.value);
    }
});
</script>
@endsection
