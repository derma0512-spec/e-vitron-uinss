@if(config('services.demo.quick_login'))
<style id="demo-kiosk-portrait-layout" data-testid="demo-kiosk-viewport">
    /* Khusus Mode Demo Pameran (Railway): tata letak 1 kolom vertikal proporsional saat dibuka di TV Standing Kiosk (Portrait) tanpa zoom-out */
    @media (min-width: 768px) and (orientation: portrait) {
        .evitron-auth-main {
            max-width: 760px !important;
            padding-top: 2.5rem !important;
            padding-bottom: 2.5rem !important;
        }

        .evitron-auth-grid {
            grid-template-columns: minmax(0, 1fr) !important;
            gap: 1.75rem !important;
        }

        .evitron-auth-hero,
        .evitron-auth-card-col {
            grid-column: span 1 / span 1 !important;
        }

        .evitron-auth-hero-img {
            max-height: 310px !important;
        }

        .evitron-auth-card-col {
            justify-content: center !important;
        }

        .evitron-auth-card {
            max-width: 100% !important;
            width: 100% !important;
            padding: 2rem 2.25rem !important;
            transform: none !important;
        }
    }
</style>
@endif
