<?php

namespace App\Providers;

use App\Http\Responses\FilamentLogoutResponse;
use App\Models\Submission;
use App\Observers\SubmissionObserver;
use Carbon\Carbon;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;
use Filament\Notifications\Livewire\Notifications;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once app_path('Support/helpers.php');

        $this->app->singleton(
            LogoutResponse::class,
            FilamentLogoutResponse::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(! $this->app->isProduction());

        app()->setLocale('id');
        Carbon::setLocale('id');
        Submission::observe(SubmissionObserver::class);

        Notifications::alignment(Alignment::Right);
        Notifications::verticalAlignment(VerticalAlignment::Start);

        RateLimiter::for('login', function (Request $request) {
            $identifier = (string) $request->input('identifier');

            return Limit::perMinute(5)->by($identifier ? $identifier.'|'.$request->ip() : $request->ip());
        });
    }
}
