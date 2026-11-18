@php
    use Filament\Support\Facades\FilamentView;

    $color = $this->getColor();
    $heading = $this->getHeading();
    $description = $this->getDescription();
    $filters = $this->getFilters();
@endphp

<x-filament-widgets::widget class="fi-wi-chart">
    <x-filament::section>
        {{-- Widget Header & Period Filter --}}
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.75rem; min-height: 2.75rem; margin-bottom: 0.875rem;">
            <div style="min-width: 0; flex: 1 1 auto;">
                <h3 style="font-size: 0.875rem; font-weight: 700; color: #0F172A; margin: 0; line-height: 1.25; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ $heading }}
                </h3>
                <p style="font-size: 0.71875rem; color: #64748B; margin: 0.15rem 0 0; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ $description }}
                </p>
            </div>
            @if ($filters)
                <div
                    x-data="{ open: false }"
                    @click.outside="open = false"
                    @keydown.escape.window="open = false"
                    class="sipetron-filter-wrapper"
                >
                    <button
                        type="button"
                        @click="open = !open"
                        class="sipetron-filter-trigger"
                        :class="{ 'sipetron-filter-trigger-active': open }"
                        title="Pilih periode filter"
                    >
                        <svg class="sipetron-filter-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.75 2.75 0 0118 6.75v8.5A2.75 2.75 0 0115.25 18H4.75A2.75 2.75 0 012 15.25v-8.5A2.75 2.75 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z" clip-rule="evenodd" />
                        </svg>
                        <span class="sipetron-filter-label">
                            {{ $filters[$this->filter] ?? $this->filter }}
                        </span>
                        <svg
                            class="sipetron-filter-chevron"
                            :class="{ 'sipetron-chevron-rotate': open }"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div
                        x-show="open"
                        class="sipetron-filter-menu"
                        style="display: none;"
                    >
                        <div class="sipetron-filter-menu-label">
                            PILIH PERIODE
                        </div>
                        @foreach ($filters as $value => $label)
                            @php $isSelected = ($this->filter === $value); @endphp
                            <button
                                type="button"
                                @click="$wire.set('filter', '{{ $value }}'); open = false;"
                                class="sipetron-filter-item {{ $isSelected ? 'sipetron-filter-item-active' : '' }}"
                            >
                                <span>{{ $label }}</span>
                                @if ($isSelected)
                                    <svg class="sipetron-filter-check-icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div
            @if ($pollingInterval = $this->getPollingInterval())
                wire:poll.{{ $pollingInterval }}="updateChartData"
            @endif
        >
            <div
                wire:key="trend-chart-{{ $this->filter }}"
                @if (FilamentView::hasSpaMode())
                    x-load="visible"
                @else
                    x-load
                @endif
                x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('chart', 'filament/widgets') }}"
                wire:ignore
                x-data="chart({
                            cachedData: @js($this->getCachedData()),
                            options: @js($this->getOptions()),
                            type: @js($this->getType()),
                        })"
                @class([
                    match ($color) {
                        'gray' => null,
                        default => 'fi-color-custom',
                    },
                    is_string($color) ? "fi-color-{$color}" : null,
                ])
            >
                <canvas
                    x-ref="canvas"
                    @if ($maxHeight = $this->getMaxHeight())
                        style="max-height: {{ $maxHeight }}"
                    @endif
                ></canvas>

                <span
                    x-ref="backgroundColorElement"
                    @class([
                        match ($color) {
                            'gray' => 'text-gray-100 dark:text-gray-800',
                            default => 'text-custom-50 dark:text-custom-400/10',
                        },
                    ])
                    @style([
                        \Filament\Support\get_color_css_variables(
                            $color,
                            shades: [50, 400],
                            alias: 'widgets::chart-widget.background',
                        ) => $color !== 'gray',
                    ])
                ></span>

                <span
                    x-ref="borderColorElement"
                    @class([
                        match ($color) {
                            'gray' => 'text-gray-400',
                            default => 'text-custom-500 dark:text-custom-400',
                        },
                    ])
                    @style([
                        \Filament\Support\get_color_css_variables(
                            $color,
                            shades: [400, 500],
                            alias: 'widgets::chart-widget.border',
                        ) => $color !== 'gray',
                    ])
                ></span>

                <span
                    x-ref="gridColorElement"
                    class="text-gray-200 dark:text-gray-800"
                ></span>

                <span
                    x-ref="textColorElement"
                    class="text-gray-500 dark:text-gray-400"
                ></span>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
