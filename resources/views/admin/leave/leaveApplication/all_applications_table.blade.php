<table id="myTable" class="table table-bordered table-hover" style="min-width: 1000px;">
    <thead class="tr_header">
        <tr>
           
            <th>@lang('common.employee_name')</th>
            <th>P No</th>
            <th>Location</th>
            <th>Department</th>
            <th>@lang('leave.leave_type')</th>
            <th>From</th>
            <th>To</th>
            <th>Request date</th>
            <th>Days Applied</th>
            <th>Holiday adjustments</th>
            <th>Final days</th>
            <th style="width: 300px;word-wrap: break-word;">@lang('leave.purpose')</th>
            <th>Supervisor approval</th>
            {{-- <th>P&C In Charge</th> --}}
            <th>Final status</th>
            <th>@lang('common.action')</th>
        </tr>
    </thead>
    <tbody>
    @if (count($results) > 0)
        {!! $sl = null !!}
        @foreach ($results as $value)
            <tr>
                <td>
                    {{ optional($value->employee)->fullName() ?? '' }}
                </td>
                <td>
                    {{ optional($value->employee)->payroll_number ?? '' }}
                </td>
                <td>
                    {{ optional(optional($value->employee)->location)->location_name ?? 'N/A' }}
                </td>
                <td>
                    @isset($value->employee->department)
                        {{ $value->employee->department->department_name }}
                    @endisset
                </td>
                <td>
                    @if (isset($value->leaveType->leave_type_name))
                        {!! $value->leaveType->leave_type_name !!}
                    @endif
                </td>
                <td>
                    {!! dateConvertDBtoForm($value->application_from_date) !!}
                </td>
                <td>{!! dateConvertDBtoForm($value->application_to_date) !!}</td>
                <td>{!! dateConvertDBtoForm($value->application_date) !!}</td>
                <td>{!! $value->number_of_day !!}</td>
                <td>{!! $value->holiday_adjustment !!}</td>  <!-- Use pre-calculated value -->
                <td>{!! $value->final_days !!}</td>        <!-- Use pre-calculated value -->
                <td>{!! $value->purpose !!}</td>
                
                <!-- Rest of your table cells remain the same -->
                <!--supervisor approval status here -->

                @if ($value->status == 1)
                    <td style="width: 100px;">
                        <span class="label label-warning">@lang('common.pending')</span>
                    </td>
                @elseif($value->status == 2)
                    <td style="width: 100px;">
                        <span class="label label-success">@lang('common.approved')</span>
                    </td>
                @elseif($value->final_status == LeaveStatus::RECALL)
                    <td style="width: 100px;">
                        <span class="label label-primary">Recalled</span>
                    </td>
                @else
                    <td style="width: 100px;">
                        <span class="label label-danger">@lang('common.rejected')</span>
                    </td>
                @endif

                @if ($value->final_status == LeaveStatus::PENDING)
                    <td style="width: 100px;">
                        <span class="label label-warning">@lang('common.pending')</span>
                    </td>
                @elseif($value->final_status == LeaveStatus::APPROVE)
                    <td style="width: 100px;">
                        <span class="label label-success">@lang('common.approved')</span>
                    </td>
                @elseif($value->final_status == LeaveStatus::RECALL)
                    <td style="width: 100px;">
                        <span class="label label-primary">Recalled</span>
                    </td>
                @else
                    <td style="width: 100px;">
                        <span class="label label-danger">@lang('common.rejected')</span>
                    </td>
                @endif

                <td>
                    @if ($value->status == 1)
                        <a href="{!! route('requestedApplication.viewDetails', $value->leave_application_id) !!}" title="View leave details!"
                            class="btn btn-info btn-xs btnColor">
                            <i class="fa fa-arrow-circle-right"></i>View
                        </a>
                        <a href="{!! route('leaveApplication.delete', $value->leave_application_id) !!}" data-token="{!! csrf_token() !!}"
                            data-id="{!! $value->leave_application_id !!}"
                            class="delete btn btn-danger btn-xs deleteBtn btnColor"><i class="fa fa-trash-o"
                                aria-hidden="true"></i></a>
                    @elseif($value->final_status == LeaveStatus::APPROVE)
                        <a href="{!! route('requestedApplication.viewDetails', $value->leave_application_id) !!}" title="View leave details!"
                            class="btn btn-info btn-xs btnColor">
                            <i class="fa fa-arrow-circle-right"></i> View
                        </a>
                        @can('requestedApplication.update')
                            <form action="{{ route('leaveApplication.recall', $value->leave_application_id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to recall this leave?');">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-xs btnColor" title="Recall Leave">
                                    <i class="fa fa-undo"></i> Recall
                                </button>
                            </form>
                        @endcan
                    @else
                        <a href="{!! route('requestedApplication.viewDetails', $value->leave_application_id) !!}" title="View leave details!"
                            class="btn btn-info btn-xs btnColor">
                            <i class="fa fa-arrow-circle-right"></i> View
                        </a>
                    @endif
                </td>
            </tr>
        @endforeach
    @endif
</tbody>
</table>
