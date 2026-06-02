<!DOCTYPE html>
<head>

	<title>Change Password</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
	  integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link href="{!! asset('admin_assets/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') !!}"
	  rel="stylesheet">
<!-- toast CSS -->
<link href="{!! asset('admin_assets/plugins/bower_components/toast-master/css/jquery.toast.css') !!}"
	  rel="stylesheet">
<!-- morris CSS -->
<link href="{!! asset('admin_assets/plugins/bower_components/morrisjs/morris.css') !!}" rel="stylesheet">
<!-- animation CSS -->
<link href="{!! asset('admin_assets/css/animate.css') !!}" rel="stylesheet">
<!-- Custom CSS -->
<link href="{!! asset('admin_assets/css/style.css') !!}" rel="stylesheet">
<!-- color CSS -->
<link href="{!! asset('admin_assets/css/colors/megna-dark.css') !!}" id="theme" rel="stylesheet">
<!-- data table CSS -->
<link href="{!! asset('admin_assets/plugins/bower_components/datatables/jquery.dataTables.min.css') !!}"
	  rel="stylesheet" type="text/css"/>

<link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet"
	  type="text/css"/>
<!-- Date Picker -->
<link rel="stylesheet" href="{!! asset('admin_assets/plugins/bower_components/datepicker/datepicker3.css') !!}">
<!-- Daterange picker -->
<link rel="stylesheet"
	  href="{!! asset('admin_assets/plugins/bower_components/daterangepicker/daterangepicker-bs3.css') !!}">
<!-- time picker-->
<link rel="stylesheet"
	  href="{!! asset('admin_assets/plugins/bower_components/timepicker/bootstrap-timepicker.min.css') !!}">
<!-- sweetalert-->
<link rel="stylesheet" href="{!! asset('admin_assets/plugins/bower_components/sweetalert/sweetalert.css') !!}">
<!-- select 2 -->
<link rel="stylesheet" href="{!! asset('admin_assets/plugins/bower_components/select2/select2.min.css') !!}">
<!-- toast CSS -->
<link href="{!! asset('admin_assets/plugins/bower_components/toast-master/css/jquery.toast.css') !!}"
	  rel="stylesheet">
<!-- Star Ratings -->
<link href="{!! asset('admin_assets/plugins/bower_components/rateyo/jquery.rateyo.min.css') !!}" rel="stylesheet">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

<script src="{!! asset('admin_assets/plugins/bower_components/jquery/dist/jquery.min.js')!!}"></script>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
	  rel="stylesheet">

<script type="text/javascript">
	var base_url = "{{ url('/').'/' }}";
</script>
<style>
	/*for yellow bg*/

	.navbar-header {
		background: #222a48;
	}

	#side-menu li a {
		color: #fff;
		border-left: 0px solid #2f323e;
	}

	.top-left-part .dark-logo {
		display: block;

	}

	.tiMenu {
		color: #fff;
	}

	.sidebar {
		background: #43436678;;
		box-shadow: 1px 0px 20px rgba(0, 0, 0, 0.08);
	}

	.hideMenu {
		color: #fff;
	}

	#side-menu ul > li > a.active {
		color: #EDDF10;
		font-weight: 400;
	}

	#side-menu ul > li > a:hover {
		color: #fff;
	}

	/*for yellow bg*/

	.bg-title .breadcrumb {
		background: 0 0;
		margin-bottom: 0;
		float: none;
		padding: 0;
		margin-bottom: 9px;
		font-weight: 700;
		color: #777;
	}

	.select2-container .select2-selection--single .select2-selection__rendered {
		height: auto;
		margin-top: -6px;
		padding-left: 0;
		padding-right: 0;
	}

	.select2-container .select2-selection--single {
		box-sizing: border-box;
		cursor: pointer;
		display: block;
		height: 35px;
	}

	.select2-container--default .select2-selection--single, .select2-selection .select2-selection--single {
		border: 1px solid #d2d6de;
		border-radius: 0;
		padding: 8px 11px;
	}

	.select2-container--default .select2-selection--single .select2-selection__arrow {
		height: 26px;
		position: absolute;
		top: 4px;
		right: 1px;
		width: 20px;
	}

	.breadcrumbColor a {
		color: #41b3f9 !important;
	}

	tr td {
		color: black !important; 
	}

	.tr_header {
		background-color: #EDF1F5;
	}

	table.dataTable thead th, table.dataTable thead td {
		padding: 10px 18px;
		border-bottom: 1px solid #e4e7ea;
	}

	.btnColor {
		color: #fff !important;
	}

	.validateRq {
		color: red;
	}

	.panel .panel-heading {
		border-radius: 0;
		font-weight: 500;
		font-size: 16px;
		padding: 10px 25px;
	}

	.btn_style {
		width: 106px;
	}

	.error {
		color: red;
	}

	.password-input-group {
		width: 100%;
	}

	.password-input-group .form-control {
		border-right: 0;
	}

	.password-input-group .input-group-addon {
		background: #fff;
		cursor: pointer;
		padding: 6px 12px;
	}

	.password-input-group .input-group-addon:hover {
		background: #f5f5f5;
	}

	.password-input-group .input-group-addon .glyphicon {
		color: #555;
	}

	#generatePassword {
		margin-top: 6px;
	}


