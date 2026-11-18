<x-filament-widgets::widget class="fi-wi-upcoming-broadcasts" style="height: 100%;">
    <x-filament::section style="height: 100%; display: flex; flex-direction: column;">
        {{-- Header Card --}}
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.75rem; min-height: 2.75rem; margin-bottom: 0.75rem;">
            <div style="min-width: 0; flex: 1 1 auto;">
                <h3 style="font-size: 0.875rem; font-weight: 700; color: #0F172A; margin: 0; line-height: 1.25; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    Jadwal Tayang Terdekat
                </h3>
                <p style="font-size: 0.71875rem; color: #64748B; margin: 0.15rem 0 0; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    Konten aktif & segera tayang di layar
                </p>
            </div>
            <a
                href="{{ route('filament.admin.resources.submissions.index', ['statusFilter' => 'terjadwal']) }}"
                class="sipetron-btn-secondary"
                style="flex-shrink: 0; white-space: nowrap;"
                title="Lihat semua jadwal penayangan"
            >
                <span>Kelola Jadwal</span>
                <span class="arrow-right">&rsaquo;</span>
            </a>
        </div>

        {{-- Daftar Konten Aktif / Terdekat --}}
        @if ($broadcasts->isEmpty())
            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2rem 1rem; text-align: center; background: #F8FAF9; border: 1px dashed #E2E8F0; border-radius: 0.75rem;">
                <div style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; background: #E2E8F0; color: #64748B; display: flex; align-items: center; justify-content: center; margin-bottom: 0.5rem;">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <span style="font-size: 0.8125rem; font-weight: 700; color: #334155;">Belum Ada Jadwal Penayangan</span>
                <span style="font-size: 0.6875rem; color: #64748B; margin-top: 0.2rem;">Permohonan yang disetujui akan muncul otomatis di sini</span>
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 0.5rem; flex: 1 1 auto;">
                @foreach ($broadcasts as $item)
                    <div
                        class="sipetron-broadcast-item"
                        onclick="window.location.href='{{ $item['url'] }}'"
                        onauxclick="if(event.button === 1) { event.preventDefault(); window.open('{{ $item['url'] }}', '_blank'); }"
                        role="button"
                        tabindex="0"
                        onkeydown="if(event.key === 'Enter' || event.key === ' ') { event.preventDefault(); window.location.href='{{ $item['url'] }}'; }"
                        style="background: #F8FAF9; border: 1px solid #E2E8F0; border-radius: 0.625rem; padding: 0.5rem 0.65rem; transition: all 180ms cubic-bezier(0.16, 1, 0.3, 1); box-sizing: border-box; cursor: pointer;"
                        title="{{ $item['title'] }} — Klik kartu untuk lihat detail pengajuan"
                    >
                        {{-- Baris 1: Judul Konten & Badge Status Tayang --}}
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem; margin-bottom: 0.25rem;">
                            <span
                                class="sipetron-broadcast-title"
                                style="font-size: 0.78125rem; font-weight: 700; color: #0F172A; line-height: 1.25; min-width: 0; flex: 1 1 auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; transition: color 150ms ease;"
                            >
                                {{ $item['title'] }}
                            </span>

                            {{-- Badge Status Penayangan --}}
                            @if ($item['status_type'] === 'live')
                                <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.125rem 0.45rem; border-radius: 9999px; background: #DCFCE7; border: 1px solid #BBF7D0; font-size: 0.625rem; font-weight: 700; color: #16A34A; white-space: nowrap; flex-shrink: 0;">
                                    <span class="sipetron-status-pulse" style="width: 0.375rem; height: 0.375rem; border-radius: 9999px; background: #16A34A;"></span>
                                    Sedang Tayang
                                </span>
                            @elseif ($item['status_type'] === 'tomorrow')
                                <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.125rem 0.45rem; border-radius: 9999px; background: #E0F2FE; border: 1px solid #BAE6FD; font-size: 0.625rem; font-weight: 700; color: #0284C7; white-space: nowrap; flex-shrink: 0;">
                                    <span style="width: 0.375rem; height: 0.375rem; border-radius: 9999px; background: #0284C7;"></span>
                                    Mulai Besok
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.125rem 0.45rem; border-radius: 9999px; background: #F1F5F9; border: 1px solid #E2E8F0; font-size: 0.625rem; font-weight: 600; color: #475569; white-space: nowrap; flex-shrink: 0;">
                                    {{ $item['status_label'] }}
                                </span>
                            @endif
                        </div>

                        {{-- Baris 2: Nama Pengaju & Titik Layar --}}
                        <div style="display: flex; align-items: center; gap: 0.35rem; font-size: 0.6875rem; color: #475569; margin-bottom: 0.35rem; min-width: 0;">
                            <span style="font-weight: 600; color: #334155; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 48%;">
                                {{ $item['organization'] }}
                            </span>
                            <span style="color: #CBD5E1;">&bull;</span>
                            <span style="display: inline-flex; align-items: center; gap: 0.2rem; color: #64748B; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1 1 auto;" title="{{ $item['videotrons'] }}">
                                <svg style="width: 0.75rem; height: 0.75rem; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>{{ $item['videotrons'] }}</span>
                            </span>
                        </div>

                        {{-- Baris 3: Tanggal Tayang & Indikator Kesiapan Materi --}}
                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.5rem; padding-top: 0.25rem; border-top: 1px solid #EEF2F6;">
                            <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.65625rem; color: #64748B; font-weight: 500;">
                                <svg style="width: 0.7rem; height: 0.7rem; color: #94A3B8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $item['date_range'] }}
                            </span>

                            {{-- Indikator Kesiapan Materi (Siap vs Belum Diunduh) --}}
                            @if ($item['is_downloaded'])
                                <span
                                    style="display: inline-flex; align-items: center; gap: 0.2rem; font-size: 0.625rem; font-weight: 700; color: #0F3D2E; background: #E8F3EC; border: 1px solid #C4DED0; padding: 0.1rem 0.4rem; border-radius: 0.25rem;"
                                    title="Materi video/poster telah diunduh oleh admin"
                                >
                                    <svg style="width: 0.65rem; height: 0.65rem; color: #16A34A;" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                    </svg>
                                    File Siap
                                </span>
                            @else
                                @if ($item['gdrive_link'])
                                    <button
                                        type="button"
                                        onclick="event.stopPropagation(); window.open('{{ $item['gdrive_link'] }}', '_blank');"
                                        onauxclick="event.stopPropagation();"
                                        style="display: inline-flex; align-items: center; gap: 0.2rem; font-size: 0.625rem; font-weight: 700; color: #D97706; background: #FEF3C7; border: 1px solid #FDE68A; padding: 0.1rem 0.4rem; border-radius: 0.25rem; cursor: pointer; transition: background-color 150ms ease;"
                                        title="Materi belum diunduh! Klik untuk buka link Google Drive pengaju"
                                    >
                                        <svg style="width: 0.65rem; height: 0.65rem; color: #D97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Unduh Materi
                                    </button>
                                @else
                                    <span
                                        style="display: inline-flex; align-items: center; gap: 0.2rem; font-size: 0.625rem; font-weight: 700; color: #D97706; background: #FEF3C7; border: 1px solid #FDE68A; padding: 0.1rem 0.4rem; border-radius: 0.25rem;"
                                        title="Materi belum diunduh"
                                    >
                                        <svg style="width: 0.65rem; height: 0.65rem;" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        Belum Diunduh
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
