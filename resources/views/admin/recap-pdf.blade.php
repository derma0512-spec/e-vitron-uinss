<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi Arsip Videotron - E-VITRON UIN SSC</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 13mm 14mm 14mm 14mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #FFFFFF;
            color: #111827;
            font-family: Helvetica, Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
        }

        /* 1. KOP SURAT RESMI (SIMETRIS TENGAH 100%) */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3px;
        }

        .kop-side {
            width: 60px;
            vertical-align: middle;
            text-align: center;
        }

        .kop-side img {
            width: 54px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .kop-center {
            text-align: center;
            vertical-align: middle;
            font-family: "Times New Roman", Times, serif;
            padding: 0 4px;
        }

        .kop-line-1 {
            font-size: 11pt;
            font-weight: bold;
            letter-spacing: 0.3px;
            margin: 0;
            color: #111827;
            text-transform: uppercase;
        }

        .kop-line-2 {
            font-size: 12pt;
            font-weight: bold;
            color: #111827;
            margin: 1px 0;
            text-transform: uppercase;
        }

        .kop-line-3 {
            font-size: 10pt;
            font-weight: bold;
            margin: 1px 0;
            color: #111827;
            text-transform: uppercase;
        }

        .kop-line-address {
            font-size: 8.5pt;
            margin: 2px 0 0 0;
            color: #374151;
        }

        .kop-line-web {
            font-size: 8pt;
            font-style: italic;
            margin: 1px 0 0 0;
            color: #4B5563;
        }

        .kop-divider-thick {
            border-top: 2pt solid #111827;
            margin-top: 3px;
        }

        .kop-divider-thin {
            border-top: 0.6pt solid #111827;
            margin-top: 1.5px;
            margin-bottom: 8px;
        }

        /* 2. JUDUL LAPORAN & RINGKASAN SATU BARIS */
        .report-title {
            font-size: 11pt;
            font-weight: bold;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin: 0 0 2px 0;
        }

        .report-meta {
            font-size: 8.5pt;
            color: #374151;
            margin: 0 0 2px 0;
        }

        .report-summary {
            font-size: 8.5pt;
            color: #1F2937;
            margin: 0;
        }

        /* 3. TABEL DATA DOKUMEN RESMI */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 0;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .data-table th {
            background-color: #F1F5F9;
            color: #111827;
            font-weight: bold;
            text-align: left;
            font-size: 8.5pt;
            padding: 4pt 4pt;
            border-top: 1.2pt solid #334155;
            border-bottom: 1pt solid #334155;
            vertical-align: middle;
        }

        .data-table td {
            border-bottom: 0.5pt solid #CBD5E1;
            padding: 3pt 4pt;
            vertical-align: top;
            font-size: 8.6pt;
            line-height: 1.3;
            color: #111827;
            word-wrap: break-word;
            overflow-wrap: break-word;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .data-table tbody tr.last-data-row td {
            border-bottom: 1pt solid #334155;
        }

        /* Tipografi Sel */
        .text-bold {
            font-weight: bold;
            color: #111827;
        }

        .text-ticket {
            font-weight: bold;
            font-size: 8.2pt;
            color: #111827;
            white-space: nowrap;
        }

        .text-sub {
            font-size: 8pt;
            color: #4B5563;
        }

        .text-reason {
            font-size: 8pt;
            font-style: italic;
            color: #374151;
        }

        .nowrap {
            white-space: nowrap;
        }

        /* 4. WRAPPER TAIL (2 BARIS TERAKHIR + TANDA TANGAN ANTI YATIM) */
        .tail-keep-together {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
            break-inside: avoid;
            margin-top: 20px;
        }

        .signature-table tr,
        .signature-table td {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .signature-col {
            width: 44%;
            vertical-align: top;
            text-align: center;
            font-size: 8.8pt;
            line-height: 1.3;
            color: #111827;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            color: #111827;
        }

        .signature-sub {
            font-size: 8.2pt;
            color: #374151;
        }
    </style>
</head>
<body>

    @php
        $labelSelesai = strtolower(\App\Models\Submission::getStatusLabel('selesai', 'pengaju'));
        $labelDitolak = strtolower(\App\Models\Submission::getStatusLabel('ditolak', 'pengaju'));
        $labelDibatalkan = strtolower(\App\Models\Submission::getStatusLabel('dibatalkan', 'pengaju'));
        $totalCount = $submissions->count();
        $headItems = $totalCount > 2 ? $submissions->slice(0, $totalCount - 2) : collect();
        $tailItems = $totalCount > 2 ? $submissions->slice($totalCount - 2) : $submissions;
    @endphp

    {{-- 1. KOP SURAT RESMI UIN SSC (DIPAKAI BERSAMA) --}}
    @include('admin.partials.kop-surat')

    {{-- 2. JUDUL LAPORAN & SATU BARIS RINGKASAN BERSIH --}}
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
        <tr>
            <td style="text-align: center; padding: 0;">
                <h1 class="report-title">LAPORAN REKAPITULASI ARSIP PENGAJUAN VIDEOTRON</h1>
                <p class="report-meta">
                    Periode: <strong>{{ $periodLabel }}</strong> &nbsp;&bull;&nbsp;
                    Kategori: <strong>{{ $statusLabel }}</strong>
                    @if(!empty($searchQuery))
                        &nbsp;&bull;&nbsp; Kata Kunci: <strong>"{{ $searchQuery }}"</strong>
                    @endif
                    &nbsp;&bull;&nbsp; Dicetak: <strong>{{ $exportedAt }}</strong>
                </p>
                <p class="report-summary">
                    Total <strong>{{ $totalArchived }} pengajuan</strong>:
                    <strong>{{ $totalCompleted }}</strong> {{ $labelSelesai }},
                    <strong>{{ $totalRejected }}</strong> {{ $labelDitolak }},
                    <strong>{{ $totalCanceled }}</strong> {{ $labelDibatalkan }}.
                </p>
                @if(!empty($isTruncated))
                    <p style="margin: 5px 0 0 0; padding: 4pt 7pt; background-color: #FFFBEB; border: 0.5pt solid #F59E0B; color: #92400E; font-size: 7.8pt; text-align: left;">
                        <strong>Catatan Batas Ekspor:</strong> Ditemukan <strong>{{ $totalMatched }}</strong> arsip pada filter ini. Demi stabilitas pencetakan dokumen, PDF menampilkan <strong>{{ $maxExportRows ?? 500 }}</strong> data pertama. Gunakan filter Bulan atau Tahun untuk mencetak arsip secara terpisah.
                    </p>
                @endif
            </td>
        </tr>
    </table>

    {{-- 3A. BARIS UTAMA TABEL (SEBELUM 2 BARIS TERAKHIR) --}}
    @if($headItems->isNotEmpty())
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">No</th>
                    <th style="width: 14%; padding-right: 6pt;">Kode Tiket</th>
                    <th style="width: 26%;">Kegiatan</th>
                    <th style="width: 18%;">Pengaju</th>
                    <th style="width: 11%;">Layar</th>
                    <th style="width: 16%;">Jadwal</th>
                    <th style="width: 10%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($headItems as $index => $sub)
                    @php
                        $duration = ($sub->start_date && $sub->end_date)
                            ? ($sub->start_date->diffInDays($sub->end_date) + 1)
                            : 0;
                        $videotrons = $sub->videotrons
                            ->pluck('name')
                            ->map(fn ($name) => trim((string) preg_replace('/^videotron\s+/i', '', (string) $name)))
                            ->filter()
                            ->join(', ') ?: '-';

                        $displayStatus = $sub->display_status;
                        $statusText = \App\Models\Submission::getStatusLabel($displayStatus, 'pengaju');

                        $cleanNote = trim(preg_replace('/\s+/', ' ', (string) $sub->admin_notes));
                        $reasonBody = null;
                        if ($displayStatus === 'ditolak') {
                            $reasonBody = $cleanNote !== '' ? $cleanNote : 'Tidak memenuhi ketentuan penayangan.';
                        } elseif ($displayStatus === 'dibatalkan') {
                            $reasonBody = $cleanNote !== '' ? $cleanNote : 'Dibatalkan mandiri oleh pengaju.';
                        }

                        $compactDate = '-';
                        if ($sub->start_date && $sub->end_date) {
                            $s = $sub->start_date->locale('id');
                            $e = $sub->end_date->locale('id');
                            if ($s->isSameDay($e)) {
                                $compactDate = $s->translatedFormat('d M Y');
                            } elseif ($s->year === $e->year && $s->month === $e->month) {
                                $compactDate = $s->format('d').'–'.$e->translatedFormat('d M Y');
                            } elseif ($s->year === $e->year) {
                                $compactDate = $s->translatedFormat('d M').' – '.$e->translatedFormat('d M Y');
                            } else {
                                $compactDate = $s->translatedFormat('d M Y').' – '.$e->translatedFormat('d M Y');
                            }
                        }

                        $picName = trim(preg_replace('/\s+/', ' ', (string) ($sub->user?->name ?? $sub->pic_name)));
                        $orgName = trim(preg_replace('/\s+/', ' ', (string) ($sub->organization ?: 'Umum / Mahasiswa')));
                        $titleClean = trim(preg_replace('/\s+/', ' ', (string) $sub->title));
                    @endphp
                    <tr>
                        <td style="width: 5%; text-align: center;">{{ $index + 1 }}</td>
                        <td style="width: 14%; padding-right: 6pt;"><span class="text-ticket">{{ $sub->ticket_code }}</span></td>
                        <td style="width: 26%;"><span class="text-bold">{{ $titleClean }}</span>@if($reasonBody)<br><span class="text-reason"><strong>Alasan:</strong> {{ $reasonBody }}</span>@endif</td>
                        <td style="width: 18%;"><span class="text-bold">{{ $picName }}</span><br><span class="text-sub">{{ $orgName }}</span></td>
                        <td style="width: 11%;">{{ $videotrons }}</td>
                        <td style="width: 16%;"><span class="nowrap">{{ $compactDate }}</span>@if($duration > 0)<br><span class="text-sub">({{ $duration }} hari)</span>@endif</td>
                        <td style="width: 10%;"><span class="text-bold nowrap">{{ $statusText }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- 3B & 4. WRAPPER 2 BARIS TERAKHIR + BLOK TANDA TANGAN (TIDAK AKAN TERPISAH SENDIRIAN) --}}
    <div class="tail-keep-together">
        <table class="data-table">
            @if($headItems->isEmpty())
                <thead>
                    <tr>
                        <th style="width: 5%; text-align: center;">No</th>
                        <th style="width: 14%; padding-right: 6pt;">Kode Tiket</th>
                        <th style="width: 26%;">Kegiatan</th>
                        <th style="width: 18%;">Pengaju</th>
                        <th style="width: 11%;">Layar</th>
                        <th style="width: 16%;">Jadwal</th>
                        <th style="width: 10%;">Status</th>
                    </tr>
                </thead>
            @endif
            <tbody>
                @forelse($tailItems as $index => $sub)
                    @php
                        $isLastRow = $loop->last;
                        $duration = ($sub->start_date && $sub->end_date)
                            ? ($sub->start_date->diffInDays($sub->end_date) + 1)
                            : 0;
                        $videotrons = $sub->videotrons
                            ->pluck('name')
                            ->map(fn ($name) => trim((string) preg_replace('/^videotron\s+/i', '', (string) $name)))
                            ->filter()
                            ->join(', ') ?: '-';

                        $displayStatus = $sub->display_status;
                        $statusText = \App\Models\Submission::getStatusLabel($displayStatus, 'pengaju');

                        $cleanNote = trim(preg_replace('/\s+/', ' ', (string) $sub->admin_notes));
                        $reasonBody = null;
                        if ($displayStatus === 'ditolak') {
                            $reasonBody = $cleanNote !== '' ? $cleanNote : 'Tidak memenuhi ketentuan penayangan.';
                        } elseif ($displayStatus === 'dibatalkan') {
                            $reasonBody = $cleanNote !== '' ? $cleanNote : 'Dibatalkan mandiri oleh pengaju.';
                        }

                        $compactDate = '-';
                        if ($sub->start_date && $sub->end_date) {
                            $s = $sub->start_date->locale('id');
                            $e = $sub->end_date->locale('id');
                            if ($s->isSameDay($e)) {
                                $compactDate = $s->translatedFormat('d M Y');
                            } elseif ($s->year === $e->year && $s->month === $e->month) {
                                $compactDate = $s->format('d').'–'.$e->translatedFormat('d M Y');
                            } elseif ($s->year === $e->year) {
                                $compactDate = $s->translatedFormat('d M').' – '.$e->translatedFormat('d M Y');
                            } else {
                                $compactDate = $s->translatedFormat('d M Y').' – '.$e->translatedFormat('d M Y');
                            }
                        }

                        $picName = trim(preg_replace('/\s+/', ' ', (string) ($sub->user?->name ?? $sub->pic_name)));
                        $orgName = trim(preg_replace('/\s+/', ' ', (string) ($sub->organization ?: 'Umum / Mahasiswa')));
                        $titleClean = trim(preg_replace('/\s+/', ' ', (string) $sub->title));
                    @endphp
                    <tr class="{{ $isLastRow ? 'last-data-row' : '' }}">
                        <td style="width: 5%; text-align: center;">{{ $index + 1 }}</td>
                        <td style="width: 14%; padding-right: 6pt;"><span class="text-ticket">{{ $sub->ticket_code }}</span></td>
                        <td style="width: 26%;"><span class="text-bold">{{ $titleClean }}</span>@if($reasonBody)<br><span class="text-reason"><strong>Alasan:</strong> {{ $reasonBody }}</span>@endif</td>
                        <td style="width: 18%;"><span class="text-bold">{{ $picName }}</span><br><span class="text-sub">{{ $orgName }}</span></td>
                        <td style="width: 11%;">{{ $videotrons }}</td>
                        <td style="width: 16%;"><span class="nowrap">{{ $compactDate }}</span>@if($duration > 0)<br><span class="text-sub">({{ $duration }} hari)</span>@endif</td>
                        <td style="width: 10%;"><span class="text-bold nowrap">{{ $statusText }}</span></td>
                    </tr>
                @empty
                    <tr class="last-data-row">
                        <td colspan="7" style="text-align: center; padding: 14px; color: #4B5563;">
                            Tidak ada data arsip pengajuan videotron pada filter ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- 4. LEMBAR PENGESAHAN / TANDA TANGAN (Opsi A: Tunggal Kanan) --}}
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
                    Cirebon, {{ now()->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d F Y') }}<br>
                    <strong>{{ $adminTitle }}</strong><br>
                    <span class="signature-sub">{{ $adminUnit }}</span><br><br><br><br>
                    <span class="signature-name">{{ $adminName }}</span>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
