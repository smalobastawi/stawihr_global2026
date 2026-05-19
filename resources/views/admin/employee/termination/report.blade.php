@extends('admin.master')
@section('content')
@section('title')

@lang('termination.termination_checklist_report')
@endsection
<div class="container-fluid">
	<div class="row bg-title">
		<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
		   <ol class="breadcrumb">
				<li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
				<li>@yield('title')</li>
			</ol>
		</div>	
		<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
			<a href="{{ route('termination.index') }}"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i class="fa fa-plus-circle" aria-hidden="true"></i> @lang('termination.termination_list')</a>
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
                                        <th>@lang('termination.checklist_name')</th>
										 <th>@lang('termination.checklist_description')</th>
										 <th>@lang('termination.cleared')</th>
										 <th>@lang('termination.comments')</th>
										 <th>@lang('termination.cleared_by')</th>
                                    </tr>
								</thead>
								<tbody>
								{!! $sl=null !!}
								@foreach($checklist_actions AS $value)
									<tr class="{!! $value->id !!}">
										<td style="width: 100px;">{!! ++$sl !!}</td>
										<td>{!! $value->checklist->checklist_name !!}</td>
										<td>{!! $value->checklist->description !!}</td>
                                        <td>{!! $value->status == 1 ? 'Yes' : 'No' !!}</td>
										<td>{!! $value->comment!!}</td>
										<td>{!! $value->clearedBy->user_name !!}</td>
									
										
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
