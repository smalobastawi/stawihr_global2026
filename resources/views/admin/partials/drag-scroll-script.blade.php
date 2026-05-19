<script>
    $(document).ready(function() {
        function enableDragScrolling() {
            const container = document.getElementById('scrollable-table-container');
            const table = document.getElementById('myTable');
            const leftIndicator = document.getElementById('scroll-indicator-left');
            const rightIndicator = document.getElementById('scroll-indicator-right');
            
            // Check if elements exist before proceeding
            if (!container || !table || !leftIndicator || !rightIndicator) {
                console.error('Required elements for drag scrolling not found');
                return;
            }

            let isDown = false;
            let startX;
            let scrollLeft;

            // Check if table is wider than container
            function checkScrollIndicators() {
                if (table.scrollWidth > container.clientWidth) {
                    // Show/hide scroll indicators based on scroll position
                    if (container.scrollLeft > 0) {
                        leftIndicator.style.display = 'block';
                    } else {
                        leftIndicator.style.display = 'none';
                    }

                    if (container.scrollLeft < (table.scrollWidth - container.clientWidth)) {
                        rightIndicator.style.display = 'block';
                    } else {
                        rightIndicator.style.display = 'none';
                    }
                } else {
                    leftIndicator.style.display = 'none';
                    rightIndicator.style.display = 'none';
                }
            }

            // Initial check
            checkScrollIndicators();

            // Mouse events for drag scrolling
            container.addEventListener('mousedown', (e) => {
                isDown = true;
                container.style.cursor = 'grabbing';
                startX = e.pageX - container.offsetLeft;
                scrollLeft = container.scrollLeft;
            });

            container.addEventListener('mouseleave', () => {
                isDown = false;
                container.style.cursor = 'grab';
            });

            container.addEventListener('mouseup', () => {
                isDown = false;
                container.style.cursor = 'grab';
            });

            container.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - container.offsetLeft;
                const walk = (x - startX) * 2; // Scroll speed multiplier
                container.scrollLeft = scrollLeft - walk;
                checkScrollIndicators();
            });

            // Touch events for mobile devices
            container.addEventListener('touchstart', (e) => {
                isDown = true;
                startX = e.touches[0].pageX - container.offsetLeft;
                scrollLeft = container.scrollLeft;
            }, { passive: true });

            container.addEventListener('touchend', () => {
                isDown = false;
            });

            container.addEventListener('touchmove', (e) => {
                if (!isDown) return;
                const x = e.touches[0].pageX - container.offsetLeft;
                const walk = (x - startX) * 2;
                container.scrollLeft = scrollLeft - walk;
                checkScrollIndicators();
            }, { passive: true });

            // Update indicators on scroll
            container.addEventListener('scroll', checkScrollIndicators);

            // Update indicators on window resize
            window.addEventListener('resize', checkScrollIndicators);

            // Set initial cursor style
            if (table.scrollWidth > container.clientWidth) {
                container.style.cursor = 'grab';
            }
        }

        // Initialize drag scrolling after a short delay to ensure DOM is fully loaded
        setTimeout(function() {
            enableDragScrolling();
        }, 100);
    });
</script>