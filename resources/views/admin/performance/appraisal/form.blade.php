@extends('admin.master')
@section('content')
@section('title')
{{ isset($editModeData) ? 'Edit' : 'Add' }} Performance Appraisal
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
                        @if(session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                                <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if(session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                                <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif

                        @if(isset($editModeData))
                            <form action="{{ route('performance.appraisal.update', $editModeData->appraisal_id) }}" method="POST" id="appraisalForm">
                                @csrf
                                @method('PUT')
                        @else
                            <form action="{{ route('performance.appraisal.store') }}" method="POST" id="appraisalForm">
                                @csrf
                        @endif

                        <div class="form-body">
                            @if(!isset($editModeData))
                            <!-- Duplicate Check Warning -->
                            <div id="duplicate_warning" class="alert alert-danger" style="display: none;">
                                <i class="fa fa-exclamation-triangle"></i> <strong>Duplicate Appraisal:</strong> 
                                <span id="duplicate_message"></span>
                            </div>
                            @endif

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="review_period_id">Review Period<span class="validateRq">*</span></label>
                                        <select name="review_period_id" id="review_period_id" class="form-control required review_period_id" required>
                                            <option value="">Select Review Period</option>
                                            @foreach($reviewPeriods as $period)
                                                <option value="{{ $period->period_id }}" 
                                                        data-start-date="{{ $period->start_date->format('Y-m-d') }}" 
                                                        data-end-date="{{ $period->end_date->format('Y-m-d') }}"
                                                        data-period-name="{{ $period->period_name }}"
                                                        {{ old('review_period_id') == $period->period_id ? 'selected' : '' }}>
                                                    {{ $period->period_name }} ({{ $period->start_date->format('M d, Y') }} - {{ $period->end_date->format('M d, Y') }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Dates are auto-populated from the selected period</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" style="margin-top: 25px;">
                                        <a href="{{ route('performance.reviewPeriod.index') }}" class="btn btn-default btn-sm" target="_blank">
                                            <i class="fa fa-cog"></i> Manage Review Periods
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="review_start_date_display">Review Start Date</label>
                                        <input type="text" id="review_start_date_display" class="form-control" readonly placeholder="Auto-populated from period">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="review_end_date_display">Review End Date</label>
                                        <input type="text" id="review_end_date_display" class="form-control" readonly placeholder="Auto-populated from period">
                                    </div>
                                </div>
                            </div>

                            @if(!isset($editModeData))
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="employee_ids">Select Employees<span class="validateRq">*</span></label>
                                        <select name="employee_ids[]" id="employee_ids" class="form-control" multiple size="8" required>
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee->employee_id }}" 
                                                        data-supervisor-id="{{ $employee->supervisor_id }}"
                                                        data-department="{{ $employee->department_id }}"
                                                        {{ in_array($employee->employee_id, old('employee_ids', [])) ? 'selected' : '' }}>
                                                    {{ $employee->full_name }} 
                                                    @if($employee->department) ({{ $employee->department->department_name }}) @endif
                                                    @if($employee->supervisor) [Supervisor: {{ $employee->supervisor->full_name }}] @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Hold Ctrl/Cmd to select multiple employees. Supervisor will be auto-filled from employee profile.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> <strong>Bulk Creation:</strong> Selecting multiple employees will create separate appraisals for each employee with the same review period. Each appraisal will use the employee's assigned supervisor from their profile.
                            </div>

                            <hr>
                            <h4>Goals & Behavioral Items to be Evaluated</h4>
                            
                            <!-- Department Filter -->
                            <div class="row" style="margin-bottom: 15px;">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="department_filter">Filter by Department:</label>
                                        <select id="department_filter" class="form-control">
                                            <option value="">All Departments</option>
                                            @foreach($departments as $dept)
                                                <option value="{{ $dept->department_id }}">{{ $dept->department_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="alert alert-warning" style="margin-top: 25px; padding: 10px;">
                                        <i class="fa fa-filter"></i> <strong>Note:</strong> Select a department to see department-specific goals. Goals marked as "All Departments" will appear for all filters.
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="goals_table">
                                    <thead>
                                        <tr class="tr_header">
                                            <th>Department</th>
                                            <th>Focus Area</th>
                                            <th>Weight</th>
                                            <th>Goal / Metric</th>
                                            <th>Itemized Weight</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($focusAreas as $focusArea)
                                            @php
                                                $focusAreaDepts = $focusArea->department_id ? [$focusArea->department_id] : [];
                                                $deptClass = $focusArea->department_id ? 'dept-' . $focusArea->department_id : 'all-depts';
                                            @endphp
                                            @if($focusArea->goals->count() > 0)
                                                @foreach($focusArea->goals as $goal)
                                                    <tr class="goal-row {{ $deptClass }}" data-departments="{{ json_encode($focusAreaDepts) }}">
                                                        @if($loop->first)
                                                            <td rowspan="{{ $focusArea->goals->count() }}" class="dept-cell">
                                                                @if($focusArea->department)
                                                                    <span class="label label-info">{{ $focusArea->department->department_name }}</span>
                                                                @else
                                                                    <span class="label label-default">All Departments</span>
                                                                @endif
                                                            </td>
                                                            <td rowspan="{{ $focusArea->goals->count() }}"><strong>{{ $focusArea->focus_area_name }}</strong></td>
                                                            <td rowspan="{{ $focusArea->goals->count() }}">{{ $focusArea->weight }}%</td>
                                                        @endif
                                                        <td>{{ $goal->performance_metric }}</td>
                                                        <td>{{ $goal->itemized_weighting }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr class="goal-row {{ $deptClass }}" data-departments="{{ json_encode($focusAreaDepts) }}">
                                                    <td class="dept-cell">
                                                        @if($focusArea->department)
                                                            <span class="label label-info">{{ $focusArea->department->department_name }}</span>
                                                        @else
                                                            <span class="label label-default">All Departments</span>
                                                        @endif
                                                    </td>
                                                    <td><strong>{{ $focusArea->focus_area_name }}</strong></td>
                                                    <td>{{ $focusArea->weight }}%</td>
                                                    <td colspan="2" class="text-muted">No goals defined</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <h5>Behavioral Expectations <span class="label label-default">Applies to All Employees</span></h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="tr_header">
                                            <th>Item</th>
                                            <th>Weight</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($behavioralItems as $item)
                                            <tr>
                                                <td>{{ $item->item_name }}</td>
                                                <td>{{ $item->weight }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>

                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-info btn_style btn-block">
                                        <i class="fa fa-check"></i> {{ isset($editModeData) ? 'Update' : 'Create Appraisal(s)' }}
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ route('performance.appraisal.index') }}" class="btn btn-info btn_style btn-block">
                                        <i class="fa fa-times"></i> Cancel
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const reviewPeriodSelect = document.getElementById('review_period_id');
    const startDateDisplay = document.getElementById('review_start_date_display');
    const endDateDisplay = document.getElementById('review_end_date_display');
    const employeeSelect = document.getElementById('employee_ids');
    const departmentFilter = document.getElementById('department_filter');

    // Existing appraisals data for duplicate check (passed from controller)
    const existingAppraisals = @json(isset($existingAppraisals) ? $existingAppraisals->groupBy('employee_id')->map(function($items) {
        return $items->pluck('review_period')->toArray();
    }) : []);
    
    const reviewPeriodNames = @json($reviewPeriods->keyBy('period_id')->map->period_name);

    function updateDateFields() {
        const selectedOption = reviewPeriodSelect.options[reviewPeriodSelect.selectedIndex];
        
        if (selectedOption && selectedOption.value) {
            const startDate = selectedOption.getAttribute('data-start-date');
            const endDate = selectedOption.getAttribute('data-end-date');
            
            startDateDisplay.value = startDate ? formatDate(startDate) : '';
            endDateDisplay.value = endDate ? formatDate(endDate) : '';
        } else {
            startDateDisplay.value = '';
            endDateDisplay.value = '';
        }
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        return date.toLocaleDateString('en-US', options);
    }

    function checkDuplicateAppraisal() {
        const selectedOptions = Array.from(employeeSelect.selectedOptions);
        const selectedPeriodId = reviewPeriodSelect.value;
        const duplicateWarning = document.getElementById('duplicate_warning');
        const duplicateMessage = document.getElementById('duplicate_message');
        const submitButton = document.querySelector('button[type="submit"]');
        
        if (selectedOptions.length > 0 && selectedPeriodId) {
            const periodName = reviewPeriodNames[selectedPeriodId];
            const duplicates = [];
            
            selectedOptions.forEach(option => {
                const employeeId = option.value;
                const employeeName = option.text.split('(')[0].trim();
                const existingPeriods = existingAppraisals[employeeId] || [];
                
                if (existingPeriods.includes(periodName)) {
                    duplicates.push(employeeName);
                }
            });
            
            if (duplicates.length > 0) {
                duplicateMessage.textContent = 'Appraisal(s) already exist for: ' + duplicates.join(', ') + ' for the review period "' + periodName + '". Please select different employees or period.';
                duplicateWarning.style.display = 'block';
                if (submitButton) submitButton.disabled = true;
                return false;
            } else {
                duplicateWarning.style.display = 'none';
                if (submitButton) submitButton.disabled = false;
                return true;
            }
        } else {
            duplicateWarning.style.display = 'none';
            if (submitButton) submitButton.disabled = false;
            return true;
        }
    }

    function filterGoalsByDepartment() {
        const selectedDept = departmentFilter.value;
        const rows = document.querySelectorAll('.goal-row');
        
        rows.forEach(row => {
            const deptCell = row.querySelector('.dept-cell');
            const deptLabel = deptCell ? deptCell.querySelector('.label') : null;
            const isAllDepts = deptLabel && deptLabel.classList.contains('label-default');
            
            if (!selectedDept || isAllDepts) {
                row.style.display = '';
            } else {
                const rowDepts = JSON.parse(row.getAttribute('data-departments') || '[]');
                if (rowDepts.length === 0 || rowDepts.includes(parseInt(selectedDept))) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    }

    // Event listeners
    reviewPeriodSelect.addEventListener('change', function() {
        updateDateFields();
        checkDuplicateAppraisal();
    });
    
    employeeSelect.addEventListener('change', checkDuplicateAppraisal);
    departmentFilter.addEventListener('change', filterGoalsByDepartment);
    
    // Initialize on page load
    updateDateFields();
    checkDuplicateAppraisal();

    // Validate on form submit
    document.getElementById('appraisalForm').addEventListener('submit', function(e) {
        if (!checkDuplicateAppraisal()) {
            e.preventDefault();
            alert('Cannot create appraisal(s): One or more selected employees already have appraisals for this review period.');
        }
    });
});
</script>
@endsection
