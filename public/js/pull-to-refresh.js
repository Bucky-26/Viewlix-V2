  (function() {
            // Only enable on touch devices
            if (!('ontouchstart' in window)) return;

            let startY = 0;
            let pulling = false;
            let canRefresh = false;
            const threshold = 80; // px to trigger refresh
            const maxPull = 150; // max translate for indicator
            let indicator = null;

            function createIndicator() {
                if (indicator) return;
                indicator = document.createElement('div');
                indicator.id = 'ptr-indicator';
                indicator.style.position = 'fixed';
                indicator.style.left = '50%';
                indicator.style.transform = 'translate(-50%, -100%)';
                indicator.style.top = '0';
                indicator.style.zIndex = '9999';
                indicator.style.padding = '8px 12px';
                indicator.style.borderRadius = '0 0 8px 8px';
                indicator.style.background = 'rgba(17,24,39,0.95)';
                indicator.style.color = '#e6eef8';
                indicator.style.fontSize = '13px';
                indicator.style.fontFamily = 'system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial';
                indicator.style.boxShadow = '0 4px 12px rgba(0,0,0,0.2)';
                indicator.style.transition = 'transform .2s ease, opacity .2s ease';
                indicator.style.opacity = '0';
                indicator.textContent = 'Pull to refresh';
                document.body.appendChild(indicator);
            }

            function removeIndicator() {
                if (!indicator) return;
                try { indicator.remove(); } catch (e) {}
                indicator = null;
            }

            window.addEventListener('touchstart', function(e) {
                // Only start when at the top of the page
                const scrollTop = document.scrollingElement ? document.scrollingElement.scrollTop : (document.documentElement.scrollTop || document.body.scrollTop);
                if (scrollTop > 0) return;
                if (e.touches && e.touches.length === 1) {
                    startY = e.touches[0].clientY;
                    pulling = true;
                    canRefresh = false;
                    createIndicator();
                    indicator.style.transition = '';
                    indicator.style.opacity = '1';
                    indicator.textContent = 'Pull to refresh';
                }
            }, { passive: true });

            window.addEventListener('touchmove', function(e) {
                if (!pulling || !indicator) return;
                if (!e.touches || e.touches.length !== 1) return;
                const currentY = e.touches[0].clientY;
                const delta = currentY - startY;
                if (delta > 0) {
                    // Only prevent native overscroll when we have a visible pull
                    if (delta > 10) e.preventDefault();
                    const translate = Math.min(delta, maxPull);
                    indicator.style.transform = `translate(-50%, ${translate}px)`;
                    if (delta > threshold) {
                        canRefresh = true;
                        indicator.textContent = 'Release to refresh';
                    } else {
                        canRefresh = false;
                        indicator.textContent = 'Pull to refresh';
                    }
                }
            }, { passive: false });

            window.addEventListener('touchend', function() {
                if (!pulling) return;
                pulling = false;
                if (indicator) {
                    indicator.style.transition = 'transform .3s ease, opacity .3s ease';
                    indicator.style.transform = 'translate(-50%, -100%)';
                    indicator.style.opacity = '0';
                }
                if (canRefresh) {
                    // tiny delay so animation can start
                    setTimeout(function() {
                        // Use location.reload to refresh the page; keep cache behavior default
                        location.reload();
                    }, 150);
                }
                // cleanup after animation
                setTimeout(removeIndicator, 500);
            });

            // If user navigates away / SPA transitions, ensure indicator is removed
            window.addEventListener('pagehide', removeIndicator);
        })();
    