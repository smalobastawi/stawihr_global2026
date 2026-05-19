@php
    $totalDeduction = getDeductions();
@endphp
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
        <tr class="">
            <th>@lang('common.serial')</th>
            <th>@lang('common.employee_name')</th>
            <th>@lang('salary_sheet.basic_salary')</th>
            <th>Total Allowance</th>
            <th>@lang('salary_sheet.gross_salary')</th>
            <th>Deductions</th>
            <th>Net pay</th>
            <th>@lang('common.status')</th>
            <th>@lang('common.action')</th>
        </tr>
        </thead>
        <tbody>
        @if(count($staffSalaryDetails)>0)
            {!! $sl=null !!}
            @foreach($staffSalaryDetails AS $value)
                <tr>
                    <td style="width: 8px;">{!! ++$sl !!}</td>
                    <td>@if(isset($value->first_name))
                            {!!  $value->first_name !!} {{$value->last_name}}
                        @endif
                    </td>
                    <td>
                        @if(isset($value->basic_salary))
                            {!!  $value->basic_salary !!}
                        @endif</td>
                    <td>
                        @if(isset($value->total_allowance))
                            {{$value->total_allowance}}
                        @else
                            0
                        @endif
                    </td>
                    <td>@if(isset($value->gross_pay))
                            {!!  $value->gross_pay !!}
                        @endif
                    </td>
                    <td>{{$totalDeduction}}</td>
                    <td>Net Pay</td>
                    @if($value->status == 0)
                        <td>
                            <span class="label label-warning">@lang('salary_sheet.unpaid')</span>
                        </td>
                    @else
                        <td>
                            <span class="label label-success">@lang('salary_sheet.paid')</span>
                        </td>
                    @endif

                    <td style="width: 4px">
                        @if($value->status == 0)
                            <button class="btn btn-info waves-effect waves-light"
                                    data-salary_details_id="{!! $value->salary_details_id !!}"
                                    data-monthAndYearName="{!! $monthAndYearName !!}"
                                    data-basic_salary="{!! $value->basic_salary !!}"
                                    data-gross_salary="{!! $value->gross_salary !!}"
                                    data-total_allowance="{!! $value->total_allowance !!}"
                                    data-total_deduction="{!! $value->total_deduction !!}"
                                    data-employee_name="@if(isset($value->employee->first_name)){!!  $value->employee->first_name !!} {{$value->employee->last_name}}@endif"
                                    data-toggle="modal" data-target="#responsive-modal">
                                <span>@lang('salary_sheet.make_payment')</span></button>
                            <a href="{!!route('delete_salary_entry',$value->salary_details_id )!!}" data-token="{!! csrf_token() !!}" data-id="{!! $value->salary_details_id!!}" class="btn btn-danger   btnColor"><i class="fa fa-trash-o" aria-hidden="true">Delete</i></a>

                        @else
                            <a href="{{url('generateSalarySheet/generatePayslip',$value->salary_details_id)}}"
                               target="_blank">
                                <button class="btn btn-success  waves-effect waves-light">
                                    <span>@lang('salary_sheet.generate_payslip')</span></button>
                            </a>
                        @endif
                    </td>

                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="10">@lang('common.no_data_available') !</td>
            </tr>
        @endif
        </tbody>
    </table>
    @if(count($staffSalaryDetails)>0)
        <div class="text-center">

        </div>
    @endif
</div>