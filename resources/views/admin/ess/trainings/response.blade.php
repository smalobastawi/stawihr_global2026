@extends('admin.master')

@section('title', trans('training.invitation_response'))

@section('content')

    <div class="container-fluid">

        <div class="row bg-title">
            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor">
                        <a href="{{ url('dashboard') }}">
                            <i class="fa fa-home"></i> @lang('dashboard.dashboard')
                        </a>
                    </li>
                    <li>@yield('title')</li>
                </ol>
            </div>

        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="alert alert-{{ $status === 'accepted' ? 'success' : 'danger' }}">
                                You have successfully {{ $status }} the training invitation for:
                                <strong>{{ $training->subject }}</strong>
                            </div>
                            <hr>
                            <a href="{{ route('ess.trainings.index') }}" class="btn btn-lg btn-success">
                                @lang('training.employee_training_list')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