</style>
</head>


@lang('common.change_password')


<div class="container-fluid">

	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-info">
				<div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i> @lang('common.change_password')</div>
				<div class="panel-wrapper collapse in" aria-expanded="true">
					<div class="panel-body">
						<form method="POST" action="{{ route('changePassword.update', Auth::user()->id) }}" id="changePassword" class="form-horizontal" enctype="multipart/form-data">
@csrf
@method('PUT')
						<div class="form-body">
							<div class="row">
								<div class="col-md-offset-2 col-md-6">
									@if($errors->any())
									<div class="alert alert-danger alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
										@foreach($errors->all() as $error)
										<strong>{!! $error !!}</strong><br>
										@endforeach
									</div>
									@endif
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
								</div>
							</div>
							<div class="row">
								<div class="col-md-8">
									<div class="form-group">
										<label class="control-label col-md-4">@lang('employee.user_name')<span class="validateRq">*</span></label>
										<div class="col-md-8">
											<input type="text" name="user_name_display" id="user_name" class="form-control user_name" value="{{ session('logged_session_data.user_name') }}" readonly>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-8">
									<div class="form-group">
										<label class="control-label col-md-4">@lang('passwords.old_password')<span class="validateRq">*</span></label>
										<div class="col-md-8">
											<div class="input-group password-input-group">
												<input type="password" name="oldPassword" id="oldPassword" class="form-control required oldPassword" placeholder="{{ __('passwords.old_password') }}" autocomplete="current-password">
												<span class="input-group-addon toggle-password" data-target="#oldPassword" title="Show password" role="button" tabindex="0" aria-label="Toggle old password visibility">
													<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
												</span>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-8">
									<div class="form-group">
										<label class="control-label col-md-4">Password rules:</label>
										<div class="col-md-4">
											<p class="control-label" style="margin-bottom: 0;">
												At least 6 Characters<br>
												At least 1 uppercase &amp; 1 lowercase<br>
												At least 1 special character<br>
												At least 1 number
											</p>
											<button type="button" class="btn btn-warning btn-sm" id="generatePassword" title="Generate a random password that meets the rules">
												<span class="glyphicon glyphicon-refresh"></span> Generate Password
											</button>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-8">
									<div class="form-group">
										<label class="control-label col-md-4">@lang('passwords.new_password')<span class="validateRq">*</span></label>
										<div class="col-md-8">
											<div class="input-group password-input-group">
												<input type="password" name="password" id="password" class="form-control required password" placeholder="{{ __('passwords.new_password') }}" autocomplete="new-password">
												<span class="input-group-addon toggle-password" data-target="#password" title="Show password" role="button" tabindex="0" aria-label="Toggle new password visibility">
													<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
												</span>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-8">
									<div class="form-group">
										<label class="control-label col-md-4">@lang('passwords.confirm_password')<span class="validateRq">*</span></label>
										<div class="col-md-8">
											<div class="input-group password-input-group">
												<input type="password" name="password_confirmation" id="password_confirmation" class="form-control required password_confirmation" placeholder="{{ __('passwords.confirm_password') }}" autocomplete="new-password">
												<span class="input-group-addon toggle-password" data-target="#password_confirmation" title="Show password" role="button" tabindex="0" aria-label="Toggle confirm password visibility">
													<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
												</span>
											</div>
										</div>
									</div>
								</div>
							</div>
					@if(env('2FA_PASSWORD_CHANGE'))
							<div class="row">
								<div class="col-md-8">
									<div class="form-group">
										<label class="control-label col-md-4">@lang('passwords.verification_code')<span class="validateRq">*</span></label>
										<div class="col-md-8">
											<input type="number" name="verification_code" id="verification_code" class="form-control required verification_code" placeholder="{{ __('passwords.verification_code') }}" required>
										</div>
									</div>
								</div>
							</div>
					@endif
						</div>
						<div class="form-actions">
							<div class="row">
								<div class="col-md-8">
									<div class="row">
										<div class="col-md-offset-4 col-md-8">
											<button type="submit" class="btn btn-info btn_style"><i class="fa fa-pencil"></i> @lang('common.update') </button>
											<button type="button" class="btn btn-danger btn_style"><a href="{{URL::to('/logout')}}" > <i class="fa fa-cross"></i> Cancel</a> </button>
											@if(env('2FA_PASSWORD_CHANGE'))
													<button type="button" class="btn btn-warning btn_style" id="receive_otp"> Send OTP </button>
											 @endif
											 <button type="button" class="btn btn-info btn_style" style="color:white"><a style="color:white" href="{{ URL::previous() ?? URL::to('/') }}"> <i class="fa fa-cross"></i> Go Back</a> </button>

										</div>

									</div>
								</div>
							</div>
						</div>
						</form>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
    function generateCompliantPassword(length) {
        length = length || 12;
        var upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        var lower = 'abcdefghjkmnpqrstuvwxyz';
        var numbers = '23456789';
        var special = '#?!@$%^&*-';
        var all = upper + lower + numbers + special;
        var password = '';
        var i;

        password += upper.charAt(Math.floor(Math.random() * upper.length));
        password += lower.charAt(Math.floor(Math.random() * lower.length));
        password += numbers.charAt(Math.floor(Math.random() * numbers.length));
        password += special.charAt(Math.floor(Math.random() * special.length));

        for (i = password.length; i < length; i++) {
            password += all.charAt(Math.floor(Math.random() * all.length));
        }

        return password.split('').sort(function () {
            return Math.random() - 0.5;
        }).join('');
    }

    function setPasswordFieldType($input, visible) {
        $input.attr('type', visible ? 'text' : 'password');
    }

    function updateToggleIcon($toggle, visible) {
        var $icon = $toggle.find('.glyphicon');
        $icon.removeClass('glyphicon-eye-open glyphicon-eye-close');
        $icon.addClass(visible ? 'glyphicon-eye-close' : 'glyphicon-eye-open');
        $toggle.attr('title', visible ? 'Hide password' : 'Show password');
    }

    $(document).ready(function(){
        $('.toggle-password').on('click keypress', function (e) {
            if (e.type === 'keypress' && e.which !== 13 && e.which !== 32) {
                return;
            }
            e.preventDefault();

            var $toggle = $(this);
            var $input = $($toggle.data('target'));
            var visible = $input.attr('type') === 'password';

            setPasswordFieldType($input, visible);
            updateToggleIcon($toggle, visible);
        });

        $('#generatePassword').on('click', function () {
            var generated = generateCompliantPassword(12);

            $('#password').val(generated);
            $('#password_confirmation').val(generated);

            $('#password, #password_confirmation').each(function () {
                var $input = $(this);
                var $toggle = $input.closest('.password-input-group').find('.toggle-password');
                setPasswordFieldType($input, true);
                updateToggleIcon($toggle, true);
            });
        });

        $('#changePassword').submit(function(e){
            var password = $('#password').val();
            var password_confirmation = $('#password_confirmation').val();
            var oldPassword = $('#oldPassword').val();

            if (password !== password_confirmation) {
                e.preventDefault(); // prevent form from submitting
                alert('Passwords do not match!');
            }
			if (password == oldPassword) {
                e.preventDefault(); // prevent form from submitting
                alert('New password cannot be same as old password!');
            }
        });

		$('#receive_otp').click(function (e) {
			e.preventDefault(); // Prevent default form submission (if inside a form)
			var $otpButton = $(this);
			$otpButton.prop('disabled', true).text('Sending...');

			$.ajax({
				url: `/api/send-password-change-otp`,
				type: "POST",
				headers: { 
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
					'Accept': 'application/json' // ✅ Forces JSON response
				},
				data: {
					_token: "{{ csrf_token() }}",
					user_name: $('#user_name').val(), 
				},
				success: function(response) {
					if (response.success) {
						alert('OTP sent successfully! Please check your email and SMS.');
					} else {
						alert(response.message);
					}
				},
				error: function(xhr, status, error) {
					try {
						let response = JSON.parse(xhr.responseText);
						if (response.message) {
							alert(response.message); 
						} else {
							alert('An unexpected error occurred. Please try again.');
						}
					} catch (e) {
						alert('An error occurred while processing your request.');
					}
					console.error(xhr.responseText);
				},
				complete: function() {
					$otpButton.prop('disabled', false).text('Send OTP'); 
				}
			});
});


		
		$('#resend-otp').click(function (e) {
			e.preventDefault(); // Prevent default form submission (if inside a form)
			var $otpButton = $(this);
			$otpButton.prop('disabled', true).text('Sending...'); 
			$.ajax({
				url: `/resend-otp`,
				type: "POST",
				data: {
					
					_token: "{{ csrf_token() }}", 
				},
				success: function(response) {
					if (response.success) {
						alert('OTP sent successfully! Please check your email and SMS.');
					} else {

						alert('Error sending OTP.Please try again');
					}
				},
				error: function(xhr, status, error) {
					alert('There was an error sending the OTP.Please try again');
				},
				complete: function() {
					$otpButton.prop('disabled', false).text('Resend OTP'); 
				}
			});
		});

    });
</script>
