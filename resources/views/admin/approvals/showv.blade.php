@extends('admin.master')
@section('content')
    @section('title')

        @lang('employee.manage_approvals')

    @endsection
    <style>
        .panel-custom {
            background-color: #41b3f9;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
            padding: 10px 15px;
            color: white;
        }

        .item {
            padding: 13px 21px;
        }

    </style>
 <div class="container-fluid">

    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i
                                class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <a href="{{ route('approvals.index') }}" class="btn btn-success pull-right m-l-20 waves-effect waves-light">
                <i class="fa fa-plus-circle" aria-hidden="true"></i>View Approvals</a>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="mdi mdi-table fa-fw"></i>
                    @lang('employee.profile')
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="panel-body">
                            <div class="">


                                <!-- Approval Records -->
                                <div class="approval_record">
                                    <section class="content">
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="panel-custom">
                                                    <h3 class="panel-title">
                                                        <i class="fa fa-bars"></i> Approval Record Details
                                                    </h3>
                                                </div>
                                                <div class="box">
                                                    <div class="box-body" style="overflow-x: auto; max-height: 300px;">
                                                        <!-- Added style for scrollable area -->
                                                        <table id="approvalTable" class="table table-bordered table-hover">
                                                            <thead>
                                                                <tr>
                                                                    <th>Field</th>
                                                                    <th>Old Value</th>
                                                                    <th>New Value</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($approval_request->queries()->pluck('changes')->toArray() as $change)
                                                                    @php
                                                                        $changes = json_decode($change, true);
                                                                        $oldValues = $changes['oldValues'] ?? [];
                                                                        $newValues = $changes['newValues'] ?? [];
                                                                    @endphp
                                                        
                                                                    @foreach ($newValues as $field => $newValue)
                                                                    @if(!in_array($field,['created_at','updated_at','id','deleted_at']))
                                                                        <tr>
                                                                            <td>{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $field)) }}</td>
                                                                            <td>{{ $oldValues[$field] ?? 'N/A' }}</td>
                                                                            <td>{{ $newValue }}</td>
                                                                        </tr>
                                                                        @endif
                                                                    @endforeach
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                    <br>
                                </div>


                                 <!-- Approval Records -->
                                 <div class="approval_statuses">
                                    <section class="content">
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="panel-custom">
                                                    <h3 class="panel-title">
                                                        <i class="fa fa-bars"></i> Approvers details
                                                    </h3>
                                                </div>
                                                <div class="box">
                                                    <div class="box-body" style="overflow-x: auto; max-height: 300px;">
                                                        <!-- Approvers List -->
                                                        <div class="approvers_list">
                                                            <p>
                                                                <strong>
                                                                    Approvers :
                                                                </strong>
                                                            </p>
                                                            <ul>
                                                                @forelse($approval_request->module->approvers as $approver)
                                                                    <li>{{$approver->user->employeeDetails->fullname()}}</li>
                                                                @empty
                                                                    <li>No Approvers Found</li>
                                                                @endforelse
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </section>
                                    <br>
                                </div>
                                <div class="approval_status">
                                    <section class="content">
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="panel-custom">
                                                    <h3 class="panel-title">
                                                        <i class="fa fa-bars"></i> Approvers status details
                                                    </h3>
                                                </div>
                                                <div class="box">
                                                    <div class="box-body" style="overflow-x: auto; max-height: 300px;">
                                                        <!-- Approvers status List -->
                                                        <div class="approvers_status list">
                                                            <ul>
                                                                @forelse($approval_request->approvals as $approval)
                                                                    <li>
                                                                        {{ $approval->approver->employeeDetails->fullname()}} - 
                                                                        <span class="status"> 
                                                                            @if($approval->action == 'decline')
                                                                            <span class="badge badge-danger">Rejected</span>
                                                                            @elseif($approval->action  == 'approve')
                                                                            <span class="badge badge-success">Approved</span>  
                                                                            @endif
                                                                        </span>
                                                                    </li>
                                                                @empty
                                                                    <li>No Approval Records Found</li>
                                                                @endforelse
                                                            </ul>
                                                        </div>
                                                    </div>
                                                     
                                                    @if($approval_request->currentApproverId()==\Auth::user()->id)
                                                        <div class="box-footer">
                                                            <button class="btn btn-primary" id="approveBtn">Approve record</button>

                                                            <button class="btn btn-danger" id="rejectBtn">Reject record</button>

                                                            <div class="form-group" id="reasonDiv" style="display: none;">
                                                                <label for="reason">Reason</label>
                                                                <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Please provide a reason..."></textarea>
                                                            </div>
                                                            <div class="box-footer">
                                                                <button class="btn btn-success" id="submitBtn" style="display: none;">Save</button>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                

                                            </div>
                                        </div>
                                    </section>
                                    <br>
                                </div>
                             
                            </div>
                        </div>
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
    var reasonDiv = document.getElementById("reasonDiv");
    var submitBtn = document.getElementById("submitBtn");

    approveBtn.addEventListener("click", function() {
        reasonDiv.style.display = "block";  
        submitBtn.style.display = "inline"; 
        submitBtn.setAttribute("data-status", 1);  
    });

    rejectBtn.addEventListener("click", function() {
        reasonDiv.style.display = "block";  
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
            $('#reasonDiv').show(); 
        });

        // Handle the submit button click
        $('#submitBtn').click(function() {
            var reason = $('#reason').val();
            var status = $(this).data('status'); 
            var approvalId = "{{ $approval_request->id }}"; 

            // Send the data to the backend using AJAX
            $.ajax({
                url: "{!! route('approvals.approve', $approval_request->id) !!}",
                type: "PUT", 
                data: {
                    _token: "{{ csrf_token() }}", 
                    _method: "PUT",             
                    reason: reason,             
                    status: status,              
                    approval_id: approvalId      
                },
                success: function(response) {
                    if (response.success) {
                        alert('Approval status updated successfully!');
                        $('#reasonDiv').hide(); 

                        location.reload(); 
                    } else {
                        alert('Error updating approval status.');
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
