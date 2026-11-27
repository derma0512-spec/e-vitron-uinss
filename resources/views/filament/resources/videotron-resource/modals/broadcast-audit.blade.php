@php
    $deviceCondition = match (true) {
        $videotron->condition === 'gangguan' => [
            'label' => 'Gangguan',
            'color' => '#DC2626',
            'bg' => '#FEF2F2',
            'border' => '#FECACA',
            'dot' => '#DC2626',
            'operational' => false,
        ],
        $videotron->condition === 'dalam_perbaikan' => [
            'label' => 'Perbaikan',
            'color' => '#B45309',
            'bg' => '#FFFBEB',
            'border' => '#FDE68A',
            'dot' => '#D97706',
            'operational' => false,
        ],
        ! $videotron->is_active => [
            'label' => 'Nonaktif',
            'color' => '#475569',
            'bg' => '#F1F5F9',
            'border' => '#CBD5E1',
            'dot' => '#94A3B8',
            'operational' => false,
        ],
        default => [
            'label' => 'Normal',
            'color' => '#047857',
            'bg' => '#ECFDF5',
            'border' => '#A7F3D0',
            'dot' => '#10B981',
            'operational' => true,
        ],
    };

    $orientationLabel = $videotron->orientationWithRatioLabel();
@endphp

