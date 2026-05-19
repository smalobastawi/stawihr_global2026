<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Batch Approval Required - {{ $count }} {{ $modelType }} Items</title>
</head>
<body>
    <h1>Batch Approval Required - {{ $count }} {{ $modelType }} Items</h1>
    
    <p>Hello {{ $notifiable->name }},</p>
    
    <p>A batch of {{ $count }} {{ $modelType }} items has been submitted for your approval.</p>
    
    <p><strong>Batch Details:</strong></p>
    <ul>
        <li>Batch ID: {{ $batchId }}</li>
        <li>Submitted by: {{ $submitter }}</li>
        <li>Current Step: {{ $currentStep }}</li>
        <li>Total Items: {{ $count }}</li>
    </ul>
    
    <p><strong>Items in this batch:</strong></p>
    <ul>
        @foreach($items as $item)
        <li>{{ $item['title'] }}</li>
        @endforeach
    </ul>
    
    @if($additionalItemsCount > 0)
    <p>... and {{ $additionalItemsCount }} more items</p>
    @endif
    
    <p>
        <a href="{{ $actionUrl }}" style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">
            Review Batch Approval
        </a>
    </p>
    
    <p>Please review and take appropriate action on these items.</p>
    
    <p>You can approve or reject individual items or use batch operations for efficiency.</p>
    
    <p>Thanks,<br>
    {{ config('app.name') }}</p>
</body>
</html>