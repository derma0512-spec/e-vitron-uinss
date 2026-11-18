    {{-- ===== E-VITRON MINT PASTEL FLOATING TOAST (SAMA PERSIS DENGAN PANEL ADMIN) ===== --}}
    <div id="sipetron-toast-container" class="sipetron-toast-container" aria-live="polite">
        @php
            $flashSuccess = session('success') ?? session('status');
            $flashError = session('error');
            $isNotifFlash = false;
            if ($flashSuccess) {
                $lowerCheck = strtolower($flashSuccess);
                $isNotifFlash = str_contains($lowerCheck, 'dibaca') || str_contains($lowerCheck, 'notifikasi');
            }
        @endphp

        @if($flashSuccess && ! $isNotifFlash)
            @php
                $ticket = null;
                if (preg_match('/(VTR-\d{6}-\d{4})/', $flashSuccess, $m)) {
                    $ticket = $m[1];
                }
                $clean = trim(preg_replace('/(?:untuk\s+)?(?:tiket\s*)?VTR-\d{6}-\d{4}/ui', '', $flashSuccess));
                $clean = trim(preg_replace('/Kode Tiket:\s*/ui', '', $clean));
                $clean = trim($clean, " :-,.");

                // Smart Title Matching
                $title = 'Berhasil';
                $lower = strtolower($flashSuccess);
                if (str_contains($lower, 'revisi') || str_contains($lower, 'perbaikan')) {
                    $title = 'Perbaikan Berhasil Dikirim';
                } elseif (str_contains($lower, 'batal')) {
                    $title = 'Pengajuan Dibatalkan';
                } elseif (str_contains($lower, 'berhasil dikirim') || str_contains($lower, 'pengajuan berhasil')) {
                    $title = 'Pengajuan Berhasil Dikirim';
                } elseif (str_contains($lower, 'dibaca') || str_contains($lower, 'notifikasi')) {
                    $title = 'Notifikasi Diperbarui';
                } elseif ($ticket) {
                    $title = 'Pengajuan Diproses';
                }

                // Hindari duplikasi kalimat judul pada deskripsi meta
                if (strcasecmp(rtrim($clean, '! .'), rtrim($title, '! .')) === 0) {
                    $clean = $ticket ? 'Menunggu verifikasi admin' : '';
                }
            @endphp
            <div class="sipetron-toast-floating-item is-entering" id="initial-flash-toast">
                <div class="sipetron-toast-card">
                    <div class="sipetron-toast-icon-circle">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <div class="sipetron-toast-content">
                        <h4 class="sipetron-toast-title">
                            {{ $title }}
                        </h4>
                        <div class="sipetron-toast-meta">
                            @if($ticket)
                                <span class="sipetron-toast-ticket">{{ $ticket }}</span>
                                @if($clean)
                                    <span class="sipetron-toast-dot" aria-hidden="true">·</span>
                                @endif
                            @endif
                            @if($clean)
                                <span class="sipetron-toast-desc" title="{{ $clean }}">{{ $clean }}</span>
                            @endif
                        </div>
                    </div>
                    <button type="button" class="sipetron-toast-close-btn" onclick="dismissToast(this.closest('.sipetron-toast-floating-item'))" title="Tutup notifikasi" aria-label="Tutup notifikasi">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @if($flashError)
            <div class="sipetron-toast-floating-item is-entering" id="initial-flash-error-toast">
                <div class="sipetron-toast-card is-danger">
                    <div class="sipetron-toast-icon-circle is-danger">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </div>
                    <div class="sipetron-toast-content">
                        <h4 class="sipetron-toast-title">
                            Terjadi Kesalahan
                        </h4>
                        <div class="sipetron-toast-meta">
                            <span class="sipetron-toast-desc" title="{{ $flashError }}">{{ $flashError }}</span>
                        </div>
                    </div>
                    <button type="button" class="sipetron-toast-close-btn" onclick="dismissToast(this.closest('.sipetron-toast-floating-item'))" title="Tutup notifikasi" aria-label="Tutup notifikasi">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>
        @endif
    </div>

