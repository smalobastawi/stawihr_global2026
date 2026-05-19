@extends('admin.master')
@section('content')
@section('title')
@lang('setting.approvals_list')
@endsection
<div class="container-fluid">
	<div class="row bg-title">
		<div class="">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
				@foreach (urlTree() as $item)
					<li class="breadcrumb-item text-primary"><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
				@endforeach
			</ol>
			
		</div>	
		<div class="col-lg-12 col-sm-8 col-md-8 col-xs-12">
			<a href="{{ route('approvalSettings.create') }}"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i class="fa fa-plus-circle" aria-hidden="true"></i> @lang('setting.add_approval')</a>
		</div>	
	</div>
                
	<div class="row">
		<div class="col-sm-12">
			<div class="panel panel-info">
				<div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
				<div class="panel-wrapper collapse in" aria-expanded="true">
					<div class="panel-body">
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
						<div class="table-responsive">
							<table id="myTable" class="table table-bordered">
								<thead>
									 <tr class="tr_header">
                                        <th>#</th>
                                        <th>@lang('setting.department')</th>
                                        <th>@lang('setting.approvers')</th>
                                        <th>@lang('setting.approvers_numbers')</th>
                                        <th style="text-align: center;">@lang('common.action')</th>
                                    </tr>
								</thead>
								<tbody>
									{!! $sl=null !!}
									@foreach($approvalSettings AS $aps)
										<tr class="{!! $aps->id !!}">
											<td >{!! ++$sl !!}</td>
                                            <td>{!! $aps->module->name!!}</td>
											<td>
												@foreach ($aps->approvers as $approver )
												 
													{{$approver->user->employeeDetails->fullname()}}</br>
												@endforeach
											</td>
											<td>{!! $aps->approver_numbers !!}</td>
											<td style="width: 100px;">
												<a href="{!! route('approvalSettings.edit',$aps) !!}"  class="btn btn-success btn-xs btnColor">
													<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
												</a>
												<a href="{!!route('approvalSettings.delete',$aps )!!}" data-token="{!! csrf_token() !!}" data-id="{!! $aps->id!!}" 
													class="delete btn btn-danger btn-xs deleteBtn btnColor"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
											
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
