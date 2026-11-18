<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $submission->ticket_code }} - E-VITRON UIN SSC</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 15mm 10mm 15mm;
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
            font-size: 8.5pt;
            line-height: 1.35;
        }

        .single-page-wrap {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .doc-header {
            text-align: center;
            margin: 6px 0 10px 0;
        }

        .doc-title {
            font-size: 11pt;
            font-weight: bold;
            color: #0E4D3C;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 2px 0;
        }

        .doc-subtitle {
            font-size: 8pt;
            color: #4B5563;
            margin: 0;
        }

        .doc-ticket-badge {
            display: inline-block;
            background-color: #E3F1EA;
            color: #0E4D3C;
            border: 0.5pt solid #A3D4C1;
            padding: 2pt 8pt;
            border-radius: 4px;
            font-weight: bold;
            font-size: 8pt;
            letter-spacing: 0.5px;
            margin-top: 3px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .info-table td {
            padding: 4pt 6pt;
            vertical-align: top;
            border-bottom: 0.5pt solid #E2E8F0;
            font-size: 8.3pt;
            color: #1F2937;
        }

        .info-table tr:first-child td {
            border-top: 1pt solid #0E4D3C;
        }

        .info-table tr:last-child td {
            border-bottom: 1pt solid #0E4D3C;
        }

        .info-label {
            width: 29%;
            font-weight: bold;
            color: #374151;
            background-color: #F8FAFC;
        }

        .info-sep {
            width: 2.5%;
            text-align: center;
            font-weight: bold;
            color: #4B5563;
        }

        .info-val {
            width: 68.5%;
            color: #111827;
        }

        .text-bold {
            font-weight: bold;
            color: #111827;
        }

        .text-sub {
            font-size: 7.8pt;
            color: #64748B;
        }

        /* Status Callout Box */
        /* Unified Official Decree Box */
        .decree-box {
            padding: 6pt 9pt;
            font-size: 8pt;
            line-height: 1.4;
            margin-bottom: 12px;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .decree-title {
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 3pt;
            font-size: 8.5pt;
        }

        .decree-selesai {
            background-color: #EEF2FF;
            border: 0.75pt solid #C7D2FE;
            border-left: 3.5pt solid #4F46E5;
            color: #312E81;
        }
        .decree-selesai .decree-title { color: #3730A3; }

        .decree-tayang,
        .decree-disetujui,
        .decree-terjadwal {
            background-color: #F0FDF4;
            border: 0.75pt solid #86EFAC;
            border-left: 3.5pt solid #16A34A;
            color: #14532D;
        }
        .decree-tayang .decree-title,
        .decree-disetujui .decree-title,
        .decree-terjadwal .decree-title { color: #15803D; }

        .decree-menunggu {
            background-color: #FFFBEB;
            border: 0.75pt solid #FDE68A;
            border-left: 3.5pt solid #D97706;
            color: #78350F;
        }
        .decree-menunggu .decree-title { color: #B45309; }

        .decree-revisi {
            background-color: #FFF7ED;
            border: 0.75pt solid #FDBA74;
            border-left: 3.5pt solid #EA580C;
            color: #7C2D12;
        }
        .decree-revisi .decree-title { color: #C2410C; }

        .decree-ditolak {
            background-color: #FEF2F2;
            border: 0.75pt solid #FECACA;
            border-left: 3.5pt solid #DC2626;
            color: #7F1D1D;
        }
        .decree-ditolak .decree-title { color: #B91C1C; }

        .decree-dibatalkan {
            background-color: #F8FAFC;
            border: 0.75pt solid #CBD5E1;
            border-left: 3.5pt solid #64748B;
            color: #334155;
        }
        .decree-dibatalkan .decree-title { color: #475569; }

        /* Signature Table */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
            break-inside: avoid;
            margin-top: 10px;
        }

        .sig-col-space {
            width: 52%;
        }

        .sig-col-right {
            width: 48%;
            vertical-align: top;
            text-align: center;
            font-size: 8.5pt;
            line-height: 1.4;
            color: #111827;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            color: #111827;
        }

        .signature-sub {
            font-size: 7.8pt;
            color: #4B5563;
        }

        .doc-footer-meta {
            margin-top: 8px;
            padding-top: 3pt;
            border-top: 0.5pt solid #E2E8F0;
            font-size: 7.2pt;
            color: #64748B;
            text-align: left;
        }
    </style>
</head>
<body>
    @php
        $dispStatus = $submission->display_status;
        $isApprovedGroup = in_array($dispStatus, ['terjadwal', 'tayang', 'selesai'], true) || in_array($submission->status, ['disetujui', 'selesai'], true);
        $isPendingGroup = in_array($dispStatus, ['menunggu', 'revisi'], true);
        $isRejectedGroup = in_array($dispStatus, ['ditolak', 'dibatalkan'], true);

        // Judul Dokumen Dinamis Sesuai Tahapan & Status Nyata
        if ($dispStatus === 'selesai' || $submission->status === 'selesai') {
            $docTitle = 'SURAT KETERANGAN SELESAI PENAYANGAN VIDEOTRON';
        } elseif ($dispStatus === 'tayang') {
            $docTitle = 'SURAT KETERANGAN PENAYANGAN AKTIF VIDEOTRON';
        } elseif ($dispStatus === 'terjadwal' || $submission->status === 'disetujui') {
            $docTitle = 'SURAT KETERANGAN PERSETUJUAN PENAYANGAN VIDEOTRON';
        } elseif ($dispStatus === 'revisi') {
            $docTitle = 'BUKTI PENGAJUAN & CATATAN PERBAIKAN MATERI';
        } elseif ($dispStatus === 'ditolak') {
            $docTitle = 'SURAT PEMBERITAHUAN PENOLAKAN PENGAJUAN VIDEOTRON';
        } elseif ($dispStatus === 'dibatalkan') {
            $docTitle = 'SURAT KETERANGAN PEMBATALAN PENGAJUAN VIDEOTRON';
        } else {
            $docTitle = 'BUKTI TANDA TERIMA PENGAJUAN PENAYANGAN VIDEOTRON';
        }

        // Ketetapan Resmi Tunggal Sesuai Status Dokumen
        $decreeMap = [
            'selesai' => [
                'class' => 'decree-selesai',
                'title' => 'SURAT KETERANGAN SELESAI PENAYANGAN (BUKTI LPJ PUBLIKASI):',
                'content' => 'Menerangkan bahwa materi publikasi dengan nomor tiket di atas telah <strong>SELESAI DITAYANGKAN SECARA PENUH DAN TUNTAS</strong> pada perangkat videotron kampus UIN Siber Syekh Nurjati Cirebon sesuai jadwal periode yang tercantum. Lembar keterangan ini sah diterbitkan oleh Tim Pengelola E-VITRON UIN Siber Syekh Nurjati Cirebon sebagai bukti penyelesaian publikasi dan kelengkapan dokumen pertanggungjawaban kegiatan (LPJ).',
            ],
            'tayang' => [
                'class' => 'decree-tayang',
                'title' => 'SURAT KETERANGAN PENAYANGAN AKTIF:',
                'content' => 'Berdasarkan hasil kurasi dan verifikasi teknis oleh Tim Admin E-VITRON UIN Siber Syekh Nurjati Cirebon, materi publikasi dengan nomor tiket di atas telah disetujui dan <strong>SAAT INI SEDANG AKTIF DISIARKAN</strong> pada layar videotron kampus terpilih sesuai jadwal penayangan yang tercantum.',
            ],
            'terjadwal' => [
                'class' => 'decree-disetujui',
                'title' => 'SURAT KETERANGAN PERSETUJUAN & IZIN PENAYANGAN:',
                'content' => 'Berdasarkan hasil kurasi dan verifikasi teknis oleh Tim Admin E-VITRON UIN Siber Syekh Nurjati Cirebon, materi publikasi dengan nomor tiket di atas dinyatakan <strong>TELAH DISETUJUI DAN DIIZINKAN TAYANG</strong> pada perangkat videotron kampus sesuai rincian jadwal yang tercantum. Penayangan akan dimulai otomatis sesuai tanggal jadwal tayang.',
            ],
            'disetujui' => [
                'class' => 'decree-disetujui',
                'title' => 'SURAT KETERANGAN PERSETUJUAN & IZIN PENAYANGAN:',
                'content' => 'Berdasarkan hasil kurasi dan verifikasi teknis oleh Tim Admin E-VITRON UIN Siber Syekh Nurjati Cirebon, materi publikasi dengan nomor tiket di atas dinyatakan <strong>TELAH DISETUJUI DAN DIIZINKAN TAYANG</strong> pada perangkat videotron kampus sesuai rincian jadwal yang tercantum. Penayangan akan dimulai otomatis sesuai tanggal jadwal tayang.',
            ],
            'revisi' => [
                'class' => 'decree-revisi',
                'title' => 'CATATAN PERBAIKAN MATERI (REVISI):',
                'content' => 'Pengajuan materi publikasi memerlukan perbaikan teknis atau pembaruan berkas dari pihak pemohon sebelum dapat disetujui. Silakan periksa catatan verifikator di bawah dan kirimkan tautan materi yang telah diperbaiki melalui portal E-VITRON.',
            ],
            'ditolak' => [
                'class' => 'decree-ditolak',
                'title' => 'PEMBERITAHUAN PENOLAKAN PENGAJUAN:',
                'content' => 'Pengajuan materi publikasi dengan nomor tiket di atas tidak memenuhi ketentuan atau kebijakan penayangan videotron kampus UIN Siber Syekh Nurjati Cirebon.',
            ],
            'dibatalkan' => [
                'class' => 'decree-dibatalkan',
                'title' => 'KETERANGAN PEMBATALAN PENGAJUAN:',
                'content' => 'Pengajuan materi publikasi dengan nomor tiket di atas telah dibatalkan secara mandiri oleh pihak pemohon sebelum masa penayangan.',
            ],
            'menunggu' => [
                'class' => 'decree-menunggu',
                'title' => 'BUKTI TANDA TERIMA PENDAFTARAN RESMI:',
                'content' => 'Lembar ini adalah bukti pendaftaran resmi yang diterbitkan secara otomatis oleh Sistem E-VITRON UIN Siber Syekh Nurjati Cirebon. Pengajuan saat ini berada dalam antrean kurasi dan verifikasi teknis oleh Tim Admin E-VITRON. Penayangan materi pada videotron hanya dapat dilaksanakan setelah pengajuan dinyatakan <strong>Disetujui</strong>.',
            ],
        ];

        $effectiveStatusKey = ($submission->status === 'selesai' || $dispStatus === 'selesai')
            ? 'selesai'
            : ($dispStatus ?: 'menunggu');
        $decree = $decreeMap[$effectiveStatusKey] ?? $decreeMap['menunggu'];

        $applicantName = $submission->user?->name ?? $submission->pic_name;
        $unitName = $submission->organization ?: 'Umum / Mahasiswa';
        $screenNames = $submission->videotrons->pluck('name')->join(', ') ?: 'Semua Layar Kampus';

        $durationDays = ($submission->start_date && $submission->end_date)
            ? ($submission->start_date->diffInDays($submission->end_date) + 1)
            : 0;
        $periodStr = ($submission->start_date && $submission->end_date)
            ? $submission->start_date->locale('id')->translatedFormat('d F Y').' – '.$submission->end_date->locale('id')->translatedFormat('d F Y')
            : '-';

        $submittedAt = $submission->created_at
            ? $submission->created_at->locale('id')->translatedFormat('d F Y, H:i').' WIB'
            : '-';

        // Konfigurasi Penandatangan Pengelola Teknis E-VITRON (Bila Dokumen Berstatus Disetujui)
        $sig = config('sipetron.signatory', []);
        $adminTitle = setting('signatory_admin_title', $sig['admin_title'] ?? 'Tim Pengelola E-VITRON');
        $adminUnit  = setting('signatory_admin_unit', $sig['admin_unit'] ?? 'UIN Siber Syekh Nurjati Cirebon');
        $adminName  = setting('signatory_admin_name', 'Tim Admin E-VITRON');
    @endphp

    <div class="single-page-wrap">
        {{-- 1. Kop Surat Resmi UIN Siber Syekh Nurjati Cirebon --}}
        @include('admin.partials.kop-surat')

        {{-- 2. Judul Dokumen & Nomor Tiket --}}
        <div class="doc-header">
            <h1 class="doc-title">{{ $docTitle }}</h1>
            <p class="doc-subtitle">Layanan Pengajuan Penayangan Videotron (E-VITRON) &bull; UIN Siber Syekh Nurjati Cirebon</p>
            <div class="doc-ticket-badge">
                NOMOR TIKET: {{ $submission->ticket_code }}
            </div>
        </div>

        {{-- 3. Tabel Rincian Data Pengajuan --}}
        <table class="info-table">
            <tr>
                <td class="info-label">Nomor Tiket Registrasi</td>
                <td class="info-sep">:</td>
                <td class="info-val"><span class="text-bold">{{ $submission->ticket_code }}</span></td>
            </tr>
            <tr>
                <td class="info-label">Judul Publikasi / Kegiatan</td>
                <td class="info-sep">:</td>
                <td class="info-val"><span class="text-bold">{{ $submission->title }}</span></td>
            </tr>
            <tr>
                <td class="info-label">Pemohon &amp; Unit / Jurusan</td>
                <td class="info-sep">:</td>
                <td class="info-val">
                    <span class="text-bold">{{ $applicantName }}</span><br>
                    <span class="text-sub">{{ $unitName }}</span>
                </td>
            </tr>
            @if($submission->whatsapp)
                <tr>
                    <td class="info-label">Kontak WhatsApp (PIC)</td>
                    <td class="info-sep">:</td>
                    <td class="info-val">{{ $submission->whatsapp }}</td>
                </tr>
            @endif
            <tr>
                <td class="info-label">Layar Videotron Tujuan</td>
                <td class="info-sep">:</td>
                <td class="info-val">{{ $screenNames }}</td>
            </tr>
            <tr>
                <td class="info-label">Periode Penayangan</td>
                <td class="info-sep">:</td>
                <td class="info-val">
                    {{ $periodStr }}
                    @if($durationDays > 0)
                        <span class="text-sub">({{ $durationDays }} hari)</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="info-label">Format Materi</td>
                <td class="info-sep">:</td>
                <td class="info-val">
                    {{ strtoupper($submission->content_type ?? 'POSTER') }}
                    @if($submission->content_type === 'video' && $submission->video_duration_seconds)
                        <span class="text-sub">(Durasi: {{ $submission->video_duration_seconds }} detik)</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="info-label">Surat Permohonan Resmi (PDF)</td>
                <td class="info-sep">:</td>
                <td class="info-val">
                    @if($submission->surat_peminjaman)
                        Terlampir (Dokumen Surat Permohonan Resmi .pdf)
                    @else
                        <span class="text-sub">-</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="info-label">Waktu Pengajuan (Submit)</td>
                <td class="info-sep">:</td>
                <td class="info-val">{{ $submittedAt }}</td>
            </tr>
            <tr>
                <td class="info-label">Waktu Cetak Dokumen</td>
                <td class="info-sep">:</td>
                <td class="info-val">{{ $printedAt }}</td>
            </tr>
        </table>

        {{-- 4. Kotak Ketetapan Resmi Dokumen Sesuai Status --}}
        <div class="decree-box {{ $decree['class'] }}">
            <div class="decree-title">{{ $decree['title'] }}</div>
            <div>{!! $decree['content'] !!}</div>
            @if($submission->admin_notes && in_array($dispStatus, ['revisi', 'ditolak'], true))
                <div style="margin-top: 4pt; padding-top: 4pt; border-top: 0.5pt dashed currentColor; font-size: 8pt;">
                    <strong>{{ $dispStatus === 'ditolak' ? 'Alasan Penolakan' : 'Catatan Petugas Verifikator' }}:</strong> "{{ $submission->admin_notes }}"
                </div>
            @endif
        </div>

        {{-- 6. BLOK TANDA TANGAN DINAMIS --}}
        <table class="signature-table">
            <tr>
                <td class="sig-col-space"></td>
                <td class="sig-col-right">
                    Cirebon, {{ now()->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d F Y') }}<br>
                    @if($isApprovedGroup)
                        <strong>{{ $adminTitle }}</strong><br>
                        <span class="signature-sub">{{ $adminUnit }}</span><br><br><br><br>
                        <span class="signature-name">{{ $adminName }}</span>
                    @else
                        <strong>Pemohon / Penanggung Jawab,</strong><br><br><br><br>
                        <span class="signature-name">{{ $applicantName }}</span><br>
                        <span class="signature-sub">{{ $unitName }}</span>
                    @endif
                </td>
            </tr>
        </table>

        {{-- 7. Meta Footer --}}
        <div class="doc-footer-meta">
            E-VITRON &bull; UIN Siber Syekh Nurjati Cirebon &bull; Dicetak: {{ $printedAt }}
        </div>
    </div>
</body>
</html>
