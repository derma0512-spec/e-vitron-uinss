{{-- Bagian 2: Stylesheet Tabel Riwayat, Drawer Detail & Pagination (Dipanggil oleh history-and-recap.blade.php) --}}
    <style>

        /* Custom Clean Year Select */
        .sipetron-year-select {
            height: 32px;
            width: 100%;
            padding: 0 26px 0 10px;
            background-color: #FFFFFF;
            border: 1px solid #CBD5E1;
            border-radius: 7px;
            font-size: 12.5px;
            font-weight: 600;
            color: #0F172A;
            outline: none;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748B'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 13px 13px;
            transition: all 140ms ease;
            box-sizing: border-box;
        }

        .sipetron-year-select:hover {
            border-color: #94A3B8;
            background-color: #F8FAFC;
        }

        .sipetron-year-select:focus {
            border-color: #0F3D2E !important;
            box-shadow: 0 0 0 2px rgba(15, 61, 46, 0.12) !important;
            background-color: #FFFFFF;
        }

        /* Month Grid */
        .sipetron-month-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 4px;
            margin-bottom: 8px;
        }

        .sipetron-month-pill {
            height: 28px;
            border-radius: 6px;
            font-size: 11.5px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 120ms ease;
            border: 1px solid #E2E8F0;
            background: #FFFFFF;
            color: #334155;
            padding: 0;
            outline: none;
        }

        .sipetron-month-pill:hover:not(.is-active-month-pill) {
            background: #F1F5F9;
            color: #0F172A;
            border-color: #CBD5E1;
        }

        .sipetron-month-pill.is-active-month-pill {
            background: #0F3D2E !important;
            color: #FFFFFF !important;
            border-color: #0F3D2E !important;
            font-weight: 700;
        }

        .sipetron-month-pill-all {
            height: 22px;
            padding: 0 8px;
            border-radius: 9999px;
            font-size: 10.5px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 120ms ease;
            border: 1px solid #E2E8F0;
            background: #FFFFFF;
            color: #64748B;
            outline: none;
        }

        .sipetron-month-pill-all:hover:not(.is-active-month-pill) {
            background: #F1F5F9;
            color: #1E293B;
            border-color: #CBD5E1;
        }

        .sipetron-month-pill-all.is-active-month-pill {
            background: #0F3D2E !important;
            color: #FFFFFF !important;
            border-color: #0F3D2E !important;
            font-weight: 700;
        }

        /* Footer Aksi Dropdown Periode (Batal & Terapkan) */
        .sipetron-period-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            padding-top: 10px;
            border-top: 1px solid #F1F5F9;
            margin-top: 4px;
        }

        .sipetron-period-btn-cancel {
            height: 34px;
            padding: 0 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #64748B;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            cursor: pointer;
            transition: all 120ms ease;
        }

        .sipetron-period-btn-cancel:hover {
            background: #E2E8F0;
            color: #1E293B;
        }

        .sipetron-period-btn-apply {
            height: 34px;
            padding: 0 16px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            color: #FFFFFF;
            background: #0F3D2E;
            border: 1px solid #0F3D2E;
            cursor: pointer;
            transition: all 120ms ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .sipetron-period-btn-apply:hover {
            background: #185340;
            border-color: #185340;
        }

        .sipetron-period-btn-apply:active {
            transform: scale(0.97);
        }

        /* Slide-over Drawer Overlay (Sama Persis dengan Halaman Pengajuan) */
        .sipetron-drawer-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.60);
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            filter: none !important;
            z-index: 9990;
            touch-action: none;
            overscroll-behavior: contain;
            cursor: pointer;
        }
        .sipetron-drawer-panel {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            max-width: 580px;
            height: 100vh;
            background: #FFFFFF;
            z-index: 9991;
            box-shadow: -10px 0 40px rgba(15, 61, 46, 0.16), -2px 0 10px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
            overscroll-behavior: contain;
        }
        @media (prefers-reduced-motion: reduce) {
            .sipetron-drawer-backdrop,
            .sipetron-drawer-panel {
                transition: none !important;
                transform: none !important;
                animation: none !important;
            }
        }
        .sipetron-drawer-header {
            padding: 1.15rem 1.5rem;
            border-bottom: 1px solid #E2E8F0;
            background: #FFFFFF;
            flex-shrink: 0;
            overscroll-behavior: contain;
        }
        .sipetron-drawer-body {
            flex: 1 1 0%;
            min-height: 0;
            overflow-y: auto;
            overscroll-behavior: contain;
            -webkit-overflow-scrolling: touch;
            padding: 1.25rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            box-sizing: border-box;
            scrollbar-width: thin;
            scrollbar-color: #CBD5E1 transparent;
        }
        .sipetron-drawer-body::-webkit-scrollbar {
            width: 6px;
        }
        .sipetron-drawer-body::-webkit-scrollbar-track {
            background: transparent;
        }
        .sipetron-drawer-body::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 9999px;
        }
        .sipetron-drawer-body::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }
        .sipetron-drawer-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #E2E8F0;
            background: #F8FAF9;
            flex-shrink: 0;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.03);
            overscroll-behavior: contain;
        }
        .sipetron-info-card {
            background: #F8FAF9;
            border: 1px solid #E2E8F0;
            border-radius: 0.75rem;
            padding: 0.875rem 1rem;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .sipetron-info-card:hover {
            border-color: #CBD5E1;
            box-shadow: 0 2px 8px -1px rgba(0, 0, 0, 0.04);
        }
        .sipetron-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #E2E8F0;
        }
        .sipetron-card-title {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.8125rem;
            font-weight: 700;
            color: #0F3D2E;
            letter-spacing: 0.01em;
        }
        .sipetron-wa-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.45rem 0.85rem;
            border-radius: 8px;
            background: #E8F8EE;
            border: 1px solid #A7E8BA;
            color: #128C5A;
            font-size: 0.78125rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 150ms ease;
            cursor: pointer;
        }
        .sipetron-wa-btn:hover {
            background: #128C5A;
            color: #FFFFFF !important;
            border-color: #128C5A;
            box-shadow: 0 2px 6px rgba(18, 140, 90, 0.25);
        }
        .sipetron-doc-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 0.65rem 0.8rem;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 0.625rem;
            transition: all 150ms ease;
        }
        .sipetron-doc-item:hover {
            border-color: #CBD5E1;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
        }
        .sipetron-chip-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 9px;
            border-radius: 7px;
            border: 1px solid #E2E8F0;
            background: #F8FAF9;
            color: #334155;
            font-size: 11px;
            font-weight: 500;
            cursor: pointer;
            transition: all 150ms ease;
            text-align: left;
            text-decoration: none;
        }
        .sipetron-chip-btn:hover {
            border-color: #CBD5E1;
            background: #E8F3EC;
            color: #0F3D2E;
        }
        html.sipetron-drawer-locked {
            overflow: hidden !important;
        }
        html.sipetron-drawer-locked body {
            padding-right: var(--sipetron-scrollbar-width, 0px) !important;
        }
        html.sipetron-drawer-locked aside.fi-sidebar,
        html.sipetron-drawer-locked aside.fi-main-sidebar {
            top: 0 !important;
        }
        html.sipetron-drawer-locked .fi-topbar {
            top: 0 !important;
        }

        /* Per Page Dropup */
        .sipetron-perpage-trigger {
            height: 32px;
            box-sizing: border-box;
            padding: 0 8px 0 10px;
            border-radius: 8px;
            border: 1px solid #E2E8F0;
            background: #FFFFFF;
            font-size: 13px;
            font-weight: 700;
            color: #0F3D2E;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            outline: none;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
            transition: all 150ms ease;
        }
        .sipetron-perpage-trigger:hover {
            border-color: #CBD5E1;
            background: #F8FAF9;
        }
        .sipetron-perpage-trigger.is-open {
            border-color: #0F3D2E !important;
            box-shadow: 0 0 0 2px rgba(15, 61, 46, 0.15) !important;
        }
        .sipetron-perpage-menu {
            position: absolute;
            bottom: calc(100% + 6px);
            left: 0;
            min-width: 120px;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.08);
            padding: 4px;
            z-index: 50;
            box-sizing: border-box;
        }
        .sipetron-perpage-item {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 8px;
            border-radius: 6px;
            border: none;
            background: transparent;
            font-size: 12px;
            font-weight: 500;
            color: #334155;
            cursor: pointer;
            transition: background 120ms ease;
        }
        .sipetron-perpage-item:hover {
            background: #F1F5F9;
            color: #0F3D2E;
        }
        .sipetron-perpage-item.is-selected {
            background: #E8F3EC;
            color: #0F3D2E;
            font-weight: 700;
        }
        /* Modern Custom Pagination Styling (Persis seperti di Halaman Pengajuan) */
        .sipetron-pagination-wrap nav {
            display: flex !important;
            align-items: center !important;
            box-shadow: none !important;
        }
        .sipetron-pagination-wrap nav > div:first-child {
            display: none !important;
        }
        .sipetron-pagination-wrap nav p {
            display: none !important;
        }
        .sipetron-pagination-wrap nav > div:last-child {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            width: auto !important;
        }
        .sipetron-pagination-wrap nav span.shadow-sm {
            box-shadow: none !important;
            border-radius: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
        }
        .sipetron-pagination-wrap nav span.shadow-sm > span {
            border: none !important;
            background: transparent !important;
            padding: 0 !important;
            box-shadow: none !important;
            display: inline-flex !important;
        }
        .sipetron-pagination-wrap nav span.shadow-sm button,
        .sipetron-pagination-wrap nav span.shadow-sm span > span {
            margin: 0 !important;
            border-radius: 8px !important;
            border: 1px solid #E2E8F0 !important;
            min-width: 34px !important;
            height: 34px !important;
            padding: 0 10px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: #475569 !important;
            background: #FFFFFF !important;
            text-decoration: none !important;
            cursor: pointer !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02) !important;
            transition: all 150ms ease !important;
        }
        .sipetron-pagination-wrap nav span.shadow-sm button:hover {
            background-color: #F8FAF9 !important;
            border-color: #CBD5E1 !important;
            color: #0F3D2E !important;
        }
        .sipetron-pagination-wrap nav span.shadow-sm span[aria-current="page"] > span {
            background: #0F3D2E !important;
            color: #FFFFFF !important;
            border-color: #0F3D2E !important;
            box-shadow: 0 2px 6px rgba(15, 61, 46, 0.25) !important;
            cursor: default !important;
        }
        .sipetron-pagination-wrap nav span.shadow-sm span[aria-disabled="true"] > span {
            opacity: 0.45 !important;
            background: #F8FAFC !important;
            border-color: #E2E8F0 !important;
            cursor: not-allowed !important;
            color: #94A3B8 !important;
            box-shadow: none !important;
        }
        .sipetron-pagination-wrap nav span.shadow-sm svg {
            width: 14px !important;
            height: 14px !important;
        }
    </style>
