<div class="form-group">
    <div class="col-md-2">
        <label class="control-label" for="email">Date range start <span class="validateRq">*</span></label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            <input type="text" class="form-control dateField required" required placeholder="Enter Date" name="date_from" 
                   value="{{ isset($filterData['date_from']) ? $filterData['date_from'] : '' }}" required>
        </div>
    </div>
    <div class="col-md-2">
        <label class="control-label" for="email">To Date <span class="validateRq">*</span></label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            <input type="text" class="form-control dateField required" required placeholder="Enter Date" name="date_to" 
                   value="{{ isset($filterData['date_to']) ? $filterData['date_to'] : '' }}" required>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
            <label for="exampleInput">@lang('employee.department')</label>
            <select name="department_id" class="form-control department_id select2">
                <option value="">--- @lang('employee.select_department') ---</option>
                @foreach($departmentList as $value)
                    <option value="{{ $value->department_id }}" 
                            {{ (isset($filterData['department_id']) && $value->department_id == $filterData['department_id']) ? 'selected' : '' }}>
                        {{ $value->department_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-2">
        <label class="control-label" for="email">Location <span class="validateRq">*</span></label>
        <div class="input-group">
            <select name="location_id" class="form-control department_id select2">
                <option value="">--- @lang('employee.location') ---</option>
                @foreach($branchList as $value)
                    <option value="{{ $value->location_id }}"
                            {{ (isset($filterData['location_id']) && $value->location_id == $filterData['location_id']) ? 'selected' : '' }}>
                        {{ $value->location_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-sm-2">
        <label for="exampleInput">Filter data</label>
        <input type="submit" id="filter" style="margin-top: 2px; width: 100px;"
               class="btn btn-info" value="@lang('common.filter')">
    </div>
    <div class="col-sm-2">
        <a href="{{ url()->current() }}">
            <button type="button" id="clearFilter" style="margin-top: 2px; width: 100px;"
                    class="btn btn-info" value="Clear filter">Clear filter
            </button>
        </a>
    </div>
</div>