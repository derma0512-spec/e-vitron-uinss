<style id="evitron-portrait-responsive-styles">
    /* Dukungan tata letak Tablet & Anjungan Kiosk Portrait (min-width: 768px & tinggi > lebar) tanpa mengubah Laptop Landscape maupun Mobile HP */
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

    @media (min-width: 768px) and (max-width: 1023px) and (orientation: portrait) {
        .evitron-nav-subtitle {
            white-space: nowrap !important;
        }

        .evitron-nav-list {
            gap: 1.05rem !important;
        }

        .evitron-nav-list .nav-link {
            font-size: 0.8125rem !important;
            white-space: nowrap !important;
        }

        .evitron-hero-feature-bar {
            width: 505px !important;
            right: 12px !important;
            bottom: 14px !important;
            padding: 10px 22px !important;
        }

        .evitron-hero-feature-icon {
            width: 34px !important;
            height: 34px !important;
        }

        .evitron-hero-feature-text {
            font-size: 11px !important;
            line-height: 1.3 !important;
            white-space: nowrap !important;
        }
    }
</style>
