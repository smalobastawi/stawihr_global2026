@extends('admin.master')
@section('content')
@section('title')
Payroll Claim Recoveries
@endsection
	<div class="container-fluid">
		<div class="row bg-title">
			<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
			   <ol class="breadcrumb">
					<li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
					<li><a href="{{ route('payroll.claims.index') }}">Payroll Claims</a></li>
					<li>@yield('title')</li>
				</ol>
			</div>	
			<div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
				<a href="{{ route('payroll.claims.index') }}"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i class="fa fa-list-ul" aria-hidden="true"></i> View Claims</a>
			</div>	
		</div>

		<!-- Period Selection -->
		<div class="row">
			<div class="col-sm-12">
				<div class="panel panel-default">
					<div class="panel-heading"><i class="fa fa-calendar fa-fw"></i> Recovery Period</div>
					<div class="panel-wrapper collapse in" aria-expanded="true">
						<div class="panel-body">
							<form method="GET" action="{{ route('payroll.claims.recoveries') }}" class="form-horizontal">
								<div class="row">
									<div class="col-md-3">
										<div class="form-group">
											<label class="control-label">Recovery Year</label>
											<select name="year" class="form-control">
												@for($y = date('Y'); $y >= 2020; $y--)
													<option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
														{{ $y }}
													</option>
												@endfor
											</select>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label class="control-label">Recovery Month</label>
											<select name="month" class="form-control">
												@for($m = 1; $m <= 12; $m++)
													<option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
														{{ date('F', mktime(0, 0, 0, $m, 1)) }}
													</option>
												@endfor
											</select>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label class="control-label">&nbsp;</label><br>
											<button type="submit" class="btn btn-info"><i class="fa fa-search"></i> Load Period</button>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label class="control-label">Total Recoveries</label>
											<div class="form-control-static">
												<strong>KES {{ number_format($totalRecoveries, 2) }}</strong>
											</div>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Recovery Summary -->
		<div class="row">
			<div class="col-md-3 col-sm-6">
				<div class="white-box">
					<h3 class="box-title">Pending Recoveries</h3>
					<ul class="list-inline two-part">
						<li><i class="icon-clock text-warning"></i></li>
						<li class="text-right"><span class="counter">{{ $recoveries->where('status', 'pending')->count() }}</span></li>
					</ul>
				</div>
			</div>
			<div class="col-md-3 col-sm-6">
				<div class="white-box">
					<h3 class="box-title">Processed Recoveries</h3>
					<ul class="list-inline two-part">
						<li><i class="icon-check text-success"></i></li>
						<li class="text-right"><span class="counter">{{ $recoveries->where('status', 'processed')->count() }}</span></li>
					</ul>
				</div>
			</div>
			<div class="col-md-3 col-sm-6">
				<div class="white-box">
					<h3 class="box-title">Total Amount</h3>
					<ul class="list-inline two-part">
						<li><i class="icon-wallet text-info"></i></li>
						<li class="text-right"><span class="counter">{{ number_format($recoveries->sum('scheduled_amount'), 0) }}</span></li>
					</ul>
				</div>
			</div>
			<div class="col-md-3 col-sm-6">
				<div class="white-box">
					<h3 class="box-title">Processed Amount</h3>
					<ul class="list-inline two-part">
						<li><i class="icon-check text-success"></i></li>
						<li class="text-right"><span class="counter">{{ number_format($recoveries->where('status', 'processed')->sum('actual_amount'), 0) }}</span></li>
					</ul>
				</div>
			</div>
		</div>
					
		<div class="row">
			<div class="col-sm-12">
				<div class="panel panel-info">
					<div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title') - {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</div>
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
									<i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
								</div>
							@endif

							@if($recoveries->count() > 0)
								<div class="table-responsive">
									<table id="myTable" class="table table-bordered">
										<thead>
											 <tr class="tr_header">
												<th>@lang('common.serial')</th>
												<th>Employee</th>
												<th>Claim</th>
												<th>Installment</th>
												<th>Scheduled Amount</th>
												<th>Actual Amount</th>
												<th>Balance</th>
												<th>@lang('common.status')</th>
												<th>@lang('common.action')</th>
											</tr>
										</thead>
										<tbody>
											{!! $sl=null !!}
											@foreach($recoveries AS $recovery)
												<tr class="{!! $recovery->id !!}">
													<td style="width: 50px;">{!! ++$sl !!}</td>
													<td>
														<strong>{{ $recovery->employee->first_name ?? '' }} {{ $recovery->employee->last_name ?? '' }}</strong><br>
														<small>{{ $recovery->employee->staff_no ?? '' }}</small>
													</td>
													<td>
														<strong>{{ $recovery->payrollClaim->claim_title }}</strong><br>
														<small>{{ $recovery->payrollClaim->reference_number }}</small><br>
														<span class="label label-info">{{ \App\Models\Payroll\PayrollClaim::getClaimTypesArray()[$recovery->payrollClaim->claim_type] ?? ucfirst($recovery->payrollClaim->claim_type) }}</span>
													</td>
													<td>
														<strong>{{ $recovery->installment_number }}</strong> of {{ $recovery->payrollClaim->recovery_periods }}<br>
														<small>{{ $recovery->recovery_period_label }}</small>
													</td>
													<td>
														<strong>KES {{ $recovery->formatted_scheduled_amount }}</strong>
													</td>
													<td>
														@if($recovery->actual_amount > 0)
															<strong>KES {{ $recovery->formatted_actual_amount }}</strong>
														@else
															<span class="text-muted">Not processed</span>
														@endif
													</td>
													<td>
														<strong>KES {{ number_format($recovery->balance_amount, 2) }}</strong>
													</td>
													<td>
														<span class="label label-{{ $recovery->status == 'processed' ? 'success' : ($recovery->status == 'pending' ? 'warning' : 'default') }}">
															{{ $recovery->status_label }}
														</span>
													</td>
													
													<td style="width: 150px;">
														@if($recovery->status == 'pending')
															<button type="button" class="btn btn-success btn-xs btnColor process-btn" data-id="{{ $recovery->id }}" title="Process Recovery">
																<i class="fa fa-check" aria-hidden="true"></i>
															</button>
															<button type="button" class="btn btn-warning btn-xs btnColor skip-btn" data-id="{{ $recovery->id }}" title="Skip This Period">
																<i class="fa fa-forward" aria-hidden="true"></i>
															</button>
														@endif
														
														@if($recovery->status == 'processed')
															<span class="badge badge-success">Completed</span><br>
															<small>{{ $recovery->processed_at->format('M d, Y') }}</small>
														@endif
														
														@if($recovery->notes)
															<button type="button" class="btn btn-info btn-xs btnColor" title="{{ $recovery->notes }}" data-toggle="tooltip">
																<i class="fa fa-info" aria-hidden="true"></i>
															</button>
														@endif
													</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							@else
								<div class="alert alert-info">
									<i class="fa fa-info-circle"></i> No recoveries scheduled for {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}.
								</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Process Recovery Modal -->
	<div class="modal fade" id="processModal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Process Recovery</h4>
				</div>
				<form id="processForm" method="POST">
					@csrf
					<div class="modal-body">
						<div class="form-group">
							<label for="actual_amount">Actual Recovery Amount</label>
							<input type="number" name="actual_amount" id="actual_amount" class="form-control" placeholder="Enter actual amount recovered" min="0" step="0.01" required>
						</div>
						<div class="form-group">
							<label for="payroll_reference">Payroll Reference</label>
							<input type="text" name="payroll_reference" id="payroll_reference" class="form-control" placeholder="Enter payroll reference (optional)">
						</div>
						<div class="form-group">
							<label for="notes">Notes</label>
							<textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Enter any notes about this recovery"></textarea>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">@lang('common.cancel')</button>
						<button type="submit" class="btn btn-success">Process Recovery</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Skip Recovery Modal -->
	<div class="modal fade" id="skipModal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Skip Recovery</h4>
				</div>
				<form id="skipForm" method="POST">
					@csrf
					<div class="modal-body">
						<div class="form-group">
							<label for="skip_reason">Skip Reason</label>
							<textarea name="reason" id="skip_reason" class="form-control" rows="3" placeholder="Enter reason for skipping this recovery period" required></textarea>
						</div>
						<div class="alert alert-warning">
							<i class="fa fa-warning"></i> Skipping this recovery will not recover any amount for this period. This action should only be used when the employee is not on payroll for this period.
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">@lang('common.cancel')</button>
						<button type="submit" class="btn btn-warning">Skip Recovery</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection

