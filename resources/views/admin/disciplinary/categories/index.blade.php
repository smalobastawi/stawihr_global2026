@extends('admin.master')
@section('content')
@section('title')
 Disciplinary Categories
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
			<a href="{{ route('disciplinary.category.create') }}"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i class="fa fa-plus-circle" aria-hidden="true"></i> Add New</a>
			<a href="{{ route('disciplinary.category.trash') }}"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i class="fa fa-plus-circle" aria-hidden="true"></i> Trash</a>
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
                                        <th>@lang('common.serial')</th>
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th style="text-align: center;">@lang('common.action')</th>
                                    </tr>
								</thead>
								<tbody>
									{!! $sl=null !!}
									@foreach($data AS $value)
										<tr class="{!! $value->id !!}">
											<td style="width: 100px;">{!! ++$sl !!}</td>
											<td>{!! $value->name !!}</td>
											<td>{!! $value->description !!}</td>
											<td>{!! GeneralStatus::getName($value->status) !!}</td>
											<td style="width: 100px;">
												@if($value->deleted_at)
												
												<a href="{!!route('disciplinary.category.restore',$value->id )!!}" data-token="{!! csrf_token() !!}" data-id="{!! $value->id!!}" class="delete btn btn-danger btn-xs deleteBtn btnColor"><i class="fa fa-undo" aria-hidden="true"></i></a>
											@else
											<a href="{!! route('disciplinary.category.edit',$value->id) !!}"  class="btn btn-success btn-xs btnColor">
												<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
											</a>
											<a href="{!!route('disciplinary.category.delete',$value->id )!!}" data-token="{!! csrf_token() !!}" data-id="{!! $value->id!!}" class="delete btn btn-danger btn-xs deleteBtn btnColor"><i class="fa fa-trash-o" aria-hidden="true"></i></a>

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
