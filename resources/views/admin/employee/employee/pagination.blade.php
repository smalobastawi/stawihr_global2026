<div class="table-responsive">
    <table id="myEmployeeDataTables" class="table table-hover manage-u-table">
        <thead>
			<tr>
				<th>#</th>
				<th>@lang('employee.name')</th>
				<th>Work Email</th>
				<th>@lang('employee.department')</th>

				<th>Payroll No</th>
				<th>Supervisor</th>
				
				<th>@lang('employee.date_of_joining')</th>
             
				<th>@lang('common.status')</th>
				
				<th>@lang('common.action')</th>
			</tr>
        </thead>
        <tbody>
        {!! $sl=null !!}
        @foreach($results AS $value)
            <tr class="{!! $value->employee_id !!}">
                <td style="width: 100px;">{!! ++$sl !!}</td>
                <td>
					<span class="font-medium">
                        <a href="{!! route('employee.show',$value->employee_id  ) !!}">
                        {!! $value->first_name !!}&nbsp;{!! $value->middle_name !!}&nbsp;{!! $value->last_name !!}
                        </a>
					</span>
						
                        {{-- <span class="text-muted">@lang('employee.role') :
						@if(isset($value->userName->role->role_name)) {!! $value->userName->role->role_name !!} @endif
					</span> --}}
					
                </td>
                <td>
                    <span class="font-medium">
                        {!! $value->email !!}
					</span>
                </td>
                <td>
					<span class="font-medium">
						@if (isset($value->department->department_name)) {!! $value->department->department_name !!} @endif
					</span>
                    <br/><span class="text-muted">@lang('employee.designation') :
                        @if (isset($value->designation->designation_name)) {!! $value->designation->designation_name!!} @endif
					</span>

                </td>
                <td>
                    <span class="font-medium">
                        {!! $value->payroll_number !!}
					</span>
                </td>
                    <td>
                        <span class="text-muted">
                            @if (isset($value->supervisor->first_name)) {!! $value->supervisor->first_name !!} {!! $value->supervisor->last_name !!}@endif
                        </span>
                    </td>
              
                <td>
                    <span class="font-medium">
						{{dateConvertDBtoForm($value->date_of_joining)}}
					</span>
                    <br/>
                    {{-- <span class="text-muted">
                        {{ \Carbon\Carbon::parse($value->date_of_joining)->diffForHumans() }}
					</span> --}}
                    <br/>
                    {{-- <span class="text-muted">
                        @lang('employee.job_status'): @if($value->permanent_status == 0) @lang('employee.probation_period') @else @lang('employee.permanent') @endif
					</span> --}}
                </td>
               
              
                <td>
                    @if($value->status == GeneralStatus::ACTIVE)
                        <span class="label label-success">@lang('common.active')</span>
					</span>
                    @elseif($value->status == GeneralStatus::INACTIVE)
                        <span class="label label-warning">@lang('common.inactive')</span>
                    @else
                        <span class="label label-danger">@lang('common.terminated')</span>
                    @endif
                </td>

                <td style="width: 150px">
                    @can('employee.show')
                    <a  title="Profile" href="{!! route('employee.show',$value->employee_id  ) !!}"  class="btn btn-primary btn-xs btnColor">
                        <i class="glyphicon glyphicon-th-large" aria-hidden="true"></i>
                    </a>
                    @endcan
                    @can('employee.edit')
                    <a href="{!! route('employee.edit',$value->employee_id) !!}"  class="btn btn-success btn-xs btnColor">
                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                    </a>
                    @endcan
                    @can('employee.delete')
                    <a href="{!!route('employee.delete',$value->employee_id )!!}" data-token="{!! csrf_token() !!}" data-id="{!! $value->employee_id!!}" class="delete btn btn-danger btn-xs deleteBtn btnColor"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                @endcan
                </td>
            </tr>

        @endforeach
        </tbody>
    </table>
    <div class="text-center">
        {{$results->links()}}
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#myEmployeeDataTables').DataTable( {
            "pageLength": 500,
            "ordering": true,
            dom: 'Bfrtip',
            buttons: [
               
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
    });
</script>