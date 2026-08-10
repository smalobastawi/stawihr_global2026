@php
    $indent = max(0, (int) ($level ?? 0)) * 18;
@endphp
<table class="node-table" style="margin-left: {{ $indent }}px;">
    <tr>
        <td class="node-cell">
            <strong>{{ $node['name'] }}</strong><br>
            <span class="muted">{{ $node['designation'] }}</span><br>
            <span class="muted">{{ $node['department'] }}@if(!empty($node['staff_no'])) · {{ $node['staff_no'] }}@endif</span>
        </td>
    </tr>
</table>

@foreach($node['children'] ?? [] as $child)
    @include('admin.annalytics.partials.org-chart-pdf-node', ['node' => $child, 'level' => ($level ?? 0) + 1])
@endforeach
