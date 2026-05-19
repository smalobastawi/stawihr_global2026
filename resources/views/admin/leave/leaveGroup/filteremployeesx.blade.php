@foreach($employees as $employee)
  <tr>
                                                            <td><input type="checkbox" class="currentEmp" name="employee_ids[]" value="{{ $employee->employee_id }}"></td>
                                                            <td>{{ $employee->department->department_name ?? '-' }}</td>
                                                            <td>{{ $employee->fullName() }}</td>
                                                            <td>{{ $employee->designation->designation_name ?? '-' }}</td>
                                                            <td>
                                                                <button type="button"
                                                                        class="btn btn-danger btn-sm delete-single"
                                                                        data-employee-id="{{ $employee->employee_id }}">
                                                                    <i class="fa fa-trash-o"></i> @lang('common.remove')
                                                                </button>
                                                            </td>
                                                        </tr>
 @endforeach
