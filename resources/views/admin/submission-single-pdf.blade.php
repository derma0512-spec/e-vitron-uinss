<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Pengajuan {{ $submission->ticket_code }} - E-VITRON UIN SSC</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 14mm 16mm 14mm 16mm;
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
            font-size: 9.5pt;
            line-height: 1.4;
        }

        .single-page-wrap {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .doc-header {
            text-align: center;
            margin-bottom: 14px;
        }

        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin: 0 0 3px 0;
        }

        .doc-subtitle {
            font-size: 9pt;
            color: #374151;
            margin: 0;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .info-table td {
            padding: 6.5pt 8pt;
            vertical-align: top;
            border-bottom: 0.5pt solid #CBD5E1;
            font-size: 9.2pt;
            color: #111827;
        }

        .info-table tr:first-child td {
            border-top: 1pt solid #334155;
        }

        .info-table tr:last-child td {
            border-bottom: 1pt solid #334155;
        }

        .info-label {
            width: 30%;
            font-weight: bold;
            color: #334155;
            background-color: #F8FAFC;
        }

        .info-sep {
            width: 3%;
            text-align: center;
            font-weight: bold;
            color: #334155;
        }

        .info-val {
            width: 67%;
            color: #111827;
        }

        .text-bold {
            font-weight: bold;
            color: #111827;
        }

        .text-sub {
            font-size: 8.5pt;
            color: #4B5563;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
            break-inside: avoid;
            margin-top: 22px;
        }

        .signature-col {
            width: 44%;
            vertical-align: top;
            text-align: center;
            font-size: 9pt;
            line-height: 1.35;
            color: #111827;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            color: #111827;
        }

        .signature-sub {
            font-size: 8.4pt;
            color: #374151;
        }

        .doc-footer-meta {
            margin-top: 18px;
            padding-top: 6px;
            border-top: 0.5pt solid #CBD5E1;
            font-size: 8pt;
            color: #4B5563;
            text-align: left;
        }
    </style>
</head>
<body>
    @php
        $displayStatus = $submission->display_status;
        $isApprovedGroup = in_array($displayStatus, ['terjadwal', 'tayang', 'selesai'], true) || $submission->status === 'disetujui';
        $isRejectedOrCanceled = in_array($displayStatus, ['ditolak', 'dibatalkan'], true);

        $docTitle = $isApprovedGroup
            ? 'SURAT KETERANGAN PERSETUJUAN PENAYANGAN VIDEOTRON'
            : 'BUKTI PENGAJUAN PENAYANGAN VIDEOTRON';

        $adminStatusLabel = \App\Models\Submission::getStatusLabel($displayStatus, 'admin');
        $picName = $submission->user?->name ?? $submission->pic_name;
        $unitName = $submission->organization ?: 'Umum / Mahasiswa';
        $screenNames = $submission->videotrons->pluck('name')->join(', ') ?: '-';

        $durationDays = ($submission->start_date && $submission->end_date)
            ? ($submission->start_date->diffInDays($submission->end_date) + 1)
            : 0;
        $periodStr = ($submission->start_date && $submission->end_date)
            ? $submission->start_date->locale('id')->translatedFormat('d F Y').' – '.$submission->end_date->locale('id')->translatedFormat('d F Y')
            : '-';

        $cleanNotes = trim(preg_replace('/\s+/', ' ', (string) $submission->admin_notes));
        if ($cleanNotes === '' && $displayStatus === 'ditolak') {
            $cleanNotes = 'Tidak memenuhi ketentuan penayangan.';
        } elseif ($cleanNotes === '' && $displayStatus === 'dibatalkan') {
            $cleanNotes = 'Dibatalkan mandiri oleh pengaju sebelum penayangan.';
        }

        $sig = config('sipetron.signatory', []);
        $adminTitle = setting('signatory_admin_title', $sig['admin_title'] ?? 'Tim Pengelola E-VITRON');
        $adminUnit = setting('signatory_admin_unit', $sig['admin_unit'] ?? 'UIN Siber Syekh Nurjati Cirebon');
        $adminName = 'Tim Admin E-VITRON';
    @endphp

    <div class="single-page-wrap">
        {{-- 1. Kop Surat Resmi (Dipakai Bersama) --}}
        @include('admin.partials.kop-surat')

        {{-- 2. Judul Dokumen --}}
        <div class="doc-header">
            <h1 class="doc-title">{{ $docTitle }}</h1>
            <p class="doc-subtitle">Nomor Tiket: <strong>{{ $submission->ticket_code }}</strong></p>
        </div>

        {{-- 3. Tabel Informasi Pengajuan --}}
        <table class="info-table">
            <tr>
                <td class="info-label">Kode Tiket</td>
                <td class="info-sep">:</td>
                <td class="info-val"><span class="text-bold">{{ $submission->ticket_code }}</span></td>
            </tr>
            <tr>
                <td class="info-label">Judul Kegiatan</td>
                <td class="info-sep">:</td>
                <td class="info-val"><span class="text-bold">{{ $submission->title }}</span></td>
            </tr>
            <tr>
                <td class="info-label">Pengaju &amp; Unit / Instansi</td>
                <td class="info-sep">:</td>
                <td class="info-val">
                    <span class="text-bold">{{ $picName }}</span><br>
                    <span class="text-sub">{{ $unitName }}</span>
                </td>
            </tr>
            <tr>
                <td class="info-label">Layar Videotron Dituju</td>
                <td class="info-sep">:</td>
                <td class="info-val">{{ $screenNames }}</td>
            </tr>
            <tr>
                <td class="info-label">Periode Tayang &amp; Durasi</td>
                <td class="info-sep">:</td>
                <td class="info-val">
                    {{ $periodStr }}
                    @if($durationDays > 0)
                        <span class="text-sub">({{ $durationDays }} hari)</span>
                    @endif
                </td>
            </tr>
            @if(! $isRejectedOrCanceled)
                <tr>
                    <td class="info-label">Surat Peminjaman (PDF)</td>
                    <td class="info-sep">:</td>
                    <td class="info-val">
                        {{ $submission->surat_peminjaman ? ($submission->surat_peminjaman_filename ?: 'Terlampir') : '-' }}
                    </td>
                </tr>
            @endif
            <tr>
                <td class="info-label">Status Pengajuan</td>
                <td class="info-sep">:</td>
                <td class="info-val"><span class="text-bold">{{ $adminStatusLabel }}</span></td>
            </tr>
            @if($cleanNotes !== '')
                <tr>
                    <td class="info-label">{{ $isRejectedOrCanceled ? 'Alasan' : 'Catatan / Keterangan' }}</td>
                    <td class="info-sep">:</td>
                    <td class="info-val">{{ $cleanNotes }}</td>
                </tr>
            @endif
            <tr>
                <td class="info-label">Tanggal Cetak</td>
                <td class="info-sep">:</td>
                <td class="info-val">{{ $printedAt }}</td>
            </tr>
        </table>

        {{-- 4. Blok Tanda Tangan Tunggal Sisi Kanan (Opsi A) --}}
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

        <div class="doc-footer-meta">
            E-VITRON UIN Siber Syekh Nurjati Cirebon &bull; Dicetak: {{ $printedAt }}
        </div>
    </div>
</body>
</html>

