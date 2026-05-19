<!DOCTYPE html>
<html>

<head>
    <title>New Notice: {{ $notice->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
            margin-bottom: 20px;
        }

        h1 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .content {
            padding: 15px 0;
        }

        .details {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }

        .detail-item {
            margin-bottom: 8px;
        }

        .detail-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }

        .buttons {
            margin: 25px 0;
            text-align: center;
        }

        .button {
            display: inline-block;
            padding: 12px 25px;
            margin: 0 10px 10px 0;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            color: white !important;
            text-align: center;
            background-color: #3490dc;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 14px;
            color: #777;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
            background-color: #e3f2fd;
            color: #1976d2;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>New Notice: {{ $notice->title }}</h1>
    </div>

    <div class="content">
        <p>Dear User,</p>

        <p>A new notice has been published. Please find the details below:</p>

        <div class="details">
            <div class="detail-item">
                <span class="detail-label">Title:</span> {{ $notice->title }}
            </div>
            <div class="detail-item">
                <span class="detail-label">Status:</span> <span class="status-badge">{{ $notice->status }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Publish Date:</span>
                {{ \Carbon\Carbon::parse($notice->publish_date)->format('d M Y') }}
            </div>
            <div class="detail-item">
                <span class="detail-label">Description:</span>
            </div>
            <div style="margin-top: 10px;">
                {!! $notice->description !!}
            </div>
            @if ($notice->attach_file)
                <div class="detail-item">
                    <span class="detail-label">Attachment:</span> Available
                </div>
            @endif
        </div>

        <p>Please log in to the system to view the full details and any attachments.</p>

        <div class="buttons">
            <a href="{{ route('notice.show', $notice->notice_id) }}" class="button">View Notice</a>
        </div>
    </div>

    <div class="footer">
        <p>
            Kind regards,<br>
            {{ $notice->createdBy->full_name ?? config('app.name') }} Team
        </p>
    </div>
</body>

</html>
