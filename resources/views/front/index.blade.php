@php
    $front_setting = getFrontData();
    $heroImage = url('front-assets/images/cover.png');
    $companyName = $front_setting?->company_title ?: env('APP_NAME', 'StawiHR');
@endphp
@extends('front.master')

@section('title')
    {{ $companyName }}
@endsection

@section('meta')
    @if($front_setting?->logo)
        <meta property="og:image" content="{{ asset('storage/uploads/front/' . $front_setting->logo) }}" />
    @endif
    <meta property="og:title" content="{{ $companyName }}" />
    <meta property="og:description" content="Secure, reliable, friendly." />
    <meta name="description" content="Secure, reliable, friendly." />
    <meta property="og:url" content="{{ $front_setting?->og_url ?: url('/') }}" />
@endsection

@section('content')
    <section class="landing-hero" style="background-image: url('{{ $heroImage }}');">
        <div class="landing-hero-overlay"></div>
        <div class="landing-hero-inner">
            <p class="landing-tagline">StawiHR-Secure, reliable, friendly.</p>
        </div>
    </section>
@endsection
