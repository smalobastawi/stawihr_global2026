<tbody id="tbody_filtered_data">
    @if (count($results) > 0)
        @php $sl = 1; @endphp
        @foreach ($results as $value)
            <tr>
                <td>{{ $sl++ }}</td>
                <td>{{ $value['employee_name'] }}</td>
                <td>{{ $value['leave_type_name'] }}</td>
                <td>{{ $value['totalDays'] }}</td>
                <td>{{ \Carbon\Carbon::parse($value['date_from'])->format('d M Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($value['date_to'])->format('d M Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($value['application_date'])->format('d M Y') }}</td>
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
</tbody>