{{-- Bagian 1: Stylesheet Layout, Drawer, Banner & Modal Instan (Dipanggil oleh list-submissions.blade.php) --}}
    <style>
        .fi-page:has(.sipetron-submissions-container) section {
            padding-top: 0.25rem !important;
            padding-bottom: 2rem !important;
            gap: 1.125rem !important;
        }

        .fi-page:has(.sipetron-submissions-container) section > div.grid {
            gap: 1.125rem !important;
        }

        .fi-page:has(.sipetron-submissions-container) .fi-header {
            margin-bottom: 0.25rem !important;
            padding-bottom: 0 !important;
        }

        @keyframes sipetron-ping {
            75%, 100% {
                transform: scale(2.2);
                opacity: 0;
            }
        }

        .sipetron-tooltip-container {
            position: relative;
            display: inline-flex;
            align-items: center;
        }
        .sipetron-tooltip-bubble {
            visibility: hidden;
            opacity: 0;
            position: absolute;
            bottom: calc(100% + 6px);
            left: 50%;
            transform: translateX(-50%);
            background: #0F172A;
            color: #FFFFFF;
            font-size: 0.6875rem;
            font-weight: 600;
            line-height: 1.4;
            padding: 0.35rem 0.65rem;
            border-radius: 0.375rem;
            white-space: nowrap;
            z-index: 50;
            pointer-events: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.15), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
        }
        .sipetron-tooltip-bubble::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border-width: 4px;
            border-style: solid;
            border-color: #0F172A transparent transparent transparent;
        }
        .sipetron-tooltip-container:hover .sipetron-tooltip-bubble {
            visibility: visible;
            opacity: 1;
            transform: translateX(-50%) translateY(-2px);
        }
        .sipetron-tab-btn {
            padding: 0.625rem 0.875rem;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #64748B;
            border-bottom: 2px solid transparent;
            transition: all 0.15s ease;
            background: none;
            border-top: none;
            border-left: none;
            border-right: none;
            cursor: pointer;
        }
        .sipetron-tab-btn:hover {
            color: #0F3D2E;
        }
        .sipetron-tab-btn.active {
            color: #0F3D2E;
            font-weight: 700;
            border-bottom: 2px solid #0F3D2E;
        }
        .sipetron-card-box {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }
        /* Scroll-lock helper for background page when drawer is open */
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

        /* Slide-over Drawer Overlay (Sama Persis dengan Kelola Videotron: Hitam Netral Murni Tanpa Blur) */
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
        .sipetron-tabs-nav {
            display: flex;
            align-items: center;
            gap: 4px;
            background: #F1F5F4;
            padding: 4px;
            border-radius: 10px;
            border: 1px solid #E2E8F0;
        }
        .sipetron-tab-pill {
            flex: 1;
            padding: 0.5rem 0.6rem;
            font-size: 0.78125rem;
            font-weight: 600;
            color: #64748B;
            border-radius: 7px;
            border: none;
            background: transparent;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 150ms ease;
            white-space: nowrap;
        }
        .sipetron-tab-pill:hover:not(.active) {
            color: #0F3D2E;
            background: rgba(255, 255, 255, 0.65);
        }
        .sipetron-tab-pill.active {
            background: #FFFFFF;
            color: #0F3D2E;
            font-weight: 700;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
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
        }
        .sipetron-chip-btn:hover {
            border-color: #CBD5E1;
            background: #E8F3EC;
            color: #0F3D2E;
        }
        .sipetron-action-btn {
            min-height: 2.625rem;
            padding: 0.55rem 0.85rem;
            border-radius: 0.5rem;
            font-size: 0.8125rem;
            font-weight: 700;
            color: #FFFFFF !important;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            text-decoration: none;
            transition: filter 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease;
        }
        .sipetron-action-btn:hover { filter: brightness(0.94); transform: translateY(-1px); }
        .sipetron-action-btn:active { transform: translateY(0) scale(0.98); }
        .sipetron-btn-approve {
            background-color: #0F3D2E !important;
            color: #FFFFFF !important;
            box-shadow: 0 2px 6px rgba(15, 61, 46, 0.25);
        }
        .sipetron-btn-approve:hover {
            background-color: #165641 !important;
            color: #FFFFFF !important;
        }
        .sipetron-btn-revision {
            background-color: #0284C7 !important;
            color: #FFFFFF !important;
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25);
        }
        .sipetron-btn-revision:hover {
            background-color: #0369A1 !important;
            color: #FFFFFF !important;
        }
        .sipetron-footer-flex-row {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 0.625rem !important;
            width: 100% !important;
        }
        .sipetron-footer-flex-row[style*="display: none"] {
            display: none !important;
        }
        .sipetron-footer-status-box {
            flex: 1 1 0% !important;
            min-width: 0 !important;
            height: 36px !important;
            box-sizing: border-box !important;
            font-size: 0.75rem !important;
            padding: 0 0.75rem !important;
            border-radius: 0.5rem !important;
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            gap: 0.4rem !important;
            margin: 0 !important;
        }
        .sipetron-footer-edit-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.45rem !important;
            height: 36px !important;
            width: auto !important;
            padding: 0 0.85rem !important;
            border-radius: 8px !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #0F3D2E !important;
            background: #FFFFFF !important;
            border: 1px solid #CBD5E1 !important;
            cursor: pointer !important;
            flex: 0 0 auto !important;
            flex-shrink: 0 !important;
            transition: all 150ms ease !important;
            text-decoration: none !important;
            white-space: nowrap !important;
            margin: 0 !important;
        }
        .sipetron-footer-edit-btn:hover {
            background: #E8F3EC !important;
            border-color: #0F3D2E !important;
            color: #0F3D2E !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 2px 6px rgba(15, 61, 46, 0.12) !important;
        }
        .sipetron-footer-edit-btn:active {
            transform: translateY(0) !important;
        }
        .sipetron-footer-edit-btn.is-cancel {
            color: #475569 !important;
        }
        .sipetron-footer-edit-btn.is-cancel:hover {
            background: #FEF2F2 !important;
            border-color: #FCA5A5 !important;
            color: #DC2626 !important;
            box-shadow: 0 2px 6px rgba(220, 38, 38, 0.1) !important;
        }
        .sipetron-btn-kirim-revisi {
            height: 38px !important;
            padding: 0 1.25rem !important;
            border-radius: 10px !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            background-color: #0284C7 !important;
            color: #FFFFFF !important;
            border: none !important;
            cursor: pointer !important;
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25) !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.45rem !important;
            transition: all 0.15s ease !important;
        }
        .sipetron-btn-kirim-revisi:hover {
            background-color: #0369A1 !important;
            box-shadow: 0 4px 10px rgba(2, 132, 199, 0.35) !important;
        }
        .sipetron-btn-kirim-revisi:active {
            transform: scale(0.98) !important;
        }
        .sipetron-rev-target-btn {
            height: 38px !important;
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.5rem !important;
            padding: 0 0.85rem !important;
            border-radius: 8px !important;
            font-size: 0.8125rem !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            background: #FFFFFF !important;
            border: 1.5px solid #CBD5E1 !important;
            color: #475569 !important;
            box-sizing: border-box !important;
            transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease, box-shadow 0.15s ease !important;
        }
        .sipetron-rev-target-btn svg {
            color: #64748B;
            transition: color 0.15s ease;
        }
        .sipetron-rev-target-btn:hover {
            border-color: #94A3B8 !important;
            background: #F8FAFC !important;
            color: #334155 !important;
        }
        .sipetron-rev-target-btn:hover svg {
            color: #334155;
        }
        .sipetron-rev-target-btn.is-active {
            background: #EFF6FF !important;
            border-color: #0284C7 !important;
            color: #0284C7 !important;
            font-weight: 600 !important;
            box-shadow: 0 1px 2px rgba(2, 132, 199, 0.08) !important;
        }
        .sipetron-rev-target-btn.is-active svg {
            color: #0284C7 !important;
        }
        .sipetron-btn-reject {
            background-color: #DC2626 !important;
            color: #FFFFFF !important;
            box-shadow: 0 2px 6px rgba(220, 38, 38, 0.25);
        }
        .sipetron-btn-reject:hover {
            background-color: #B91C1C !important;
            color: #FFFFFF !important;
        }

        .sipetron-instant-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.60);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            padding: 1rem;
        }
        .sipetron-instant-modal-card {
            background: #FFFFFF;
            border-radius: 1rem;
            width: 100%;
            max-height: calc(100vh - 2rem);
            overflow-y: auto;
            box-sizing: border-box;
            padding: 1.5rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
            border: 1px solid #E2E8F0;
        }
    </style>
