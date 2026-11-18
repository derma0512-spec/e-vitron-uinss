<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\SubmissionTrendChartWidget;
use App\Filament\Widgets\UpcomingBroadcastsWidget;
use App\Filament\Widgets\VideotronUsageChartWidget;
use App\Models\Submission;
use App\Models\Videotron;
use Filament\Pages\Dashboard as BaseDashboard;

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

        $stats = Submission::getDisplayStatusCounts();

        $deviceStats = Videotron::getOperationalStats();

        return [
            'stats' => $stats,
            'deviceStats' => $deviceStats,
            'videotrons' => Videotron::all(),
            'currentDate' => $now->locale('id')->translatedFormat('l, d F Y'),
            'currentTime' => $now->format('H:i:s').' WIB',
            'adminName' => auth()->user()?->name ?? 'Admin',
        ];
    }
}
