@extends('admin.master')
@section('content')
@section('title', getPageTitle() . ' | ' . config('app.name'))
<style>
    .appendBtnColor {
        color: #fff;
        font-weight: 700;
    }
</style>

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>

            </ol>
        </div>
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
            <a href="{{ route('contract.index') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                    class="fa fa-list-ul" aria-hidden="true"></i>View Contracts
            </a>

            @if(isset($staffDetails))
            <a href="{{ route('employee.show', $staffDetails->employee_id) }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                    class="fa fa-list-ul" aria-hidden="true"></i>Employee Profile
            </a>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                        aria-hidden="true">×</span></button>
                                @foreach ($errors->all() as $error)
                                    <strong>{!! $error !!}</strong><br>
                                @endforeach
                            </div>
                        @endif
                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i
                                    class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i
                                    class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
                        @if (isset($editModeData))
                            <form action="{{ route('contract.update', $editModeData->id) }}" method="POST" enctype="multipart/form-data" id="employeeForm">
                                @csrf
                                @method('PUT')
                        @else
                            <form action="{{ route('contract.store') }}" method="POST" enctype="multipart/form-data" id="employeeForm">
                                @csrf
                        @endif

                        <input class="form-control  delete_education_qualifications_cid"
                            id="delete_education_qualifications_cid" name="delete_education_qualifications_cid"
                            type="hidden" value="">
                        <input class="form-control  delete_experiences_cid" id="delete_experiences_cid"
                            name="delete_experiences_cid" type="hidden" value="">
                        <input class="form-control" id="delete_employee_documents_cid" name="delete_employee_documents_cid"
                            type="hidden" value="">
                        <div class="form-body">
                         

                            <div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<label for="exampleInput">Staff Name<span class="validateRq">*</span></label>
								
										@if(isset($staffDetails))
											<input type="text" class="form-control" value="{{ $staffDetails->first_name . ' ' . $staffDetails->middle_name . ' ' . $staffDetails->last_name }}" readonly>
											<input type="hidden" name="employee_id" value="{{ $staffDetails->employee_id }}" >
										@else
											<select name="employee_id" class="form-control" required>
												<option value="">Select Staff</option>
												@foreach($staffList as $staff)
													<option value="{{ $staff->employee_id }}">{{ $staff->first_name . ' ' . $staff->middle_name . ' ' . $staff->last_name }}</option>
												@endforeach
											</select>
										@endif
									</div>
								</div>
								
								<div class="col-md-3">
									<div class="form-group">
										<label for="hire_date">Hire Date <small class="text-muted">(Probation starts on this date)</small><span class="validateRq">*</span></label>
										<input type="date" name="hire_date" id="hire_date" class="form-control" required 
											   value="{{ isset($editModeData) ? $editModeData->hire_date : '' }}">
									</div>
								</div>
								
								<div class="col-md-3">
									<div class="form-group">
										<label for="start_date">Contract Start Date<span class="validateRq">*</span></label>
										<input type="date" name="start_date" id="start_date" class="form-control" required 
											   value="{{ isset($editModeData) ? $editModeData->start_date : '' }}">
									</div>
								</div>
								
								<!-- Hidden probation start date (same as hire date) -->
								<input type="hidden" name="probation_start_date" id="probation_start_date" value="{{ isset($editModeData) ? $editModeData->probation_start_date : '' }}">
								
								<div class="col-md-3">
									<div class="form-group">
										<label for="probation_end_date">Probation End <small class="text-muted">(Auto: 6 months from hire)</small></label>
										<input type="date" name="probation_end_date" id="probation_end_date" class="form-control" 
											   value="{{ isset($editModeData) ? $editModeData->probation_end_date : '' }}" readonly style="background-color: #e9ecef; cursor: not-allowed;">
									</div>
								</div>
								
								<div class="col-md-3">
									<div class="form-group">
										<label for="end_date">Contract End Date<span class="validateRq"></span></label>
										<input type="date" name="end_date" class="form-control" 
											   value="{{ isset($editModeData) ? $editModeData->end_date : '' }}">
									</div>
								</div>
								
								<div class="col-md-3">
                                    <div class="form-group">
										<label for="exampleInput">Contract Type<span
											class="validateRq">*</span></label>
                                        <select class="form-control select2" name="contract_type" required>
											<option selected="selected" disabled="disabled" value="">
												Select Contract Type
											</option>
											@foreach (\StaffContractTypes::toArray() as $key => $value)
												<option value="{{ $key }}"
													@isset($editModeData->contract_type)
													@if ($key == $editModeData->contract_type)
														selected = "selected"
													@endif
												@endisset>
													{{ $value }}
												</option>
											@endforeach
										</select>
                                    </div>
                                </div>
                             
                            </div>
                            <!-- employee documents here -->
                            <h3 class="box-title">Employee Documents</h3>
                            <hr>

                            <!-- Display existing documents -->
                            @if(isset($employeeDocuments) && $employeeDocuments->count() > 0)
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Existing Documents</h4>
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Document Name</th>
                                                    <th>Type</th>
                                                    <th>Date Uploaded</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($employeeDocuments as $doc)
                                                    <tr>
                                                        <td>{{ $doc->document_name }}</td>
                                                        <td>{{ ucfirst($doc->document_type) }}</td>
                                                        <td>{{ $doc->date_uploaded ? \Carbon\Carbon::parse($doc->date_uploaded)->format('Y-m-d') : 'N/A' }}</td>
                                                        <td>
                                                            <a style="color: #fff;" href="{{ url('uploads/employeeDocs') . '/' . $doc->document_link }}" target="_blank" class="btn btn-info btn-xs">
                                                                <i class="fa fa-eye" ></i> View
                                                            </a>
                                                            <button type="button" class="btn btn-danger btn-xs delete-existing-doc" data-doc-id="{{ $doc->id }}">
                                                                <i class="fa fa-trash"></i> Delete
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <hr>
                                    </div>
                                </div>
                            @endif

                            <h4>Add New Documents</h4>
                            <div class="employee_document_append_div">

                            </div>
                            <div class="row">
                                <div class="col-md-9"></div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input id="addEmployeeDocuments" type="button"
                                            class="form-control btn btn-success appendBtnColor" value="Add Contract Document">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>
        
                    </div>

               
                    <hr>
                 
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-12 ">
                                <button type="submit" class="btn btn-info btn_style"><i class="fa fa-pencil"></i>
                                    @lang('common.update')</button>
                            </div>
                        </div>
                    </div>
                </div>

                </form>
            </div>
        </div>
    </div>
