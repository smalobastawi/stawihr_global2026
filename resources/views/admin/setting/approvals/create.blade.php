@extends('admin.master')
@section('content')
@section('title')
@lang('setting.add_approval')
@endsection
	<style>
		.appendBtnColor{
			color: #fff;
			font-weight: 700;
		}
	</style>

	<div class="container-fluid">
		<div class="row bg-title">
			
			<div class="col-lg-12 col-md-7 col-sm-7 col-xs-12">
				<a href="{{route('approvalSettings.index')}}"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i class="fa fa-list-ul" aria-hidden="true"></i>  @lang('setting.view_approval_list')</a>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-info">
					<div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i> @yield('title')</div>
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
									<i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
								</div>
							@endif
                            <form method="POST">
							@csrf

							    <div class="form-body">
                                        <h3 class="box-title">@lang('employee.employee_account')</h3>
                                        <hr>
								    <div class="row">
                                        <div class="col-md-3 mt-1" style="margin-top:10px;">
                                                <label class="control-label">@lang('setting.select_department')<span class="validateRq">*</span></label>
                                                <select type="text" class="form-control" name="model_type" placeholder="@lang('setting.select_department')" >
                                                    @foreach ($departments as $value)
                                                                <option value="{{ $value->id }}">
                                                                    {{ $value->name }}
                                                                </option>
                                                        @endforeach
                                                </select>
                                        </div>

										<div class="col-md-3">
                                            <label for="exampleInput">@lang('setting.number_of_approvers')<span class="validateRq">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="ti-user"></i></div>
                                                <input class="form-control required number_of_approvers" required id="number_of_approvers"
                                                                 name="number_of_approvers" type="number"
                                                    value="{{ old('approvers_numbers') }}">
                                            </div>
                                        </div>
                                       
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3 mt-1" style="margin-top:10px;">

											
                                            <div class="form-group">
                                                <label for="exampleInput">Approvers<span class="validateRq">*</span></label>
                                                <select class="approversSelect" multiple name="approvers[]" style="width: 200px" required>
                                                    @foreach ($employees as $value)
                                                        <!-- Store only the employee id in the value attribute -->
                                                        <option value="{{ $value->employee_id }}">
                                                            {{ $value->first_name . ' ' . $value->last_name }}
                                                        </option>
                                                    @endforeach
                                                </select>                                                
                                                
                                            </div>
                                        </div>
                                    </div>
								</div>
								
							<div class="form-actions">
								<div class="row">
                                    <br/>
									<div class="col-md-12 ">
										<button type="submit" class="btn btn-info btn_style"><i class="fa fa-save"></i> @lang('setting.save')</button>
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

	
	<!-- end of employee documents -->
@endsection
@section('page_scripts')
	<script>
			
		$(' .approversSelect').select2({
			placeholder: 'click to Search',
		});
	</script>
@endsection

