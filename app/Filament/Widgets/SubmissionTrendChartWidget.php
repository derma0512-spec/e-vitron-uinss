<?php

namespace App\Filament\Widgets;

use App\Models\Submission;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

class SubmissionTrendChartWidget extends ChartWidget
{
    private const PRIMARY_COLOR = '#0F3D2E';

    private const FILTER_SIX_MONTHS = '6_months';

    private const FILTER_THIRTY_DAYS = '30_days';

    private const FILTER_THIS_YEAR = 'this_year';

    public ?string $filter = self::FILTER_SIX_MONTHS;

    protected static ?string $maxHeight = '240px';

    protected static ?string $pollingInterval = null;

    protected static ?int $sort = 2;

    protected static string $view = 'filament.widgets.submission-trend-chart-widget';

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function getHeading(): string|Htmlable|null
    {
        return match ($this->filter) {
            self::FILTER_THIRTY_DAYS => 'Tren Pengajuan Harian',
            default => 'Tren Pengajuan per Bulan',
        };
    }

    public function getDescription(): string|Htmlable|null
    {
        $now = now();

        return match ($this->filter) {
            self::FILTER_THIRTY_DAYS => $now->copy()->subDays(29)->locale('id')->isoFormat('D MMM').' - '.
                $now->copy()->locale('id')->isoFormat('D MMM Y'),
            self::FILTER_THIS_YEAR => 'Jan - Des '.$now->year,
            default => $this->monthsAgoStart($now, 5)->locale('id')->isoFormat('MMM').' - '.
                $now->copy()->locale('id')->isoFormat('MMM Y'),
        };
    }

    /**
     * @return array<string, string>|null
     */
    protected function getFilters(): ?array
    {
        return [
            self::FILTER_SIX_MONTHS => '6 Bulan Terakhir',
            self::FILTER_THIRTY_DAYS => '30 Hari Terakhir',
            self::FILTER_THIS_YEAR => 'Tahun Ini',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $now = now();
        $isThirtyDays = ($this->filter === self::FILTER_THIRTY_DAYS);

        $series = match ($this->filter) {
            self::FILTER_THIRTY_DAYS => $this->getDailySeries($now),
            self::FILTER_THIS_YEAR => $this->getYearlySeries($now),
            default => $this->getSixMonthsSeries($now),
        };

        return [
            'datasets' => [
                $this->buildDataset($series['data'], $series['fullDates'], $isThirtyDays),
            ],
            'labels' => $series['labels'],
        ];
    }

    /**
     * Normalize to the 1st of the month before subtracting months to prevent end-of-month Carbon overflow (e.g. Mar 31 -> Mar 3).
     */
    public function monthsAgoStart(Carbon $reference, int $months): Carbon
    {
        return $reference->copy()->startOfMonth()->subMonths($months);
    }

    /**
     * Query daily aggregated totals via toBase() without Eloquent model hydration overhead.
     * Both MySQL and SQLite support DATE(created_at).
     *
     * @return array<string, int>
     */
    private function getDailyCounts(Carbon $start, Carbon $end): array
    {
        return Submission::query()
            ->whereBetween('created_at', [$start, $end])
            ->toBase()
            ->selectRaw('DATE(created_at) as day_date, COUNT(*) as total')
            ->groupBy('day_date')
            ->pluck('total', 'day_date')
            ->map(fn ($total) => (int) $total)
            ->all();
    }

    /**
     * Aggregate daily counts into year-month buckets (maximum 366 aggregated rows in memory).
     *
     * @return array<string, int>
     */
    private function getMonthlyCounts(Carbon $start, Carbon $end): array
    {
        $monthlyCounts = [];

        foreach ($this->getDailyCounts($start, $end) as $day => $total) {
            $ym = substr((string) $day, 0, 7);
            $monthlyCounts[$ym] = ($monthlyCounts[$ym] ?? 0) + $total;
        }

        return $monthlyCounts;
    }

    /**
     * @return array{labels: list<string>, data: list<int>, fullDates: list<string>}
     */
    private function getDailySeries(Carbon $now): array
    {
        $start = $now->copy()->subDays(29)->startOfDay();
        $end = $now->copy()->endOfDay();
        $counts = $this->getDailyCounts($start, $end);

        $labels = [];
        $data = [];
        $fullDates = [];

        for ($i = 29; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i);
            $key = $day->format('Y-m-d');
            $labels[] = $day->copy()->locale('id')->isoFormat('D MMM');
            $data[] = (int) ($counts[$key] ?? 0);
            $fullDates[] = $day->copy()->locale('id')->isoFormat('D MMMM Y');
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'fullDates' => $fullDates,
        ];
    }

