@extends('layouts.app')

@section('title', 'Subscription Issue - ' . config('app.name'))

@section('content')
<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0">
                {{-- Header with appropriate color based on status --}}
                <div class="card-header text-white text-center py-4
                    @if(in_array($status, ['expired', 'trial_expired', 'suspended']))
                        bg-danger
                    @elseif($status === 'in_arrears')
                        bg-warning text-dark
                    @else
                        bg-info
                    @endif
                ">
                    <div class="mb-3">
                        <i class="fas fa-{{ $status === 'suspended' ? 'ban' : ($status === 'expired' ? 'calendar-times' : 'exclamation-circle') }}"
                           style="font-size: 3rem;"></i>
                    </div>
                    <h4 class="mb-0">
                        @switch($status)
                            @case('expired')
                                Subscription Expired
                                @break
                            @case('trial_expired')
                                Trial Period Ended
                                @break
                            @case('suspended')
                                Account Suspended
                                @break
                            @case('in_arrears')
                                Payment Required
                                @break
                            @case('no_subscription')
                                No Active Subscription
                                @break
                            @default
                                Subscription Issue
                        @endswitch
                    </h4>
                </div>

                <div class="card-body p-5">
                    {{-- Status Message --}}
                    <div class="text-center mb-4">
                        <p class="lead">{{ $message }}</p>
                    </div>

                    {{-- Subscription Details (if available) --}}
                    @if($subscription)
                        <div class="bg-light p-3 rounded mb-4">
                            <h6 class="text-muted mb-3">Subscription Details</h6>
                            <table class="table table-sm table-borderless mb-0">
                                @if($subscription['package_name'])
                                    <tr>
                                        <td class="text-muted">Package:</td>
                                        <td class="text-end fw-bold">{{ $subscription['package_name'] }}</td>
                                    </tr>
                                @endif
                                @if($subscription['start_date'])
                                    <tr>
                                        <td class="text-muted">Start Date:</td>
                                        <td class="text-end">{{ \Carbon\Carbon::parse($subscription['start_date'])->format('M d, Y') }}</td>
                                    </tr>
                                @endif
                                @if($subscription['end_date'])
                                    <tr>
                                        <td class="text-muted">Expiry Date:</td>
                                        <td class="text-end {{ $status === 'expired' ? 'text-danger fw-bold' : '' }}">
                                            {{ \Carbon\Carbon::parse($subscription['end_date'])->format('M d, Y') }}
                                        </td>
                                    </tr>
                                @endif
                                @if(isset($subscription['days_remaining']) && $subscription['days_remaining'] <= 0)
                                    <tr>
                                        <td class="text-muted">Days Overdue:</td>
                                        <td class="text-end text-danger fw-bold">
                                            {{ abs($subscription['days_remaining']) }}
                                        </td>
                                    </tr>
                                @endif
                                @if($subscription['amount'])
                                    <tr>
                                        <td class="text-muted">Amount:</td>
                                        <td class="text-end">{{ number_format($subscription['amount'], 2) }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    @endif

                    {{-- User Info --}}
                    @if($user)
                        <div class="text-center text-muted small mb-4">
                            <p class="mb-1">
                                <strong>Account:</strong> {{ $user['email'] ?? 'N/A' }}
                            </p>
                            @if($user['domain'])
                                <p class="mb-0">{{ $user['domain'] }}</p>
                            @endif
                        </div>
                    @endif

                    {{-- Action Buttons --}}
                    <div class="d-grid gap-2">
                        @foreach($actions as $action)
                            @php
                                $btnClass = match($action['style']) {
                                    'primary' => 'btn-primary',
                                    'secondary' => 'btn-secondary',
                                    'outline-secondary' => 'btn-outline-secondary',
                                    default => 'btn-primary',
                                };
                                $isExternal = !str_contains($action['url'], request()->getHost());
                            @endphp

                            <a href="{{ $action['url'] }}"
                               class="btn {{ $btnClass }} btn-lg d-flex align-items-center justify-content-center gap-2"
                               @if($isExternal) target="_blank" rel="noopener noreferrer" @endif>
                                <i class="fas fa-{{ $action['icon'] }}"></i>
                                {{ $action['label'] }}
                                @if($isExternal)
                                    <i class="fas fa-external-link-alt small"></i>
                                @endif
                            </a>
                        @endforeach
                    </div>

                    {{-- Help Text --}}
                    <div class="text-center mt-4">
                        <p class="text-muted small mb-0">
                            Need help? Contact our support team at
                            <a href="mailto:support@stawihr.com">support@stawihr.com</a>
                            or call <a href="tel:+254701304585">+254 701 304 585</a>
                        </p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="card-footer text-center py-3 bg-light">
                    <small class="text-muted">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        border-radius: 1rem;
        overflow: hidden;
    }
    .card-header {
        border-radius: 0 !important;
    }
    .min-vh-100 {
        min-height: 100vh;
    }
</style>
@endpush
