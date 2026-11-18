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

    $this->info("Pruned {$prunedSessions} expired sessions, {$prunedCache} expired cache entries, and {$prunedLocks} expired cache locks.");
})->purpose('Bersihkan baris sesi dan cache kedaluwarsa agar database tetap ringan dalam jangka panjang');

Schedule::command('sipetron:prune-storage')->dailyAt('03:00');
