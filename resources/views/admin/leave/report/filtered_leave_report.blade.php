@if(count($results) > 0)
{{$sl = null}}
@foreach($results as $value)
    <tr>
        <td>{{ ++$sl }}</td>
        <td>{{ $value['employee_name'] }}</td>
        
        <td>{{ $value['leave_type_name'] }}</td>
        <td>{{ $value['totalDays'] }}</td>
        <td>{{ $value['date_from'] }}</td>
        <td>{{ $value['date_to'] }}</td>
        <td>{{ $value['application_date'] }}</td>
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