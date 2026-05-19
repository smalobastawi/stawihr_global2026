@extends('admin.master')
@section('content')
@section('title')
@lang('documents.document_uploads')
@endsection

<style>
    .document-row.acknowledged {
        background-color: #d4edda !important;
    }
    .document-row.pending {
        background-color: #fff3cd !important;
    }
    .consent-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
    }
    .consent-badge.acknowledged {
        background-color: #28a745;
        color: white;
    }
    .consent-badge.pending {
        background-color: #ffc107;
        color: #000;
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
	</div>

    <!-- Info Box -->
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-info">
                <h5><i class="fa fa-info-circle"></i> Document Acknowledgment Required</h5>
                <p>Please review all documents and acknowledge that you have read and understood the content. Documents marked in <strong style="color: #ffc107;">yellow</strong> require your acknowledgment. Documents marked in <strong style="color: #28a745;">green</strong> have been acknowledged.</p>
            </div>
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
                                        <th>@lang('documents.document_category')</th>
                                        <th>@lang('documents.uploaded_on')</th>
                                        <th>Status</th>
                                        <th style="text-align: center;">@lang('common.action')</th>
                                    </tr>
								</thead>
								<tbody>
									{!! $sl=null !!}
									@foreach($documents AS $dc)
										@php
											$hasConsented = isset($documentConsents[$dc->id]) && $documentConsents[$dc->id];
											$rowClass = $hasConsented ? 'acknowledged' : 'pending';
										@endphp
										<tr class="{!! $dc->id !!} document-row {{ $rowClass }}">
											<td >{!! ++$sl !!}</td>
                                            <td>{!! $dc->name!!}</td>
                                            <td>{!! $dc->category->name!!}</td>
                                            <td>{{ $dc->created_at->format('d-m-Y H:i') }}</td>
                                            <td>
                                                @if($hasConsented)
                                                    <span class="consent-badge acknowledged">
                                                        <i class="fa fa-check-circle"></i> Acknowledged
                                                    </span>
                                                @else
                                                    <span class="consent-badge pending">
                                                        <i class="fa fa-clock-o"></i> Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td style="width: 120px;">
                                                <a href="?doc_id={{$dc->id}}" class="btn btn-success btn-xs btnColor" title="View Document">
												<i class="iconFontSize mdi mdi-eye hideMenu"></i>
                                                </a>
                                                @if(!$hasConsented)
                                                    <button onclick="acknowledgeDocument({{ $dc->id }})" class="btn btn-warning btn-xs btnColor" title="Acknowledge Document">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                @else
                                                    <button class="btn btn-success btn-xs btnColor" disabled title="Already Acknowledged">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                @endif
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
