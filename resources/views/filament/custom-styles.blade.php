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