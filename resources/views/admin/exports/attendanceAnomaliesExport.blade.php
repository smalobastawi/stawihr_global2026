<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>National_id</th>
        <th>Date</th>
        <th>time_in</th>
        <th>time_out</th>
        <th>lunch_checkin</th>
    </tr>
    </thead>
    <tbody>
    @foreach($anomalies as $anomaly)
        <tr>
            <td>{{ $anomaly->employee->first_name.' '. $anomaly->employee->last_name }}</td>
            <td>{{ $anomaly->national_id }}</td>
            <td>{{ $anomaly->date->format('Y-m-d') }}</td>

            @if( $anomaly->time_in==null )
                <td style="background-color: darkorange"></td>
            @else
                <td>{{ $anomaly->time_in->format('h:i A') }}</td>
            @endif
            @if( empty($anomaly->time_out) )
                <td style="background-color: darkorange"></td>
            @else
                <td>{{ $anomaly->time_out->format('h:i A') }}</td>
            @endif
                @if( empty($anomaly->lunch_checkin) )
            <td style="background-color: darkorange"></td>
                @else
                    <td >{{ $anomaly->lunch_checkin->format('h:i A') }}</td>
                @endif
        </tr>
    @endforeach
    </tbody>
</table>