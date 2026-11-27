<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Resources\VideotronResource\Pages\ListVideotrons;
use App\Filament\Widgets\SubmissionTrendChartWidget;
use App\Filament\Widgets\UpcomingBroadcastsWidget;
use App\Filament\Widgets\VideotronUsageChartWidget;
use App\Http\Middleware\FilamentAuthenticate;
use App\Http\Middleware\SecurityHeaders;
use Filament\FontProviders\LocalFontProvider;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\View\TablesRenderHook;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('E-VITRON')
            ->brandLogo(fn () => view('filament.brand-logo'))
            ->brandLogoHeight('2.5rem')
            ->sidebarWidth('15.5rem')
            ->darkMode(false)
            ->font(
                'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
                provider: LocalFontProvider::class
            )
            ->colors([
                'primary' => Color::hex('#0F3D2E'),
                'warning' => Color::hex('#D9A441'),
            ])
            ->maxContentWidth(MaxWidth::Full)
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => view('filament.custom-styles')
            )
            ->renderHook(
                PanelsRenderHook::SIDEBAR_FOOTER,
                fn () => view('filament.sidebar-footer')
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_START,
                fn () => view('filament.resources.videotron-resource.toolbar-title'),
                scopes: ListVideotrons::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_START,
                fn () => view('filament.resources.user-resource.toolbar-title'),
                scopes: ListUsers::class,
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('filament.resources.user-resource.toolbar-status-filter'),
                scopes: ListUsers::class,
            )
            ->databaseNotifications(isLazy: false)
            ->databaseNotificationsPolling('10s')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->widgets([
                SubmissionTrendChartWidget::class,
                UpcomingBroadcastsWidget::class,
                VideotronUsageChartWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SecurityHeaders::class,
            ])
            ->authMiddleware([
                FilamentAuthenticate::class,
            ]);
    }
}
