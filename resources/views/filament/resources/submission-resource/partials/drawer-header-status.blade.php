{{-- Container Utama Drawer Detail Pengajuan Admin: Header, Banner Status & Footer (Dipanggil oleh list-submissions.blade.php) --}}
        {{-- Drawer Root Container with Scroll-Lock Lifecycle & Escape Handler --}}
        <div 
            x-data="{
                isOpen: true,
                editingDecision: false,
                modal: null,
                busy: null,
                revText: '',
                revError: '',
                revType: 'surat',
                rejText: '',
                rejError: '',
                openModal(type, preset = '', defaultRevType = 'surat') {
                    this.editingDecision = false;
                    this.revError = '';
                    this.rejError = '';
                    if (type === 'revision') {
                        this.revText = preset || '';
                        this.revType = defaultRevType || 'surat';
                    } else if (type === 'reject') {
                        this.rejText = preset || '';
                    }
                    this.busy = type;
                    this.modal = type;
                    // Spinner aktif sesaat memberi respon taktil, lalu mati tepat saat popup modal selesai terbuka (150ms)
                    setTimeout(() => { if (this.busy === type) this.busy = null; }, 150);
                    this.$nextTick(() => {
                        if (type === 'revision' && this.$refs.revInput) {
                            this.$refs.revInput.focus();
                        } else if (type === 'reject' && this.$refs.rejInput) {
                            this.$refs.rejInput.focus();
                        }
                    });
                },
                closeModal() {
                    this.modal = null;
                    this.busy = null;
                    this.revError = '';
                    this.rejError = '';
                },
                handleRevInput(text) {
                    const clean = (text || '').trim();
                    if (clean.length >= 5) {
                        this.revError = '';
                    }
                    const lower = clean.toLowerCase();
                    const hasSurat = ['surat', 'peminjaman', 'permohonan', 'stempel', 'tanda tangan', 'ttd', 'dokumen', 'pdf', 'berkas'].some(k => lower.includes(k));
                    const hasMateri = ['drive', 'link', 'tautan', 'video', 'poster', 'durasi', 'resolusi', 'akses', 'materi', 'gdrive', 'konten'].some(k => lower.includes(k));
                    if (hasSurat && hasMateri) {
                        this.revType = 'keduanya';
                    } else if (hasSurat) {
                        this.revType = 'surat';
                    } else if (hasMateri) {
                        this.revType = 'materi';
                    }
                },
                toggleRevType(target) {
                    if (target === 'surat') {
                        if (this.revType === 'materi') {
                            this.revType = 'keduanya';
                        } else if (this.revType === 'keduanya') {
                            this.revType = 'materi';
                        } else {
                            this.revType = 'surat';
                        }
                    } else if (target === 'materi') {
                        if (this.revType === 'surat') {
                            this.revType = 'keduanya';
                        } else if (this.revType === 'keduanya') {
                            this.revType = 'surat';
                        } else {
                            this.revType = 'materi';
                        }
                    }
                },
                pickRevTemplate(val, type = 'surat') {
                    this.revText = val;
                    this.revType = type || 'surat';
                    this.revError = '';
                    if (this.$refs.revInput) { this.$refs.revInput.focus(); }
                },
                pickRejTemplate(val) {
                    this.rejText = val;
                    this.rejError = '';
                    if (this.$refs.rejInput) { this.$refs.rejInput.focus(); }
                },
                submitApprove() {
                    $wire.confirmApprove().then(() => { this.closeModal(); });
                },
                submitRevision() {
                    const val = (this.revText || '').trim();
                    if (val.length < 5) {
                        this.revError = 'Catatan revisi wajib diisi dengan jelas (minimal 5 karakter).';
                        if (this.$refs.revInput) { this.$refs.revInput.focus(); }
                        return;
                    }

                    if (!this.revType) {
                        this.revType = 'surat';
                    }

                    this.revError = '';
                    $wire.confirmRevision(val, this.revType).then(() => { this.closeModal(); });
                },
                submitReject() {
                    const val = (this.rejText || '').trim();
                    if (val.length < 5) {
                        this.rejError = 'Alasan penolakan wajib diisi dengan jelas (minimal 5 karakter).';
                        if (this.$refs.rejInput) { this.$refs.rejInput.focus(); }
                        return;
                    }
                    this.rejError = '';
                    $wire.confirmReject(val).then(() => { this.closeModal(); });
                },
                navHandler: null,
                wheelHandler: null,
                keyHandler: null,
                init() {
                    this.isOpen = true;
                    this.applyLock();

                    this.navHandler = () => {
                        this.removeLock();
                    };
                    document.addEventListener('livewire:navigating', this.navHandler, { once: true });

                    this.wheelHandler = (e) => {
                        if (!this.isOpen) return;
                        if (!e.target.closest('.sipetron-drawer-body') && !e.target.closest('.sipetron-instant-modal-card')) {
                            e.preventDefault();
                        }
                    };
                    window.addEventListener('wheel', this.wheelHandler, { passive: false });
                    window.addEventListener('touchmove', this.wheelHandler, { passive: false });

                    this.keyHandler = (e) => {
                        if (!this.isOpen) return;
                        if (['ArrowUp', 'ArrowDown', 'PageUp', 'PageDown', 'Home', 'End', ' '].includes(e.key)) {
                            if (!e.target.closest('.sipetron-drawer-body') && !e.target.closest('.sipetron-instant-modal-card') && !['INPUT', 'TEXTAREA'].includes(e.target.tagName)) {
                                e.preventDefault();
                            }
                        }
                    };
                    window.addEventListener('keydown', this.keyHandler);
                },
                destroy() {
                    this.cleanupListeners();
                    this.removeLock();
                },
                cleanupListeners() {
                    if (this.navHandler) {
                        document.removeEventListener('livewire:navigating', this.navHandler);
                    }
                    if (this.wheelHandler) {
                        window.removeEventListener('wheel', this.wheelHandler);
                        window.removeEventListener('touchmove', this.wheelHandler);
                    }
                    if (this.keyHandler) {
                        window.removeEventListener('keydown', this.keyHandler);
                    }
                },
                applyLock() {
                    const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
                    if (scrollbarWidth > 0) {
                        document.documentElement.style.setProperty('--sipetron-scrollbar-width', scrollbarWidth + 'px');
                    } else {
                        document.documentElement.style.setProperty('--sipetron-scrollbar-width', '0px');
                    }
                    // Only lock html element to prevent breaking position:sticky on sidebar & topbar
                    document.documentElement.classList.add('sipetron-drawer-locked');
                },
                removeLock() {
                    document.documentElement.classList.remove('sipetron-drawer-locked');
                    document.documentElement.style.removeProperty('--sipetron-scrollbar-width');
                },
                closeInstant() {
                    if (!this.isOpen) return;

                    // 1. Immediately trigger client-side exit transition (150-200ms)
                    this.modal = null;
                    this.isOpen = false;
                    this.cleanupListeners();
                    this.removeLock();
                    if (document.activeElement && typeof document.activeElement.blur === 'function') {
                        document.activeElement.blur();
                    }

                    // 2. Clean URL query string immediately without full reload
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

                    // 3. Sync Livewire in background after exit animation completes
                    setTimeout(() => {
                        $wire.closeDetail();
                    }, 180);
                },
                handleEscape() {
                    if (this.modal) {
                        this.closeModal();
                        return;
                    }
                    this.closeInstant();
                }
            }"
            wire:key="sipetron-drawer-root"
            @keydown.escape.window="handleEscape()"
        >
            {{-- Backdrop Overlay (Blur & Dimmed): Covers whole screen, instant click close --}}
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
                                Detail Pengajuan
                            </span>
                            <span style="font-size: 0.75rem; color: #94A3B8;">&bull;</span>
                            <span style="font-size: 0.75rem; color: #64748B;">
                                {{ $selected->created_at->format('d M Y, H:i') }} WIB
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

            {{-- 2. Scrollable Body --}}
            <div class="sipetron-drawer-body">
                
                {{-- Konteks Status: Pre-check Siap Tayang / Catatan Revisi / Alasan Tolak / Status Unduh Materi --}}
                @if($selected->status === 'menunggu')
                    @php
                        $preCheck = app(\App\Services\SubmissionDecisionService::class)->checkAvailabilityAndConflicts($selected);
                    @endphp
                    @if($preCheck['can_approve'])
                        <div style="background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 0.75rem; padding: 0.75rem 0.95rem; display: flex; align-items: flex-start; gap: 0.6rem;">
                            <div style="width: 22px; height: 22px; border-radius: 50%; background: #DCFCE7; color: #16A34A; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px;">
                                <svg style="width: 13px; height: 13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div style="min-width: 0;">
                                <div style="font-size: 0.8125rem; font-weight: 800; color: #166534; line-height: 1.2;">
                                    Layar Siap & Perangkat Normal
                                </div>
                                <div style="font-size: 0.71875rem; color: #15803D; margin-top: 3px; line-height: 1.4;">
                                    Seluruh perangkat videotron sasaran berstatus aktif normal dan siap ditambahkan ke jadwal.
                                </div>
                            </div>
                        </div>

                    @else
                        <div style="background: #FEF2F2; border: 1px solid #FECACA; border-radius: 0.75rem; padding: 0.75rem 0.95rem; display: flex; align-items: flex-start; gap: 0.6rem;">
                            <div style="width: 22px; height: 22px; border-radius: 50%; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px;">
                                <svg style="width: 13px; height: 13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div style="min-width: 0;">
                                <div style="font-size: 0.8125rem; font-weight: 800; color: #991B1B; line-height: 1.2;">
                                    Perhatian: Ada Gangguan Perangkat
                                </div>
                                <div style="font-size: 0.71875rem; color: #B91C1C; margin-top: 3px; line-height: 1.4;">
                                    {{ $preCheck['error_message'] }}
                                </div>
                            </div>
                        </div>
                    @endif
                @elseif($selected->status === 'revisi')
                    <div style="background: #FFF7ED; border: 1px solid #FED7AA; border-radius: 0.75rem; padding: 0.75rem 0.95rem;">
                        <div style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.78125rem; font-weight: 800; color: #9A3412; margin-bottom: 0.3rem;">
                            <svg style="width: 15px; height: 15px; color: #D97706; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>Menunggu Perbaikan dari Pengaju</span>
                        </div>
                        <div style="font-size: 0.78125rem; color: #7C2D12; line-height: 1.45; word-break: break-word; background: #FFFFFF; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #FFEDD5; margin-top: 0.35rem;">
                            "{{ $latestRevision?->admin_feedback ?? $selected->admin_notes ?? '-' }}"
                        </div>
                        <div style="font-size: 0.6875rem; color: #9A3412; margin-top: 0.4rem;">
                            Setelah pengaju mengirim perbaikan, tiket ini akan otomatis kembali ke antrean <strong>Perlu Direview</strong>.
                        </div>
                    </div>
                @elseif($selected->status === 'ditolak')
                    <div style="background: #FEF2F2; border: 1px solid #FECACA; border-radius: 0.75rem; padding: 0.75rem 0.95rem;">
                        <div style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.78125rem; font-weight: 800; color: #991B1B; margin-bottom: 0.3rem;">
                            <svg style="width: 15px; height: 15px; color: #DC2626; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Alasan Penolakan Pengajuan</span>
                        </div>
                        <div style="font-size: 0.78125rem; color: #7F1D1D; line-height: 1.45; word-break: break-word; background: #FFFFFF; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #FEE2E2; margin-top: 0.35rem;">
                            "{{ $selected->admin_notes ?: '-' }}"
                        </div>
                    </div>
                @elseif($hasMaterialStage)
                    <div style="background: {{ $selected->material_downloaded_at ? '#F0FDF4' : '#FBF3E0' }}; border: 1px solid {{ $selected->material_downloaded_at ? '#BBF7D0' : '#F0DDA8' }}; border-radius: 0.75rem; padding: 0.75rem 0.95rem; display: flex; justify-content: space-between; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                        <div style="min-width: 0;">
                            <div style="font-size: 0.6875rem; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.04em;">Kesiapan Materi Tayang</div>
                            <div style="font-size: 0.875rem; font-weight: 800; color: {{ $selected->material_downloaded_at ? '#15803D' : '#9A6B12' }};">
                                {{ $selected->material_downloaded_at ? 'Materi Siap / Sudah Diunduh' : 'Materi Belum Diunduh' }}
                            </div>
                            @if($selected->material_downloaded_at)
                                <div style="font-size: 0.6875rem; color: #475569; margin-top: 2px;">
                                    Diunduh pada {{ $selected->material_downloaded_at->format('d M Y, H:i') }} WIB oleh {{ $selected->materialDownloader?->name ?? 'Admin' }}
                                </div>
                            @else
                                <div style="font-size: 0.6875rem; color: #9A6B12; margin-top: 2px;">
                                    Harap unduh materi dari Google Drive sebelum tanggal mulai penayangan.
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                {{-- Kartu Dokumen, Rincian Kegiatan & Riwayat Status --}}
                @include('filament.resources.submission-resource.partials.drawer-docs-and-footer')
            </div>

            {{-- 3. Sticky Drawer Footer: Tindakan Admin Cepat --}}
            <div class="sipetron-drawer-footer" @wheel.prevent.stop @touchmove.prevent.stop>
                @if($selected->status === 'menunggu')
                    <div wire:key="footer-menunggu-{{ $selected->id }}" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.625rem;">
                        <button 
                            type="button" 
                            @click="openModal('approve')" 
                            class="sipetron-action-btn sipetron-btn-approve"
                            title="Setujui pengajuan penayangan"
                        >
                            <svg x-show="busy !== 'approve'" style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            <svg x-show="busy === 'approve'" style="display: none; width: 1rem; height: 1rem; animation: sipetron-spin 0.65s linear infinite;" fill="none" viewBox="0 0 24 24">
                                <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                <path style="opacity: 0.9;" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
                            </svg>
                            <span>Setujui</span>
                        </button>
                        <button 
                            type="button" 
                            @click="openModal('revision')" 
                            class="sipetron-action-btn sipetron-btn-revision"
                            title="Minta pengaju merevisi materi atau dokumen"
                        >
                            <svg x-show="busy !== 'revision'" style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <svg x-show="busy === 'revision'" style="display: none; width: 1rem; height: 1rem; animation: sipetron-spin 0.65s linear infinite;" fill="none" viewBox="0 0 24 24">
                                <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                <path style="opacity: 0.9;" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
                            </svg>
                            <span>Revisi</span>
                        </button>
                        <button 
                            type="button" 
                            @click="openModal('reject')" 
                            class="sipetron-action-btn sipetron-btn-reject"
                            title="Tolak pengajuan penayangan"
                        >
                            <svg x-show="busy !== 'reject'" style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <svg x-show="busy === 'reject'" style="display: none; width: 1rem; height: 1rem; animation: sipetron-spin 0.65s linear infinite;" fill="none" viewBox="0 0 24 24">
                                <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                <path style="opacity: 0.9;" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
                            </svg>
                            <span>Tolak</span>
                        </button>
                    </div>
                @elseif(in_array($selected->status, ['disetujui', 'revisi', 'ditolak'], true))
                    {{-- Baris 1 (Default): Kiri Kotak Status / Tombol Unduh, Kanan Tombol Kompak [Edit] (Identik Layout Footer Riwayat) --}}
                    <div wire:key="footer-decided-default-{{ $selected->id }}-{{ $selected->status }}" x-show="!editingDecision" class="sipetron-footer-flex-row">
                        @if($selected->status === 'disetujui' && ! $selected->material_downloaded_at)
                            <button 
                                type="button" 
                                wire:click="markMaterialDownloaded" 
                                wire:loading.attr="disabled"
                                wire:target="markMaterialDownloaded"
                                wire:confirm="Tandai materi tiket {{ $selected->ticket_code }} sudah diunduh? Pastikan file benar-benar sudah tersimpan di penyimpanan operator."
                                class="sipetron-action-btn sipetron-btn-approve"
                                style="flex: 1 1 0%; min-width: 0; height: 36px; min-height: 36px; padding: 0 0.85rem;"
                            >
                                <svg wire:loading.remove wire:target="markMaterialDownloaded" style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <svg wire:loading wire:target="markMaterialDownloaded" style="width: 1rem; height: 1rem; animation: sipetron-spin 0.65s linear infinite;" fill="none" viewBox="0 0 24 24">
                                    <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                    <path style="opacity: 0.9;" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
                                </svg>
                                <span>Tandai Materi Sudah Diunduh</span>
                            </button>
                        @elseif($selected->status === 'disetujui' && $selected->material_downloaded_at)
                            <div class="sipetron-footer-status-box" style="color: #0F3D2E; background: #F0FDF4; border: 1px dashed #BBF7D0;">
                                <svg style="width: 14px; height: 14px; color: #16A34A; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Materi sudah siap untuk jadwal penayangan videotron.</span>
                            </div>
                        @elseif($selected->status === 'revisi')
                            <div class="sipetron-footer-status-box" style="color: #9A3412; background: #FFF7ED; border: 1px dashed #FED7AA;">
                                <svg style="width: 14px; height: 14px; color: #D97706; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Menunggu pengaju mengirim ulang perbaikan berkas/materi.</span>
                            </div>
                        @elseif($selected->status === 'ditolak')
                            <div class="sipetron-footer-status-box" style="color: #991B1B; background: #FEF2F2; border: 1px dashed #FECACA;">
                                <svg style="width: 14px; height: 14px; color: #DC2626; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Pengajuan telah ditolak oleh Admin.</span>
                            </div>
                        @endif

                        <button
                            type="button"
                            @click="editingDecision = true"
                            class="sipetron-footer-edit-btn"
                            title="Ubah atau koreksi keputusan status pengajuan"
                        >
                            <svg style="width: 14px; height: 14px; color: #0F3D2E; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span>Edit</span>
                        </button>
                    </div>

                    {{-- Baris 1 (Saat Klik Edit): Pilihan Status di Kiri & Tombol Batal di Kanan (Tetap 1 Baris 36px) --}}
                    <div
                        wire:key="footer-decided-edit-{{ $selected->id }}-{{ $selected->status }}"
                        x-show="editingDecision"
                        x-cloak
                        class="sipetron-footer-flex-row"
                        style="display: none;"
                    >
                        <div style="flex: 1 1 0%; min-width: 0; display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem;">
                            @if($selected->status !== 'disetujui')
                                <button
                                    type="button"
                                    @click="openModal('approve')"
                                    class="sipetron-action-btn sipetron-btn-approve"
                                    style="height: 36px; min-height: 36px; padding: 0 0.75rem; font-size: 0.75rem;"
                                >
                                    <svg style="width: 0.9rem; height: 0.9rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Setujui</span>
                                </button>
                            @endif

                            @if($selected->status !== 'revisi')
                                <button
                                    type="button"
                                    @click="openModal('revision')"
                                    class="sipetron-action-btn sipetron-btn-revision"
                                    style="height: 36px; min-height: 36px; padding: 0 0.75rem; font-size: 0.75rem;"
                                >
                                    <svg style="width: 0.9rem; height: 0.9rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <span>Revisi</span>
                                </button>
                            @endif

                            @if($selected->status !== 'ditolak')
                                <button
                                    type="button"
                                    @click="openModal('reject')"
                                    class="sipetron-action-btn sipetron-btn-reject"
                                    style="height: 36px; min-height: 36px; padding: 0 0.75rem; font-size: 0.75rem;"
                                >
                                    <svg style="width: 0.9rem; height: 0.9rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    <span>Tolak</span>
                                </button>
                            @endif
                        </div>

                        <button
                            type="button"
                            @click="editingDecision = false"
                            class="sipetron-footer-edit-btn is-cancel"
                            title="Batal ubah keputusan"
                        >
                            <svg style="width: 13px; height: 13px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Batal</span>
                        </button>
                    </div>
                @elseif(in_array($selected->status, ['selesai', 'dibatalkan'], true))
                    <div wire:key="footer-final-{{ $selected->id }}-{{ $selected->status }}" style="font-size: 0.78125rem; color: #64748B; text-align: center; background: #FFFFFF; padding: 0.6rem 0.75rem; border-radius: 0.5rem; border: 1px dashed #CBD5E1; display: flex; align-items: center; justify-content: center; gap: 0.4rem;">
                        <svg style="width: 14px; height: 14px; color: #94A3B8; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Pengajuan sudah final. Lihat tab <strong>Riwayat</strong> untuk linimasa lengkap.</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Modal Konfirmasi Keputusan Admin (Setujui / Revisi / Tolak) --}}
        @include('filament.resources.submission-resource.partials.drawer-action-modals')
    </div>