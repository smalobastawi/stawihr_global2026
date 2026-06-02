@php
    $companyLogo = companyLogoUrl();
    $companyName = companyDisplayName();
@endphp

@if($companyLogo || $companyName)
    <div class="company-report-header" style="margin-bottom: 20px; display: flex; align-items: center; gap: 15px;">
        @if($companyLogo)
            <img src="{{ $companyLogo }}" alt="{{ $companyName }}"
                style="height: 60px; max-width: 180px; object-fit: contain;">
        @endif
        <div>
            <h4 style="margin: 0;">{{ $companyName }}</h4>
            @if(companyDisplayAddress())
                <p style="margin: 5px 0 0;">{{ companyDisplayAddress() }}</p>
            @endif
            @if(companyDisplayPhone() || companyDisplayEmail())
                <p style="margin: 5px 0 0;">
                    @if(companyDisplayPhone()) Tel: {{ companyDisplayPhone() }} @endif
                    @if(companyDisplayPhone() && companyDisplayEmail()) | @endif
                    @if(companyDisplayEmail()) Email: {{ companyDisplayEmail() }} @endif
                </p>
            @endif
        </div>
    </div>
@endif
