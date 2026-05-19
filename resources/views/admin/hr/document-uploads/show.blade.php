@extends('admin.master')
@section('content')
@section('title')
@lang('documents.upload_document')
@endsection
	<style>
		.appendBtnColor {
			color: #fff;
			font-weight: 700;
		}
		.document-viewer {
			width: 100%;
			height: 80vh;
			border: none;
		}
	</style>

	<div class="container-fluid">
        <div class="row bg-title">
            <!-- Breadcrumb Section -->
            <div>
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    @foreach (urlTree() as $item)
                        <li class="breadcrumb-item text-primary"><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
                    @endforeach
                </ol>
            </div>
        
            <div class="col-lg-12 col-md-7 col-sm-7 col-xs-12" style="display: flex; justify-content: flex-end; align-items: center; gap: 15px;">
                <!-- Views -->
                <div style="display: flex; align-items: center;">
                    <i class="fa fa-eye"></i>
                    <b style="color: blue; margin-left: 5px;">{{ $count??'' }}</b>
                    <span style="margin-left: 5px;">Views</span>
                </div>
        
                <a href="{{ route('documents-upload.index') }}" class="btn btn-success waves-effect waves-light">
                    <i class="fa fa-list-ul" aria-hidden="true"></i> @lang('documents.view_uploaded_documents')
                </a>
            </div>
        </div>
        

		<div class="row">
			<div class="col-md-12">
				@if($document->file_path)
                    @php
                        $fileUrl = route('documents-upload.serve', $document->id);
                        $extension = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
                    @endphp

                    @if($extension === 'pdf')
                        <iframe src="{{ $fileUrl }}" class="document-viewer"></iframe>
                        @elseif(in_array($extension, ['doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx']))
                        <!-- Attempt to render via Google Docs Viewer -->
                        <iframe
                            src="https://docs.google.com/viewer?url={{ urlencode($fileUrl) }}&embedded=true"
                            class="document-viewer">
                        </iframe>
                        <div class="alert alert-info mt-3">
                            If the preview is unavailable, <a href="{{ route('documents-upload.download', $document->id) }}" class="btn btn-primary" target="_blank">Download the Document</a>.
                        </div>
                    @else
                        <div class="alert alert-info">
                            <p>No preview available. Please download the document below:</p>
                            <a href="{{ route('documents-upload.download', $document->id) }}" class="btn btn-primary" target="_blank">Download Document</a>
                        </div>
                    @endif
                @else
                    <div class="alert alert-warning">
                        @lang('documents.no_document')
                    </div>
                @endif

			</div>
		</div>
	</div>
@endsection
