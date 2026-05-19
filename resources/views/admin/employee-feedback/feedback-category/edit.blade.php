@extends('admin.master')
@section('content')

@section('title')

@if(isset($editModeData))
	Edit Feed category
@else
	Add Feedback Category
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
				<a href="{{route('feedback.category.index')}}"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i class="fa fa-list-ul" aria-hidden="true"></i> Go to Categories</a>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-info">
					<div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
					<div class="panel-wrapper collapse in" aria-expanded="true">
						<div class="panel-body">

							@if(isset($editModeData))
								<form method="POST" action="{{ route('feedback.category.update', $editModeData->id) }}" enctype="multipart/form-data" id="branchForm" class="form-horizontal">
								@csrf
								@method('PUT')
							@else
								<form method="POST" action="{{ route('feedback.category.store') }}" enctype="multipart/form-data" id="branchForm" class="form-horizontal">
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
										<div class="col-md-6">
											<div class="form-group">
												<label class="control-label col-md-4">Category Name<span class="validateRq">*</span></label>
												<div class="col-md-8">
													<input type="text" name="name" value="{{ old('name', $editModeData->name ?? '') }}" required class="form-control required name" id="name">
												</div>
											</div>
									
											<div class="form-group">
												<label class="control-label col-md-4">Description</label>
												<div class="col-md-8">
													<textarea name="description" class="form-control required description" id="description" placeholder="{{ __('common.description') }}">{{ old('description', $editModeData->description ?? '') }}</textarea>
												</div>
											</div>
									
											<div class="form-group">
												<label class="control-label col-md-4">Status<span class="validateRq">*</span></label>
												<div class="col-md-8">
													<select name="status" class="form-control required status" id="status" required>
														<option value="{{ \App\Lib\Enumerations\GeneralStatus::ACTIVE }}" {{ old('status', $editModeData->status ?? '') == \App\Lib\Enumerations\GeneralStatus::ACTIVE ? 'selected' : '' }}>ACTIVE</option>
														<option value="{{ \App\Lib\Enumerations\GeneralStatus::INACTIVE }}" {{ old('status', $editModeData->status ?? '') == \App\Lib\Enumerations\GeneralStatus::INACTIVE ? 'selected' : '' }}>INACTIVE</option>
													</select>
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


