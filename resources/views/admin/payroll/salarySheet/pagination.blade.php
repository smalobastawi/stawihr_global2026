<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
        <tr class="tr_header">
            <th>@lang('common.serial')</th>
            <th>@lang('common.year')</th>
            <th>@lang('common.employee_name')</th>
            <th>@lang('employee.department')</th>
            <th>Payroll No</th>
            <th>Job Category</th>
            <th>@lang('common.action')</th>
        </tr>
        </thead>
        <tbody>
        @if(count($results)>0)
            {!! $sl=null !!}
            @foreach($results AS $value)
                @if($value->employee->department->department_name == '')
                    .
                @else
                    <tr>
                        <td style="">{!! ++$sl !!}</td>
                        <td>
                            @php
                                $monthAndYear   = explode('-',$value->month_of_salary);

                                $month = $monthAndYear[1];
                                $dateObj   = DateTime::createFromFormat('!m', $month);
                                $monthName = $dateObj->format('F');
                                $year = $monthAndYear[0];

                                $monthAndYearName = $monthName." ".$year ;
                                echo $monthAndYearName;
                            @endphp
                        </td>
                        <td>@if(isset($value->employee->first_name))
                                {!!  $value->employee->first_name !!} {{$value->employee->last_name}}
                            @endif
                        </td>
                        <td>@if(isset($value->employee->department->department_name))
                                {{$value->employee->department->department_name}}
                            @endif
                        </td>
                        <td>{{$value->employee->payroll_number}}</td>
                        <td>@if(isset($value->employee->jobCategory->name))
                                {!!  $value->employee->jobCategory->name !!}
                            @endif
                        </td>

                        <td style="width: 100px">

                            <a href="{{route('generatePayslip',$value->salary_details_id)}}"
                               target="_blank">
                                <button class="btn btn-success  waves-effect waves-light">
                                    <span>View Details</span></button>
                            </a>
                            <a href="{!!route('delete_salary_entry',$value->salary_details_id  )!!}" data-token="{!! csrf_token() !!}" data-id="{!! $value->salary_details_id !!}" class="delete btn btn-danger btn-xs deleteBtn btnColor"><i class="fa fa-trash-o" aria-hidden="true">Delete</i></a>


                        </td>

                    </tr>
                @endif
            @endforeach
        @else
            <tr>
                <td colspan="7">@lang('common.no_data_available') !</td>
            </tr>
        @endif
        </tbody>
    </table>
    @if(count($results)>0)
        <div class="text-center">
            {{$results->links()}}
        </div>
    @endif
</div>
