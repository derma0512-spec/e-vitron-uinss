<?php

namespace App\Filament\Widgets;

use App\Models\Videotron;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Collection;

class VideotronUsageChartWidget extends ChartWidget
{
    protected static string $view = 'filament.widgets.videotron-usage-chart';

    protected static ?string $heading = 'Statistik Penayangan per Videotron';

    protected static ?string $description = 'Jumlah penayangan terjadwal & selesai per titik layar';

    protected static ?string $maxHeight = '180px';

    protected static ?string $pollingInterval = null;

    protected static ?int $sort = 4;

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * @var Collection<int, object>|null
     */
    protected ?Collection $cachedUsageRecords = null;

    /**
     * @return Collection<int, object>
     */
    protected function getUsageRecords(): Collection
    {
        if ($this->cachedUsageRecords !== null) {
            return $this->cachedUsageRecords;
        }

        return $this->cachedUsageRecords = Videotron::query()
            ->leftJoin('submission_videotron', 'videotrons.id', '=', 'submission_videotron.videotron_id')
            ->leftJoin('submissions', function ($join) {
                $join->on('submission_videotron.submission_id', '=', 'submissions.id')
                    ->whereIn('submissions.status', ['disetujui', 'selesai']);
            })
            ->selectRaw('videotrons.id, videotrons.name, videotrons.location, COUNT(DISTINCT submissions.id) as total_usage')
            ->groupBy('videotrons.id', 'videotrons.name', 'videotrons.location')
            ->orderByDesc('total_usage')
            ->orderBy('videotrons.name')
            ->get();
    }

    protected function getViewData(): array
    {
        $records = $this->getUsageRecords();

        $topDevice = $records->first();
        $totalUsageSum = (int) $records->sum('total_usage');
        $totalDevices = $records->count();
        $avgUsage = $records->count() > 0 ? round($totalUsageSum / $records->count(), 1) : 0;

        return [
            'topDevice' => $topDevice,
            'totalUsageSum' => $totalUsageSum,
            'totalDevices' => $totalDevices,
            'avgUsage' => $avgUsage,
        ];
    }

    protected function getData(): array
    {
        $records = $this->getUsageRecords();

        if ($records->isEmpty()) {
            return [
                'datasets' => [
                    [
                        'label' => 'Total Penayangan',
                        'data' => [0],
                        'locations' => ['-'],
                        'backgroundColor' => '#0F3D2E',
                        'hoverBackgroundColor' => '#237A5C',
                    ],
                ],
                'labels' => ['Belum ada data'],
            ];
        }

        $labels = $records->pluck('name')->toArray();
        $locations = $records->pluck('location')->toArray();
        $data = $records->pluck('total_usage')->map(fn ($val) => (int) $val)->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Total Penayangan',
                    'data' => $data,
                    'locations' => $locations,
                    'backgroundColor' => '#0F3D2E',
                    'hoverBackgroundColor' => '#165641',
                    'borderColor' => 'transparent',
                    'borderWidth' => 0,
                    'borderRadius' => 5,
                    'borderSkipped' => false,
                    'maxBarThickness' => 19,
                    'categoryPercentage' => 0.8,
                    'barPercentage' => 0.88,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        $data = $this->getCachedData()['datasets'][0]['data'] ?? [];
        $maxVal = ! empty($data) ? max($data) : 3;
        $xMax = max($maxVal + 1, 4);

        return [
            'indexAxis' => 'y',
            'animation' => [
                'duration' => 750,
                'easing' => 'easeOutQuart',
            ],
            'layout' => [
                'padding' => [
                    'top' => 4,
                    'bottom' => 4,
                    'left' => 0,
                    'right' => 28,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'sipetronBarBadge' => [
                    'display' => true,
                ],
                'tooltip' => [
                    'enabled' => true,
                    'backgroundColor' => '#0F172A',
                    'titleFont' => ['size' => 12, 'weight' => 'bold'],
                    'bodyFont' => ['size' => 11],
                    'padding' => 8,
                    'cornerRadius' => 6,
                    'displayColors' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'display' => false,
                    'min' => 0,
                    'max' => $xMax,
                    'beginAtZero' => true,
                ],
                'y' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'border' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'color' => '#334155',
                        'font' => ['size' => 11, 'weight' => '600'],
                        'padding' => 6,
                    ],
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}
