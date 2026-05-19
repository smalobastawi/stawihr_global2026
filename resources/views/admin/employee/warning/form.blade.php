@extends('admin.master')
@section('content')

@section('title')
@if(isset($editModeData))
	@lang('warning.edit_warning')
@else
 @lang('warning.add_warning')
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
				<a href="{{route('warning.index')}}"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i class="fa fa-list-ul" aria-hidden="true"></i> @lang('warning.view_warning')</a>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-info">
					<div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
					<div class="panel-wrapper collapse in" aria-expanded="true">
						<div class="panel-body">
							@if(isset($editModeData))
								<form action="{{ route('warning.update', ) }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
@csrf
@method('PUT')

							@else
								<form method="POST">
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
											<label class="control-label col-md-4">@lang('warning.employee_name')<span class="validateRq">*</span></label>
											<div class="col-md-8">
												<select name="warning_to" class="form-control warning_to select2 required">
@foreach($employeeList as $__key => $__value)
<option value="{{ $__key }}" {{ (string)Request::old('warning_to') == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
											</div>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-8">
										<div class="form-group">
											<label class="control-label col-md-4">@lang('warning.warning_type')<span class="validateRq">*</span></label>
											<div class="col-md-8">
												<input type="text" name="warning_type" value="{{ Request::old('warning_type') }}">
											</div>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-8">
										<div class="form-group">
											<label class="control-label col-md-4">@lang('warning.subject')<span class="validateRq">*</span></label>
											<div class="col-md-8">
												<input type="text" name="subject" value="{{ Request::old('subject') }}">
											</div>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-8">
										<div class="form-group">
											<label class="control-label col-md-4">@lang('warning.warning_by')<span class="validateRq">*</span></label>
											<div class="col-md-8">
												<select name="warning_by" class="form-control warning_by select2 required">
@foreach($employeeList as $__key => $__value)
<option value="{{ $__key }}" {{ (string)session('logged_session_data.employee_id') == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
											</div>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-8">
										<div class="form-group">
											<label class="control-label col-md-4">@lang('warning.warning_date')<span class="validateRq">*</span></label>
											<div class="col-md-8">
												<div class="input-group">
													<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
												<input type="text" name="warning_date" value="{{ isset($editModeData) ? dateConvertDBtoForm($editModeData->warning_date) : Request::old('warning_date') }}">
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-8">
										<div class="form-group">
											<label class="control-label col-md-4">@lang('warning.description')</label>
											<div class="col-md-8">
												<textarea name="description">{{ Request::old('description') }}</textarea>
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
														<button type="submit" class="btn btn-info btn_style"><i class="fa fa-pencil"></i> @lang('common.udpate')</button>
													@else
														<button type="submit" class="btn btn-info btn_style"><i class="fa fa-check"></i> @lang('common.save')</button>
													@endif
												</div>
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
	<link rel="stylesheet" href="{!! asset('admin_assets/plugins/bower_components/html5-editor/bootstrap-wysihtml5.css')!!}" />
	<script src="{!! asset('admin_assets/js/cbpFWTabs.js')!!}"></script>
	<script src="{!! asset('admin_assets/plugins/bower_components/html5-editor/wysihtml5-0.3.0.js')!!}"></script>
	<script src="{!! asset('admin_assets/plugins/bower_components/html5-editor/bootstrap-wysihtml5.js')!!}"></script>

	<script type="text/javascript">
        (function() {
            $('.description').wysihtml5();

            [].slice.call(document.querySelectorAll('.sttabs')).forEach(function(el) {
                new CBPFWTabs(el);
            });
        })();
	</script>
@endsection


