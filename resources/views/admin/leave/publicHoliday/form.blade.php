@extends('admin.master')
@section('content')
@section('title')
@if(isset($editModeData))
	@lang('holiday.edit_public_holiday')
@else
	@lang('holiday.add_public_holiday')
@endif

@endsection
<style>
	.dateField{z-index: 99 !important}
</style>

	<div class="container-fluid">
		<div class="row bg-title">
			<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
				<ol class="breadcrumb">
					<li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
					<li>@yield('title')</li>

				</ol>
			</div>
			<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
				<a href="{{route('publicHoliday.index')}}"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i class="fa fa-list-ul" aria-hidden="true"></i>  @lang('holiday.view_public_holiday')</a>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-info">
					<div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
					<div class="panel-wrapper collapse in" aria-expanded="true">
						<div class="panel-body">
								@if(isset($editModeData))
									<form action="{{ route('publicHoliday.update', ) }}" method="POST" enctype="multipart/form-data" id="publicHolidayForm" class="form-horizontal">
@csrf
@method('PUT')

								@else
									<form action="{{ route('publicHoliday.store') }}" method="POST" enctype="multipart/form-data" id="publicHolidayForm" class="form-horizontal">
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
									<br>
									<br>
									<br>
									<br>
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label class="control-label col-md-4">@lang('holiday.holiday_name')<span class="validateRq">*</span></label>
												<div class="col-md-8">
													<select name="holiday_id" class="form-control holiday_id select2 required">
@foreach($holidayList as $__key => $__value)
<option value="{{ $__key }}" {{ (string)Request::old('holiday_id') == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label class="control-label col-md-4">@lang('common.from_date')<span class="validateRq">*</span></label>
												<div class="col-md-8">
													<input type="text" name="from_date" value="{{ (isset($editModeData)) ? dateConvertDBtoForm($editModeData->from_date) :  Request::old('from_date') }}">
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label class="control-label col-md-4">@lang('common.to_date')<span class="validateRq">*</span></label>
												<div class="col-md-8">
													<input type="text" name="to_date" value="{{ (isset($editModeData)) ? dateConvertDBtoForm($editModeData->to_date) :  Request::old('to_date') }}">
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label class="control-label col-md-4">@lang('holiday.comment')</label>
												<div class="col-md-8">
													<textarea name="comment">{{ Request::old('comment') }}</textarea>
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


