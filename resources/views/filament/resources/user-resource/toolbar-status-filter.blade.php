@php
    $livewire = \Livewire\Livewire::current();
    $currentFilter = ($livewire && property_exists($livewire, 'statusFilter')) ? $livewire->statusFilter : 'all';

    $baseQuery = \App\Models\User::query()
        ->where('role', '!=', 'admin')
        ->where('auth_type', 'local');

    $rawCounts = (clone $baseQuery)
        ->selectRaw("COALESCE(account_status, 'approved') as status_key, COUNT(*) as total")
        ->groupBy('status_key')
        ->pluck('total', 'status_key');

    $totalAll = $rawCounts->sum();

    $statusOptions = [
        'all' => [
            'label' => 'Semua Status',
            'dot' => '#64748B',
            'count' => $totalAll,
        ],
        'pending' => [
            'label' => 'Menunggu Approve',
            'dot' => '#D97706',
            'count' => (int) ($rawCounts['pending'] ?? 0),
        ],
        'approved' => [
            'label' => 'Disetujui (Aktif)',
            'dot' => '#16A34A',
            'count' => (int) ($rawCounts['approved'] ?? 0),
        ],
        'inactive' => [
            'label' => 'Nonaktif',
            'dot' => '#64748B',
            'count' => (int) ($rawCounts['inactive'] ?? 0),
        ],
        'rejected' => [
            'label' => 'Ditolak',
            'dot' => '#DC2626',
            'count' => (int) ($rawCounts['rejected'] ?? 0),
        ],
    ];

    $currentStatusLabel = $statusOptions[$currentFilter]['label'] ?? 'Semua Status';
@endphp

<div
    x-data="{ open: false }"
    @click.outside="open = false"
    @keydown.escape.window="open = false"
    class="sipetron-user-status-filter"
    style="position: relative; width: 205px; flex-shrink: 0;"
>
    <button
        type="button"
        @click="open = !open"
        :class="{ 'is-open': open }"
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
            :style="open ? 'transform: rotate(180deg);' : 'transform: rotate(0deg);'"
        >
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </button>

    <div
        x-show="open"
        x-cloak
        class="sipetron-dropdown-panel sipetron-dropdown-panel-right"
        style="display: none;"
    >
        <div class="sipetron-dropdown-panel-header">
            PILIH STATUS AKUN
        </div>
        <div style="display: flex; flex-direction: column; gap: 2px;">
            @foreach($statusOptions as $key => $opt)
                @php
                    $isSelected = ($currentFilter === $key);
                @endphp
                <button
                    type="button"
                    wire:click="setStatusFilter('{{ $key }}')"
                    @click="open = false"
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
                        <span class="sipetron-dropdown-badge">{{ $opt['count'] }}</span>
                    </div>
                </button>
            @endforeach
        </div>
    </div>
</div>

