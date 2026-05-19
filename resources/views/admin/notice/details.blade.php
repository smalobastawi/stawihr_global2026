@extends('admin.master')
@section('content')
@section('title')
    @lang('dashboard.notice_board')
@endsection

<style>
    .notice-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        margin-bottom: 30px;
    }
    .notice-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        position: relative;
    }
    .notice-header::after {
        content: '';
        position: absolute;
        bottom: -20px;
        left: 0;
        right: 0;
        height: 40px;
        background: #fff;
        border-radius: 50% 50% 0 0;
    }
    .notice-title {
        font-size: 1.75rem;
        font-weight: 600;
        margin: 0 0 15px 0;
        line-height: 1.4;
    }
    .notice-meta {
        display: flex;
        align-items: center;
        gap: 20px;
        font-size: 0.9rem;
        opacity: 0.95;
    }
    .notice-meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .notice-meta i {
        font-size: 1rem;
    }
    .notice-body {
        padding: 30px;
    }
    .audience-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f0f4ff;
        color: #4c51bf;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 500;
        margin-bottom: 25px;
    }
    .audience-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
    }
    .audience-tag {
        background: #e0e7ff;
        color: #4338ca;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }
    .notice-content {
        font-size: 1rem;
        line-height: 1.8;
        color: #374151;
        margin-top: 25px;
    }
    .notice-content p {
        margin-bottom: 1rem;
    }
    .attachment-section {
        margin-top: 30px;
        padding-top: 30px;
        border-top: 1px solid #e5e7eb;
    }
    .attachment-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .attachment-preview {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    .attachment-preview img {
        width: 100%;
        height: auto;
        display: block;
    }
    .attachment-preview embed {
        width: 100%;
        height: 600px;
        border: none;
    }
    .action-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 30px;
        background: #f9fafb;
        border-top: 1px solid #e5e7eb;
    }
    .btn-modern {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        text-decoration: none;
    }
    .btn-modern:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    .btn-primary-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .btn-primary-modern:hover {
        color: white;
    }
    .btn-secondary-modern {
        background: #fff;
        color: #4b5563;
        border: 1px solid #d1d5db;
    }
    .btn-secondary-modern:hover {
        background: #f3f4f6;
        color: #374151;
    }
    .btn-success-modern {
        background: #10b981;
        color: white;
    }
    .btn-success-modern:hover {
        color: white;
    }
    .breadcrumb-modern {
        background: transparent;
        padding: 0;
        margin-bottom: 20px;
    }
    .breadcrumb-modern a {
        color: #6b7280;
        text-decoration: none;
    }
    .breadcrumb-modern a:hover {
        color: #4f46e5;
    }
    .breadcrumb-modern .active {
        color: #1f2937;
        font-weight: 500;
    }
</style>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="breadcrumb-modern">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
            <li class="breadcrumb-item"><a href="{{ route('notice.index') }}">@lang('dashboard.notice_board')</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $editModeData->title }}</li>
        </ol>
    </nav>

    <!-- Action Buttons Top -->
    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('notice.index') }}" class="btn-modern btn-secondary-modern">
            <i class="fa fa-arrow-left"></i> Back to Notices
        </a>
    </div>

    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
            <div class="notice-card">
                <!-- Header -->
                <div class="notice-header">
                    <h1 class="notice-title">{{ $editModeData->title }}</h1>
                    <div class="notice-meta">
                        <div class="notice-meta-item">
                            <i class="fa fa-calendar"></i>
                            <span>{{ date('d M Y', strtotime($editModeData->publish_date)) }}</span>
                        </div>
                        <div class="notice-meta-item">
                            <i class="fa fa-user"></i>
                            <span>By: {{ $editModeData->createdBy?->employeeDetails?->fullName() ?? $editModeData->createdBy?->user_name ?? 'Unknown' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Body -->
                <div class="notice-body">
                    <!-- Targeted Audience -->
                    <div class="audience-badge">
                        <i class="fa fa-users"></i>
                        <span>Targeted Audience</span>
                    </div>
                    <div class="audience-list">
                        @foreach ($editModeData->targeted_audience as $audienceItem)
                            <span class="audience-tag">{{ $audienceItem }}</span>
                        @endforeach
                    </div>

                    <!-- Content -->
                    <div class="notice-content">
                        {!! $editModeData->description !!}
                    </div>

                    <!-- Attachment -->
                    @if($editModeData->attach_file != '')
                        @php
                            $info = new SplFileInfo($editModeData->attach_file);
                            $extension = strtolower($info->getExtension());
                            $isImage = in_array($extension, ['png', 'jpg', 'jpeg', 'gif', 'webp']);
                        @endphp

                        <div class="attachment-section">
                            <div class="attachment-title">
                                <i class="fa fa-paperclip"></i>
                                Attachment
                                <a href="{{ asset('uploads/notice/' . $editModeData->attach_file) }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-outline-primary pull-right">
                                    <i class="fa fa-download"></i> Download
                                </a>
                            </div>
                            <div class="attachment-preview">
                                @if($isImage)
                                    <img src="{{ asset('uploads/notice/' . $editModeData->attach_file) }}" 
                                         alt="Notice Attachment">
                                @else
                                    <embed src="{{ asset('uploads/notice/' . $editModeData->attach_file) }}" 
                                           type="application/pdf">
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Action Bar -->
                <div class="action-bar">
                    <div>
                        <a href="{{ route('notice.edit', $editModeData->notice_id) }}" class="btn-modern btn-primary-modern">
                            <i class="fa fa-pencil"></i> Edit Notice
                        </a>
                    </div>
                    <div>
                        <a href="{{ route('notice.create') }}" class="btn-modern btn-success-modern">
                            <i class="fa fa-plus"></i> Create New Notice
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
