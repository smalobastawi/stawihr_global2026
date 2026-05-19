<div class="table-responsive">
    <table id="myDataTables1" class="table table-hover manage-u-table">
        <thead>
            <tr>
                <th>#</th>
                <th>@lang('employee.photo')</th>
                <th>@lang('employee.name')</th>
                <th>@lang('employee.department')</th>
                <th>@lang('employee.phone')</th>
                <th title="national_id or face id or external machine id">Payroll No</th>
                <th>@lang('employee.date_of_joining')</th>
                <th>@lang('common.status')</th>
                <th>@lang('common.action')</th>
            </tr>
        </thead>
        <tbody>
            {!! $sl = null !!}
            @foreach ($results as $value)
                <tr class="{!! $value->employee_id !!}">
                    <td style="width: 100px;">{!! ++$sl !!}</td>
                    <td>
                        @if ($value->photo != '' && file_exists('uploads/employeePhoto/' . $value->photo))
                            <img style=" width: 70px; " src="{!! asset('uploads/employeePhoto/' . $value->photo) !!}" alt="user-img" class="img-circle">
                        @else
                            <img style=" width: 70px; " src="{!! asset('admin_assets/img/default.png') !!}" alt="user-img" class="img-circle">
                        @endif
                    </td>
                    <td>
                        <span class="font-medium">
                            {!! $value->first_name !!}&nbsp;{!! $value->last_name !!}
                        </span>
                        <br /><span class="text-muted">Role :
                            @if (isset($value->userName->role->role_name))
                                {!! $value->userName->role->role_name !!}
                            @endif
                        </span>
                        <br /><span class="text-muted">
                            @if (isset($value->supervisor->first_name))
                                Supervisor : {!! $value->supervisor->first_name !!} {!! $value->supervisor->last_name !!}
                            @endif
                        </span>
                    </td>
                    <td>
                        <span class="font-medium">
                            @if (isset($value->department->department_name))
                                {!! $value->department->department_name !!}
                            @endif
                        </span>
                        <br /><span class="text-muted">Designation :
                            @if (isset($value->designation->designation_name))
                                {!! $value->designation->designation_name !!}
                            @endif
                        </span>
                        <br /><span class="text-muted">
                            @if (isset($value->workLocation->location_name))
                                Location : {!! $value->workLocation->location_name !!}
                            @endif
                        </span>

                    </td>
                    <td>
                        <span class="font-medium">
                            {{ $value->phone }}
                        </span>
                        <br /><span class="text-muted">
                            @if ($value->email != '')
                                Email :{!! $value->email !!}
                            @endif
                        </span>
                    </td>
                    <td>
                        <span class="font-medium">
                            {!! $value->payroll_number !!}
                    </td>
                    </span>

                    <td>
                        <span class="font-medium">
                            {{ dateConvertDBtoForm($value->date_of_joining) }}
                        </span>
                        <br /><span class="text-muted">
                            {{ \Carbon\Carbon::parse($value->date_of_joining)->diffForHumans() }}
                        </span>
                    </td>
                    <td>
                        <select class="form-control permanent_status">
                            <option value="0">@lang('employee_permanent.probation_period')</option>
                            <option value="1">@lang('employee_permanent.permanent')</option>
                        </select>
                    </td>
                    <input type="hidden" class="employee_id" value="{{ $value->employee_id }}">
                    <td style="width: 150px">
                        <button type="button" class="btn btn-sm btn-success updateStatus">
                            @lang('employee_permanent.update_status')
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="text-center">
        {{ $results->links() }}
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#myDataTables1').DataTable({
            "pageLength": 100,
            "ordering": true,
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'copyHtml5',
                    footer: true
                },
                {
                    extend: 'excelHtml5',
                    footer: true
                },
                {
                    extend: 'csvHtml5',
                    footer: true
                },
                {
                    extend: 'pdfHtml5',
                    footer: true
                },
                'pageLength'
            ],
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api(),
                    data;

                // Remove the formatting to get integer data for summation
                var intVal = function(i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '') * 1 :
                        typeof i === 'number' ?
                        i : 0;
                };

                // Total over all pages
                total = api
                    .column(5)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Total over this page
                pageTotal = api
                    .column(5, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Update footer
                $(api.column(5).footer()).html(
                    pageTotal
                );
            }

        });
    });
</script>
