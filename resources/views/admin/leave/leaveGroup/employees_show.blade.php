@foreach($employees as $employee)
<tr>
    <td>{{ $employee->department->department_name ?? '-' }}</td>
    <td>{{ $employee->fullName() }}</td>
    <td>{{ $employee->designation->designation_name ?? '-' }}</td>
</tr>
@endforeach