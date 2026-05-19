@extends('admin.master')
@section('content')
@section('title')
@lang('payment.my_payroll')
@endsection
	<div class="container-fluid">
		<div class="row bg-title">
			<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
			   <ol class="breadcrumb">
					<li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
					<li>@yield('title')</li>
				</ol>
			</div>	
			<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
				<a href="{{route('ess.loans.index')}}"
				   class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
							class="fa fa-list-ul" aria-hidden="true"></i>Loans</a>
			</div>

		</div>
					
		<div class="row">
			<div class="col-sm-12">
				<div class="panel panel-info">
					<div class="panel-heading"><i class="mdi mdi-table fa-fw"></i>@lang('payment.my_payroll')</div>
					<div class="panel-wrapper collapse in" aria-expanded="true">
						<div class="panel-body">
							@if(count($results)>0)
							
							@endif
							<div class="table-responsive">
								<table  id="" class="table table-bordered">
									<thead>
										 <tr class="tr_header">
											<th>@lang('common.serial')</th>
											<th>@lang('common.month')</th>
											<th>@lang('employee.photo')</th>
											<th>@lang('common.employee_name')</th>
											<th>@lang('salary_sheet.basic_salary')</th>
											<th>@lang('salary_sheet.gross_salary')</th>
											<th>@lang('common.status')</th>
											<th>@lang('common.action')</th>
										</tr>
									</thead>
									<tbody>
										@if(count($results)>0)
											{!! $sl=null !!}
											@foreach($results AS $value)
												<tr>
													<td style="width: 100px;">{!! ++$sl !!}</td>
													<td>
                                                       {{ $value->payrollPeriod->name }}
													</td>
													<td>
														@if($value->employee->photo != '')
															<img style=" width: 70px; " src="{!! asset('uploads/employeePhoto/'.$value->employee->photo) !!}" alt="user-img" class="img-circle">
														@else
															<img style=" width: 70px; " src="{!! asset('admin_assets/img/default.png') !!}" alt="user-img" class="img-circle">
														@endif
													</td>
											<td>@if(isset($value->employee->first_name)){!!  $value->employee->first_name !!} {{$value->employee->last_name}}@endif</td>
											<td>{!! $value->basic_salary !!}</td>
													<td>{!! $value->gross_salary !!}</td>
													<td>
														<span class="label label-success">@lang('salary_sheet.paid')</span>
													</td>
													<td style="width: 100px">
															<a href="{{route('ess.payroll.payslip.generate',$value->id)}}" target="_blank"><button  class="btn btn-success waves-effect waves-light"><span>Download Payslip</span> </button></a>
													</td>

												</tr>
											@endforeach
										@else
											<tr>
												<td colspan="8">@lang('common.no_data_available') ! </td>
											</tr>
										@endif
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection


