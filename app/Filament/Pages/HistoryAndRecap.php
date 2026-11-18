<?php

namespace App\Filament\Pages;

use App\Models\Submission;
use App\Services\ArchiveReportExportService;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HistoryAndRecap extends Page
{
    use WithPagination;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Riwayat & Rekap';

    protected static ?string $title = 'Riwayat Pengajuan & Rekap Laporan';

    protected static ?int $navigationSort = 5;

    protected static string $view = 'filament.pages.history-and-recap';

    public function getHeading(): string
    {
        return 'Riwayat & Rekap';
    }

    public function getSubheading(): ?string
    {
        return 'Arsip riwayat pengajuan yang telah selesai masa tayangnya, ditolak, atau dibatalkan.';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public string $tableSearch = '';

    public ?string $filterStatus = '';

    public string $periodPreset = 'all';

    public ?string $filterYear = '';

    public ?string $filterMonth = '';

    public ?string $startDate = '';

    public ?string $endDate = '';

    public int $perPage = 10;

    public ?int $selectedSubmissionId = null;

    /** True ketika halaman dibuka dari notifikasi (via ?selected= di URL). Tabel hanya menampilkan 1 baris. */
    public bool $fromNotification = false;

    protected $queryString = [
        'tableSearch' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'periodPreset' => ['except' => 'all'],
        'filterYear' => ['except' => ''],
        'filterMonth' => ['except' => ''],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'perPage' => ['except' => 10],
        'selectedSubmissionId' => ['as' => 'drawer', 'except' => null],
    ];

    public function mount(): void
    {
        $statusParam = request()->query('filterStatus') ?: request()->query('status');
        if (filled($statusParam)) {
            $this->filterStatus = (string) $statusParam;
        }

        $perPageParam = request()->query('perPage');
        if ($perPageParam && in_array((int) $perPageParam, [5, 10, 25, 50], true)) {
            $this->perPage = (int) $perPageParam;
        }

        $selectedParam = request()->query('selected');
        $drawerParam = request()->query('drawer');

        if ($selectedParam && Submission::where('id', $selectedParam)->exists()) {
            // Dibuka dari notifikasi / tautan luar (?selected=ID) -> mode 1 baris
            $this->selectedSubmissionId = (int) $selectedParam;
            $this->fromNotification = true;
        } elseif ($drawerParam && Submission::where('id', $drawerParam)->exists()) {
            // Dibuka / di-reload saat browsing tabel biasa (?drawer=ID) -> mode tabel lengkap
            $this->selectedSubmissionId = (int) $drawerParam;
            $this->fromNotification = false;
        } else {
            $this->selectedSubmissionId = null;
            $this->fromNotification = false;
        }
    }

    public function updatingTableSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function setPeriodPreset(string $preset, ?string $year = null, ?string $month = null): void
    {
        $now = Carbon::now();

        if ($preset === 'all' || ($preset === 'custom' && blank($year) && blank($month))) {
            $this->periodPreset = 'all';
            $this->filterYear = '';
            $this->filterMonth = '';
            $this->startDate = '';
            $this->endDate = '';
        } elseif ($preset === 'this_month') {
            $this->periodPreset = 'this_month';
            $this->filterYear = (string) $now->year;
            $this->filterMonth = (string) $now->month;
            $this->startDate = $now->copy()->startOfMonth()->toDateString();
            $this->endDate = $now->copy()->endOfMonth()->toDateString();
        } elseif ($preset === 'last_month') {
            $this->periodPreset = 'last_month';
            $sub = $now->copy()->startOfMonth()->subMonth();
            $this->filterYear = (string) $sub->year;
            $this->filterMonth = (string) $sub->month;
            $this->startDate = $sub->copy()->startOfMonth()->toDateString();
            $this->endDate = $sub->copy()->endOfMonth()->toDateString();
        } elseif ($preset === 'this_year') {
            $this->periodPreset = 'this_year';
            $this->filterYear = (string) $now->year;
            $this->filterMonth = '';
            $this->startDate = $now->copy()->startOfYear()->toDateString();
            $this->endDate = $now->copy()->endOfYear()->toDateString();
        } elseif ($preset === 'custom') {
            $this->periodPreset = 'custom';
            $this->filterYear = filled($year) ? (string) $year : '';
            $this->filterMonth = filled($month) ? (string) $month : '';

            if (filled($this->filterYear) && filled($this->filterMonth)) {
                $y = (int) $this->filterYear;
                $m = (int) $this->filterMonth;
                $c = Carbon::createFromDate($y, $m, 1);
                $this->startDate = $c->copy()->startOfMonth()->toDateString();
                $this->endDate = $c->copy()->endOfMonth()->toDateString();
            } elseif (filled($this->filterYear) && blank($this->filterMonth)) {
                $y = (int) $this->filterYear;
                $c = Carbon::createFromDate($y, 1, 1);
                $this->startDate = $c->copy()->startOfYear()->toDateString();
                $this->endDate = $c->copy()->endOfYear()->toDateString();
            } else {
                $this->startDate = '';
                $this->endDate = '';
            }
        }

        $this->resetPage();
    }

    public function applyPeriodFilter(string $mode, ?string $year = null, ?string $month = null): void
    {
        if ($mode === 'all') {
            $this->setPeriodPreset('all');
        } else {
            $this->setPeriodPreset('custom', $year, $month);
        }
    }

    /**
     * Dapatkan daftar tahun unik yang ada di database pengajuan
     *
     * @return array<int>
     */
    public function getAvailableYearsProperty(): array
    {
        $bounds = Submission::query()
            ->selectRaw('
                MIN(YEAR(start_date)) as min_start,
                MAX(YEAR(start_date)) as max_start,
                MIN(YEAR(end_date)) as min_end,
                MAX(YEAR(end_date)) as max_end,
                MIN(YEAR(created_at)) as min_created,
                MAX(YEAR(created_at)) as max_created
            ')
            ->first();

        $currentYear = (int) now()->format('Y');
        $candidates = array_filter([
            $bounds?->min_start ? (int) $bounds->min_start : null,
            $bounds?->max_start ? (int) $bounds->max_start : null,
            $bounds?->min_end ? (int) $bounds->min_end : null,
            $bounds?->max_end ? (int) $bounds->max_end : null,
            $bounds?->min_created ? (int) $bounds->min_created : null,
            $bounds?->max_created ? (int) $bounds->max_created : null,
            $currentYear,
        ], fn ($y) => is_int($y) && $y >= 2000 && $y <= 2100);

        $minYear = ! empty($candidates) ? min($candidates) : $currentYear;
        $maxYear = ! empty($candidates) ? max($candidates) : $currentYear;

        $allYears = range($maxYear, $minYear);

        return array_values($allYears);
    }

    public function getPeriodLabelProperty(): string
    {
        if ($this->periodPreset === 'all' || (blank($this->filterYear) && blank($this->filterMonth))) {
            return 'Semua Periode';
        }

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $now = Carbon::now();

        if ($this->periodPreset === 'this_month') {
            $mName = $monthNames[$now->month] ?? '';

            return "Bulan Ini ({$mName})";
        }

        if ($this->periodPreset === 'last_month') {
            $sub = $now->copy()->startOfMonth()->subMonth();
            $mName = $monthNames[$sub->month] ?? '';

            return "Bulan Lalu ({$mName})";
        }

        if ($this->periodPreset === 'this_year') {
            return "Tahun Ini ({$now->year})";
        }

        $hasYear = filled($this->filterYear);
        $hasMonth = filled($this->filterMonth) && isset($monthNames[(int) $this->filterMonth]);

        if ($hasYear && $hasMonth) {
            $monthName = $monthNames[(int) $this->filterMonth];

            return "{$monthName} {$this->filterYear}";
        }

        if ($hasYear && ! $hasMonth) {
            return "Tahun {$this->filterYear}";
        }

        if (! $hasYear && $hasMonth) {
            $monthName = $monthNames[(int) $this->filterMonth];

            return "Bulan {$monthName}";
        }

        return 'Semua Periode';
    }

    public function resetFilters(): void
    {
        $this->tableSearch = '';
        $this->filterStatus = '';
        $this->periodPreset = 'all';
        $this->filterYear = '';
        $this->filterMonth = '';
        $this->startDate = '';
        $this->endDate = '';
        $this->resetPage();
        $this->dispatch('reset-archive-filter');
    }

    /**
     * Rekap Statistik untuk Kartu Atas
     *
     * @return array{total: int, completed: int, rejected: int, canceled: int}
     */
    public function getRecapStatsProperty(): array
    {
        $counts = Submission::getDisplayStatusCounts();
        $completed = (int) ($counts['selesai'] ?? 0);
        $rejected = (int) ($counts['ditolak'] ?? 0);
        $canceled = (int) ($counts['dibatalkan'] ?? 0);

        return [
            'total' => $completed + $rejected + $canceled,
            'completed' => $completed,
            'rejected' => $rejected,
            'canceled' => $canceled,
        ];
    }

    public function getTotalFilteredCountProperty(): int
    {
        $query = Submission::query()->where(function ($q) {
            $q->whereIn('status', ['selesai', 'ditolak', 'dibatalkan'])
                ->orWhere(function ($sq) {
                    $sq->where('status', 'disetujui')
                        ->whereDate('end_date', '<', today());
                });
        });

        if (filled($this->filterStatus)) {
            if ($this->filterStatus === 'selesai') {
                $query->where(function ($sq) {
                    $sq->where('status', 'selesai')
                        ->orWhere(function ($ssq) {
                            $ssq->where('status', 'disetujui')
                                ->whereDate('end_date', '<', today());
                        });
                });
            } else {
                $query->where('status', $this->filterStatus);
            }
        }

        $this->applyPeriodQueryConstraint($query);

        if (filled($this->tableSearch)) {
            $s = trim($this->tableSearch);
            $query->where(function ($q) use ($s) {
                $q->where('ticket_code', 'like', "%{$s}%")
                    ->orWhere('title', 'like', "%{$s}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$s}%"))
                    ->orWhere('pic_name', 'like', "%{$s}%");
            });
        }

        return $query->count();
    }

    /**
     * Terapkan batasan periode (Tahun & Bulan) pada query
     */
    protected function applyPeriodQueryConstraint($query): void
    {
        if ($this->periodPreset === 'all') {
            return;
        }

        $hasYear = filled($this->filterYear);
        $hasMonth = filled($this->filterMonth);

        // 1. Tahun + Bulan Spesifik (Rentang Tanggal Penuh)
        if ($hasYear && $hasMonth) {
            $y = (int) $this->filterYear;
            $m = (int) $this->filterMonth;
            $start = Carbon::createFromDate($y, $m, 1)->startOfMonth()->toDateString();
            $end = Carbon::createFromDate($y, $m, 1)->endOfMonth()->toDateString();

            $query->whereDate('start_date', '<=', $end)
                ->whereDate('end_date', '>=', $start);

            return;
        }

        // 2. Tahun Spesifik + Semua Bulan (1 Tahun Penuh)
        if ($hasYear && ! $hasMonth) {
            $y = (int) $this->filterYear;
            $start = Carbon::createFromDate($y, 1, 1)->startOfYear()->toDateString();
            $end = Carbon::createFromDate($y, 1, 1)->endOfYear()->toDateString();

            $query->whereDate('start_date', '<=', $end)
                ->whereDate('end_date', '>=', $start);

            return;
        }

        // 3. Semua Tahun + Bulan Tertentu
        if (! $hasYear && $hasMonth) {
            $m = (int) $this->filterMonth;
            $query->where(function ($q) use ($m) {
                $q->whereMonth('start_date', $m)
                    ->orWhereMonth('end_date', $m);
            });

            return;
        }

        // Fallback untuk custom startDate & endDate jika ada
        if (filled($this->startDate)) {
            $query->whereDate('start_date', '>=', $this->startDate);
        }
        if (filled($this->endDate)) {
            $query->whereDate('end_date', '<=', $this->endDate);
        }
    }

    public function getPrintPdfUrlProperty(): string
    {
        return route('admin.history-and-recap.export-pdf', array_filter([
            'status' => $this->filterStatus,
            'period' => $this->periodPreset !== 'all' ? $this->periodPreset : null,
            'year' => $this->filterYear,
            'month' => $this->filterMonth,
            'search' => trim($this->tableSearch),
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ], fn ($v) => filled($v)));
    }

    protected function getFilteredArchiveRecords(): Collection
    {
        $query = Submission::query()->with(['videotrons', 'user'])->where(function ($q) {
            $q->whereIn('status', ['selesai', 'ditolak', 'dibatalkan'])
                ->orWhere(function ($sq) {
                    $sq->where('status', 'disetujui')
                        ->whereDate('end_date', '<', today());
                });
        });

        if (filled($this->filterStatus)) {
            if ($this->filterStatus === 'selesai') {
                $query->where(function ($sq) {
                    $sq->where('status', 'selesai')
                        ->orWhere(function ($ssq) {
                            $ssq->where('status', 'disetujui')
                                ->whereDate('end_date', '<', today());
                        });
                });
            } else {
                $query->where('status', $this->filterStatus);
            }
        }

        $this->applyPeriodQueryConstraint($query);

        if (filled($this->tableSearch)) {
            $s = trim($this->tableSearch);
            $query->where(function ($q) use ($s) {
                $q->where('ticket_code', 'like', "%{$s}%")
                    ->orWhere('title', 'like', "%{$s}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$s}%"))
                    ->orWhere('pic_name', 'like', "%{$s}%");
            });
        }

        return $query->orderBy('ticket_code', 'asc')->get();
    }

    public function exportPdf(): StreamedResponse
    {
        return app(ArchiveReportExportService::class)->exportPdf(
            $this->getFilteredArchiveRecords(),
            $this->filterStatus,
            $this->periodLabel,
            $this->tableSearch
        );
    }

    public function exportCsv(): StreamedResponse
    {
        return app(ArchiveReportExportService::class)->exportXlsx(
            $this->getFilteredArchiveRecords(),
            $this->filterStatus,
            $this->periodLabel,
            $this->tableSearch
        );
    }

    public function selectSubmission(int $id): void
    {
        $this->selectedSubmissionId = $id;
        $this->fromNotification = false;
    }

    public function closeDetail(): void
    {
        $this->selectedSubmissionId = null;
        $this->fromNotification = false;
    }

    public function getSelectedSubmissionProperty(): ?Submission
    {
        if (! $this->selectedSubmissionId) {
            return null;
        }

        return Submission::with(['videotrons', 'user', 'statusHistories.user', 'revisions', 'materialDownloader'])->find($this->selectedSubmissionId);
    }

    public function getSubmissionsProperty(): LengthAwarePaginator
    {
        // Ketika dibuka dari notifikasi, tampilkan hanya 1 baris pengajuan yang dipilih
        if ($this->fromNotification && $this->selectedSubmissionId) {
            return Submission::query()
                ->with(['videotrons', 'user'])
                ->where('id', $this->selectedSubmissionId)
                ->paginate(1);
        }

        $query = Submission::query()->with(['videotrons', 'user'])->where(function ($q) {
            $q->whereIn('status', ['selesai', 'ditolak', 'dibatalkan'])
                ->orWhere(function ($sq) {
                    $sq->where('status', 'disetujui')
                        ->whereDate('end_date', '<', today());
                });
        });

        if (filled($this->filterStatus)) {
            if ($this->filterStatus === 'selesai') {
                $query->where(function ($sq) {
                    $sq->where('status', 'selesai')
                        ->orWhere(function ($ssq) {
                            $ssq->where('status', 'disetujui')
                                ->whereDate('end_date', '<', today());
                        });
                });
            } else {
                $query->where('status', $this->filterStatus);
            }
        }

        $this->applyPeriodQueryConstraint($query);

        if (filled($this->tableSearch)) {
            $s = trim($this->tableSearch);
            $query->where(function ($q) use ($s) {
                $q->where('ticket_code', 'like', "%{$s}%")
                    ->orWhere('title', 'like', "%{$s}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$s}%"))
                    ->orWhere('pic_name', 'like', "%{$s}%");
            });
        }

        return $query->latest('updated_at')->paginate($this->perPage);
    }
}
