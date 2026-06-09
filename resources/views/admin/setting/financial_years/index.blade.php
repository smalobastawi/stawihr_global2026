@extends('admin.master')
@section('content')
@section('title')
 Financial Years
@endsection
<div class="container-fluid">
	<div class="row bg-title">
		<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
		   <ol class="breadcrumb">
				<li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
				<li>@yield('title')</li>
			</ol>
		</div>	
		<div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
			<a href="{{ route('financial_year.create') }}"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i class="fa fa-plus-circle" aria-hidden="true"></i>Add New</a>
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
						@if(!empty($showAllCompanies))
							<div class="alert alert-info">
								Viewing financial years across all permitted companies. Select a company from the header to manage years for one company.
							</div>
						@elseif(!empty($activeCompanyId))
							<div class="alert alert-info">
								Managing financial years for <strong>{{ \App\Support\CompanyContext::selectedCompanyName() }}</strong>.
							</div>
						@endif
						<div class="table-responsive">
							<table id="myTable" class="table table-bordered">
								<thead>
									 <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        @if(!empty($showAllCompanies))
                                            <th>Company</th>
                                        @endif
                                        <th>Name</th>
                                        <th>Start</th>
                                        <th>End</th>
                                        <th>Status</th>
                                        <th style="text-align: center;">@lang('common.action')</th>
                                    </tr>
								</thead>
								<tbody>
									{!! $sl=null !!}
									@foreach($results AS $value)
										<tr class="{!! $value->id !!}">
											<td style="width: 100px;">{!! ++$sl !!}</td>
											@if(!empty($showAllCompanies))
												<td>{{ $value->company?->name ?? 'N/A' }}</td>
											@endif
											<td>{!! $value->name !!}</td>
											<td>{!! \Carbon\Carbon::parse($value->start_date)->format('d-m-Y') !!}</td>
											<td>{!!\Carbon\Carbon::parse( $value->end_date)->format('d-m-Y') !!}</td>
											<td>
												@if($value->status == 1)
													<span class="label label-success">Active</span>
												@else
													<span class="label label-default">Inactive</span>
												@endif
											</td>
											
											<td style="width: 100px;">
												<a href="{!! route('financial_year.edit', $value->id) !!}"  class="btn btn-success btn-xs btnColor">
													<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
												</a>
												<a href="{!!route('financial_year.delete',$value->id )!!}" data-token="{!! csrf_token() !!}" data-id="{!! $value->id!!}" class="delete btn btn-danger btn-xs deleteBtn btnColor"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
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
