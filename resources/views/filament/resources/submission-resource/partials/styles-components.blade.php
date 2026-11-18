{{-- Bagian 2: Stylesheet Form, Tabel, Toolbar Filter & Pagination (Dipanggil oleh list-submissions.blade.php) --}}
    <style>
        .sipetron-modal-textarea {
            width: 100%;
            padding: 0.6rem 0.7rem;
            border: 1px solid #CBD5E1;
            background: #FFFFFF;
            border-radius: 0.5rem;
            font-size: 0.8125rem;
            color: #1E293B;
            outline: none;
            box-sizing: border-box;
            transition: border-color 150ms ease, box-shadow 150ms ease, background-color 150ms ease;
        }
        .sipetron-modal-textarea:focus {
            border-color: #0F3D2E;
            box-shadow: 0 0 0 3px rgba(15, 61, 46, 0.1);
        }
        .sipetron-modal-textarea.is-invalid,
        .sipetron-modal-textarea.is-invalid:focus {
            border-color: #EF4444 !important;
            background: #FFFBFB !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) !important;
        }
        .sipetron-modal-inline-error {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            margin-top: 0.4rem;
            padding: 0.45rem 0.65rem;
            border-radius: 0.5rem;
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #B91C1C;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1.35;
        }

        @media (max-width: 640px) {
            .sipetron-kv-grid { grid-template-columns: minmax(0, 1fr) !important; gap: 0.15rem !important; }
            .sipetron-kv-grid > div:nth-child(odd) { margin-top: 0.4rem; }
        }
        /* Per Page Custom Dropup */
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
            background-color: #F8FAF9;
        }
        .sipetron-perpage-trigger:focus,
        .sipetron-perpage-trigger.is-open {
            border-color: #0F3D2E !important;
            box-shadow: 0 0 0 3px rgba(15, 61, 46, 0.12) !important;
        }
        .sipetron-perpage-menu {
            position: absolute;
            bottom: calc(100% + 6px);
            left: 0;
            min-width: 110px;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            padding: 4px;
            z-index: 50;
            box-sizing: border-box;
        }
        .sipetron-perpage-item {
            height: 30px;
            box-sizing: border-box;
            padding: 0 8px;
            border-radius: 6px;
            border: none;
            background: transparent;
            color: #1E293B;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            text-align: left;
            transition: background-color 150ms ease, color 150ms ease;
        }
        .sipetron-perpage-item:hover:not(.is-selected) {
            background-color: #F3F6F4 !important;
        }
        .sipetron-perpage-item.is-selected {
            background-color: #E8F3EC !important;
            color: #0F3D2E !important;
            font-weight: 700;
        }

        /* Modern Custom Pagination Styling */
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

        /* Dropdown Trigger & Search Input per specs */
        .sipetron-dropdown-trigger {
            height: 42px;
            box-sizing: border-box;
            padding: 0 14px;
            border-radius: 10px;
            border: 1px solid #E2E8F0;
            background: #FFFFFF;
            font-size: 14px;
            color: #1E293B;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            width: 100%;
            outline: none;
            transition: border-color 150ms ease, box-shadow 150ms ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
        }
        .sipetron-dropdown-trigger:focus,
        .sipetron-dropdown-trigger.is-open {
            border-color: #0F3D2E !important;
            box-shadow: 0 0 0 3px rgba(15, 61, 46, 0.15) !important;
        }

        .sipetron-search-input {
            height: 42px;
            box-sizing: border-box;
            width: 100%;
            padding: 0 2.25rem 0 2.6rem;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            font-size: 14px;
            color: #1E293B;
            outline: none;
            transition: border-color 150ms ease, box-shadow 150ms ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
        }
        .sipetron-search-input:focus {
            border-color: #0F3D2E !important;
            box-shadow: 0 0 0 3px rgba(15, 61, 46, 0.15) !important;
        }

        /* Dropdown Panel */
        .sipetron-dropdown-panel {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            width: 100%;
            min-width: max(100%, 250px);
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            padding: 6px;
            z-index: 60;
            max-height: min(460px, calc(100vh - 120px));
            overflow-y: auto;
            box-sizing: border-box;
        }
        .sipetron-dropdown-panel-right {
            right: 0 !important;
            left: auto !important;
        }

        .sipetron-dropdown-panel-header {
            padding: 6px 12px 8px 12px;
            font-size: 11px;
            font-weight: 700;
            color: #94A3B8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #F1F5F9;
            margin-bottom: 4px;
        }

        /* Dropdown Item */
        .sipetron-dropdown-item {
            height: 40px;
            min-height: 40px;
            box-sizing: border-box;
            padding: 0 12px;
            border-radius: 8px;
            border: none;
            background: transparent;
            color: #1E293B;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            text-align: left;
            transition: background-color 150ms ease, color 150ms ease;
        }
        .sipetron-dropdown-item:hover:not(.is-selected) {
            background-color: #F3F6F4 !important;
        }
        .sipetron-dropdown-item.is-selected {
            background-color: #E8F3EC !important;
            color: #0F3D2E !important;
            font-weight: 600;
        }
        .sipetron-dropdown-item.is-selected:hover {
            background-color: #DCF0E4 !important;
        }

        /* Dropdown Badge */
        .sipetron-dropdown-badge {
            min-width: 24px;
            height: 22px;
            padding: 0 6px;
            box-sizing: border-box;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
            background: #F1F5F9;
            color: #64748B;
            flex-shrink: 0;
            line-height: 1;
        }
        .sipetron-dropdown-item.is-selected .sipetron-dropdown-badge {
            background: #C4DED0;
            color: #0F3D2E;
        }

        /* Reset Button (Sama Persis dengan Riwayat & Rekap) */
        .sipetron-reset-btn {
            height: 42px;
            padding: 0 16px;
            border-radius: 10px;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            color: #64748B;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 150ms ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
            white-space: nowrap;
        }
        .sipetron-reset-btn:hover {
            border-color: #CBD5E1;
            background: #F8FAFC;
            color: #334155;
        }
        .sipetron-reset-btn.is-active {
            background: #FEF2F2;
            border-color: #FECACA;
            color: #DC2626;
        }
        .sipetron-reset-btn.is-active:hover {
            background: #FEE2E2;
            border-color: #F87171;
            color: #B91C1C;
        }

        /* Instant Table Container & Loading State (Tanpa Delay & Tanpa Efek Memudar) */
        .sipetron-table-wrap {
            transition: none !important;
        }
        .sipetron-table-wrap.sipetron-loading-state {
            opacity: 1 !important;
            pointer-events: auto !important;
        }

        @media (prefers-reduced-motion: reduce) {
            .sipetron-dropdown-panel,
            .sipetron-dropdown-trigger svg,
            .sipetron-dropdown-trigger div svg,
            .sipetron-table-wrap {
                transition: none !important;
                transform: none !important;
                animation: none !important;
            }
        }
    </style>