    /**
     * @return array{labels: list<string>, data: list<int|null>, fullDates: list<string>}
     */
    private function getYearlySeries(Carbon $now): array
    {
        $start = $now->copy()->startOfYear();
        $end = $now->copy()->endOfYear();
        $counts = $this->getMonthlyCounts($start, $end);

        $currentMonth = (int) $now->month;
        $labels = [];
        $data = [];
        $fullDates = [];

        for ($month = 1; $month <= 12; $month++) {
            $date = $now->copy()->startOfYear()->month($month);
            $ym = $date->format('Y-m');
            $labels[] = $date->copy()->locale('id')->isoFormat('MMM');
            $fullDates[] = $date->copy()->locale('id')->isoFormat('MMMM Y');

            // Future months are set to null so Chart.js stops drawing the line at the current month instead of dropping to 0.
            $data[] = $month > $currentMonth ? null : (int) ($counts[$ym] ?? 0);
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'fullDates' => $fullDates,
        ];
    }

    /**
     * @return array{labels: list<string>, data: list<int>, fullDates: list<string>}
     */
    private function getSixMonthsSeries(Carbon $now): array
    {
        $start = $this->monthsAgoStart($now, 5)->startOfDay();
        $end = $now->copy()->endOfDay();
        $counts = $this->getMonthlyCounts($start, $end);

        $labels = [];
        $data = [];
        $fullDates = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = $this->monthsAgoStart($now, $i);
            $ym = $date->format('Y-m');
            $formattedMonth = $date->copy()->locale('id')->isoFormat('MMMM Y');

            $labels[] = $formattedMonth;
            $data[] = (int) ($counts[$ym] ?? 0);
            $fullDates[] = $formattedMonth;
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'fullDates' => $fullDates,
        ];
    }

    /**
     * @param  list<int|null>  $data
     * @param  list<string>  $fullDates
     * @return array<string, mixed>
     */
    private function buildDataset(array $data, array $fullDates, bool $sparsePoints): array
    {
        return [
            'label' => 'Jumlah Pengajuan',
            'data' => $data,
            'fullDates' => $fullDates,
            'fill' => 'start',
            'borderColor' => self::PRIMARY_COLOR,
            'borderWidth' => 2.5,
            'backgroundColor' => $sparsePoints ? 'rgba(15, 61, 46, 0.05)' : 'rgba(15, 61, 46, 0.06)',
            'tension' => $sparsePoints ? 0.25 : 0.3,
            'spanGaps' => false,
            'pointBackgroundColor' => self::PRIMARY_COLOR,
            'pointBorderColor' => '#FFFFFF',
            'pointBorderWidth' => 2,
            'pointRadius' => $sparsePoints
                ? array_map(fn (?int $v): float|int => ($v ?? 0) > 0 ? 3.5 : 0, $data)
                : 4.5,
            'pointHoverRadius' => $sparsePoints
                ? array_map(fn (?int $v): int => ($v ?? 0) > 0 ? 6 : 0, $data)
                : 6,
            'pointHitRadius' => $sparsePoints
                ? array_map(fn (?int $v): int => ($v ?? 0) > 0 ? 10 : 0, $data)
                : 10,
        ];
    }

    protected function getOptions(): RawJs
    {
        $maxTicks = $this->filter === self::FILTER_THIRTY_DAYS ? 8 : 12;

        return RawJs::make(<<<JS
        {
            animation: {
                duration: 750,
                easing: 'easeOutQuart'
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: true,
                    backgroundColor: '#0F172A',
                    bodyFont: { size: 12 },
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        title: function() {
                            return '';
                        },
                        label: function(context) {
                            const fullLabel = context.dataset?.fullDates?.[context.dataIndex] || context.label || '';
                            const val = context.raw !== undefined && context.raw !== null ? context.raw : 0;
                            return fullLabel + ': ' + val + ' pengajuan';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    },
                    grid: {
                        color: '#F1F5F9'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        autoSkip: true,
                        maxTicksLimit: {$maxTicks},
                        maxRotation: 0
                    }
                }
            }
        }
        JS);
    }
}