@section('page_scripts')
<script>
	$(document).ready(function() {
		// Initialize tooltips
		$('[data-toggle="tooltip"]').tooltip();

		// Process recovery
		$('.process-btn').click(function() {
			var recoveryId = $(this).data('id');
			var row = $(this).closest('tr');
			var scheduledAmount = row.find('td:eq(4)').text().replace('KES ', '').replace(',', '');
			
			$('#actual_amount').val(scheduledAmount);
			$('#processForm').attr('action', '{{ route("payroll.claims.processRecovery", ":id") }}'.replace(':id', recoveryId));
			$('#processModal').modal('show');
		});

		// Skip recovery
		$('.skip-btn').click(function() {
			var recoveryId = $(this).data('id');
			$('#skipForm').attr('action', '{{ route("payroll.claims.skipRecovery", ":id") }}'.replace(':id', recoveryId));
			$('#skipModal').modal('show');
		});

		// Auto-fill scheduled amount when processing
		$('#processModal').on('shown.bs.modal', function() {
			$('#actual_amount').focus();
		});

		// Form validation
		$('#processForm').on('submit', function(e) {
			var actualAmount = parseFloat($('#actual_amount').val());
			if (actualAmount <= 0) {
				e.preventDefault();
				alert('Please enter a valid amount greater than 0');
				return false;
			}
		});
	});
</script>
@endsection