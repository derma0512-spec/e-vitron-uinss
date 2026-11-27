{{-- Bagian 1: Stylesheet Toolbar Filter, Dropdown & Kalender Arsip (Dipanggil oleh history-and-recap.blade.php) --}}
    <style>
        /* ========================================================
           E-VITRON DESIGN SYSTEM: HARMONISASI DENGAN PENGAJUAN & VIDEOTRON
           ======================================================== */
        .fi-history-recap-page section,
        .fi-page:has(.sipetron-history-container) section {
            padding-top: 0.25rem !important;
            padding-bottom: 2rem !important;
            gap: 1.125rem !important;
        }

        .fi-history-recap-page section > div.grid,
        .fi-page:has(.sipetron-history-container) section > div.grid {
            gap: 1.125rem !important;
        }

        .fi-history-recap-page .fi-header,
        .fi-page:has(.sipetron-history-container) .fi-header {
            margin-bottom: 0.25rem !important;
            padding-bottom: 0 !important;
        }

        /* --------------------------------------------------------
           1. TOOLBAR FILTER MENGAMBANG (SAMA PERSIS DENGAN PENGAJUAN)
           -------------------------------------------------------- */
        .sipetron-filter-toolbar {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            align-items: center;
            width: 100%;
        }

        /* Search Box Wrap */
        .sipetron-search-wrap {
            flex: 1 1 280px;
            min-width: 0;
            position: relative;
        }

        .sipetron-search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            pointer-events: none;
            display: flex;
            align-items: center;
            z-index: 2;
        }

        .sipetron-search-input {
            height: 42px;
            box-sizing: border-box;
            width: 100%;
            padding: 0 2.25rem 0 2.6rem;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            font-size: 13.5px;
            color: #1E293B;
            outline: none;
            transition: border-color 150ms ease, box-shadow 150ms ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
        }

        .sipetron-search-input::placeholder {
            color: #94A3B8;
        }

        .sipetron-search-input:focus {
            border-color: #0F3D2E !important;
            box-shadow: 0 0 0 3px rgba(15, 61, 46, 0.12) !important;
        }

        .sipetron-search-clear {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 22px;
            height: 22px;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94A3B8;
            background: #F1F5F9;
            border: none;
            cursor: pointer;
            transition: all 150ms ease;
        }

        .sipetron-search-clear:hover {
            background: #E2E8F0;
            color: #334155;
        }

        /* Status & Period Dropdown Triggers */
        .sipetron-dropdown-wrap {
            flex: 0 1 200px;
            min-width: 175px;
            position: relative;
        }

        .sipetron-dropdown-wrap-wide {
            flex: 0 1 250px;
            min-width: 220px;
        }

        .sipetron-dropdown-trigger {
            height: 42px;
            box-sizing: border-box;
            padding: 0 14px;
            border-radius: 10px;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            font-size: 14px;
            font-weight: 500;
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
            user-select: none;
        }

        .sipetron-dropdown-trigger:hover {
            border-color: #CBD5E1;
            background-color: #F8FAF9;
        }

        .sipetron-dropdown-trigger:focus,
        .sipetron-dropdown-trigger.is-open {
            border-color: #0F3D2E !important;
            box-shadow: 0 0 0 3px rgba(15, 61, 46, 0.15) !important;
        }

        .sipetron-dropdown-trigger.is-active {
            border-color: #0F3D2E !important;
            color: #0F3D2E !important;
            font-weight: 600;
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
            box-sizing: border-box;
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

        /* Reset Button */
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

        /* --------------------------------------------------------
           2. CARD BOX TABEL DENGAN TOMBOL EKSPOR DI KANAN
           -------------------------------------------------------- */
        .sipetron-card-box {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            padding: 1.25rem 1.5rem;
            box-sizing: border-box;
            width: 100%;
        }

        .sipetron-table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.875rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #F1F5F9;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .sipetron-table-title-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .sipetron-table-title {
            font-size: 0.9375rem;
            font-weight: 700;
            color: #0F172A;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .sipetron-badge-green {
            display: inline-flex;
            align-items: center;
            padding: 0.2rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            background-color: #E8F3EC;
            color: #0F3D2E;
            border: 1px solid #D2E7DC;
        }

        .sipetron-badge-subtle {
            display: inline-flex;
            align-items: center;
            padding: 0.2rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            background-color: #F1F5F9;
            color: #475569;
            border: 1px solid #E2E8F0;
        }

        @keyframes sipetron-ping {
            75%, 100% {
                transform: scale(2.2);
                opacity: 0;
            }
        }

        .sipetron-takedown-pill {
            font-size: 0.6875rem;
            font-weight: 700;
            color: #B45309;
            background: #FFFBEB;
            border: 1px solid #FDE68A;
            padding: 0.15rem 0.6rem;
            border-radius: 9999px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 150ms ease;
            box-shadow: 0 1px 2px rgba(217, 119, 6, 0.08);
        }

        .sipetron-takedown-pill.is-active {
            color: #92400E;
            background: #FEF3C7;
            border-color: #F59E0B;
        }

        .sipetron-takedown-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.38rem;
            padding: 0.3rem 0.72rem;
            border-radius: 9999px;
            background-color: #FFFFFF;
            border: 1px solid #CBD5E1;
            color: #0F3D2E;
            font-size: 0.71875rem;
            font-weight: 600;
            line-height: 1;
            cursor: pointer;
            transition: background-color 150ms ease, border-color 150ms ease, color 150ms ease, box-shadow 150ms ease;
            white-space: nowrap;
        }

        .sipetron-takedown-btn:hover,
        .sipetron-takedown-btn:focus-visible {
            background-color: #F8FAF9;
            border-color: #0F3D2E;
            color: #0F3D2E;
            outline: none;
        }

        .sipetron-takedown-btn:disabled {
            opacity: 0.65;
            cursor: wait;
        }

        .sipetron-takedown-checkbox {
            width: 12px;
            height: 12px;
            border-radius: 3px;
            border: 1.5px solid #94A3B8;
            background: transparent;
            display: inline-block;
            flex-shrink: 0;
            transition: border-color 150ms ease;
        }

        .sipetron-takedown-btn:hover .sipetron-takedown-checkbox,
        .sipetron-takedown-btn:focus-visible .sipetron-takedown-checkbox {
            border-color: #0F3D2E;
        }

        /* Action Buttons di Kanan Header Card */
        .sipetron-card-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-card-export-csv {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            height: 34px;
            padding: 0 0.85rem;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            color: #0F3D2E;
            background: #FFFFFF;
            border: 1px solid #CBD5E1;
            cursor: pointer;
            transition: all 150ms ease;
            text-decoration: none;
            box-sizing: border-box;
            user-select: none;
        }

        .btn-card-export-csv:hover {
            background: #F8FAF9;
            border-color: #0F3D2E;
            color: #0F3D2E;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(15, 61, 46, 0.08);
        }

        .btn-card-export-csv:active {
            transform: translateY(0);
        }

        .btn-card-print-pdf {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            height: 34px;
            padding: 0 0.95rem;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            color: #FFFFFF;
            background: #0F3D2E;
            border: 1px solid #0F3D2E;
            cursor: pointer;
            transition: all 150ms ease;
            text-decoration: none;
            box-shadow: 0 1px 2px rgba(15, 61, 46, 0.15);
            box-sizing: border-box;
            user-select: none;
        }

        .btn-card-print-pdf:hover {
            background: #185340;
            border-color: #185340;
            color: #FFFFFF;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(15, 61, 46, 0.25);
        }

        .btn-card-print-pdf:active {
            transform: translateY(0);
        }

        /* Sembunyikan header tabel & toolbar bawaan Filament */
        .sipetron-card-box .fi-ta-header,
        .sipetron-card-box .fi-ta-header-toolbar,
        .sipetron-card-box .fi-ta-search-field,
        .sipetron-card-box .fi-ta-filter-indicators,
        .sipetron-card-box div:has(> .fi-ta-search-field),
        .sipetron-card-box div:has(> .fi-ta-header-toolbar),
        .sipetron-card-box .fi-input-wrp:has(input.sipetron-search-input) {
            display: none !important;
        }

        .sipetron-card-box .fi-ta-ctn {
            border: none !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            background: transparent !important;
        }

        /* Active Bookmark Left Border on History Table */
        body:has(.fi-modal.fi-modal-open) .fi-ta-table tbody tr.videotron-row-active > td:first-child {
            box-shadow: inset 4px 0 0 #0F3D2E !important;
            background-color: #F8FAF9 !important;
        }

        body:has(.fi-modal.fi-modal-open) .fi-ta-table tbody tr.videotron-row-active > td {
            background-color: #F8FAF9 !important;
        }

        /* Month & Year Picker Section inside Popover */
        /* Form Periode Dropdown Panel (Sesuai Spesifikasi Baru) */
        .sipetron-period-form {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 4px 6px 6px 6px;
        }

        .sipetron-period-field {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .sipetron-period-label {
            font-size: 11px;
            font-weight: 700;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .sipetron-period-select {
            height: 38px;
            width: 100%;
            padding: 0 10px;
            background-color: #FFFFFF;
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            color: #0F172A;
            outline: none;
            cursor: pointer;
            transition: border-color 150ms ease, box-shadow 150ms ease, background-color 150ms ease;
            box-sizing: border-box;
        }

        .sipetron-period-select:focus {
            border-color: #0F3D2E !important;
            box-shadow: 0 0 0 3px rgba(15, 61, 46, 0.15) !important;
        }

        .sipetron-period-select:disabled {
            background-color: #F1F5F9;
            color: #94A3B8;
            cursor: not-allowed;
            border-color: #E2E8F0;
        }
    </style>
