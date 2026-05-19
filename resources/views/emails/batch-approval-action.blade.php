<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Batch {{ $actionText }} - {{ $count }} {{ $modelType }} Items</title>
</head>
<body>
    <h1>Batch {{ $actionText }} - {{ $count }} {{ $modelType }} Items</h1>
    
    <p>Hello {{ $notifiable->name }},</p>
    
    <p>A batch of {{ $count }} {{ $modelType }} items has been {{ $action }}.</p>
    
    <p><strong>Batch Details:</strong></p>
    <ul>
        <li>Batch ID: {{ $batchId }}</li>
        <li>{{ $actionText }} by: {{ $actionedBy }}</li>
        <li>Total Items: {{ $count }}</li>
    </ul>
    
    @if(!empty($comments))
    <p><strong>Comments:</strong> {{ $comments }}</p>
    @endif
    
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
            View Details
        </a>
    </p>
    
    <p>Your submissions have been processed in batch.</p>
    
    <p>You can view the detailed status of each item in the approval system.</p>
    
    <p>Thanks,<br>
    {{ config('app.name') }}</p>
</body>
</html>