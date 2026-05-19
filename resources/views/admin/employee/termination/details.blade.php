@extends('admin.master')
@section('content')
@section('title')
@lang('termination.employee_termination_details')
@endsection
<style>
	.post {
		font-weight: 500;
		font-size: 16px;
	}

	.details {
		font-size: 13px;
		color: #98a6ad;
	}
</style>
<div class="container-fluid">
	<div class="row bg-title">
		<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
			<ol class="breadcrumb">
				<li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
				<li>@yield('title')</li>
			</ol>
		</div>
		<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
			<a href="{{route('termination.index')}}" class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i class="fa fa-list-ul" aria-hidden="true"></i> @lang('termination.view_termination') </a>
			@if(isset($result->terminateTo) && $result->terminateTo->status == 3)
				<a href="{!! route('termination.reinstate',$result->termination_id) !!}" class="btn btn-warning pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light" onclick="return confirm('Are you sure you want to reinstate this employee?');"><i class="fa fa-undo" aria-hidden="true"></i> Reinstate Employee </a>
			@endif
		</div>
	</div>
	@if(!$result)
		<div class="alert alert-danger">Termination record not found.</div>
	@else
	<div class="row">
		<div class="col-md-offset-2 col-md-7">
			<p class="box-title post">@yield('title')</p>
			<br>
		</div>
		<div class="sm-12">
			<div class="panel panel-info">

				<div class="panel-heading">{{$result->subject}}</div>
				<div class="panel-wrapper collapse in">
					<div class="panel-body">
						<span class="details"><i class="fa fa-user"></i>&nbsp;
							@if(isset($result->terminateTo->first_name))
							{{$result->terminateTo->first_name}} {{ $result->terminateTo->last_name}}
							@endif
						</span>&nbsp;
						<span class="details"><i class="fa fa-align-justify"></i>&nbsp;
							@if(isset($result->terminateTo->department->department_name))
							{{$result->terminateTo->department->department_name}}
							@endif
						</span>&nbsp;
						@if($result->status == 1)
						<span class="label label-rouded label-info">PENDING</span>
						@else
						<span class="label label-rouded label-success">APPROVED</span>
						@endif
						<p class="coverLater">{!! $result->description !!}</p>
					</div>
					<div class="panel-footer">
						<p>
							<b>@lang('termination.terminated_by') :</b>@if(isset($result->terminateBy->first_name))
							{{$result->terminateBy->first_name}} {{ $result->terminateBy->last_name}}
							@endif
							<b>@lang('termination.notice_date') :</b>{{date(" d M Y ", strtotime($result->notice_date))}},
							<b>@lang('termination.termination_date') :</b>{{date(" d M Y ", strtotime($result->termination_date))}}
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-12">
			<div class="panel panel-info">
				<div class="panel-heading"></div>
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
							
								<div class="col-md-12">
									<h4 class="text-center">Termination Docs</h4>
								</div>
							<table id="myTable1" class="table table-bordered">
								<thead>
									 <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        <th>Description</th>
										 <th>Date Uploaded</th>
										 <th>View</th>
										 
                                    </tr>
								</thead>
								<tbody>
								{!! $sl=null !!}
								@foreach($result->terminationDocs AS $value)
									<tr class="{!! $value->id !!}">
										<td>{!! ++$sl !!}</td>
										<td>{!! $value->document_name !!}</td>
										<td>{!! $value->created_at->format('Y-m-d') !!}</td>
										<td>
											<a
												href="{{ url('uploads/employeeDocs') . '/' . $value->file_url }}">
												View </a>
										</td>
									
									</tr>
								@endforeach
								</tbody>
							</table>
							
						</div>
						<div class="table-responsive">
							<div class="col-md-12">
								<h4 class="text-center">Termination checklist</h4>
							</div>
							<table id="" class="table table-bordered">
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
	@endif
</div>
@endsection