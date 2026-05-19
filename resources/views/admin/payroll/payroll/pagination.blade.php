<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
        <tr class="tr_header">
            <th>@lang('common.serial')</th>
            <th>@lang('common.month')</th>
            <th>@lang('common.employee_name')</th>
            <th>Job Category</th>
            <th>@lang('paygrade.gross_salary')</th>
            <th>@lang('paygrade.basic_salary')</th>
            <th>All Deductions</th>
            <th>Net Salary</th>
            <th>@lang('common.action')</th>
        </tr>
        </thead>
        <tbody>
        @if(count($results)>0)
            {!! $sl=null !!}
            @foreach($results AS $value)
                @if($value->employee->department->department_name == 'Administration')
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
                            @if(isset($value->employee->department->department_name))
                                <br>
                                <span class="text-muted">@lang('employee.department') : {{$value->employee->department->department_name}}</span>
                                <br>
                                Payroll No: {{$value->employee->payroll_number}}
                            @endif

                        </td>
                        <td>    @if(isset($value->employee->jobCategory->name))
                                {!!  $value->employee->jobCategory->name !!}
                            @endif
                        </td>
                        <td>{!! $value->gross_pay !!}</td>
                        <td>{!! $value->basic_salary !!}</td>
                        <td>-{!! $value->total_deduction !!}</td>
                        <td>{!! $value->net_salary !!}</td>
                        <td style="width: 100px">
                            @if($value->status == 0)
                                <button class="btn btn-info waves-effect waves-light"
                                        data-salary_details_id="{!! $value->salary_details_id !!}"
                                        data-monthAndYearName="{!! $monthAndYearName !!}"
                                        data-basic_salary="{!! $value->basic_salary !!}"
                                        data-gross_salary="{!! $value->gross_pay !!}"
                                        data-total_allowance="{!! $value->total_allowance !!}"
                                        data-total_deduction="{!! $value->total_deduction !!}"
                                        data-employee_name="@if(isset($value->employee->first_name)){!!  $value->employee->first_name !!} {{$value->employee->last_name}}@endif"
                                        data-toggle="modal" data-target="#responsive-modal">
                                    <span>@lang('salary_sheet.make_payment')</span></button>
                                <a href="{!!route('delete_salary_entry',$value->salary_details_id )!!}" data-token="{!! csrf_token() !!}" data-id="{!! $value->salary_details_id!!}" class="btn btn-danger   btnColor"><i class="fa fa-trash-o" aria-hidden="true">Delete</i></a>
                                <a href="{{route('generatePayslip',$value->salary_details_id)}}"
                                    target="_blank">
                                     <button class="btn btn-success  waves-effect waves-light">
                                         <span>@lang('salary_sheet.generate_payslip')</span></button>
                                 </a>
                            @else
                                <a href="{{route('generatePayslip',$value->salary_details_id)}}"
                                   target="_blank">
                                    <button class="btn btn-success  waves-effect waves-light">
                                        <span>@lang('salary_sheet.generate_payslip')</span></button>
                                </a>
                            @endif
                        </td>

                    </tr>
                @endif
            @endforeach
        @else
            <tr>
                <td colspan="10">@lang('common.no_data_available') !</td>
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