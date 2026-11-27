    {{-- Status KPI Cards --}}
    <div class="sipetron-grid-stats">
        {{-- Card 1: Perlu Direview --}}
        <a href="{{ route('filament.admin.resources.submissions.index', ['statusFilter' => 'menunggu']) }}" class="sipetron-stat-card" title="Lihat pengajuan yang perlu direview">
            <div class="sipetron-stat-icon" style="background: #FEF3C7; color: #D97706; box-shadow: 0 2px 6px rgba(217, 119, 6, 0.15);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="sipetron-stat-content">
                <span class="sipetron-stat-label">Perlu Direview</span>
                <span class="sipetron-stat-value" data-count="{{ $stats['menunggu'] }}">{{ $stats['menunggu'] }}</span>
                <span class="sipetron-stat-desc">Menunggu keputusan</span>
            </div>
            <span class="sipetron-stat-arrow">&rsaquo;</span>
        </a>

        {{-- Card 2: Menunggu Perbaikan --}}
        <a href="{{ route('filament.admin.resources.submissions.index', ['statusFilter' => 'revisi']) }}" class="sipetron-stat-card" title="Lihat pengajuan menunggu perbaikan">
            <div class="sipetron-stat-icon" style="background: #E0F2FE; color: #0284C7; box-shadow: 0 2px 6px rgba(2, 132, 199, 0.15);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </div>
            <div class="sipetron-stat-content">
                <span class="sipetron-stat-label">Menunggu Perbaikan</span>
                <span class="sipetron-stat-value" data-count="{{ $stats['revisi'] }}">{{ $stats['revisi'] }}</span>
                <span class="sipetron-stat-desc">Menunggu pengaju</span>
            </div>
            <span class="sipetron-stat-arrow">&rsaquo;</span>
        </a>

        {{-- Card 3: Terjadwal --}}
        <a href="{{ route('filament.admin.resources.submissions.index', ['statusFilter' => 'terjadwal']) }}" class="sipetron-stat-card" title="Lihat pengajuan terjadwal">
            <div class="sipetron-stat-icon" style="background: #CCFBF1; color: #0D9488; box-shadow: 0 2px 6px rgba(13, 148, 136, 0.15);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="sipetron-stat-content">
                <span class="sipetron-stat-label">Terjadwal</span>
                <span class="sipetron-stat-value" data-count="{{ $stats['terjadwal'] }}">{{ $stats['terjadwal'] }}</span>
                <span class="sipetron-stat-desc">Siap tayang</span>
            </div>
            <span class="sipetron-stat-arrow">&rsaquo;</span>
        </a>

        {{-- Card 4: Sedang Tayang --}}
        <a href="{{ route('filament.admin.resources.submissions.index', ['statusFilter' => 'tayang']) }}" class="sipetron-stat-card" title="Lihat konten sedang tayang">
            <div class="sipetron-stat-icon" style="background: #DCFCE7; color: #16A34A; box-shadow: 0 2px 6px rgba(22, 163, 74, 0.15);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="sipetron-stat-content">
                <span class="sipetron-stat-label">Sedang Tayang</span>
                <span class="sipetron-stat-value" data-count="{{ $stats['tayang'] }}">{{ $stats['tayang'] }}</span>
                <span class="sipetron-stat-desc">Aktif di layar</span>
            </div>
            <span class="sipetron-stat-arrow">&rsaquo;</span>
        </a>

        {{-- Card 5: Selesai Tayang --}}
        <a href="{{ route('filament.admin.pages.history-and-recap', ['filterStatus' => 'selesai']) }}" class="sipetron-stat-card" title="Lihat arsip pengajuan selesai tayang">
            <div class="sipetron-stat-icon" style="background: #EEF2FF; color: #4F46E5; box-shadow: 0 2px 6px rgba(79, 70, 229, 0.15);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="sipetron-stat-content">
                <span class="sipetron-stat-label">Selesai Tayang</span>
                <span class="sipetron-stat-value" data-count="{{ $stats['selesai'] }}">{{ $stats['selesai'] }}</span>
                <span class="sipetron-stat-desc">Tuntas ditayangkan</span>
            </div>
            <span class="sipetron-stat-arrow">&rsaquo;</span>
        </a>
    </div>

    {{-- Banner Pengingat Tipis: Hanya Tampil Jika Ada Video Habis Masa Tayang Belum Diturunkan --}}
    @if(($pendingTakedownCount ?? 0) > 0)
        <a
            href="{{ route('filament.admin.pages.history-and-recap', ['filterStatus' => 'perlu_diturunkan']) }}"
            class="sipetron-takedown-bar"
        >
            <div style="display: flex; align-items: center; gap: 0.75rem; min-width: 0; flex: 1 1 280px;">
                <div class="sipetron-stat-icon" style="width: 2.15rem; height: 2.15rem; min-width: 2.15rem; background: #FEF3C7; color: #D97706; box-shadow: 0 2px 6px rgba(217, 119, 6, 0.15);">
                    <svg style="width: 1.05rem; height: 1.05rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div style="min-width: 0;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        <span style="font-size: 0.875rem; font-weight: 700; color: #0F172A; line-height: 1.25;">
                            Pengingat Penurunan Konten
                        </span>
                        <span style="font-size: 0.6875rem; font-weight: 700; color: #B45309; background: #FEF3C7; border: 1px solid #FDE68A; padding: 0.08rem 0.55rem; border-radius: 9999px; line-height: 1.3; white-space: nowrap;">
                            {{ $pendingTakedownCount }} Video
                        </span>
                    </div>
                    <div style="font-size: 0.75rem; color: #64748B; margin-top: 0.12rem; line-height: 1.35;">
                        Masa tayang telah berakhir dan belum dikonfirmasi diturunkan dari layar videotron fisik.
                    </div>
                </div>
            </div>
            <span
                class="sipetron-btn-secondary"
                style="flex-shrink: 0; white-space: nowrap;"
            >
                <span>Buka Checklist</span>
                <span class="arrow-right">&rsaquo;</span>
            </span>
        </a>
    @endif

    {{-- Baris 2: Tren Pengajuan (Kiri, 1.8) & Jadwal Tayang Terdekat (Kanan, 1.2) --}}
    <div class="sipetron-grid-charts">
        <div style="height: 100%; min-width: 0;">
            @livewire(\App\Filament\Widgets\SubmissionTrendChartWidget::class, key('submission-trend-chart-widget'))
        </div>
        <div style="height: 100%; min-width: 0;">
            @livewire(\App\Filament\Widgets\UpcomingBroadcastsWidget::class, key('upcoming-broadcasts-widget'))
        </div>
    </div>

    {{-- Baris 3: Penggunaan per Videotron (Kiri, 1/2) & Kondisi Perangkat Videotron (Kanan, 1/2) --}}
    <div class="sipetron-grid-bottom">
        <div style="height: 100%; min-width: 0;">
            @livewire(\App\Filament\Widgets\VideotronUsageChartWidget::class, key('videotron-usage-chart-widget'))
        </div>

        <div class="sipetron-card-box">
            {{-- Header Card --}}
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.875rem; gap: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.625rem; min-width: 0; flex: 1 1 auto;">
                    <div class="sipetron-header-device-icon" style="width: 2.25rem; height: 2.25rem; border-radius: 0.5rem; background: #E8F3EC; color: #0F3D2E; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div style="min-width: 0;">
                        <h2 style="font-size: 1rem; font-weight: 700; color: #0F172A; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Kondisi Perangkat Videotron</h2>
                        <span style="font-size: 0.75rem; color: #64748B; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Monitoring kesiapan layar di lingkungan kampus</span>
                    </div>
                </div>
                <a href="{{ route('filament.admin.resources.videotrons.index') }}" class="sipetron-btn-secondary" style="flex-shrink: 0; white-space: nowrap;">
                    <span>Kelola Videotron</span>
                    <span class="arrow-right">&rsaquo;</span>
                </a>
            </div>

            {{-- Primary Device Indicators --}}
            <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.75rem; margin-bottom: 0.75rem;">
                {{-- Total Devices --}}
                <div class="sipetron-indicator-card sipetron-indicator-card-total" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 0.75rem 0.875rem; display: flex; align-items: center; gap: 0.75rem; min-width: 0;">
                    <div class="sipetron-indicator-icon" style="width: 2.35rem; height: 2.35rem; border-radius: 0.5rem; background: #F1F5F9; border: 1px solid #E2E8F0; color: #475569; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div style="min-width: 0;">
                        <span style="font-size: 0.65625rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.04em; display: block; white-space: nowrap;">Total Perangkat</span>
                        <div style="display: flex; align-items: baseline; gap: 0.35rem; flex-wrap: wrap;">
                            <span style="font-size: 1.45rem; font-weight: 800; color: #0F172A; line-height: 1;">{{ $deviceStats['total'] }}</span>
                            <span style="font-size: 0.75rem; font-weight: 600; color: #64748B; white-space: nowrap;">Titik Layar</span>
                        </div>
                    </div>
                </div>

                {{-- Operational Status --}}
                @if($deviceStats['troubled'] == 0)
                    <div class="sipetron-indicator-card sipetron-indicator-card-status-normal" style="background: #F0FDF4; border: 1px solid #DCFCE7; border-radius: 0.75rem; padding: 0.75rem 0.875rem; display: flex; align-items: center; gap: 0.75rem; min-width: 0;">
                        <div class="sipetron-indicator-icon" style="width: 2.35rem; height: 2.35rem; border-radius: 0.5rem; background: #DCFCE7; color: #16A34A; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div style="min-width: 0;">
                            <span style="font-size: 0.65625rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.04em; display: block; white-space: nowrap;">Status Operasional</span>
                            <div style="display: flex; align-items: baseline; gap: 0.35rem; flex-wrap: wrap;">
                                <span style="font-size: 1.15rem; font-weight: 800; color: #16A34A; line-height: 1.1;">100% Normal</span>
                            </div>
                            <span style="font-size: 0.625rem; color: #64748B; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Semua layar siap tayang</span>
                        </div>
                    </div>
                @else
                    <div class="sipetron-indicator-card sipetron-indicator-card-status-troubled" style="background: #FEF2F2; border: 1px solid #FEE2E2; border-radius: 0.75rem; padding: 0.75rem 0.875rem; display: flex; align-items: center; gap: 0.75rem; min-width: 0;">
                        <div class="sipetron-indicator-icon" style="width: 2.35rem; height: 2.35rem; border-radius: 0.5rem; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div style="min-width: 0;">
                            <span style="font-size: 0.65625rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.04em; display: block; white-space: nowrap;">Perlu Perhatian</span>
                            <div style="display: flex; align-items: baseline; gap: 0.35rem; flex-wrap: wrap;">
                                <span style="font-size: 1.45rem; font-weight: 800; color: #DC2626; line-height: 1;">{{ $deviceStats['troubled'] }}</span>
                                <span style="font-size: 0.75rem; font-weight: 600; color: #B91C1C; white-space: nowrap;">Kendala</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Device Condition Breakdown --}}
            <div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 0.5rem; background: #F8FAF9; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 0.4rem 0.375rem; margin-bottom: 0.75rem;">
                <div class="sipetron-status-pill sipetron-status-pill-active" title="Jumlah layar operasional aktif">
                    <span style="display: inline-flex; align-items: center; justify-content: center; gap: 0.25rem; font-size: 0.65625rem; font-weight: 700; color: #16A34A; white-space: nowrap;">
                        <span class="sipetron-status-pulse" style="width: 0.375rem; height: 0.375rem; border-radius: 9999px; background: #16A34A; flex-shrink: 0;"></span>
                        Aktif
                    </span>
                    <span class="sipetron-status-pill-val" style="font-size: 0.8125rem; font-weight: 800; color: {{ $deviceStats['active'] > 0 ? '#16A34A' : '#94A3B8' }}; display: block; margin-top: 0.0625rem;">{{ $deviceStats['active'] }}</span>
                </div>

                <div class="sipetron-status-pill sipetron-status-pill-repair" title="Jumlah layar dalam perbaikan/pemeliharaan">
                    <span style="display: inline-flex; align-items: center; justify-content: center; gap: 0.25rem; font-size: 0.65625rem; font-weight: 700; color: #D97706; white-space: nowrap;">
                        <span style="width: 0.375rem; height: 0.375rem; border-radius: 9999px; background: #D97706; flex-shrink: 0;"></span>
                        Perbaikan
                    </span>
                    <span class="sipetron-status-pill-val" style="font-size: 0.8125rem; font-weight: 700; color: {{ $deviceStats['repair'] > 0 ? '#D97706' : '#94A3B8' }}; display: block; margin-top: 0.0625rem;">{{ $deviceStats['repair'] }}</span>
                </div>

                <div class="sipetron-status-pill sipetron-status-pill-issue" title="Jumlah layar dengan kendala / offline">
                    <span style="display: inline-flex; align-items: center; justify-content: center; gap: 0.25rem; font-size: 0.65625rem; font-weight: 700; color: #DC2626; white-space: nowrap;">
                        <span style="width: 0.375rem; height: 0.375rem; border-radius: 9999px; background: #DC2626; flex-shrink: 0;"></span>
                        Gangguan
                    </span>
                    <span class="sipetron-status-pill-val" style="font-size: 0.8125rem; font-weight: 700; color: {{ $deviceStats['issue'] > 0 ? '#DC2626' : '#94A3B8' }}; display: block; margin-top: 0.0625rem;">{{ $deviceStats['issue'] }}</span>
                </div>

                <div class="sipetron-status-pill sipetron-status-pill-inactive" title="Jumlah layar nonaktif">
                    <span style="display: inline-flex; align-items: center; justify-content: center; gap: 0.25rem; font-size: 0.65625rem; font-weight: 700; color: #64748B; white-space: nowrap;">
                        <span style="width: 0.375rem; height: 0.375rem; border-radius: 9999px; background: #94A3B8; flex-shrink: 0;"></span>
                        Nonaktif
                    </span>
                    <span class="sipetron-status-pill-val" style="font-size: 0.8125rem; font-weight: 700; color: {{ $deviceStats['inactive'] > 0 ? '#475569' : '#94A3B8' }}; display: block; margin-top: 0.0625rem;">{{ $deviceStats['inactive'] }}</span>
                </div>
            </div>

            {{-- Installed Videotron Units --}}
            <div>
                <span style="font-size: 0.625rem; font-weight: 700; text-transform: uppercase; color: #64748B; letter-spacing: 0.05em; display: block; margin-bottom: 0.375rem;">
                    Daftar Titik Layar
                </span>
                <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.45rem;">
                    @foreach($videotrons as $v)
                        @php
                            $statusInfo = match(true) {
                                $v->condition === 'gangguan' => [
                                    'label' => 'Gangguan',
                                    'color' => '#DC2626',
                                    'bg' => '#FEF2F2',
                                    'border' => '#FEE2E2',
                                    'dot' => '#DC2626',
                                ],
                                $v->condition === 'dalam_perbaikan' => [
                                    'label' => 'Perbaikan',
                                    'color' => '#D97706',
                                    'bg' => '#FFFBEB',
                                    'border' => '#FEF3C7',
                                    'dot' => '#D97706',
                                ],
                                ! $v->is_active => [
                                    'label' => 'Nonaktif',
                                    'color' => '#64748B',
                                    'bg' => '#F1F5F9',
                                    'border' => '#E2E8F0',
                                    'dot' => '#94A3B8',
                                ],
                                default => [
                                    'label' => 'Normal',
                                    'color' => '#16A34A',
                                    'bg' => '#F0FDF4',
                                    'border' => '#DCFCE7',
                                    'dot' => '#16A34A',
                                ],
                            };
                            $isActive = $v->is_active && ($v->condition === 'normal' || empty($v->condition));
                        @endphp
                        @php
                            $activeCount = (int) ($v->active_broadcasts_count ?? 0);
                        @endphp
                        <a
                            href="{{ route('filament.admin.resources.videotrons.index', ['tableSearch' => $v->name, 'tableAction' => 'inspect_broadcasts', 'tableActionRecord' => $v->id]) }}"
                            class="sipetron-device-chip"
                            style="height: 100%; min-height: 2.75rem; width: 100%; box-sizing: border-box; text-decoration: none; cursor: pointer;"
                            title="Klik untuk melihat video yang seharusnya tayang hari ini di {{ $v->name }} ({{ $activeCount > 0 ? $activeCount.' tayang' : 'Kosong' }})"
                        >
                            <span class="sipetron-chip-dot {{ $isActive ? 'sipetron-status-pulse' : '' }}" style="background: {{ $statusInfo['dot'] }};"></span>
                            <div style="min-width: 0; flex: 1;">
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.35rem;">
                                    <span class="sipetron-chip-name">{{ $v->name }}</span>
                                    <span style="font-size: 0.59375rem; font-weight: 700; color: {{ $statusInfo['color'] }}; background: {{ $statusInfo['bg'] }}; border: 1px solid {{ $statusInfo['border'] }}; padding: 0.05rem 0.35rem; border-radius: 9999px; line-height: 1.2; flex-shrink: 0;">
                                        {{ $statusInfo['label'] }}
                                    </span>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.35rem; margin-top: 0.1rem;">
                                    <div class="sipetron-chip-loc" style="margin: 0; flex: 1; min-width: 0;">
                                        {{ $v->location }}
                                    </div>
                                    <span style="font-size: 0.59375rem; font-weight: 700; color: {{ $activeCount > 0 ? '#0F3D2E' : '#94A3B8' }}; white-space: nowrap; flex-shrink: 0;">
                                        {{ $activeCount > 0 ? $activeCount.' tayang' : 'Kosong' }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
