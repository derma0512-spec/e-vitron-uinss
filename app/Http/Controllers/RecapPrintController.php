<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Services\VideotronPlaybackService;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class RecapPrintController extends Controller
{
    /**
     * Batas maksimum baris arsip per satu kali ekspor PDF / cetak agar memori PHP tetap stabil bertahun-tahun.
     */
    public const MAX_EXPORT_ROWS = 500;

    /**
     * Menampilkan laporan rekap arsip resmi dalam format PDF A4 Portrait di tab baru (Khusus Admin)
     */
    public function exportPdf(Request $request): Response
    {
        if (! auth()->user()?->isActiveAdmin()) {
            abort(403);
        }

        $filterStatus = (string) $request->query('status', '');
        $periodPreset = (string) $request->query('period', 'all');
        $filterYear = (string) $request->query('year', '');
        $filterMonth = (string) $request->query('month', '');
        $tableSearch = trim((string) $request->query('search', ''));
        $startDate = (string) $request->query('start_date', '');
        $endDate = (string) $request->query('end_date', '');

        $playbackService = app(VideotronPlaybackService::class);
        $today = $playbackService->todayDate();

        $query = Submission::query()->with(['videotrons', 'user'])->where(function ($q) use ($today) {
            $q->whereIn('status', ['selesai', 'ditolak', 'dibatalkan'])
                ->orWhere(function ($sq) use ($today) {
                    $sq->where('status', 'disetujui')
                        ->whereDate('end_date', '<', $today);
                });
        });

        if (filled($filterStatus) && in_array($filterStatus, ['perlu_diturunkan', 'selesai', 'ditolak', 'dibatalkan'], true)) {
            if ($filterStatus === 'perlu_diturunkan') {
                $playbackService->applyPendingTakedownSubmissionConstraints($query, $today);
            } elseif ($filterStatus === 'selesai') {
                $playbackService->applyExpiredBroadcastConstraints($query, $today);
            } else {
                $query->where('status', $filterStatus);
            }
        }

        $hasYear = filled($filterYear);
        $hasMonth = filled($filterMonth);

        if ($periodPreset !== 'all') {
            if ($hasYear && $hasMonth) {
                $y = (int) $filterYear;
                $m = (int) $filterMonth;
                $start = Carbon::createFromDate($y, $m, 1)->startOfMonth()->toDateString();
                $end = Carbon::createFromDate($y, $m, 1)->endOfMonth()->toDateString();
                $query->whereDate('start_date', '<=', $end)->whereDate('end_date', '>=', $start);
            } elseif ($hasYear && ! $hasMonth) {
                $y = (int) $filterYear;
                $start = Carbon::createFromDate($y, 1, 1)->startOfYear()->toDateString();
                $end = Carbon::createFromDate($y, 1, 1)->endOfYear()->toDateString();
                $query->whereDate('start_date', '<=', $end)->whereDate('end_date', '>=', $start);
            } elseif (! $hasYear && $hasMonth) {
                $m = (int) $filterMonth;
                $query->where(function ($q) use ($m) {
                    $q->whereMonth('start_date', $m)->orWhereMonth('end_date', $m);
                });
            } else {
                if (filled($startDate)) {
                    $query->whereDate('start_date', '>=', $startDate);
                }
                if (filled($endDate)) {
                    $query->whereDate('end_date', '<=', $endDate);
                }
            }
        }

        if (filled($tableSearch)) {
            $query->where(function ($q) use ($tableSearch) {
                $q->where('ticket_code', 'like', "%{$tableSearch}%")
                    ->orWhere('title', 'like', "%{$tableSearch}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$tableSearch}%"))
                    ->orWhere('pic_name', 'like', "%{$tableSearch}%");
            });
        }

        $totalMatched = (clone $query)->count();
        $isTruncated = $totalMatched > self::MAX_EXPORT_ROWS;
        $records = $query->orderBy('ticket_code', 'asc')->limit(self::MAX_EXPORT_ROWS)->get();

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

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $now = Carbon::now();
        if ($periodPreset === 'this_month') {
            $periodLabel = 'Bulan Ini ('.($monthNames[$now->month] ?? '').')';
        } elseif ($periodPreset === 'last_month') {
            $sub = $now->copy()->startOfMonth()->subMonth();
            $periodLabel = 'Bulan Lalu ('.($monthNames[$sub->month] ?? '').')';
        } elseif ($periodPreset === 'this_year') {
            $periodLabel = "Tahun Ini ({$now->year})";
        } elseif ($hasYear && $hasMonth && isset($monthNames[(int) $filterMonth])) {
            $periodLabel = $monthNames[(int) $filterMonth]." {$filterYear}";
        } elseif ($hasYear) {
            $periodLabel = "Tahun {$filterYear}";
        } elseif ($hasMonth && isset($monthNames[(int) $filterMonth])) {
            $periodLabel = 'Bulan '.$monthNames[(int) $filterMonth];
        } else {
            $periodLabel = 'Semua Periode';
        }

        $searchQuery = filled($tableSearch) ? $tableSearch : null;
        $exportedAt = now()->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d F Y, H:i').' WIB';

        $logoPath = public_path('images/uin-ssc-logo-official.png');
        if (! is_file($logoPath)) {
            $logoPath = public_path('images/uin-ssc-logo-transparent.png');
        }
        $logoBase64 = is_file($logoPath)
            ? 'data:image/png;base64,'.base64_encode((string) file_get_contents($logoPath))
            : null;

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
            'isTruncated' => $isTruncated,
            'totalMatched' => $totalMatched,
            'maxExportRows' => self::MAX_EXPORT_ROWS,
        ])->render();

        $options = new Options;
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $canvas = $dompdf->getCanvas();
        $fontMetrics = $dompdf->getFontMetrics();
        $font = $fontMetrics->getFont('Helvetica', 'normal');
        $fontBold = $fontMetrics->getFont('Helvetica', 'bold');

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

        $filename = 'rekap-arsip-videotron-'.now()->timezone('Asia/Jakarta')->format('Y-m-d').'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Menampilkan dokumen cetak satu halaman A4 Portrait per pengajuan (Khusus Admin)
     */
    public function printSinglePdf(int $id): Response
    {
        if (! auth()->user()?->isActiveAdmin()) {
            abort(403);
        }

        $submission = Submission::with(['videotrons', 'user'])->findOrFail($id);

        $logoPath = public_path('images/uin-ssc-logo-official.png');
        if (! is_file($logoPath)) {
            $logoPath = public_path('images/uin-ssc-logo-transparent.png');
        }
        $logoBase64 = is_file($logoPath)
            ? 'data:image/png;base64,'.base64_encode((string) file_get_contents($logoPath))
            : null;

        $printedAt = now()->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d F Y, H:i').' WIB';

        $html = view('admin.submission-single-pdf', [
            'submission' => $submission,
            'logoBase64' => $logoBase64,
            'printedAt' => $printedAt,
        ])->render();

        $options = new Options;
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'bukti-pengajuan-'.strtolower($submission->ticket_code).'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Menampilkan dokumen cetak formal laporan rekapitulasi videotron
     */
    public function print(Request $request): View
    {
        if (! auth()->user()?->isActiveAdmin()) {
            abort(403);
        }

        $status = $request->query('status');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $playbackService = app(VideotronPlaybackService::class);
        $today = $playbackService->todayDate();

        $query = Submission::query()->with(['videotrons', 'user'])->where(function ($q) use ($today) {
            $q->whereIn('status', ['selesai', 'ditolak', 'dibatalkan'])
                ->orWhere(function ($sq) use ($today) {
                    $sq->where('status', 'disetujui')
                        ->whereDate('end_date', '<', $today);
                });
        });

        if ($status && in_array($status, ['perlu_diturunkan', 'selesai', 'ditolak', 'dibatalkan'], true)) {
            if ($status === 'perlu_diturunkan') {
                $playbackService->applyPendingTakedownSubmissionConstraints($query, $today);
            } elseif ($status === 'selesai') {
                $playbackService->applyExpiredBroadcastConstraints($query, $today);
            } else {
                $query->where('status', $status);
            }
        }

        if ($startDate) {
            $query->whereDate('start_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('end_date', '<=', $endDate);
        }

        $totalMatched = (clone $query)->count();
        $isTruncated = $totalMatched > self::MAX_EXPORT_ROWS;
        $submissions = $query->latest('updated_at')->limit(self::MAX_EXPORT_ROWS)->get();

        $totalArchived = $submissions->count();
        $totalCompleted = $submissions->filter(fn ($s) => $s->display_status === 'selesai')->count();
        $totalRejected = $submissions->filter(fn ($s) => $s->display_status === 'ditolak')->count();
        $totalCanceled = $submissions->filter(fn ($s) => $s->display_status === 'dibatalkan')->count();

        return view('admin.recap-print', [
            'submissions' => $submissions,
            'totalArchived' => $totalArchived,
            'totalCompleted' => $totalCompleted,
            'totalRejected' => $totalRejected,
            'totalCanceled' => $totalCanceled,
            'filterStatus' => $status,
            'isTruncated' => $isTruncated,
            'totalMatched' => $totalMatched,
            'maxExportRows' => self::MAX_EXPORT_ROWS,
        ]);
    }
}
