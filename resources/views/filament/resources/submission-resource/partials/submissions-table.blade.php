{{-- Tabel Utama Daftar Pengajuan & Pagination (Dipanggil oleh list-submissions.blade.php) --}}
    {{-- Tabel Utama Daftar Pengajuan (100% Full Width) --}}
    <div style="width: 100%;">
        <div class="sipetron-card-box" style="padding: 1.25rem; min-width: 0; box-sizing: border-box; width: 100%;">
            
            {{-- Card Header: Judul Tabel & Quick Action --}}
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.875rem; padding-bottom: 0.75rem; border-bottom: 1px solid #F1F5F9; flex-wrap: wrap; gap: 0.5rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                    <h3 style="font-size: 0.9375rem; font-weight: 700; color: #0F172A; margin: 0;">
                        Daftar Pengajuan
                    </h3>
                    <span style="font-size: 0.6875rem; font-weight: 700; color: #0F3D2E; background: #E8F3EC; border: 1px solid #C4DED0; padding: 0.1rem 0.55rem; border-radius: 9999px;">
                        {{ $submissions->total() }} Data
                    </span>
                    @if(($counts['menunggu'] ?? 0) > 0)
                        <button 
                            type="button" 
                            wire:click="setStatusFilter('menunggu')"
                            style="font-size: 0.6875rem; font-weight: 700; color: {{ $this->statusFilter === 'menunggu' ? '#92400E' : '#B45309' }}; background: {{ $this->statusFilter === 'menunggu' ? '#FEF3C7' : '#FFFBEB' }}; border: 1px solid {{ $this->statusFilter === 'menunggu' ? '#F59E0B' : '#FDE68A' }}; padding: 0.15rem 0.6rem; border-radius: 9999px; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem; transition: all 150ms ease; box-shadow: 0 1px 2px rgba(217, 119, 6, 0.08);"
                            title="Klik untuk langsung memfilter pengajuan yang perlu direview"
                        >
                            <span style="position: relative; display: inline-flex; width: 7px; height: 7px;">
                                <span style="position: absolute; display: inline-flex; height: 100%; width: 100%; border-radius: 9999px; background: #F59E0B; opacity: 0.75; animation: sipetron-ping 1.6s cubic-bezier(0, 0, 0.2, 1) infinite;"></span>
                                <span style="position: relative; display: inline-flex; border-radius: 9999px; width: 7px; height: 7px; background: #D97706;"></span>
                            </span>
                            <span>{{ $counts['menunggu'] }} Perlu Direview</span>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Table (6 Kolom: Kode Tiket, Kegiatan & Jadwal Tayang, Pengaju, Status File, Status, Aksi) --}}
            <div 
                class="sipetron-table-wrap"
                style="overflow-x: auto; width: 100%; min-width: 0; margin-bottom: 1rem;"
            >
                <table class="sipetron-table" style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 0.8125rem; text-align: left;">
                    <colgroup>
                        <col class="sipetron-col-ticket" style="width: 142px;">
                        <col class="sipetron-col-content">
                        <col class="sipetron-col-requester" style="width: 155px;">
                        <col class="sipetron-col-file" style="width: 118px;">
                        <col class="sipetron-col-status" style="width: 140px;">
                        <col class="sipetron-col-action" style="width: 88px;">
                    </colgroup>
                    <thead>
                        <tr class="sipetron-thead-row">
                            <th class="sipetron-col-ticket" style="white-space: nowrap;">Kode Tiket</th>
                            <th class="sipetron-col-content" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Kegiatan & Jadwal Tayang</th>
                            <th class="sipetron-col-requester" style="white-space: nowrap; padding-left: 0.75rem; padding-right: 0.75rem;">Pengaju</th>
                            <th class="sipetron-col-file" style="white-space: nowrap; padding-left: 0.5rem; padding-right: 0.5rem;">Status File</th>
                            <th class="sipetron-col-status" style="white-space: nowrap; padding-left: 0.5rem; padding-right: 0.5rem;">Status</th>
                            <th class="sipetron-col-action sipetron-sticky-col" style="white-space: nowrap; padding-left: 0.5rem; padding-right: 0.75rem;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $item)
                            @php
                                $isActive = ($selected && $selected->id === $item->id);
                                $statusKey = $item->display_status;
                                $statusBadge = match($statusKey) {
                                    'menunggu' => ['bg' => '#FEF3C7', 'dot' => '#D97706', 'text' => '#B45309', 'label' => 'Perlu Direview'],
                                    'revisi' => ['bg' => '#E0F2FE', 'dot' => '#0284C7', 'text' => '#0369A1', 'label' => 'Menunggu Perbaikan'],
                                    'terjadwal' => ['bg' => '#CCFBF1', 'dot' => '#0D9488', 'text' => '#0F766E', 'label' => 'Terjadwal'],
                                    'tayang' => ['bg' => '#DCFCE7', 'dot' => '#16A34A', 'text' => '#15803D', 'label' => 'Sedang Tayang'],
                                    'selesai' => ['bg' => '#EEF2FF', 'dot' => '#6366F1', 'text' => '#4338CA', 'label' => 'Selesai'],
                                    'ditolak' => ['bg' => '#FEE2E2', 'dot' => '#DC2626', 'text' => '#B91C1C', 'label' => 'Ditolak Admin'],
                                    'dibatalkan' => ['bg' => '#F1F5F9', 'dot' => '#94A3B8', 'text' => '#475569', 'label' => 'Dibatalkan Pengaju'],
                                    default => ['bg' => '#F1F5F9', 'dot' => '#64748B', 'text' => '#475569', 'label' => ucfirst($statusKey)],
                                };
                            @endphp
                            <tr 
                                wire:key="sub-row-{{ $item->id }}"
                                wire:click="selectSubmission({{ $item->id }})" 
                                class="sipetron-table-row {{ $isActive ? 'active-row' : '' }}"
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

                                {{-- 2. Kegiatan & Jadwal Tayang --}}
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

                                {{-- 3. Pengaju & Unit/Instansi (Sama dengan Riwayat & Rekap) --}}
                                <td class="sipetron-col-requester" style="white-space: nowrap; overflow: hidden; padding-left: 0.75rem; padding-right: 0.75rem;">
                                    <span style="font-weight: 600; color: #1E293B; font-size: 0.8125rem; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $item->user?->name ?? $item->pic_name }}
                                    </span>
                                    <div style="font-size: 0.6875rem; color: #64748B; margin-top: 0.2rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $item->organization ?: 'Umum/Mahasiswa' }}">
                                        {{ $item->organization ?: 'Umum/Mahasiswa' }}
                                    </div>
                                </td>

                                {{-- 4. Status File (Materi): Hanya relevan setelah pengajuan Disetujui (Terjadwal/Tayang) --}}
                                <td class="sipetron-col-file" style="white-space: nowrap; padding-left: 0.5rem; padding-right: 0.5rem;">
                                    <div class="sipetron-cell-center">
                                        @if(! in_array($statusKey, ['terjadwal', 'tayang', 'selesai'], true))
                                            <span style="color: #94A3B8; font-size: 0.8125rem; font-weight: 600;" title="Materi baru diunduh setelah pengajuan disetujui">—</span>
                                        @elseif($item->material_downloaded_at !== null)
                                            <span style="display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.6875rem; font-weight: 700; color: #0F3D2E; background: #E8F3EC; border: 1px solid #C4DED0; padding: 0.2rem 0.55rem; border-radius: 9999px;">
                                                <svg style="width: 0.65rem; height: 0.65rem; color: #16A34A;" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                                </svg>
                                                File Siap
                                            </span>
                                        @else
                                            <span style="display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.6875rem; font-weight: 700; color: #B45309; background: #FFFBEB; border: 1px solid #FDE68A; padding: 0.2rem 0.55rem; border-radius: 9999px;">
                                                <svg style="width: 0.6875rem; height: 0.6875rem; color: #D97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                Belum Diunduh
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- 5. Status Pengajuan --}}
                                <td class="sipetron-col-status" style="white-space: nowrap; padding-left: 0.5rem; padding-right: 0.5rem;">
                                    <div class="sipetron-cell-center">
                                        <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.22rem 0.65rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 700; background: {{ $statusBadge['bg'] }}; color: {{ $statusBadge['text'] }}; border: 1px solid rgba(0, 0, 0, 0.05);">
                                            <span style="width: 0.4rem; height: 0.4rem; border-radius: 9999px; background: {{ $statusBadge['dot'] }};"></span>
                                            {{ $statusBadge['label'] }}
                                        </span>
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
                                    <svg style="width: 2.5rem; height: 2.5rem; margin: 0 auto 0.5rem auto; color: #CBD5E1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <div style="font-weight: 700; font-size: 0.875rem; color: #1E293B;">Tidak ada pengajuan aktif ditemukan</div>
                                    <div style="font-size: 0.75rem; margin-top: 0.25rem; color: #64748B;">Coba sesuaikan kata kunci pencarian atau filter yang dipilih.</div>
                                    @if($hasActiveFilter)
                                        <div style="margin-top: 0.75rem;">
                                            <button 
                                                type="button" 
                                                wire:click="resetFilters"
                                                style="font-size: 0.75rem; font-weight: 600; color: #0F3D2E; background: #E8F3EC; border: 1px solid #C4DED0; padding: 0.35rem 0.75rem; border-radius: 0.375rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem; transition: all 150ms ease;"
                                            >
                                                <svg style="width: 11px; height: 11px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                <span>Reset Semua Filter</span>
                                            </button>
                                        </div>
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

                {{-- Pagination Links (Wrapped with clean styles, no English duplicate text) --}}
                <div class="sipetron-pagination-wrap">
                    {{ $submissions->links() }}
                </div>
            </div>
        </div>
    </div>