<div style="display: flex; flex-direction: column; gap: 1rem; color: #1E293B;">

    {{-- 1. Ringkasan Teknis Perangkat (tanpa mengulang nama di header) --}}
    <div class="sipetron-info-card" style="padding: 0.75rem 0.875rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div style="width: 72px; height: 46px; border-radius: 0.5rem; overflow: hidden; border: 1px solid #E2E8F0; background: #E2E8F0; flex-shrink: 0;">
                <img src="{{ $videotron->image_url }}" alt="{{ $videotron->name }}" style="width: 100%; height: 100%; object-fit: cover; display: block;" />
            </div>
            <div style="min-width: 0; flex: 1;">
                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                    <span style="display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.6875rem; font-weight: 700; color: {{ $deviceCondition['color'] }}; background: {{ $deviceCondition['bg'] }}; border: 1px solid {{ $deviceCondition['border'] }}; padding: 0.1rem 0.5rem; border-radius: 9999px; line-height: 1.25;">
                        <span style="width: 6px; height: 6px; border-radius: 9999px; background: {{ $deviceCondition['dot'] }}; display: inline-block;"></span>
                        {{ $deviceCondition['label'] }}
                    </span>
                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.71875rem; font-weight: 600; color: #475569;">
                        <svg style="width: 13px; height: 13px; color: #94A3B8; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ $videotron->operating_hours }}</span>
                    </span>
                </div>
                <div style="font-size: 0.71875rem; color: #64748B; margin-top: 4px; line-height: 1.35;">
                    {{ $videotron->formattedResolution() }} &bull; {{ $orientationLabel }}
                </div>
            </div>
        </div>

        @if(! $deviceCondition['operational'])
            <div style="margin-top: 0.6rem; padding-top: 0.55rem; border-top: 1px dashed {{ $deviceCondition['border'] }}; font-size: 0.71875rem; color: {{ $deviceCondition['color'] }}; line-height: 1.45;">
                <strong>Perangkat berstatus {{ $deviceCondition['label'] }}.</strong>
                @if(filled($videotron->issue_notes))
                    {{ $videotron->issue_notes }}
                @else
                    Daftar di bawah menampilkan jadwal yang tercatat pada sistem.
                @endif
            </div>
        @endif
    </div>

    {{-- 2. Daftar Utama: Sedang Tayang Hari Ini (mengikuti .sipetron-info-card baku E-VITRON) --}}
    <div class="sipetron-info-card">
        <div class="sipetron-card-header">
            <div class="sipetron-card-title">
                <svg style="width: 15px; height: 15px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span>Sedang Tayang Hari Ini</span>
            </div>
            <span style="font-size: 0.6875rem; font-weight: 700; color: {{ $activeBroadcasts->isNotEmpty() ? '#0F3D2E' : '#64748B' }}; background: {{ $activeBroadcasts->isNotEmpty() ? '#E8F3EC' : '#F1F5F9' }}; padding: 2px 7px; border-radius: 6px;">
                {{ $activeBroadcasts->isNotEmpty() ? $activeBroadcasts->count().' Konten' : 'Kosong' }}
            </span>
        </div>

        @if($activeBroadcasts->isEmpty())
            <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.625rem; padding: 1.25rem 1rem; text-align: center;">
                <svg style="width: 24px; height: 24px; color: #94A3B8; margin: 0 auto 0.45rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <div style="font-size: 0.8125rem; font-weight: 700; color: #334155;">
                    Tidak ada konten terjadwal tayang hari ini
                </div>
                <div style="font-size: 0.71875rem; color: #64748B; margin-top: 0.25rem; line-height: 1.45;">
                    Jika ada konten yang tampil di layar fisik{{ $pendingTakedowns->isNotEmpty() ? ' di luar daftar belum diturunkan di bawah' : '' }}, berarti penayangan tidak melalui aplikasi.
                </div>
            </div>
        @else
            <div style="font-size: 0.71875rem; color: #64748B; margin-bottom: 0.625rem; line-height: 1.45;">
                Cocokkan daftar ini dengan layar fisik untuk memastikan seluruh tayangan terdaftar melalui aplikasi.
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.625rem;">
                @foreach($activeBroadcasts as $sub)
                    @php
                        $safeDriveUrl = preg_match('#^https?://#i', (string) $sub->gdrive_link) ? $sub->gdrive_link : null;
                        $isLastDay = $sub->end_date && $sub->end_date->toDateString() === $today;
                    @endphp
                    <div class="sipetron-doc-item" style="display: flex; flex-direction: column; gap: 0.55rem;">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.5rem;">
                            <div style="min-width: 0; flex: 1;">
                                <div style="font-size: 0.8125rem; font-weight: 800; color: #0F172A; line-height: 1.4;">
                                    {{ $sub->title }}
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap; margin-top: 0.35rem;">
                                    <span style="font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 0.6875rem; font-weight: 700; color: #0F3D2E; background: #E8F3EC; padding: 1px 6px; border-radius: 4px;">
                                        {{ $sub->ticket_code }}
                                    </span>
                                    @if($isLastDay)
                                        <span style="font-size: 0.65625rem; font-weight: 700; color: #B45309; background: #FEF3C7; padding: 1px 6px; border-radius: 4px;">
                                            Hari Terakhir
                                        </span>
                                    @endif
                                    @if(! $sub->isMaterialDownloaded())
                                        <span style="font-size: 0.65625rem; font-weight: 600; color: #64748B; background: #F1F5F9; padding: 1px 6px; border-radius: 4px;">
                                            Belum ditandai diunduh
                                        </span>
                                    @endif
                                </div>
                                <div style="font-size: 0.71875rem; color: #475569; margin-top: 0.35rem; line-height: 1.4;">
                                    {{ $sub->pic_name ?: ($sub->user?->name ?? 'Pengaju') }} &bull; {{ $sub->organization ?: 'Umum' }}
                                </div>
                            </div>

                            @if($sub->content_type === 'video')
                                <span style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.2rem 0.5rem; border-radius: 6px; font-size: 0.6875rem; font-weight: 700; background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; flex-shrink: 0;">
                                    <svg style="width: 12px; height: 12px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    <span>Video @if($sub->video_duration_seconds)({{ $sub->video_duration_seconds }}s)@endif</span>
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.2rem 0.5rem; border-radius: 6px; font-size: 0.6875rem; font-weight: 700; background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0; flex-shrink: 0;">
                                    <svg style="width: 12px; height: 12px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Poster</span>
                                </span>
                            @endif
                        </div>

                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; padding-top: 0.5rem; border-top: 1px dashed #E2E8F0; flex-wrap: wrap;">
                            <div style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.71875rem; color: #475569; font-weight: 600;">
                                <svg style="width: 13px; height: 13px; color: #94A3B8; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>{{ $sub->start_date?->locale('id')->translatedFormat('d M Y') }} – {{ $sub->end_date?->locale('id')->translatedFormat('d M Y') }}</span>
                            </div>

                            <div style="display: flex; align-items: center; gap: 0.35rem;">
                                @if($safeDriveUrl)
                                    <a
                                        href="{{ $safeDriveUrl }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="sipetron-chip-btn"
                                        title="Buka materi di Google Drive"
                                    >
                                        <span>Buka Drive</span>
                                        <svg style="width: 12px; height: 12px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                @endif
                                <a
                                    href="{{ route('filament.admin.resources.submissions.index', ['selected' => $sub->id]) }}"
                                    class="sipetron-chip-btn"
                                >
                                    <span>Detail</span>
                                    <svg style="width: 12px; height: 12px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- 3. Info Lipat Read-Only: Konten Resmi Habis Masa Tayang (mengikuti banner amber Riwayat & Rekap) --}}
    @if($pendingTakedowns->isNotEmpty())
        <div
            x-data="{ open: false }"
            style="background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 0.75rem; overflow: hidden;"
        >
            <button
                type="button"
                x-on:click="open = !open"
                class="sipetron-audit-accordion-trigger"
                style="width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; padding: 0.75rem 0.9rem; background: transparent; border: none; cursor: pointer; text-align: left;"
            >
                <span style="display: flex; align-items: center; gap: 0.6rem; min-width: 0;">
                    <span style="width: 24px; height: 24px; border-radius: 50%; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="14" height="14" style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    <span style="font-size: 0.75rem; font-weight: 700; color: #92400E; line-height: 1.35;">
                        {{ $pendingTakedowns->count() }} konten habis masa tayang belum diturunkan
                    </span>
                </span>
                <svg
                    class="sipetron-audit-chevron"
                    x-bind:class="{ 'is-open': open }"
                    width="14"
                    height="14"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div
                x-show="open"
                x-cloak
                style="padding: 0.75rem 0.9rem 0.85rem; border-top: 1px dashed #FCD34D; display: flex; flex-direction: column; gap: 0.625rem;"
            >
                @foreach($pendingTakedowns as $expired)
                    @php
                        $todayStart = now()->timezone('Asia/Jakarta')->startOfDay();
                        $endStart = $expired->end_date?->copy()->timezone('Asia/Jakarta')->startOfDay();
                        $daysOverdue = ($endStart && $endStart->lt($todayStart))
                            ? (int) $endStart->diffInDays($todayStart)
                            : null;
                    @endphp
                    <div style="padding: 0.7rem 0.85rem; background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.5rem; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;">
                        <div style="min-width: 0; flex: 1;">
                            <div style="display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">
                                <span style="font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 0.65625rem; font-weight: 700; color: #0F3D2E; background: #E8F3EC; padding: 1px 5px; border-radius: 4px;">
                                    {{ $expired->ticket_code }}
                                </span>
                                @if($expired->end_date)
                                    <span style="font-size: 0.65625rem; font-weight: 600; color: #B45309;">
                                        Berakhir {{ $expired->end_date->locale('id')->translatedFormat('d M Y') }}@if($daysOverdue !== null) ({{ $daysOverdue }} hari lalu)@endif
                                    </span>
                                @endif
                            </div>
                            <div style="font-size: 0.78125rem; font-weight: 700; color: #0F172A; margin-top: 6px; line-height: 1.4;">
                                {{ $expired->title }}
                            </div>
                            <div style="font-size: 0.6875rem; color: #64748B; margin-top: 4px; line-height: 1.4;">
                                {{ $expired->pic_name ?: ($expired->user?->name ?? 'Pengaju') }} &bull; {{ $expired->organization ?: 'Umum' }}
                            </div>
                        </div>
                        <a
                            href="{{ \App\Filament\Pages\HistoryAndRecap::getUrl(['selected' => $expired->id]) }}"
                            class="sipetron-chip-btn"
                            style="flex-shrink: 0;"
                        >
                            <span>Detail</span>
                            <svg width="12" height="12" style="width: 12px; height: 12px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                @endforeach

                <div style="display: flex; justify-content: flex-end; padding-top: 0.45rem;">
                    <a href="{{ $historyPendingUrl }}" class="sipetron-audit-footer-link">
                        <span>Kelola semua di Riwayat &amp; Rekap</span>
                        <svg width="12" height="12" style="width: 12px; height: 12px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    @endif

</div>
