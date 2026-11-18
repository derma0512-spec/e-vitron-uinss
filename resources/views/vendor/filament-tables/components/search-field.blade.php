@php
    use Illuminate\View\ComponentAttributeBag;

    $livewire = $this ?? \Livewire\Livewire::current();
    $searchTerm = '';
    if ($livewire) {
        if (method_exists($livewire, 'getTableSearch')) {
            $searchTerm = (string) ($livewire->getTableSearch() ?? '');
        } elseif (isset($livewire->tableSearch)) {
            $searchTerm = (string) ($livewire->tableSearch ?? '');
        }
    }
    if (blank($searchTerm) && filled(request('tableSearch'))) {
        $searchTerm = (string) request('tableSearch');
    }
@endphp

@props([
    'debounce' => '300ms',
    'onBlur' => false,
    'placeholder' => __('filament-tables::table.fields.search.placeholder'),
    'wireModel' => 'tableSearch',
])

<div
    x-id="['input']"
    x-data="{
        search: @js($searchTerm),
        isClearing: false,
        clearStartTime: 0,
        init() {
            if (this.$wire && typeof this.$watch === 'function') {
                this.$watch('$wire.{{ $wireModel }}', (value) => {
                    this.search = value || '';
                    if (this.$refs.input && this.$refs.input.value !== (value || '')) {
                        this.$refs.input.value = value || '';
                    }
                });
            }

            // Dengarkan saat request Livewire selesai memuat seluruh data tabel
            window.addEventListener('sipetron:livewire-done', () => {
                if (!this.isClearing) return;
                const elapsed = Date.now() - (this.clearStartTime || 0);
                const delay = Math.max(0, 400 - elapsed);
                setTimeout(() => {
                    this.isClearing = false;
                }, delay);
            });
        },
        clear() {
            if (!this.search && !(this.$refs.input && this.$refs.input.value)) {
                return;
            }
            this.clearStartTime = Date.now();
            this.isClearing = true;
            this.search = '';
            if (this.$refs.input) {
                this.$refs.input.value = '';
                this.$refs.input.focus();
            }
            if (this.$wire && typeof this.$wire.set === 'function') {
                this.$wire.set('{{ $wireModel }}', '');
            }

            // Fallback pengaman agar spinner tidak pernah macet
            setTimeout(() => {
                this.isClearing = false;
            }, 2500);
        }
    }"
    {{ $attributes->class(['fi-ta-search-field sipetron-search-box-wrap']) }}
    style="position: relative; width: 380px; max-width: 100%;"
>
    <label x-bind:for="$id('input')" class="sr-only">
        {{ __('filament-tables::table.fields.search.label') }}
    </label>

    <div style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94A3B8; pointer-events: none; display: flex; align-items: center; z-index: 2;">
        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    </div>

    <input
        x-ref="input"
        x-bind:id="$id('input')"
        type="text"
        wire:model.live.debounce.300ms="{{ $wireModel }}"
        x-on:input="search = $event.target.value"
        x-on:keyup="search = $event.target.value"
        x-on:keydown.escape="if (search) { clear(); }"
        placeholder="{{ $placeholder }}"
        class="sipetron-search-input"
        autocomplete="off"
    />

    {{-- Tombol Hapus Kata Kunci (X) yang bertransisi jadi mikro spinner saat diklik --}}
    <button
        type="button"
        x-show="Boolean(search && String(search).trim().length > 0) || isClearing"
        x-cloak
        x-on:click="if (!isClearing) clear()"
        x-bind:class="{
            'sipetron-clearing-active': isClearing,
            'sipetron-search-has-query': Boolean(search && String(search).trim().length > 0) || isClearing
        }"
        title="Hapus kata kunci pencarian"
        class="sipetron-search-clear-btn {{ filled($searchTerm) ? 'sipetron-search-has-query' : '' }}"
        tabindex="-1"
    >
        {{-- Ikon Silang (X) saat standby --}}
        <svg
            x-show="!isClearing"
            class="sipetron-close-icon-svg"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
        </svg>

        {{-- Mikro Spinner saat tombol ini diklik --}}
        <svg
            x-show="isClearing"
            x-cloak
            class="animate-spin sipetron-search-spinner-svg"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            <circle cx="12" cy="12" r="9" stroke="#CBD5E1" stroke-width="2.5"></circle>
            <path d="M12 3a9 9 0 0 1 9 9" stroke="#0F3D2E" stroke-width="2.5" stroke-linecap="round"></path>
        </svg>
    </button>
</div>
