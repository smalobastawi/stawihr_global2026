@if(count($results) > 0)
{{$sl = null}}
@foreach($results as $value)
    <tr>
        <td>{{ ++$sl }}</td>
        <td>{{ $value['employee_name'] }}</td>
        
        <td>{{ $value['leave_type_name'] }}</td>
        <td>{{ $value['totalDays'] }}</td>
        <td>{{ $value['days_used'] }}</td>
        <td>{{ $value['roll_over_days'] }}</td>
        <td>{{ $value['totalBlance'] }}</td>
        <td>{{ $value['employee_location'] ?? '' }}</td>
        <td>{{ $value['employee_department'] ?? '' }}</td>
        <td>{{ $value['employee_designation'] ?? '' }}</td>
    </tr>
@endforeach
@else
<tr>
    <td colspan="10">@lang('common.no_data_available')!</td>
</tr>
@endif