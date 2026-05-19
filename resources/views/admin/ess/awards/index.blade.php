@extends('admin.master')

@section('title', trans('award.award_list'))

@section('content')

    <div class="container-fluid">
        <div class="row bg-title">
			<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
			   <ol class="breadcrumb">
					<li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
					<li>@yield('title')</li>
				</ol>
			</div>	
			<div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
				{{-- <a href="{{ route('award.create') }}"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i class="fa fa-plus-circle" aria-hidden="true"></i> @lang('award.add_new_award')</a> --}}
			</div>	
		</div><!--/.row -->

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
											 <th>@lang('award.award_name')</th>
											<th>@lang('award.gift_item')</th>
											<th>@lang('common.month')</th>
										</tr>
									</thead>
									<tbody>
										{!! $sl=null !!}
										@foreach($results as $value)
											<tr class="{!! $value->employee_award_id !!}">
												<td style="width: 100px;">{!! ++$sl !!}</td>
												<td>{!! $value->award_name !!}</td>
												<td>{!! $value->gift_item !!}</td>
												<td>{!! convartMonthAndYearToWord($value->month) !!}</td>
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

    </div><!--/.container-fluid -->

@endsection