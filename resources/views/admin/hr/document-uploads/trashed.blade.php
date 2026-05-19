@extends('admin.master')
@section('content')
@section('title')
@lang('documents.deleted_documents')
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
		
		<div class="col-lg-12 col-md-7 col-sm-7 col-xs-12">
				<a href="{{route('documents-upload.index')}}"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i class="fa fa-list-ul" aria-hidden="true"></i>  @lang('documents.view_uploaded_documents')</a>
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
                                        <th>@lang('documents.document_name')</th>
                                        <th>@lang('documents.uploaded_on')</th>
                                        <th>@lang('documents.uploaded_by')</th>
										<th>@lang('documents.approved_by')</th>
										<th>@lang('documents.deleted_by')</th>

                                        <th style="text-align: center;">@lang('common.action')</th>
                                    </tr>
								</thead>
								<tbody>
									{!! $sl=null !!}
									@foreach($documents AS $dc)
										<tr class="{!! $dc->id !!}">
											<td >{!! ++$sl !!}</td>
                                            <td>{!! $dc->name!!}</td>
                                            <td>{{ $dc->created_at->format('d-m-Y H:i') }}</td>
                                            <td>{{ $dc->uploaded_by }} </td>
                                            <td>{{ $dc->approved_by }} </td>
                                            <td>{{ $dc->deleted_by }} </td>
											<td style="width: 100px;">
											@if($dc->approved_by)
                                                <a href="{!! route('documents-upload.show-deleted-document',$dc->id) !!}"  class="btn btn-primary btn-xs btnColor">
													<i class="iconFontSize mdi mdi-eye hideMenu"></i>
                                                </a>
											@endif
												@can("documents-upload.restore")
													 <form action="{!! route('documents-upload.restore-document', $dc->id) !!}" method="POST" style="display:inline;">
														@csrf
														<button type="submit" class="btn btn-success btn-xs btnColor">
															<i class="iconFontSize mdi mdi-restore hideMenu"></i>
														</button>
													</form>
												@endcan
                                        
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
