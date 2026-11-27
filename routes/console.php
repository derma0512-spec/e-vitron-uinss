<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('sipetron:prune-storage', function () {
    $lifetimeMinutes = (int) config('session.lifetime', 120);
    $expiredSessionCutoff = now()->subMinutes(max(60, $lifetimeMinutes))->getTimestamp();

    $prunedSessions = 0;
    if (Schema::hasTable('sessions')) {
        $prunedSessions = DB::table('sessions')
            ->where('last_activity', '<', $expiredSessionCutoff)
            ->delete();
    }

    $prunedCache = 0;
    if (Schema::hasTable('cache')) {
        $prunedCache = DB::table('cache')
            ->where('expiration', '>', 0)
            ->where('expiration', '<', now()->getTimestamp())
            ->delete();
    }

    $prunedLocks = 0;
    if (Schema::hasTable('cache_locks')) {
        $prunedLocks = DB::table('cache_locks')
            ->where('expiration', '>', 0)
            ->where('expiration', '<', now()->getTimestamp())
            ->delete();
    }

    $prunedNotifications = 0;
    if (Schema::hasTable('notifications')) {
        $prunedNotifications = DB::table('notifications')
            ->where(function ($query): void {
                $query->where(function ($readQuery): void {
                    $readQuery->whereNotNull('read_at')
                        ->where('read_at', '<', now()->subDays(180));
                })->orWhere(function ($unreadQuery): void {
                    $unreadQuery->whereNull('read_at')
                        ->where('created_at', '<', now()->subDays(365));
                });
            })
            ->delete();
    }

    $this->info("Pruned {$prunedSessions} expired sessions, {$prunedCache} expired cache entries, {$prunedLocks} expired cache locks, and {$prunedNotifications} old notifications.");
})->purpose('Bersihkan baris sesi, cache kedaluwarsa, dan notifikasi lama agar database tetap ringan dalam jangka panjang');

Schedule::command('sipetron:prune-storage')->dailyAt('03:00');
