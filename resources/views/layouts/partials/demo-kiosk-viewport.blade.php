@if(config('services.demo.quick_login'))
<script data-testid="demo-kiosk-viewport">
    (function () {
        var TARGET_DESKTOP_WIDTH = 1280;
        var MIN_KIOSK_WIDTH = 600;
        var resizeTimer = null;
        var ignoreResizeUntil = 0;

        function syncKioskViewport() {
            ignoreResizeUntil = Date.now() + 180;
            var meta = document.querySelector('meta[name="viewport"]');
            document.documentElement.style.zoom = '1';
            document.documentElement.style.minWidth = '';

            if (meta && meta.getAttribute('data-kiosk-scaled') === '1') {
                meta.setAttribute('content', 'width=device-width, initial-scale=1.0');
                meta.removeAttribute('data-kiosk-scaled');
            }

            var trueWidth = window.innerWidth || document.documentElement.clientWidth || TARGET_DESKTOP_WIDTH;

            // 1. Layar HP (< 600px): Gunakan tampilan Mobile Responsive asli
            if (trueWidth < MIN_KIOSK_WIDTH) {
                document.documentElement.style.zoom = '';
                return;
            }

            // 2. Layar Laptop / Monitor Landscape (>= 1280px): Gunakan tampilan Desktop 1:1 normal
            if (trueWidth >= TARGET_DESKTOP_WIDTH) {
                document.documentElement.style.zoom = '';
                return;
            }

            // 3. Layar Standing Touch Kiosk / Portrait Monitor (600px - 1279px):
            //    Skalakan proporsional ke kanvas Desktop 1280px
            if (meta) {
                meta.setAttribute('content', 'width=' + TARGET_DESKTOP_WIDTH);
                meta.setAttribute('data-kiosk-scaled', '1');
            }

            var afterMetaWidth = window.innerWidth || document.documentElement.clientWidth || trueWidth;
            if (afterMetaWidth < TARGET_DESKTOP_WIDTH) {
                var scale = trueWidth / TARGET_DESKTOP_WIDTH;
                document.documentElement.style.minWidth = TARGET_DESKTOP_WIDTH + 'px';
                document.documentElement.style.zoom = String(scale);
            } else {
                document.documentElement.style.zoom = '';
            }
        }

        syncKioskViewport();
        window.addEventListener('DOMContentLoaded', syncKioskViewport);
        window.addEventListener('resize', function () {
            if (Date.now() < ignoreResizeUntil) {
                return;
            }
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(syncKioskViewport, 90);
        });
    })();
</script>
@endif
