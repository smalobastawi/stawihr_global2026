<input type="hidden" name="filtering" value="1">

<div class="col-md-2">
    <div class="form-group">
        <label for="department_id">Department</label>
        <select class="form-control select2" name="department_id">
            <option value="">-- @lang('training.select_department') --</option>
            {{-- @foreach ($departments as $department)
                <option value="{{ $department->department_id }}"
                    {{ $department_id == $department->department_id ? 'selected' : '' }}>
                    {{ $department->department_name }}
                </option>
            @endforeach --}}
        </select>
    </div>
</div>
<div class="col-md-2">
    <div class="form-group employeeName">
        <label for="employee_id">@lang('common.employee')<span class="validateRq">*</span></label>
        <select class="form-control select2" required name="employee_id">
            <option value="">-- @lang('training.select_employee') --</option>
            {{-- @foreach ($employeeList as $employee)
                <option value="{{ $employee->employee_id }}"
                    {{ $employee_id == $employee->employee_id ? 'selected' : '' }}>
                    {{ $employee->first_name }} {{ $employee->last_name }}
                </option>
            @endforeach --}}
        </select>
    </div>
</div>
<div class="col-md-2">
    <div class="form-group">
        <label for="training_type_id">@lang('training.training_type')</label>
        <select class="form-control select2" name="training_type_id">
            <option value="">-- @lang('training.select_training_type') --</option>
            {{-- @foreach ($trainingTypes as $type)
                <option value="{{ $type->training_type_id }}"
                    {{ $training_type_id == $type->training_type_id ? 'selected' : '' }}>
                    {{ $type->training_type_name }}
                </option>
            @endforeach --}}
        </select>
    </div>
</div>
<div class="col-md-2">
    <div class="form-group">
        <label for="training_id">@lang('training.training')</label>
        <select class="form-control select2" name="training_id">
            <option value="">-- @lang('training.select_training') --</option>
            {{-- @foreach ($trainings as $training)
                <option value="{{ $training->id }}" {{ $training_id == $training->id ? 'selected' : '' }}>
                    {{ $training->description }}
                </option>
            @endforeach --}}
        </select>
    </div>
</div>
<div class="col-md-2">
    <div class="form-group">
        <label for="facilitator_id">@lang('training.facilitator')</label>
        <select class="form-control select2" name="facilitator_id">
            <option value="">-- @lang('training.select_facilitator') --</option>
            {{--  @foreach ($facilitators as $facilitator)
                <option value="{{ $facilitator->id }}" {{ $facilitator_id == $facilitator->id ? 'selected' : '' }}>
                    {{ $facilitator->name }}
                </option>
            @endforeach --}}
        </select>
    </div>
</div>

<div class="col-md-2">
    <div class="form-group">
        <label for="search_btn"></label>
        <a href="" id="search_btn" class="btn btn-success  form-control" style="color: white;">Search</a>
    </div>
</div>
