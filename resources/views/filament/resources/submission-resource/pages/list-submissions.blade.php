<x-filament-panels::page>
    {{-- Stylesheet Layout & Komponen Kelola Pengajuan --}}
    @include('filament.resources.submission-resource.partials.styles-layout')
    @include('filament.resources.submission-resource.partials.styles-components')

    {{-- Search & Filter Toolbar + Tabel Utama dalam 1 Container (Tanpa Gap Bawaan Filament, Sama Persis dengan Riwayat & Rekap) --}}
    @php
        $counts = $this->statusCounts;
        $hasActiveFilter = $this->statusFilter !== 'all' || $this->dateFilter !== 'all' || !empty(trim($this->search));
    @endphp
    <div class="sipetron-submissions-container" style="width: 100%;" @sipetron:new-notification-arrived.window="if (!$wire.selectedSubmissionId) { $wire.$refresh(); }">
    <div 
        x-data="{ activeDropdown: null }" 
        @click.outside="activeDropdown = null" 
        @keydown.escape.window="activeDropdown = null"
        style="display: flex; gap: 0.75rem; margin-bottom: 1rem; flex-wrap: wrap; align-items: center; width: 100%;"
    >
        {{-- 1. Search Input (Sistem, Logika & Animasi Sama Persis dengan Kelola Videotron) --}}
        <div 
            x-data="{
                search: @js($this->search ?? ''),
                isClearing: false,
                clearStartTime: 0,
                init() {
                    if (this.$wire && typeof this.$watch === 'function') {
                        this.$watch('$wire.search', (value) => {
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
                        this.$wire.set('search', '');
                    }

                    // Fallback pengaman agar spinner tidak pernah macet
                    setTimeout(() => {
                        this.isClearing = false;
                    }, 2500);
                }
            }"
            class="sipetron-search-box-wrap"
            style="flex: 1 1 260px; min-width: 0; position: relative;"
        >
            <div style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94A3B8; pointer-events: none; display: flex; align-items: center; z-index: 2;">
                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <input 
                x-ref="input"
                type="text" 
                wire:model.live.debounce.300ms="search" 
                x-on:input="search = $event.target.value"
                x-on:keyup="search = $event.target.value"
                x-on:keydown.escape="if (search) { clear(); }"
                placeholder="Cari kode tiket, judul konten, atau nama pengaju..."
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
                class="sipetron-search-clear-btn {{ filled($this->search) ? 'sipetron-search-has-query' : '' }}"
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

        {{-- 2. Custom Status Filter Dropdown (Alpine.js - Fokus Antrean Aktif) --}}
        @php
            $statusOptions = [
                'all' => ['label' => 'Semua Status', 'dot' => '#64748B', 'count' => $counts['all'] ?? 0],
                'menunggu' => ['label' => 'Perlu Direview', 'dot' => '#D97706', 'count' => $counts['menunggu'] ?? 0],
                'revisi' => ['label' => 'Menunggu Perbaikan', 'dot' => '#0284C7', 'count' => $counts['revisi'] ?? 0],
                'tayang' => ['label' => 'Sedang Tayang', 'dot' => '#16A34A', 'count' => $counts['tayang'] ?? 0],
                'terjadwal' => ['label' => 'Terjadwal', 'dot' => '#0D9488', 'count' => $counts['terjadwal'] ?? 0],
            ];
            $currentStatusLabel = $statusOptions[$this->statusFilter]['label'] ?? 'Semua Status';
        @endphp
        <div style="position: relative; flex: 0 1 200px; min-width: 175px;">
            <button 
                type="button" 
                @click="activeDropdown = (activeDropdown === 'status' ? null : 'status')"
                :class="{ 'is-open': activeDropdown === 'status' }"
                class="sipetron-dropdown-trigger"
            >
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

            {{-- Floating Dropdown Panel --}}
            <div 
                x-show="activeDropdown === 'status'" 
                x-cloak
                class="sipetron-dropdown-panel"
                style="display: none;"
            >
                <div class="sipetron-dropdown-panel-header">
                    PILIH STATUS
                </div>
                <div style="display: flex; flex-direction: column; gap: 2px;">
                    @foreach($statusOptions as $key => $opt)
                        @php
                            $isSelected = ($this->statusFilter === $key);
                        @endphp
                        <button 
                            type="button" 
                            wire:click="setStatusFilter('{{ $key }}')" 
                            @click="activeDropdown = null"
                            class="sipetron-dropdown-item {{ $isSelected ? 'is-selected' : '' }}"
                        >
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

        {{-- 3. Custom Date Filter Dropdown (Alpine.js) --}}
        @php
            $dateOptions = [
                'all' => 'Semua Tanggal',
                'today' => 'Diajukan Hari Ini',
                'this_week' => 'Diajukan Minggu Ini',
                'this_month' => 'Diajukan Bulan Ini',
            ];
            $currentDateLabel = $dateOptions[$this->dateFilter] ?? 'Semua Tanggal';
        @endphp
        <div style="position: relative; flex: 0 1 250px; min-width: 220px;">
            <button 
                type="button" 
                @click="activeDropdown = (activeDropdown === 'date' ? null : 'date')"
                :class="{ 'is-open': activeDropdown === 'date' }"
                class="sipetron-dropdown-trigger"
            >
                <div style="display: flex; align-items: center; gap: 10px; min-width: 0; flex: 1;">
                    <svg style="width: 18px; height: 18px; color: #94A3B8; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 500;">
                        {{ $currentDateLabel }}
                    </span>
                </div>

                <div 
                    style="display: flex; align-items: center; justify-content: center; width: 16px; height: 16px; color: #94A3B8; flex-shrink: 0; transition: transform 150ms ease;"
                    :style="activeDropdown === 'date' ? 'transform: rotate(180deg);' : 'transform: rotate(0deg);'"
                >
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </button>

            {{-- Floating Dropdown Panel --}}
            <div 
                x-show="activeDropdown === 'date'" 
                x-cloak
                class="sipetron-dropdown-panel sipetron-dropdown-panel-right"
                style="display: none;"
            >
                <div class="sipetron-dropdown-panel-header">
                    PILIH RENTANG WAKTU
                </div>
                <div style="display: flex; flex-direction: column; gap: 2px;">
                    @foreach($dateOptions as $key => $label)
                        @php
                            $isSelected = ($this->dateFilter === $key);
                        @endphp
                        <button 
                            type="button" 
                            wire:click="setDateFilter('{{ $key }}')" 
                            @click="activeDropdown = null"
                            class="sipetron-dropdown-item {{ $isSelected ? 'is-selected' : '' }}"
                        >
                            <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $label }}</span>
                            @if($isSelected)
                                <svg style="width: 16px; height: 16px; color: #0F3D2E; flex-shrink: 0; margin-left: auto;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- 4. Tombol Reset Filter (Sama Persis dengan Riwayat & Rekap) --}}
        <button 
            type="button" 
            wire:click="resetFilters" 
            @click="activeDropdown = null"
            class="sipetron-reset-btn {{ $hasActiveFilter ? 'is-active' : '' }}" 
            title="{{ $hasActiveFilter ? 'Bersihkan semua filter aktif' : 'Filter dalam keadaan default' }}"
        >
            <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span>Reset</span>
        </button>
    </div>

    @php
        $selected = $this->selectedSubmission;
        $submissions = $this->submissions;
    @endphp


    {{-- Tabel Utama Daftar Pengajuan --}}
    @include('filament.resources.submission-resource.partials.submissions-table')
    </div>

    {{-- DRAWER OVERLAY: Detail Pengajuan (Slide-Over Sisi Kanan) --}}
    @if($selected)
        @php
            $detailKey = $selected->display_status;
            $detailBadge = match($detailKey) {
                'menunggu' => ['bg' => '#FEF3C7', 'dot' => '#D97706', 'text' => '#B45309', 'label' => 'Perlu Direview'],
                'revisi' => ['bg' => '#E0F2FE', 'dot' => '#0284C7', 'text' => '#0369A1', 'label' => 'Menunggu Perbaikan'],
                'terjadwal' => ['bg' => '#CCFBF1', 'dot' => '#0D9488', 'text' => '#0F766E', 'label' => 'Terjadwal'],
                'tayang' => ['bg' => '#DCFCE7', 'dot' => '#16A34A', 'text' => '#15803D', 'label' => 'Sedang Tayang'],
                'selesai' => ['bg' => '#EEF2FF', 'dot' => '#6366F1', 'text' => '#4338CA', 'label' => 'Selesai'],
                'ditolak' => ['bg' => '#FEE2E2', 'dot' => '#DC2626', 'text' => '#B91C1C', 'label' => 'Ditolak Admin'],
                'dibatalkan' => ['bg' => '#F1F5F9', 'dot' => '#94A3B8', 'text' => '#475569', 'label' => 'Dibatalkan Pengaju'],
                default => ['bg' => '#F1F5F9', 'dot' => '#64748B', 'text' => '#475569', 'label' => ucfirst($detailKey)],
            };
            $hasMaterialStage = in_array($selected->status, ['disetujui', 'selesai'], true);
            $latestRevision = $selected->revisions->first();
            $safeDriveUrl = preg_match('#^https?://#i', (string) $selected->gdrive_link) ? $selected->gdrive_link : null;
            $picDisplayName = $selected->pic_name ?? $selected->user?->name ?? 'Pengaju';
            $picInitials = collect(explode(' ', trim($picDisplayName)))->filter()->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
            if (empty($picInitials)) {
                $picInitials = 'PG';
            }
            
            $cleanPhone = preg_replace('/[^0-9]/', '', (string)$selected->whatsapp);
            if (str_starts_with($cleanPhone, '0')) {
                $waNumber = '62' . substr($cleanPhone, 1);
            } else {
                $waNumber = $cleanPhone;
            }
            $waMessage = rawurlencode("Halo Kak {$picDisplayName}, kami dari Tim Admin E-VITRON UIN Siber Syekh Nurjati Cirebon ingin mengonfirmasi terkait pengajuan videotron tiket {$selected->ticket_code} ({$selected->title})...");
        @endphp
        @include('filament.resources.submission-resource.partials.drawer-header-status')
    @endif
</x-filament-panels::page>