@extends('admin.master')
@section('content')
@section('title')
@if(isset($editModeData))
@lang('award.edit_award')
@else
@lang('award.add_new_award')
@endif
@endsection
	<div class="container-fluid">
		<div class="row bg-title">
			<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
				<ol class="breadcrumb">
					<li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
					<li>@yield('title')</li>
				</ol>
			</div>
			<div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
				<a href="{{route('award.index')}}"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i class="fa fa-list-ul" aria-hidden="true"></i>  @lang('award.view_award')  </a>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-info">
					<div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
					<div class="panel-wrapper collapse in" aria-expanded="true">
						<div class="panel-body">
							@if(isset($editModeData))
								<form method="POST" action="{{ route('award.update', $editModeData->employee_award_id) }}" class="form-horizontal" id="awardForm" enctype="multipart/form-data">
@csrf
@method('PUT')
							@else
								<form method="POST" action="{{ route('award.store') }}" class="form-horizontal" id="awardForm" enctype="multipart/form-data">
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
												<label class="control-label col-md-4">@lang('award.award_name')<span class="validateRq">*</span></label>
												<div class="col-md-8">
													<select name="award_name" class="form-control award_name required select2">
@foreach(employeeAward() as $key => $value)
<option value="{{ $key }}" {{ Request::old('award_name') == $key ? 'selected' : '' }}>{{ $value }}</option>
@endforeach
</select>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label class="control-label col-md-4">@lang('common.employee_name')<span class="validateRq">*</span></label>
												<div class="col-md-8">
													<select name="employee_id" class="form-control employee_id required select2">
@foreach($employeeList as $key => $value)
<option value="{{ $key }}" {{ Request::old('employee_id') == $key ? 'selected' : '' }}>{{ $value }}</option>
@endforeach
</select>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label class="control-label col-md-4">@lang('award.gift_item')<span class="validateRq">*</span></label>
												<div class="col-md-8">
													<input type="text" name="gift_item" id="gift_item" class="form-control required gift_item" value="{{ Request::old('gift_item') }}" placeholder="{{ __('award.gift_item') }}">
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label class="control-label col-md-4">@lang('common.month')<span class="validateRq">*</span></label>
												<div class="col-md-8">
													<div class="input-group">
														<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
														<input type="text" name="month" id="month" class="form-control required monthField" value="{{ Request::old('month') }}" placeholder="{{ __('common.month') }}" readonly="readonly">
													</div>
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

