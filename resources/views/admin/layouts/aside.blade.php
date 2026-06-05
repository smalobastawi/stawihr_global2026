 <div class="navbar-default sidebar" role="navigation">
     <div class="sidebar-nav slimscroll sidebar">
         <div class="sidebar-head">
             <h3>
                 <span class="fa-fw open-close">
                     <i class="ti-close ti-menu"></i>
                 </span>
                 <span class="hide-menu">
                     Navigation
                 </span>
             </h3>
         </div>
         <div class="user-profile">
             <div class="dropdown user-pro-body">

                 <div>
                    <img src="{{ asset('storage/uploads/front/' . $front_setting?->logo ?? '') }}" alt=""
                        class="logo-light" style="height: 50px; width: 60px;" />
                 </div>

                 <a href="#" class="dropdown-toggle u-dropdown " data-toggle="dropdown" role="button"
                     aria-haspopup="true" aria-expanded="false">
                     <span class="hideMenu">
                         @isset($employeeInfo)
                             {!! $employeeInfo->user_name !!}
                         @endisset
                     </span>
                 </a>

             </div>
         </div>
         <ul class="nav" id="side-menu">
             <li>
                 <a href="{{ url('dashboard') }}" class="waves-effect">
                     <i class="mdi mdi-home hideMenu" data-icon="v"></i> <span class="hide-menu hideMenu">
                         {{ __('menu.dashboard') }} </span>
                 </a>
             </li>
             @can('pmMenu__Self Service')
                 <li class="treeview-menu waves-effect">
                     <a href="javascript:void(0)" class="module">
                         <i class="iconFontSize mdi mdi-contacts hideMenu"></i> <span class="hide-menu hideMenu">Self
                             Service<span class="fa arrow"></span></span>
                     </a>

                     <ul class="treeview-menu nav nav-second-level" style="background-color: #00b3ee99">
                         @can('ess.leave.index')
                             <li>
                                 <a href="{{ route('ess.leave.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Leave</span>
                                 </a>
                             </li>
                         @endcan
                         @can('ess.leave.scheduled.index')
                             <li>
                                 <a href="{{ route('ess.leave.scheduled.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Scheduled Leaves</span>
                                 </a>
                             </li>
                         @endcan
                         @can('ess.payroll.index')
                             <li>
                                 <a href="{{ route('ess.payroll.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">My Payroll</span>

                                 </a>

                             </li>
                         @endcan
                        

                         @can('ess.loans.index')
                             <li>
                                 <a href="{{ route('ess.loans.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">My Loans</span>
                                 </a>
                             </li>
                         @endcan

                        @can('ess.approval.index')
                             <li>
                                 <a href="{{ route('ess.approval.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Approvals</span>

                                 </a>
                             </li>
                         @endcan

                         @can('ess.diciplinary.index')
                             <li>
                                 <a href="{{ route('ess.diciplinary.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Disciplinary</span>

                                 </a>

                             </li>
                         @endcan

                         @can('ess.shifts.index')
                             <li>
                                 <a href="{{ route('ess.shifts.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Shifts</span>

                                 </a>
                             </li>
                         @endcan


                         @can('ess.trainings.index')
                             <li>
                                 <a href="{{ route('ess.trainings.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Trainings</span>

                                 </a>

                             </li>
                         @endcan

                         @can('ess.awards.index')
                             <li>
                                 <a href="{{ route('ess.awards.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Awards</span>
                                 </a>
                             </li>
                         @endcan

                         @can('ess.notices.index')
                             <li>
                                 <a href="{{ route('ess.notices.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Notices</span>
                                 </a>
                             </li>
                         @endcan

                         @can('ess.documents.index')
                             <li>
                                 <a href="{{ route('ess.documents.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Policy Documents</span>

                                 </a>
                             </li>
                         @endcan
                         @can('ess.feedback.index')
                             <li>
                                 <a href="{{ route('ess.feedback.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">ESS Feedback</span>

                                 </a>
                             </li>
                         @endcan
                         @can('ess.feedback.anonymous.create')
                             <li>
                                 <a href="{{ route('ess.feedback.anonymous.create') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Anonymous Feedback</span>

                                 </a>
                             </li>
                         @endcan


                         @can('ess.recruitment.job.posts')
                             <li>
                                 <a href="{{ route('ess.recruitment.job.posts') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Job Posts</span>
                                 </a>
                             </li>
                         @endcan

                        @if (auth()->user()->can('ess.subordinates.index') || auth()->user()->can('ess.recruitment.job.posts'))
                            <li>
                                <a href="{{ route('ess.subordinates.index') }}">
                                    <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                    <span class="hideMenu">My Team</span>
                                </a>
                            </li>
                        @endif

                        @can('ess.performance.myAppraisals')
                            <li>
                                <a href="{{ route('ess.performance.myAppraisals') }}">
                                    <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                    <span class="hideMenu">My Performance</span>
                                </a>
                            </li>
                        @endcan

                        @can('ess.pip.myPlans')
                            <li>
                                <a href="{{ route('ess.pip.myPlans') }}">
                                    <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                    <span class="hideMenu">My PIP Plans</span>
                                </a>
                            </li>
                        @endcan

                        @can('ess.vehicle.myVehicle')
                            <li>
                                <a href="{{ route('ess.vehicle.myVehicle') }}">
                                    <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                    <span class="hideMenu">My Vehicle</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>

            @endcan

             @can('pmMenu__Administration')
                 <li class="treeview waves-effect">
                     <a href="javascript:void(0)" class="module">
                         <i class="iconFontSize mdi mdi-contacts hideMenu"></i>
                         <span class="hide-menu hideMenu">Administration <span class="fa arrow"></span></span>
                     </a>

                     <ul class="treeview-menu nav nav-second-level" style="background-color: #00b3ee99">
                         @can('user.index')
                             <li>
                                 <a href="{{ route('user.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Users</span>
                                 </a>
                             </li>
                         @endcan
                         @can('userRole.index')
                             <li>
                                 <a href="{{ route('userRole.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Roles</span>
                                 </a>
                             </li>
                         @endcan
                         @can('rolePermission.index')
                             <li>
                                 <a href="{{ route('rolePermission.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Role Permissions</span>
                                 </a>
                             </li>
                         @endcan
                         @can('company.permissions.index')
                             <li>
                                 <a href="{{ route('company.permissions.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Company Permissions</span>
                                 </a>
                             </li>
                         @endcan
                         @can('changePassword.index')
                             <li>
                                 <a href="{{ route('changePassword.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Change Password</span>
                                 </a>
                             </li>
                         @endcan
                         @can('reports.activity_logs')
                             <li>
                                 <a href="{{ route('reports.activity_logs') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Activity Logs</span>
                                 </a>
                             </li>
                         @endcan
                     </ul>
                 </li>
             @endcan

             @can(['document-categories.index', 'documents-upload.index'])
                 <li class="treeview waves-effect">
                     <a href="javascript:void(0)" class="module">
                         <i class="mdi mdi-file-document"></i>
                         <span class="hide-menu hideMenu">Policy Documents <span class="fa arrow"></span></span>
                     </a>
                     <ul class="treeview-menu nav nav-second-level" style="background-color: #00b3ee99">
                         @can('document-categories.index')
                             <li>
                                 <a class="hideMenu" href="{{ route('document-categories.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     Document Categories
                                 </a>
                             </li>
                         @endcan
                         @can('documents-upload.index')
                             <li>
                                 <a class="hideMenu" href="{{ route('documents-upload.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     Upload Documents
                                 </a>
                             </li>
                         @endcan
                     </ul>
                 </li>
             @endcan

             @can('pmMenu__Employee Management')
                 <li class="treeview-menu waves-effect">
                     <a href="javascript:void(0)" class="module">
                         <i class="iconFontSize mdi mdi-account-multiple-plus hideMenu"></i> <span
                             class="hide-menu hideMenu">Employee Management<span class="fa arrow"></span></span>
                     </a>

                     <ul class="treeview-menu nav nav-second-level" style="background-color: #00b3ee99">
                         @can('contract.index')
                             <li>
                                 <a href="{{ route('contract.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Staff Contracts</span>
                                 </a>
                             </li>
                         @endcan
                         @can('employee.index')
                             <li>

                                 <a href="{{ route('employee.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Manage Employees</span>
                                 </a>
                             </li>
                         @endcan
                        @can('department.index')
                            <li>
                                <a href="{{ route('department.index') }}">
                                    <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                    <span class="hideMenu">Departments</span>
                                </a>
                            </li>
                        @endcan
                        @can('employeeSection.index')
                            <li>
                                <a href="{{ route('employeeSection.index') }}">
                                    <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                    <span class="hideMenu">Sections</span>
                                </a>
                            </li>
                        @endcan
                        @can('designation.index')
                             <li>
                                 <a href="{{ route('designation.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Designations</span>
                                 </a>
                             </li>
                         @endcan
                         @can('location.index')
                             <li>
                                 <a href="{{ route('location.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">
                                         Location
                                     </span>
                                 </a>
                             </li>
                         @endcan

                         @can('region.index')
                             <li>
                                 <a href="{{ route('region.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">
                                         Regions
                                     </span>
                                 </a>
                             </li>
                         @endcan

                         @can('termination.index')
                             <li>
                                 <a href="{{ route('termination.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Termination</span>
                                 </a>
                             </li>
                         @endcan
                         @can('termination-checklist.index')
                             <li>

                                 <a href="{{ route('termination-checklist.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Termination Checklist</span>
                                 </a>
                             </li>
                         @endcan
                         {{-- Programs and Projects modules disabled per client request
                         @can('employee.program.index')
                             <li>
                                 <a href="{{ route('employee.program.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Programs</span>
                                 </a>
                             </li>
                         @endcan
                         @can('employee.project.index')
                             <li>
                                 <a href="{{ route('employee.project.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Projects</span>
                                 </a>
                             </li>
                         @endcan
                         --}}
                         {{-- Ethnicities module disabled per client request
                         @can('ethnicities.index')
                             <li>
                                 <a href="{{ route('ethnicities.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Ethnicities</span>
                                 </a>
                             </li>
                         @endcan
                         --}}
                         @can('employee.joinersReport')

                             <li>
                                 <a href="javascript:void(0)" class="module">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i><span
                                         class="hide-menu hideMenu">Reports<span class="fa arrow"></span></span>
                                 </a>
                                 <ul class="treeview-menu nav nav-third-level" style="background-color: #00b3ee99">
                                     @can('employee.joinersReport')
                                         <li class="">
                                             <a class="hideMenu" href="{{ route('employee.joinersReport') }}"> <i
                                                     class="fa fa-circle-o"></i>Joiners Report</a>

                                         </li>
                                     @endcan
                                     @can('employee.leaversReport')
                                         <li class="">
                                             <a class="hideMenu" href="{{ route('employee.leaversReport') }}"> <i
                                                     class="fa fa-circle-o"></i>Leavers report</a>
                                         </li>
                                     @endcan
                                    @can('employee.masterRoll')
                                        <li class="">
                                            <a class="hideMenu" href="{{ route('employee.masterRoll') }}"> <i
                                                    class="fa fa-circle-o"></i>Master Roll</a>
                                        </li>
                                    @endcan
                                    @canany(['employee.turnoverReport', 'employee.joinersReport'])
                                        <li class="">
                                            <a class="hideMenu" href="{{ route('employee.turnoverReport') }}"> <i
                                                    class="fa fa-circle-o"></i>Employee Turnover Report</a>
                                        </li>
                                    @endcanany
                                 </ul>
                             </li>
                         @endcan

                     </ul>
                </li>
            @endcan

            @can('pmMenu__Vehicle Management')
                <li class="treeview-menu waves-effect">
                    <a href="javascript:void(0)" class="module">
                        <i class="iconFontSize mdi mdi-car hideMenu"></i> <span
                            class="hide-menu hideMenu">Vehicle Management<span class="fa arrow"></span></span>
                    </a>

                    <ul class="treeview-menu nav nav-second-level" style="background-color: #00b3ee99">
                        @can('vehicle.index')
                            <li>
                                <a href="{{ route('vehicle.index') }}">
                                    <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                    <span class="hideMenu">Vehicles</span>
                                </a>
                            </li>
                        @endcan
                        @can('vehicle.assignment.index')
                            <li>
                                <a href="{{ route('vehicle.assignment.index') }}">
                                    <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                    <span class="hideMenu">Assignment History</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('pmMenu__Leave Management')
                {{-- Next menu group --}}
                <li class="treeview-menu waves-effect">
                    <a href="javascript:void(0)" class="module">
                        <i class="iconFontSize mdi mdi-format-line-weight hideMenu"></i> <span
                            class="hide-menu hideMenu">Leave Management<span class="fa arrow"></span></span>
                    </a>

                     <ul class="treeview-menu nav nav-second-level" style="background-color: #00b3ee99">
                         @can('holiday.index')
                             <li>
                                 <a href="javascript:void(0)" class="module">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i><span
                                         class="hide-menu hideMenu">Setup<span class="fa arrow"></span></span>
                                 </a>
                                 <ul class="treeview-menu nav nav-third-level" style="background-color: #00b3ee99">
                                     @can('holiday.index')
                                         <li class="">
                                             <a class="hideMenu" href="{{ route('holiday.index') }}">
                                                 <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>Manage
                                                 Holidays
                                             </a>
                                         </li>
                                     @endcan
                                     @can('publicHoliday.index')
                                         <li class="">
                                             <a class="hideMenu" href="{{ route('publicHoliday.index') }}"> <i data-icon="/"
                                                     class="linea-icon linea-basic fa-fw"></i>Public
                                                 Holidays</a>

                                         </li>
                                     @endcan
                                     @can('weeklyHoliday.index')
                                         <li class="">
                                             <a class="hideMenu" href="{{ route('weeklyHoliday.index') }}"> <i data-icon="/"
                                                     class="linea-icon linea-basic fa-fw"></i>Weekly
                                                 Holidays</a>

                                         </li>
                                     @endcan
                                     @can('leaveType.index')
                                         <li class="">
                                             <a class="hideMenu" href="{{ route('leaveType.index') }}"> <i data-icon="/"
                                                     class="linea-icon linea-basic fa-fw"></i>Leave
                                                 Types</a>

                                         </li>
                                     @endcan

                                     @can('leaveGroup.index')
                                         <li class="">
                                             <a class="hideMenu" href="{{ route('leaveGroup.index') }}"> <i data-icon="/"
                                                     class="linea-icon linea-basic fa-fw"></i>Leave
                                                 Groups</a>

                                         </li>
                                     @endcan
                                    
                                 </ul>
                             </li>
                         @endcan
                        
                         @can('rolloverLeaves')
                             <li>
                                 <a href="{{ route('rolloverLeaves') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Rollover leaves</span>
                                 </a>
                             </li>
                         @endcan
                        @can('applyForLeave.applyOnBehalf.create')
                            <li>
                                <a href="{{ route('applyOnBehalf.create') }}">
                                    <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                    <span class="hideMenu">Apply Leave On Behalf</span>
                                </a>
                            </li>
                        @endcan
                         @can('pendingLeaveRequests.pendingLeaveRequests')
                             <li>
                                 <a href="{{ route('pendingLeaveRequests.pendingLeaveRequests') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Pending Approvals</span>
                                 </a>
                             </li>
                         @endcan
                         @can('leaveManagement.manualUpload')
                             <li>
                                 <a href="{{ route('leaveManagement.manualUpload') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Manual Uploads</span>
                                 </a>
                             </li>
                         @endcan
                         @can('leave.adjustments.index')
                             <li>
                                 <a href="{{ route('leave.adjustments.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Leave Adjustments</span>
                                 </a>
                             </li>
                         @endcan
                         @can('leave.schedule.index')
                             <li>
                                 <a href="{{ route('leave.schedule.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Leave Schedule</span>
                                 </a>
                             </li>
                         @endcan
                         @can('allLeaveApplications.allLeaveApplications')
                             <li>
                                 <a href="{{ route('allLeaveApplications.allLeaveApplications') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">All applications</span>
                                 </a>
                             </li>
                         @endcan
                         <li>
                             <a href="javascript:void(0)" class="module">
                                 <i data-icon="/" class="linea-icon linea-basic fa-fw"></i><span
                                     class="hide-menu hideMenu">Reports<span class="fa arrow"></span></span>
                             </a>
                             <ul class="treeview-menu nav nav-third-level" style="background-color: #00b3ee99">
                                 @can('leaveReport.leaveReport.form')
                                     <li class="">
                                         <a class="hideMenu" href="{{ route('leaveReport.leaveReport.form') }}">
                                             <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                             Leave Report
                                         </a>
                                     </li>
                                 @endcan
                                 @can('summaryReport.summaryReport.form')
                                     <li class="">
                                         <a class="hideMenu" href="{{ route('summaryReport.summaryReport.form') }}">
                                             <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>Summary
                                             report</a>

                                     </li>
                                 @endcan
                                 @can('myLeaveReport.myLeaveReport.view')
                                     <li class="">
                                         <a class="hideMenu" href="{{ route('myLeaveReport.myLeaveReport.view') }}">
                                             <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>My
                                             Leave report</a>

                                     </li>
                                 @endcan

                                 @can('leave.report.balances.form')
                                     <li class="">
                                         <a class="hideMenu" href="{{ route('leave.report.balances.form') }}">
                                             <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>Leave
                                             Balances</a>

                                     </li>
                                 @endcan
                                 @can('leave.report.onLeaveToday')
                                     <li class="">
                                         <a class="hideMenu" href="{{ route('leave.report.onLeaveToday') }}">
                                             <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>On Leave Today
                                         </a>

                                     </li>
                                 @endcan
                                @can('leaveReport.fullOrganizationReport')
                                    <li class="">
                                        <a class="hideMenu" href="{{ route('leaveReport.fullOrganizationReport') }}">
                                            <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>Full
                                            Org report
                                        </a>
                                    </li>
                                @endcan
                                @can('leave.report.history')
                                    <li class="">
                                        <a class="hideMenu" href="{{ route('leave.report.history') }}">
                                            <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>Leave
                                            History</a>
                                    </li>
                                @endcan
                                @can('leave.report.encashment')
                                    <li class="">
                                        <a class="hideMenu" href="{{ route('leave.report.encashment') }}">
                                            <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>Leave
                                            Encashment</a>
                                    </li>
                                @endcan

                            </ul>
                        </li>

                     </ul>
                 </li>
             @endcan

             @can('pmMenu__Attendance')
                 <li class="treeview-menu waves-effect">
                     <a href="javascript:void(0)" class="module">
                         <i class="iconFontSize mdi mdi-clock-fast hideMenu"></i> <span
                             class="hide-menu hideMenu">Attendance<span class="fa arrow"></span></span>
                     </a>

                     <ul class="treeview-menu nav nav-second-level" style="background-color: #00b3ee99">
                         @can('workShift.index')
                             <li>
                                 <a href="javascript:void(0)" class="module">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i><span
                                         class="hide-menu hideMenu">Setup<span class="fa arrow"></span></span>
                                 </a>
                                 <ul class="treeview-menu nav nav-third-level" style="background-color: #00b3ee99">
                                     @can('workShift.index')
                                         <li class="">
                                             <a class="hideMenu" href="{{ route('workShift.index') }}"> <i data-icon="/"
                                                     class="linea-icon linea-basic fa-fw"></i>Work
                                                 Shifts</a>
                                         </li>
                                     @endcan
                                     @can('attendance.dashboard')
                                         <li class="">
                                             <a class="hideMenu" href="{{ route('attendance.dashboard') }}"> <i data-icon="/"
                                                     class="linea-icon linea-basic fa-fw"></i>IP
                                                 Whitelist</a>

                                         </li>
                                     @endcan
                                 </ul>
                             </li>
                         @endcan
                         @can('attendance.approveOvertimes')
                             <li>
                                 <a href="{{ route('attendance.approveOvertimes') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Overtime Approval</span>
                                 </a>
                             </li>
                         @endcan
                         @can('biometricDevices')
                             <li>
                                 <a href="{{ route('biometricDevices') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Biometric Devices</span>
                                 </a>
                             </li>
                         @endcan
                         <li>
                             <a href="javascript:void(0)" class="module">
                                 <i data-icon="/" class="linea-icon linea-basic fa-fw"></i><span
                                     class="hide-menu hideMenu">Reports<span class="fa arrow"></span></span>
                             </a>
                             <ul class="treeview-menu nav nav-third-level" style="background-color: #00b3ee99">
                                 @can('myAttendanceReport.myAttendanceReport')
                                     <li class="">
                                         <a class="hideMenu" href="{{ route('myAttendanceReport.myAttendanceReport') }}"> <i
                                                 data-icon="/" class="linea-icon linea-basic fa-fw"></i>My
                                             Attendance Report</a>

                                     </li>
                                 @endcan
                                 @can('dailyAttendance.dailyAttendance')
                                     <li class="">
                                         <a class="hideMenu" href="{{ route('dailyAttendance.dailyAttendance') }}">
                                             <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>Daily
                                             Attendance</a>

                                     </li>
                                 @endcan
                                 
                                 @can('monthlyAttendance.monthlyAttendance')
                                     <li class="">
                                         <a class="hideMenu" href="{{ route('monthlyAttendance.monthlyAttendance') }}">
                                             <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>Monthly
                                             report</a>

                                     </li>
                                 @endcan
                                
                                 @can('attendance.view_raw_logs')
                                     <li class="">
                                         <a class="hideMenu" href="{{ route('attendance.view_raw_logs') }}"> <i
                                                 data-icon="/" class="linea-icon linea-basic fa-fw"></i>Raw
                                             Reports</a>

                                     </li>
                                 @endcan

                             </ul>

                         </li>
                     </ul>
                 </li>
             @endcan

             @can('pmMenu__Payroll')
              
                 <li class="treeview-menu waves-effect">
                     <a href="javascript:void(0)" class="module">
                         <i class="iconFontSize mdi mdi-cash hideMenu"></i> <span class="hide-menu hideMenu">Payroll<span
                                 class="fa arrow"></span></span>
                     </a>

                     <ul class="treeview-menu nav nav-second-level" style="background-color: #00b3ee99">
                         <li>
                             <a href="javascript:void(0)" class="module">
                                 <i data-icon="/" class="linea-icon linea-basic fa-fw"></i><span
                                     class="hide-menu hideMenu">Setup<span class="fa arrow"></span></span>
                             </a>
                             <ul class="treeview-menu nav nav-third-level" style="background-color: #00b3ee99">
                                 @can('Tax-bands.index')
                                     <li class="">
                                         <a class="hideMenu" href="{{ route('tax-bands.index') }}"> <i data-icon="/"
                                                 class="linea-icon linea-basic fa-fw"></i>PAYE
                                             Setup</a>
                                     </li>
                                 @endcan


                                @can('earning_types.index')
                                    <li class="">
                                        <a class="hideMenu" href="{{ route('earning_types.index') }}"> <i data-icon="/"
                                                class="linea-icon linea-basic fa-fw"></i>Earning Types
                                        </a>

                                    </li>
                                @endcan
                             
                                @can('deduction_types.index')
                                     <li>
                                         <a href="{{ route('deduction_types.index') }}">
                                             <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                             <span class="hideMenu">Deduction Types</span>
                                         </a>
                                     </li>
                                 @endcan
                                

                                 @can('payroll.settings.periods.index')
                                     <li>
                                         <a href="{{ route('payroll.settings.periods.index') }}">
                                             <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                             <span class="hideMenu">Pay Period</span>
                                         </a>
                                     </li>
                                 @endcan
                                 @can('payroll.settings.pension-schemes.index')
                                     <li>
                                         <a href="{{ route('payroll.settings.pension-schemes.index') }}">
                                             <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                             <span class="hideMenu">Pension Schemes</span>
                                         </a>
                                     </li>
                                 @endcan
                                 @can('banks.index')
                                     <li>
                                         <a href="{{ route('banks.index') }}">
                                             <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                             <span class="hideMenu">Banks Setup</span>
                                         </a>
                                     </li>
                                 @endcan

                             </ul>
                         </li>
                    

                         @can('payroll.dashboard')
                             <li>
                                 <a href="{{ route('payroll.dashboard') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Payroll Dashboard</span>
                                 </a>
                             </li>
                         @endcan
                         @can('payroll.process.form')
                             <li>
                                 <a href="{{ route('payroll.process.form') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Process Payroll</span>
                                 </a>
                             </li>
                         @endcan
                       
                         @can('payroll.employees.index')
                             <li>
                                 <a href="{{ route('payroll.employees.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Payroll Profiles</span>
                                 </a>
                             </li>
                         @endcan

                       

                         @canany(['loans.dashboard', 'loans.index', 'loans.applications.index'])
                            <li>
                                <a href="javascript:void(0)" class="module">
                                    <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                    <span class="hide-menu hideMenu">Loan Management<span class="fa arrow"></span></span>
                                </a>
                                <ul class="treeview-menu nav nav-third-level" style="background-color: #00b3ee99">
                                    @can('loans.dashboard')
                                        <li class="">
                                            <a class="hideMenu" href="{{ route('loans.dashboard') }}">
                                                <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>Loan Dashboard
                                            </a>
                                        </li>
                                    @endcan
                                    @can('loans.index')
                                        <li class="">
                                            <a class="hideMenu" href="{{ route('loans.index') }}">
                                                <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>All Loans
                                            </a>
                                        </li>
                                    @endcan
                                    @can('loans.create')
                                        <li class="">
                                            <a class="hideMenu" href="{{ route('loans.create') }}">
                                                <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>Create Loan
                                            </a>
                                        </li>
                                    @endcan
                                    @can('loans.applications.index')
                                        <li class="">
                                            <a class="hideMenu" href="{{ route('loans.applications.index') }}">
                                                <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>Loan Applications
                                            </a>
                                        </li>
                                    @endcan
                                    @can('loans.applications.approve')
                                        <li class="">
                                            <a class="hideMenu" href="{{ route('loans.applications.pending') }}">
                                                <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>Pending Approvals
                                            </a>
                                        </li>
                                    @endcan
                                    @can('loans.manual-deductions.index')
                                        <li class="">
                                            <a class="hideMenu" href="{{ route('loans.manual-deductions.index') }}">
                                                <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>Manual Deductions
                                            </a>
                                        </li>
                                    @endcan
                                    @can('loans.types.index')
                                        <li class="">
                                            <a class="hideMenu" href="{{ route('loans.types.index') }}">
                                                <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>Loan Types
                                            </a>
                                        </li>
                                    @endcan
                                    @can('loans.reports.summary')
                                        <li class="">
                                            <a class="hideMenu" href="{{ route('loans.reports.summary') }}">
                                                <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>Loan Reports
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endcanany
                       
                         @can('employee_earnings.index')
                             <li>
                                 <a href="{{ route('employee_earnings.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Employee Earnings</span>
                                 </a>
                             </li>
                         @endcan
                         @can('employee_deductions.index')
                             <li>
                                 <a href="{{ route('employee_deductions.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Employee Deductions</span>
                                 </a>
                             </li>
                         @endcan
                         @can('payroll.overtime.index')
                             <li>
                                 <a href="{{ route('payroll.overtime.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Payroll Overtimes</span>
                                 </a>
                             </li>
                         @endcan



                         <li>
                             <a href="javascript:void(0)" class="module">
                                 <i data-icon="/" class="linea-icon linea-basic fa-fw"></i><span
                                     class="hide-menu hideMenu">Reports<span class="fa arrow"></span></span>
                             </a>
                             <ul class="treeview-menu nav nav-third-level" style="background-color: #00b3ee99">
                                 @can('payrollReportsIndex')
                                     <li class="">
                                         <a class="hideMenu" href="{{ route('payrollReportsIndex') }}"> <i data-icon="/"
                                                 class="linea-icon linea-basic fa-fw"></i>Payment Reports
                                             Dashboard</a>

                                     </li>
                                 @endcan
                                 @can('payroll.salary.history.index')
                                     <li class="">
                                         <a class="hideMenu" href="{{ route('payroll.salary.history.index') }}"> <i
                                                 data-icon="/" class="linea-icon linea-basic fa-fw"></i>Salary Change
                                             History</a>

                                     </li>
                                 @endcan

                                 @can('payroll9.index')
                                     <li class="">
                                         <a class="hideMenu" href="{{ route('payroll9.index') }}"> <i data-icon="/"
                                                 class="linea-icon linea-basic fa-fw"></i>P9
                                             Home</a>

                                     </li>
                                 @endcan

                                 @can('nssfReportsIndex')
                                     <li class="">
                                         <a class="hideMenu" href="{{ route('nssfReportsIndex') }}"> <i data-icon="/"
                                                 class="linea-icon linea-basic fa-fw"></i>NSSF
                                             report</a>

                                     </li>
                                 @endcan

                                 @can('shifReportsIndex')
                                     <li class="">
                                         <a class="hideMenu" href="{{ route('shifReportsIndex') }}"> <i data-icon="/"
                                                 class="linea-icon linea-basic fa-fw"></i>SHIF
                                             Report</a>

                                     </li>
                                 @endcan
                                 @can('ahlReportIndex')
                                     <li class=" ">
                                         <a class="hideMenu" href="{{ route('ahlReportIndex') }}"> <i data-icon="/"
                                                 class="linea-icon linea-basic fa-fw"></i>AHL
                                             report</a>

                                     </li>
                                 @endcan
                                 @can('paye.report.index')
                                     <li class=" ">
                                         <a class="hideMenu" href="{{ route('paye.report.index') }}"> <i data-icon="/"
                                                 class="linea-icon linea-basic fa-fw"></i>PAYE
                                             Report</a>

                                     </li>
                                 @endcan

                                 {{-- Project module disabled per client request
                                 @can('project.project-allocation-report.index')
                                     <li class="">
                                         <a class="hideMenu" href="{{ route('project.project-allocation-report.index') }}">
                                             <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>Project Allocation
                                             Report</a>
                                     </li>
                                 @endcan
                                 --}}

                                 @can('payroll.reports.earnings')
                                     <li class="">
                                         <a class="hideMenu" href="{{ route('payroll.reports.earnings') }}"> <i
                                                 data-icon="/" class="linea-icon linea-basic fa-fw"></i>Earnings
                                             report</a>

                                     </li>
                                 @endcan
                                 @can('project.project-allocation-report.index')
                                     <li class="">
                                         <a class="hideMenu" href="{{ route('payroll.reports.deductions') }}"> <i
                                                 data-icon="/" class="linea-icon linea-basic fa-fw"></i>Deductions
                                             report</a>

                                     </li>
                                 @endcan

                                 @can('payroll.reports.variance')
                                     <li class="">
                                         <a class="hideMenu" href="{{ route('payroll.reports.variance') }}"> <i
                                                 data-icon="/" class="linea-icon linea-basic fa-fw"></i>Variance 
                                             report</a>

                                     </li>
                                 @endcan



                                 @can('payroll9.index')
                                     <li class="">
                                         <a class="hideMenu" href="#"> <i data-icon="/"
                                                 class="linea-icon linea-basic fa-fw"></i>Bonus report
                                             report</a>

                                     </li>
                                 @endcan



                             </ul>

                         </li>
                     </ul>
                 </li>
                 {{-- end of payroll module --}}
             @endcan

             @can('pmMenu__Disciplinary')
                 <li class="treeview-menu waves-effect">
                     <a href="javascript:void(0)" class="module">
                         <i class="iconFontSize  fa fa-ban hideMenu"></i> <span class="hide-menu hideMenu">Disciplinary
                             <span class="fa arrow"></span></span>
                     </a>

                     <ul class="treeview-menu nav nav-second-level" style="background-color: #00b3ee99">
                         @can('disciplinary.category.index')
                             <li>
                                 <a href="{{ route('disciplinary.category.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Categories</span>

                                 </a>

                             </li>
                         @endcan
                         @can('disciplinary.cases.index')
                             <li>
                                 <a href="{{ route('disciplinary.cases.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa fa-fw"></i>
                                     <span class="hideMenu">Cases</span>

                                 </a>

                             </li>





                             <li>
                                 <a href="javascript:void(0)" class="module">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i><span
                                         class="hide-menu hideMenu">Reports<span class="fa arrow"></span></span>
                                 </a>
                                 <ul class="treeview-menu nav nav-third-level" style="background-color: #00b3ee99">
                                 @endcan
                                 @can('disciplinary.cases.action.index')
                                     <li>
                                         <a href="{{ route('disciplinary.cases.action.index') }}">
                                             <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                             <span class="hideMenu">Disciplinary Actions</span>
                                         </a>

                                     </li>
                                 @endcan


                             </ul>
                         </li>

                     </ul>
                 </li>
             @endcan



             @can('pmMenu__Performance Management')
                 <li class="treeview-menu waves-effect">
                     <a href="javascript:void(0)" class="module">
                         <i class="iconFontSize mdi mdi-trending-up hideMenu"></i>
                         <span class="hide-menu hideMenu">
                             Performance Management <span class="fa arrow"></span>
                         </span>
                     </a>

                     <ul class="treeview-menu nav nav-second-level" style="background-color: #00b3ee99">

                         {{-- ============================================ --}}
                         {{-- HR / ADMIN FUNCTIONS - Setup & Configuration --}}
                         {{-- ============================================ --}}
                         <li>
                             <a href="javascript:void(0)" class="module">
                                 <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                 <span class="hide-menu hideMenu">Setup <span class="fa arrow"></span></span>
                             </a>

                             <ul class="treeview-menu nav nav-third-level" style="background-color: #00b3ee99">
                                 @can('performance.ratingScale.index')
                                     <li><a class="hideMenu" href="{{ route('performance.ratingScale.index') }}"><i
                                                 class="fa fa-circle-o"></i> Rating Scales</a></li>
                                 @endcan

                                 @can('performance.reviewPeriod.index')
                                     <li><a class="hideMenu" href="{{ route('performance.reviewPeriod.index') }}"><i
                                                 class="fa fa-circle-o"></i> Review Periods</a></li>
                                 @endcan

                                 @can('performance.focusArea.index')
                                     <li><a class="hideMenu" href="{{ route('performance.focusArea.index') }}"><i
                                                 class="fa fa-circle-o"></i> Focus Areas</a></li>
                                 @endcan

                                 @can('performance.behavioralItem.index')
                                     <li><a class="hideMenu" href="{{ route('performance.behavioralItem.index') }}"><i
                                                 class="fa fa-circle-o"></i> Behavioral Items</a></li>
                                 @endcan
                             </ul>
                         </li>

                         {{-- ============================================ --}}
                         {{-- HR / ADMIN FUNCTIONS - Appraisal Management --}}
                         {{-- ============================================ --}}
                         @can('performance.appraisal.index')
                            <li style="border-top: 1px solid rgba(255,255,255,0.1); margin-top: 5px; padding-top: 5px;">
                                <a href="{{ route('performance.appraisal.index') }}">
                                    <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                    <span class="hideMenu">Manage Appraisals</span>
                                </a>
                            </li>
                         @endcan

                         {{-- ============================================ --}}
                         {{-- SUPERVISOR FUNCTIONS - Evaluation Workflow --}}
                         {{-- ============================================ --}}
                         @can('performance.supervisor.evaluations')
                            <li style="border-top: 1px solid rgba(255,255,255,0.1); margin-top: 5px; padding-top: 5px;">
                                <a href="{{ route('performance.supervisor.evaluations') }}">
                                    <i data-icon="/" class="linea-icon linea-basic fa-fw" style="color: #f0ad4e;"></i>
                                    <span class="hideMenu" style="color: #f0ad4e;">Supervisor Evaluation</span>
                                </a>
                            </li>
                         @endcan

                         {{-- ============================================ --}}
                         {{-- HOD FUNCTIONS - HOD Review Workflow --}}
                         {{-- ============================================ --}}
                         @can('performance.hod.evaluations')
                            <li>
                                <a href="{{ route('performance.hod.evaluations') }}">
                                    <i data-icon="/" class="linea-icon linea-basic fa-fw" style="color: #5bc0de;"></i>
                                    <span class="hideMenu" style="color: #5bc0de;">HOD Evaluation</span>
                                </a>
                            </li>
                         @endcan

                        {{-- PIP Management --}}
                         @can('pip.plan.index')
                             <li>
                                 <a href="{{ route('pip.plan.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">PIP Plans</span>
                                 </a>
                             </li>
                         @endcan

                         {{-- Performance Reports --}}
                         <li>
                             <a href="javascript:void(0)" class="module">
                                 <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                 <span class="hide-menu hideMenu">Reports <span class="fa arrow"></span></span>
                             </a>

                             <ul class="treeview-menu nav nav-third-level" style="background-color: #00b3ee99">
                                 @can('performance.report.department')
                                     <li><a class="hideMenu" href="{{ route('performance.report.department') }}"><i
                                                 class="fa fa-circle-o"></i> Department Report</a></li>
                                 @endcan

                                 @can('performance.report.employee')
                                     <li><a class="hideMenu" href="{{ route('performance.report.employee') }}"><i
                                                 class="fa fa-circle-o"></i> Employee Report</a></li>
                                 @endcan

                                 @can('performance.report.summary')
                                     <li><a class="hideMenu" href="{{ route('performance.report.summary') }}"><i
                                                 class="fa fa-circle-o"></i> Summary Report</a></li>
                                 @endcan

                                 @can('pip.report.dashboard')
                                     <li><a class="hideMenu" href="{{ route('pip.report.dashboard') }}"><i
                                                 class="fa fa-circle-o"></i> PIP Dashboard</a></li>
                                 @endcan

                                 @can('pip.report.byDepartment')
                                     <li><a class="hideMenu" href="{{ route('pip.report.byDepartment') }}"><i
                                                 class="fa fa-circle-o"></i> PIP By Department</a></li>
                                 @endcan

                                 @can('pip.report.byOutcome')
                                     <li><a class="hideMenu" href="{{ route('pip.report.byOutcome') }}"><i
                                                 class="fa fa-circle-o"></i> PIP By Outcome</a></li>
                                 @endcan
                             </ul>
                         </li>

                     </ul>
                 </li>
             @endcan

             @can('pmMenu__Recruitment')
                 <li class="treeview-menu waves-effect">
                     <a href="javascript:void(0)" class="module">
                         <i class="iconFontSize mdi mdi-newspaper hideMenu"></i> <span
                             class="hide-menu hideMenu">Recruitment<span class="fa arrow"></span></span>
                     </a>

                     <ul class="treeview-menu nav nav-second-level" style="background-color: #00b3ee99">
                         @can('jobRequisition.index')
                             <li>
                                 <a href="{{ route('jobRequisition.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Job Requisitions</span>
                                 </a>
                             </li>
                         @endcan
                         @can('jobPost.index')
                             <li>
                                 <a href="{{ route('jobPost.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Job Post</span>
                                 </a>
                             </li>
                         @endcan
                         @can('jobCandidate.index')
                             <li>
                                 <a href="{{ route('jobCandidate.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Job Candidates</span>
                                 </a>
                             </li>
                         @endcan
                     </ul>
                 </li>
             @endcan

             @can('pmMenu__Training')
                 <li class="treeview-menu waves-effect">
                     <a href="javascript:void(0)" class="module">
                         <i class="iconFontSize mdi mdi-web hideMenu"></i> <span class="hide-menu hideMenu">Training<span
                                 class="fa arrow"></span></span>
                     </a>

                     <ul class="treeview-menu nav nav-second-level" style="background-color: #00b3ee99">
                         @can('trainingType.index')
                             <li>
                                 <a href="{{ route('trainingType.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Training Type</span>

                                 </a>

                             </li>
                         @endcan

                         @can('training.facilitator.index')
                             <li>
                                 <a href="{{ route('training.facilitator.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Training Facilitator</span>

                                 </a>

                             </li>
                         @endcan
                         @can('trainingInfo.index')
                             <li>
                                 <a href="{{ route('trainingInfo.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Trainings</span>

                                 </a>

                             </li>
                         @endcan
                         {{-- @can('trainingInfo.attendants.index')
                                <li>
                                    <a href="{{ route('trainingInfo.attendants.index') }}">
                                        <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                        <span class="hideMenu">Participants/Attendance</span>

                                    </a>

                                </li>
                                @endcan --}}
                         @can('training.report.form')
                             <li>
                                 <a href="{{ route('training.report.form') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Training Report</span>
                                 </a>
                             </li>
                         @endcan

                     </ul>
                 </li>
             @endcan

             @can('pmMenu__Awards')
                 <li class="treeview-menu waves-effect">
                     <a href="javascript:void(0)" class="module">
                         <i class="iconFontSize mdi mdi-trophy-variant hideMenu"></i> <span
                             class="hide-menu hideMenu">Awards<span class="fa arrow"></span></span>
                     </a>

                     <ul class="treeview-menu nav nav-second-level" style="background-color: #00b3ee99">
                         @can('award.index')
                             <li>
                                 <a href="{{ route('award.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Award</span>

                                 </a>

                             </li>
                         @endcan
                     </ul>
                 </li>
             @endcan

             @can('pmMenu__Notice Board')
                 <li class="treeview-menu waves-effect">
                     <a href="javascript:void(0)" class="module">
                         <i class="iconFontSize mdi mdi-flag hideMenu"></i> <span class="hide-menu hideMenu">Notice
                             Board<span class="fa arrow"></span></span>
                     </a>

                     <ul class="treeview-menu nav nav-second-level" style="background-color: #00b3ee99">
                         @can('notice.index')
                             <li>
                                 <a href="{{ route('notice.index') }}">
                                     <i data-icon="/" class="linea-icon linea-basic fa-fw"></i>
                                     <span class="hideMenu">Notices</span>

                                 </a>

                             </li>
                         @endcan
                     </ul>
                 </li>
             @endcan

             @can('pmMenu__Annalytics')
                 <li class="treeview-menu waves-effect">
                     <a href="javascript:void(0)" class="module">
                         <i class="iconFontSize mdi mdi mdi-chart-line hideMenu"></i> <span
                             class="hide-menu hideMenu">Analytics<span class="fa arrow"></span></span>
                     </a>
                     <ul class="treeview-menu nav nav-second-level" style="background-color: #00b3ee99">
                         @can('reports.annalytics.view')
                             <li class="">
                                 <a class="hideMenu" href="{{ route('reports.annalytics.view') }}"> <i data-icon="/"
                                         class="linea-icon linea-basic fa-fw"></i>
                                     HR Insights</a>

                             </li>
                         @endcan
                     </ul>
                 </li>
             @endcan

             @can('pmMenu__Employee Feedback')
                 <li class="treeview-menu waves-effect">
                     <a href="javascript:void(0)" class="module">
                         <i class="iconFontSize fa fa-quote-left hideMenu"></i> <span class="hide-menu hideMenu">Employee
                             Feedback<span class="fa arrow"></span></span>
                     </a>
                     <ul class="treeview-menu nav nav-second-level" style="background-color: #00b3ee99">
                         @can('feedback.category.index')
                             <li class="">
                                 <a class="hideMenu" href="{{ route('feedback.category.index') }}"> <i data-icon="/"
                                         class="linea-icon linea-basic fa-envelope"></i>
                                     Categories</a>

                             </li>
                         @endcan

                         {{-- @can('ess.feedback.index') movode to ess section
                                <li class="">
                                    <a class="hideMenu" href="{{ route('ess.feedback.index') }}"> <i data-icon="/"
                                            class="linea-icon fa fa-envelope-o fa-fw"></i>
                                        My Feedback</a>

                                </li>
                                @endcan --}}
                         @can('employee.feedback.index')
                             <li class="">
                                 <a class="hideMenu" href="{{ route('employee.feedback.index') }}"> <i data-icon="/"
                                         class="linea-icon fa fa-envelope-o fa-fw"></i>
                                     Employee Feedbacks</a>

                             </li>
                         @endcan
                         {{-- @can('ess.feedback.anonymous.create') moved to ess section of the menu
                                <li class="">
                                    <a class="hideMenu" href="{{ route('ess.feedback.anonymous.create') }}"> <i
                                            data-icon="/" class="linea-icon fa fa-envelope fa-fw"></i>
                                        Anonymous Feedback</a>

                                </li>
                                @endcan --}}
                         @can('anonymous.feedback.index')
                             <li class="">
                                 <a class="hideMenu" href="{{ route('anonymous.feedback.index') }}"> <i data-icon="/"
                                         class="linea-icon fa fa-envelope fa-fw"></i>
                                     Anonymous Box</a>

                             </li>
                         @endcan

                     </ul>

                 </li>
             @endcan

             {{-- @can('pmMenu__Survey')
                 <li class="treeview-menu waves-effect">
                     <a href="javascript:void(0)" class="module">
                         <i class="iconFontSize fa fa-question-circle hideMenu"></i> <span class="hide-menu hideMenu">
                             Surveys<span class="fa arrow"></span></span>
                     </a>
                     <ul class="treeview-menu nav nav-second-level" style="background-color: #00b3ee99">

                         <li class="">
                             <a class="hideMenu" href="{{ route('survey.index') }}">
                                 <i data-icon="/" class="linea-icon linea-basic fa-envelope"></i> Surveys
                             </a>
                         </li>

                     </ul>

                 </li>
             @endcan --}}

             @can('pmMenu__Settings')
                 <li class="treeview-menu waves-effect">
                     <a href="javascript:void(0)" class="module">
                         <i class="iconFontSize mdi mdi mdi-settings hideMenu"></i> <span
                             class="hide-menu hideMenu">Settings<span class="fa arrow"></span></span>
                     </a>
                     <ul class="treeview-menu nav nav-second-level" style="background-color: #00b3ee99">
                         @can('front.setting')
                             <li class="">
                                 <a class="hideMenu" href="{{ route('front.setting') }}"> <i data-icon="/"
                                         class="linea-icon linea-basic fa-fw"></i>General
                                     Settings</a>

                             </li>
                         @endcan
                         @can('company.index')
                             <li class="">
                                 <a class="hideMenu" href="{{ route('company.index') }}"> <i data-icon="/"
                                         class="linea-icon linea-basic fa-fw"></i>Companies
                                 </a>

                             </li>
                         @endcan
                         @can('company.setting')
                             <li class="">
                                 <a class="hideMenu" href="{{ route('company.setting') }}"> <i data-icon="/"
                                         class="linea-icon linea-basic fa-fw"></i>Company
                                     Settings</a>

                             </li>
                         @endcan
                         @can('generalSettings.index')
                             <li class="">
                                 <a class="hideMenu" href="{{ route('generalSettings.index') }}"> <i data-icon="/"
                                         class="linea-icon linea-basic fa-fw"></i>Print
                                     Head Settings</a>

                             </li>
                         @endcan

                         @can('approval-workflows.index')
                             <li class="">
                                 <a class="hideMenu" href="{{ route('approval-workflows.index') }}"> <i data-icon="/"
                                         class="linea-icon linea-basic fa-fw"></i>
                                     Approval Workflows</a>

                             </li>
                         @endcan
                          @can('financial_year.index')
                              <li class="">
                                  <a class="hideMenu" href="{{ route('financial_year.index') }}"> <i data-icon="/"
                                          class="linea-icon linea-basic fa-fw"></i>
                                      Financial Year Settings</a>

                              </li>
                          @endcan
                          @can('systemSettings.index')
                              <li class="">
                                  <a class="hideMenu" href="{{ route('systemSettings.index') }}"> <i data-icon="/"
                                          class="linea-icon linea-basic fa-fw"></i>
                                      System Settings</a>

                              </li>
                          @endcan
                      </ul>
                  </li>
              @endcan

             <li>
                 <a href="{{ route('user.guide') }}" class="waves-effect" target="_blank" rel="noopener">
                     <i class="mdi mdi-book-open-page-variant hideMenu"></i>
                     <span class="hide-menu hideMenu">User Guide</span>
                 </a>
             </li>
         </ul>
     </div>
 </div>
