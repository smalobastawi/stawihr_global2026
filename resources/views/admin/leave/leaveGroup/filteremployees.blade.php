@foreach($employees as $employee)
<tr>
    <td><input class="empids" type="checkbox" name="employee_ids[]" value="{{ $employee->employee_id }}"></td>
    <td>{{ $employee->department->department_name ?? '-' }}</td>
    <td>{{ $employee->fullName()}}</td>
    <td>{{ $employee->designation->designation_name ?? '-' }}</td>
    <td>
        <button type="button" class="btn btn-success btn-sm add-single" data-employee-id="{{ $employee->employee_id }}">@lang('common.add')</button>
    </td>
</tr>
@endforeach