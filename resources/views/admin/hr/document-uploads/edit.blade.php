@extends('admin.master')
@section('content')
@section('title')
@lang('documents.upload_document')
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
				<a href="{{route('documents-upload.index')}}"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i class="fa fa-list-ul" aria-hidden="true"></i>  @lang('documents.view_uploaded_documents')</a>
			</div>
		</div>
		<div class="row">
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-clipboard-text fa-fw"></i> @yield('title')
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    @foreach($errors->all() as $error)
                                        <strong>{!! $error !!}</strong><br>
                                    @endforeach
                                </div>
                            @endif
                            @if(session()->has('success'))
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <i class="mdi mdi-check-circle"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                </div>
                            @endif
                            @if(session()->has('error'))
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <i class="mdi mdi-alert-circle"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                                </div>
                            @endif
                            <form action="{{ route('documents-upload.update', $document) }}" method="POST" enctype="multipart/form-data" id="documentUploadsForm">
@csrf
@method('PUT')

        
                             
                            <div class="form-body">
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="category">@lang('documents.category')<span class="validateRq">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="ti-file"></i>
                                            </div>
                                            <select class="form-control required category" required id="category" name="category_id">
                                                <option value="">@lang('documents.select_document_category')</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ $document->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
        
                                    <div class="col-md-6">
                                        <label for="name">@lang('documents.name')<span class="validateRq">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="mdi mdi-text"></i>
                                            </div>
                                            <input class="form-control required name" required id="name" name="name" type="text" value="{{ $document->name }}">
                                        </div>
                                    </div>
                                </div>
        
                              
                                    <div class="row">
                                            <div class="col-md-6">
                                                <label for="exampleInput">@lang('documents.description')<span class="validateRq">*</span></label>
                                                
                                                <div class="input-group">
                                                        <div class="input-group">
                                                            <div class="input-group-addon"><i class="ti-notepad"></i></div>
                                                            <textarea 
                                                                class="form-control required description" 
                                                                required 
                                                                id="description" 
                                                                name="description"
                                                            >{{ old('description', $document->description ?? '') }}{{ isset($document->description) ? $document->description : '' }}</textarea>
                                                        </div>
                                                    </div>
                                            </div>
                                        <div class="col-md-6">
                                                <label for="file">@lang('documents.upload_file')<span class="validateRq">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-addon"><i class="ti-file"></i></div>
                                                    <input 
                                                        class="form-control required file" 
                                                        required 
                                                        id="file" 
                                                        name="file" 
                                                        type="file"
                                                    >
                                                </div>
                                                @if(isset($document->file_path))
                                                    <small class="text-muted">Current file: {{ basename($document->file_path) }}</small>
                                                @endif
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

        
        
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
	</div>
	</div>
@endsection


