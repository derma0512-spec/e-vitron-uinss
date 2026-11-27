<x-filament-panels::page>
    {{-- Stylesheet Toolbar, Tabel & Drawer Riwayat dan Rekap --}}
    @include('filament.pages.partials.history-and-recap.styles-toolbar-table')
    @include('filament.pages.partials.history-and-recap.styles-drawer-pagination')

    @php
        $stats = $this->recapStats;
        $submissions = $this->submissions;
        $filteredCount = ($this->fromNotification && $this->selectedSubmissionId) ? $this->totalFilteredCount : $submissions->total();
        $hasActiveFilter = filled($tableSearch) || filled($filterStatus) || $periodPreset !== 'all';
    @endphp

    <div class="sipetron-history-container" style="width: 100%;">
        
        {{-- 1. Toolbar Pencarian & Filter Periode/Arsip --}}
        {{-- 1. TOOLBAR FILTER MENGAMBANG (SAMA PERSIS DENGAN PENGAJUAN) --}}
        <div class="sipetron-filter-toolbar" x-data="{ activeDropdown: null }" @click.outside="activeDropdown = null" @keydown.escape.window="activeDropdown = null">
            
            {{-- 1. Search Box (Sistem, Logika & Animasi Spinner Sama Persis dengan Pengajuan & Kelola Videotron) --}}
            <div 
                x-data="{
                    search: @js($tableSearch ?? ''),
                    isClearing: false,
                    clearStartTime: 0,
                    init() {
                        if (this.$wire && typeof this.$watch === 'function') {
                            this.$watch('$wire.tableSearch', (value) => {
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
                            this.$wire.set('tableSearch', '');
                        }

                        // Fallback pengaman agar spinner tidak pernah macet
                        setTimeout(() => {
                            this.isClearing = false;
                        }, 2500);
                    }
                }"
                class="sipetron-search-wrap sipetron-search-box-wrap"
            >
                <div class="sipetron-search-icon">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <input 
                    x-ref="input"
                    type="text" 
                    wire:model.live.debounce.300ms="tableSearch" 
                    x-on:input="search = $event.target.value"
                    x-on:keyup="search = $event.target.value"
                    x-on:keydown.escape="if (search) { clear(); }"
                    placeholder="Cari kode tiket, judul kegiatan, atau nama pengaju..." 
                    class="sipetron-search-input"
                    autocomplete="off" 
                />

                {{-- Tombol Hapus Kata Kunci (X) yang bertransisi jadi mikro spinner saat diklik --}}
                <button
                    type="button"
                    x-show="Boolean(search && String(search).trim().length > 0) || isClearing"
                    x-cloak
                    x-on:click="if (!isClearing) { clear(); activeDropdown = null; }"
                    x-bind:class="{
                        'sipetron-clearing-active': isClearing,
                        'sipetron-search-has-query': Boolean(search && String(search).trim().length > 0) || isClearing
                    }"
                    title="Hapus kata kunci pencarian"
                    class="sipetron-search-clear-btn {{ filled($tableSearch) ? 'sipetron-search-has-query' : '' }}"
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

            {{-- 2. Dropdown Filter Status (Harmonisasi dengan Pengajuan) --}}
            @php
                $statusOptions = [
                    '' => ['label' => 'Semua Status', 'dot' => '#64748B', 'count' => $stats['total'] ?? 0],
                    'perlu_diturunkan' => ['label' => 'Perlu Diturunkan', 'dot' => '#D97706', 'count' => $stats['pending_takedown'] ?? 0],
                    'selesai' => ['label' => 'Selesai Tayang', 'dot' => '#16A34A', 'count' => $stats['completed'] ?? 0],
                    'ditolak' => ['label' => 'Ditolak Admin', 'dot' => '#DC2626', 'count' => $stats['rejected'] ?? 0],
                    'dibatalkan' => ['label' => 'Dibatalkan Pengaju', 'dot' => '#94A3B8', 'count' => $stats['canceled'] ?? 0],
                ];
                $currentStatusLabel = $statusOptions[$filterStatus]['label'] ?? 'Semua Status';
            @endphp
            <div class="sipetron-dropdown-wrap" @click.outside="if (activeDropdown === 'status') activeDropdown = null">
                <button type="button" 
                        @click.stop="activeDropdown = (activeDropdown === 'status' ? null : 'status')" 
                        :class="{ 'is-open': activeDropdown === 'status' }"
                        class="sipetron-dropdown-trigger">
                    <div style="display: flex; align-items: center; gap: 10px; min-width: 0; flex: 1;">
                        <svg style="width: 18px; height: 18px; color: #94A3B8; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 500;">
                            {{ $currentStatusLabel }}
                        </span>
                    </div>
                    <div 
                        style="display: flex; align-items: center; justify-content: center; width: 16px; height: 16px; color: #94A3B8; flex-shrink: 0; transition: transform 150ms ease;"
                        :style="activeDropdown === 'status' ? 'transform: rotate(180deg);' : 'transform: rotate(0deg);'"
                    >
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>

                <div x-show="activeDropdown === 'status'" 
                     x-cloak
                     @click.stop
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     class="sipetron-dropdown-panel" 
                     style="display: none;">
                    <div class="sipetron-dropdown-panel-header">PILIH STATUS</div>
                    <div style="display: flex; flex-direction: column; gap: 2px;">
                        @foreach($statusOptions as $key => $opt)
                            @php
                                $isSelected = ((string)$filterStatus === (string)$key);
                            @endphp
                            <button type="button" 
                                    wire:click="$set('filterStatus', '{{ $key }}')" 
                                    @click="activeDropdown = null" 
                                    class="sipetron-dropdown-item {{ $isSelected ? 'is-selected' : '' }}">
                                <div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
                                    <span style="width: 8px; height: 8px; border-radius: 9999px; background: {{ $opt['dot'] }}; flex-shrink: 0;"></span>
                                    <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $opt['label'] }}</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0; margin-left: auto;">
                                    @if($isSelected)
                                        <svg style="width: 15px; height: 15px; color: #0F3D2E; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @endif
                                    <span class="sipetron-dropdown-badge">
                                        {{ $opt['count'] }}
                                    </span>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- 3. Dropdown Preset Periode (Daftar Bergaya Sama seperti Status) --}}
            @php
                $now = now();
                $thisMonthLabel = 'Bulan Ini (' . \Carbon\Carbon::now()->locale('id')->isoFormat('MMMM') . ')';
                $lastMonthLabel = 'Bulan Lalu (' . \Carbon\Carbon::now()->subMonth()->locale('id')->isoFormat('MMMM') . ')';
                $thisYearLabel  = 'Tahun Ini (' . $now->year . ')';
                $isActivePeriod = $periodPreset !== 'all';
                $availYears = $this->availableYears;
                $indoMonthsArr = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                ];
            @endphp
            <div class="sipetron-dropdown-wrap sipetron-dropdown-wrap-wide"
                 x-data="{
                    defaultYear: '',
                    archiveYear: @js($filterYear ?? ''),
                    archiveMonth: @js($filterMonth ?? ''),
                    applyCustomArchive() {
                        $wire.setPeriodPreset('custom', this.archiveYear, this.archiveMonth);
                        activeDropdown = null;
                    },
                    resetLocalArchive() {
                        this.archiveYear = '';
                        this.archiveMonth = '';
                    },
                    handleEnterKey(event) {
                        if (activeDropdown !== 'period') return;
                        if (event.target && event.target.closest && event.target.closest('.sipetron-dropdown-item')) {
                            return;
                        }
                        event.preventDefault();
                        event.stopPropagation();
                        this.applyCustomArchive();
                    },
                    init() {
                        this.$watch('$wire.periodPreset', (val) => {
                            if (val === 'all') {
                                this.resetLocalArchive();
                            }
                        });
                        this.$watch('$wire.filterYear', (val) => {
                            this.archiveYear = val ? String(val) : '';
                        });
                        this.$watch('$wire.filterMonth', (val) => {
                            this.archiveMonth = val ? String(val) : '';
                        });
                    }
                 }"
                 @click.outside="if (activeDropdown === 'period') activeDropdown = null"
                 @reset-archive-filter.window="resetLocalArchive()"
                 @keydown.window.enter="handleEnterKey($event)">
                <button type="button"
                        @click.stop="activeDropdown = (activeDropdown === 'period' ? null : 'period')"
                        :class="{ 'is-open': activeDropdown === 'period' }"
                        class="sipetron-dropdown-trigger">
                    <div style="display: flex; align-items: center; gap: 10px; min-width: 0; flex: 1;">
                        <svg style="width: 18px; height: 18px; color: #94A3B8; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 500;">
                            {{ $this->periodLabel }}
                        </span>
                    </div>
                    <div
                        style="display: flex; align-items: center; justify-content: center; width: 16px; height: 16px; color: #94A3B8; flex-shrink: 0; transition: transform 150ms ease;"
                        :style="activeDropdown === 'period' ? 'transform: rotate(180deg);' : 'transform: rotate(0deg)'"
                    >
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>

                <div x-show="activeDropdown === 'period'"
                     x-cloak
                     @click.stop
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     class="sipetron-dropdown-panel"
                     style="display: none; min-width: 280px; width: 280px;">

                    <div class="sipetron-dropdown-panel-header">PILIH PERIODE</div>
                    <div style="display: flex; flex-direction: column; gap: 2px;">

                        {{-- Semua Periode --}}
                        <button type="button"
                                wire:click="setPeriodPreset('all')"
                                @click="activeDropdown = null; resetLocalArchive()"
                                class="sipetron-dropdown-item {{ $periodPreset === 'all' ? 'is-selected' : '' }}">
                            <span>Semua Periode</span>
                            @if($periodPreset === 'all')
                                <svg style="width: 15px; height: 15px; color: #0F3D2E; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            @endif
                        </button>

                        {{-- Bulan Ini --}}
                        <button type="button"
                                wire:click="setPeriodPreset('this_month')"
                                @click="activeDropdown = null"
                                class="sipetron-dropdown-item {{ $periodPreset === 'this_month' ? 'is-selected' : '' }}">
                            <span>{{ $thisMonthLabel }}</span>
                            @if($periodPreset === 'this_month')
                                <svg style="width: 15px; height: 15px; color: #0F3D2E; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            @endif
                        </button>

                        {{-- Bulan Lalu --}}
                        <button type="button"
                                wire:click="setPeriodPreset('last_month')"
                                @click="activeDropdown = null"
                                class="sipetron-dropdown-item {{ $periodPreset === 'last_month' ? 'is-selected' : '' }}">
                            <span>{{ $lastMonthLabel }}</span>
                            @if($periodPreset === 'last_month')
                                <svg style="width: 15px; height: 15px; color: #0F3D2E; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            @endif
                        </button>

                        {{-- Tahun Ini --}}
                        <button type="button"
                                wire:click="setPeriodPreset('this_year')"
                                @click="activeDropdown = null"
                                class="sipetron-dropdown-item {{ $periodPreset === 'this_year' ? 'is-selected' : '' }}">
                            <span>{{ $thisYearLabel }}</span>
                            @if($periodPreset === 'this_year')
                                <svg style="width: 15px; height: 15px; color: #0F3D2E; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            @endif
                        </button>

                        {{-- Garis Pemisah --}}
                        <div style="border-top: 1px solid #F1F5F9; margin: 8px 0 6px 0;"></div>

                        {{-- Header Bagian Arsip Spesifik --}}
                        <div class="sipetron-dropdown-panel-header" style="border-bottom: none; margin-bottom: 4px; padding: 2px 4px 4px 4px;">
                            ARSIP SPESIFIK
                        </div>

                        {{-- Form Arsip Langsung (Tahun & Bulan Grid) --}}
                        <div style="padding: 0 4px 4px 4px;" wire:ignore>
                            {{-- Pilih Tahun --}}
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 8px;">
                                <label style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.04em;">Tahun</label>
                                <div style="position: relative; width: 140px;">
                                    <select x-model="archiveYear" class="sipetron-year-select">
                                        <option value="">Semua Tahun</option>
                                        @foreach($availYears as $yearOption)
                                            <option value="{{ $yearOption }}">{{ $yearOption }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Pilih Bulan Header & Tombol "Semua" --}}
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 6px;">
                                <label style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.04em;">Bulan</label>
                                <button type="button"
                                        @click="archiveMonth = ''"
                                        :class="{ 'is-active-month-pill': archiveMonth === '' }"
                                        class="sipetron-month-pill-all">
                                    Semua Bulan
                                </button>
                            </div>

                            {{-- Grid 12 Bulan (4 Kolom x 3 Baris) --}}
                            @php
                                $shortMonths = [
                                    1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                                    5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Ags',
                                    9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
                                ];
                            @endphp
                            <div class="sipetron-month-grid">
                                @foreach($shortMonths as $mNum => $mLabel)
                                    <button type="button"
                                            @click="archiveMonth = '{{ $mNum }}'"
                                            :class="{ 'is-active-month-pill': archiveMonth == '{{ $mNum }}' }"
                                            class="sipetron-month-pill">
                                        {{ $mLabel }}
                                    </button>
                                @endforeach
                            </div>

                            {{-- Tombol Terapkan Arsip --}}
                            <button type="button"
                                    @click="applyCustomArchive()"
                                    class="sipetron-period-btn-apply"
                                    style="width: 100%; height: 34px; font-size: 12.5px; border-radius: 7px; justify-content: center; margin-top: 2px;">
                                <span>Terapkan Arsip</span>
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            {{-- 4. Tombol Reset Filter --}}
            <button type="button" 
                    wire:click="resetFilters" 
                    @click="activeDropdown = null; $dispatch('reset-archive-filter'); window.dispatchEvent(new CustomEvent('reset-archive-filter'));"
                    class="sipetron-reset-btn {{ $hasActiveFilter ? 'is-active' : '' }}" 
                    title="{{ $hasActiveFilter ? 'Bersihkan semua filter aktif' : 'Filter dalam keadaan default' }}">
                <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>Reset</span>
            </button>
        </div>

        {{-- 2. Tabel Daftar Riwayat & Drawer Detail Arsip --}}
        @include('filament.pages.partials.history-and-recap.table-and-drawer')
    </div>
</x-filament-panels::page>