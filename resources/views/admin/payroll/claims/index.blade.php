@extends('admin.master')
@section('content')
@section('title')
Payroll Claims
@endsection
	<div class="container-fluid">
		<div class="row bg-title">
			<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
			   <ol class="breadcrumb">
					<li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
					<li>@yield('title')</li>
				</ol>
			</div>	
			<div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
				<a href="{{ route('payroll.claims.create') }}"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i class="fa fa-plus-circle" aria-hidden="true"></i> Add Payroll Claim</a>
			</div>	
		</div>

		<!-- Filter Section -->
		<div class="row">
			<div class="col-sm-12">
				<div class="panel panel-default">
					<div class="panel-heading"><i class="fa fa-filter fa-fw"></i> @lang('common.filters')</div>
					<div class="panel-wrapper collapse" aria-expanded="false">
						<div class="panel-body">
							<form method="GET" action="{{ route('payroll.claims.index') }}" class="form-horizontal">
								<div class="row">
									<div class="col-md-3">
										<div class="form-group">
											<label class="control-label">Employee</label>
											<select name="employee_id" class="form-control">
												<option value="">@lang('common.select_employee')</option>
												@foreach($employees as $employee)
													<option value="{{ $employee->employee_id }}" {{ request('employee_id') == $employee->employee_id ? 'selected' : '' }}>
														{{ $employee->staff_no }} - {{ $employee->fullName() }} 
													</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label class="control-label">Claim Type</label>
											<select name="claim_type" class="form-control">
												<option value="">All Types</option>
												@foreach($claimTypes as $key => $type)
													<option value="{{ $key }}" {{ request('claim_type') == $key ? 'selected' : '' }}>
														{{ $type }}
													</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group">
											<label class="control-label">@lang('common.status')</label>
											<select name="status" class="form-control">
												<option value="">@lang('common.all_status')</option>
												@foreach($statuses as $key => $status)
													<option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
														{{ $status }}
													</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group">
											<label class="control-label">Claim Year</label>
											<select name="claim_year" class="form-control">
												<option value="">@lang('common.all_years')</option>
												@for($year = date('Y'); $year >= 2020; $year--)
													<option value="{{ $year }}" {{ request('claim_year') == $year ? 'selected' : '' }}>
														{{ $year }}
													</option>
												@endfor
											</select>
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group">
											<label class="control-label">Claim Month</label>
											<select name="claim_month" class="form-control">
												<option value="">@lang('common.all_months')</option>
												@for($month = 1; $month <= 12; $month++)
													<option value="{{ $month }}" {{ request('claim_month') == $month ? 'selected' : '' }}>
														{{ date('F', mktime(0, 0, 0, $month, 1)) }}
													</option>
												@endfor
											</select>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label">@lang('common.search')</label>
											<input type="text" name="search" class="form-control" placeholder="Search by title, reference, or employee name" value="{{ request('search') }}">
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label class="control-label">Date From</label>
											<input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label class="control-label">Date To</label>
											<input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<button type="submit" class="btn btn-info"><i class="fa fa-search"></i> @lang('common.filter')</button>
											<a href="{{ route('payroll.claims.index') }}" class="btn btn-default"><i class="fa fa-refresh"></i> @lang('common.reset')</a>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
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
									<i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
								</div>
							@endif
							<div class="table-responsive">
								<table id="myTable" class="table table-bordered">
									<thead>
										 <tr class="tr_header">
											<th>@lang('common.serial')</th>
											<th>Reference</th>
											<th>Employee</th>
											<th>Claim Title</th>
											<th>Type</th>
											<th>Amount</th>
											<th>Claim Period</th>
											<th>Recovery</th>
											<th>@lang('common.status')</th>
											<th>@lang('common.action')</th>
										</tr>
									</thead>
									<tbody>
										{!! $sl=null !!}
										@foreach($claims AS $claim)
											<tr class="{!! $claim->id !!}">
												<td style="width: 50px;">{!! ++$sl !!}</td>
												<td>
													<strong>{{ $claim->reference_number }}</strong><br>
													<small>{{ $claim->created_at->format('M d, Y') }}</small>
												</td>
												<td>
													<strong>{{ $claim->employee->fullName() ?? '' }} </strong><br>
													<small>{{ $claim->employee->staff_no ?? '' }}</small>
												</td>
												<td>
													<strong>{{ $claim->claim_title }}</strong>
													@if($claim->description)
														<br><small class="text-muted">{{ \Str::limit($claim->description, 50) }}</small>
													@endif
												</td>
												<td>
													<span class="label label-info">
														{{ $claimTypes[$claim->claim_type] ?? ucfirst($claim->claim_type) }}
													</span>
												</td>
												<td>
													<strong>{{ $claim->currency }} {{ $claim->formatted_claim_amount }}</strong>
													@if($claim->amount_recovered > 0)
														<br><small class="text-success">Recovered: {{ $claim->formatted_amount_recovered }}</small>
													@endif
												</td>
												<td>
													{{ date('F Y', mktime(0, 0, 0, $claim->claim_month, 1, $claim->claim_year)) }}
												</td>
												<td>
													@if($claim->recovery_method == 'lump_sum')
														<span class="label label-default">Lump Sum</span>
													@else
														<span class="label label-primary">{{ $claim->recovery_periods }} Installments</span>
														@if($claim->recovery_percentage > 0)
															<br><small>{{ $claim->recovery_percentage }}% Recovered</small>
														@endif
													@endif
												</td>
												<td>
													<span class="label label-{{ $claim->status == 'approved' ? 'success' : ($claim->status == 'pending_approval' ? 'warning' : ($claim->status == 'active' ? 'info' : ($claim->status == 'rejected' ? 'danger' : 'default'))) }}">
														{{ $claim->status_label }}
													</span>
												</td>
												
												<td style="width: 200px;">
													<a href="{{ route('payroll.claims.show', $claim->id) }}" class="btn btn-info btn-xs btnColor" title="@lang('common.view')">
														<i class="fa fa-eye" aria-hidden="true"></i>
													</a>
													@if(in_array($claim->status, ['draft', 'pending_approval']))
														<a href="{{ route('payroll.claims.edit', $claim->id) }}" class="btn btn-success btn-xs btnColor" title="@lang('common.edit')">
															<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
														</a>
													@endif
													
													@if($claim->status == 'draft')
														<button type="button" class="btn btn-primary btn-xs btnColor submit-btn" data-id="{{ $claim->id }}" title="Submit for Approval">
															<i class="fa fa-paper-plane" aria-hidden="true"></i>
														</button>
													@endif
													
													@if($claim->status == 'pending_approval')
														<button type="button" class="btn btn-success btn-xs btnColor approve-btn" data-id="{{ $claim->id }}" title="@lang('common.approve')">
															<i class="fa fa-check" aria-hidden="true"></i>
														</button>
														<button type="button" class="btn btn-warning btn-xs btnColor reject-btn" data-id="{{ $claim->id }}" title="Reject">
															<i class="fa fa-times" aria-hidden="true"></i>
														</button>
													@endif
													
													@if($claim->status == 'approved')
														<button type="button" class="btn btn-info btn-xs btnColor activate-btn" data-id="{{ $claim->id }}" title="Activate Recovery">
															<i class="fa fa-play" aria-hidden="true"></i>
														</button>
													@endif
													
													@if($claim->status == 'draft')
														<a href="javascript:void(0)" data-id="{{ $claim->id }}" class="delete btn btn-danger btn-xs deleteBtn btnColor" title="@lang('common.delete')">
															<i class="fa fa-trash-o" aria-hidden="true"></i>
														</a>
													@endif
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
								
								<!-- Pagination -->
								@if($claims->hasPages())
									<div class="text-center">
										{{ $claims->appends(request()->query())->links() }}
									</div>
								@endif
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Submit for Approval Modal -->
	<div class="modal fade" id="submitModal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Submit Claim for Approval</h4>
				</div>
				<form id="submitForm" method="POST">
					@csrf
					<div class="modal-body">
						<p>Are you sure you want to submit this claim for approval?</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">@lang('common.cancel')</button>
						<button type="submit" class="btn btn-primary">Submit</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Approval Modal -->
	<div class="modal fade" id="approvalModal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Approve Claim</h4>
				</div>
				<form id="approvalForm" method="POST">
					@csrf
					<div class="modal-body">
						<div class="form-group">
							<label for="approval_notes">@lang('common.notes')</label>
							<textarea name="approval_notes" id="approval_notes" class="form-control" rows="3" placeholder="Enter approval notes (optional)"></textarea>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">@lang('common.cancel')</button>
						<button type="submit" class="btn btn-success">@lang('common.approve')</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Reject Modal -->
	<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Reject Claim</h4>
				</div>
				<form id="rejectForm" method="POST">
					@csrf
					<div class="modal-body">
						<div class="form-group">
							<label for="reject_notes">Rejection Reason</label>
							<textarea name="approval_notes" id="reject_notes" class="form-control" rows="3" placeholder="Enter reason for rejection" required></textarea>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">@lang('common.cancel')</button>
						<button type="submit" class="btn btn-warning">Reject</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Activate Recovery Modal -->
	<div class="modal fade" id="activateModal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Activate Claim for Recovery</h4>
				</div>
				<form id="activateForm" method="POST">
					@csrf
					<div class="modal-body">
						<div class="form-group">
							<label for="activation_reference">Activation Reference</label>
							<input type="text" name="activation_reference" id="activation_reference" class="form-control" placeholder="Enter activation reference (optional)">
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">@lang('common.cancel')</button>
						<button type="submit" class="btn btn-info">Activate Recovery</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection

