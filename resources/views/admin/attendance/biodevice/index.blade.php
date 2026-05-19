@extends('admin.master')
@section('content')
@section('title')
Biometric devices
@endsection
<div class="container-fluid">
	<div class="row bg-title">
		<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
		   <ol class="breadcrumb">
				<li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
				<li>@yield('title')</li>
			</ol>
		</div>
		{{-- <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
			<a href="{{ route('createDevice') }}"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i class="fa fa-plus-circle" aria-hidden="true"></i>Add new device</a>
		</div> --}}
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
                                        <th>No</th>
                                        <th>Device Name</th>
                                        <th>Serial</th>
                                        <th>Location</th>
										<th>IP Address</th>
                                        <th>Device Type</th>
                                        <th>Status</th> 
                                    </tr>
								</thead>
								<tbody>
									{!! $sl=null !!}
									@foreach($results AS $value)
										<tr class="{!! $value->device_ip_address !!}">
											<td style="">{!! ++$sl !!}</td>
											<td>{!! $value->device_name !!}</td>
											<td>{!! $value->device_serial !!}</td>
											<td>{!! $value->device_location !!}</td>
											<td>{!! $value->device_ip_address !!}</td>
											<td>{!! $value->device_type !!}</td>
											<td>
												@if($value->status == 1)
													<span style="color: green;">Online</span>
												@else
													<span style="color: red;">Offline</span>
												@endif
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
