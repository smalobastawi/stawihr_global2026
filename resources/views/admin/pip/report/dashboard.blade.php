@extends('admin.master')
@section('content')
@section('title')
PIP Dashboard
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                @foreach (urlTree() as $item)
                    <li class="breadcrumb-item text-primary"><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
                @endforeach
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="white-box">
                <h3 class="box-title">Total PIPs</h3>
                <ul class="list-inline two-part">
                    <li><i class="mdi mdi-file-document text-info"></i></li>
                    <li class="text-right"><span class="counter">{{ $stats['total'] }}</span></li>
                </ul>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="white-box">
                <h3 class="box-title">Active</h3>
                <ul class="list-inline two-part">
                    <li><i class="mdi mdi-run text-success"></i></li>
                    <li class="text-right"><span class="counter">{{ $stats['active'] }}</span></li>
                </ul>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="white-box">
                <h3 class="box-title">Completed</h3>
                <ul class="list-inline two-part">
                    <li><i class="mdi mdi-check-circle text-primary"></i></li>
                    <li class="text-right"><span class="counter">{{ $stats['completed'] }}</span></li>
                </ul>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="white-box">
                <h3 class="box-title">Pending Ack</h3>
                <ul class="list-inline two-part">
                    <li><i class="mdi mdi-alert-circle text-warning"></i></li>
                    <li class="text-right"><span class="counter">{{ $stats['pending_ack'] }}</span></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 col-md-6">
            <div class="white-box">
                <h3 class="box-title">Successful</h3>
                <ul class="list-inline two-part">
                    <li><i class="mdi mdi-trophy text-success"></i></li>
                    <li class="text-right"><span class="counter">{{ $stats['successful'] }}</span></li>
                </ul>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="white-box">
                <h3 class="box-title">Partial</h3>
                <ul class="list-inline two-part">
                    <li><i class="mdi mdi-trending-neutral text-warning"></i></li>
                    <li class="text-right"><span class="counter">{{ $stats['partial'] }}</span></li>
                </ul>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="white-box">
                <h3 class="box-title">Failure</h3>
                <ul class="list-inline two-part">
                    <li><i class="mdi mdi-alert text-danger"></i></li>
                    <li class="text-right"><span class="counter">{{ $stats['failure'] }}</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
