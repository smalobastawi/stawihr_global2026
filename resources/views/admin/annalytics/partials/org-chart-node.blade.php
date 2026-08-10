<li>
    <div class="org-node">
        <div class="org-node-name">{{ $node['name'] }}</div>
        <div class="org-node-role">{{ $node['designation'] }}</div>
        <div class="org-node-meta">
            {{ $node['department'] }}
            @if(!empty($node['staff_no']))
                · {{ $node['staff_no'] }}
            @endif
        </div>
        @if(($node['direct_reports'] ?? 0) > 0)
            <div class="org-node-reports">{{ $node['direct_reports'] }} direct report{{ $node['direct_reports'] === 1 ? '' : 's' }}</div>
        @endif
    </div>

    @if(!empty($node['children']))
        <ul>
            @foreach($node['children'] as $child)
                @include('admin.annalytics.partials.org-chart-node', ['node' => $child])
            @endforeach
        </ul>
    @endif
</li>
