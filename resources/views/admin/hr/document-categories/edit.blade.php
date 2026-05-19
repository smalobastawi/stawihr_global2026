@extends('admin.master')
@section('content')
@section('title')
@lang('documents.edit_document_category')
@endsection
	<style>
		.appendBtnColor{
			color: #fff;
			font-weight: 700;
		}
	</style>

	<div class="container-fluid">
		<div class="row bg-title">
			
			<div class="col-lg-12 col-md-7 col-sm-7 col-xs-12">
				<a href="{{route('document-categories.index')}}"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i class="fa fa-list-ul" aria-hidden="true"></i>  @lang('documents.view_document_categories')</a>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-info">
					<div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i> @yield('title')</div>
					<div class="panel-wrapper collapse in" aria-expanded="true">
						<div class="panel-body">
							@if($errors->any())
								<div class="alert alert-danger alert-dismissible" role="alert">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
									@foreach($errors->all() as $error)
										<strong>{!! $error !!}</strong><br>
									@endforeach
								</div>
							@endif
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
							                     <form method="POST" action="{{ route('document-categories.update', $documentCategory) }}" enctype="multipart/form-data" id="documentCategoriesForm">
    @csrf
    @method('PUT')
							    <div class="form-body">
                                        <hr>
								    <div class="row">
                                        
										<div class="col-md-6">
                                            <label for="exampleInput">@lang('documents.category_name')<span class="validateRq">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="ti-user"></i></div>
                                                <input class="form-control required category_name" required id="category_name"
                                                                 name="category_name" type="text"
                                                    value="{{ $documentCategory->name }}">
                                            </div>
                                        </div>
										<div class="col-md-6">
                                            <label for="exampleInput">@lang('documents.category_description')<span class="validateRq">*</span></label>
											<div class="input-group">
												<div class="input-group-addon"> <i class="ti-file"></i></div>
											<textarea class="form-control required category_description" required 
          											id="category_description" name="description">{{ trim($documentCategory->description) }}</textarea>

											</div>
										</div>	
                                    </div>
                                   
								</div>
								
							<div class="form-actions">
								<div class="row">
                                    <br/>
									<div class="col-md-12 ">
										<button type="submit" class="btn btn-info btn_style"><i class="fa fa-save"></i> @lang('setting.save')</button>
									</div>
								</div>
							</div>
						</div>

						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	</div>
@endsection


