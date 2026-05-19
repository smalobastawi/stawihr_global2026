<div class="table-responsive">
    <table id="myDataTables1" class="table table-bordered" style="margin-bottom: 47px">
        <thead class="tr_header">
        <tr>
            <th>@lang('common.serial')</th>
            <th>Date</th>
            <th>National Id</th>
            <th>@lang('common.employee_name')</th>
            <th>Department</th>
            <th>Shift</th>
            <th>@lang('common.presence')</th>
            <th>@lang('attendance.in_time')</th>
            <th>@lang('attendance.out_time')</th>
            <th>Lunch Checkin</th>
        </tr>
        </thead>
        <tbody>
        {!! $sl=null !!}

        @if($results->all() != null)
            @foreach($results as $value)
                <tr>
                    <td>{!! ++$sl !!}</td>
                    <td>{!! $value->date->format('Y-m-d') !!}</td>
                    <td>{{$value->employee->national_id}}</td>
                    <td>{{$value->employee->first_name.' '.$value->employee->middle_name.' '.$value->employee->last_name}}</td>

                    <td style="width: 200px">
                        {{$value->department->department_name}}
                    </td>
                    <td style="width: 200px">
                        {{$value->workShift->shift_name}}
                    </td>
                    <td style="width: 200px">
                        {{$value->presence_status}}
                    </td>
                    <td style="">
                        {{(isset($value->time_in)) ? $value->time_in->format('h:i A') : ''}}
                    </td>
                    <td style="">
                        {{ (isset($value->outTime)) ? $value->time_out->format('h:i A') : ''}}
                    </td>

                    <td style="width: 200px">
                    {{ (isset($value->lunch_checkin)) ? $value->lunch_checkin->format('h:i A') : ''}}
                </tr>
            @endforeach

        @endif
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function() {
        $('#myDataTables1').DataTable( {
            "pageLength": 100,
            "ordering": true,
            dom: 'Bfrtip',
            buttons: [
                { extend: 'copyHtml5', footer: true },
                { extend: 'excelHtml5', footer: true },
                { extend: 'csvHtml5', footer: true },
                { extend: 'pdfHtml5', footer: true },
                'pageLength'
            ],
            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;

                // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };

                // Total over all pages
                total = api
                    .column( 5 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                // Total over this page
                pageTotal = api
                    .column( 5, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                // Update footer
                $( api.column( 5 ).footer() ).html(
                    pageTotal
                );
            }

        } );
    } );
</script>