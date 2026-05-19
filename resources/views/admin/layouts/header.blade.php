 <nav class="navbar navbar-default navbar-static-top m-b-0">
     <div class="navbar-header">

         <ul class="nav navbar-top-links navbar-right pull-right">

             @if (isset($activeCompanies) && count($activeCompanies) > 0)
                 <li class="dropdown">
                     <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" style="color:#fff">
                         <i class="fa fa-building fa-fw" style="color: #fff !important;"></i>
                         Company: {{ $currentCompany ? $currentCompany->name : 'SuperAdmin Mode' }}
                         <span class="caret"></span>
                     </a>
                     <ul class="dropdown-menu dropdown-user animated flipInY">
                         @if ($currentCompany)
                             <li>
                                 <form method="POST" action="{{ route('company.switch') }}" style="display: inline;">
                                     @csrf
                                     <button type="submit" class="dropdown-item"
                                         style="border: none; background: none; width: 100%; text-align: left; ">
                                         <i class="fa fa-globe mr-2"></i>
                                         Switch to SuperAdmin Mode (All Companies)
                                     </button>
                                 </form>
                             </li>
                             <li role="separator" class="divider"></li>
                         @endif
                         @foreach ($activeCompanies as $company)
                             <li>
                                 <form method="POST" action="{{ route('company.switch') }}" style="display: inline;">
                                     @csrf
                                     <input type="hidden" name="company_id" value="{{ $company->id }}">
                                     <button type="submit" class="dropdown-item"
                                         style="border: none; background: none; width: 100%; text-align: left; padding: 10px 20px;">
                                         <i class="fa fa-building mr-2"></i>
                                         {{ $company->name }}
                                         @if ($currentCompany && $currentCompany->id == $company->id)
                                             <span class="badge badge-success">Current</span>
                                         @endif
                                     </button>
                                 </form>
                             </li>
                         @endforeach
                     </ul>
                 </li>
             @endif
             <li>
                 <a href="javascript:void(0)" class="waves-effect waves-light" style="color:#fff;">
                     Financial Year:
                     @if (isset($activeFinancialYear) && $activeFinancialYear)
                         {{ \Carbon\Carbon::parse($activeFinancialYear->start_date)->format('d M') }} -
                         {{ \Carbon\Carbon::parse($activeFinancialYear->end_date)->format('d M') }},
                         {{ \Carbon\Carbon::parse($activeFinancialYear->end_date)->format('Y') }}
                     @else
                         No active financial year
                     @endif
                 </a>
             </li>
             <li class="dropdown">
                 <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#">
                     <i class="fa fa-bell fa-fw" style="color: #fff !important;"></i>
                     @if (auth()->user() && auth()->user()->unreadNotifications->count() > 0)
                         <span class="badge badge-danger">{{ auth()->user()->unreadNotifications->count() }}</span>
                     @endif
                 </a>
                 <ul class="dropdown-menu dropdown-user animated flipInY">
                     <span class="dropdown-item dropdown-header" style="font-size: 14px !important;">
                         {{ auth()->user() ? auth()->user()->unreadNotifications->count() : 0 }} Notifications
                     </span>
                     <div role="separator" class="divider"></div>
                     @if (auth()->user())
                         @foreach (auth()->user()->unreadNotifications->take(5) as $notification)
                             @php
                                 $hasLink = isset($notification->data['link']);
                                 $link = $hasLink ? $notification->data['link'] : '#';
                             @endphp
                             <a href="{{ $hasLink ? route('ess.notifications.markRead', ['id' => $notification->id, 'redirect' => $notification->data['link']]) : route('ess.notifications.markRead', $notification->id) }}"
                                 class="dropdown-item" style="white-space: normal; overflow-wrap: break-word;">
                                 <i class="fa fa-envelope mr-2"></i>
                                 <span style="display: inline-block; width: calc(100% - 30px); vertical-align: top;">
                                     {{ $notification->data['message'] ?? 'New notification' }}
                                     @if (isset($notification->data['supervisor_name']))
                                         <small class="text-muted">(Supervisor:
                                             {{ $notification->data['supervisor_name'] }})</small>
                                     @endif
                                 </span>
                                 <br>
                                 <span class="text-muted text-sm float-right">
                                     {{ $notification->created_at->diffForHumans() }}
                                 </span>
                             </a>

                             <div role="separator" class="divider"></div>
                         @endforeach
                     @endif
                     {{-- <div role="separator" class="divider"></div> --}}

                     <a href="{{ route('ess.notifications.index') }}" class="dropdown-item dropdown-footer"
                         style="font-size: 14px !important;">
                         See All Notifications
                     </a>

                 </ul>
             </li>
             <li class="dropdown">
                 @php
                     // Get the authenticated user
                     $user = auth()->user();

                     // Determine the display name
                     if ($user && $user->employeeDetails) {
                         $employee = $user->employeeDetails;
                         $displayName = trim(
                             $employee->first_name .
                                 ' ' .
                                 ($employee->middle_name ?? '') .
                                 ' ' .
                                 ($employee->last_name ?? ''),
                         );
                         // If the concatenated name is empty, fallback to username
                         $displayName = !empty($displayName)
                             ? $displayName
                             : $user->user_name ?? ($user->email ?? 'User');
                     } else {
                         $displayName = $user->user_name ?? ($user->email ?? 'User');
                     }

                     // Also store the full name for the dropdown menu
                     if ($user && $user->employeeDetails) {
                         $fullName = trim(
                             $employee->first_name .
                                 ' ' .
                                 ($employee->middle_name ?? '') .
                                 ' ' .
                                 ($employee->last_name ?? ''),
                         );
                         $fullName = !empty($fullName) ? $fullName : $displayName;
                     } else {
                         $fullName = $displayName;
                     }
                 @endphp

                 @if (isset($employeeInfo->photo))
                     <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#">
                         <img src="{!! asset('uploads/employeePhoto/' . $employeeInfo->photo) !!}" alt="user-img" width="36" class="img-circle">
                         <b class="hidden-xs" style="color: #fff !important;">{{ $displayName }}</b>
                     </a>
                 @else
                     <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#">
                         <img src="{!! asset('admin_assets/img/default.png') !!}" alt="user-img" width="36" class="img-circle">
                         <b class="hidden-xs" style="color: #fff !important;">
                             <span class="hideMenu" style="color: #fff !important;">{{ $displayName }}</span>
                         </b>
                     </a>
                 @endif

                 <ul class="dropdown-menu dropdown-user animated flipInY">
                     <li>
                         <a href="{{ url('profile') }}">
                             <i class="ti-user"></i>
                             {{ $fullName }}
                         </a>
                     </li>

                     @if (config('app.password_login'))
                         <li role="separator" class="divider"></li>
                         <li>
                             <a href="{{ url('changePassword') }}">
                                 <i class="ti-settings"></i>
                                 @lang('common.change_password')
                             </a>
                         </li>
                     @endif

                     <li role="separator" class="divider"></li>
                     <li>
                         <a href="{{ URL::to('/logout') }}">
                             <i class="fa fa-power-off"></i> @lang('common.logout')
                         </a>
                     </li>
                 </ul>
             </li>
         </ul>

         <ul class="nav navbar-top-links navbar-left">
             <li>
                 <a href="javascript:void(0)" class="open-close waves-effect waves-light">
                     <i class="ti-menu tiMenu"></i>
                 </a>
             </li>
             <li class="dropdown">

                 <ul class="dropdown-menu mailbox animated bounceInDown">
                     <li>
                         <div class="drop-title">@lang('common.chose_a_language')</div>
                     </li>
                     <li>
                         <div class="message-center">
                             <a href="{{ url('local/en') }}">

                                 <h5>English</h5>
                             </a>
                         </div>


                         <div class="message-center">
                             <a href="{{ url('local/es') }}" title="Spanish">

                                 <h5>Español</h5>
                             </a>
                         </div>

                         <div class="message-center">
                             <a href="{{ url('local/fr') }}" title="French">

                                 <h5>Française</h5>
                             </a>
                         </div>

                         <div class="message-center">
                             <a href="{{ url('local/th') }}" title="Thai">

                                 <h5>ไทย</h5>
                             </a>
                         </div>

                     </li>
                     <!-- <li>
                            <a class="text-center" href="javascript:void(0);"> <strong>@lang('common.see_all_languages')</strong> <i class="fa fa-angle-right"></i> </a>
                        </li> -->
                 </ul>
                 <!-- /.dropdown-messages -->
             </li>
         </ul>
     </div>

     <!-- /.navbar-header -->
     <!-- /.navbar-top-links -->
     <!-- /.navbar-static-side -->
 </nav>
