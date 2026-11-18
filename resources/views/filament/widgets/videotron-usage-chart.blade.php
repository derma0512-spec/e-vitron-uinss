@php
    use Filament\Support\Facades\FilamentView;

    $color = $this->getColor();
    $heading = $this->getHeading();
    $description = $this->getDescription();
    $topDevice = $topDevice ?? null;
    $totalUsageSum = $totalUsageSum ?? 0;
    $totalDevices = $totalDevices ?? 5;
    $avgUsage = $avgUsage ?? 0;
@endphp

<x-filament-widgets::widget class="fi-wi-chart" style="height: 100%;">
    <div class="sipetron-card-box sipetron-usage-widget" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 1rem; padding: 1.25rem 1.35rem; height: 100%; display: flex; flex-direction: column; box-sizing: border-box; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
        {{-- Header Card --}}
        <div style="margin-bottom: 0.35rem; flex-shrink: 0;">
            <h3 style="font-size: 0.9375rem; font-weight: 700; color: #0F172A; margin: 0; line-height: 1.25;">
                {{ $heading }}
            </h3>
            <p style="font-size: 0.75rem; color: #64748B; margin: 0.2rem 0 0 0; line-height: 1.3;">
                {{ $description }}
            </p>
        </div>

        {{-- Chart Canvas Container --}}
        <div style="flex: 1 1 auto; display: flex; align-items: center; width: 100%; min-height: 175px; margin: 0.25rem 0;">
            <div
                x-load
                x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('chart', 'filament/widgets') }}"
                wire:ignore
                x-data="chart({
                            cachedData: @js($this->getCachedData()),
                            options: @js($this->getOptions()),
                            type: @js($this->getType()),
                        })"
                style="width: 100%; height: 175px;"
            >
                <canvas
                    x-ref="canvas"
                    style="height: 175px; max-height: 175px; width: 100%;"
                ></canvas>

                <span x-ref="backgroundColorElement" class="text-custom-50 dark:text-custom-400/10"></span>
                <span x-ref="borderColorElement" class="text-custom-500 dark:text-custom-400"></span>
                <span x-ref="gridColorElement" class="text-gray-200 dark:text-gray-800"></span>
                <span x-ref="textColorElement" class="text-gray-500 dark:text-gray-400"></span>
            </div>
        </div>

        {{-- Summary Cards --}}
        <style>
            .sipetron-usage-summary-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 0.625rem;
            }
            .sipetron-summary-card {
                cursor: default;
                user-select: none;
                border-radius: 0.75rem;
                padding: 0.65rem 0.8rem;
                display: flex;
                align-items: center;
                gap: 0.65rem;
                min-width: 0;
                transition: transform 220ms cubic-bezier(0.16, 1, 0.3, 1), box-shadow 220ms ease, border-color 220ms ease, background-color 220ms ease;
            }
            .sipetron-summary-card:hover {
                transform: translateY(-2px);
            }
            .sipetron-summary-card-active {
                background: #F0FDF4;
                border: 1px solid #DCFCE7;
            }
            .sipetron-summary-card-active:hover {
                border-color: #86EFAC !important;
                background-color: #F7FEFA !important;
                box-shadow: 0 6px 16px -2px rgba(22, 163, 74, 0.12), 0 2px 6px -1px rgba(0, 0, 0, 0.03) !important;
            }
            .sipetron-summary-card-total {
                background: #F8FAF9;
                border: 1px solid #E2E8F0;
            }
            .sipetron-summary-card-total:hover {
                border-color: #CBD5E1 !important;
                background-color: #FFFFFF !important;
                box-shadow: 0 6px 16px -2px rgba(15, 23, 42, 0.06), 0 2px 6px -1px rgba(0, 0, 0, 0.03) !important;
            }
            .sipetron-summary-icon {
                transition: transform 220ms cubic-bezier(0.34, 1.56, 0.64, 1);
            }
            .sipetron-summary-card:hover .sipetron-summary-icon {
                transform: scale(1.08);
            }
            @media (max-width: 639px) {
                .sipetron-usage-summary-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
        <div style="margin-top: 0.45rem; padding-top: 0.6rem; border-top: 1px solid #E2E8F0; flex-shrink: 0;">
            <div class="sipetron-usage-summary-grid">
                {{-- Most Active Videotron --}}
                <div class="sipetron-summary-card sipetron-summary-card-active" title="{{ $topDevice?->name ?? 'Videotron Masjid' }} ({{ $topDevice?->total_usage ?? 0 }} Penayangan)">
                    <div class="sipetron-summary-icon" style="width: 2.35rem; height: 2.35rem; border-radius: 0.5rem; background: #DCFCE7; color: #15803D; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg style="width: 1.2rem; height: 1.2rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125Z" />
                        </svg>
                    </div>
                    <div style="min-width: 0; flex: 1;">
                        <span style="font-size: 0.65625rem; font-weight: 700; color: #15803D; text-transform: uppercase; letter-spacing: 0.04em; display: block; line-height: 1.2;">Titik Teraktif</span>
                        <div style="font-size: 0.8125rem; font-weight: 700; color: #0F172A; line-height: 1.25; margin-top: 0.125rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $topDevice?->name ?? 'Videotron Masjid' }}
                        </div>
                        <div style="font-size: 0.6875rem; color: #16A34A; font-weight: 700; line-height: 1.2; margin-top: 0.125rem; display: flex; align-items: center; gap: 0.25rem;">
                            <span class="sipetron-status-pulse" style="width: 0.35rem; height: 0.35rem; border-radius: 9999px; background: #16A34A; display: inline-block;"></span>
                            {{ $topDevice?->total_usage ?? 0 }}x Penayangan
                        </div>
                    </div>
                </div>

                {{-- Total Broadcasts --}}
                <div class="sipetron-summary-card sipetron-summary-card-total" title="Total akumulasi {{ $totalUsageSum ?? 0 }} penayangan videotron">
                    <div class="sipetron-summary-icon" style="width: 2.35rem; height: 2.35rem; border-radius: 0.5rem; background: #E8F3EC; border: 1px solid #D1E5DA; color: #0F3D2E; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg style="width: 1.2rem; height: 1.2rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                        </svg>
                    </div>
                    <div style="min-width: 0; flex: 1;">
                        <span style="font-size: 0.65625rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.04em; display: block; line-height: 1.2;">Total Penayangan</span>
                        <div style="font-size: 0.8125rem; font-weight: 700; color: #0F172A; line-height: 1.25; margin-top: 0.125rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <span style="font-weight: 800; font-size: 0.9375rem;">{{ $totalUsageSum ?? 0 }}</span> Penayangan
                        </div>
                        <div style="font-size: 0.6875rem; color: #64748B; font-weight: 600; line-height: 1.2; margin-top: 0.125rem; display: flex; align-items: center; gap: 0.25rem;">
                            <span style="width: 0.35rem; height: 0.35rem; border-radius: 9999px; background: #94A3B8; display: inline-block;"></span>
                            Tersebar di {{ $totalDevices ?? 5 }} titik layar
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
