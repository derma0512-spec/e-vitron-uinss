<script>
    let _activeVideotronOutsideHandler = null;

    window.toggleVideotronCalendar = function(triggerEl) {
        const wrp = triggerEl ? triggerEl.closest('.fi-input-wrp') : null;
        const picker = wrp ? wrp.querySelector('.fi-fo-date-time-picker') : null;
        const panel = picker ? picker.querySelector('.fi-fo-date-time-picker-panel') : null;
        const modalContent = wrp ? wrp.closest('.fi-modal-content') : document.querySelector('.fi-modal-content');
        const data = window.Alpine && picker ? window.Alpine.$data(picker) : null;
        if (!data || typeof data.togglePanelVisibility !== 'function') return;

        const isOpening = !data.isOpen();

        if (isOpening) {
            data.togglePanelVisibility();
            if (modalContent) {
                modalContent.style.setProperty('padding-bottom', '58px', 'important');
                setTimeout(() => { modalContent.scrollTop = 155; }, 60);

                if (_activeVideotronOutsideHandler) {
                    document.removeEventListener('click', _activeVideotronOutsideHandler, true);
                }
                _activeVideotronOutsideHandler = (e) => {
                    if (wrp && !wrp.contains(e.target) && panel && !panel.contains(e.target)) {
                        window.closeVideotronCalendar(wrp, () => {
                            if (data.isOpen()) data.togglePanelVisibility();
                        });
                    }
                };
                setTimeout(() => {
                    if (_activeVideotronOutsideHandler) {
                        document.addEventListener('click', _activeVideotronOutsideHandler, true);
                    }
                }, 100);
            }
        } else {
            window.closeVideotronCalendar(wrp, () => {
                if (data.isOpen()) data.togglePanelVisibility();
            });
        }
    };

    window.closeVideotronCalendar = function(pickerOrChild, onClosed) {
        if (_activeVideotronOutsideHandler) {
            document.removeEventListener('click', _activeVideotronOutsideHandler, true);
            _activeVideotronOutsideHandler = null;
        }
        const wrp = pickerOrChild ? pickerOrChild.closest('.fi-input-wrp') : null;
        const picker = wrp ? wrp.querySelector('.fi-fo-date-time-picker') : null;
        const panel = picker ? picker.querySelector('.fi-fo-date-time-picker-panel') : null;
        const modalContent = wrp ? wrp.closest('.fi-modal-content') : document.querySelector('.fi-modal-content');
        const input = wrp ? wrp.querySelector('input[type="text"]') : null;

        if (panel && !panel.style.display.includes('none')) {
            panel.classList.remove('videotron-calendar-closing');
            if (typeof onClosed === 'function') {
                onClosed();
            }
            if (input) {
                input.focus();
            }
            if (modalContent) {
                modalContent.style.removeProperty('padding-bottom');
            }
        } else {
            if (typeof onClosed === 'function') {
                onClosed();
            }
            if (input) {
                input.focus();
            }
            if (modalContent) {
                modalContent.style.removeProperty('padding-bottom');
            }
        }
    };

    // Keyboard Shortcut: Enter = Simpan Perubahan & Escape = Tutup
    if (!window._videotronEnterKeyRegistered) {
        window._videotronEnterKeyRegistered = true;
        document.addEventListener('keydown', function(e) {
            // Handle Escape: Tutup kalender terlebih dahulu jika terbuka, atau tutup modal
            if (e.key === 'Escape') {
                const modal = document.querySelector('.modal-ubah-status-videotron, .fi-modal-window:has(.videotron-condition-radio-grid)');
                if (!modal) return;
                const openCalendar = modal.querySelector('.fi-fo-date-time-picker-panel:not([style*="display: none"]):not(.videotron-calendar-closing)');
                if (openCalendar) {
                    if (window.closeVideotronCalendar) {
                        window.closeVideotronCalendar(openCalendar, () => {
                            const picker = openCalendar.closest('.fi-fo-date-time-picker');
                            const data = window.Alpine && picker ? window.Alpine.$data(picker) : null;
                            if (data && data.isOpen()) data.togglePanelVisibility();
                        });
                    }
                    return;
                }
                const closeBtn = modal.querySelector('.fi-modal-close-btn');
                if (closeBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    closeBtn.click();
                }
                return;
            }

            if (e.key !== 'Enter' || e.isComposing) return;

            // Pastikan modal Ubah Data atau Ubah Status Videotron sedang terbuka dan terlihat
            const modal = document.querySelector('.modal-ubah-data-videotron, .modal-ubah-status-videotron, .fi-modal-window:has(.videotron-condition-radio-grid)');
            if (!modal) return;
            const modalParent = modal.closest('.fi-modal');
            const isModalVisible = modal.offsetParent !== null && (!modalParent || window.getComputedStyle(modalParent).display !== 'none');
            if (!isModalVisible) return;

            const active = document.activeElement;

            // Jika fokus berada pada tombol Close (X) atau tombol Batal, biarkan aksi bawaan tombol berjalan
            if (active && (
                active.classList.contains('fi-modal-close-btn') ||
                active.classList.contains('fi-btn-batal-custom') ||
                active.getAttribute('aria-label') === 'Tutup' ||
                active.getAttribute('aria-label') === 'Close' ||
                active.textContent?.trim() === 'Batal' ||
                active.textContent?.trim() === 'Tutup'
            )) {
                return;
            }

            // Jika fokus berada pada tombol navigasi di dalam kalender yang masih aktif terbuka, biarkan event tombol berjalan
            const activeCalendarPanel = modal.querySelector('.fi-fo-date-time-picker-panel:not([style*="display: none"]):not(.videotron-calendar-closing)');
            if (activeCalendarPanel && active && activeCalendarPanel.contains(active)) {
                return;
            }

            // Textarea: Shift+Enter untuk baris baru (\n), Enter biasa untuk langsung Simpan
            if (active && active.tagName === 'TEXTAREA' && e.shiftKey) {
                return;
            }

            // Langsung eksekusi tombol Simpan Perubahan!
            const primaryBtn = modal.querySelector('.fi-modal-footer-actions button.fi-color-primary, .fi-modal-footer-actions button[type="submit"]');
            if (primaryBtn && !primaryBtn.disabled) {
                e.preventDefault();
                e.stopPropagation();
                primaryBtn.click();
            }
        }, true);
    }

    // Setup Semua Tombol Tutup (X) & Tombol Batal di Seluruh Panel Admin
    function setupAllAdminButtons() {
        // Semua tombol close di modal & slide-over admin (WAJIB BULAT, BUKAN KOTAK)
        document.querySelectorAll('.fi-modal-close-btn, .sipetron-drawer-close-btn, :is(.fi-modal, [role="dialog"], .fi-modal-window) button[aria-label="Tutup"], :is(.fi-modal, [role="dialog"], .fi-modal-window) button[aria-label="Close"]').forEach(btn => {
            // Lindungi tombol close notifikasi toast agar animasi bawaan Filament tetap mulus
            if (btn.closest('.fi-no-notification') || btn.classList.contains('fi-no-notification-close-btn')) {
                return;
            }

            btn.classList.remove('fi-btn-batal-custom');
            btn.style.setProperty('border-radius', '9999px', 'important');
            btn.style.setProperty('width', '32px', 'important');
            btn.style.setProperty('height', '32px', 'important');
            btn.style.setProperty('min-width', '32px', 'important');
            btn.style.setProperty('min-height', '32px', 'important');

            if (btn.getAttribute('title') !== null) {
                btn.removeAttribute('title');
            }
            if (btn.getAttribute('aria-label') !== 'Tutup') {
                btn.setAttribute('aria-label', 'Tutup');
            }
            if (btn.getAttribute('tabindex') !== '0') {
                btn.setAttribute('tabindex', '0');
            }
            const sr = btn.querySelector('.sr-only');
            if (sr && sr.textContent.trim() !== 'Tutup') {
                sr.textContent = 'Tutup';
            }
        });

        // Identifikasi semua tombol Batal di footer modal & form actions (JANGAN tombol close X!)
        document.querySelectorAll('.fi-modal-footer button, .fi-modal-footer-actions button, .fi-form-actions :is(button, a)').forEach(btn => {
            if (btn.classList.contains('fi-modal-close-btn') || btn.classList.contains('sipetron-drawer-close-btn') || btn.closest('.fi-modal-header') || btn.getAttribute('aria-label') === 'Tutup') {
                btn.classList.remove('fi-btn-batal-custom');
                return;
            }
            const txt = (btn.textContent || '').trim().toLowerCase();
            const click = btn.getAttribute('x-on:click') || btn.getAttribute('wire:click') || '';
            const isCancel = txt === 'batal' || txt === 'cancel' || ((click.includes('close') || click.includes('isOpen = false') || click.includes('Modal\', false')) && !btn.querySelector('svg:only-child') && txt.length > 0);
            if (isCancel && !btn.classList.contains('fi-btn-batal-custom')) {
                btn.classList.add('fi-btn-batal-custom');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', setupAllAdminButtons);
    window.addEventListener('open-modal', () => setTimeout(setupAllAdminButtons, 50));
    document.addEventListener('click', (e) => {
        if (e.target && e.target.closest('button, a')) {
            setTimeout(setupAllAdminButtons, 80);
            setTimeout(setupAllAdminButtons, 300);
        }
    });



    // Cegah auto-focus dan text highlight biru pada modal Ubah Data Videotron saat dibuka
    function handleModalUbahDataFocus() {
        const modal = document.querySelector('.modal-ubah-data-videotron');
        if (!modal) return;

        if (window.getSelection) {
            window.getSelection().removeAllRanges();
        }

        const active = document.activeElement;
        if (active && (active.tagName === 'INPUT' || active.tagName === 'TEXTAREA' || active.classList.contains('fi-modal-close-btn'))) {
            if (active.selectionStart !== undefined && active.selectionEnd !== undefined) {
                active.selectionEnd = active.selectionStart;
            }
            modal.focus({ preventScroll: true });
        }
    }

    window.addEventListener('open-modal', () => {
        setTimeout(handleModalUbahDataFocus, 10);
        setTimeout(handleModalUbahDataFocus, 60);
        setTimeout(handleModalUbahDataFocus, 150);
    });

    // Instant Progress Bar saat baris tabel atau tombol aksi diklik (0ms visual feedback)
    (function() {
        let progressBar = null;
        let progressTimeout = null;

        function getProgressBar() {
            if (!progressBar) {
                progressBar = document.getElementById('sipetron-instant-progress');
                if (!progressBar && document.body) {
                    progressBar = document.createElement('div');
                    progressBar.id = 'sipetron-instant-progress';
                    document.body.appendChild(progressBar);
                }
            }
            return progressBar;
        }

        function startInstantProgress() {
            const bar = getProgressBar();
            if (!bar) return;
            clearTimeout(progressTimeout);
            bar.classList.remove('sipetron-progress-done');
            bar.style.width = '0%';
            bar.style.opacity = '1';
            void bar.offsetWidth;
            bar.classList.add('sipetron-progress-active');

            progressTimeout = setTimeout(() => {
                finishInstantProgress();
            }, 2500);
        }

        function finishInstantProgress() {
            const bar = getProgressBar();
            if (!bar) return;
            clearTimeout(progressTimeout);
            bar.classList.remove('sipetron-progress-active');
            bar.classList.add('sipetron-progress-done');
            setTimeout(() => {
                bar.classList.remove('sipetron-progress-done');
                bar.style.width = '0%';
                bar.style.opacity = '0';
            }, 300);
        }

        document.addEventListener('click', function(e) {
            const target = e.target;
            if (!target) return;
            const row = target.closest('.fi-resource-videotrons .fi-ta-row, .fi-resource-videotrons .fi-ta-record-action, .fi-resource-videotrons .fi-ta-table tbody tr, .sipetron-table-row, .sipetron-detail-btn');
            const btn = target.closest('.fi-resource-videotrons .fi-ta-actions button, .fi-resource-videotrons header button, .fi-resource-videotrons .fi-header-actions button, .sipetron-detail-btn');
            if (row || btn) {
                if (!target.closest('.sipetron-ticket-copy-btn')) {
                    startInstantProgress();
                }
            }
        }, true);

        window.addEventListener('open-modal', finishInstantProgress);
        window.addEventListener('close-modal', finishInstantProgress);
        window.addEventListener('sipetron:livewire-done', finishInstantProgress);
    })();
</script>

<script>
    // ========================================================
    // E-VITRON ADMIN DATABASE NOTIFICATIONS (INSTANT AJAX)
    // Hapus satu-satu, Hapus Semua, & Tandai Dibaca tanpa
    // memicu Livewire re-render agar drawer slide-over
    // tetap 100% tenang, mulus, dan tidak loncat / re-slide!
    // ========================================================
    window.setAdminNotifBadgeCount = function(count) {
        count = parseInt(count || 0, 10);

        // 1. Badge di Header Drawer Slide-over
        const headerBadge = document.querySelector('.sipetron-notif-badge');
        if (headerBadge) {
            if (count > 0) {
                headerBadge.textContent = count;
                headerBadge.style.setProperty('display', '', '');
            } else {
                headerBadge.style.setProperty('display', 'none', 'important');
            }
        }

        // 2. Badge Bel di Topbar Admin Filament (Instan 0ms)
        const topbarBadges = document.querySelectorAll('.sipetron-bell-badge, .fi-topbar-database-notifications-btn .fi-badge, [data-fi-modal-id="database-notifications"] .fi-badge, button:has(.heroicon-m-bell, svg) .fi-badge');
        topbarBadges.forEach(b => {
            if (count > 0) {
                b.textContent = count > 99 ? '99+' : count;
                b.style.setProperty('display', '', '');
            } else {
                b.style.setProperty('display', 'none', 'important');
            }
        });

        // 3. Tooltip Lonceng Admin
        const tooltipInner = document.querySelector('.sipetron-admin-notif-tooltip-inner');
        if (tooltipInner) {
            tooltipInner.innerHTML = (count > 0 ? (count + ' Notifikasi Baru') : 'Tidak ada notifikasi baru')
                + '<div class="sipetron-admin-notif-tooltip-arrow"></div>';
        }
    };

    window.updateAdminNotifBadgeCount = function(delta) {
        const headerBadge = document.querySelector('.sipetron-notif-badge');
        const bellBadge = document.querySelector('.sipetron-bell-badge');
        let current = 0;
        if (headerBadge && headerBadge.textContent.trim() && headerBadge.style.display !== 'none') {
            current = parseInt(headerBadge.textContent.trim(), 10) || 0;
        } else if (bellBadge && bellBadge.textContent.trim() && bellBadge.style.display !== 'none') {
            current = parseInt(bellBadge.textContent.trim(), 10) || 0;
        }
        const updated = Math.max(0, current + delta);
        window.setAdminNotifBadgeCount(updated);
    };

    window.deleteAdminSingleNotif = function(e, notifId, url) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        const triggerBtn = e ? (e.currentTarget || e.target) : null;
        const card = triggerBtn ? triggerBtn.closest('.sipetron-notif-card') : null;
        const wrapper = card ? (card.closest('[wire\\:key^="admin-notif-"]') || card.parentElement) : null;
        const targetEl = wrapper || card;
        if (!targetEl) return;

        const isUnread = targetEl.getAttribute('data-unread') === 'true' || targetEl.querySelector('[data-unread="true"]') !== null;

        // 1. Animasi menyusut & menghilang seketika dan sangat mulus (180ms)
        targetEl.style.transition = 'opacity 180ms ease, transform 180ms ease, max-height 200ms ease, margin 200ms ease, padding 200ms ease';
        targetEl.style.overflow = 'hidden';
        targetEl.style.maxHeight = targetEl.offsetHeight + 'px';
        targetEl.offsetHeight; // force reflow
        targetEl.style.opacity = '0';
        targetEl.style.transform = 'translateX(24px)';
        targetEl.style.maxHeight = '0px';
        targetEl.style.marginTop = '0px';
        targetEl.style.marginBottom = '0px';
        targetEl.style.paddingTop = '0px';
        targetEl.style.paddingBottom = '0px';

        // 2. Kurangi badge unread lokal seketika
        if (isUnread) {
            window.updateAdminNotifBadgeCount(-1);
        }

        // 3. Kirim request DELETE di latar belakang tanpa melibatkan Livewire
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            || document.querySelector('input[name="_token"]')?.value
            || '{{ csrf_token() }}';

        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(function(res) {
            return res.json();
        }).then(function(data) {
            if (data && typeof data.unread_count === 'number') {
                window.setAdminNotifBadgeCount(data.unread_count);
            }
        }).catch(function(err) { console.warn('[E-VITRON] Admin notification request failed:', err); });

        // 4. Setelah animasi selesai, bersihkan elemen dari DOM
        setTimeout(function() {
            if (targetEl && targetEl.parentNode) {
                targetEl.parentNode.removeChild(targetEl);
            }

            // Cek apakah item notifikasi sudah habis
            const list = document.querySelector('.sipetron-notif-list');
            if (list) {
                const remaining = list.querySelectorAll('.sipetron-notif-card');
                if (remaining.length === 0) {
                    const actionsBar = document.querySelector('.sipetron-notif-actions-bar');
                    if (actionsBar) {
                        actionsBar.style.setProperty('display', 'none', 'important');
                        actionsBar.classList.add('is-hidden');
                    }

                    const headerBadge = document.querySelector('.sipetron-notif-badge');
                    if (headerBadge) {
                        headerBadge.style.setProperty('display', 'none', 'important');
                    }

                    list.innerHTML = `
                        <div class="flex flex-col items-center justify-center py-20 px-4 text-center">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-3">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-700">Tidak Ada Notifikasi</p>
                            <p class="text-xs text-slate-400 mt-1 max-w-[220px]">Pemberitahuan pembaruan pengajuan videotron Anda akan muncul di sini.</p>
                        </div>
                    `;
                }
            }
        }, 200);
    };

    window.clearAllAdminNotifications = function(url) {
        if (!confirm('Hapus semua notifikasi? Tindakan ini hanya akan mengosongkan daftar notifikasi Anda tanpa menghapus data pengajuan.')) {
            return;
        }

        const list = document.querySelector('.sipetron-notif-list');
        if (list) {
            list.style.transition = 'opacity 180ms ease';
            list.style.opacity = '0';
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            || document.querySelector('input[name="_token"]')?.value
            || '{{ csrf_token() }}';

        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).catch(function(err) { console.warn('[E-VITRON] Admin notification request failed:', err); });

        window.setAdminNotifBadgeCount(0);
        const actionsBar = document.querySelector('.sipetron-notif-actions-bar');
        if (actionsBar) {
            actionsBar.style.setProperty('display', 'none', 'important');
            actionsBar.classList.add('is-hidden');
        }
        const headerBadge = document.querySelector('.sipetron-notif-badge');
        if (headerBadge) {
            headerBadge.style.setProperty('display', 'none', 'important');
        }

        setTimeout(function() {
            if (list) {
                list.innerHTML = `
                    <div class="flex flex-col items-center justify-center py-20 px-4 text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-3">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-slate-700">Tidak Ada Notifikasi</p>
                        <p class="text-xs text-slate-400 mt-1 max-w-[220px]">Pemberitahuan pembaruan pengajuan videotron Anda akan muncul di sini.</p>
                    </div>
                `;
                list.style.opacity = '1';
            }
        }, 200);
    };

    window.markAllAdminNotificationsRead = function(url) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            || document.querySelector('input[name="_token"]')?.value
            || '{{ csrf_token() }}';

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).catch(function(err) { console.warn('[E-VITRON] Admin notification request failed:', err); });

        document.querySelectorAll('[data-unread="true"]').forEach(function(el) {
            el.setAttribute('data-unread', 'false');
        });

        window.setAdminNotifBadgeCount(0);
        const readBtn = document.querySelector('.sipetron-notif-action-btn--read');
        if (readBtn) readBtn.style.display = 'none';
    };
</script>




