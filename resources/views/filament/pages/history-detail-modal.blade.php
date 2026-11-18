@php
    $safeDriveUrl = preg_match('#^https?://#i', (string) $submission->gdrive_link) ? $submission->gdrive_link : null;
    $picDisplayName = $submission->pic_name ?? $submission->user?->name ?? 'Pengaju';
    $picInitials = collect(explode(' ', trim($picDisplayName)))
        ->filter()
        ->map(fn ($w) => strtoupper(substr($w, 0, 1)))
        ->take(2)
        ->join('');
    if (empty($picInitials)) {
        $picInitials = 'PG';
    }

    $cleanPhone = preg_replace('/[^0-9]/', '', (string) $submission->whatsapp);
    if (str_starts_with($cleanPhone, '0')) {
        $waNumber = '62'.substr($cleanPhone, 1);
    } else {
        $waNumber = $cleanPhone;
    }
    $waMessage = rawurlencode("Halo Kak {$picDisplayName}, kami dari Tim Admin E-VITRON UIN Siber Syekh Nurjati Cirebon ingin mengonfirmasi terkait pengajuan videotron tiket {$submission->ticket_code} ({$submission->title})...");
    $daysCount = ($submission->start_date && $submission->end_date)
        ? ($submission->start_date->diffInDays($submission->end_date) + 1)
        : 0;
@endphp

