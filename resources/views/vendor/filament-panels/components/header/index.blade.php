@props([
    'actions' => [],
    'breadcrumbs' => [],
    'heading',
    'subheading' => null,
])

@php
    $headingStr = is_string($heading) ? $heading : strip_tags((string) $heading);
    $headingLower = strtolower($headingStr);
    
    $badge = match(true) {
        request()->routeIs('filament.admin.pages.dashboard') || str_contains($headingLower, 'dashboard') || str_contains($headingLower, 'selamat datang') => 'Pusat Kendali Administrasi',
        str_contains($headingLower, 'pengajuan') => 'Manajemen Pengajuan',
        str_contains($headingLower, 'videotron') || str_contains($headingLower, 'perangkat') => 'Manajemen Perangkat',
        str_contains($headingLower, 'riwayat') || str_contains($headingLower, 'rekap') || str_contains($headingLower, 'laporan') => 'Arsip & Laporan',
        default => 'Panel Administrasi',
    };

    try {
        $opStats = \App\Models\Videotron::getOperationalStats();
        $deviceTotal = $opStats['total'];
        $deviceActive = $opStats['active'];
    } catch (\Throwable $e) {
        $deviceTotal = 5;
        $deviceActive = 5;
    }

    $now = now();
    $currentDate = $now->locale('id')->isoFormat('dddd, D MMMM Y');
    $currentTime = $now->format('H:i:s') . ' WIB';
@endphp

<header
    {{ $attributes->class(['fi-header sipetron-header-row']) }}
>
    <div class="sipetron-header-info">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
            <span class="sipetron-header-badge">
                {{ $badge }}
            </span>
        </div>

        <h1 class="fi-header-heading sipetron-header-title">
            {{ $heading }}
        </h1>

        @if ($subheading)
            <p class="fi-header-subheading sipetron-header-subtitle">
                {{ $subheading }}
            </p>
        @endif
    </div>

    <div class="sipetron-header-actions">
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_HEADER_ACTIONS_BEFORE, scopes: isset($this) && method_exists($this, 'getRenderHookScopes') ? $this->getRenderHookScopes() : null) }}

        @if ($actions)
            <div class="sipetron-header-page-actions flex items-center gap-2">
                <x-filament::actions :actions="$actions" />
            </div>
        @endif

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_HEADER_ACTIONS_AFTER, scopes: isset($this) && method_exists($this, 'getRenderHookScopes') ? $this->getRenderHookScopes() : null) }}

        {{-- Status Operasional Pill --}}
        <div class="sipetron-header-pill">
            <div class="sipetron-pill-icon sipetron-pill-icon-green">
                <span class="sipetron-pulse-container">
                    <span class="sipetron-pulse-ping"></span>
                    <span class="sipetron-pulse-dot"></span>
                </span>
            </div>
            <div class="sipetron-pill-text">
                <span class="sipetron-pill-title">{{ $deviceActive }}/{{ $deviceTotal }} Layar Aktif</span>
                <span class="sipetron-pill-subtitle">Status Operasional</span>
            </div>
        </div>

        {{-- Tanggal & Waktu (Live Realtime WIB) --}}
        <div
            class="sipetron-header-pill"
            x-data="{
                dateStr: '{{ $currentDate }}',
                timeStr: '{{ $currentTime }}',
                init() {
                    const updateClock = () => {
                        const now = new Date();
                        this.dateStr = now.toLocaleDateString('id-ID', {
                            timeZone: 'Asia/Jakarta',
                            weekday: 'long',
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        });
                        const parts = new Intl.DateTimeFormat('en-GB', {
                            timeZone: 'Asia/Jakarta',
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit',
                            hour12: false
                        }).formatToParts(now);
                        const h = parts.find(p => p.type === 'hour')?.value || '00';
                        const m = parts.find(p => p.type === 'minute')?.value || '00';
                        const s = parts.find(p => p.type === 'second')?.value || '00';
                        this.timeStr = `${h}:${m}:${s} WIB`;
                    };
                    updateClock();
                    setInterval(updateClock, 1000);
                }
            }"
        >
            <div class="sipetron-pill-icon sipetron-pill-icon-calendar">
                <svg style="width: 1.05rem; height: 1.05rem; color: #0F3D2E;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="sipetron-pill-text">
                <span class="sipetron-pill-date" x-text="dateStr">{{ $currentDate }}</span>
                <span class="sipetron-pill-time" x-text="timeStr">{{ $currentTime }}</span>
            </div>
        </div>
    </div>
</header>
