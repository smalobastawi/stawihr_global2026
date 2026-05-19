@extends('admin.master')

@section('title', $training->subject .' | '. config('app.name'))

@section('content')
    <style>
        .employeeName {
            position: relative;
        }

        #employee_id-error {
            position: absolute;
            top: 66px;
            left: 0;
            width: 100%;
        }

        /* Add to your CSS file */
        .employee-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        #selectAllCheckbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        #addSelectedBtn {
            transition: all 0.3s;
        }
    </style>

    <div class="container-fluid">

        <div class="row bg-title">
            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                            @lang('dashboard.dashboard')</a></li>
                    <li>{{ $training->subject }}</li>
                </ol>
            </div>


            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                <a href="{{ route('trainingInfo.index') }}"
                    class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                    <i aria-hidden="true"></i>View Training Lists
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">

                            <div class="row">
                                <div class="col-sm-12">
                                    <h3 class="text-center">
                                        <strong>{{ $training->subject }}</strong>
                                    </h3>
                                    <h4 class="text-center">
                                        <strong>Training Date: {{ \Carbon\Carbon::parse($training->start_date)->format('d F, Y') }} to {{ \Carbon\Carbon::parse($training->end_date)->format('d F, Y') }}</strong>
                                    </h4>
                                    {{-- <h4 class="text-center">
                                        <strong>Training Time: {{ $training->time }}</strong>
                                    </h4>
                                    <h4 class="text-center">
                                        <strong>Training Venue: {{ $training->venue }}</strong>
                                    </h4> --}}
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div id="searchBox">
                                    <form method="POST" action="{{ route('trainingInfo.attendants.index', $training) }}"
                                        accept-charset="UTF-8" id="report">
                                        @include('admin.training.report.filterform')
                                    </form>
                                </div>
                            </div>
                            <hr>
                            {{-- @if ($results->isNotEmpty())
                                @if (!$training->invites_approved)
                                    <form action="{{ route('trainingInfo.invitees.approve', $training->id) }}"
                                        method="post">
                                        @method('PUT')
                                        @csrf
                                        <h4 class="text-right">
                                            <button class="btn btn-success" style="color: #fff" id="tr_approve_invites"
                                                href="{{ route('training.report.download') }}">
                                                <i class="fa fa-check fa-lg" aria-hidden="true"></i> Approve Invites
                                            </button>
                                        </h4>
                                    </form>
                                @elseif(!$training->attendance_approved)
                                    <form action="{{ route('trainingInfo.attendants.approve', $training->id) }}"
                                        method="post">
                                        @method('PUT')
                                        @csrf
                                        <h4 class="text-right">
                                            <button class="btn btn-success" style="color: #fff" id="tr_approve_invites"
                                                href="{{ route('training.report.download') }}">
                                                <i class="fa fa-check fa-lg" aria-hidden="true"></i> Approve Attendance
                                                List
                                            </button>
                                        </h4>
                                    </form>
                                @endif
                            @endif --}}

                            @if (isset($results))
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#invites" aria-controls="invites" role="tab"
                                            data-toggle="tab">Invites</a>
                                    </li>

                                        
                                    <li role="presentation">
                                        <a href="#add_invites" aria-controls="add_invites" role="tab"
                                            data-toggle="tab">Add Invites</a>
                                    </li>
                                    

                                    <li role="presentation">
                                        <a href="#attendances" aria-controls="attendances" role="tab"
                                            data-toggle="tab">Attendances</a>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="invites">
                                        <div class="table-responsive" id="invite_data">
                                            @include('admin.training.report.filteredInvites', [
                                                'results' => $invited,
                                            ])
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="attendances">
                                        <div class="table-responsive" id="attendance_data">
                                            @include('admin.training.report.filtererdTable', [
                                                'results' => $attended,
                                            ])
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="add_invites">
                                        @include('admin.training.invites_attendances.add.invite', [
                                            'emps' => $employeesForInvite,
                                        ])
                                    </div>
                                </div>
                            @endif


                        </div>
                        <!--/.panel-body -->
                    </div>
                    <!--/.panel-wrapper collapse in -->
                </div>
                <!--/.panel panel-info -->
            </div>
            <!--/.col-sm-12 -->
        </div>
        <!--/.row -->



    </div>
    <!--/.container-fluid -->

@endsection

@section('page_scripts')

    <script>
        $(document).ready(function () {
            function initFilters() {
                $('.select2').select2();
                $('#report').on('change', 'select', function() {
                    $.ajax({
                        url: "{{ route('trainingInfo.attendants.index', $training) }}",
                        data: $('#report').serialize() + '&filtering=1',
                        success: function(response) {
                            $('#report').html(response.formData);
                            $('#attendance_data').html(response.attendances);
                            $('#invite_data').html(response.invites);
                            $('#add_invites').html(response.inviteFormData);
                            $('#add_attendances').html(response.attendanceFormData);
                            initFilters(); // Reinitialize
                        }
                    });
                });

                // Handle adding invites
                $(document).on('click', '.adding_invite', function(e) {
                    e.preventDefault();
                    const button = $(this);
                    
                    $.post(button.attr('addRoute'), {
                        _token: "{{ csrf_token() }}",
                        employee_id: button.data('employee-id')
                    }).done(function() {
                        button.prop("disabled", true).html('<i class="fa fa-check"></i> Added');
                    });
                });
            }

            initFilters();

            // Handle form submission on tr_download button click
            $(document).on('click', '#tr_download', function() {
                let form = $('#report');
                let actionUrl = $(this).attr('href');
                form.attr('action', actionUrl);
                form.attr('method', 'GET');
                form.submit();
            });

            // Select All functionality using event delegation
            $(document).on('click', '#selectAllCheckbox, #selectAllBtn', function(e) {
                e.preventDefault();
                const isChecked = $('#selectAllCheckbox').prop('checked');
                $('.employee-checkbox').prop('checked', !isChecked);
                $('#selectAllCheckbox').prop('checked', !isChecked);
                toggleAddSelectedBtn();
            });

            // Individual checkbox change
            $(document).on('change', '.employee-checkbox', function() {
                toggleAddSelectedBtn();
            });

            // Add selected employees
            $(document).on('click', '#addSelectedBtn', function() {
                const selectedEmployees = [];
                $('.employee-checkbox:checked').each(function() {
                    selectedEmployees.push({
                        employee_id: $(this).data('employee-id'),
                        training_id: $(this).data('training-id')
                    });
                });

                if (selectedEmployees.length > 0) {
                    addMultipleInvites(selectedEmployees);
                }
            });


            function toggleAddSelectedBtn() {
                const hasChecked = $('.employee-checkbox:checked').length > 0;
                $('#addSelectedBtn').prop('disabled', !hasChecked);
            }

            function addMultipleInvites(employees) {
                $.ajax({
                    url: "{{ route('trainingInfo.invitees.addMultiple') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        employees: employees
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload(); // Refresh to show updated list
                        }
                    },
                    error: function(xhr) {
                        alert('Error: ' + xhr.responseJSON.message);
                        location.reload(); // Refresh to show updated list
                    }
                });
            }

        });
    </script>

@endsection
