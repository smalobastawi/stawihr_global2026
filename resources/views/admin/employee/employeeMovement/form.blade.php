@extends('admin.master')
@section('content')

@section('title')

@if(isset($editModeData))
    @lang('promotion.edit_employee_promotion')
@else
	@lang('promotion.add_employee_promotion')
@endif

@endsection

	<div class="container-fluid">
		<div class="row bg-title">
			<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
				<ol class="breadcrumb">
					<li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
					<li>@yield('title')</li>

				</ol>
			</div>
			<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
				<a href="{{route('promotion.index')}}"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i class="fa fa-list-ul" aria-hidden="true"></i>  @lang('promotion.view_employee_promotion')</a>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-info">
					<div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
					<div class="panel-wrapper collapse in" aria-expanded="true">
						<div class="panel-body">

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
									<strong>{{ session()->get('error') }}</strong>
								</div>
							@endif

							@if(isset($editModeData))
								<form action="{{ route('promotion.update', $editModeData->promotion_id) }}" method="POST" enctype="multipart/form-data" id="promotionForm">
									@csrf
									@method('PUT')
								@else
									<form action="{{ route('promotion.store') }}" method="POST" enctype="multipart/form-data" id="promotionForm">
										@csrf
								@endif

							<div class="form-body">

								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<label for="exampleInput">@lang('common.employee_name')<span class="validateRq">*</span></label>
											<select name="employee_id" class="form-control employee_id required select2">
												<option value="">-- Select --</option>
												@foreach($employeeList as $key => $value)
													<option value="{{ $key }}" {{ old('employee_id', isset($editModeData) ? $editModeData->employee_id : '') == $key ? 'selected' : '' }}>{{ $value }}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="exampleInput">@lang('promotion.current_department')<span class="validateRq">*</span></label>
											<select name="current_department" id="current_department" class="form-control current_department required" style="pointer-events: none">
												@foreach($departmentList as $key => $value)
													<option value="{{ $key }}" {{ old('current_department', isset($editModeData) ? $editModeData->current_department : '') == $key ? 'selected' : '' }}>{{ $value }}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="exampleInput">@lang('promotion.current_designation')<span class="validateRq">*</span></label>
											<select name="current_designation" id="current_designation" class="form-control current_designation required" style="pointer-events: none">
												@foreach($designationList as $key => $value)
													<option value="{{ $key }}" {{ old('current_designation', isset($editModeData) ? $editModeData->current_designation : '') == $key ? 'selected' : '' }}>{{ $value }}</option>
												@endforeach
											</select>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<label for="exampleInput">@lang('promotion.current_salary')<span class="validateRq">*</span></label>
											<input type="text" name="current_salary" id="current_salary" class="form-control required current_salary" readonly="readonly" placeholder="{{ __('promotion.current_salary') }}" value="{{ old('current_salary', isset($editModeData) ? $editModeData->current_salary : '') }}">
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="exampleInput">@lang('promotion.new_salary')<span class="validateRq">*</span></label>
											<input type="text" name="new_salary" id="new_salary" class="form-control required new_salary" placeholder="{{ __('promotion.new_salary') }}" value="{{ old('new_salary', isset($editModeData) ? $editModeData->new_salary : '') }}">
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="exampleInput">@lang('promotion.promoted_department')<span class="validateRq">*</span></label>
											<select name="promoted_department" class="form-control promoted_department required select2">
												<option value="">-- Select --</option>
												@foreach($departmentList as $key => $value)
													<option value="{{ $key }}" {{ old('promoted_department', isset($editModeData) ? $editModeData->promoted_department : '') == $key ? 'selected' : '' }}>{{ $value }}</option>
												@endforeach
											</select>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<label for="exampleInput">@lang('promotion.promoted_designation')<span class="validateRq">*</span></label>
											<select name="promoted_designation" class="form-control promoted_designation required select2">
												<option value="">-- Select --</option>
												@foreach($designationList as $key => $value)
													<option value="{{ $key }}" {{ old('promoted_designation', isset($editModeData) ? $editModeData->promoted_designation : '') == $key ? 'selected' : '' }}>{{ $value }}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="col-md-4">
										<label for="exampleInput">@lang('promotion.promotion_date')<span class="validateRq">*</span></label>
										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
											<input type="text" name="promotion_date" class="form-control required dateField" readonly="readonly" placeholder="{{ __('promotion.promotion_date') }}" value="{{ old('promotion_date', isset($editModeData) ? dateConvertDBtoForm($editModeData->promotion_date) : '') }}">
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="exampleInput">@lang('warning.description')</label>
											<textarea name="description" id="description" class="form-control description" cols="50" rows="3" placeholder="{{ __('warning.description') }}">{{ old('description', isset($editModeData) ? $editModeData->description : '') }}</textarea>
										</div>
									</div>
								</div>

								<div class="form-actions">
									<div class="row">
										<div class="col-md-12">
											@if(isset($editModeData))
												@if($editModeData->status ==1)
													<input type="submit" name="update" class="btn btn-info btn_style" value="@lang('common.update')">
													<input name="submit" type="submit" class="btn btn-info btn_style" value="@lang('common.submit')">
												@endif
											@else
												<button type="submit" class="btn btn-info btn_style"><i class="fa fa-check"></i> @lang('common.save')</button>
											@endif
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

		$(document).on("change",".employee_id",function(){
			var employee_id  = $('.employee_id').val();
			if(employee_id  !='') {
				var action = "{{ route('employeeMovement.findEmployeeInfo') }}";
				$.ajax({
					type: 'POST',
					url: action,
					data: {'employee_id': employee_id, '_token': $('input[name=_token]').val()},
					success: function (data) {
						$('#current_department').val(data.department.department_id).trigger("change");
						$('#current_designation').val(data.designation.designation_id).trigger("change");
						$('#current_salary').val(data.current_salary);
					}
				});
			}else{
				$('#current_department').val('');
				$('#current_designation').val('');
                $('#current_salary').val('');
			}
		});

		@if(isset($editModeData))
			@if($editModeData->status == 2)
				{!!  "$('#promotionForm').find('input').attr('readonly', true);" !!}
				{!!  "$('#promotionForm').find('textarea').attr('readonly', true);"!!}
				{!!  "$('#promotionForm').find('select').css('pointer-events', 'none');"!!}
				{!!  "$('#promotionForm').find('.select2').removeClass('select2');"!!}

			@endif
		@endif

	</script>
@endsection
