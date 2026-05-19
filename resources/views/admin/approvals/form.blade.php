 @extends('admin.master')

@section('title')

@if(isset($editModeData))
@lang('deduction.edit_advance')
@else
@lang('deduction.add_advance')
@endif

@endsection
@section('content')
	<div class="container-fluid">
		<div class="row bg-title">
			<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
				<ol class="breadcrumb">
					<li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
					<li>@yield('title')</li>
				</ol>
			</div>
			<div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
				<a href="{{route('advances.index')}}"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i class="fa fa-list-ul" aria-hidden="true"></i> View Advances</a>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-info">
					<div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
					<div class="panel-wrapper collapse in" aria-expanded="true">
						<div class="panel-body">
					@php
						$selectedAdvanceName = old('name', isset($editModeData->name) ? (string) $editModeData->name : '');
						$selectedEmployeeId = old('employee_id', isset($editModeData->employee->employee_id) ? (string) $editModeData->employee->employee_id : '');
						$amountValue = old('amount', isset($editModeData) ? $editModeData->amount : '');
						$monthValue = old('month', isset($editModeData) ? $editModeData->month : '');
					@endphp
					@if(isset($editModeData))
						<form action="{{ route('advances.update', $editModeData->id) }}" method="POST" enctype="multipart/form-data" class="form-horizontal" id="salaryAdvanceForm">
							@csrf
							@method('PUT')
					@else
						<form action="{{ route('advances.store') }}" method="POST" enctype="multipart/form-data" class="form-horizontal" id="salaryAdvanceForm">
							@csrf
					@endif
								<div class="form-body">
									<div class="row">
										<div class="col-md-offset-2 col-md-6">
											@if($errors->any())
												<div class="alert alert-danger alert-dismissible" role="alert">
													<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
													@foreach($errors->all() as $error)
														<strong>{!! $error !!}</strong><br>
													@endforeach
												</div>
											@endif
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
										</div>
									</div>
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label class="control-label col-md-4">@lang('deduction.advance_name')<span class="validateRq">*</span></label>
								<div class="col-md-8">
									<select name="name" class="form-control advance_type_name select2 required">
										<option value="">@lang('common.please_select')</option>
										@foreach($advanceTypes as $value => $label)
											@php
												$optionValue = is_int($value) ? (string) $label : (string) $value;
												$optionLabel = is_int($value) ? $label : $label;
											@endphp
											<option value="{{ $optionValue }}" {{ $optionValue === (string) $selectedAdvanceName ? 'selected' : '' }}>{{ $optionLabel }}</option>
										@endforeach
									</select>
								</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label class="control-label col-md-4">@lang('deduction.employee_name')<span class="validateRq">*</span></label>
								<div class="col-md-8">
									<select name="employee_id" class="form-control employee_id select2 required">
										<option value="">@lang('common.please_select')</option>
										@foreach($employeeList as $value => $label)
											@php
												$optionValue = is_int($value) ? (string) $label : (string) $value;
												$optionLabel = is_int($value) ? $label : $label;
											@endphp
											<option value="{{ $optionValue }}" {{ $optionValue === (string) $selectedEmployeeId ? 'selected' : '' }}>{{ $optionLabel }}</option>
										@endforeach
									</select>
								</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label class="control-label col-md-4">@lang('deduction.advance_amount')<span class="validateRq">*</span></label>
								<div class="col-md-8">
									<input type="number" name="amount" id="amount" class="form-control required amount" placeholder="{{ __('deduction.advance_amount') }}" value="{{ $amountValue }}" step="0.01" min="0">
								</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label class="control-label col-md-4">@lang('deduction.advance_month')<span class="validateRq">*</span></label>
												<div class="col-md-8">
													<input type="text" name="month" value="{{ old('month') }}" class="form-control monthField" id="month" placeholder="{{ __('common.month') }}" />
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="form-actions">
									<div class="row">
										<div class="col-md-8">
											<div class="row">
												<div class="col-md-offset-4 col-md-8">
													@if(isset($editModeData))
														<button type="submit" class="btn btn-info btn_style"><i class="fa fa-pencil"></i> @lang('common.update')</button>
													@else
														<button type="submit" class="btn btn-info btn_style"><i class="fa fa-check"></i> @lang('common.save')</button>
													@endif
												</div>
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
	</div>
@endsection
@section('page_scripts')
	<script>
        jQuery(function (){
            $("#salaryAdvanceForm").validate();

        });
	</script>
@endsection


 
