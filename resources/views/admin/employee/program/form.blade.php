@extends('admin.master')
@section('content')

@section('title')
@if(isset($editModeData))
Edit Program
@else
Add New Program
@endif
@endsection

<div class="container-fluid">
	<div class="row bg-title">
		<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
			<ol class="breadcrumb">
				<li class="active breadcrumbColor">
					<a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
				</li>
				<li>@yield('title')</li>
			</ol>
		</div>
		<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
			<a href="{{ route('employee.program.index') }}" class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
				<i class="fa fa-list-ul" aria-hidden="true"></i> View Programs
			</a>
		</div>
	</div>
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
		<div class="col-md-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')
				</div>
				<div class="panel-wrapper collapse in" aria-expanded="true">
					<div class="panel-body">
						@if(isset($editModeData))
						<form action="{{ route('employee.program.update', $editModeData->id) }}" method="POST" class="form-horizontal">
							@csrf
							@method('PUT')
						@else
						<form action="{{ route('employee.program.store') }}" method="POST" class="form-horizontal">
							@csrf
						@endif

						<div class="form-body">

							<div class="row">
								<div class="col-md-8">
									<div class="form-group">
										<label class="control-label col-md-4">Main Program</label>
										<div class="col-md-8">
											<select name="main_program" class="form-control select2">
<option value="">Select Main Program (Optional)</option>
@foreach($programList->pluck('name', 'id') as $__key => $__value)
<option value="{{ $__key }}" {{ (string)null == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
										</div>
										
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-8">
									<div class="form-group">
										<label class="control-label col-md-4">Program Name<span class="validateRq">*</span></label>
										<div class="col-md-8">
											<input type="text" name="name" value="{{ null }}" class="form-control required" placeholder="Program Name">
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-8">
									<div class="form-group">
										<label class="control-label col-md-4">Program Code<span class="validateRq">*</span></label>
										<div class="col-md-8">
											<input type="text" name="code" value="{{ null }}" class="form-control required" placeholder="Program Code">
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-8">
									<div class="form-group">
										<label class="control-label col-md-4">Start Date<span class="validateRq">*</span></label>
										<div class="col-md-8">
											<div class="input-group">
												<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
												<input type="date" name="start_date" value="{{ null }}" class="form-control required" placeholder="YYYY-MM-DD">
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-8">
									<div class="form-group">
										<label class="control-label col-md-4">End Date<span class="validateRq">*</span></label>
										<div class="col-md-8">
											<div class="input-group">
												<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
												<input type="date" name="end_date" value="{{ null }}" class="form-control required" placeholder="YYYY-MM-DD">
											</div>
										</div>
									</div>
								</div>
							</div>

							

							<div class="row">
								<div class="col-md-8">
									<div class="form-group">
										<label class="control-label col-md-4">Status<span class="validateRq">*</span></label>
										<div class="col-md-8">
											<select name="status" class="form-control select2 required">
@foreach(['active' => 'Active', 'inactive' => 'Inactive', 'completed' => 'Completed'] as $__key => $__value)
<option value="{{ $__key }}" {{ (string)null == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
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
												<button type="submit" class="btn btn-info">
													<i class="fa fa-check"></i> Update
												</button>
												@else
												<button type="submit" class="btn btn-info">
													<i class="fa fa-check"></i> Save
												</button>
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

