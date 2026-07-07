@php
    $tooltip = $tooltip ?? '';
@endphp
@if ($tooltip)
    <i class="fa fa-info-circle text-muted" style="cursor: help; margin-left: 4px;"
        title="{{ $tooltip }}" aria-label="{{ $tooltip }}"></i>
@endif
