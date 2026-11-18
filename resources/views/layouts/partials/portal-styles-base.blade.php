    <style>
        [x-cloak] { display: none !important; }
        html {
            overflow-x: hidden;
            scroll-behavior: smooth;
            scrollbar-width: thin;
            scrollbar-color: #CBD5E1 transparent;
        }
        body { 
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; 
            background-color: #FFFFFF;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            scrollbar-width: thin;
            scrollbar-color: #CBD5E1 transparent;
        }

        /* Modern Custom Scrollbar (Sama Persis Panel Admin) */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 9999px;
            border: 2px solid transparent;
            background-clip: content-box;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
            border: 2px solid transparent;
            background-clip: content-box;
        }

        /* Scroll-lock saat overlay / drawer / modal terbuka (Sama Persis Panel Admin: Bebas Sentakan / Zero Layout Shift) */
        html.sipetron-drawer-locked,
        html.sipetron-drawer-locked body {
            overflow: hidden !important;
            height: 100% !important;
            max-height: 100vh !important;
            overscroll-behavior: none !important;
        }
        html.sipetron-drawer-locked [role="dialog"],
        html.sipetron-drawer-locked [role="dialog"] * {
            overscroll-behavior: contain !important;
        }
        html.sipetron-drawer-locked body {
            padding-right: var(--sipetron-scrollbar-width, 0px) !important;
        }
        html.sipetron-drawer-locked header {
            right: var(--sipetron-scrollbar-width, 0px) !important;
        }

        /* Slide-over Drawer Overlay (Sama Persis Panel Admin: Hitam Netral Murni Tanpa Blur, Full Viewport) */
        .sipetron-drawer-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.60) !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            z-index: 99990 !important;
            touch-action: none;
            overscroll-behavior: contain;
            cursor: pointer;
        }

        /* Kartu Pengajuan Aktif dengan List Garis Kuning/Emas Tipis Elegan */
        .sipetron-card-active {
            border-color: #D9A93B !important;
            box-shadow: 0 0 0 1.5px #D9A93B, 0 8px 24px -4px rgba(217, 169, 59, 0.22), 0 2px 6px rgba(14, 77, 60, 0.08) !important;
            position: relative;
            z-index: 5;
            outline: none !important;
        }

        /* Drawer Panel (Sama Persis Panel Admin: Width 580px, Height 100vh, Full Top-Right Layer) */
        .sipetron-drawer-panel {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            max-width: 580px;
            height: 100vh;
            background: #FFFFFF;
            z-index: 99991 !important;
            box-shadow: -10px 0 40px rgba(15, 61, 46, 0.16), -2px 0 10px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
            overscroll-behavior: contain;
        }

        .sipetron-drawer-header {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid #E2E8F0;
            background: #FFFFFF;
            flex-shrink: 0;
            overscroll-behavior: contain;
        }
        @media (min-width: 640px) {
            .sipetron-drawer-header {
                padding: 1rem 1.25rem;
            }
        }

        .sipetron-drawer-body {
            flex: 1 1 0%;
            min-height: 0;
            overflow-y: auto;
            overscroll-behavior: contain !important;
            -webkit-overflow-scrolling: touch;
            padding: 0.875rem 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
            box-sizing: border-box;
            background: #F8FAF9;
            scrollbar-width: thin;
            scrollbar-color: #CBD5E1 transparent;
        }
        @media (min-width: 640px) {
            .sipetron-drawer-body {
                padding: 1rem 1.25rem;
            }
        }
        .sipetron-drawer-body::-webkit-scrollbar {
            width: 5px;
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
        .sipetron-drawer-body::-webkit-scrollbar-button {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }

        .sipetron-drawer-footer {
            padding: 0.75rem 1rem;
            border-top: 1px solid #E2E8F0;
            background: #FFFFFF;
            flex-shrink: 0;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.03);
            overscroll-behavior: contain;
        }
        @media (min-width: 640px) {
            .sipetron-drawer-footer {
                padding: 0.85rem 1.25rem;
            }
        }

        .font-montserrat {
            font-family: 'Montserrat', 'Plus Jakarta Sans', sans-serif;
        }

        .ornament-pattern {
            pointer-events: none;
            position: absolute;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60' viewBox='0 0 60 60'%3E%3Cg fill='none' stroke-linejoin='round'%3E%3Cg stroke='%23DDBA6E' stroke-width='3.6'%3E%3Cpath d='M30 9 L36.2 15.1 L44.8 15.2 L44.9 23.8 L51 30 L44.9 36.2 L44.8 44.8 L36.2 44.9 L30 51 L23.8 44.9 L15.2 44.8 L15.1 36.2 L9 30 L15.1 23.8 L15.2 15.2 L23.8 15.1 Z'/%3E%3Cpath d='M0 0 L15.2 15.2 M60 0 L44.8 15.2 M0 60 L15.2 44.8 M60 60 L44.8 44.8'/%3E%3Cpath d='M30 19 L41 30 L30 41 L19 30 Z'/%3E%3C/g%3E%3Cg stroke='%23FBF6EA' stroke-width='1.3'%3E%3Cpath d='M30 9 L36.2 15.1 L44.8 15.2 L44.9 23.8 L51 30 L44.9 36.2 L44.8 44.8 L36.2 44.9 L30 51 L23.8 44.9 L15.2 44.8 L15.1 36.2 L9 30 L15.1 23.8 L15.2 15.2 L23.8 15.1 Z'/%3E%3Cpath d='M0 0 L15.2 15.2 M60 0 L44.8 15.2 M0 60 L15.2 44.8 M60 60 L44.8 44.8'/%3E%3Cpath d='M30 19 L41 30 L30 41 L19 30 Z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            background-size: 60px 60px;
            mask-image: radial-gradient(circle at center, black 20%, transparent 68%);
            -webkit-mask-image: radial-gradient(circle at center, black 20%, transparent 68%);
        }

        /* Dropdown panel */
        .dropdown-panel {
            display: none;
        }
        .dropdown-panel.open {
            display: block;
        }

        /* Tooltip notifikasi tepat di tengah (centered) dan selaras tema UIN SSC (#0E4D3C) */
        .portal-tooltip {
            position: absolute;
            left: 50%;
            top: calc(100% + 6px);
            z-index: 60;
            pointer-events: none;
            display: none;
            transform: translate(-50%, 0);
        }

        #notif-wrapper:hover #notif-tooltip {
            display: block;
        }

        /* Tooltip otomatis hilang saat drawer notifikasi terbuka */
        html.notif-drawer-open #notif-tooltip,
        #notif-wrapper:has(#notif-panel.open) #notif-tooltip {
            display: none !important;
        }

        /* Micro-animation: Goyangan lonceng notifikasi saat kursor disorot */
        @keyframes bell-ring {
            0%, 100% { transform: rotate(0); }
            20% { transform: rotate(14deg); }
            40% { transform: rotate(-12deg); }
            60% { transform: rotate(8deg); }
            80% { transform: rotate(-4deg); }
        }
        #notif-wrapper:hover .bell-icon {
            animation: bell-ring 600ms ease-in-out;
            transform-origin: top center;
        }

        /* Hilangkan garis/lingkaran fokus hitam pada lonceng saat ditutup / tombol Esc */
        #notif-btn,
        #notif-btn:focus,
        #notif-btn:focus-visible {
            outline: none !important;
            box-shadow: none !important;
            -webkit-tap-highlight-color: transparent !important;
        }
        /* Smooth Navigation Links & Underline Indicator */
        .nav-link {
            display: inline-block;
            transition: color 280ms cubic-bezier(0.16, 1, 0.3, 1), transform 280ms cubic-bezier(0.16, 1, 0.3, 1);
            will-change: transform, color;
        }
        .group\/nav:hover .nav-link {
            color: #0E4D3C;
            transform: translateY(-1.5px);
        }
        .nav-bar-hover {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            border-radius: 9999px 9999px 0 0;
            background-color: #D9A93B;
            transform: scaleX(0);
            transform-origin: center;
            pointer-events: none;
        }
        .group\/nav:hover .nav-bar-hover {
            transform: scaleX(1);
        }
        .nav-bar-active {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            border-radius: 9999px 9999px 0 0;
            background-color: #D9A93B;
            pointer-events: none;
        }

        /* Smooth CTA Hero Buttons Interaction */
        .btn-hero-primary {
            background-color: #D9A93B;
            box-shadow: 0 4px 14px rgba(217, 169, 59, 0.30);
            transition: all 250ms cubic-bezier(0.16, 1, 0.3, 1);
            will-change: transform, background-color, box-shadow;
        }
        .btn-hero-primary:hover {
            background-color: #C5942B;
            box-shadow: 0 8px 22px rgba(197, 148, 43, 0.45);
            transform: translateY(-2px);
        }
        .btn-hero-primary:active {
            transform: translateY(0) scale(0.98);
        }

        .btn-hero-secondary {
            background-color: #ffffff;
            border: 1px solid #0E4D3C;
            color: #0E4D3C;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            transition: all 250ms cubic-bezier(0.16, 1, 0.3, 1);
            will-change: transform, background-color, color, box-shadow;
        }
        .btn-hero-secondary:hover {
            background-color: #0E4D3C;
            border-color: #0E4D3C;
            color: #ffffff;
            box-shadow: 0 6px 18px rgba(14, 77, 60, 0.25);
            transform: translateY(-2px);
        }
        .btn-hero-secondary:active {
            transform: translateY(0) scale(0.98);
        }
        /* ========================================================
           E-VITRON NOTIFICATION DRAWER & CARDS STYLING (SAMA PERSIS ADMIN)
           ======================================================== */
        .sipetron-notif-header-wrap {
            width: 100% !important;
            padding-right: 2.25rem !important;
        }

        .sipetron-notif-header-title-row {
            display: flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
        }

        .sipetron-notif-header-title {
            font-size: 1rem !important;
            font-weight: 700 !important;
            color: #0F172A !important;
            margin: 0 !important;
            line-height: 1.3 !important;
            letter-spacing: -0.01em !important;
        }

        .sipetron-notif-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 1.25rem;
            height: 1.25rem;
            padding: 0 0.375rem;
            font-size: 0.6875rem;
            font-weight: 700;
            color: #1D4ED8;
            background-color: #EFF6FF;
            border: 1px solid #BFDBFE;
            border-radius: 9999px;
            line-height: 1;
        }

        .sipetron-notif-badge.is-hidden,
        .sipetron-notif-badge[style*="display: none"] {
            display: none !important;
        }

        .sipetron-notif-heading-subtitle {
            font-size: 0.75rem !important;
            color: #64748B !important;
            margin: 0.2rem 0 0 0 !important;
            line-height: 1.4 !important;
            font-weight: 400 !important;
        }

        .sipetron-notif-actions-bar {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 0.625rem;
            padding-top: 0.5rem;
            border-top: 1px solid #F1F5F9;
        }

        .sipetron-notif-actions-bar.is-hidden,
        .sipetron-notif-actions-bar[style*="display: none"] {
            display: none !important;
        }

        .sipetron-notif-action-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.35rem !important;
            height: 1.75rem !important;
            font-size: 0.6875rem !important;
            font-weight: 600 !important;
            padding: 0 0.625rem !important;
            border-radius: 0.375rem !important;
            border: 1px solid transparent !important;
            cursor: pointer !important;
            transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease !important;
            line-height: 1 !important;
            white-space: nowrap !important;
            text-decoration: none !important;
            outline: none !important;
        }

        .sipetron-notif-action-icon {
            width: 0.75rem !important;
            height: 0.75rem !important;
            flex-shrink: 0 !important;
        }

        .sipetron-notif-action-btn--read {
            background-color: #F0FDF4 !important;
            color: #15803D !important;
            border-color: #BBF7D0 !important;
        }

        .sipetron-notif-action-btn--read:hover {
            background-color: #DCFCE7 !important;
            color: #166534 !important;
            border-color: #86EFAC !important;
        }

        .sipetron-notif-action-btn--clear {
            background-color: #FEF2F2 !important;
            color: #B91C1C !important;
            border-color: #FECACA !important;
        }

        .sipetron-notif-action-btn--clear:hover {
            background-color: #FEE2E2 !important;
            color: #991B1B !important;
            border-color: #FCA5A5 !important;
        }
    </style>
