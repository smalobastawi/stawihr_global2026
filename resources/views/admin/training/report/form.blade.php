@extends('admin.master')
@section('content')
@section('title', 'Training Participants & Invitees')

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                </li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>

    <div class="panel panel-info">
        <div class="panel-heading">
            <i class="mdi mdi-clipboard-text fa-fw"></i> @yield('title')
        </div>
        <div class="panel-wrapper collapse in" aria-expanded="true">
            <div class="container">
                <!-- Filters -->
                <form id="trainingFilterForm">
                    <div class="form-row">
                        <div class="col-md-6">
                            <label for="trainingTypeFilter">Training Type</label>
                            <select id="trainingTypeFilter" class="form-control" name="training_type">
                                <option value="">All Types</option>
                                @foreach ($trainingTypes as $type)
                                    <option value="{{ $type->training_type_id }}">{{ $type->training_type_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="facilitatorFilter">Facilitator</label>
                            <select id="facilitatorFilter" class="form-control" name="facilitator">
                                <option value="">Select Facilitator</option>
                                @foreach ($facilitators as $facilitator)
                                    <option value="{{ $facilitator->id }}">{{ $facilitator->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="trainingFilter">Training</label>
                            <select id="trainingFilter" class="form-control" name="training">
                                <option value="">Select Training</option>
                                <!-- Options populated via AJAX -->
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <br>

            <!-- Tabs -->
            <div class="panel">
             <div class="panel-body">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#invitesTab">Invites</a></li>
                <li><a data-toggle="tab" href="#attendancesTab">Attendances</a></li>
            </ul>
            <div class="tab tab-content">
                <!-- Invitees Tab -->
                <div id="invitesTab" class="tab-pane fade in active">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="employeeInviteSelect">Select Employee to Invite</label>
                            <select id="employeeInviteSelect" class="form-control">
                                <option value="">Select Employee</option>
                                @foreach ($employees as $employee)
                                <option value="{{ $employee->employee_id }}">{{ $employee->fullName() }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row ">..</div>
                    <div class="row">
                    <button id="addInviteeBtn" class="btn btn-primary">Add Invite</button>
                    <button id="approveInviteeBtn" class="btn btn-success">Approve Invites</button>
                    </div>
                     
                    <div id="invitesTable" class="table-responsive">
                        <!-- Table loaded via AJAX -->
                    </div>
                </div>
                <!-- Attendees Tab -->
                <div id="attendancesTab" class="tab-pane fade">
                    <div class="row mb-3">
                        <div class=" ">
                            <label  for="employeeAttendanceSelect">Select Employee to Add</label>
                            <select id="employeeAttendanceSelect" class="form-control">
                                <option value="">Select Employeew</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->employee_id }}">{{ $employee->fullName() }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row ">..</div>
                    <div class="row">
                    <button id="addAttendeeBtn" class="btn btn-primary">Add Attendee</button>
                    <button id="approveAttendeeBtn" class="btn btn-success">Approve Attendee</button>
                    </div>
                    <div id="attendantsTable" class="table-responsive">
                        <!-- Table loaded via AJAX -->
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

$(document).ready(function() {
    $('#employeeAttendanceSelect').select2();
});


$(document).ready(function() {
    $('#employeeInviteSelect').select2();
});

    $(document).ready(function () {
        
        // Fetch trainings based on type and facilitator
        $('#trainingTypeFilter, #facilitatorFilter').on('change', function () {
            const typeId = $('#trainingTypeFilter').val();
            const facilitatorId = $('#facilitatorFilter').val();
            $.ajax({
                url: "{{ route('trainingType.list.options') }}",
                type: 'GET',
                data: { type: typeId, facilitator: facilitatorId },
                success: function (response) {
                    $('#trainingFilter').html(response.html);
                },
            });
        });

        $('#trainingTypeFilter').on('change', function () {
            const typeId = $(this).val();
            $.ajax({
                url: "{{ route('training.facilitator.filter') }}",
                type: 'GET',
                data: { training_type_id: typeId },
                success: function (response) {
                    $('#facilitatorFilter').html(response.html);
                    $('#trainingFilter').html('<option value="">Select Training</option>');
                },
            });
        });

        // Add Invite
        $('#addInviteeBtn').on('click', function () {
            const trainingId = $('#trainingFilter').val();
            const employeeId = $('#employeeInviteSelect').val();
            if (!trainingId || !employeeId) {
                alert('Please select a training and an employee.');
                return;
            }

            $.ajax({
                url: "{{ route('trainingInfo.invitees.add',['training'=>'0']) }}"+ trainingId,
                type: 'POST',
                data: {
                    training_id: trainingId,
                    employee_id: employeeId,
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    alert('Employee successfully invited!');
                    fetchTabData(trainingId, 'invites');
                },
            });
        });

        // Add Attendee
        $('#addAttendeeBtn').on('click', function () {
            const trainingId = $('#trainingFilter').val();
            const employeeId = $('#employeeAttendanceSelect').val();
            if (!trainingId || !employeeId) {
                alert('Please select a training and an employee.');
                return;
            }

            $.ajax({
                url: "{{ route('trainingInfo.attendants.add', ['training' => '0']) }}" + trainingId,
                type: 'POST',
                data: {
                    training_id: trainingId,
                    employee_id: employeeId,
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    alert('Employee successfully added as an attendee!');
                    fetchTabData(trainingId, 'attendants');
                },
            });
        });

        // Fetch invitees and attendees based on selected training
        $('#trainingFilter').on('change', function () {
            const trainingId = $(this).val();
            if (trainingId) {
                fetchTabData(trainingId, 'invites');
                fetchTabData(trainingId, 'attendants');
            }
        });

        function fetchTabData(trainingId, tab) {
            $.ajax({
                url: `{{ url('attendance_and_invites') }}/${tab}/${trainingId}`,
                success: function (response) {
                    $(`#${tab}Table`).html(response);
                },
            });
        }
    });
</script>
@endsection
