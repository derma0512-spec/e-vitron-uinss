{{-- Tabel Daftar Riwayat Pengajuan & Drawer Detail Arsip (Dipanggil oleh history-and-recap.blade.php) --}}
        {{-- 2. CARD BOX TABEL DENGAN TOMBOL EKSPOR DI KANAN HEADER --}}
        <div class="sipetron-card-box">
            
            {{-- Header Card: Judul Tabel di Kiri, Aksi Ekspor di Kanan --}}
            <div class="sipetron-table-header">
                <div class="sipetron-table-title-group">
                    <h3 class="sipetron-table-title">
                        Daftar Riwayat
                    </h3>
                    <span class="sipetron-badge-green">
                        {{ $filteredCount }} Data
                    </span>
                    @if(($stats['pending_takedown'] ?? 0) > 0 || $filterStatus === 'perlu_diturunkan')
                        <button
                            type="button"
                            wire:click="setFilterStatus('{{ $filterStatus === 'perlu_diturunkan' ? '' : 'perlu_diturunkan' }}')"
                            class="sipetron-takedown-pill {{ $filterStatus === 'perlu_diturunkan' ? 'is-active' : '' }}"
                            title="{{ $filterStatus === 'perlu_diturunkan' ? 'Klik untuk kembali ke semua riwayat' : 'Klik untuk memfilter video habis masa tayang yang perlu diturunkan' }}"
                        >
                            @if(($stats['pending_takedown'] ?? 0) > 0)
                                <span style="position: relative; display: inline-flex; width: 7px; height: 7px;">
                                    <span style="position: absolute; display: inline-flex; height: 100%; width: 100%; border-radius: 9999px; background: #F59E0B; opacity: 0.75; animation: sipetron-ping 1.6s cubic-bezier(0, 0, 0.2, 1) infinite;"></span>
                                    <span style="position: relative; display: inline-flex; border-radius: 9999px; width: 7px; height: 7px; background: #D97706;"></span>
                                </span>
                            @endif
                            <span>{{ $stats['pending_takedown'] ?? 0 }} Perlu Diturunkan</span>
                        </button>
                    @endif
                    @if(filled($filterStatus) && $filterStatus !== 'perlu_diturunkan')
                        @php
                            $filterBadgeLabel = match($filterStatus) {
                                'selesai' => 'Selesai Tayang',
                                'ditolak' => 'Ditolak Admin',
                                'dibatalkan' => 'Dibatalkan Pengaju',
                                default => ucfirst($filterStatus),
                            };
                        @endphp
                        <span class="sipetron-badge-subtle">
                            Status: {{ $filterBadgeLabel }}
                        </span>
                    @endif
                    @if($periodPreset !== 'all')
                        <span class="sipetron-badge-subtle">
                            Periode: {{ $this->periodLabel }}
                        </span>
                    @endif
                </div>

                {{-- Tombol Ekspor Excel & Cetak PDF --}}
                <div class="sipetron-card-actions">
                    <button type="button" 
                            wire:click="exportCsv" 
                            wire:loading.attr="disabled"
                            wire:target="exportCsv"
                            class="btn-card-export-csv" 
                            title="Unduh laporan rekap arsip dalam format Excel (.xlsx)">
                        <svg wire:loading.remove wire:target="exportCsv" style="width: 14px; height: 14px; color: #0F3D2E;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <svg wire:loading wire:target="exportCsv" class="animate-spin" style="width: 14px; height: 14px; color: #0F3D2E;" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="9" stroke="#CBD5E1" stroke-width="2.5"></circle>
                            <path d="M12 3a9 9 0 0 1 9 9" stroke="#0F3D2E" stroke-width="2.5" stroke-linecap="round"></path>
                        </svg>
                        <span>Ekspor Excel</span>
                    </button>

                    <a href="{{ $this->printPdfUrl }}"
                       target="_blank"
                       class="btn-card-print-pdf"
                       style="text-decoration: none;"
                       title="Cetak laporan rekap arsip resmi dalam format PDF A4 Portrait di tab baru">
                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Cetak PDF</span>
                    </a>
                </div>
            </div>

            {{-- Tabel Riwayat & Rekap (6 Kolom Selaras dengan Halaman Pengajuan) --}}
            @php
                $selected = $this->selectedSubmission;
                $takedownAdmins = $this->resolveTakedownAdminNames($submissions->items(), $selected);
                $todayJakarta = now()->timezone('Asia/Jakarta')->startOfDay();
            @endphp
            <div 
                class="sipetron-table-wrap"
                style="overflow-x: auto; width: 100%; min-width: 0; margin-bottom: 1rem;"
            >
                <table class="sipetron-table" style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 0.8125rem; text-align: left;">
                    <colgroup>
                        <col class="sipetron-col-ticket" style="width: 142px;">
                        <col class="sipetron-col-content">
                        <col class="sipetron-col-requester" style="width: 155px;">
                        <col class="sipetron-col-file" style="width: 148px;">
                        <col class="sipetron-col-status" style="width: 148px;">
                        <col class="sipetron-col-action" style="width: 88px;">
                    </colgroup>
                    <thead>
                        <tr class="sipetron-thead-row">
                            <th class="sipetron-col-ticket" style="white-space: nowrap;">Kode Tiket</th>
                            <th class="sipetron-col-content" style="white-space: nowrap;">Kegiatan & Jadwal Tayang</th>
                            <th class="sipetron-col-requester" style="white-space: nowrap; padding-left: 0.75rem; padding-right: 0.75rem;">Pengaju</th>
                            <th class="sipetron-col-file" style="white-space: nowrap; padding-left: 0.5rem; padding-right: 0.5rem;">Penurunan</th>
                            <th class="sipetron-col-status" style="white-space: nowrap; padding-left: 0.5rem; padding-right: 0.5rem;">Status Akhir</th>
                            <th class="sipetron-col-action sipetron-sticky-col" style="white-space: nowrap; padding-left: 0.5rem; padding-right: 0.75rem;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $item)
                            @php
                                $isActive = ($selected && $selected->id === $item->id);
                                $statusKey = $item->display_status;
                                $isExpiredBroadcast = $statusKey === 'selesai' && $item->videotrons->isNotEmpty();
                                $isPendingTakedown = $isExpiredBroadcast && $item->videotrons->contains(fn ($v) => empty($v->pivot?->taken_down_at));
                                $isTakenDown = $isExpiredBroadcast && ! $isPendingTakedown;
                                $latestTakenPivot = $isTakenDown
                                    ? $item->videotrons->sortByDesc(fn ($v) => $v->pivot?->taken_down_at)->first()?->pivot
                                    : null;
                                $takenByName = $latestTakenPivot?->taken_down_by
                                    ? ($takedownAdmins[(int) $latestTakenPivot->taken_down_by] ?? 'Admin')
                                    : 'Admin';
                                $takenAtShort = $latestTakenPivot?->taken_down_at
                                    ? \Carbon\Carbon::parse($latestTakenPivot->taken_down_at)->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d M, H:i')
                                    : null;
                                $takenAtFull = $latestTakenPivot?->taken_down_at
                                    ? \Carbon\Carbon::parse($latestTakenPivot->taken_down_at)->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d M Y, H:i').' WIB'
                                    : null;
                                $endStart = $item->end_date?->copy()->timezone('Asia/Jakarta')->startOfDay();
                                $daysOverdue = ($isPendingTakedown && $endStart && $endStart->lt($todayJakarta))
                                    ? (int) $endStart->diffInDays($todayJakarta)
                                    : null;
                                $statusBadge = match($statusKey) {
                                    'selesai' => ['bg' => '#EEF2FF', 'dot' => '#6366F1', 'text' => '#4338CA', 'label' => 'Selesai'],
                                    'ditolak' => ['bg' => '#FEE2E2', 'dot' => '#DC2626', 'text' => '#B91C1C', 'label' => 'Ditolak Admin'],
                                    'dibatalkan' => ['bg' => '#F1F5F9', 'dot' => '#94A3B8', 'text' => '#475569', 'label' => 'Dibatalkan Pengaju'],
                                    default => ['bg' => '#F1F5F9', 'dot' => '#64748B', 'text' => '#475569', 'label' => ucfirst((string) $statusKey)],
                                };
                            @endphp
                            <tr 
                                wire:key="sub-row-{{ $item->id }}"
                                wire:click="selectSubmission({{ $item->id }})" 
                                class="sipetron-table-row {{ $isActive ? 'active-row' : '' }}"
                                style="cursor: pointer;"
                            >
                                {{-- 1. Kode Tiket & Tanggal Pengajuan --}}
                                <td class="sipetron-col-ticket" style="white-space: nowrap;">
                                    <span class="sipetron-row-ticket" style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: 0.78125rem; font-weight: 700; color: #0F172A; letter-spacing: -0.01em;">
                                        {{ $item->ticket_code }}
                                    </span>
                                    <div style="font-size: 0.65625rem; color: #94A3B8; margin-top: 0.15rem;">
                                        {{ $item->created_at?->locale('id')->translatedFormat('d M Y') }}
                                    </div>
                                </td>

                                {{-- 2. Konten & Jadwal Tayang (Smart Tooltip) --}}
                                <td class="sipetron-col-content" style="white-space: nowrap; overflow: hidden;">
                                    <span 
                                        class="sipetron-row-title"
                                        x-data="{ isOverflow: false }"
                                        x-init="$nextTick(() => { isOverflow = ($el.scrollWidth > $el.clientWidth); })"
                                        @mouseenter="isOverflow = ($el.scrollWidth > $el.clientWidth)"
                                        :title="isOverflow ? '{{ addslashes($item->title) }}' : null"
                                        style="font-weight: 600; color: #1E293B; font-size: 0.8125rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block;"
                                    >
                                        {{ $item->title }}
                                    </span>
                                    <div style="font-size: 0.6875rem; color: #64748B; margin-top: 0.2rem; display: flex; align-items: center; gap: 0.35rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <svg class="sipetron-row-schedule-icon" style="width: 0.75rem; height: 0.75rem; color: #94A3B8; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            @if($item->start_date && $item->end_date)
                                                @php
                                                    $duration = $item->start_date->diffInDays($item->end_date) + 1;
                                                @endphp
                                                @if($item->start_date->isSameDay($item->end_date))
                                                    {{ $item->start_date->locale('id')->translatedFormat('d M Y') }} <span style="color: #94A3B8; font-size: 0.65625rem;">(1 hari)</span>
                                                @elseif($item->start_date->year === $item->end_date->year)
                                                    {{ $item->start_date->locale('id')->translatedFormat('d M') }} – {{ $item->end_date->locale('id')->translatedFormat('d M Y') }} <span style="color: #94A3B8; font-size: 0.65625rem;">({{ $duration }} hari)</span>
                                                @else
                                                    {{ $item->start_date->locale('id')->translatedFormat('d M Y') }} – {{ $item->end_date->locale('id')->translatedFormat('d M Y') }} <span style="color: #94A3B8; font-size: 0.65625rem;">({{ $duration }} hari)</span>
                                                @endif
                                            @elseif($item->start_date)
                                                Mulai {{ $item->start_date->locale('id')->translatedFormat('d M Y') }}
                                            @else
                                                <span style="color: #94A3B8;">Jadwal belum ditentukan</span>
                                            @endif
                                        </span>
                                    </div>
                                </td>

                                {{-- 3. Pengaju --}}
                                <td class="sipetron-col-requester" style="white-space: nowrap; overflow: hidden; padding-left: 0.75rem; padding-right: 0.75rem;">
                                    <span style="font-weight: 600; color: #1E293B; font-size: 0.8125rem; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $item->user?->name ?? $item->pic_name }}
                                    </span>
                                    <div style="font-size: 0.6875rem; color: #64748B; margin-top: 0.2rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $item->organization ?: 'Umum/Mahasiswa' }}">
                                        {{ $item->organization ?: 'Umum/Mahasiswa' }}
                                    </div>
                                </td>

                                {{-- 4. Checklist Penurunan Konten --}}
                                <td class="sipetron-col-file" style="white-space: nowrap; padding-left: 0.5rem; padding-right: 0.5rem;">
                                    <div class="sipetron-cell-center">
                                        @if($isPendingTakedown)
                                            <button
                                                type="button"
                                                wire:click.stop="markSubmissionTakenDown({{ $item->id }})"
                                                wire:confirm="Tandai konten tiket {{ $item->ticket_code }} sudah diturunkan? Pastikan materi benar-benar sudah dihapus dari layar videotron fisik. Tindakan ini bersifat final dan akan mencatat nama Anda."
                                                wire:loading.attr="disabled"
                                                wire:target="markSubmissionTakenDown({{ $item->id }})"
                                                class="sipetron-takedown-btn"
                                            >
                                                <span class="sipetron-takedown-checkbox" wire:loading.remove wire:target="markSubmissionTakenDown({{ $item->id }})"></span>
                                                <svg wire:loading wire:target="markSubmissionTakenDown({{ $item->id }})" class="animate-spin" style="width: 13px; height: 13px; color: #0F3D2E; flex-shrink: 0;" viewBox="0 0 24 24" fill="none">
                                                    <circle cx="12" cy="12" r="9" stroke="#CBD5E1" stroke-width="2.5"></circle>
                                                    <path d="M12 3a9 9 0 0 1 9 9" stroke="#0F3D2E" stroke-width="2.5" stroke-linecap="round"></path>
                                                </svg>
                                                <span>Tandai Turun</span>
                                            </button>
                                        @elseif($isTakenDown)
                                            <div style="display: flex; flex-direction: column; align-items: center; gap: 0.1rem; max-width: 134px;" title="Diturunkan oleh {{ $takenByName }} pada {{ $takenAtFull }}">
                                                <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.71875rem; font-weight: 700; color: #0F3D2E; max-width: 100%;">
                                                    <svg style="width: 12px; height: 12px; color: #16A34A; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.6" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $takenByName }}</span>
                                                </span>
                                                @if($takenAtShort)
                                                    <span style="font-size: 0.625rem; color: #64748B; font-weight: 500;">
                                                        {{ $takenAtShort }}
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span style="color: #94A3B8; font-size: 0.8125rem; font-weight: 600;">—</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- 5. Status Akhir --}}
                                <td class="sipetron-col-status" style="white-space: nowrap; padding-left: 0.5rem; padding-right: 0.5rem;">
                                    <div class="sipetron-cell-center">
                                        @if($isPendingTakedown)
                                            <span
                                                style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.22rem 0.65rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 700; background: #FEF3C7; color: #B45309; border: 1px solid rgba(0, 0, 0, 0.05);"
                                                @if($daysOverdue !== null) title="Masa tayang berakhir {{ $daysOverdue }} hari lalu" @endif
                                            >
                                                <span style="width: 0.4rem; height: 0.4rem; border-radius: 9999px; background: #D97706;"></span>
                                                Perlu Diturunkan
                                            </span>
                                        @else
                                            <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.22rem 0.65rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 700; background: {{ $statusBadge['bg'] }}; color: {{ $statusBadge['text'] }}; border: 1px solid rgba(0, 0, 0, 0.05);">
                                                <span style="width: 0.4rem; height: 0.4rem; border-radius: 9999px; background: {{ $statusBadge['dot'] }};"></span>
                                                {{ $statusBadge['label'] }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- 6. Aksi: Detail (Sticky Right Column) --}}
                                <td class="sipetron-col-action sipetron-sticky-col" style="white-space: nowrap; padding-left: 0.5rem; padding-right: 0.75rem;">
                                    <div class="sipetron-cell-center">
                                        <button 
                                            type="button" 
                                            wire:click.stop="selectSubmission({{ $item->id }})"
                                            class="sipetron-detail-btn"
                                        >
                                            <span>Detail</span>
                                            <svg class="sipetron-btn-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7" />
                                            </svg>
                                            <svg class="sipetron-btn-spin" style="display: none;" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 3rem 1rem; color: #94A3B8;">
                                    @if($filterStatus === 'perlu_diturunkan' && blank($tableSearch) && $periodPreset === 'all' && ($stats['pending_takedown'] ?? 0) === 0)
                                        <svg style="width: 2.5rem; height: 2.5rem; margin: 0 auto 0.5rem auto; color: #16A34A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div style="font-weight: 700; color: #166534; font-size: 0.875rem;">Semua video habis masa tayang sudah diturunkan</div>
                                        <div style="font-size: 0.75rem; color: #64748B; margin-top: 0.25rem;">Tidak ada konten yang perlu dikonfirmasi penurunan saat ini.</div>
                                        <div style="margin-top: 0.75rem;">
                                            <button
                                                type="button"
                                                wire:click="setFilterStatus('')"
                                                style="font-size: 0.75rem; font-weight: 600; color: #0F3D2E; background: #E8F3EC; border: 1px solid #C4DED0; padding: 0.35rem 0.75rem; border-radius: 0.375rem; cursor: pointer;"
                                            >
                                                Lihat Semua Riwayat
                                            </button>
                                        </div>
                                    @else
                                        <svg style="width: 2.5rem; height: 2.5rem; margin: 0 auto 0.5rem auto; color: #CBD5E1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <div style="font-weight: 600; color: #475569; font-size: 0.875rem;">Tidak ada arsip pengajuan ditemukan</div>
                                        <div style="font-size: 0.75rem; color: #94A3B8; margin-top: 0.25rem;">Coba gunakan kata kunci pencarian atau filter status yang lain.</div>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Table Footer: Per Page & Pagination --}}
            @php
                $totalSub = $submissions->total();
                $curPage = $submissions->currentPage();
                $pPage = $submissions->perPage();
                $fromSub = $totalSub > 0 ? ($curPage - 1) * $pPage + 1 : 0;
                $toSub = min($curPage * $pPage, $totalSub);
            @endphp
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.875rem; padding-top: 0.875rem; margin-top: 0.25rem; border-top: 1px solid #F1F5F9;">
                <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: #64748B;">
                    <span>Tampilkan</span>
                    
                    {{-- Custom Per Page Dropup --}}
                    <div x-data="{ open: false }" @keydown.escape.window="open = false" style="position: relative; display: inline-block;">
                        <button 
                            type="button" 
                            @click="open = !open"
                            class="sipetron-perpage-trigger"
                            :class="{ 'is-open': open }"
                            title="Ubah jumlah data per halaman"
                        >
                            <span>{{ $this->perPage }}</span>
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
                            @foreach([5, 10, 25, 50] as $val)
                                @php
                                    $isCur = ((int)$this->perPage === $val);
                                @endphp
                                <button 
                                    type="button" 
                                    wire:click="$set('perPage', {{ $val }})" 
                                    @click="open = false"
                                    class="sipetron-perpage-item {{ $isCur ? 'is-selected' : '' }}"
                                >
                                    <span>{{ $val }} data</span>
                                    @if($isCur)
                                        <svg style="width: 13px; height: 13px; color: #0F3D2E; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <span>dari <strong style="color: #0F172A; font-weight: 700;">{{ $totalSub }}</strong> data</span>
                    @if($totalSub > 0)
                        <span style="color: #94A3B8; font-size: 0.75rem; font-weight: 500;">({{ $fromSub }}–{{ $toSub }})</span>
                    @endif
                </div>

                {{-- Pagination Links --}}
                <div class="sipetron-pagination-wrap">
                    {{ $submissions->links('vendor.pagination.sipetron-livewire') }}
                </div>
            </div>
        </div>

    {{-- DRAWER OVERLAY: Detail Arsip Pengajuan (Slide-Over Sisi Kanan) --}}
    @if($selected)
        @php
            $detailKey = $selected->display_status;
            $selectedNeedsTakedown = $detailKey === 'selesai'
                && $selected->videotrons->isNotEmpty()
                && $selected->videotrons->contains(fn ($v) => empty($v->pivot?->taken_down_at));
            $detailBadge = $selectedNeedsTakedown
                ? ['bg' => '#FEF3C7', 'dot' => '#D97706', 'text' => '#B45309', 'label' => 'Perlu Diturunkan']
                : match($detailKey) {
                    'selesai' => ['bg' => '#EEF2FF', 'dot' => '#6366F1', 'text' => '#4338CA', 'label' => 'Selesai'],
                    'ditolak' => ['bg' => '#FEE2E2', 'dot' => '#DC2626', 'text' => '#B91C1C', 'label' => 'Ditolak Admin'],
                    'dibatalkan' => ['bg' => '#F1F5F9', 'dot' => '#94A3B8', 'text' => '#475569', 'label' => 'Dibatalkan Pengaju'],
                    default => ['bg' => '#F1F5F9', 'dot' => '#64748B', 'text' => '#475569', 'label' => ucfirst((string) $detailKey)],
                };
        @endphp

        <div 
            x-data="{
                isOpen: true,
                applyLock() {
                    const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
                    if (scrollbarWidth > 0) {
                        document.documentElement.style.setProperty('--sipetron-scrollbar-width', scrollbarWidth + 'px');
                    } else {
                        document.documentElement.style.setProperty('--sipetron-scrollbar-width', '0px');
                    }
                    document.documentElement.classList.add('sipetron-drawer-locked');
                },
                removeLock() {
                    document.documentElement.classList.remove('sipetron-drawer-locked');
                    document.documentElement.style.removeProperty('--sipetron-scrollbar-width');
                },
                closeInstant() {
                    if (!this.isOpen) return;
                    this.isOpen = false;
                    this.removeLock();

                    const url = new URL(window.location.href);
                    let changed = false;
                    ['selected', 'drawer', 'from', 'source', 'ref'].forEach(p => {
                        if (url.searchParams.has(p)) {
                            url.searchParams.delete(p);
                            changed = true;
                        }
                    });
                    if (changed) {
                        window.history.replaceState({}, '', url.pathname + (url.search ? url.search : ''));
                    }

                    setTimeout(() => {
                        $wire.closeDetail();
                    }, 180);
                }
            }"
            x-init="applyLock()"
            @keydown.escape.window="closeInstant()"
            wire:key="sipetron-drawer-root"
        >
            {{-- Backdrop Overlay --}}
            <div 
                class="sipetron-drawer-backdrop" 
                x-show="isOpen"
                @click="closeInstant()"
                @wheel.prevent.stop
                @touchmove.prevent.stop
            ></div>

            {{-- Slide-Over Drawer Panel --}}
            <div 
                class="sipetron-drawer-panel" 
                x-show="isOpen"
                @click.stop
                wire:key="detail-drawer-panel-{{ $selected->id }}"
                x-transition:enter="transition transform ease-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition transform ease-in duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
            >
                {{-- 1. Sticky Drawer Header --}}
                <div class="sipetron-drawer-header" @wheel.prevent.stop @touchmove.prevent.stop>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.75rem;">
                        <div>
                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                <span style="font-size: 0.6875rem; font-weight: 800; color: #0F3D2E; text-transform: uppercase; letter-spacing: 0.06em; background: #E8F3EC; padding: 0.15rem 0.5rem; border-radius: 4px;">
                                    Detail Arsip Pengajuan
                                </span>
                                <span style="font-size: 0.75rem; color: #94A3B8;">&bull;</span>
                                <span style="font-size: 0.75rem; color: #64748B;">
                                    {{ $selected->created_at?->locale('id')->translatedFormat('d M Y, H:i') }} WIB
                                </span>
                            </div>

                            <div 
                                x-data="{ 
                                    copied: false,
                                    copyCode(code) {
                                        if (navigator.clipboard && window.isSecureContext) {
                                            navigator.clipboard.writeText(code).catch((err) => { console.warn('[E-VITRON] Clipboard writeText failed:', err); });
                                        } else {
                                            const t = document.createElement('textarea');
                                            t.value = code;
                                            t.style.position = 'fixed';
                                            t.style.opacity = '0';
                                            document.body.appendChild(t);
                                            t.select();
                                            try { document.execCommand('copy'); } catch (err) { console.warn('[E-VITRON] Clipboard fallback failed:', err); }
                                            document.body.removeChild(t);
                                        }
                                        this.copied = true;
                                        setTimeout(() => { this.copied = false; }, 2000);
                                    }
                                }" 
                                style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.35rem;"
                            >
                                <span style="font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 1.1875rem; font-weight: 800; color: #0F172A; letter-spacing: -0.02em;">
                                    {{ $selected->ticket_code }}
                                </span>
                                <button 
                                    type="button" 
                                    @click="copyCode('{{ $selected->ticket_code }}')"
                                    title="Salin Kode Tiket"
                                    style="display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 6px; border: 1px solid #E2E8F0; background: #F8FAF9; color: #64748B; cursor: pointer; transition: all 150ms ease;"
                                    onmouseover="this.style.borderColor='#CBD5E1'; this.style.backgroundColor='#FFFFFF';"
                                    onmouseout="this.style.borderColor='#E2E8F0'; this.style.backgroundColor='#F8FAF9';"
                                >
                                    <svg x-show="!copied" style="width: 13px; height: 13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    <svg x-show="copied" x-cloak style="width: 13px; height: 13px; color: #16A34A; display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                                <span x-show="copied" x-cloak style="font-size: 0.6875rem; color: #16A34A; font-weight: 700; display: none;">Tersalin!</span>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 0.625rem; flex-shrink: 0;">
                            <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: {{ $detailBadge['bg'] }}; color: {{ $detailBadge['text'] }}; box-shadow: 0 1px 2px rgba(0,0,0,0.03);">
                                <span style="width: 0.45rem; height: 0.45rem; border-radius: 9999px; background: {{ $detailBadge['dot'] }};"></span>
                                {{ $detailBadge['label'] }}
                            </span>

                            <button 
                                type="button" 
                                @click="closeInstant()" 
                                aria-label="Tutup"
                                class="fi-modal-close-btn sipetron-drawer-close-btn"
                                tabindex="0"
                            >
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- 2. Scrollable Drawer Body --}}
                <div class="sipetron-drawer-body">
                    @include('filament.pages.history-detail-modal', ['submission' => $selected, 'takedownAdmins' => $takedownAdmins])
                </div>

                {{-- 3. Sticky Drawer Footer --}}
                <div class="sipetron-drawer-footer" @wheel.prevent.stop @touchmove.prevent.stop>
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.625rem;">
                        <div style="flex: 1; min-width: 0; font-size: 0.75rem; color: #64748B; background: #FFFFFF; padding: 0.5rem 0.75rem; border-radius: 0.5rem; border: 1px dashed #CBD5E1; display: flex; align-items: center; gap: 0.4rem;">
                            <svg style="width: 14px; height: 14px; color: #94A3B8; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                            <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Arsip final &bull; <strong>{{ $selected->updated_at?->locale('id')->translatedFormat('d M Y, H:i') ?? '-' }} WIB</strong></span>
                        </div>
                        <a
                            x-data="{ isPrinting: false }"
                            @click="isPrinting = true; setTimeout(() => isPrinting = false, 800)"
                            href="{{ route('admin.submissions.print-pdf', ['id' => $selected->id]) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="btn-card-export-csv"
                            style="flex-shrink: 0; height: 36px; padding: 0 0.85rem; text-decoration: none;"
                            title="Cetak Bukti Pengajuan (PDF A4 Portrait)"
                        >
                            <svg x-show="!isPrinting" style="width: 14px; height: 14px; color: #0F3D2E;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <svg x-show="isPrinting" style="display: none; width: 14px; height: 14px; color: #0F3D2E; animation: sipetron-spin 0.65s linear infinite;" fill="none" viewBox="0 0 24 24">
                                <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                <path style="opacity: 0.9;" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
                            </svg>
                            <span>Cetak</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
