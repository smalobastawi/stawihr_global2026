@extends('admin.master')

@section('title')
    StawiHR - Payroll Index
@endsection
@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="">
            {{--				<a href="{{route('calculatePaye')}}" class="btn btn-primary pull-right">Calculate Paye</a>--}}
            <a href="{{ route('generateSalarySheet.create') }}"  class="btn btn-success pull-right m-l-20  waves-effect waves-light"> <i class="fa fa-plus-circle" aria-hidden="true"></i> @lang('salary_sheet.generate_salary')</a>
            <a href="{{ route('payrollcaculator.index') }}"  class="btn btn-success pull-right m-l-20  waves-effect waves-light"> <i class="fa fa-plus-circle" aria-hidden="true"></i> Payroll Caculator</a>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if(session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if(session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
                            <div class="row">
                                <!-- panel -->
                                <div class="col-lg-3 col-xs-6">
                                    <a href="{{route('employee.index')}}">
                                        <!-- small box -->
                                    </a><div class="small-box bg-teal"><a href="#">
                                            <div class="">
                                                <b> <h2 class="text-white text-center font-bold">{{$totalPayrollGenerated}}/{{$totalEmployee}}</h2></b>
                                                <p class="text-center">Payroll Generated ({{$currentMonth}})</p>
                                            </div>

                                        </a><a href="#" class="small-box-footer">View Current Month <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                                    </div>

                                </div><!-- ./col -->

                                <div class="col-lg-3 col-xs-6">
                                    <a href="{{route('shifReportsIndex')}}">
                                        <!-- small box -->
                                    </a><div class="small-box bg-maroon"><a href="{{route('shifReportsIndex')}}">
                                            <div class="">
                                                <h2 class="text-white  text-center font-bold">{{$totalNHIFGenerated}}/{{$totalEmployee}}</h2>
                                                <p class="text-center">SHIF Report ({{$currentMonth}})</p>
                                            </div>
                                            <div class="icon" aria-hidden="true">
                                                <i class="fa fa-archive"></i>
                                            </div>
                                        </a><a href="{{route('shifReportsIndex')}}" class="small-box-footer">View all <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                                    </div>

                                </div><!-- ./col -->


                                <div class="col-lg-3 col-xs-6">
                                    <!-- small box -->
                                    <a href="{{route('nssfReportsIndex')}}">
                                    </a><div class="small-box bg-orange"><a href="{{route('nssfReportsIndex')}}">
                                            <div class="">
                                                <h2 class="text-white  text-center font-bold"> {{$totalNSSFGenerated}}/{{$totalEmployee}}</h2>
                                                <p class="text-center">NSSF Report ({{$currentMonth}})</p>
                                            </div>
                                            <div class="icon" aria-hidden="true">
                                                <i class="fa fa-address-book"></i>
                                            </div>
                                        </a>
                                        <a href="{{route('nssfReportsIndex')}}" class="small-box-footer">View all <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                                    </div>

                                </div><!-- ./col -->

                                <div class="col-lg-3 col-xs-6">
                                    <!-- small box -->

                                    <a href="{{route('ahlReportIndex')}}">
                                    </a><div class="small-box bg-purple"><a href="{{route('ahlReportIndex')}}">
                                            <div class="">
                                                <h2 class="text-white  text-center font-bold"> {{$totalAHLGenerated}}/{{$totalEmployee}}</h2>
                                                <p class="text-center">AHL Report ({{$currentMonth}})</p>
                                            </div>
                                            <div class="icon" aria-hidden="true">
                                                <i class="fa fa-anchor"></i>
                                            </div>
                                        </a> <a href="{{route('ahlReportIndex')}}" class="small-box-footer">View all <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>

                                    </div>
                                </div><!-- ./col -->


                            </div>
                        <div class="row">
                            <form method="GET" action="{{route('payrollIndex')}}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">@lang('common.month')</label>
                                    <input type="text" name="month" value="{{ request('month') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary form-control">
                                        <i class="fa fa-filter"></i> Filter
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <div class="form-group">
                                    <a href="{{route('payrollIndex')}}" class="btn btn-default form-control">
                                        <i class="fa fa-refresh"></i> Reset
                                    </a>
                                </div>
                            </div>
                            </form>
                        </div>
                        <br>
                        <div class="data">
                            @include('admin.payroll.salarySheet.pagination')
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="responsive-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title"><b>@lang('salary_sheet.payment_for')<span class="monthAndYearName"></span></b></h4> </div>
            <div class="modal-body">
                <form>
                    {{ csrf_field() }}
                    <input type="hidden" class="salary_details_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="recipient-name" class="control-label">@lang('common.employee_name')</label>
                                <input type="text" class="form-control employee_name" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="recipient-name" class="control-label">@lang('paygrade.basic_salary')</label>
                                <input type="text" class="form-control basic_salary" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="recipient-name" class="control-label">Gross Pay</label>
                                <input type="text" class="form-control gross_pay" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="recipient-name" class="control-label">@lang('salary_sheet.total_deduction')</label>
                                <input type="text" class="form-control total_deduction" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="recipient-name" class="control-label">Net Salary</label>
                                <input type="text" class="form-control gross_salary" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="recipient-name" class="control-label">@lang('salary_sheet.payment_method')</label>
                                <select class="form-control payment_method">
                                    <option value="Cash">@lang('salary_sheet.cash')</option>
                                    <option value="Cheque">@lang('salary_sheet.cheque')</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="message-text" class="control-label">@lang('salary_sheet.comments')</label>
                                <textarea class="form-control comment"></textarea>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><b>@lang('common.close')</b></button>
                <button type="button" class="btn btn-info btn_style waves-effect waves-light makePayment" data-dismiss="modal"	> <b>@lang('salary_sheet.pay')</b></button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
    <script>
        $(function() {
            $(document).on('click','[data-salary_details_id]',function(event){
                var salary_details_id 	= $(this).attr('data-salary_details_id');
                var monthAndYearName 	= $(this).attr('data-monthAndYearName');
                var employee_name 		= $(this).attr('data-employee_name');
                var basic_salary 		= $(this).attr('data-basic_salary');
                var gross_pay 		= $(this).attr('data-gross_pay');
                var total_allowance 	= $(this).attr('data-total_allowance');
                var total_deduction 	= $(this).attr('data-total_deduction');
                var gross_salary 		= $(this).attr('data-gross_salary');

                if(total_allowance==0 && basic_salary==0 && total_deduction==0 && gross_pay==0){
                    $('.basic_salary').parent().css({"display": "none"});
                    $('.gross_pay').parent().css({"display": "none"});
                    $('.total_allowance').parent().css({"display": "none"});
                    $('.total_deduction').parent().css({"display": "none"});
                    $('.comment').parent().parent().addClass('col-md-6');
                    $('.comment').parent().parent().removeClass('col-md-12');
                }else{
                    $('.basic_salary').parent().css({"display": "block"});
                    $('.gross_pay').parent().css({"display": "block"});
                    $('.total_allowance').parent().css({"display": "block"});
                    $('.total_deduction').parent().css({"display": "block"});
                    $('.comment').parent().parent().addClass('col-md-12');
                    $('.comment').parent().parent().removeClass('col-md-6');
                }

                $('.employee_name').val(employee_name);
                $('.basic_salary').val(basic_salary);
                $('.gross_pay').val(gross_pay);
                $('.total_allowance').val(total_allowance);
                $('.total_deduction').val(total_deduction);
                $('.gross_salary').val(gross_salary);
                $('.monthAndYearName').html(monthAndYearName);
                $('.salary_details_id').val(salary_details_id);

            });

            $(document).on('click','.makePayment',function(event){

                var payment_method 		 = $('.payment_method').val();
                var comment 	  		 = $('.comment').val();
                var salary_details_id 	 = $('.salary_details_id').val();
                var action 				 = "{{ route('makePayment') }}";
                $.ajax({
                    type: 'POST',
                    url: action,
                    data: {'payment_method': payment_method,'comment': comment,'salary_details_id':salary_details_id,'_token': $('input[name=_token]').val()},
                    success: function (data) {
                        if (data != 'success') {
                            $.toast({
                                heading: 'Warning',
                                text: 'An error occured, please try again. If the problem persists, contact Support for assistance.  !',
                                position: 'top-right',
                                loaderBg: '#ff6849',
                                icon: 'success',
                                hideAfter: 3000,
                                stack: 6
                            });
                            window.setTimeout(function () {
                                location.reload()
                            }, 3000)

                        } else {
                            $.toast({
                                heading: 'success',
                                text: 'Payment Paid !',
                                position: 'top-right',
                                loaderBg: '#ff6849',
                                icon: 'success',
                                hideAfter: 3000,
                                stack: 6
                            });
                            window.setTimeout(function () {
                                location.reload()
                            }, 3000);
                        }
                    }
                });
            });

        });

    </script>
@endsection

