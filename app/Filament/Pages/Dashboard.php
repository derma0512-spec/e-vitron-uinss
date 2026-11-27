<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\SubmissionTrendChartWidget;
use App\Filament\Widgets\UpcomingBroadcastsWidget;
use App\Filament\Widgets\VideotronUsageChartWidget;
use App\Models\Submission;
use App\Models\Videotron;
use App\Services\VideotronPlaybackService;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Database\Eloquent\Builder;

class Dashboard extends BaseDashboard
{
    protected static string $view = 'filament.pages.dashboard';

    protected static ?string $title = 'Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function getHeading(): string
    {
        $adminName = auth()->user()?->name ?? 'Admin E-VITRON';

        return "Selamat datang, {$adminName}";
    }

    public function getSubheading(): ?string
    {
        return 'Berikut ringkasan aktivitas penayangan videotron dan status permohonan terkini.';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getWidgets(): array
    {
        return [
            SubmissionTrendChartWidget::class,
            UpcomingBroadcastsWidget::class,
            VideotronUsageChartWidget::class,
        ];
    }

    public function getViewData(): array
    {
        $now = now();
        $playbackService = app(VideotronPlaybackService::class);
        $today = $playbackService->todayDate();

        $stats = Submission::getDisplayStatusCounts();

        $deviceStats = Videotron::getOperationalStats();

        $videotrons = Videotron::query()
            ->withCount([
                'submissions as active_broadcasts_count' => fn (Builder $query) => $playbackService->applyActiveBroadcastConstraints($query, $today),
            ])
            ->get();

        return [
            'stats' => $stats,
            'deviceStats' => $deviceStats,
            'videotrons' => $videotrons,
            'pendingTakedownCount' => $playbackService->countPendingTakedownSubmissions($today),
            'currentDate' => $now->locale('id')->translatedFormat('l, d F Y'),
            'currentTime' => $now->format('H:i:s').' WIB',
            'adminName' => auth()->user()?->name ?? 'Admin',
        ];
    }
}
