@extends('admin.master')
@section('content')

@section('title')

@if(isset($editModeData))
	Edit  category
@elseif(isset($readOnly))
Details
@else
	Add  Category
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
				<a href="{{route('disciplinary.category.index')}}"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i class="fa fa-list-ul" aria-hidden="true"></i> Go to Categories</a>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-info">
					<div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
					<div class="panel-wrapper collapse in" aria-expanded="true">
						<div class="panel-body">

						@php
							$categoryName = old('name', isset($editModeData) ? $editModeData->name : '');
							$categoryDescription = old('description', isset($editModeData) ? $editModeData->description : '');
							$statusValue = old('status', isset($editModeData) ? $editModeData->status : '');
							$statusOptions = [
								\App\Lib\Enumerations\GeneralStatus::ACTIVE => 'ACTIVE',
								\App\Lib\Enumerations\GeneralStatus::INACTIVE => 'INACTIVE',
							];
							$isReadOnly = isset($readOnly);
						@endphp

						@if(isset($editModeData))
							<form action="{{ route('disciplinary.category.update', $editModeData->id) }}" method="POST"
								enctype="multipart/form-data" id="branchForm" class="form-horizontal">
								@csrf
								@method('PUT')
						@elseif($isReadOnly)
							<div id="branchForm" class="form-horizontal">
						@else
							<form action="{{ route('disciplinary.category.store') }}" method="POST"
								enctype="multipart/form-data" id="branchForm" class="form-horizontal">
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
									<input type="text" name="name" id="name"
										class="form-control required name"
										value="{{ $categoryName }}" {{ $isReadOnly ? 'readonly' : '' }}
										required>
								</div>
											</div>
									
											<div class="form-group">
												<label class="control-label col-md-4">Description</label>
								<div class="col-md-8">
									<textarea name="description" id="description" class="form-control required description"
										placeholder="{{ __('common.description') }}"
										{{ $isReadOnly ? 'readonly' : '' }}>{{ $categoryDescription }}</textarea>
								</div>
											</div>
									
											<div class="form-group">
												<label class="control-label col-md-4">Status<span class="validateRq">*</span></label>
								<div class="col-md-8">
									<select name="status" id="status" class="form-control required status" {{ $isReadOnly ? 'disabled' : '' }} required>
										<option value="">{{ __('common.please_select') }}</option>
										@foreach($statusOptions as $value => $label)
											<option value="{{ $value }}" {{ (string) $value === (string) $statusValue ? 'selected' : '' }}>{{ $label }}</option>
										@endforeach
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
						@elseif(!$isReadOnly)
							<button type="submit" class="btn btn-info btn_style"><i class="fa fa-check"></i> @lang('common.save')</button>
						@endif
												</div>
											</div>
										</div>
									</div>
								</div>
						@if($isReadOnly)
							</div>
						@else
							</form>
						@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection



