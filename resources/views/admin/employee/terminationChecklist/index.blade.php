@extends('admin.master')
@section('content')
@section('title')
@lang('termination_checklist.termination_checklist_items')
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
		@can("termination-checklist.create")
			<div class="col-lg-12 col-sm-8 col-md-6 col-xs-6">
			
				<a href="{{ route('termination-checklist.create') }}"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i class="fa fa-plus-circle" aria-hidden="true"></i> @lang('termination_checklist.add_termination_checklist_item')</a>
			</div>
		@endcan	
		
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
                                        <th>@lang('termination_checklist.checklist_name')</th>
                                        <th>@lang('termination_checklist.created_on')</th>
                                        <th>@lang('termination_checklist.created_by')</th>
                                        <th style="text-align: center;">@lang('common.action')</th>
                                    </tr>
								</thead>
								<tbody>
									{!! $sl=null !!}
									@foreach($termination_checklist_items AS $op)
										<tr class="{!! $op->id !!}">
											<td >{!! ++$sl !!}</td>
                                            <td>{!! $op->checklist_name!!}</td>
                                            <td>{{ $op->created_at->format('d-m-Y H:i') }}</td>
                                            <td>{{ $op->created_by }} </td>
											<td style="width: 100px;">
												@can("termination-checklist.edit")
													<a href="{!! route('termination-checklist.edit',$op->id) !!}"  class="btn btn-success btn-xs btnColor" >
														<i class="iconFontSize mdi mdi-pencil hideMenu"></i>
													</a>
												@endcan
                                           
													<a href="{!! route('termination-checklist.delete', $op->id) !!}" 
														data-token="{!! csrf_token() !!}" 
														data-id="{!! $op->id !!}" 
														class="delete btn btn-danger btn-xs deleteBtn btnColor">
														<i class="iconFontSize mdi mdi-delete hideMenu"></i>
													</a>
													 
											
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
