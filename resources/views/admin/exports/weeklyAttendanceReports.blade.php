
<!DOCTYPE html>
<html lang="en">

<body>

<div class="table-responsive">


    <table id="attendanceTable1" class="table table-bordered">
        <thead class="tr_header">
        <tr> <td colspan="9" class="printHead" >
                @if($dataExport['printHead'])
                    {!!  $dataExport['printHead']->description !!}
                @endif
                <b>@lang('common.date') : </b> {{$dataExport['date']}} {{$dataExport['week_year']}}
            </td>
        </tr>
        <tr>
            <th>@lang('common.employee_name')</th>
            <th>Presence</th>
            @foreach($dataExport['weekdays'] as $wkd)
                <th>{{$wkd}}</th>
            @endforeach
        </tr>
        </thead>
        <tbody >
        @if(count($dataExport['week_data']) > 0)
            @php
                $count_up_=0;
                $count_up=0;
            @endphp
            @foreach($dataExport['week_data'] AS $key=>$data)

                <tr>
                    <td >{{$key}}</td>
                    <td style="">Presence</td>

                    @foreach($dataExport['weekdays'] as $day)
                        @if(isset($data[$day]))
                            <td >{{$data[$day]->presence_status}}</td>
                        @else
                            <td >--</td>
                        @endif
                    @endforeach
                </tr>


                <tr  >
                    <td ></td>
                    <td>Time In</td>

                    @foreach($dataExport['weekdays'] as $day)
                        @if(isset($data[$day]))
                            @if($data[$day]->presence_status=="PRESENT")
                                <td>{{date('h:i A', strtotime($data[$day]->time_in))}}</td>
                            @else
                                <td>--</td>
                            @endif
                        @else
                            <td>--</td>
                        @endif
                    @endforeach
                </tr>
                <tr >
                    <td ></td>
                    <td>Time Out</td>

                    @foreach($dataExport['weekdays'] as $day)
                        @if(isset($data[$day]))
                            @if($data[$day]->presence_status=="PRESENT")
                                <td>{{date('h:i A', strtotime($data[$day]->time_out))}}</td>
                            @else
                                <td>--</td>
                            @endif
                        @else
                            <td>--</td>
                        @endif
                    @endforeach
                </tr>
                <tr >
                    <td ></td>
                    <td>Sign</td>

                    @foreach($dataExport['weekdays'] as $day)
                        @if(isset($data[$day]))
                            @if($data[$day]->presence_status=="PRESENT")
                                <td></td>
                            @else
                                <td>--</td>
                            @endif
                        @else
                            <td>--</td>
                        @endif
                    @endforeach
                </tr>

                <tr >
                    <td ></td>
                    <td>Hours Worked</td>

                    @foreach($dataExport['weekdays'] as $day)
                        @if(isset($data[$day]))
                            @if($data[$day]->presence_status=="PRESENT")
                                <td>

                                    <?php
                                    if ($data[$day]->time_in != null) {
                                        $min = 1 * 60 + 20;
                                        $clockOut = \Carbon\Carbon::parse($data[$day]->time_out);

                                        $newClockOut = $clockOut->subMinutes($min)->format('Y-m-d H:i:s');
                                        $finishTime = \Carbon\Carbon::parse($newClockOut);
                                        $totalDuration = $finishTime->diffInSeconds($data[$day]->time_in);
                                        $hours_worked = gmdate('H:i', $totalDuration);

                                        echo $hours_worked;
                                    } else {
                                        echo "--";
                                    }
                                    ?>
                                </td>
                            @else
                                <td>--</td>
                            @endif
                        @else
                            <td>--</td>
                        @endif
                    @endforeach
                </tr>
                <tr >
                    <td ></td>
                    <td>O/T Earned</td>

                    @foreach($dataExport['weekdays'] as $day)
                        @if(isset($data[$day]))
                            <td><?php
                                if ($data[$day]->time_in != null) {
                                    $hours = 9 * 60 * 60;
                                    $clockOut = \Carbon\Carbon::parse($data[$day]->time_out);
                                    $clockIn = \Carbon\Carbon::parse($data[$day]->time_in);
                                    $totalDuration1 = $clockOut->diffInSeconds($clockIn);


                                    if ($totalDuration1 > $hours) {
                                        $interval = $totalDuration1-$hours;
                                        echo gmdate('H:i', $interval);
                                    } else {
                                        echo '0';
                                    }
                                } else {
                                    echo '--';
                                }
                                ?></td>
                        @else
                            <td></td>
                        @endif
                    @endforeach
                </tr>
                <tr >
                    <td ></td>
                    <td>Accident/Incident</td>
                    @foreach($dataExport['weekdays'] as $day)
                        @if(isset($data[$day]))
                            @if($data[$day]->presence_status=="PRESENT")
                                <td></td>
                            @else
                                <td>--</td>
                            @endif
                        @else
                            <td>--</td>
                        @endif
                    @endforeach
                </tr>

            @endforeach

        @else
            <tr>
                <td colspan="9">No data</td>
            </tr>
        @endif
        </tbody>

    </table>
</div>

<script>
    $(document).ready(function() {
        $('#attendanceTable1').DataTable( {
            "pageLength": 1000,
            "ordering": true,
            dom: 'Bfrtip',
            buttons: [
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5',
                'pageLength'
            ],

        } );
    } );
</script>
</body>
</html>
