<div class="col-md-6" style="margin-bottom: 24px;">
    <div class="card shadow" style="border-radius: 12px; border: 1px solid #e5e7eb;">
        <div class="card-body text-center" style="padding: 20px;">
            <h5 class="card-title" style="font-weight: 700; margin-bottom: 16px;">{{ $chart['title'] }}</h5>
            <canvas id="chart-{{ $chart['slug'] }}" height="220"></canvas>
            <div style="margin-top: 16px;">
                <a href="{{ route('reports.annalytics.explore', [$report, $chart['slug']]) }}?{{ http_build_query(array_filter(['year' => $filters['year'] ?? null, 'leave_type_id' => $filters['leave_type_id'] ?? null])) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fa fa-search"></i> Explore
                </a>
            </div>
        </div>
    </div>
</div>