</div>


<!-- employee documents start here -->
<div class="employee_docs_row_element1" style="display: none;">
    <input name="employeeDocuments_cid[]" type="hidden">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="exampleInput">Document Name<span class="validateRq">*</span></label>
                <input type="text" name="document_name[]" class="form-control responsibility"
                    placeholder="e.g KRA PIN" cols="30" rows="2" required></input>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="exampleInput">Type<span class="validateRq">*</span></label>
                <select name="document_type[]" class="form-control type" cols="30" rows="2" required>

                    <option value="personal">Personal</option>
                    <option value="official">Official</option>
                </select>
            </div>
        </div>


        <div class="col-md-3">
            <div class="form-group">
                <label for="exampleInput">Upload file<span class="validateRq">*</span></label>
                <input type="file" name="document_file[]" class="form-control responsibility"
                    placeholder="document_name" cols="30" rows="2" required
                    accept="application/pdf"></input>
            </div>
        </div>
        <div class="col-md-3"></div>
        <div class="col-md-3">
            <div class="form-group">
                <input type="button" class="form-control btn btn-danger deleteEmployeeDocuments appendBtnColor"
                    style="margin-top: 17px" value="@lang('common.delete')">
            </div>
        </div>
    </div>
    <hr>
</div>

<!-- end of employee documents -->
@endsection
@section('page_scripts')
<script>
    $(document).ready(function() {

        // Function to calculate probation dates based on hire date
        function calculateProbationDates() {
            var hireDate = $('#hire_date').val();
            if (hireDate) {
                var date = new Date(hireDate);

                // Set probation start date to hire date
                $('#probation_start_date').val(hireDate);

                // Calculate probation end date (6 months later)
                date.setMonth(date.getMonth() + 6);
                var probationEndDate = date.toISOString().split('T')[0];
                $('#probation_end_date').val(probationEndDate);
            }
        }

        // Calculate on page load (for edit mode)
        calculateProbationDates();

        // Calculate when hire date changes
        $('#hire_date').on('change', function() {
            calculateProbationDates();
        });

        $('#addEmployeeDocuments').click(function() {
            $('.employee_document_append_div').append('<div class="employee_documents_row_element">' +
                $('.employee_docs_row_element1').html() + '</div>');
        });

        $(document).on("click", ".deleteEmployeeDocuments", function() {
            $(this).parents('.employee_documents_row_element').remove();
            var deletedID = $(this).parents('.employee_documents_row_element').find(
                '.employeeDocuments_cid').val();
            if (deletedID) {
                var prevDelId = $('#delete_employee_documents_cid').val();
                if (prevDelId) {
                    $('#delete_employee_documents_cid').val(prevDelId + ',' + deletedID);
                } else {
                    $('#delete_employee_documents_cid').val(deletedID);
                }
            }
        });

        // Handle deletion of existing documents
        $(document).on("click", ".delete-existing-doc", function() {
            if(confirm('Are you sure you want to delete this document?')) {
                var docId = $(this).data('doc-id');
                var prevDelId = $('#delete_employee_documents_cid').val();
                if (prevDelId) {
                    $('#delete_employee_documents_cid').val(prevDelId + ',' + docId);
                } else {
                    $('#delete_employee_documents_cid').val(docId);
                }
                $(this).closest('tr').remove();
            }
        });
    });
</script>

@endsection

