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
		.acknowledgment-box {
			padding: 20px;
			border-radius: 8px;
			margin-top: 20px;
		}
		.acknowledgment-box.acknowledged {
			background-color: #d4edda;
			border: 1px solid #28a745;
		}
		.acknowledgment-box.pending {
			background-color: #fff3cd;
			border: 1px solid #ffc107;
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

                <a href="{{ route('ess.documents.index') }}" class="btn btn-success waves-effect waves-light">
                    <i class="fa fa-list-ul" aria-hidden="true"></i> @lang('documents.view_uploaded_documents')
                </a>
            </div>
        </div>

        <!-- Acknowledgment Status Banner -->
        @if($hasConsented)
            <div class="alert alert-success">
                <h5><i class="fa fa-check-circle"></i> Document Acknowledged</h5>
                <p>You acknowledged this document on {{ $consent->consented_at->format('d-m-Y H:i') }}.</p>
            </div>
        @else
            <div class="alert alert-warning">
                <h5><i class="fa fa-exclamation-triangle"></i> Acknowledgment Required</h5>
                <p>Please review this document and acknowledge that you have read and understood the content.</p>
            </div>
        @endif

		<div class="row">
			<div class="col-md-12">
				@if($document->file_path)
                    @php
                        $fileUrl = route('ess.documents.serve', $document->id);
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
                            If the preview is unavailable, <a href="{{ $fileUrl }}" class="btn btn-primary" target="_blank">Download the Document</a>.
                        </div>
                    @else
                        <div class="alert alert-info">
                            <p>No preview available. Please download the document below:</p>
                            <a href="{{ $fileUrl }}" class="btn btn-primary" target="_blank">Download Document</a>
                        </div>
                    @endif
                @else
                    <div class="alert alert-warning">
                        @lang('documents.no_document')
                    </div>
                @endif

                <!-- Acknowledgment Section -->
                <div class="acknowledgment-box {{ $hasConsented ? 'acknowledged' : 'pending' }}">
                    @if($hasConsented)
                        <div class="text-center">
                            <h4><i class="fa fa-check-circle text-success"></i> Acknowledged</h4>
                            <p>You have acknowledged this document on <strong>{{ $consent->consented_at->format('d-m-Y H:i') }}</strong>.</p>
                            <p class="text-muted small">IP Address: {{ $consent->ip_address }}</p>
                        </div>
                    @else
                        <div class="text-center">
                            <h4><i class="fa fa-exclamation-circle text-warning"></i> Acknowledgment Required</h4>
                            <p>By clicking the button below, you confirm that you have read and understood this document and agree to abide by the terms stated therein.</p>
                            <button onclick="acknowledgeDocument({{ $document->id }})" class="btn btn-success btn-lg">
                                <i class="fa fa-check"></i> I Acknowledge This Document
                            </button>
                        </div>
                    @endif
                </div>

			</div>
		</div>
	</div>

<script>
function acknowledgeDocument(documentId) {
    swal({
        title: "Acknowledge Document",
        text: "I have read and understood this document and agree to abide by the terms stated therein.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        confirmButtonText: "Yes, I Acknowledge",
        cancelButtonText: "Cancel",
        closeOnConfirm: false
    }, function(isConfirm) {
        if (isConfirm) {
            $.ajax({
                url: '{{ route("ess.documents.acknowledge", ["document" => ":documentId"]) }}'.replace(':documentId', documentId),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    acknowledgment_text: 'I have read and understood this document and agree to abide by the terms stated therein.'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        swal({
                            title: "Acknowledged!",
                            text: response.message,
                            type: "success"
                        }, function() {
                            location.reload();
                        });
                    } else {
                        swal("Error!", response.message || "Failed to acknowledge document.", "error");
                    }
                },
                error: function(xhr, status, error) {
                    var errorMessage = "Failed to acknowledge document.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    swal("Error!", errorMessage, "error");
                }
            });
        }
    });
}
</script>
@endsection
