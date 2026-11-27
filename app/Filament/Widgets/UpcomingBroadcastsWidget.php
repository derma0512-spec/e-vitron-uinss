<?php

namespace App\Filament\Widgets;

use App\Models\Submission;
use Filament\Widgets\Widget;

class UpcomingBroadcastsWidget extends Widget
{
    protected static bool $isLazy = false;

    protected static string $view = 'filament.widgets.upcoming-broadcasts-widget';

    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return auth()->user()?->isActiveAdmin() ?? false;
    }

    public function getViewData(): array
    {
        $today = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();

        $items = Submission::where('status', 'disetujui')
            ->where('end_date', '>=', $today)
            ->with(['videotrons:id,name'])
            ->orderByRaw('CASE WHEN start_date <= ? AND end_date >= ? THEN 0 ELSE 1 END', [$today, $today])
            ->orderBy('start_date', 'asc')
            ->take(3)
            ->get()
            ->map(function (Submission $sub) use ($today, $tomorrow) {
                $startDateStr = $sub->start_date?->toDateString();
                $endDateStr = $sub->end_date?->toDateString();

                $isCurrentlyLive = ($today >= $startDateStr && $today <= $endDateStr);
                $isTomorrow = ($startDateStr === $tomorrow);

                $statusType = match (true) {
                    $isCurrentlyLive => 'live',
                    $isTomorrow => 'tomorrow',
                    default => 'upcoming',
                };

                $statusLabel = match ($statusType) {
                    'live' => 'Sedang Tayang',
                    'tomorrow' => 'Mulai Besok',
                    default => 'Mulai '.$sub->start_date?->locale('id')->translatedFormat('d M'),
                };

                // Format tanggal tayang: misal "17 – 20 Okt 2026"
                $dateRange = '';
                if ($sub->start_date && $sub->end_date) {
                    if ($sub->start_date->format('Y-m') === $sub->end_date->format('Y-m')) {
                        $dateRange = $sub->start_date->format('d').' – '.$sub->end_date->locale('id')->translatedFormat('d M Y');
                    } else {
                        $dateRange = $sub->start_date->locale('id')->translatedFormat('d M').' – '.$sub->end_date->locale('id')->translatedFormat('d M Y');
                    }
                }

                $videotronNames = $sub->videotrons->pluck('name')->implode(', ');
                if (empty($videotronNames)) {
                    $videotronNames = 'Semua Layar';
                }

                return [
                    'id' => $sub->id,
                    'title' => $sub->title,
                    'organization' => $sub->organization ?: 'Umum',
                    'videotrons' => $videotronNames,
                    'date_range' => $dateRange,
                    'status_type' => $statusType,
                    'status_label' => $statusLabel,
                    'is_downloaded' => $sub->material_downloaded_at !== null,
                    'gdrive_link' => $sub->gdrive_link,
                    'url' => route('filament.admin.resources.submissions.index', ['selected' => $sub->id, 'from' => 'beranda']),
                ];
            });

        return [
            'broadcasts' => $items,
        ];
    }
}
