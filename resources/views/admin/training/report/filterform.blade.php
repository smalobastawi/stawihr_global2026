<input type="hidden" name="filtering" value="1">

<div class="col-md-2">
    <div class="form-group">
        <label for="department_id">Department</label>
        <select class="form-control select2" name="department_id">
            <option value="" selected="selected" disabled="disabled">-- @lang('training.select_department') --</option>
            @foreach ($departments as $department)
                <option value="{{ $department->department_id }}"
                    {{ $department_id == $department->department_id ? 'selected' : '' }}>
                    {{ $department->department_name ?? 'No department assigned' }}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="col-md-2">
    <div class="form-group employeeName">
        <label for="employee_id">@lang('common.employee')<span class="validateRq">*</span></label>
        <select class="form-control select2" required name="employee_id">
            <option value="" selected="selected" disabled="disabled">-- @lang('training.select_employee') --</option>
            @foreach ($employeeList as $employee)
                <option value="{{ $employee->employee_id }}"
                    {{ $employee_id == $employee->employee_id ? 'selected' : '' }}>
                    {{ $employee->first_name }} {{ $employee->last_name }}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="col-md-2">
    <div class="form-group">
        <label for="training_type_id">@lang('training.training_type')</label>
        <select class="form-control select2" name="training_type_id">
            <option value="" selected="selected" disabled="disabled">-- @lang('training.select_training_type') --</option>
            @foreach ($trainingTypes as $type)
                <option value="{{ $type->training_type_id }}"
                    {{ $training_type_id == $type->training_type_id ? 'selected' : '' }}>
                    {{ $type->training_type_name }}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="col-md-2">
    <div class="form-group">
        <label for="training_id">@lang('training.training')</label>
        <select class="form-control select2" name="training_id">
            <option value="" selected="selected" disabled="disabled">
                -- @lang('training.select_training') --
            </option>
            @foreach ($trainings as $value)
                <option value="{{ $value->id }}" @isset($training)
                    {{ $training->id == $value->id ? 'selected' : '' }}
                @endisset>
                    {{ $value->subject }}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="col-md-2">
    <div class="form-group">
        <label for="facilitator_id">@lang('training.facilitator')</label>
        <select class="form-control select2" name="facilitator_id">
            <option value="" selected="selected" disabled="disabled">
                -- @lang('training.select_facilitator') --
            </option>
            @foreach ($facilitators as $facilitator)
                <option value="{{ $facilitator->id }}" {{ $facilitator_id == $facilitator->id ? 'selected' : '' }}>
                    {{ $facilitator->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="col-md-2">
    <div class="form-group">
        <label for="reset_btn"> Training Dates</label>
        <label class="form-control">
            @isset($training)
                {{ $training->start_date }} to {{ $training->end_date }}
            @endisset
        </label>
    </div>
</div>
