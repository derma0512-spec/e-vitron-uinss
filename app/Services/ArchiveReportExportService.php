<?php

namespace App\Services;

use App\Models\Submission;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class ArchiveReportExportService
{
    /**
     * Mengekspor daftar arsip pengajuan yang telah difilter ke dalam berkas PDF A4 Portrait resmi.
     *
     * @param  Collection<int, Submission>  $records
     */
    public function exportPdf(
        Collection $records,
        ?string $filterStatus,
        string $periodLabel,
        ?string $tableSearch
    ): StreamedResponse {
        $totalArchived = $records->count();
        $totalCompleted = $records->filter(fn (Submission $s) => $s->display_status === 'selesai')->count();
        $totalRejected = $records->filter(fn (Submission $s) => $s->display_status === 'ditolak')->count();
        $totalCanceled = $records->filter(fn (Submission $s) => $s->display_status === 'dibatalkan')->count();

        $statusLabel = match ($filterStatus) {
            'perlu_diturunkan' => 'Perlu Diturunkan',
            'selesai' => 'Selesai Tayang',
            'ditolak' => 'Ditolak Admin',
            'dibatalkan' => 'Dibatalkan Pengaju',
            default => 'Semua Status Arsip',
        };
        $searchQuery = filled($tableSearch) ? trim($tableSearch) : null;
        $exportedAt = now()->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d F Y, H:i').' WIB';

        $logoPath = public_path('images/uin-ssc-logo-official.png');
        if (! is_file($logoPath)) {
            $logoPath = public_path('images/uin-ssc-logo-transparent.png');
        }
        $logoBase64 = null;
        if (is_file($logoPath)) {
            $logoBase64 = 'data:image/png;base64,'.base64_encode((string) file_get_contents($logoPath));
        }

        $html = view('admin.recap-pdf', [
            'submissions' => $records,
            'totalArchived' => $totalArchived,
            'totalCompleted' => $totalCompleted,
            'totalRejected' => $totalRejected,
            'totalCanceled' => $totalCanceled,
            'statusLabel' => $statusLabel,
            'periodLabel' => $periodLabel,
            'searchQuery' => $searchQuery,
            'exportedAt' => $exportedAt,
            'logoBase64' => $logoBase64,
        ])->render();

        $filename = 'rekap-arsip-videotron-'.now()->timezone('Asia/Jakarta')->format('Y-m-d').'.pdf';

        return response()->streamDownload(function () use ($html, $exportedAt) {
            $options = new Options;
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', false);
            $options->set('defaultFont', 'Helvetica');

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Tambahkan footer resmi & nomor halaman di setiap lembar A4 Portrait
            $canvas = $dompdf->getCanvas();
            $fontMetrics = $dompdf->getFontMetrics();
            $font = $fontMetrics->getFont('Helvetica', 'normal');
            $fontBold = $fontMetrics->getFont('Helvetica', 'bold');

            // Garis tipis pemisah footer (dari x=42.5 ke x=552.7, y=800)
            $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) use ($font, $fontBold, $exportedAt) {
                $canvas->line(42.5, 800, 552.7, 800, [0.80, 0.84, 0.88], 0.6);
                $canvas->text(
                    42.5,
                    807,
                    "E-VITRON UIN Siber Syekh Nurjati Cirebon  •  Dicetak: {$exportedAt}",
                    $font,
                    8,
                    [0.28, 0.33, 0.41]
                );
                $pageLabel = "Halaman {$pageNumber} dari {$pageCount}";
                $textWidth = $fontMetrics->getTextWidth($pageLabel, $fontBold, 8.2);
                $canvas->text(
                    552.7 - $textWidth,
                    807,
                    $pageLabel,
                    $fontBold,
                    8.2,
                    [0.06, 0.24, 0.18]
                );
            });

            echo $dompdf->output();
        }, $filename, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Mengekspor daftar arsip pengajuan yang telah difilter ke dalam berkas Microsoft Excel (.xlsx) asli.
     *
     * @param  Collection<int, Submission>  $records
     */
    public function exportXlsx(
        Collection $records,
        ?string $filterStatus,
        string $periodLabel,
        ?string $tableSearch
    ): StreamedResponse {
        $totalCompleted = $records->filter(fn (Submission $s) => $s->display_status === 'selesai')->count();
        $totalRejected = $records->filter(fn (Submission $s) => $s->display_status === 'ditolak')->count();
        $totalCanceled = $records->filter(fn (Submission $s) => $s->display_status === 'dibatalkan')->count();

        $statusLabel = match ($filterStatus) {
            'perlu_diturunkan' => 'Perlu Diturunkan',
            default => filled($filterStatus)
                ? Submission::getStatusLabel($filterStatus, 'admin')
                : 'Semua Status Arsip',
        };
        $searchLabel = filled($tableSearch) ? trim($tableSearch) : 'Semua (Tanpa Kata Kunci)';
        $exportedAt = now()->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d F Y, H:i').' WIB';

        $filename = 'rekap-arsip-videotron-'.now()->timezone('Asia/Jakarta')->format('Y-m-d').'.xlsx';

        return response()->streamDownload(function () use (
            $records,
            $totalCompleted,
            $totalRejected,
            $totalCanceled,
            $statusLabel,
            $periodLabel,
            $searchLabel,
            $exportedAt
        ) {
            $tempFile = tempnam(sys_get_temp_dir(), 'sipetron_xlsx_');
            $zip = new ZipArchive;
            $zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            $zip->addFromString('[Content_Types].xml', $this->buildXlsxContentTypesXml());
            $zip->addFromString('_rels/.rels', $this->buildXlsxRootRelsXml());
            $zip->addFromString('xl/workbook.xml', $this->buildXlsxWorkbookXml());
            $zip->addFromString('xl/_rels/workbook.xml.rels', $this->buildXlsxWorkbookRelsXml());
            $zip->addFromString('xl/styles.xml', $this->buildXlsxStylesXml());
            $zip->addFromString('xl/worksheets/sheet1.xml', $this->buildXlsxSheetXml(
                $records,
                $periodLabel,
                $statusLabel,
                $searchLabel,
                $exportedAt,
                $totalCompleted,
                $totalRejected,
                $totalCanceled
            ));

            $zip->close();

            readfile($tempFile);
            @unlink($tempFile);
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Melakukan sanitasi karakter kontrol dan escaping XML agar aman bagi struktur OpenXML Excel.
     */
    protected function xmlEscape(?string $value): string
    {
        $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', (string) $value) ?? '';

        return htmlspecialchars($clean, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    /**
     * Membangun elemen sel teks inline (<c t="inlineStr">) pada lembar kerja Excel.
     */
    protected function buildInlineStrCell(string $cellRef, int $styleIndex, ?string $value): string
    {
        $escaped = $this->xmlEscape($value);

        return "<c r=\"{$cellRef}\" s=\"{$styleIndex}\" t=\"inlineStr\"><is><t xml:space=\"preserve\">{$escaped}</t></is></c>";
    }

    /**
     * Membangun elemen sel angka (<c t="n">) pada lembar kerja Excel.
     */
    protected function buildNumberCell(string $cellRef, int $styleIndex, int|float $value): string
    {
        return "<c r=\"{$cellRef}\" s=\"{$styleIndex}\" t=\"n\"><v>{$value}</v></c>";
    }

    /**
     * Membangun elemen sel rumus HYPERLINK (<c t="str"><f>HYPERLINK(...)</f></c>) pada lembar kerja Excel.
     */
    protected function buildHyperlinkCell(string $cellRef, int $styleIndex, string $url, string $label): string
    {
        $safeUrl = str_replace('"', '""', $url);
        $safeLabel = str_replace('"', '""', $label);
        $formula = $this->xmlEscape("HYPERLINK(\"{$safeUrl}\",\"{$safeLabel}\")");
        $escapedLabel = $this->xmlEscape($label);

        return "<c r=\"{$cellRef}\" s=\"{$styleIndex}\" t=\"str\"><f>{$formula}</f><v>{$escapedLabel}</v></c>";
    }

    /**
     * Menghasilkan dokumen XML [Content_Types].xml untuk paket OpenXML Excel.
     */
    protected function buildXlsxContentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .'</Types>';
    }

    /**
     * Menghasilkan dokumen XML _rels/.rels untuk paket OpenXML Excel.
     */
    protected function buildXlsxRootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>';
    }

    /**
     * Menghasilkan dokumen XML xl/workbook.xml untuk paket OpenXML Excel.
     */
    protected function buildXlsxWorkbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets>'
            .'<sheet name="Rekap Arsip Videotron" sheetId="1" r:id="rId1"/>'
            .'</sheets>'
            .'<definedNames>'
            .'<definedName name="_xlnm.Print_Titles" localSheetId="0">\'Rekap Arsip Videotron\'!$6:$6</definedName>'
            .'</definedNames>'
            .'</workbook>';
    }

    /**
     * Menghasilkan dokumen XML xl/_rels/workbook.xml.rels untuk paket OpenXML Excel.
     */
    protected function buildXlsxWorkbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            .'</Relationships>';
    }

    /**
     * Menghasilkan dokumen XML xl/styles.xml (tipografi, palet warna, border, dan perataan sel) untuk Excel.
     */
    protected function buildXlsxStylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<fonts count="10">'
            // 0: Default cell font (12pt Clear & Readable)
            .'<font><sz val="12"/><color rgb="FF1E293B"/><name val="Calibri"/></font>'
            // 1: Title font (16pt Bold Dark Green)
            .'<font><b/><sz val="16"/><color rgb="FF0F3D2E"/><name val="Calibri"/></font>'
            // 2: Subtitle / Dibatalkan font (12pt Bold Slate)
            .'<font><b/><sz val="12"/><color rgb="FF475569"/><name val="Calibri"/></font>'
            // 3: Meta info font (11.5pt Bold Dark Green/Slate)
            .'<font><b/><sz val="11.5"/><color rgb="FF0F3D2E"/><name val="Calibri"/></font>'
            // 4: Table Header font (12pt Bold White)
            .'<font><b/><sz val="12"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>'
            // 5: Status Selesai font (12pt Bold Green)
            .'<font><b/><sz val="12"/><color rgb="FF15803D"/><name val="Calibri"/></font>'
            // 6: Status Ditolak font (12pt Bold Red)
            .'<font><b/><sz val="12"/><color rgb="FFB91C1C"/><name val="Calibri"/></font>'
            // 7: Ticket / Bold Data font (12pt Bold Slate)
            .'<font><b/><sz val="12"/><color rgb="FF0F172A"/><name val="Calibri"/></font>'
            // 8: Hyperlink font (12pt Bold Underlined Ocean Blue)
            .'<font><b/><u/><sz val="12"/><color rgb="FF0284C7"/><name val="Calibri"/></font>'
            // 9: Muted / Empty Attachment font (11.5pt Italic Slate Gray)
            .'<font><i/><sz val="11.5"/><color rgb="FF94A3B8"/><name val="Calibri"/></font>'
            .'</fonts>'
            .'<fills count="8">'
            // 0: None
            .'<fill><patternFill patternType="none"/></fill>'
            // 1: Gray125 (required by Excel)
            .'<fill><patternFill patternType="gray125"/></fill>'
            // 2: Header Dark Green (#0F3D2E)
            .'<fill><patternFill patternType="solid"><fgColor rgb="FF0F3D2E"/></patternFill></fill>'
            // 3: Meta Box Soft Green (#E8F3EC)
            .'<fill><patternFill patternType="solid"><fgColor rgb="FFE8F3EC"/></patternFill></fill>'
            // 4: Zebra Row (#F8FAF9)
            .'<fill><patternFill patternType="solid"><fgColor rgb="FFF8FAF9"/></patternFill></fill>'
            // 5: Status Selesai Bg (#DCFCE7)
            .'<fill><patternFill patternType="solid"><fgColor rgb="FFDCFCE7"/></patternFill></fill>'
            // 6: Status Ditolak Bg (#FEE2E2)
            .'<fill><patternFill patternType="solid"><fgColor rgb="FFFEE2E2"/></patternFill></fill>'
            // 7: Status Dibatalkan Bg (#F1F5F9)
            .'<fill><patternFill patternType="solid"><fgColor rgb="FFF1F5F9"/></patternFill></fill>'
            .'</fills>'
            .'<borders count="3">'
            // 0: No border
            .'<border><left/><right/><top/><bottom/><diagonal/></border>'
            // 1: Thin slate border for data cells
            .'<border>'
            .'<left style="thin"><color rgb="FFCBD5E1"/></left>'
            .'<right style="thin"><color rgb="FFCBD5E1"/></right>'
            .'<top style="thin"><color rgb="FFCBD5E1"/></top>'
            .'<bottom style="thin"><color rgb="FFCBD5E1"/></bottom>'
            .'<diagonal/>'
            .'</border>'
            // 2: Header border
            .'<border>'
            .'<left style="thin"><color rgb="FF1E5643"/></left>'
            .'<right style="thin"><color rgb="FF1E5643"/></right>'
            .'<top style="medium"><color rgb="FF0F3D2E"/></top>'
            .'<bottom style="medium"><color rgb="FF0F3D2E"/></bottom>'
            .'<diagonal/>'
            .'</border>'
            .'</borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="18">'
            // 0: Default
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            // 1: Report Title (Center, Bold 16pt Green)
            .'<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment vertical="center" horizontal="center"/></xf>'
            // 2: Report Subtitle (Center, Bold 12pt Slate)
            .'<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment vertical="center" horizontal="center"/></xf>'
            // 3: Meta Info Bar (Soft Green Bg, Border, Center)
            .'<xf numFmtId="0" fontId="3" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" horizontal="center"/></xf>'
            // 4: Table Header (Dark Green Bg, White Bold, Center)
            .'<xf numFmtId="0" fontId="4" fillId="2" borderId="2" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" horizontal="center" wrapText="1"/></xf>'
            // 5: Data Row Odd - Left Wrap with Left Padding (Vertical Center)
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" horizontal="left" indent="1" wrapText="1"/></xf>'
            // 6: Data Row Odd - Center No-Wrap (Single Line Clean)
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" horizontal="center"/></xf>'
            // 7: Data Row Odd - Bold Center No-Wrap (Ticket Code)
            .'<xf numFmtId="0" fontId="7" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" horizontal="center"/></xf>'
            // 8: Data Row Even (Zebra) - Left Wrap with Left Padding (Vertical Center)
            .'<xf numFmtId="0" fontId="0" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" horizontal="left" indent="1" wrapText="1"/></xf>'
            // 9: Data Row Even (Zebra) - Center No-Wrap (Single Line Clean)
            .'<xf numFmtId="0" fontId="0" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" horizontal="center"/></xf>'
            // 10: Data Row Even (Zebra) - Bold Center No-Wrap (Ticket Code)
            .'<xf numFmtId="0" fontId="7" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" horizontal="center"/></xf>'
            // 11: Status Selesai Badge Cell (Center No-Wrap)
            .'<xf numFmtId="0" fontId="5" fillId="5" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" horizontal="center"/></xf>'
            // 12: Status Ditolak Badge Cell (Center No-Wrap)
            .'<xf numFmtId="0" fontId="6" fillId="6" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" horizontal="center"/></xf>'
            // 13: Status Dibatalkan Badge Cell (Center No-Wrap)
            .'<xf numFmtId="0" fontId="2" fillId="7" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" horizontal="center"/></xf>'
            // 14: Data Row Odd - Hyperlink Cell (Center, Bold Underlined Blue)
            .'<xf numFmtId="0" fontId="8" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" horizontal="center"/></xf>'
            // 15: Data Row Even (Zebra) - Hyperlink Cell (Center, Bold Underlined Blue)
            .'<xf numFmtId="0" fontId="8" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" horizontal="center"/></xf>'
            // 16: Data Row Odd - Muted Italic Center Cell (Belum Melampirkan)
            .'<xf numFmtId="0" fontId="9" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" horizontal="center"/></xf>'
            // 17: Data Row Even (Zebra) - Muted Italic Center Cell (Belum Melampirkan)
            .'<xf numFmtId="0" fontId="9" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" horizontal="center"/></xf>'
            .'</cellXfs>'
            .'</styleSheet>';
    }

    /**
     * Menghasilkan dokumen XML xl/worksheets/sheet1.xml yang memuat seluruh baris data arsip pengajuan.
     *
     * @param  Collection<int, Submission>  $records
     */
    protected function buildXlsxSheetXml(
        Collection $records,
        string $periodLabel,
        string $statusLabel,
        string $searchLabel,
        string $exportedAt,
        int $totalCompleted,
        int $totalRejected,
        int $totalCanceled
    ): string {
        $cols = [
            1 => 8,   // A: No
            2 => 24,  // B: Kode Tiket (1 baris utuh & lega)
            3 => 48,  // C: Judul Kegiatan
            4 => 30,  // D: Nama Pengaju (PIC)
            5 => 34,  // E: Unit / Instansi / Fakultas
            6 => 22,  // F: No. WhatsApp
            7 => 36,  // G: Videotron Dituju
            8 => 32,  // H: Periode Tayang (1 baris utuh & lega)
            9 => 15,  // I: Durasi
            10 => 25, // J: Surat Peminjaman (PDF)
            11 => 25, // K: Materi Google Drive
            12 => 25, // L: Status Akhir (1 baris utuh & lega)
            13 => 52, // M: Catatan / Keterangan
        ];

        $colLetters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<sheetPr><pageSetUpPr fitToPage="1"/></sheetPr>'
            .'<sheetViews><sheetView workbookViewId="0" showGridLines="1" zoomScale="110"/></sheetViews>'
            .'<cols>';

        foreach ($cols as $idx => $width) {
            $xml .= "<col min=\"{$idx}\" max=\"{$idx}\" width=\"{$width}\" customWidth=\"1\"/>";
        }
        $xml .= '</cols><sheetData>';

        // Row 1: Main Title (Centered across A1:M1)
        $xml .= '<row r="1" ht="30" customHeight="1">'
            .$this->buildInlineStrCell('A1', 1, 'LAPORAN REKAPITULASI ARSIP PENGAJUAN VIDEOTRON')
            .'</row>';

        // Row 2: Subtitle (Centered across A2:M2)
        $xml .= '<row r="2" ht="22" customHeight="1">'
            .$this->buildInlineStrCell('A2', 2, 'LAYANAN PENGAJUAN PENAYANGAN VIDEOTRON (E-VITRON) — UIN SIBER SYEKH NURJATI CIREBON')
            .'</row>';

        // Row 3 & Row 4: Dipecah 2 Baris ("Periode • Kategori" dan "Total Arsip ... • Dicetak ..."), Merge A:M & Rata Tengah
        $totalAll = $records->count();
        $lblSelesai = Submission::getStatusLabel('selesai', 'admin');
        $lblDitolak = Submission::getStatusLabel('ditolak', 'pengaju');
        $lblBatal = Submission::getStatusLabel('dibatalkan', 'pengaju');

        $metaLine1 = "Periode: {$periodLabel}   •   Kategori: {$statusLabel}";
        $metaLine2 = "Total Arsip: {$totalAll} Data ({$lblSelesai}: {$totalCompleted}, {$lblDitolak}: {$totalRejected}, {$lblBatal}: {$totalCanceled})   •   Dicetak: {$exportedAt}";

        $xml .= '<row r="3" ht="24" customHeight="1">';
        foreach ($colLetters as $idx => $letter) {
            $xml .= $this->buildInlineStrCell("{$letter}3", 3, $idx === 0 ? $metaLine1 : '');
        }
        $xml .= '</row>';

        $xml .= '<row r="4" ht="24" customHeight="1">';
        foreach ($colLetters as $idx => $letter) {
            $xml .= $this->buildInlineStrCell("{$letter}4", 3, $idx === 0 ? $metaLine2 : '');
        }
        $xml .= '</row>';

        // Row 6: Table Headers
        $headers = [
            'No',
            'Kode Tiket',
            'Judul Kegiatan',
            'Nama Pengaju (PIC)',
            'Unit / Instansi / Fakultas',
            'No. WhatsApp',
            'Videotron Dituju',
            'Periode Tayang',
            'Durasi',
            'Surat Peminjaman (PDF)',
            'Materi Google Drive',
            'Status Akhir',
            'Catatan / Keterangan',
        ];

        $xml .= '<row r="6" ht="36" customHeight="1">';
        foreach ($headers as $idx => $hText) {
            $cellRef = $colLetters[$idx].'6';
            $xml .= $this->buildInlineStrCell($cellRef, 4, $hText);
        }
        $xml .= '</row>';

        // Data Rows starting at Row 7
        $rowNum = 7;
        $no = 1;
        foreach ($records as $sub) {
            $isEven = ($no % 2 === 0);
            $styleLeft = $isEven ? 8 : 5;
            $styleCenter = $isEven ? 9 : 6;
            $styleTicket = $isEven ? 10 : 7;
            $styleLink = $isEven ? 15 : 14;
            $styleMuted = $isEven ? 17 : 16;

            $displayStatus = $sub->display_status;
            $statusText = Submission::getStatusLabel($displayStatus, 'admin');
            $styleStatus = match ($displayStatus) {
                'selesai' => 11,
                'ditolak' => 12,
                default => 13,
            };

            $vtrNames = $sub->videotrons->pluck('name')->join(', ') ?: '-';

            $periodeTayang = ($sub->start_date && $sub->end_date)
                ? $sub->start_date->locale('id')->translatedFormat('d M Y').' – '.$sub->end_date->locale('id')->translatedFormat('d M Y')
                : '-';

            $duration = ($sub->start_date && $sub->end_date)
                ? ($sub->start_date->diffInDays($sub->end_date) + 1).' Hari'
                : '-';

            $cleanTitle = trim(preg_replace('/\s+/', ' ', (string) $sub->title));
            $cleanOrg = trim(preg_replace('/\s+/', ' ', (string) ($sub->organization ?: 'Umum / Mahasiswa')));
            $cleanNotes = $sub->admin_notes
                ? trim(preg_replace('/\s+/', ' ', $sub->admin_notes))
                : '-';

            // Surat Peminjaman (PDF) Cell
            $pdfUrl = $sub->surat_peminjaman_url;
            $pdfCellXml = $pdfUrl
                ? $this->buildHyperlinkCell("J{$rowNum}", $styleLink, $pdfUrl, 'Lihat Surat PDF')
                : $this->buildInlineStrCell("J{$rowNum}", $styleMuted, 'Belum Melampirkan');

            // Materi Google Drive Cell
            $rawDriveUrl = trim((string) $sub->gdrive_link);
            $hasValidDriveUrl = $rawDriveUrl !== '' && preg_match('/^https?:\/\//i', $rawDriveUrl);
            $driveCellXml = $hasValidDriveUrl
                ? $this->buildHyperlinkCell("K{$rowNum}", $styleLink, $rawDriveUrl, 'Buka Materi Drive')
                : $this->buildInlineStrCell("K{$rowNum}", $styleMuted, 'Tidak Ada Tautan');

            // Hitung tinggi baris otomatis (konservatif dengan indent="1" dan font 12pt) agar Catatan & Videotron Dituju tidak pernah terpotong
            $maxLines = max(
                1,
                (int) ceil(mb_strlen($cleanTitle) / 34),
                (int) ceil(mb_strlen($cleanOrg) / 24),
                (int) ceil(mb_strlen($vtrNames) / 24),
                (int) ceil(mb_strlen($cleanNotes) / 36)
            );
            $rowHeight = max(36, ($maxLines * 20) + 14);

            $xml .= "<row r=\"{$rowNum}\" ht=\"{$rowHeight}\" customHeight=\"1\">"
                .$this->buildNumberCell("A{$rowNum}", $styleCenter, $no)
                .$this->buildInlineStrCell("B{$rowNum}", $styleTicket, $sub->ticket_code)
                .$this->buildInlineStrCell("C{$rowNum}", $styleLeft, $cleanTitle)
                .$this->buildInlineStrCell("D{$rowNum}", $styleLeft, $sub->user?->name ?? $sub->pic_name)
                .$this->buildInlineStrCell("E{$rowNum}", $styleLeft, $cleanOrg)
                .$this->buildInlineStrCell("F{$rowNum}", $styleCenter, $sub->whatsapp ?: '-')
                .$this->buildInlineStrCell("G{$rowNum}", $styleLeft, $vtrNames)
                .$this->buildInlineStrCell("H{$rowNum}", $styleCenter, $periodeTayang)
                .$this->buildInlineStrCell("I{$rowNum}", $styleCenter, $duration)
                .$pdfCellXml
                .$driveCellXml
                .$this->buildInlineStrCell("L{$rowNum}", $styleStatus, $statusText)
                .$this->buildInlineStrCell("M{$rowNum}", $styleLeft, $cleanNotes)
                .'</row>';

            $rowNum++;
            $no++;
        }

        $lastDataRow = max(7, $rowNum - 1);

        $xml .= '</sheetData>'
            .'<mergeCells count="4">'
            .'<mergeCell ref="A1:M1"/>'
            .'<mergeCell ref="A2:M2"/>'
            .'<mergeCell ref="A3:M3"/>'
            .'<mergeCell ref="A4:M4"/>'
            .'</mergeCells>'
            .'<pageMargins left="0.25" right="0.25" top="0.35" bottom="0.45" header="0.2" footer="0.25"/>'
            .'<pageSetup paperSize="9" orientation="landscape" fitToWidth="1" fitToHeight="0"/>'
            .'<headerFooter><oddFooter>&amp;R&amp;"Calibri,Bold"&amp;10Halaman &amp;P dari &amp;N</oddFooter></headerFooter>'
            ."<ignoredErrors><ignoredError sqref=\"F7:F{$lastDataRow}\" numberStoredAsText=\"1\"/></ignoredErrors>"
            .'</worksheet>';

        return $xml;
    }
}
