@extends('admin.master')
@section('content')
@section('title')
@if(isset($editModeData))

	@lang('holiday.edit_weekly_holiday')
@else

	@lang('holiday.add_weekly_holiday')
@endif
@endsection

	<div class="container-fluid">
		<div class="row bg-title">
			<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
				<ol class="breadcrumb">
					<li class="active breadcrumbColor"><a href="{{url('dashboard')}}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
					<li>@yield('title')</li>

				</ol>
			</div>
			<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
				<a href="{{route('weeklyHoliday.index')}}"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i class="fa fa-list-ul" aria-hidden="true"></i>  @lang('holiday.view_weekly_holiday')</a>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-info">
					<div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
					<div class="panel-wrapper collapse in" aria-expanded="true">
						<div class="panel-body">
							@if(isset($editModeData))
								<form action="{{ route('weeklyHoliday.update', ) }}" method="POST" enctype="multipart/form-data" id="weeklyHolidayForm" class="form-horizontal">
@csrf
@method('PUT')

							@else
								<form action="{{ route('weeklyHoliday.store') }}" method="POST" enctype="multipart/form-data" id="weeklyHoliday" class="form-horizontal">
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
												<label class="control-label col-md-4">@lang('holiday.holiday_name')<span class="validateRq">*</span></label>
												<div class="col-md-8">
													<select name="day_name" class="form-control day_name select2 required">
@foreach($weekList as $__key => $__value)
<option value="{{ $__key }}" {{ (string)Request::old('day_name') == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label class="control-label col-md-4">@lang('common.status')<span class="validateRq">*</span></label>
												<div class="col-md-8">
													<select name="status" class="form-control status select2 required">
@foreach(array('1' => __('common.active'), '2' => __('common.inactive')) as $__key => $__value)
<option value="{{ $__key }}" {{ (string)Request::old('status') == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
												</div>
											</div>
										</div>
									</div>
								</div>
								{{-- <div class="row">
									<div class="col-md-8">
										<div class="form-group">
											<label class="control-label col-md-4">@lang('holiday.applicable_departments')</label>
											<div class="col-md-8">
												<select name="departments[]" class="form-control select2" multiple="multiple" id="departments">
@foreach($departments as $__key => $__value)
<option value="{{ $__key }}" {{ in_array($__key, isset($editModeData) ? $editModeData->departments->pluck('department_id')->all() : array_keys($departments)) ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
											</div>
										</div>
									</div>
								</div> --}}

								<div class="row">
									<div class="col-md-8">
										<div class="form-group">
											<label class="control-label col-md-4">@lang('holiday.applicable_leave_groups')</label>
											<div class="col-md-8">
												<select name="leave_groups[]" class="form-control select2" multiple="multiple" id="leave_groups">
@foreach($leaveGroups as $__key => $__value)
<option value="{{ $__key }}" {{ in_array($__key, isset($editModeData) ? $editModeData->leaveGroups->pluck('id')->all() : array_keys($leaveGroups)) ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
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
	$('.select2').select2({

	});
</script>
@endsection