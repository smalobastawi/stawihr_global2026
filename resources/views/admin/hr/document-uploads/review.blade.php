@extends('admin.master')
@section('content')
@section('title')
@lang('documents.review_document')
@endsection
	<style>
		.appendBtnColor{
			color: #fff;
			font-weight: 700;
		}

        .document-viewer {
        width: 100%;
        height: 600px; 
        border: none;
    }

    .button-container {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 15px;
        margin-top: 20px; 
    }

    .btn {
        padding: 10px 20px;
        font-size: 16px;
    }
	</style>

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
			<div class="col-md-12">
				<div class="panel panel-info">
					<div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i> @yield('title')</div>
					<div class="panel-wrapper collapse in" aria-expanded="true">
						<div class="panel-body">
                            <div class="container-fluid">
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
                                        <form action="{{ route('documents-upload.update-review', ) }}" method="POST" enctype="multipart/form-data" id="documentReviewForm">
@csrf
@method('PUT')


                                            <div class="button-container">
                                                <button class="btn btn-primary" id="approveBtn">Approve record</button>
                                                <button class="btn btn-danger" id="rejectBtn">Reject record</button>
                                            </div>
                                
                                            <div class="form-group" id="approveDiv" style="display: none;">
                                                <label for="reason">Reason</label>
                                                <textarea class="form-control" id="approval_reason" name="approval_reason" rows="3" placeholder="Please provide the approval reason..."></textarea>
                                            </div>
                                
                                            <div class="form-group" id="rejectDiv" style="display: none;">
                                                <label for="reason">Reason</label>
                                                <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" placeholder="Please provide the rejection reason..."></textarea>
                                            </div>
                                
                                            <div class="button-container">
                                                <button class="btn btn-success" id="submitBtn" style="display: none;">Save</button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                            
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


						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
	
@endsection
@section('page_scripts')

<script>
    var approveBtn = document.getElementById("approveBtn");
    var rejectBtn = document.getElementById("rejectBtn");
    var approveDiv = document.getElementById("approveDiv");
    var submitBtn = document.getElementById("submitBtn");

    approveBtn.addEventListener("click", function($event) {
        $event.preventDefault();


        approveDiv.style.display = "block";  
        //check if reject div is open,if so close it
        if(rejectDiv.style.display == "block"){
            rejectDiv.style.display = "none";
        }
        submitBtn.style.display = "inline"; 
        submitBtn.setAttribute("data-status", 1);  
    });

    rejectBtn.addEventListener("click", function($event) {
        $event.preventDefault();

        rejectDiv.style.display = "block"; 
        //check if approve div is open,if so close it
        if(approveDiv.style.display == "block"){
            approveDiv.style.display = "none";
        } 
        submitBtn.style.display = "inline";  
        submitBtn.setAttribute("data-status", 2);  
    });

    // Submit Button click
        $(function() {
           // Set the status on Approve or Reject button click
           $('#approveBtn').click(function() {
            $('#submitBtn').data('status', 1);
            $('#reasonDiv').show(); 
        });

        $('#rejectBtn').click(function() {
            $('#submitBtn').data('status', 2);
            $('#rejectDiv').show(); 
        });

        // Handle the submit button click
        $('#submitBtn').click(function(event) {
            event.preventDefault();
            //reason to pick from the approval text area and also reject text area
                var rejection_reason = $('#rejection_reason').val();
                var approval_reason = $('#approval_reason').val();
            var status = $(this).data('status'); 
            var approvalId = "{{ $document['id'] }}"; 

            
              // Validate rejection reason if the status is "Reject"
            if (status === 2 && (!rejection_reason || rejection_reason.trim() === "")) {
                    alert('Please provide a reason for rejection before submitting.');
                    return; 
              }

            $.ajax({
                url: "{!! route('documents-upload.update-review', $document['id']) !!}",
                type: "PUT", 
                data: {
                    _token: "{{ csrf_token() }}", 
                    _method: "PUT",             
                    approval_reason: approval_reason??null, 
                    rejection_reason: rejection_reason??null,            
                    status: status,              
                    approval_id: approvalId      
                },
                success: function(response) {
                    if (response.success) {
                        alert('Review submited successfully!');
                        $('#reasonDiv').hide(); 
                        $('#rejectDiv').hide(); 

                        //redirect to document-uploads.index page
                        window.location.href = "{{ route('documents-upload.index') }}";
                        
                    } else {
                        alert('Error updating  review status.');
                    }
                },
                error: function(xhr, status, error) {
                    alert('There was an error processing the request.');
                    console.error(xhr.responseText); 
                }
            });
        });

     
    });
</script>

@endsection


