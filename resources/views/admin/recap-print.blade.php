<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekapitulasi Videotron - E-VITRON UIN SSC</title>
    <link rel="icon" type="image/png" href="{{ asset('images/sipetron-icon.png') }}">
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 12mm 12mm 12mm;
        }

        * {
            box-sizing: border-box;
            font-family: 'Times New Roman', Times, serif, system-ui;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #F1F5F9;
            color: #111827;
            font-size: 11pt;
            line-height: 1.35;
        }

        /* Screen Header Bar */
        .no-print-bar {
            background: #0F3D2E;
            color: #FFFFFF;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
            position: sticky;
            top: 0;
            z-index: 999;
            font-family: system-ui, -apple-system, sans-serif;
        }

        .no-print-bar .title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 600;
        }

        .no-print-bar .actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-print {
            background-color: #D9A441;
            color: #0F3D2E;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 150ms ease;
        }

        .btn-print:hover {
            background-color: #C28E31;
        }

        .btn-back {
            background-color: rgba(255, 255, 255, 0.15);
            color: #FFFFFF;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 150ms ease;
        }

        .btn-back:hover {
            background-color: rgba(255, 255, 255, 0.25);
        }

        /* Sheet Container */
        .sheet-container {
            max-width: 1100px;
            margin: 24px auto;
            background: #FFFFFF;
            padding: 32px 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-radius: 4px;
        }

        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        .kop-logo {
            width: 90px;
            vertical-align: middle;
            text-align: center;
            padding-right: 15px;
        }

        .kop-logo img {
            width: 80px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .kop-text {
            text-align: center;
            vertical-align: middle;
        }

        .kop-line-1 {
            font-size: 12pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin: 0;
            text-transform: uppercase;
        }

        .kop-line-2 {
            font-size: 14pt;
            font-weight: 900;
            color: #0F3D2E;
            margin: 1px 0;
            text-transform: uppercase;
        }

        .kop-line-3 {
            font-size: 12pt;
            font-weight: bold;
            margin: 1px 0;
            text-transform: uppercase;
        }

        .kop-line-address {
            font-size: 9pt;
            margin: 2px 0 0 0;
            color: #374151;
        }

        .kop-line-web {
            font-size: 9pt;
            font-style: italic;
            margin: 1px 0 0 0;
            color: #374151;
        }

        .kop-divider {
            border-top: 2.5px solid #000000;
            border-bottom: 1px solid #000000;
            height: 3px;
            margin: 8px 0 16px 0;
        }

        /* Report Header */
        .report-header {
            text-align: center;
            margin-bottom: 16px;
        }

        .report-title {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 4px 0;
        }

        .report-subtitle {
            font-size: 9.5pt;
            color: #4B5563;
            margin: 0;
        }

        /* Summary Box */
        .summary-box {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
            padding: 8px 16px;
            background-color: #F8FAF9;
            border: 1px solid #E2E8F0;
            border-radius: 6px;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 10pt;
        }

        .summary-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .summary-label {
            color: #64748B;
        }

        .summary-val {
            font-weight: 700;
            color: #0F3D2E;
        }

        /* Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 24px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #94A3B8;
            padding: 6px 8px;
            vertical-align: middle;
        }

        .data-table th {
            background-color: #F1F5F9;
            color: #0F3D2E;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 8.5pt;
            letter-spacing: 0.3px;
        }

        .data-table tr:nth-child(even) td {
            background-color: #FAFAFA;
        }

        .data-table .col-center {
            text-align: center;
        }

        .badge-status {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: bold;
            font-family: system-ui, sans-serif;
            text-align: center;
        }

        .badge-selesai {
            background-color: #ECFDF5;
            color: #065F46;
            border: 1px solid #A7F3D0;
        }

        .badge-ditolak {
            background-color: #FEF2F2;
            color: #991B1B;
            border: 1px solid #FECACA;
        }

        .badge-dibatalkan {
            background-color: #F3F4F6;
            color: #4B5563;
            border: 1px solid #E5E7EB;
        }

        /* Signatures Section */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
            margin-top: 10px;
        }

        .signature-col {
            width: 45%;
            vertical-align: top;
            text-align: center;
            font-size: 10pt;
        }

        .signature-space {
            height: 60px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        .signature-nip {
            font-size: 9pt;
            color: #374151;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #FFFFFF !important;
                color: #000000 !important;
            }

            .no-print-bar {
                display: none !important;
            }

            .sheet-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            .summary-box {
                background: #FFFFFF !important;
                border: 1px solid #000000 !important;
            }

            .data-table th {
                background-color: #EEEEEE !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .data-table tr:nth-child(even) td {
                background-color: transparent !important;
            }

            .badge-status {
                border: 1px solid #000000 !important;
                background: transparent !important;
                color: #000000 !important;
            }
        }
    </style>
