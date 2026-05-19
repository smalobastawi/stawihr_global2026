@extends('admin.master')
@section('content')
@section('title')
@lang('documents.document_categories')
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
			<a href="{{ route('document-categories.create') }}"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i class="fa fa-plus-circle" aria-hidden="true"></i> @lang('documents.add_document_category')</a>
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
                                        <th>@lang('documents.document_category_name')</th>
                                      
                                        <th style="text-align: center;">@lang('common.action')</th>
                                    </tr>
								</thead>
								<tbody>
									{!! $sl=null !!}
									@foreach($categories AS $dc)
										<tr class="{!! $dc->id !!}">
											<td >{!! ++$sl !!}</td>
                                            <td>{!! $dc->name!!}</td>
											
											<td style="width: 100px;">
												<a href="{!! route('document-categories.edit',$dc->id) !!}"  class="btn btn-success btn-xs btnColor">
													<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
												</a>
												<a href="{!!route('document-categories.delete',$dc->id )!!}" data-token="{!! csrf_token() !!}" data-id="{!! $dc->id!!}" 
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
