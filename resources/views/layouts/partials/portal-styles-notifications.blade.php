    <style>

        .sipetron-notif-list {
            padding: 0.625rem !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 0.375rem !important;
            background-color: #F8FAFC !important;
        }

        .sipetron-notif-card {
            display: flex !important;
            align-items: flex-start !important;
            gap: 0.625rem !important;
            padding: 0.625rem 0.75rem !important;
            background-color: #FFFFFF !important;
            border: 1px solid #E2E8F0 !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03) !important;
            cursor: pointer !important;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease !important;
            text-decoration: none !important;
            position: relative !important;
            overflow: visible !important;
        }

        .sipetron-notif-card:hover {
            border-color: #CBD5E1 !important;
            box-shadow: 0 2px 6px -1px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04) !important;
        }

        .sipetron-notif-card.is-success:hover { border-color: #86EFAC !important; background-color: #F9FCF9 !important; }
        .sipetron-notif-card.is-info:hover    { border-color: #93C5FD !important; background-color: #F8FAFC !important; }
        .sipetron-notif-card.is-danger:hover  { border-color: #CBD5E1 !important; background-color: #FAFAFA !important; }

        [data-unread="true"] .sipetron-notif-card {
            background-color: #F8FAFC !important;
            border-color: #BFDBFE !important;
        }
        [data-unread="true"] .sipetron-notif-card:hover {
            background-color: #F0F7FF !important;
        }
        [data-unread="true"] .sipetron-notif-card.is-success:hover { border-color: #86EFAC !important; }
        [data-unread="true"] .sipetron-notif-card.is-info:hover    { border-color: #60A5FA !important; }
        [data-unread="true"] .sipetron-notif-card.is-danger:hover  { border-color: #93C5FD !important; }

        .sipetron-notif-icon-box {
            width: 2rem !important;
            height: 2rem !important;
            border-radius: 0.45rem !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
            margin-top: 0.0625rem !important;
        }

        .sipetron-notif-icon-box.is-success { background-color: #ECFDF5 !important; color: #059669 !important; border: 1px solid #A7F3D0 !important; }
        .sipetron-notif-icon-box.is-info    { background-color: #EFF6FF !important; color: #2563EB !important; border: 1px solid #BFDBFE !important; }
        .sipetron-notif-icon-box.is-danger  { background-color: #FFF1F2 !important; color: #E11D48 !important; border: 1px solid #FECDD3 !important; }

        .sipetron-notif-icon-box svg {
            width: 0.9rem !important;
            height: 0.9rem !important;
            flex-shrink: 0 !important;
        }

        .sipetron-notif-body-wrap {
            flex: 1 1 0% !important;
            min-width: 0 !important;
        }

        .sipetron-notif-row-1 {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 0.375rem !important;
            margin-bottom: 0.2rem !important;
        }

        .sipetron-notif-card-title {
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            color: #0F172A !important;
            line-height: 1.25 !important;
            margin: 0 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            transition: color 0.12s !important;
        }

        .sipetron-notif-card.is-success:hover .sipetron-notif-card-title { color: #047857 !important; }
        .sipetron-notif-card.is-info:hover    .sipetron-notif-card-title { color: #1D4ED8 !important; }
        .sipetron-notif-card.is-danger:hover  .sipetron-notif-card-title { color: #0F172A !important; }

        .sipetron-notif-meta-right {
            display: flex !important;
            align-items: center !important;
            gap: 0.35rem !important;
            flex-shrink: 0 !important;
        }

        .sipetron-notif-time-text {
            font-size: 0.625rem !important;
            color: #94A3B8 !important;
            white-space: nowrap !important;
            line-height: 1.2 !important;
        }

        .sipetron-notif-unread-dot {
            display: none !important;
            width: 0.4375rem !important;
            height: 0.4375rem !important;
            border-radius: 50% !important;
            background-color: #3B82F6 !important;
            flex-shrink: 0 !important;
        }

        [data-unread="true"] .sipetron-notif-unread-dot {
            display: inline-block !important;
        }

        .sipetron-notif-row-2 {
            display: flex !important;
            align-items: center !important;
            gap: 0.35rem !important;
            margin-bottom: 0.15rem !important;
            min-width: 0 !important;
            overflow: hidden !important;
        }

        .sipetron-notif-pill {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
            font-size: 0.5875rem !important;
            font-weight: 700 !important;
            padding: 0.0625rem 0.3125rem !important;
            border-radius: 0.25rem !important;
            white-space: nowrap !important;
            flex-shrink: 0 !important;
            line-height: 1.5 !important;
            letter-spacing: -0.01em !important;
        }

        .sipetron-notif-pill.is-success { background-color: #ECFDF5 !important; color: #047857 !important; border: 1px solid #A7F3D0 !important; }
        .sipetron-notif-pill.is-info    { background-color: #EFF6FF !important; color: #1D4ED8 !important; border: 1px solid #BFDBFE !important; }
        .sipetron-notif-pill.is-danger  { background-color: #FFF1F2 !important; color: #BE123C !important; border: 1px solid #FECDD3 !important; }

        .sipetron-notif-activity-title {
            font-size: 0.6875rem !important;
            font-weight: 600 !important;
            color: #334155 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            line-height: 1.3 !important;
            min-width: 0 !important;
        }

        .sipetron-notif-desc-text {
            font-size: 0.6375rem !important;
            color: #64748B !important;
            line-height: 1.35 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            margin: 0 !important;
        }

        .sipetron-notif-right-col {
            position: relative !important;
            width: 1.25rem !important;
            height: 1.25rem !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            align-self: center !important;
            flex-shrink: 0 !important;
        }

        .sipetron-notif-arrow-icon {
            width: 0.8rem !important;
            height: 0.8rem !important;
            color: #CBD5E1 !important;
            transition: opacity 0.15s ease, color 0.15s ease, transform 0.15s ease !important;
            flex-shrink: 0 !important;
            opacity: 1 !important;
            display: block !important;
        }

        /* Desktop: Arrow terlihat normal, hilang saat hover dan digantikan tombol hapus */
        @media (hover: hover) and (min-width: 641px) {
            .sipetron-notif-card:hover .sipetron-notif-arrow-icon {
                opacity: 0 !important;
                transform: translateX(3px) !important;
            }

            .sipetron-notif-card:hover .sipetron-notif-del-btn {
                opacity: 1 !important;
            }

            .sipetron-notif-del-btn:hover {
                color: #EF4444 !important;
                background-color: #FEE2E2 !important;
            }
        }

        .sipetron-notif-del-btn {
            position: absolute !important;
            inset: 0 !important;
            width: 100% !important;
            height: 100% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border: none !important;
            background: transparent !important;
            color: #94A3B8 !important;
            border-radius: 0.3125rem !important;
            cursor: pointer !important;
            transition: opacity 0.15s ease, color 0.12s ease, background-color 0.12s ease, transform 0.1s ease !important;
            padding: 0 !important;
            opacity: 0 !important;
            z-index: 2 !important;
            line-height: 1 !important;
            -webkit-tap-highlight-color: transparent !important;
        }

        .sipetron-notif-del-btn svg {
            width: 0.7rem !important;
            height: 0.7rem !important;
            display: block !important;
        }

        /* Mobile / Touch Devices: Tombol silang langsung tampil jelas, ramah jempol, dan interaktif */
        @media (max-width: 640px), (hover: none) {
            .sipetron-notif-arrow-icon {
                display: none !important;
            }

            .sipetron-notif-right-col {
                width: 1.75rem !important;
                height: 1.75rem !important;
                margin-left: 0.125rem !important;
            }

            .sipetron-notif-del-btn {
                opacity: 1 !important;
                background-color: #F1F5F9 !important;
                color: #64748B !important;
                border-radius: 0.375rem !important;
            }

            .sipetron-notif-del-btn svg {
                width: 0.75rem !important;
                height: 0.75rem !important;
            }

            .sipetron-notif-del-btn:active {
                color: #EF4444 !important;
                background-color: #FEE2E2 !important;
                transform: scale(0.92) !important;
            }
        }

        /* ========================================================
           E-VITRON MINT PASTEL FLOATING TOAST (SAMA PERSIS ADMIN)
           ======================================================== */
        .sipetron-toast-container {
            position: fixed;
            top: 5.25rem; /* 84px: persis di bawah header navbar (70px + 14px jeda napas) */
            right: 1.5rem; /* 24px: persis panel admin */
            z-index: 99980; /* Di bawah backdrop & drawer sidebar (99990+) agar tidak menutupi menu navigasi */
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.625rem;
            pointer-events: none;
            max-width: calc(100vw - 3rem);
            transition: top 200ms cubic-bezier(0.16, 1, 0.3, 1), opacity 200ms cubic-bezier(0.16, 1, 0.3, 1), transform 200ms cubic-bezier(0.16, 1, 0.3, 1), visibility 200ms ease;
        }

        /* Saat Sidebar Terbuka: Sembunyikan toast secara mulus dan matikan interaksinya */
        .mobile-nav-open .sipetron-toast-container {
            opacity: 0 !important;
            visibility: hidden !important;
            pointer-events: none !important;
            transform: translateY(-8px) scale(0.96) !important;
        }

        @media (max-width: 768px) {
            .sipetron-toast-container {
                top: 4.5rem; /* Tepat di bawah navbar ponsel */
                bottom: auto;
                right: 0.875rem; /* Pojok kanan atas */
                left: 0.875rem;
                align-items: flex-end;
                max-width: none;
                flex-direction: column;
            }
            .sipetron-toast-floating-item {
                transform-origin: top right;
                width: auto;
                max-width: 100%;
                display: flex;
                justify-content: flex-end;
            }
            .sipetron-toast-card {
                min-width: 280px;
                width: auto;
                max-width: min(360px, calc(100vw - 1.75rem));
                box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.18), 0 4px 10px -2px rgba(0, 0, 0, 0.08);
            }
            @keyframes sipetronToastEnter {
                0% {
                    opacity: 0;
                    transform: translateY(-14px) scale(0.96);
                }
                100% {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }
            @keyframes sipetronToastLeave {
                0% {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
                100% {
                    opacity: 0;
                    transform: translateY(-10px) scale(0.96);
                }
            }
        }

        .sipetron-toast-floating-item {
            pointer-events: auto;
            transform-origin: top right;
            transition: opacity 180ms cubic-bezier(0.16, 1, 0.3, 1), transform 180ms cubic-bezier(0.16, 1, 0.3, 1);
        }

        .sipetron-toast-floating-item.is-entering {
            animation: sipetronToastEnter 180ms cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .sipetron-toast-floating-item.is-leaving {
            animation: sipetronToastLeave 180ms cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes sipetronToastEnter {
            0% {
                opacity: 0;
                transform: translateX(16px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes sipetronToastLeave {
            0% {
                opacity: 1;
                transform: translateX(0);
            }
            100% {
                opacity: 0;
                transform: translateX(8px);
            }
        }

        .sipetron-toast-card {
            position: relative;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            min-width: 300px;
            max-width: 375px;
            width: auto;
            box-sizing: border-box;
            padding: 9px 12px 10px 12px;
            background-color: #EDF9F2;
            border: 1px solid #C3EED3;
            border-radius: 10px;
            box-shadow: 0 4px 16px -2px rgba(16, 185, 129, 0.12), 0 2px 5px -1px rgba(0, 0, 0, 0.04);
            cursor: default;
            user-select: none;
            transition: box-shadow 150ms ease, border-color 150ms ease;
        }

        .sipetron-toast-card.is-danger {
            background-color: #FEF2F2;
            border-color: #FECACA;
            box-shadow: 0 4px 16px -2px rgba(239, 68, 68, 0.12), 0 2px 5px -1px rgba(0, 0, 0, 0.04);
        }

        .sipetron-toast-card.is-clickable {
            cursor: pointer;
        }

        .sipetron-toast-card:hover {
            box-shadow: 0 8px 24px -4px rgba(16, 185, 129, 0.18), 0 3px 8px -1px rgba(0, 0, 0, 0.06);
        }

        .sipetron-toast-card.is-danger:hover {
            box-shadow: 0 8px 24px -4px rgba(239, 68, 68, 0.18), 0 3px 8px -1px rgba(0, 0, 0, 0.06);
        }

        .sipetron-toast-icon-circle {
            width: 20px;
            height: 20px;
            min-width: 20px;
            max-width: 20px;
            border-radius: 50%;
            background-color: #10B981;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .sipetron-toast-icon-circle svg {
            width: 12px;
            height: 12px;
            stroke: #FFFFFF;
        }

        .sipetron-toast-icon-circle.is-danger {
            background-color: #EF4444;
        }

        .sipetron-toast-content {
            flex: 1 1 auto;
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .sipetron-toast-title {
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.25;
            color: #0F3D2E;
            margin: 0;
            padding: 0;
            letter-spacing: -0.01em;
        }

        .sipetron-toast-card.is-danger .sipetron-toast-title {
            color: #991B1B;
        }

        .sipetron-toast-meta {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 4px;
            flex-wrap: nowrap;
            overflow: hidden;
        }

        .sipetron-toast-ticket {
            display: inline-flex;
            align-items: center;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.01em;
            line-height: 1.3;
            color: #065F46;
            background-color: #DCF5E6;
            border: 1px solid #B8E8C9;
            border-radius: 4px;
            padding: 1px 6px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .sipetron-toast-card.is-danger .sipetron-toast-ticket {
            color: #991B1B;
            background-color: #FEE2E2;
            border-color: #FECACA;
        }

        .sipetron-toast-dot {
            font-size: 11px;
            line-height: 1;
            color: #94A3B8;
            flex-shrink: 0;
            user-select: none;
        }

        .sipetron-toast-desc {
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 11.5px;
            font-weight: 400;
            line-height: 1.3;
            color: #4B6358;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex-shrink: 1;
        }

        .sipetron-toast-card.is-danger .sipetron-toast-desc {
            color: #7F1D1D;
        }

        .sipetron-toast-close-btn {
            width: 20px;
            height: 20px;
            min-width: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            padding: 0;
            margin: 0;
            margin-left: 2px;
            margin-top: 1px;
            color: #9CA3AF;
            cursor: pointer;
            border-radius: 4px;
            flex-shrink: 0;
            transition: color 140ms ease, background-color 140ms ease;
        }

        .sipetron-toast-close-btn:hover {
            color: #1F2937;
            background-color: rgba(0, 0, 0, 0.04);
        }

        .sipetron-toast-close-btn svg {
            width: 13px;
            height: 13px;
            stroke: currentColor;
        }
    </style>