</head>
<body>

    {{-- Screen Floating Action Bar (Hidden when printed) --}}
    <div class="no-print-bar">
        <div class="title">
            <img src="{{ asset('images/sipetron-white-logo.png') }}" alt="E-VITRON" style="height: 24px;">
            <span>Pratinjau Cetak Laporan Rekapitulasi Videotron</span>
        </div>
        <div class="actions">
            <span style="font-size: 12px; opacity: 0.85; margin-right: 8px;">Tip: Pilih kertas A4 Landscape pada dialog cetak</span>
            <button onclick="window.print()" class="btn-print">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                Cetak / Simpan PDF
            </button>
            <a href="/admin/history-and-recap" class="btn-back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    {{-- Document Sheet --}}
    <div class="sheet-container">
        
        {{-- Kop Surat Resmi (Dipakai Bersama) --}}
        @include('admin.partials.kop-surat')

        {{-- Judul Laporan --}}
        <div class="report-header">
            <h1 class="report-title">LAPORAN REKAPITULASI PENAYANGAN INFORMASI VIDEOTRON</h1>
            <p class="report-subtitle">
                @if($filterStatus)
                    @php
                        $printStatusLabel = match ($filterStatus) {
                            'perlu_diturunkan' => 'Perlu Diturunkan',
                            'selesai' => 'Selesai Tayang',
                            'ditolak' => 'Ditolak Admin',
                            'dibatalkan' => 'Dibatalkan Pengaju',
                            default => ucfirst((string) $filterStatus),
                        };
                    @endphp
                    Kategori Status: <strong>{{ $printStatusLabel }}</strong> &bull;
                @else
                    Kategori: <strong>Seluruh Arsip Riwayat</strong> &bull;
                @endif
                Dicetak pada: {{ now()->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB
            </p>
        </div>

        {{-- Ringkasan Rekap (Summary Strip) --}}
        @if(!empty($isTruncated))
            <div style="margin-bottom: 12px; padding: 9px 14px; background-color: #FFFBEB; border: 1px solid #F59E0B; border-radius: 6px; color: #92400E; font-family: system-ui, -apple-system, sans-serif; font-size: 9.5pt;">
                <strong>Catatan Batas Cetak:</strong> Ditemukan <strong>{{ $totalMatched }}</strong> arsip pada filter ini. Demi menjaga kecepatan cetak, pratinjau menampilkan <strong>{{ $maxExportRows ?? 500 }}</strong> data terbaru. Gunakan filter rentang tanggal agar seluruh data tercetak lengkap.
            </div>
        @endif
        <div class="summary-box">
            <div class="summary-item">
                <span class="summary-label">Total Arsip Pengajuan:</span>
                <span class="summary-val">{{ $totalArchived }} Data</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Selesai Tayang:</span>
                <span class="summary-val" style="color: #065F46;">{{ $totalCompleted }} Pengajuan</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Ditolak Admin:</span>
                <span class="summary-val" style="color: #991B1B;">{{ $totalRejected }} Pengajuan</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Dibatalkan Pengaju:</span>
                <span class="summary-val" style="color: #4B5563;">{{ $totalCanceled }} Pengajuan</span>
            </div>
        </div>

        {{-- Tabel Data Riwayat --}}
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 35px;">No</th>
                    <th style="width: 110px;">Kode Tiket</th>
                    <th>Judul Kegiatan & Deskripsi</th>
                    <th style="width: 140px;">Pengaju / Unit</th>
                    <th style="width: 130px;">Videotron Dituju</th>
                    <th style="width: 140px;">Periode Tayang</th>
                    <th style="width: 60px;">Durasi</th>
                    <th style="width: 95px;">Status Akhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($submissions as $index => $sub)
                    @php
                        $duration = ($sub->start_date && $sub->end_date)
                            ? $sub->start_date->diffInDays($sub->end_date) + 1
                            : 0;
                        $videotrons = $sub->videotrons->pluck('name')->join(', ');
                        $statusClass = match($sub->display_status) {
                            'selesai' => 'badge-selesai',
                            'ditolak' => 'badge-ditolak',
                            'dibatalkan' => 'badge-dibatalkan',
                            default => 'badge-dibatalkan',
                        };
                        $statusLabel = match($sub->display_status) {
                            'selesai' => 'Selesai',
                            'ditolak' => 'Ditolak',
                            'dibatalkan' => 'Dibatalkan',
                            default => ucfirst($sub->display_status),
                        };
                    @endphp
                    <tr>
                        <td class="col-center">{{ $index + 1 }}</td>
                        <td class="col-center" style="font-weight: bold; font-family: monospace; font-size: 9pt;">
                            {{ $sub->ticket_code }}
                        </td>
                        <td>
                            <strong>{{ $sub->title }}</strong>
                            @if($sub->admin_notes && $sub->display_status === 'ditolak')
                                <div style="font-size: 8pt; color: #991B1B; margin-top: 2px;">
                                    <em>Alasan: {{ $sub->admin_notes }}</em>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div>{{ $sub->user?->name ?? $sub->pic_name }}</div>
                            <div style="font-size: 8pt; color: #64748B;">{{ $sub->organization ?? 'Umum/Mahasiswa' }}</div>
                        </td>
                        <td style="font-size: 8.5pt;">
                            {{ $videotrons ?: 'Semua Layar' }}
                        </td>
                        <td class="col-center" style="font-size: 8.5pt;">
                            @if($sub->start_date && $sub->end_date)
                                {{ $sub->start_date->format('d/m/Y') }} - {{ $sub->end_date->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="col-center" style="font-size: 8.5pt;">
                            {{ $duration > 0 ? $duration . ' hari' : '-' }}
                        </td>
                        <td class="col-center">
                            <span class="badge-status {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="col-center" style="padding: 20px; color: #6B7280;">
                            Tidak ada data arsip pengajuan yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Lembar Pengesahan / Tanda Tangan (Opsi A: Tunggal Kanan) --}}
        @php
            $sig = config('sipetron.signatory', []);
            $adminTitle = setting('signatory_admin_title', $sig['admin_title'] ?? 'Tim Pengelola E-VITRON');
            $adminUnit = setting('signatory_admin_unit', $sig['admin_unit'] ?? 'UIN Siber Syekh Nurjati Cirebon');
            $adminName = 'Tim Admin E-VITRON';
        @endphp
        <table class="signature-table">
            <tr>
                <td style="width: 52%;"></td>
                <td class="signature-col" style="width: 48%;">
                    <p style="margin: 0;">Cirebon, {{ now()->timezone('Asia/Jakarta')->translatedFormat('d F Y') }}</p>
                    <p style="margin: 0; font-weight: bold;">{{ $adminTitle }}</p>
                    <p style="margin: 0; font-size: 9pt; color: #4B5563;">{{ $adminUnit }}</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">{{ $adminName }}</p>
                </td>
            </tr>
        </table>

    </div>

</body>
</html>
