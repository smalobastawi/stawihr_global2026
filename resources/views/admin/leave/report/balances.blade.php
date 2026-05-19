@extends('admin.master')
@section('content')
@section('title')
    @lang('leave.leave_balances')
@endsection
<style>
    .employeeName{
        position: relative;
    }
    #employee_id-error{
        position: absolute;
        top: 66px;
        left: 0;
        width: 100%;
        height: 100%;
    }
</style>
<script>
    jQuery(function (){
        $("#leaveReport").validate();
    });
</script>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="mdi mdi-table fa-fw"></i>@yield('title')
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <hr>
                        @if(count($results) > 0)
                            <h4 class="text-right">
                                {{-- <a class="btn btn-success" style="color: #fff" href="{{route('leave.summaryReport.download')}}?employee_id={{$employee_id}}&from_date={{$from_date}}&to_date={{$to_date}}">
                                    <i class="fa fa-download fa-lg" aria-hidden="true"></i> @lang('common.download') PDF
                                </a> --}}
                            </h4>
                        @endif
                        
                            <div class="table-responsive">
                                <table id="dataTable" class="table table-bordered">
                                    <thead class="tr_header">
                                        <!-- Filter Row -->
                                        <tr id="filterRow">
                                            <th></th>
                                            <th>
                                                <input type="text" id="employee_name_filter" class="form-control" placeholder="@lang('common.employee_name')">
                                            </th>
											<th>
                                                <select id="leaveType_filter" class="form-control select2">
                                                    <option value="">All</option>
                                                    @foreach($leaveTypes as $leaveType)
                                                        <option value="{{ $leaveType->leave_type_id }}">{{ $leaveType->leave_type_name }}</option>
                                                    @endforeach
                                                </select>
                                            </th>
											<th colspan="4"></th>
                                            <th>
                                                <select id="location_filter" class="form-control select2">
                                                    <option value="">All</option>
                                                    @foreach($locations as $location)
                                                        <option value="{{ $location->location_id }}">{{ $location->location_name }}</option>
                                                    @endforeach
                                                </select>
                                            </th>
                                            <th>
                                                <select id="department_filter" class="form-control select2">
                                                    <option value="">All</option>
                                                    @foreach($departments as $department)
                                                        <option value="{{ $department->department_id }}">{{ $department->department_name }}</option>
                                                    @endforeach
                                                </select>
                                            </th>
                                            <th>
                                                <select id="designation_filter" class="form-control select2">
                                                    <option value="">All</option>
                                                    @foreach($designations as $designation)
                                                        <option value="{{ $designation->designation_id }}">{{ $designation->designation_name }}</option>
                                                    @endforeach
                                                </select>
                                            </th>
                                            <!-- Empty cells for columns not filtered -->
                                           
                                        </tr>
                                        <!-- Table Header Row -->
                                        <tr>
                                            <th style="width:100px;">@lang('common.serial')</th>
                                            <th>Employee Name</th>
                                           
                                            <th>@lang('leave.leave_type')</th>
                                            <th>@lang('leave.number_of_day')</th>
                                            <th>@lang('leave.leave_consume')</th>
                                            <th>Rolled over leaves</th>
                                            <th>@lang('leave.current_balance')</th>
											<th>Location</th>
                                            <th>Department</th>
                                            <th>Designation</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody_filtered_data">
                                        @if(count($results) > 0)
                                            {{$sl = null}}
                                            @foreach($results as $value)
                                                <tr>
                                                    <td>{{ ++$sl }}</td>
                                                    <td>{{ $value['employee_name'] }}</td>
                                                     <td>{{ $value['leave_type_name'] }}</td>
                                                    <td>{{ $value['totalDays'] }}</td>
                                                    <td>{{ $value['days_used'] }}</td>
                                                    <td>{{ $value['roll_over_days'] }}</td>
                                                    <td>{{ $value['totalBlance'] }}</td>
													<td>{{ $value['employee_location'] ?? '' }}</td>
                                                    <td>{{ $value['employee_department'] ?? '' }}</td>
                                                    <td>{{ $value['employee_designation'] ?? '' }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="10">@lang('common.no_data_available')!</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        
                    </div><!-- panel-body -->
                </div><!-- panel-wrapper -->
            </div><!-- panel -->
        </div><!-- col-sm-12 -->
    </div><!-- row -->
</div><!-- container-fluid -->

@endsection

@section('page_scripts')
<script>
    $(document).ready(function(){
        // Initialize select2 on filter fields
        $('.select2').select2();

        // Listen for change or keyup events on the filter fields
        $('#employee_name_filter, #location_filter, #department_filter, #designation_filter,#leaveType_filter').on('change keyup', function(){
            // Retrieve filter values
            var employeeName = $('#employee_name_filter').val();
            var location     = $('#location_filter').val();
            var department   = $('#department_filter').val();
            var designation  = $('#designation_filter').val();
			var leave_type  = $('#leaveType_filter').val();
			var filtering  = 'filtering';

            // AJAX call to fetch filtered data
            $.ajax({
                url: '',
                type: 'GET',
                data: {
                    employee_name: employeeName,
                    location: location,
                    department: department,
                    designation: designation,
					leave_type: leave_type,
					filtering:filtering
                },
                success: function(response){
                    // Update table body with the new filtered data.
                    // You might need to adjust this part based on the response format.
                    $('#tbody_filtered_data').html(response);
                },
                error: function(xhr, status, error){
                    console.error('Filter error:', error);
                }
            });
        });
    });
</script>
@endsection
