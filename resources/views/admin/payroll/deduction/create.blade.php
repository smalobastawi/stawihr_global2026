@extends('admin.master')
@section('content')
@section('title')

@if(isset($editModeData))

@lang('payroll_setup.edit_deduction_type')
@else
@lang('payroll_setup.add_deduction_type')
@endif

@endsection
	<div class="container-fluid">
		<div class="row bg-title">
			<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
				<ol class="breadcrumb">
					<li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
					<li>@yield('title')</li>
				</ol>
			</div>
			<div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
				<a href="{{route('deduction_types.index')}}"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i class="fa fa-list-ul" aria-hidden="true"></i> @lang('deduction.view_deduction') </a>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-info">
					<div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
					<div class="panel-wrapper collapse in" aria-expanded="true">
						<div class="panel-body">
							@if(isset($editModeData))
								<form action="{{ route('deduction_types.update', $editModeData->id) }}" method="POST" enctype="multipart/form-data" id="deductionForm" class="form-horizontal">
									@csrf
									@method('PUT')
								@else
									<form action="{{ route('deduction_types.store') }}" method="POST">
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
												<label class="control-label col-md-4">@lang('payroll_setup.deduction_name')<span class="validateRq">*</span></label>
												<div class="col-md-8">
													<input type="text" name="name" value="{{ Request::old('name') }}">
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label class="control-label col-md-4">Type<span class="validateRq">*</span></label>
												<div class="col-md-8">
													<select name="deduction_type" class="form-control deduction_type required">
@foreach(array('Percentage' => 'Percentage', 'Fixed' => 'Fixed') as $__key => $__value)
<option value="{{ $__key }}" {{ (string)Request::old('deduction_type') == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label class="control-label col-md-4">@lang('payroll_setup.percentage_of_basic')<span class="validateRq">*</span></label>
												<div class="col-md-8">
													<input type="number" name="percentage_of_basic" value="{{ Request::old('percentage_of_basic') }}">
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label class="control-label col-md-4">Tax Deductible<span class="validateRq">*</span></label>
												<div class="col-md-8">
													<select name="tax_deductible" class="form-control tax_deductible required">
@foreach(array(1 => 'Yes', 0 => 'No') as $__key => $__value)
<option value="{{ $__key }}" {{ (string)Request::old('tax_deductible') == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label class="control-label col-md-4">@lang('payroll_setup.limit_per_month')<span class="validateRq">*</span></label>
												<div class="col-md-8">
													<input type="number" name="limit_per_month" value="{{ Request::old('limit_per_month') }}">
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label class="control-label col-md-4">@lang('common.status')<span class="validateRq">*</span></label>
												
												<div class="col-md-8">
												<select class="residencySelect form-control select2" name="status"
												required>
												<option selected="selected" disabled="disabled">Select Status
												</option>
												@foreach (\App\Lib\Enumerations\GeneralStatus::toArray() as $key => $value)
												<option value="{{ $key }}" @isset($editModeData->status)
													@if ($key == $editModeData->status)
													selected="selected"
													@endif
													@endisset>
													{{ $value }}
												</option>
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
			$("#deductionForm").validate();

			$(document).on("change",".deduction_type",function(){
				var deduction_type		 =  $('.deduction_type').val();
				if(deduction_type == 'Fixed'){
					$('.percentage_of_basic').val('0');
					$('body').find('.percentage_of_basic').attr('readonly', true);
				}else{
					$('.percentage_of_basic').val('0');
					$('body').find('.percentage_of_basic').attr('readonly', false);
				}

			});

			@if(isset($editModeData))
				@if($editModeData->deduction_type == 'Fixed')
				{!! "$('body').find('.percentage_of_basic').attr('readonly', true)" !!}
				@endif
			@endif

		});


		jQuery(function (){
    $("#deductionForm").validate();

    $(document).on("change",".deduction_type",function(){
        var deduction_type = $(this).val();
        if(deduction_type == 'Fixed'){
            $('#percentage_of_basic').val('0');
            $('#percentage_of_basic').prop('disabled', true);
        }else{
            $('#percentage_of_basic').prop('disabled', false);
        }
    });

    // Initialize on page load
    if($('.deduction_type').val() == 'Fixed'){
        $('#percentage_of_basic').prop('disabled', true);
    }

    @if(isset($editModeData))
        @if($editModeData->deduction_type == 'Fixed')
            $('#percentage_of_basic').prop('disabled', true);
        @endif
    @endif
});
	</script>
@endsection