<div style="display: flex; flex-direction: column; gap: 1rem; color: #1E293B;">

    {{-- 0. Status Banner Arsip --}}
    @if($submission->display_status === 'selesai')
        <div style="background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 0.75rem; padding: 0.75rem 0.95rem; display: flex; align-items: flex-start; gap: 0.65rem;">
            <div style="width: 24px; height: 24px; border-radius: 50%; background: #DCFCE7; color: #16A34A; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px;">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div style="min-width: 0; flex: 1;">
                <div style="font-size: 0.8125rem; font-weight: 800; color: #166534; line-height: 1.2;">
                    Masa Tayang Selesai
                </div>
                <div style="font-size: 0.71875rem; color: #15803D; margin-top: 3px; line-height: 1.4;">
                    Materi telah selesai disiarkan sesuai periode jadwal dan diarsipkan secara aman.
                </div>
                @if($submission->material_downloaded_at)
                    <div style="font-size: 0.6875rem; color: #047857; margin-top: 6px; padding-top: 5px; border-top: 1px dashed #A7F3D0; display: flex; align-items: center; gap: 4px;">
                        <svg style="width: 12px; height: 12px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Materi diunduh pada <strong>{{ $submission->material_downloaded_at->locale('id')->translatedFormat('d M Y, H:i') }} WIB</strong> oleh {{ $submission->materialDownloader?->name ?? 'Admin' }}</span>
                    </div>
                @endif
            </div>
        </div>
    @elseif($submission->display_status === 'ditolak')
        <div style="background: #FEF2F2; border: 1px solid #FECACA; border-radius: 0.75rem; padding: 0.75rem 0.95rem;">
            <div style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.78125rem; font-weight: 800; color: #991B1B; margin-bottom: 0.3rem;">
                <svg style="width: 15px; height: 15px; color: #DC2626; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Alasan Penolakan Pengajuan</span>
            </div>
            <div style="font-size: 0.78125rem; color: #7F1D1D; line-height: 1.45; word-break: break-word; background: #FFFFFF; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #FEE2E2; margin-top: 0.35rem;">
                "{{ $submission->admin_notes ?: '-' }}"
            </div>
        </div>
    @elseif($submission->display_status === 'dibatalkan')
        <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 0.75rem 0.95rem; display: flex; align-items: flex-start; gap: 0.65rem;">
            <div style="width: 24px; height: 24px; border-radius: 50%; background: #E2E8F0; color: #475569; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px;">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </div>
            <div style="min-width: 0;">
                <div style="font-size: 0.8125rem; font-weight: 800; color: #334155; line-height: 1.2;">
                    Pengajuan Dibatalkan Pengaju
                </div>
                <div style="font-size: 0.71875rem; color: #64748B; margin-top: 3px; line-height: 1.4;">
                    Pengajuan dibatalkan secara mandiri oleh pemohon sebelum jadwal tayang diproses.
                </div>
            </div>
        </div>
    @endif

    {{-- 1. UTAMA: Dokumen & Materi Penayangan (Sama Persis dengan Halaman Detail Pengajuan) --}}
    @php
        $hideSuratAndWarning = in_array($submission->display_status, ['ditolak', 'dibatalkan'], true);
    @endphp
    <div class="sipetron-info-card">
        <div class="sipetron-card-header">
            <div class="sipetron-card-title">
                <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Dokumen & Materi Penayangan</span>
            </div>
            @if(! $hideSuratAndWarning)
                <span style="font-size: 0.6875rem; font-weight: 700; color: #0F3D2E; background: #E8F3EC; padding: 2px 7px; border-radius: 6px;">
                    Prioritas Verifikasi
                </span>
            @endif
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.625rem;">

            {{-- Item A: Surat Peminjaman (PDF) --}}
            @if(! $hideSuratAndWarning)
            <div class="sipetron-doc-item" style="flex-direction: column; align-items: stretch; gap: 0.5rem; padding: 0.75rem 0.85rem; {{ ! $submission->surat_peminjaman ? 'background: #FFFDF5; border: 1px solid #FDE68A;' : 'background: #FFFFFF; border: 1px solid #E2E8F0;' }}">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.6rem; min-width: 0;">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: {{ $submission->surat_peminjaman ? '#FEE2E2' : '#FEF3C7' }}; color: {{ $submission->surat_peminjaman ? '#DC2626' : '#D97706' }}; border: 1px solid {{ $submission->surat_peminjaman ? '#FECACA' : '#FDE68A' }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0; position: relative;">
                            <svg style="width: 17px; height: 17px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            @if(! $submission->surat_peminjaman)
                                <span style="position: absolute; top: -3px; right: -3px; width: 8px; height: 8px; border-radius: 9999px; background: #D97706; border: 1.5px solid #FFFFFF;"></span>
                            @endif
                        </div>
                        <div style="min-width: 0;">
                            <div style="font-size: 0.8125rem; font-weight: 700; color: #0F172A; line-height: 1.2;">
                                Surat Peminjaman (PDF)
                            </div>
                            <div style="font-size: 0.6875rem; color: #64748B; margin-top: 2px;">
                                Dokumen resmi pengajuan penayangan
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 0.35rem; flex-shrink: 0;">
                        @if($submission->surat_peminjaman)
                            <a 
                                x-data="{ loading: false }"
                                @click="loading = true; setTimeout(() => loading = false, 650)"
                                href="{{ $submission->surat_peminjaman_url }}" 
                                target="_blank" 
                                rel="noopener noreferrer" 
                                class="sipetron-chip-btn sipetron-doc-btn-view" 
                                title="Buka dan baca surat peminjaman di tab baru"
                            >
                                <svg x-show="!loading" class="sipetron-doc-icon" style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="loading" style="display: none; width: 12px; height: 12px; animation: sipetron-spin 0.65s linear infinite;" fill="none" viewBox="0 0 24 24">
                                    <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                    <path style="opacity: 0.9;" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
                                </svg>
                                <span>Lihat</span>
                            </a>
                            <a 
                                x-data="{ loading: false }"
                                @click="loading = true; setTimeout(() => loading = false, 650)"
                                href="{{ $submission->surat_peminjaman_url }}" 
                                download="{{ $submission->surat_peminjaman_filename }}" 
                                class="sipetron-chip-btn sipetron-doc-btn-download" 
                                title="Unduh file surat peminjaman"
                            >
                                <svg x-show="!loading" class="sipetron-doc-icon" style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <svg x-show="loading" style="display: none; width: 12px; height: 12px; animation: sipetron-spin 0.65s linear infinite;" fill="none" viewBox="0 0 24 24">
                                    <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                    <path style="opacity: 0.9;" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
                                </svg>
                                <span>Unduh</span>
                            </a>
                        @else
                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 9999px; font-size: 0.6875rem; font-weight: 700; background: #FEF3C7; color: #B45309; border: 1px solid #FDE68A;">
                                <svg style="width: 11px; height: 11px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span>Belum Melampirkan</span>
                            </span>
                        @endif
                    </div>
                </div>
                @if($submission->surat_peminjaman)
                    <div style="font-size: 0.6875rem; color: #64748B; background: #F8FAF9; padding: 4px 8px; border-radius: 5px; border: 1px solid #F1F5F9; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $submission->surat_peminjaman_filename }}">
                        Berkas: <strong style="color: #334155;">{{ $submission->surat_peminjaman_filename }}</strong>
                    </div>
                @else
                    <div style="font-size: 0.6875rem; color: #B45309; background: #FEF3C7; padding: 4px 8px; border-radius: 5px; border: 1px solid #FDE68A; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        Belum ada berkas surat peminjaman (PDF) yang dilampirkan
                    </div>
                @endif
            </div>
            @endif

            {{-- Item B: Materi Konten Google Drive --}}
            @if($safeDriveUrl)
                <a 
                    x-data="{ loading: false }"
                    @click="loading = true; setTimeout(() => loading = false, 650)"
                    href="{{ $safeDriveUrl }}" 
                    target="_blank" 
                    rel="noopener noreferrer" 
                    class="sipetron-doc-item sipetron-doc-drive-card" 
                    style="flex-direction: column; align-items: stretch; gap: 0.5rem; padding: 0.75rem 0.85rem; text-decoration: none; cursor: pointer;"
                    title="Klik untuk membuka folder/file materi di Google Drive"
                >
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.6rem; min-width: 0;">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: #E0F2FE; color: #0284C7; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg style="width: 17px; height: 17px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15z" />
                                </svg>
                            </div>
                            <div style="min-width: 0;">
                                <div style="font-size: 0.8125rem; font-weight: 700; color: #0F172A; line-height: 1.2;">
                                    Materi Google Drive
                                </div>
                                <div style="font-size: 0.6875rem; color: #64748B; margin-top: 2px;">
                                    Tautan berkas video/poster dari pengaju
                                </div>
                            </div>
                        </div>

                        <div style="flex-shrink: 0;">
                            <span class="sipetron-chip-btn sipetron-doc-btn-drive">
                                <span>Buka Drive</span>
                                <svg x-show="!loading" class="sipetron-doc-icon" style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                <svg x-show="loading" style="display: none; width: 12px; height: 12px; animation: sipetron-spin 0.65s linear infinite;" fill="none" viewBox="0 0 24 24">
                                    <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                    <path style="opacity: 0.9;" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div style="font-size: 0.6875rem; color: #0284C7; background: #F0F9FF; padding: 4px 8px; border-radius: 5px; border: 1px solid #E0F2FE; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $submission->gdrive_link }}">
                        Tautan: <strong style="color: #0369A1;">{{ $submission->gdrive_link }}</strong>
                    </div>
                </a>
            @else
                <div class="sipetron-doc-item" style="flex-direction: column; align-items: stretch; gap: 0.5rem; padding: 0.75rem 0.85rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.6rem; min-width: 0;">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: #E0F2FE; color: #0284C7; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg style="width: 17px; height: 17px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15z" />
                                </svg>
                            </div>
                            <div style="min-width: 0;">
                                <div style="font-size: 0.8125rem; font-weight: 700; color: #0F172A; line-height: 1.2;">
                                    Materi Google Drive
                                </div>
                                <div style="font-size: 0.6875rem; color: #64748B; margin-top: 2px;">
                                    Tautan berkas video/poster dari pengaju
                                </div>
                            </div>
                        </div>

                        <div style="flex-shrink: 0;">
                            <span style="font-size: 0.6875rem; color: #DC2626; font-weight: 600;">Link tidak valid</span>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Item C: Detail Konten (Judul, Jenis & Deskripsi) --}}
            <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.625rem; padding: 0.75rem 0.85rem;">
                <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.35rem;">
                    <div style="font-size: 0.8125rem; font-weight: 800; color: #0F172A; line-height: 1.35;">
                        {{ $submission->title }}
                    </div>
                    @if($submission->content_type === 'video')
                        <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.2rem 0.55rem; border-radius: 6px; font-size: 0.6875rem; font-weight: 700; background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; flex-shrink: 0;">
                            <svg style="width: 12px; height: 12px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <span>Video @if($submission->video_duration_seconds)({{ $submission->video_duration_seconds }}s)@endif</span>
                        </span>
                    @else
                        <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.2rem 0.55rem; border-radius: 6px; font-size: 0.6875rem; font-weight: 700; background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0; flex-shrink: 0;">
                            <svg style="width: 12px; height: 12px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Poster Gambar</span>
                        </span>
                    @endif
                </div>
                <div style="font-size: 0.75rem; color: #475569; line-height: 1.45;">
                    {{ $submission->description }}
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Jadwal & Sasaran Videotron (Sama Persis dengan Halaman Detail Pengajuan) --}}
    <div class="sipetron-info-card">
        <div class="sipetron-card-header">
            <div class="sipetron-card-title">
                <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Jadwal & Sasaran Videotron</span>
            </div>
            <span style="font-size: 0.6875rem; font-weight: 700; color: #0F3D2E; background: #E8F3EC; padding: 2px 7px; border-radius: 6px;">
                {{ $daysCount }} Hari Penayangan
            </span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.625rem;">
            {{-- Rentang Jadwal Box --}}
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.55rem 0.75rem; background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.5rem; font-size: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.4rem; color: #64748B;">
                    <svg style="width: 14px; height: 14px; color: #0F3D2E;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Rentang Tayang:</span>
                </div>
                <div style="font-weight: 800; color: #0F3D2E;">
                    {{ $submission->start_date?->locale('id')->translatedFormat('d M Y') }} – {{ $submission->end_date?->locale('id')->translatedFormat('d M Y') }}
                </div>
            </div>

            {{-- Daftar Videotron --}}
            <div style="display: flex; flex-direction: column; gap: 0.35rem;">
                @forelse($submission->videotrons as $v)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.45rem 0.65rem; background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.5rem; font-size: 0.75rem;">
                        <div style="display: flex; align-items: center; gap: 0.45rem; min-width: 0;">
                            <span style="width: 0.45rem; height: 0.45rem; border-radius: 9999px; background: {{ $v->condition === 'normal' && $v->is_active ? '#10B981' : '#F59E0B' }}; flex-shrink: 0;" title="{{ $v->condition === 'normal' && $v->is_active ? 'Perangkat Siap & Normal' : 'Dalam Perawatan' }}"></span>
                            <span style="font-weight: 700; color: #0F172A;">{{ $v->name }}</span>
                            <span style="color: #64748B; font-size: 0.6875rem;">({{ $v->location }})</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.35rem; flex-shrink: 0;">
                            <span style="font-size: 0.6875rem; color: #475569; font-family: monospace; background: #F1F5F9; padding: 1px 5px; border-radius: 4px;">
                                {{ $v->resolution }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div style="color: #94A3B8; font-size: 0.75rem; text-align: center; padding: 0.5rem;">Tidak ada perangkat videotron dipilih.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- 3. Identitas Pengaju (PIC) & Kontak Cepat WhatsApp (Sama Persis dengan Halaman Detail Pengajuan) --}}
    <div class="sipetron-info-card">
        <div class="sipetron-card-header">
            <div class="sipetron-card-title">
                <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>Identitas Pengaju</span>
            </div>
            <span style="font-size: 0.6875rem; font-weight: 600; color: #64748B;">Akun: {{ $submission->user?->name }}</span>
        </div>

        <div style="display: flex; align-items: flex-start; gap: 0.75rem; margin-bottom: 0.75rem;">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #0F3D2E 0%, #1A6B51 100%); color: #FFFFFF; font-weight: 800; font-size: 13px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 6px rgba(15, 61, 46, 0.2);">
                {{ $picInitials }}
            </div>
            <div style="min-width: 0; flex: 1;">
                <div style="font-size: 0.875rem; font-weight: 800; color: #0F172A; line-height: 1.2;">
                    {{ $picDisplayName }}
                </div>
                <div style="font-size: 0.75rem; color: #64748B; margin-top: 2px;">
                    {{ $submission->organization ?: 'Civitas Akademika UIN SSC' }}
                </div>
                <div style="font-size: 0.6875rem; color: #94A3B8; margin-top: 2px;">
                    {{ $submission->user?->email }}
                </div>
            </div>
        </div>

        {{-- Direct WhatsApp Button --}}
        @if($cleanPhone)
            <div style="margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px dashed #E2E8F0; display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; flex-wrap: wrap;">
                <span style="font-size: 0.6875rem; color: #64748B;">Kontak Cepat:</span>
                <a 
                    x-data="{ loading: false }"
                    @click="loading = true; setTimeout(() => loading = false, 650)"
                    href="https://wa.me/{{ $waNumber }}?text={{ $waMessage }}" 
                    target="_blank" 
                    rel="noopener noreferrer" 
                    class="sipetron-wa-btn"
                    title="Buka obrolan WhatsApp langsung dengan pengaju"
                >
                    <svg style="width: 17px; height: 17px; fill: currentColor; flex-shrink: 0;" viewBox="0 0 24 24">
                        <path d="M20.52 3.449C18.24 1.245 15.24 0 12.045 0 5.463 0 .104 5.334.101 11.893c0 2.096.549 4.14 1.595 5.945L0 24l6.335-1.652c1.746.943 3.71 1.444 5.71 1.447h.006c6.585 0 11.946-5.336 11.949-11.896 0-3.176-1.24-6.165-3.48-8.45zm-8.475 18.321h-.005c-1.774 0-3.513-.477-5.032-1.378l-.361-.214-3.76 1.003 1.005-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.002-5.45 4.456-9.884 9.903-9.884 2.645 0 5.132 1.03 7.001 2.898a9.825 9.825 0 012.897 6.994c-.003 5.45-4.457 9.863-9.903 9.863zm5.432-7.397c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                    </svg>
                    <span>Chat WhatsApp ({{ $submission->whatsapp }})</span>
                    <svg x-show="!loading" style="width: 12px; height: 12px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    <svg x-show="loading" style="display: none; width: 12px; height: 12px; flex-shrink: 0; animation: sipetron-spin 0.65s linear infinite;" fill="none" viewBox="0 0 24 24">
                        <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                        <path style="opacity: 0.9;" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
                    </svg>
                </a>
            </div>
        @endif
    </div>

    {{-- 4. Linimasa & Catatan Tiket --}}
    @if($submission->admin_notes || $submission->revisions->isNotEmpty() || $submission->statusHistories->isNotEmpty())
        <div class="sipetron-info-card">
            <div class="sipetron-card-header">
                <div class="sipetron-card-title">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Linimasa & Catatan Tiket</span>
                </div>
            </div>

            {{-- Catatan Terakhir Admin --}}
            @if($submission->admin_notes)
                <div style="margin-bottom: 0.65rem;">
                    <div style="font-size: 0.6875rem; font-weight: 700; color: #64748B; margin-bottom: 0.25rem;">Catatan Admin Terakhir:</div>
                    <div style="font-size: 0.75rem; color: #334155; line-height: 1.45; background: #FFFFFF; padding: 0.5rem 0.75rem; border-radius: 0.5rem; border: 1px solid #E2E8F0;">
                        "{{ $submission->admin_notes }}"
                    </div>
                </div>
            @endif

            {{-- Riwayat Feedback Revisi --}}
            @if($submission->revisions->isNotEmpty())
                <div style="margin-bottom: 0.65rem;">
                    <div style="font-size: 0.6875rem; font-weight: 700; color: #9A3412; margin-bottom: 0.25rem;">Riwayat Catatan Revisi:</div>
                    <div style="display: flex; flex-direction: column; gap: 0.35rem;">
                        @foreach($submission->revisions as $rev)
                            <div style="background: #FFF7ED; border: 1px solid #FED7AA; border-radius: 0.5rem; padding: 0.5rem 0.65rem; font-size: 0.71875rem; color: #7C2D12;">
                                <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 0.65625rem; margin-bottom: 0.2rem;">
                                    <span>Permintaan Revisi</span>
                                    <span>{{ $rev->requested_at?->locale('id')->translatedFormat('d M Y, H:i') }} WIB</span>
                                </div>
                                <div>{{ $rev->admin_feedback }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Log Perubahan Status --}}
            @if($submission->statusHistories->isNotEmpty())
                <div>
                    <div style="font-size: 0.6875rem; font-weight: 700; color: #64748B; margin-bottom: 0.35rem;">Log Perubahan Status:</div>
                    <div style="position: relative; padding-left: 1rem; border-left: 2px solid #E2E8F0; margin-left: 0.35rem; display: flex; flex-direction: column; gap: 0.5rem;">
                        @foreach($submission->statusHistories as $hist)
                            @php
                                $hColor = match($hist->to_status) {
                                    'disetujui' => ['dot' => '#16A34A', 'bg' => '#DCFCE7', 'text' => '#15803D', 'label' => 'Disetujui'],
                                    'revisi' => ['dot' => '#EA580C', 'bg' => '#FFEDD5', 'text' => '#C2410C', 'label' => 'Revisi'],
                                    'ditolak' => ['dot' => '#DC2626', 'bg' => '#FEE2E2', 'text' => '#B91C1C', 'label' => 'Ditolak'],
                                    'menunggu' => ['dot' => '#D97706', 'bg' => '#FEF3C7', 'text' => '#B45309', 'label' => 'Menunggu'],
                                    'selesai' => ['dot' => '#4F46E5', 'bg' => '#EEF2FF', 'text' => '#4338CA', 'label' => 'Selesai'],
                                    'dibatalkan' => ['dot' => '#64748B', 'bg' => '#F1F5F9', 'text' => '#475569', 'label' => 'Dibatalkan'],
                                    default => ['dot' => '#64748B', 'bg' => '#F1F5F9', 'text' => '#475569', 'label' => ucfirst((string) $hist->to_status)],
                                };
                            @endphp
                            <div style="position: relative;">
                                <div style="position: absolute; left: -1.35rem; top: 0.25rem; width: 0.55rem; height: 0.55rem; border-radius: 9999px; background: {{ $hColor['dot'] }}; border: 1.5px solid #FFFFFF; box-shadow: 0 0 0 1px #CBD5E1;"></div>
                                <div style="font-size: 0.71875rem; color: #334155; line-height: 1.4;">
                                    <span style="font-weight: 700; color: {{ $hColor['text'] }};">{{ $hColor['label'] }}</span>
                                    <span style="color: #94A3B8; font-size: 0.65625rem;">({{ $hist->created_at?->locale('id')->translatedFormat('d M Y, H:i') }} WIB)</span>
                                    oleh <strong>{{ $hist->user?->name ?? 'Sistem' }}</strong>
                                    @if($hist->notes)
                                        <div style="color: #64748B; font-style: italic; font-size: 0.6875rem; margin-top: 1px;">"{{ $hist->notes }}"</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif

</div>
