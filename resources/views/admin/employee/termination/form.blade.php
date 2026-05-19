@extends('admin.master')

@section('title')
    @if (isset($editModeData))
        @lang('termination.edit_termination')
    @else
        @lang('termination.add_new_termination')
    @endif
@endsection

@section('content')

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-md-6">
            <ol class="breadcrumb">
                <li><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                <li class="active">@yield('title')</li>
            </ol>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('termination.index') }}" class="btn btn-success">
                <i class="fa fa-list"></i> View Terminations
            </a>
        </div>
    </div>

    <div class="panel panel-info">
        <div class="panel-heading">@yield('title')</div>

        <div class="panel-body">

            <form action="{{ isset($editModeData) ? route('termination.update', $editModeData->termination_id) : route('termination.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($editModeData)) @method('PUT') @endif

                <input type="hidden" id="delete_termination_documents_cid" name="delete_termination_documents_cid">

                <!-- Employee -->
                <div class="form-group">
                    <label>Employee</label>
                    <select name="terminate_to" class="form-control select2" required>
                        @foreach($employeeList as $key => $value)
                            <option value="{{ $key }}"
                                {{ (isset($editModeData) && $editModeData->terminate_to == $key) ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Termination Type -->
                <div class="form-group">
                    <label>Termination Type</label>
                    <select name="termination_type" class="form-control select2" required>
                        @foreach(TerminationReasons::toArray() as $key => $value)
                            <option value="{{ $key }}"
                                {{ (isset($editModeData) && $editModeData->termination_type == $key) ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Subject -->
                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" name="subject" class="form-control"
                        value="{{ old('subject', $editModeData->subject ?? '') }}" required>
                </div>

                <!-- Terminated By -->
                <div class="form-group">
                    <label>Terminated By</label>
                    <input type="hidden" name="terminate_by" value="{{ auth()->id() }}">

                    <div class="form-group">
                       
                        <input type="text" class="form-control" 
                            value="{{ auth()->user()->user_name ?? auth()->user()->name }}" 
                            readonly>
                    </div>
                </div>

                <!-- NOTICE DATE -->
                <div class="form-group">
                    <label>Notice Date</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" name="notice_date" class="form-control datepicker"
                            value="{{ isset($editModeData) ? dateConvertDBtoForm($editModeData->notice_date) : old('notice_date') }}" required>
                    </div>
                </div>

                <!-- TERMINATION DATE -->
                <div class="form-group">
                    <label>Termination Date</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" name="termination_date" class="form-control datepicker"
                            value="{{ isset($editModeData) ? dateConvertDBtoForm($editModeData->termination_date) : old('termination_date') }}" required>
                    </div>
                </div>

                <!-- Eligible -->
                <div class="form-group">
                    <label>Eligible for Rehire</label>
                    <select name="eligible_for_rehire" class="form-control" required>
                        <option value="">Select</option>
                        <option value="1" {{ (isset($editModeData) && $editModeData->eligible_for_rehire == 1) ? 'selected' : '' }}>Can be Re-hired</option>
                        <option value="0" {{ (isset($editModeData) && $editModeData->eligible_for_rehire == 0) ? 'selected' : '' }}>Do not Hire</option>
                    </select>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control">{{ old('description', $editModeData->description ?? '') }}</textarea>
                </div>

                <!-- Existing Documents -->
                @if(isset($editModeData))
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($editModeData->terminationDocs as $key => $doc)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $doc->document_name }}</td>
                            <td>{{ $doc->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ url('uploads/employeeDocs/'.$doc->file_url) }}" target="_blank">View</a>
                                |
                                <a href="#" class="remove-file" data-id="{{ $doc->id }}">Delete</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

                <!-- Add Docs -->
                <button type="button" id="addTerminationDocuments" class="btn btn-success">Add Document</button>
                <div class="termination_document_append_div"></div>

                <br><br>

                <button type="submit" class="btn btn-info">Save</button>

            </form>
        </div>
    </div>
</div>

@endsection

@section('page_scripts')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script>
$(document).ready(function(){

    // FIX: DATE PICKER INITIALIZATION
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    });

    $('.select2').select2();

    // Add documents
    $('#addTerminationDocuments').click(function(){
        $('.termination_document_append_div').append(`
            <div class="row doc-row">
                <div class="col-md-4">
                    <input type="text" name="document_name[]" class="form-control" placeholder="Document name" required>
                </div>
                <div class="col-md-4">
                    <input type="file" name="document_file[]" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-doc">X</button>
                </div>
            </div><br>
        `);
    });

    // Remove new doc row
    $(document).on('click', '.remove-doc', function(){
        $(this).closest('.doc-row').remove();
    });

    // FIX: correct selector (tr not li)
    $(document).on('click', '.remove-file', function(e){
        e.preventDefault();
        let id = $(this).data('id');
        let row = $(this).closest('tr');

        if(confirm('Delete file?')){
            $.post("{{ route('termination.doc.delete') }}", {
                id: id,
                _token: "{{ csrf_token() }}"
            }, function(res){
                if(res.success){
                    row.remove();
                }
            });
        }
    });

});
</script>

@endsection