@if($subscriptionBanner['show'] ?? false)
    <div class="subscription-banner subscription-banner--{{ $subscriptionBanner['type'] }} {{ $subscriptionBanner['dismissible'] ? 'subscription-banner--dismissible' : '' }}"
         role="alert"
         id="subscription-banner">

        <div class="subscription-banner__content">
            <div class="subscription-banner__icon">
                <i class="fas fa-{{ $subscriptionBanner['icon'] ?? 'info-circle' }}"></i>
            </div>

            <div class="subscription-banner__text">
                @if($subscriptionBanner['title'])
                    <strong class="subscription-banner__title">{{ $subscriptionBanner['title'] }}</strong>
                @endif
                <span class="subscription-banner__message">{{ $subscriptionBanner['message'] }}</span>
            </div>

            <div class="subscription-banner__actions">
                @if($subscriptionBanner['action_url'])
                    <a href="{{ $subscriptionBanner['action_url'] }}"
                       class="btn btn-{{ $subscriptionBanner['type'] === 'danger' ? 'danger' : ($subscriptionBanner['type'] === 'warning' ? 'warning' : 'primary') }} btn-sm"
                       target="{{ str_contains($subscriptionBanner['action_url'], 'stawihr.com') ? '_self' : '_blank' }}">
                        {{ $subscriptionBanner['action_text'] }}
                    </a>
                @endif

                @if($subscriptionBanner['secondary_action_url'])
                    <a href="{{ $subscriptionBanner['secondary_action_url'] }}"
                       class="btn btn-outline-secondary btn-sm"
                       target="_blank">
                        {{ $subscriptionBanner['secondary_action_text'] }}
                    </a>
                @endif

                @if($subscriptionBanner['dismissible'])
                    <button type="button"
                            class="subscription-banner__close"
                            onclick="dismissSubscriptionBanner()"
                            aria-label="Dismiss">
                        <i class="fas fa-times"></i>
                    </button>
                @endif
            </div>
        </div>
    </div>

    @once
        @push('styles')
            <style>
                .subscription-banner {
                    padding: 1rem;
                    border-left: 4px solid;
                    margin-bottom: 1rem;
                }

                .subscription-banner--danger {
                    background-color: #f8d7da;
                    border-color: #dc3545;
                    color: #721c24;
                }

                .subscription-banner--warning {
                    background-color: #fff3cd;
                    border-color: #ffc107;
                    color: #856404;
                }

                .subscription-banner--info {
                    background-color: #d1ecf1;
                    border-color: #17a2b8;
                    color: #0c5460;
                }

                .subscription-banner--secondary {
                    background-color: #e2e3e5;
                    border-color: #6c757d;
                    color: #383d41;
                }

                .subscription-banner__content {
                    display: flex;
                    align-items: center;
                    gap: 1rem;
                    flex-wrap: wrap;
                }

                .subscription-banner__icon {
                    font-size: 1.5rem;
                    flex-shrink: 0;
                }

                .subscription-banner__text {
                    flex: 1;
                    min-width: 200px;
                }

                .subscription-banner__title {
                    display: block;
                    margin-bottom: 0.25rem;
                }

                .subscription-banner__actions {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    flex-shrink: 0;
                }

                .subscription-banner--dismissible .subscription-banner__actions {
                    padding-right: 2rem;
                    position: relative;
                }

                .subscription-banner__close {
                    position: absolute;
                    right: 0;
                    top: 50%;
                    transform: translateY(-50%);
                    background: none;
                    border: none;
                    font-size: 1.25rem;
                    cursor: pointer;
                    opacity: 0.5;
                    transition: opacity 0.2s;
                }

                .subscription-banner__close:hover {
                    opacity: 1;
                }

                /* Sticky banner at top */
                .subscription-banner--sticky {
                    position: sticky;
                    top: 0;
                    z-index: 1030;
                    margin-bottom: 0;
                    border-left: none;
                    border-bottom: 2px solid;
                }

                @media (max-width: 768px) {
                    .subscription-banner__content {
                        flex-direction: column;
                        align-items: flex-start;
                    }

                    .subscription-banner__actions {
                        width: 100%;
                        justify-content: flex-start;
                    }
                }
            </style>
        @endpush

        @push('scripts')
            <script>
                function dismissSubscriptionBanner() {
                    const banner = document.getElementById('subscription-banner');
                    if (banner) {
                        banner.style.display = 'none';

                        // Optional: Remember dismissal in session storage
                        sessionStorage.setItem('subscriptionBannerDismissed', Date.now());
                    }
                }

                // Check if banner should be shown (if not dismissed in this session)
                document.addEventListener('DOMContentLoaded', function() {
                    const banner = document.getElementById('subscription-banner');
                    if (banner && banner.classList.contains('subscription-banner--dismissible')) {
                        const dismissed = sessionStorage.getItem('subscriptionBannerDismissed');
                        if (dismissed) {
                            // Show again after 1 hour
                            const oneHour = 60 * 60 * 1000;
                            if (Date.now() - parseInt(dismissed) < oneHour) {
                                banner.style.display = 'none';
                            }
                        }
                    }
                });
            </script>
        @endpush
    @endonce
@endif
