    <style>
        /* Layout spacing for dashboard sections */
        .fi-dashboard-page section {
            padding-top: 0.25rem !important;
            padding-bottom: 2rem !important;
            gap: 1.125rem !important;
        }

        .fi-dashboard-page section > div > div.grid,
        .fi-dashboard-page section > div.grid {
            gap: 1.125rem !important;
        }

        .fi-dashboard-page .fi-header,
        .fi-dashboard-page .sipetron-header-row {
            margin-bottom: 0.25rem !important;
            padding-bottom: 0 !important;
        }

        /* Prevent inline <script> and <style> children from occupying CSS Grid tracks inside Filament's page section. */
        .fi-dashboard-page script,
        .fi-dashboard-page style {
            display: none !important;
        }

        /* KPI cards grid */
        .sipetron-grid-stats {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 0.625rem;
            margin-bottom: 0 !important;
            width: 100%;
            box-sizing: border-box;
            align-items: stretch;
        }

        .sipetron-grid-charts {
            display: grid;
            grid-template-columns: minmax(0, 1.8fr) minmax(0, 1.2fr);
            gap: 1.125rem;
            margin-bottom: 0 !important;
            align-items: stretch;
            width: 100%;
            box-sizing: border-box;
        }

        .sipetron-grid-bottom {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 1.125rem;
            margin-bottom: 0 !important;
            align-items: stretch;
            width: 100%;
            box-sizing: border-box;
        }



        .sipetron-stat-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 0.875rem;
            padding: 0.75rem 0.55rem;
            display: flex;
            align-items: center;
            gap: 0.375rem;
            height: 100%;
            min-height: 5.5rem;
            box-sizing: border-box;
            text-decoration: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            transition: transform 200ms ease, box-shadow 200ms ease, border-color 200ms ease;
            min-width: 0;
            cursor: pointer;
        }

        .sipetron-stat-card:hover {
            transform: translateY(-2px);
            border-color: #86EFAC;
            box-shadow: 0 10px 20px -3px rgba(15, 61, 46, 0.10), 0 4px 6px -2px rgba(15, 61, 46, 0.04);
        }

        .sipetron-stat-arrow {
            color: #94A3B8;
            font-size: 1.1rem;
            line-height: 1;
            font-weight: 700;
            margin-left: auto;
            padding-left: 0;
            flex-shrink: 0;
            transition: transform 200ms ease, color 200ms ease;
        }

        .sipetron-stat-card:hover .sipetron-stat-arrow {
            transform: translateX(3px);
            color: #16A34A;
        }

        .sipetron-stat-icon {
            width: 1.875rem;
            height: 1.875rem;
            min-width: 1.875rem;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sipetron-stat-icon svg {
            width: 1rem;
            height: 1rem;
        }

        .sipetron-stat-content {
            min-width: 0;
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: visible;
        }

        /* Allow descenders (g/p/y) to render without clipping inside compact flex rows. */
        .sipetron-stat-label {
            font-size: 0.71875rem;
            font-weight: 700;
            color: #334155;
            line-height: 1.3;
            display: block;
            white-space: nowrap;
            letter-spacing: -0.015em;
            overflow: visible;
        }

        .sipetron-stat-value {
            font-size: 1.35rem;
            font-weight: 800;
            color: #0F172A;
            line-height: 1.1;
            display: block;
            margin: 0.075rem 0;
        }

        .sipetron-stat-desc {
            font-size: 0.65625rem;
            font-weight: 600;
            color: #475569;
            line-height: 1.45;
            display: block;
            white-space: nowrap;
            overflow: visible;
            padding-bottom: 2px;
        }

        /* Unified Card Box */
        .sipetron-card-box {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 1rem;
            padding: 1.25rem 1.35rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            min-width: 0;
            max-width: 100%;
            box-sizing: border-box;
            overflow: hidden;
            height: 100%;
            transition: box-shadow 200ms cubic-bezier(0.16, 1, 0.3, 1), border-color 200ms cubic-bezier(0.16, 1, 0.3, 1);
        }

        .sipetron-card-box:hover {
            border-color: #CBD5E1 !important;
            box-shadow: 0 6px 18px -2px rgba(15, 61, 46, 0.08), 0 2px 6px -1px rgba(0, 0, 0, 0.04) !important;
            transform: none !important;
        }

        /* Chart Widget Wrapper Auto-Fit Equal Height */
        .sipetron-grid-charts .fi-wi-chart,
        .sipetron-grid-charts .fi-wi-chart > .fi-section,
        .sipetron-grid-bottom .fi-wi-chart,
        .sipetron-grid-bottom .fi-wi-chart > .fi-section {
            height: 100%;
        }

        /* Allow floating period filter dropdown to overflow chart section boundaries without clipping. */
        .sipetron-grid-charts .fi-section,
        .sipetron-grid-charts .fi-section > div,
        .sipetron-grid-charts .fi-section-content-ctn,
        .sipetron-grid-charts .fi-wi-chart {
            overflow: visible !important;
        }

        /* Period filter dropdown */
        .sipetron-filter-wrapper {
            position: relative;
            flex-shrink: 0;
        }

        .sipetron-filter-trigger {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            height: 2rem;
            padding: 0 0.55rem;
            background-color: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 0.5rem;
            font-size: 0.71875rem;
            font-weight: 600;
            color: #334155;
            line-height: 1.2;
            cursor: pointer;
            outline: none !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            transition: all 180ms cubic-bezier(0.16, 1, 0.3, 1);
            user-select: none;
        }

        .sipetron-filter-trigger:hover {
            background-color: #F8FAFC;
            border-color: #CBD5E1;
            color: #0F172A;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .sipetron-filter-trigger-active {
            background-color: #FFFFFF !important;
            border-color: #0F3D2E !important;
            color: #0F3D2E !important;
            box-shadow: 0 0 0 3px rgba(15, 61, 46, 0.08) !important;
        }

        .sipetron-filter-icon {
            width: 0.875rem;
            height: 0.875rem;
            color: #94A3B8;
            flex-shrink: 0;
            transition: color 150ms ease;
        }

        .sipetron-filter-trigger:hover .sipetron-filter-icon,
        .sipetron-filter-trigger-active .sipetron-filter-icon {
            color: #0F3D2E;
        }

        .sipetron-filter-label {
            font-size: 0.71875rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .sipetron-filter-chevron {
            width: 0.8rem;
            height: 0.8rem;
            color: #64748B;
            flex-shrink: 0;
            transition: transform 200ms cubic-bezier(0.16, 1, 0.3, 1), color 150ms ease;
        }

        .sipetron-filter-trigger:hover .sipetron-filter-chevron {
            color: #0F172A;
        }

        .sipetron-chevron-rotate {
            transform: rotate(180deg);
            color: #0F3D2E !important;
        }

        .sipetron-filter-menu {
            position: absolute;
            top: calc(100% + 6px);
            right: 0;
            min-width: 11.25rem;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 0.75rem;
            box-shadow: 0 12px 28px -4px rgba(15, 61, 46, 0.14), 0 4px 10px -2px rgba(0, 0, 0, 0.05);
            padding: 0.375rem;
            z-index: 100;
            overflow: hidden;
        }

        .sipetron-filter-menu-label {
            padding: 0.35rem 0.65rem 0.25rem;
            font-size: 0.625rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #94A3B8;
        }

        .sipetron-filter-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            width: 100%;
            padding: 0.45rem 0.65rem;
            border-radius: 0.5rem;
            border: none;
            background: transparent;
            font-size: 0.75rem;
            font-weight: 500;
            color: #334155;
            cursor: pointer;
            text-align: left;
            transition: background-color 140ms ease, color 140ms ease;
            box-sizing: border-box;
        }

        .sipetron-filter-item:hover {
            background-color: #F8FAF9;
            color: #0F3D2E;
        }

        .sipetron-filter-item-active {
            background-color: #E8F3EC !important;
            color: #0F3D2E !important;
            font-weight: 700 !important;
        }

        .sipetron-filter-check-icon {
            width: 0.9rem;
            height: 0.9rem;
            color: #16A34A;
            flex-shrink: 0;
        }

        /* Widget Jadwal Tayang Terdekat Styling */
        .sipetron-broadcast-item {
            cursor: pointer !important;
            transition: transform 180ms cubic-bezier(0.16, 1, 0.3, 1), background-color 150ms ease, border-color 150ms ease, box-shadow 150ms ease !important;
        }

        .sipetron-broadcast-item:hover {
            transform: translateY(-2px);
            background-color: #FFFFFF !important;
            border-color: #86EFAC !important;
            box-shadow: 0 6px 16px -2px rgba(15, 61, 46, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
        }

        .sipetron-broadcast-item:hover .sipetron-broadcast-title {
            color: #0F3D2E !important;
        }

        .sipetron-broadcast-item:active {
            transform: translateY(0) scale(0.995);
        }

        .sipetron-btn-link:hover {
            background-color: #E8F3EC !important;
            color: #0F3D2E !important;
        }

        /* Fallback Legacy Select */
        .sipetron-filter-select {
            appearance: none;
            -webkit-appearance: none;
            background-color: #FFFFFF;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.1rem 1.1rem;
            border: 1px solid #E2E8F0;
            border-radius: 0.5rem;
            padding: 0.35rem 1.75rem 0.35rem 0.65rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #334155;
            line-height: 1.25;
            cursor: pointer;
            transition: border-color 150ms ease, box-shadow 150ms ease;
            outline: none !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }

        .sipetron-filter-select:hover {
            border-color: #CBD5E1;
            background-color: #F8FAFC;
        }

        .sipetron-filter-select:focus {
            outline: none !important;
            border-color: #94A3B8 !important;
            box-shadow: 0 0 0 1.5px #CBD5E1 !important;
        }

        .sipetron-grid-charts .fi-section,
        .sipetron-grid-bottom .fi-section {
            border-radius: 1rem !important;
            border: 1px solid #E2E8F0 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02) !important;
            transition: box-shadow 200ms ease, border-color 200ms ease !important;
        }

        .sipetron-grid-charts .fi-section:hover,
        .sipetron-grid-bottom .fi-section:hover {
            box-shadow: 0 6px 18px -2px rgba(15, 61, 46, 0.08), 0 2px 6px -1px rgba(0, 0, 0, 0.04) !important;
            transform: none !important;
        }

        /* Chip Daftar Titik Layar & Widget Kondisi Perangkat Micro-Animations */
        .sipetron-header-device-icon {
            transition: transform 250ms cubic-bezier(0.34, 1.56, 0.64, 1), background-color 200ms ease;
        }
        .sipetron-card-box:hover .sipetron-header-device-icon {
            transform: scale(1.06) rotate(-2deg);
        }

        /* 2 Big Indicator Boxes */
        .sipetron-indicator-card {
            cursor: default;
            user-select: none;
            transition: transform 220ms cubic-bezier(0.16, 1, 0.3, 1), box-shadow 220ms ease, border-color 220ms ease, background-color 220ms ease;
        }
        .sipetron-indicator-card:hover {
            transform: translateY(-2px);
        }
        .sipetron-indicator-card-total:hover {
            border-color: #CBD5E1 !important;
            background-color: #FFFFFF !important;
            box-shadow: 0 6px 16px -2px rgba(15, 23, 42, 0.06), 0 2px 6px -1px rgba(0, 0, 0, 0.03) !important;
        }
        .sipetron-indicator-card-status-normal:hover {
            border-color: #86EFAC !important;
            background-color: #F7FEFA !important;
            box-shadow: 0 6px 16px -2px rgba(22, 163, 74, 0.12), 0 2px 6px -1px rgba(0, 0, 0, 0.03) !important;
        }
        .sipetron-indicator-card-status-troubled:hover {
            border-color: #FCA5A5 !important;
            background-color: #FFF5F5 !important;
            box-shadow: 0 6px 16px -2px rgba(220, 38, 38, 0.12), 0 2px 6px -1px rgba(0, 0, 0, 0.03) !important;
        }
        .sipetron-indicator-icon {
            transition: transform 220ms cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .sipetron-indicator-card:hover .sipetron-indicator-icon {
            transform: scale(1.08);
        }

        /* 4 Status Breakdown Pills */
        .sipetron-status-pill {
            text-align: center;
            min-width: 0;
            border-radius: 0.5rem;
            padding: 0.375rem 0.25rem;
            transition: transform 200ms cubic-bezier(0.16, 1, 0.3, 1), background-color 200ms ease, box-shadow 200ms ease, border-color 200ms ease;
            cursor: default;
            border: 1px solid transparent;
        }
        .sipetron-status-pill:hover {
            transform: translateY(-2px);
            background-color: #FFFFFF;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
        }
        .sipetron-status-pill-active:hover {
            border-color: #BBF7D0;
        }
        .sipetron-status-pill-repair:hover {
            border-color: #FDE68A;
        }
        .sipetron-status-pill-issue:hover {
            border-color: #FECACA;
        }
        .sipetron-status-pill-inactive:hover {
            border-color: #E2E8F0;
        }
        .sipetron-status-pill-val {
            transition: transform 200ms cubic-bezier(0.34, 1.56, 0.64, 1);
            display: inline-block;
        }
        .sipetron-status-pill:hover .sipetron-status-pill-val {
            transform: scale(1.15);
        }

        /* Chip Daftar Titik Layar Styling */
        .sipetron-device-chip {
            display: flex;
            align-items: flex-start;
            gap: 0.45rem;
            background: #F8FAF9;
            border: 1px solid #E2E8F0;
            border-radius: 0.5rem;
            padding: 0.35rem 0.55rem;
            min-width: 0;
            width: 100%;
            min-height: 2.75rem;
            height: 100%;
            box-sizing: border-box;
            transition: background-color 200ms ease, border-color 200ms ease, box-shadow 200ms ease;
            cursor: pointer;
            text-decoration: none;
        }

        .sipetron-device-chip:hover {
            transform: none;
            background-color: #FFFFFF !important;
            border-color: #86EFAC !important;
            box-shadow: 0 4px 12px -2px rgba(22, 163, 74, 0.12), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
        }

        .sipetron-chip-dot {
            width: 0.3125rem;
            height: 0.3125rem;
            border-radius: 9999px;
            flex-shrink: 0;
            margin-top: 0.28rem;
            display: inline-block;
            transition: transform 220ms cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .sipetron-device-chip:hover .sipetron-chip-dot {
            transform: scale(1.4);
        }

        .sipetron-chip-name {
            font-size: 0.71875rem;
            font-weight: 700;
            color: #0F172A;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.15;
            transition: color 180ms ease;
        }

        .sipetron-device-chip:hover .sipetron-chip-name {
            color: #0F3D2E !important;
        }

        .sipetron-chip-loc {
            font-size: 0.625rem;
            color: #475569;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.15;
            margin-top: 0.125rem;
        }

        /* Layar Kecil (< 1024px): Semua Card Menjadi 1 Kolom */
        @media (max-width: 1023px) {
            .sipetron-grid-stats {
                grid-template-columns: 1fr;
            }
            .sipetron-grid-charts {
                grid-template-columns: 1fr;
            }
            .sipetron-grid-bottom {
                grid-template-columns: 1fr;
            }
        }
        .sipetron-takedown-bar {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 0.875rem;
            padding: 0.85rem 1.15rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.85rem;
            flex-wrap: wrap;
            box-sizing: border-box;
            width: 100%;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            transition: transform 200ms ease, box-shadow 200ms ease, border-color 200ms ease;
        }

        .sipetron-takedown-bar:hover {
            transform: translateY(-2px);
            border-color: #86EFAC;
            box-shadow: 0 10px 20px -3px rgba(15, 61, 46, 0.10), 0 4px 6px -2px rgba(15, 61, 46, 0.04);
        }

        .sipetron-takedown-bar:hover .arrow-right {
            transform: translateX(3px);
        }
    </style>
