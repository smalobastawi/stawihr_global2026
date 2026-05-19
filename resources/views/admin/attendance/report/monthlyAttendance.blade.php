@extends('admin.master')
@section('content')
@section('title')
Report - Monthly Attendance
@endsection
<style>
	.employeeName{
		position: relative;
	}
	#employee_id-error{
		position: absolute;
		top: 66px;
		left: 0;
		width: 100%he;
		width: 100%;
		height: 100%;
	}
		/*
		tbody {
			display:block;
			height:500px;
			overflow:auto;
		}
		thead, tbody tr {
			display:table;
			width:100%;
			table-layout:fixed;
		}
		thead {
			width: calc( 100% - 1em )
		}*/


</style>
<script>
    jQuery(function (){
        $("#monthlyAttendance").validate();
     });

	jQuery(function (){
		$(document).ready(function() {
			$('.select2').select2();
		});
	});

</script>
<div class="container-fluid">
	<div class="row bg-title">
		<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
			<ol class="breadcrumb">
				<li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
				<li>@yield('title')</li>
			</ol>
		</div>

	</div>

	<div class="row">
		<div class="col-sm-12">
			<div class="panel panel-info">
				<div class="panel-heading"><i class="mdi mdi-table fa-fw"></i>@yield('title')</div>
				<div class="panel-wrapper collapse in" aria-expanded="true">
					<div class="panel-body">
						<div class="row">
							<div id="searchBox">
								<form method="POST">
							@csrf
								<div class="col-md-1"></div>
								<div class="col-md-3">
									<div class="form-group employeeName">
										<label class="control-label" for="email">@lang('common.employee')<span class="validateRq">*</span></label>
										<select class="form-control employee_id select2 required" required name="employee_id">
											<option value="">---- @lang('common.please_select') ----</option>
											@foreach($employeeList as $value)
												<option value="{{$value->employee_id}}"  @if(@$value->employee_id == $employee_id) {{"selected"}} @endif>{{$value->first_name}} {{$value->last_name}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="col-md-3">
									<label class="control-label" for="email">@lang('common.from_date')<span class="validateRq">*</span></label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										<input type="text" class="form-control dateField required" readonly placeholder="@lang('common.from_date')"  name="from_date" value="@if(isset($from_date)) {{$from_date}}@else {{ dateConvertDBtoForm(date('Y-m-01')) }} @endif">
									</div>
								</div>

								<div class="col-md-3">
									<label class="control-label" for="email">@lang('common.to_date')<span class="validateRq">*</span></label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										<input type="text" class="form-control dateField required" readonly placeholder="@lang('common.to_date')"  name="to_date" value="@if(isset($to_date)) {{$to_date}}@else {{ dateConvertDBtoForm( date("Y-m-t", strtotime(date('Y-m-01')))) }} @endif">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<input type="submit" id="filter" style="margin-top: 25px; width: 100px;" class="btn btn-info " value="@lang('common.filter')">
									</div>
								</div>
								</form>
							</div>
							</div>
						<hr>
						@if(count($results) > 0 && $results!='')
							<h4 class="text-right">
								<a class="btn btn-success" style="color: #fff" href="{{ URL('attendance/downloadMonthlyAttendance/?employee_id='.$employee_id.'&from_date='.$from_date.'&to_date='.$to_date)}}"><i class="fa fa-download fa-lg" aria-hidden="true"></i> @lang('common.download') PDF</a>
							</h4>
						@endif


						@if($results!='' )
						<div class="table-responsive">
							<table id="" class="table table-bordered">
								<thead class="tr_header">
								<tr>
									<th style="width:100px;">@lang('common.serial')</th>
									<th>@lang('common.date')</th>
									<th>@lang('attendance.in_time')</th>
									<th>@lang('attendance.out_time')</th>
									<th>@lang('attendance.working_time')</th>
									<th>@lang('attendance.late')</th>
									<th>@lang('attendance.late_time')</th>
									<th>@lang('attendance.over_time')</th>
									<th>@lang('common.status')</th>
								</tr>
								</thead>
								<tbody>
                                <?php
                                $totalPresent = 0;
                                $totalAbsence = 0;
                                $totalLeave   = 0;
                                $totalLate    = 0;
                                $totalHour    = 0;
                                $totalMinit   = 0;


                                ?>

								{{$serial = null}}
								@if(count($results) > 0)
									@foreach($results AS $value)

										<tr>
											<td style="width:100px;">{{++$serial}}</td>
											<td>{{ $value['date']->format('Y-m-d') }}</td>
											<td>
                                                <?php
                                                if ($value['in_time'] != '') {
                                                    echo date('h:i A', strtotime( $value['in_time']));
                                                } else {
                                                    echo "--";
                                                }
                                                ?>
											</td>
											<td>
                                                <?php
                                                if ($value['out_time'] != '') {
                                                    echo date('h:i A', strtotime($value['out_time']));
                                                } else {
                                                    echo "--";
                                                }
                                                ?>
											</td>

											<td>
                                                <?php
													if( $value['out_time'] == null){
                                                        echo "--";
													}else{
                                                        if ($value['working_time'] != '00:00:00' ) {
                                                            echo $d =  date('H:i', strtotime($value['working_time']));

                                                            $hour_minit = explode(':',$d);

                                                            $totalHour += $hour_minit[0];
                                                            $totalMinit += $hour_minit[1];


                                                        } else {
                                                            echo 'One Time Punch';
                                                        }
													}

                                                ?>
											</td>
											<td>
                                                <?php
													if($value['ifLate'] == ''){
													    echo "--";
													}else{
														if($value['ifLate'] == 'Yes'){
															echo "<b style='color: red'>".__('common.yes')."</b>";
															$totalLate +=1;
														}else{
															echo __('common.no');
														}
                                                    }

                                                ?>
											</td>
											<td>
                                                <?php
													if($value['totalLateTime'] ==''){
                                                        echo "--";
													}else{
                                                        if ($value['totalLateTime'] != '00:00:00') {
                                                            echo date('H:i', strtotime($value['totalLateTime']));
                                                        }else{
                                                            echo "--";
                                                        }
													}
                                                ?>

											</td>
											<td>
												<?php
												if ($value['out_time'] != null) {
													$hours = 9 * 60 * 60;
													$clockOut = \Carbon\Carbon::parse($value['out_time']);
													$clockIn = \Carbon\Carbon::parse($value['in_time']);
													$totalDuration1 = $clockOut->diffInSeconds($clockIn);


													if ($totalDuration1 > $hours) {
														$interval = $totalDuration1-$hours;
														echo gmdate('H:i', $interval);
													} else {
														echo '0';
													}
												} else {
													echo '--';
												}
												?>
											</td>
											<td>
                                                <?php
                                                if($value['presence_status'] =='ABSENT'){
                                                    echo "<span class='label label-danger'>".__('common.absence')."</span>";
                                                    $totalAbsence +=1;
                                                }elseif($value['presence_status'] =='PRESENT'){
                                                    echo "<span class='label label-success'>".__('common.present')."</span></p>";
													$totalPresent +=1;
                                                }else{
                                                    echo "<span class='label label-info'>".__('common.leave')."</span>";
													$totalLeave +=1;
                                                }
                                                ?>
											</td>
										</tr>
									@endforeach

								@endif
                               <?php

								$total_working_hour = (($totalHour*60)+$totalMinit)/60;

								?>
								@if(count($results) > 0)
									<tr>
										<td colspan="7"></td>
										<td style="background: #eee"><b>@lang('attendance.total_working_days'): &nbsp;</b></td>
										<td style="background: #eee"><b>{{$totalDaysInMonth}}</b>  @lang('common.days')</td>
									</tr>
									<tr>
										<td colspan="7"></td>
										<td style="background: #fff"><b>@lang('attendance.total_present'): &nbsp;</b></td>
										<td style="background: #fff"><b>{{$totalPresent}}</b> @lang('common.days')</td>
									</tr>
									<tr>
										<td colspan="7"></td>
										<td style="background: #eee"><b>@lang('attendance.total_absence'): &nbsp;</b></td>
										<td style="background: #eee"><b>{{$totalAbsence}}</b> @lang('common.days')</td>
									</tr>
									<tr>
										<td colspan="7"></td>
										<td style="background: #fff"><b>@lang('attendance.total_leave'): &nbsp;</b></td>
										<td style="background: #fff"><b>{{$totalLeave}}</b> @lang('common.days')</td>
									</tr>
									<tr>
										<td colspan="7"></td>
										<td style="background: #eee"><b>@lang('attendance.total_late'): &nbsp;</b></td>
										<td style="background: #eee"><b>{{$totalLate}}</b> @lang('common.days')</td>
									</tr>

								    

									<tr>
										<td colspan="7"></td>
										<td style="background: #eee"><b>@lang('attendance.actual_working_hour'): &nbsp;</b></td>
										<td style="background: #eee"><b>{{round($total_working_hour)}}</b> @lang('common.hours')</td>
									</tr>

                                   
									

								
								@endif
								</tbody>
							</table>
						</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

