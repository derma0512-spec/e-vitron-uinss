<meta name="color-scheme" content="light only">
<style id="sipetron-critical-anti-flash">
    /* Kunci warna dasar & tata letak sejak milidetik ke-0 agar 0% jepret putih (FOUC) */
    html,
    body,
    .fi-body {
        color-scheme: light only !important;
        background-color: #F8FAF9 !important;
    }

    aside.fi-sidebar,
    aside.fi-main-sidebar,
    .fi-sidebar-header,
    .fi-sidebar-nav {
        background-color: #0F3D2E !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }

    @media (min-width: 1024px) {
        html,
        body,
        .fi-body,
        .fi-layout {
            background: linear-gradient(to right, #0F3D2E 0, #0F3D2E 15.5rem, #F8FAF9 15.5rem, #F8FAF9 100%) !important;
        }

        .fi-layout {
            display: flex !important;
            flex-direction: row !important;
            width: 100% !important;
            min-height: 100vh !important;
        }

        aside.fi-sidebar,
        aside.fi-main-sidebar {
            position: sticky !important;
            top: 0 !important;
            width: 15.5rem !important;
            min-width: 15.5rem !important;
            max-width: 15.5rem !important;
            height: 100vh !important;
            max-height: 100vh !important;
            transform: none !important;
            flex-shrink: 0 !important;
            display: flex !important;
        }

        .fi-main-ctn {
            flex: 1 1 0% !important;
            min-width: 0 !important;
            width: calc(100% - 15.5rem) !important;
            max-width: calc(100% - 15.5rem) !important;
            margin: 0 !important;
            opacity: 1 !important;
        }
    }
</style>

<link rel="stylesheet" href="{{ asset('css/sipetron-custom.css') }}?v=20261109c">

<style id="sipetron-portrait-responsive-styles">
    /* Dukungan tata letak Admin untuk Tablet & Layar Anjungan Kiosk Portrait (min-width: 768px & tinggi > lebar) tanpa mengubah Laptop Landscape maupun Mobile HP */
    @media (min-width: 1024px) and (orientation: portrait) {
        .fi-topbar-open-sidebar-btn,
        .fi-topbar-close-sidebar-btn {
            display: none !important;
        }
    }

    @media (min-width: 768px) and (orientation: portrait) {
        /* 5 Kartu KPI: 3 kartu atas + 2 kartu bawah agar lebar lega dan teks tidak terpotong */
        body .fi-dashboard-page .sipetron-grid-stats {
            grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
            gap: 0.75rem !important;
        }

        body .fi-dashboard-page .sipetron-grid-stats > .sipetron-stat-card:nth-child(-n+3) {
            grid-column: span 2 / span 2 !important;
        }

        body .fi-dashboard-page .sipetron-grid-stats > .sipetron-stat-card:nth-child(n+4) {
            grid-column: span 3 / span 3 !important;
        }

        /* Tumpuk 2 baris chart & bottom widgets menjadi 1 kolom lebar penuh yang tajam di layar TV Portrait */
        body .fi-dashboard-page .sipetron-grid-charts,
        body .fi-dashboard-page .sipetron-grid-bottom {
            grid-template-columns: minmax(0, 1fr) !important;
            gap: 1.125rem !important;
        }

        /* Rampingkan proporsi kolom tabel Pengajuan & Riwayat agar 100% muat tanpa terpotong di bawah kolom Aksi */
        .sipetron-table {
            min-width: 0 !important;
            width: 100% !important;
        }

        .sipetron-table col.sipetron-col-ticket {
            width: 118px !important;
        }

        .sipetron-table col.sipetron-col-requester {
            width: 112px !important;
        }

        .sipetron-table col.sipetron-col-file {
            width: 100px !important;
        }

        .sipetron-table col.sipetron-col-status {
            width: 114px !important;
        }

        .sipetron-table col.sipetron-col-action {
            width: 72px !important;
        }

        .sipetron-col-ticket {
            padding-left: 0.65rem !important;
            padding-right: 0.45rem !important;
        }

        .sipetron-col-content,
        .sipetron-col-requester {
            padding-left: 0.45rem !important;
            padding-right: 0.45rem !important;
        }

        .sipetron-col-file,
        .sipetron-col-status {
            padding-left: 0.3rem !important;
            padding-right: 0.3rem !important;
        }

        .sipetron-col-action {
            padding-left: 0.3rem !important;
            padding-right: 0.5rem !important;
        }
    }

    /* Kunci scroll halaman belakang saat sidebar laci (< 1024px) sedang dibuka agar fokus & selaras Portal Pemohon */
    @media (max-width: 1023px) {
        html:has(.fi-main-sidebar.fi-sidebar-open),
        body:has(.fi-main-sidebar.fi-sidebar-open) {
            overflow: hidden !important;
            overscroll-behavior: none !important;
        }

        .fi-sidebar-close-overlay {
            overscroll-behavior: contain !important;
            touch-action: none !important;
        }

        aside.fi-main-sidebar.fi-sidebar-open {
            overscroll-behavior: contain !important;
            touch-action: pan-y !important;
        }
    }
</style>

<script type="speculationrules">
{
    "prefetch": [
        {
            "where": {
                "and": [
                    { "href_matches": "/admin*" },
                    { "not": { "href_matches": "/admin/*print*" } },
                    { "not": { "href_matches": "/admin/*export*" } },
                    { "not": { "href_matches": "*logout*" } },
                    { "selector_matches": ".fi-sidebar-nav a.fi-sidebar-item-button" }
                ]
            },
            "eagerness": "moderate"
        }
    ]
}
</script>

<script>
    // 0ms Instant Active Feedback saat menu Sidebar Admin diklik
    document.addEventListener('click', function (e) {
        const navLink = e.target.closest('.fi-sidebar-nav a.fi-sidebar-item-button');
        if (!navLink || e.ctrlKey || e.metaKey || e.shiftKey) return;

        const targetItem = navLink.closest('.fi-sidebar-item');
        if (!targetItem || targetItem.classList.contains('fi-active')) return;

        document.querySelectorAll('.fi-sidebar-nav .fi-sidebar-item.fi-active').forEach(function (item) {
            item.classList.remove('fi-active', 'fi-sidebar-item-active');
        });
        targetItem.classList.add('fi-active', 'fi-sidebar-item-active');
    }, true);
</script>

{{-- Toast Queue Manager & Interaksi Baris Tabel Admin --}}
@include('filament.partials.scripts-toast-and-tables')

{{-- Kalender Videotron, Pintasan Keyboard Modal & Notifikasi Instan Admin --}}
@include('filament.partials.scripts-modals-and-notifications')