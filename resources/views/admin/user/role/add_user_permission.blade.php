@extends('admin.master')
@section('content')
@section('title', __('role.add_role_permission'))
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>@lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i> @lang('role.role_permission')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <form action="{{ route('rolePermission.store') }}" method="POST">
                            @csrf
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-8 col-sm-12">
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
                                        <div class="form-group">
                                            <label for="role">Role<span class="validateRq">*</span></label>
                                            
                                            @can('rolePermission.index')
                                                <select name="role_id" class="form-control role_id select2 required" onchange="getMenu(this)" id="role_id">
                                                    @foreach($data as $__key => $__value)
                                                        <option value="{{ $__key }}" {{ (string)Request::old('role_id') == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
                                                    @endforeach
                                                </select>
                                            @endcan
                                        </div>
                                    </div>
                                    <div class="col-md-4"></div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <div class="ShowMember">

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="submit" id="formSubmit" disabled="disabled" class="btn btn-info btn_style"><i class="fa fa-check"></i> @lang('common.update')</button>
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
    $(document).on('change','[data-menu]',function(event){
        if(this.checked==false){
            var getMenuId = $(this).attr('data-menu');
            $('[data-formenu="'+getMenuId+'"]').prop('checked',false);
        }
    });
    
    $(document).on('change','[data-formenu]',function(event){
        if(this.checked==true){
            var getMenuId = $(this).attr('data-formenu');
            $('[data-menu="'+getMenuId+'"]').prop('checked',true);
        }
    });
    
    $(document).on("click", '.checkAll', function (event) {
        if (this.checked) {
            $('.inputCheckbox').each(function () {
                this.checked = true;
            });
        } else {
            $('.inputCheckbox').each(function () {
                this.checked = false;
            });
        }
    });

    function getMenu(select) {
        var role_id = $('.role_id').val();
        if (role_id != '') {
            $('body').find('#formSubmit').attr('disabled', false);
        } else {
            $('.inputCheckbox').each(function(){
                this.checked = false;
            });
            $('body').find('#formSubmit').attr('disabled', true);
            return false;
        }

        var action = "{{ URL::to('rolePermission/get_all_menu') }}";
        $.ajax({
            type: 'POST',
            url: action,
            data: {role_id: role_id, '_token': $('input[name=_token]').val()},
            success: function (result) {
                $('.ShowMember').html(result);
            }
        });
    }
</script>

<script>
    // Module level - When a module is checked/unchecked
    $(document).on("click", '.menucls', function (event) {
        var menuId = $(this).attr('id');
        if (this.checked) {
            $('.' + menuId).each(function () {
                this.checked = true;
            });
        } else {
            $('.' + menuId).each(function () {
                this.checked = false;
            });
        }
    });

    // Menu level - When a menu is checked/unchecked
    $(document).on("click", '.pmgcls', function (event) {
        var groupId = $(this).data('group-id');
        if (this.checked) {
            $('.pmgcls__' + groupId).each(function () {
                this.checked = true;
            });
        } else {
            $('.pmgcls__' + groupId).each(function () {
                this.checked = false;
            });
        }
    });

    // Section level - When a section is checked/unchecked
    $(document).on("click", '.sub_section_cls', function (event) {
        var sectionId = $(this).attr('id').replace('subSectionCls__', '');
        if (this.checked) {
            $('.subSectionCls__' + sectionId).each(function () {
                this.checked = true;
            });
        } else {
            $('.subSectionCls__' + sectionId).each(function () {
                this.checked = false;
            });
        }
    });

    // ActionType level - When an action type is checked/unchecked
    $(document).on("click", '.action_type_cls', function (event) {
        var actionTypeId = $(this).attr('id').replace('actionCls__', '');
        if (this.checked) {
            $('.actionCls__' + actionTypeId).each(function () {
                this.checked = true;
            });
        } else {
            $('.actionCls__' + actionTypeId).each(function () {
                this.checked = false;
            });
        }
    });
</script>
@endsection