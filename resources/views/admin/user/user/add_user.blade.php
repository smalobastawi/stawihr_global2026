@extends('admin.master')
@section('content')
    @section('title','Add User')

    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                            Dashboard</a></li>
                    <li>@yield('title')</li>

                </ol>
            </div>
            <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
                <a href="{{route('user.index')}}"
                   class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                            class="fa fa-list-ul" aria-hidden="true"></i> View User</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i> @yield('title')</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                                aria-hidden="true">×</span></button>
                                    @foreach($errors->all() as $error)
                                        <strong>{!! $error !!}</strong><br>
                                    @endforeach
                                </div>
                            @endif
                            @if(session()->has('success'))
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                    </button>
                                    <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                </div>
                            @endif
                            @if(session()->has('error'))
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                    </button>
                                    <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                                </div>
                            @endif
                            @if(isset($editModeData))
                                <form method="POST" action="{{ route('user.update', $editModeData->id) }}" id="userForm" enctype="multipart/form-data">
@csrf
@method('PUT')
                            @else
                                <form method="POST" action="{{ route('user.store') }}" id="userForm" enctype="multipart/form-data">
@csrf
                            @endif
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">Select Roles<span
                                                        class="validateRq">*</span></label>
                                            <select class="roleSelect" multiple name="roles[]"
                                                    style="width: 200px" required>
                                                @foreach ($roles as $value)
                                                    <option value="{{ $value->id }}"
                                                    >
                                                        {{ $value->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="exampleInput">User Name<span class="validateRq">*</span></label>
                                            <input type="text" name="user_name" id="user_name" class="form-control required user_name" value="{{ Request::old('user_name') }}" placeholder="Enter user name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="exampleInput">Email<span class="validateRq">*</span></label>
                                            <input type="email" name="email" id="email" class="form-control required email" value="{{ Request::old('email') }}" placeholder="Enter email" required>
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="password">Password<span class="validateRq">*</span></label>
                                            <input id="passInput" class="form-control" placeholder="Your Password"
                                                   name="password" type="password" size="30" aria-required="false" required>
                                            <span class="input-group-addon" role="button" title="veiw password" id="passBtn">
                                                <i class="fa fa-eye fa-fw" aria-hidden="true"></i>
                                             </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="password_confirmation">Password Confirmation<span
                                                        class="validateRq">*</span></label>
                                            <input id="passInput1" class="form-control" placeholder="Password confirmation"
                                                   name="password_confirmation" type="password" size="30" aria-required="false" id="confirm_password" required>
                                            <span class="input-group-addon" role="button" title="veiw password" id="passBtn1">
                                                <i class="fa fa-eye fa-fw" aria-hidden="true"></i>
                                             </span>

                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="picture">Status<span class="validateRq">*</span></label>
                                            <select name="status" class="form-control status select2 required">
<option value="1" {{ Request::old('status') == '1' ? 'selected' : '' }}>Active</option>
<option value="2" {{ Request::old('status') == '2' ? 'selected' : '' }}>Inactive</option>
</select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-info btn_style"><i class="fa fa-check"></i>
                                            Submit
                                        </button>
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
@endsection

@section('page_scripts')
    <script>

        $('.roleSelect').select2({
            placeholder: 'Search roles',
        });

        const PassBtn = document.querySelector('#passBtn');
        PassBtn.addEventListener('click', () => {
            const input = document.querySelector('#passInput');
            input.getAttribute('type') === 'password' ? input.setAttribute('type', 'text') : input.setAttribute('type', 'password');

            if (input.getAttribute('type') === 'text') {
                PassBtn.innerHTML = '<i class="fa fa-eye-slash"></i>';
            } else {
                PassBtn.innerHTML = '<i class="fa fa-eye fa-fw" aria-hidden="true"></i>';
            }

        });

        const PassBtn1 = document.querySelector('#passBtn1');
        PassBtn1.addEventListener('click', () => {
            const input = document.querySelector('#passInput1');
            input.getAttribute('type') === 'password' ? input.setAttribute('type', 'text') : input.setAttribute('type', 'password');

            if (input.getAttribute('type') === 'text') {
                PassBtn1.innerHTML = '<i class="fa fa-eye-slash"></i>';
            } else {
                PassBtn1.innerHTML = '<i class="fa fa-eye fa-fw" aria-hidden="true"></i>';
            }

        });

        var password = document.getElementById("password")
            , confirm_password = document.getElementById("confirm_password");

        function validatePassword(){
            if(password.value != confirm_password.value) {
                confirm_password.setCustomValidity("Passwords Don't Match");
            } else {
                confirm_password.setCustomValidity('');
            }
        }

        password.onchange = validatePassword;
        confirm_password.onkeyup = validatePassword;
    </script>

@endsection