@section('page_scripts')
<script>
	$(document).ready(function() {
		// Toggle filter panel
		$('.panel-heading').click(function() {
			$(this).next('.panel-wrapper').collapse('toggle');
		});

		// Submit for approval
		$('.submit-btn').click(function() {
			var claimId = $(this).data('id');
			$('#submitForm').attr('action', '{{ route("payroll.claims.submit", ":id") }}'.replace(':id', claimId));
			$('#submitModal').modal('show');
		});

		// Approve claim
		$('.approve-btn').click(function() {
			var claimId = $(this).data('id');
			$('#approvalForm').attr('action', '{{ route("payroll.claims.approve", ":id") }}'.replace(':id', claimId));
			$('#approvalModal').modal('show');
		});

		// Reject claim
		$('.reject-btn').click(function() {
			var claimId = $(this).data('id');
			$('#rejectForm').attr('action', '{{ route("payroll.claims.reject", ":id") }}'.replace(':id', claimId));
			$('#rejectModal').modal('show');
		});

		// Activate recovery
		$('.activate-btn').click(function() {
			var claimId = $(this).data('id');
			$('#activateForm').attr('action', '{{ route("payroll.claims.activate", ":id") }}'.replace(':id', claimId));
			$('#activateModal').modal('show');
		});

		// Delete claim
		$('.deleteBtn').click(function() {
			var claimId = $(this).data('id');
			if (confirm('Are you sure you want to delete this claim?')) {
				$.ajax({
					url: '{{ route("payroll.claims.destroy", ":id") }}'.replace(':id', claimId),
					type: 'DELETE',
					data: {
						'_token': '{{ csrf_token() }}'
					},
					success: function(response) {
						location.reload();
					},
					error: function(xhr) {
						alert('Error deleting claim');
					}
				});
			}
		});
	});
</script>
@endsection