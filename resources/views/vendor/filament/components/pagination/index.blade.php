@props([
    'currentPageOptionProperty' => 'tableRecordsPerPage',
    'extremeLinks' => false,
    'paginator',
    'pageOptions' => [],
])

@php
    use Illuminate\Contracts\Pagination\CursorPaginator;

    $isRtl = __('filament-panels::layout.direction') === 'rtl';
    $isSimple = ! $paginator instanceof \Illuminate\Pagination\LengthAwarePaginator;
    $totalRecords = $paginator->total();
    $curPage = $paginator->currentPage();
    $pPage = $paginator->perPage();
    $fromRec = $totalRecords > 0 ? ($curPage - 1) * $pPage + 1 : 0;
    $toRec = min($curPage * $pPage, $totalRecords);
    $currentVal = (int) ($this->{$currentPageOptionProperty} ?? 10);
@endphp

<nav
    aria-label="{{ __('filament::components/pagination.label') }}"
    role="navigation"
    {{
        $attributes->class([
            'fi-pagination flex items-center justify-between flex-wrap gap-3',
            'fi-simple' => $isSimple,
        ])
    }}
>
    {{-- Sisi Kiri: Tampilkan [ 10 v ] dari XX data (X–Y) persis seperti Pengajuan --}}
    <div class="sipetron-pagination-left flex items-center gap-2 text-[13px] text-slate-500">
        <span>Tampilkan</span>

        @if (count($pageOptions) > 1)
            <div x-data="{ open: false }" @keydown.escape.window="open = false" class="relative inline-block">
                <button
                    type="button"
                    @click="open = !open"
                    class="sipetron-perpage-trigger"
                    :class="{ 'is-open': open }"
                    title="Ubah jumlah data per halaman"
                >
                    <span x-text="$wire.get('{{ $currentPageOptionProperty }}') ?? {{ $currentVal }}">{{ $currentVal }}</span>
                    <svg
                        style="width: 12px; height: 12px; color: #94A3B8; flex-shrink: 0; transition: transform 150ms ease;"
                        :style="open ? 'transform: rotate(180deg);' : 'transform: rotate(0deg);'"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div
                    x-show="open"
                    x-cloak
                    @click.outside="open = false"
                    class="sipetron-perpage-menu"
                    style="display: none;"
                >
                    <div style="padding: 3px 6px 5px 6px; font-size: 10px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #F1F5F9; margin-bottom: 2px;">
                        Baris per hal
                    </div>
                    @foreach ($pageOptions as $option)
                        @if ($option !== 'all')
                            <button
                                type="button"
                                wire:click="$set('{{ $currentPageOptionProperty }}', {{ $option }})"
                                @click="open = false"
                                class="sipetron-perpage-item"
                                :class="{ 'is-selected': Number($wire.get('{{ $currentPageOptionProperty }}') ?? {{ $currentVal }}) === {{ $option }} }"
                            >
                                <span>{{ $option }} data</span>
                                <template x-if="Number($wire.get('{{ $currentPageOptionProperty }}') ?? {{ $currentVal }}) === {{ $option }}">
                                    <svg style="width: 13px; height: 13px; color: #0F3D2E; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                </template>
                            </button>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <span>dari <strong style="color: #0F172A; font-weight: 700;">{{ $totalRecords }}</strong> data</span>
        @if ($totalRecords > 0)
            <span style="color: #94A3B8; font-size: 0.75rem; font-weight: 500;">({{ $fromRec }}–{{ $toRec }})</span>
        @endif
    </div>

    {{-- Sisi Kanan: Pagination Buttons jika ada halaman > 1 --}}
    @if ((! $isSimple) && $paginator->hasPages())
        <ol
            class="fi-pagination-items justify-self-end rounded-lg bg-white shadow-sm ring-1 ring-gray-950/10 dark:bg-white/5 dark:ring-white/20"
        >
            @if (! $paginator->onFirstPage())
                @if ($extremeLinks)
                    <x-filament::pagination.item
                        :aria-label="__('filament::components/pagination.actions.first.label')"
                        :icon="$isRtl ? 'heroicon-m-chevron-double-right' : 'heroicon-m-chevron-double-left'"
                        :icon-alias="$isRtl ? 'pagination.first-button.rtl' : 'pagination.first-button'"
                        rel="first"
                        :wire:click="'gotoPage(1, \'' . $paginator->getPageName() . '\')'"
                        :wire:key="$this->getId() . '.pagination.first'"
                    />
                @endif

                <x-filament::pagination.item
                    :aria-label="__('filament::components/pagination.actions.previous.label')"
                    :icon="$isRtl ? 'heroicon-m-chevron-right' : 'heroicon-m-chevron-left'"
                    :icon-alias="$isRtl ? ['pagination.previous-button.rtl', 'pagination.previous-button'] : 'pagination.previous-button'"
                    rel="prev"
                    :wire:click="'previousPage(\'' . $paginator->getPageName() . '\')'"
                    :wire:key="$this->getId() . '.pagination.previous'"
                />
            @endif

            @foreach ($paginator->render()->offsetGet('elements') as $element)
                @if (is_string($element))
                    <x-filament::pagination.item disabled :label="$element" />
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <x-filament::pagination.item
                            :active="$page === $paginator->currentPage()"
                            :aria-label="trans_choice('filament::components/pagination.actions.go_to_page.label', $page, ['page' => $page])"
                            :label="$page"
                            :wire:click="'gotoPage(' . $page . ', \'' . $paginator->getPageName() . '\')'"
                            :wire:key="$this->getId() . '.pagination.' . $paginator->getPageName() . '.' . $page"
                        />
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <x-filament::pagination.item
                    :aria-label="__('filament::components/pagination.actions.next.label')"
                    :icon="$isRtl ? 'heroicon-m-chevron-left' : 'heroicon-m-chevron-right'"
                    :icon-alias="$isRtl ? ['pagination.next-button.rtl', 'pagination.next-button'] : 'pagination.next-button'"
                    rel="next"
                    :wire:click="'nextPage(\'' . $paginator->getPageName() . '\')'"
                    :wire:key="$this->getId() . '.pagination.next'"
                />

                @if ($extremeLinks)
                    <x-filament::pagination.item
                        :aria-label="__('filament::components/pagination.actions.last.label')"
                        :icon="$isRtl ? 'heroicon-m-chevron-double-left' : 'heroicon-m-chevron-double-right'"
                        :icon-alias="$isRtl ? 'pagination.last-button.rtl' : 'pagination.last-button'"
                        rel="last"
                        :wire:click="'gotoPage(' . $paginator->lastPage() . ', \'' . $paginator->getPageName() . '\')'"
                        :wire:key="$this->getId() . '.pagination.last'"
                    />
                @endif
            @endif
        </ol>
    @endif
</nav>
