{{-- Modal Konfirmasi Setujui, Revisi & Tolak Pengajuan (Dipanggil oleh drawer-header-status.blade.php) --}}
            @if(in_array($selected->status, ['menunggu', 'disetujui', 'revisi', 'ditolak'], true))
            @php
                $modalPreCheck = $preCheck ?? app(\App\Services\SubmissionDecisionService::class)->checkAvailabilityAndConflicts($selected);
                $modalUnavailable = isset($modalPreCheck['unavailable_videotrons']) && $modalPreCheck['unavailable_videotrons']->isNotEmpty()
                    ? $modalPreCheck['unavailable_videotrons']->pluck('name')->join(', ')
                    : null;
            @endphp

            {{-- MODAL: Setujui Pengajuan (Client-Side Instant) --}}
            <div
                x-show="modal === 'approve'"
                x-transition.opacity.duration.150ms
                @click.self="closeModal()"
                class="sipetron-instant-modal-overlay"
                style="display: none;"
            >
                <div class="sipetron-instant-modal-card" style="max-width: 480px;">
                    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; background: #DCFCE7; color: #16A34A; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg style="width: 1.35rem; height: 1.35rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h3 style="font-size: 1.0625rem; font-weight: 800; color: #0F172A; margin: 0;">Setujui Pengajuan Penayangan</h3>
                                <span style="font-size: 0.75rem; color: #64748B;">Tiket: <strong style="font-family: monospace;">{{ $selected->ticket_code }}</strong></span>
                            </div>
                        </div>
                        <button type="button" @click="closeModal()" aria-label="Tutup" class="fi-modal-close-btn" tabindex="0">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div style="background: #F8FAF9; border: 1px solid #E2E8F0; border-radius: 0.625rem; padding: 0.9rem 1rem; margin-bottom: 1.25rem; font-size: 0.8125rem;">
                        <div style="font-weight: 700; color: #0F172A; margin-bottom: 0.3rem;">{{ $selected->title }}</div>
                        <div style="color: #64748B; font-size: 0.75rem; line-height: 1.5;">
                            Perangkat: <strong style="color: #334155;">{{ $selected->videotrons->pluck('name')->join(', ') }}</strong><br>
                            Jadwal Tayang: <strong style="color: #334155;">{{ $selected->start_date?->format('d M Y') }} s/d {{ $selected->end_date?->format('d M Y') }}</strong>
                        </div>

                        @if($modalPreCheck['can_approve'] ?? true)
                            <div style="margin-top: 0.65rem; padding-top: 0.6rem; border-top: 1px dashed #E2E8F0; font-size: 0.75rem; color: #15803D; font-weight: 600; display: flex; align-items: center; gap: 0.4rem;">
                                <svg style="width: 14px; height: 14px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Perangkat videotron sasaran dalam kondisi aktif dan siap dijadwalkan.</span>
                            </div>
                        @else
                            <div style="margin-top: 0.65rem; padding: 0.55rem 0.7rem; background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 0.5rem; font-size: 0.75rem; color: #92400E; line-height: 1.4; display: flex; align-items: flex-start; gap: 0.45rem;">
                                <svg style="width: 15px; height: 15px; color: #D97706; flex-shrink: 0; margin-top: 1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div>
                                    <strong style="color: #78350F;">Catatan Perangkat:</strong> {{ $modalPreCheck['error_message'] }}
                                    <div style="margin-top: 2px; color: #B45309; font-size: 0.71875rem;">
                                        Anda tetap dapat menyetujui pengajuan ini apabila penayangan tetap dijadwalkan.
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                        <button type="button" @click="closeModal()" class="fi-btn-batal-custom">Batal</button>
                        <button
                            type="button"
                            @click="submitApprove()"
                            wire:loading.attr="disabled"
                            wire:target="confirmApprove"
                            style="padding: 0.5rem 1.25rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 700; background: #0F3D2E; color: #FFFFFF; border: none; cursor: pointer; box-shadow: 0 2px 6px rgba(15, 61, 46, 0.25); display: inline-flex; align-items: center; gap: 0.4rem;"
                        >
                            <svg wire:loading wire:target="confirmApprove" style="width: 0.95rem; height: 0.95rem; animation: sipetron-spin 0.65s linear infinite;" fill="none" viewBox="0 0 24 24">
                                <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                <path style="opacity: 0.9;" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
                            </svg>
                            <span>Ya, Setujui Pengajuan</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- MODAL: Minta Revisi (Client-Side Instant) --}}
            <div
                x-show="modal === 'revision'"
                x-transition.opacity.duration.150ms
                @click.self="closeModal()"
                class="sipetron-instant-modal-overlay"
                style="display: none;"
            >
                <div class="sipetron-instant-modal-card" style="max-width: 520px;">
                    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; background: #E0F2FE; color: #0284C7; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg style="width: 1.35rem; height: 1.35rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </div>
                            <div>
                                <h3 style="font-size: 1.0625rem; font-weight: 800; color: #0F172A; margin: 0;">Minta Revisi kepada Pengaju</h3>
                                <span style="font-size: 0.75rem; color: #64748B;">Tiket: <strong style="font-family: monospace;">{{ $selected->ticket_code }}</strong></span>
                            </div>
                        </div>
                        <button type="button" @click="closeModal()" aria-label="Tutup" class="fi-modal-close-btn" tabindex="0">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #475569; margin-bottom: 0.35rem;">
                            Pilih Template Alasan Cepat:
                        </label>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.35rem; margin-bottom: 0.75rem;">
                            @if($modalUnavailable)
                                <button
                                    type="button"
                                    @click="pickRevTemplate(@js('Perangkat ' . $modalUnavailable . ' saat ini sedang dalam perawatan/gangguan. Mohon sesuaikan materi video pada tautan drive atau jadwal pada surat peminjaman.'), 'keduanya')"
                                    class="sipetron-chip-btn"
                                    style="background: #FFFBEB; border-color: #FDE68A; color: #92400E;"
                                >
                                    <svg style="width: 12px; height: 12px; color: #D97706; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <span>Perangkat sedang gangguan / perawatan</span>
                                </button>
                            @endif
                            <button type="button" @click="pickRevTemplate('Surat peminjaman wajib dilampirkan dalam format PDF yang bertanda tangan dan berstempel resmi.', 'surat')" class="sipetron-chip-btn">
                                <svg style="width: 12px; height: 12px; color: #64748B; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>Surat peminjaman belum lengkap / stempel</span>
                            </button>
                            <button type="button" @click="pickRevTemplate('Tautan Google Drive materi masih terkunci (minta akses). Mohon ubah akses menjadi Siapa saja yang memiliki tautan.', 'materi')" class="sipetron-chip-btn">
                                <svg style="width: 12px; height: 12px; color: #64748B; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span>Akses Google Drive terkunci</span>
                            </button>
                            <button type="button" @click="pickRevTemplate('Durasi materi video melebihi batas ketentuan maksimal (maks. 60 detik). Mohon sesuaikan durasi.', 'materi')" class="sipetron-chip-btn">
                                <svg style="width: 12px; height: 12px; color: #64748B; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Durasi video melebihi batas</span>
                            </button>
                            <button type="button" @click="pickRevTemplate('Resolusi materi tidak sesuai dengan rasio videotron sasaran. Mohon unggah materi beresolusi pas pada tautan drive.', 'materi')" class="sipetron-chip-btn">
                                <svg style="width: 12px; height: 12px; color: #64748B; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                </svg>
                                <span>Resolusi materi tidak pas</span>
                            </button>
                            <button type="button" @click="pickRevTemplate('Berkas surat peminjaman (PDF) serta tautan materi Google Drive belum sesuai ketentuan. Mohon perbaiki keduanya.', 'keduanya')" class="sipetron-chip-btn">
                                <svg style="width: 12px; height: 12px; color: #64748B; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                                </svg>
                                <span>Berkas surat &amp; materi Drive belum sesuai</span>
                            </button>
                        </div>

                        <label style="display: block; font-size: 0.78125rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">
                            Bagian yang Wajib Diperbaiki Pengaju <span style="color: #DC2626;">*</span>
                        </label>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 0.85rem;">
                            <button
                                type="button"
                                @click="toggleRevType('surat')"
                                class="sipetron-rev-target-btn"
                                :class="{ 'is-active': revType === 'surat' || revType === 'keduanya' }"
                            >
                                <svg style="width: 15px; height: 15px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>Surat Permohonan (PDF)</span>
                            </button>
                            <button
                                type="button"
                                @click="toggleRevType('materi')"
                                class="sipetron-rev-target-btn"
                                :class="{ 'is-active': revType === 'materi' || revType === 'keduanya' }"
                            >
                                <svg style="width: 15px; height: 15px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                                <span>Materi Tayang (Drive)</span>
                            </button>
                        </div>

                        <label style="display: block; font-size: 0.78125rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">
                            Catatan Revisi untuk Pengaju <span style="color: #DC2626;">*</span>
                        </label>
                        <textarea
                            x-ref="revInput"
                            x-model="revText"
                            @input="handleRevInput(revText)"
                            rows="4"
                            placeholder="Klik salah satu template di atas atau ketik catatan perbaikan..."
                            class="sipetron-modal-textarea"
                            :class="{ 'is-invalid': Boolean(revError) }"
                        ></textarea>

                        {{-- Pesan Error Validasi --}}
                        <div x-show="revError" class="sipetron-modal-inline-error" style="display: none; margin-top: 0.45rem;">
                            <svg style="width: 15px; height: 15px; color: #DC2626; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span x-text="revError" style="font-weight: 600;"></span>
                        </div>

                        <span style="display: block; font-size: 0.75rem; color: #64748B; margin-top: 0.35rem;">
                            Catatan ini akan langsung tampil di portal pengaju dan diberitahukan via notifikasi.
                        </span>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                        <button type="button" @click="closeModal()" class="fi-btn-batal-custom">Batal</button>
                        <button
                            type="button"
                            @click="submitRevision()"
                            wire:loading.attr="disabled"
                            wire:target="confirmRevision"
                            class="sipetron-btn-kirim-revisi"
                        >
                            <svg wire:loading wire:target="confirmRevision" style="width: 0.95rem; height: 0.95rem; animation: sipetron-spin 0.65s linear infinite;" fill="none" viewBox="0 0 24 24">
                                <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                <path style="opacity: 0.9;" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
                            </svg>
                            <span>Kirim Permintaan Revisi</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- MODAL: Tolak Pengajuan (Client-Side Instant) --}}
            <div
                x-show="modal === 'reject'"
                x-transition.opacity.duration.150ms
                @click.self="closeModal()"
                class="sipetron-instant-modal-overlay"
                style="display: none;"
            >
                <div class="sipetron-instant-modal-card" style="max-width: 520px;">
                    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg style="width: 1.35rem; height: 1.35rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <div>
                                <h3 style="font-size: 1.0625rem; font-weight: 800; color: #0F172A; margin: 0;">Tolak Pengajuan Penayangan</h3>
                                <span style="font-size: 0.75rem; color: #64748B;">Tiket: <strong style="font-family: monospace;">{{ $selected->ticket_code }}</strong></span>
                            </div>
                        </div>
                        <button type="button" @click="closeModal()" aria-label="Tutup" class="fi-modal-close-btn" tabindex="0">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #475569; margin-bottom: 0.35rem;">
                            Pilih Alasan Penolakan Cepat:
                        </label>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.35rem; margin-bottom: 0.75rem;">
                            @if($modalUnavailable)
                                <button
                                    type="button"
                                    @click="pickRejTemplate(@js('Perangkat ' . $modalUnavailable . ' saat ini sedang mengalami gangguan teknis / dalam perbaikan sehingga belum dapat melayani penayangan.'))"
                                    class="sipetron-chip-btn"
                                    style="background: #FEF2F2; border-color: #FECACA; color: #991B1B;"
                                >
                                    <svg style="width: 12px; height: 12px; color: #DC2626; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <span>Perangkat sedang gangguan / perbaikan</span>
                                </button>
                            @endif
                            <button type="button" @click="pickRejTemplate('Materi mengandung unsur promosi komersial / konten yang tidak sesuai dengan ketentuan publikasi universitas.')" class="sipetron-chip-btn">
                                <svg style="width: 12px; height: 12px; color: #DC2626; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                                <span>Unsur komersial / tidak sesuai aturan</span>
                            </button>
                            <button type="button" @click="pickRejTemplate('Jadwal penayangan pada tanggal yang diajukan sudah penuh untuk agenda prioritas universitas.')" class="sipetron-chip-btn">
                                <svg style="width: 12px; height: 12px; color: #DC2626; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Jadwal penuh agenda dinas</span>
                            </button>
                            <button type="button" @click="pickRejTemplate('Pengajuan tidak memenuhi syarat administrasi dan legalitas surat permohonan dinas.')" class="sipetron-chip-btn">
                                <svg style="width: 12px; height: 12px; color: #DC2626; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span>Tidak memenuhi syarat legalitas</span>
                            </button>
                        </div>

                        <label style="display: block; font-size: 0.78125rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">
                            Alasan Penolakan <span style="color: #DC2626;">*</span>
                        </label>
                        <textarea
                            x-ref="rejInput"
                            x-model="rejText"
                            @input="if ((rejText || '').trim().length >= 5) rejError = ''"
                            rows="4"
                            placeholder="Klik salah satu template di atas atau tuliskan alasan penolakan secara jelas..."
                            class="sipetron-modal-textarea"
                            :class="{ 'is-invalid': Boolean(rejError) }"
                        ></textarea>

                        <div x-show="rejError" class="sipetron-modal-inline-error" style="display: none;">
                            <svg style="width: 15px; height: 15px; color: #DC2626; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span x-text="rejError"></span>
                        </div>
                        <span x-show="!rejError" style="font-size: 0.6875rem; color: #64748B; margin-top: 0.3rem; display: block;">
                            Alasan penolakan ini akan dicatat permanen dalam sistem audit dan diberitahukan ke pengaju.
                        </span>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                        <button type="button" @click="closeModal()" class="fi-btn-batal-custom">Batal</button>
                        <button
                            type="button"
                            @click="submitReject()"
                            wire:loading.attr="disabled"
                            wire:target="confirmReject"
                            style="padding: 0.5rem 1.25rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 700; background: #DC2626; color: #FFFFFF; border: none; cursor: pointer; box-shadow: 0 2px 6px rgba(220, 38, 38, 0.25); display: inline-flex; align-items: center; gap: 0.4rem;"
                        >
                            <svg wire:loading wire:target="confirmReject" style="width: 0.95rem; height: 0.95rem; animation: sipetron-spin 0.65s linear infinite;" fill="none" viewBox="0 0 24 24">
                                <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                <path style="opacity: 0.9;" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
                            </svg>
                            <span>Ya, Tolak Pengajuan</span>
                        </button>
                    </div>
                </div>
            </div>
            @endif
