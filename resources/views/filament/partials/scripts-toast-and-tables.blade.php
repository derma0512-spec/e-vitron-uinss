<script>
    // ========================================================
    // E-VITRON TOAST QUEUE & LIFECYCLE MANAGER
    // 1 Active Toast, FIFO Queue, Deduplikasi, Hover-Pause,
    // Smooth 180ms transitions, A11y reduced motion
    // ========================================================
    window.SipetronToastQueue = {
        activeId: null,
        queue: [],
        recentSignatures: new Map(),
        timer: null,
        timeRemaining: 0,
        timerStartedAt: 0,
        isPaused: false,
        currentDuration: 4500,
        toasts: new Map(),

        register: function (toast) {
            this.toasts.set(toast.id, toast);

            const now = Date.now();
            if (toast.sig) {
                const lastTime = this.recentSignatures.get(toast.sig);
                if (lastTime && (now - lastTime < 4000)) {
                    setTimeout(() => toast.closeFn(true), 50);
                    return;
                }
                this.recentSignatures.set(toast.sig, now);
            }

            for (const [s, t] of this.recentSignatures.entries()) {
                if (now - t > 10000) this.recentSignatures.delete(s);
            }

            if (!this.activeId) {
                this.display(toast.id);
            } else {
                this.queue.push(toast.id);
                toast.hideFn();
            }
        },

        display: function (id) {
            const toast = this.toasts.get(id);
            if (!toast) {
                this.processNext();
                return;
            }

            this.activeId = id;
            toast.showFn();
            this.startTimer(toast.duration);
        },

        startTimer: function (duration) {
            this.clearTimer();
            this.currentDuration = duration || 4500;
            this.timeRemaining = this.currentDuration;
            this.timerStartedAt = Date.now();
            this.isPaused = false;

            this.timer = setTimeout(() => {
                this.dismiss(this.activeId);
            }, this.currentDuration);
        },

        pause: function () {
            if (!this.activeId || this.isPaused) return;
            this.isPaused = true;
            if (this.timer) {
                clearTimeout(this.timer);
                this.timer = null;
            }
            const elapsed = Date.now() - this.timerStartedAt;
            this.timeRemaining = Math.max(1500, this.currentDuration - elapsed);
        },

        resume: function () {
            if (!this.activeId || !this.isPaused) return;
            this.isPaused = false;
            this.timerStartedAt = Date.now();
            this.currentDuration = this.timeRemaining;
            this.timer = setTimeout(() => {
                this.dismiss(this.activeId);
            }, this.timeRemaining);
        },

        dismiss: function (id) {
            if (id && this.activeId !== id) {
                this.queue = this.queue.filter(qId => qId !== id);
                const toast = this.toasts.get(id);
                if (toast) {
                    toast.closeFn(true);
                    this.toasts.delete(id);
                }
                return;
            }

            this.clearTimer();
            const currentActiveId = this.activeId;
            const activeToast = this.toasts.get(currentActiveId);
            this.activeId = null;

            if (activeToast) {
                activeToast.leaveFn(() => {
                    activeToast.closeFn(false);
                    this.toasts.delete(currentActiveId);
                    setTimeout(() => {
                        this.processNext();
                    }, 150);
                });
            } else {
                this.processNext();
            }
        },

        processNext: function () {
            this.clearTimer();
            if (this.queue.length > 0) {
                const nextId = this.queue.shift();
                this.display(nextId);
            } else {
                this.activeId = null;
            }
        },

        clearTimer: function () {
            if (this.timer) {
                clearTimeout(this.timer);
                this.timer = null;
            }
        },

        unregister: function (id) {
            this.toasts.delete(id);
            this.queue = this.queue.filter(qId => qId !== id);
            if (this.activeId === id) {
                this.activeId = null;
                this.clearTimer();
                setTimeout(() => this.processNext(), 150);
            }
        }
    };

    const registerToastAlpine = () => {
        if (!window.Alpine) return;
        window.Alpine.data('sipetronToastItem', ({ id, sig, isError }) => ({
            id: id,
            isVisible: false,
            isEntering: false,
            isLeaving: false,

            init() {
                const duration = isError ? 9000 : 4500;
                window.SipetronToastQueue.register({
                    id: this.id,
                    sig: sig,
                    duration: duration,
                    showFn: () => {
                        this.isVisible = true;
                        this.isEntering = true;
                        this.isLeaving = false;
                        setTimeout(() => { this.isEntering = false; }, 200);
                    },
                    hideFn: () => {
                        this.isVisible = false;
                        this.isEntering = false;
                        this.isLeaving = false;
                    },
                    leaveFn: (onComplete) => {
                        this.isLeaving = true;
                        setTimeout(() => {
                            this.isVisible = false;
                            if (onComplete) onComplete();
                        }, 180);
                    },
                    closeFn: (silent) => {
                        window.dispatchEvent(new CustomEvent('notificationClosed', { detail: { id: this.id } }));
                    }
                });
            },

            onMouseEnter() {
                window.SipetronToastQueue.pause();
            },

            onMouseLeave() {
                window.SipetronToastQueue.resume();
            },

            close() {
                window.SipetronToastQueue.dismiss(this.id);
            },

            destroy() {
                window.SipetronToastQueue.unregister(this.id);
            }
        }));
    };

    if (window.Alpine) {
        registerToastAlpine();
    } else {
        document.addEventListener('alpine:init', registerToastAlpine);
    }
</script>

<script>
    function clearSubmissionsRowLoading() {
        document.querySelectorAll('.sipetron-row-loading').forEach(function(el) {
            el.classList.remove('sipetron-row-loading');
        });
    }

    function clearVideotronRowLoading() {
        document.querySelectorAll('.videotron-row-loading').forEach(function(el) {
            el.classList.remove('videotron-row-loading');
        });
        document.querySelectorAll('.videotron-btn-loading').forEach(function(el) {
            el.classList.remove('videotron-btn-loading');
        });
        clearSubmissionsRowLoading();
        window.dispatchEvent(new CustomEvent('sipetron:livewire-done'));
    }

    const triggerLivewireDone = () => {
        const elapsed = Date.now() - (window._subRowClickTime || 0);
        const remaining = Math.max(0, 320 - elapsed);
        if (remaining > 0) {
            setTimeout(() => {
                clearSubmissionsRowLoading();
                window.dispatchEvent(new CustomEvent('sipetron:livewire-done'));
            }, remaining);
        } else {
            clearSubmissionsRowLoading();
            window.dispatchEvent(new CustomEvent('sipetron:livewire-done'));
        }
    };
    const attachLivewireCommitHook = () => {
        if (!window.Livewire || typeof window.Livewire.hook !== 'function') return;
        window.Livewire.hook('commit', ({ succeed }) => {
            if (typeof succeed === 'function') {
                succeed(() => {
                    triggerLivewireDone();
                });
            }
        });
    };
    if (window.Livewire) {
        attachLivewireCommitHook();
    } else {
        document.addEventListener('livewire:init', attachLivewireCommitHook);
    }

    function clearVideotronRowActive() {
        document.querySelectorAll('.videotron-row-active').forEach(function(el) {
            el.classList.remove('videotron-row-active');
        });
    }

    // Penanganan klik pada baris tabel Kelola Videotron:
    // - Toggle Aktif bekerja mandiri
    // - Tombol Kondisi hanya membuka modal Kondisi
    // - Tombol Ubah Data hanya membuka laci Ubah Data
    // - Klik area baris videotron hanya membuka laci Tayang Hari Ini (inspect_broadcasts)
    document.addEventListener('click', function(e) {
        const table = e.target.closest('.fi-resource-videotrons .fi-ta-table');
        if (!table) return;

        const tr = e.target.closest('tbody tr');
        if (!tr) return;

        // 1. Jika yang diklik adalah area kolom toggle switch (Aktif), biarkan bekerja mandiri
        if (e.target.closest('input[type="checkbox"], [role="switch"], .fi-ta-toggle, .fi-table-cell-is-active, td:has(.fi-ta-toggle)')) {
            return;
        }

        function markRowActive(targetBtn) {
            document.querySelectorAll('.videotron-row-loading').forEach(function(el) {
                el.classList.remove('videotron-row-loading');
            });
            document.querySelectorAll('.videotron-btn-loading').forEach(function(el) {
                el.classList.remove('videotron-btn-loading');
            });
            clearVideotronRowActive();
            tr.classList.add('videotron-row-loading');
            tr.classList.add('videotron-row-active');
            if (targetBtn) {
                targetBtn.classList.add('videotron-btn-loading');
            }
            setTimeout(clearVideotronRowLoading, 3000);
        }

        // 2. Klik di dalam kolom Aksi: hanya jalankan tombol yang benar-benar diklik
        const actionsCell = e.target.closest('.fi-ta-actions-cell, .fi-ta-actions');
        if (actionsCell) {
            const actionBtn = e.target.closest('.fi-ta-actions button, .fi-ta-actions a');
            if (actionBtn) {
                markRowActive(actionBtn);
            }
            return;
        }

        // 3. Klik di area baris videotron (Foto / Videotron & Lokasi / Resolusi / Kondisi):
        // buka laci Tayang Hari Ini (inspect_broadcasts) tanpa membajak ke Ubah Data
        const inspectBtn = tr.querySelector('button[wire\\:click*="inspect_broadcasts"]');
        if (!inspectBtn) return;

        markRowActive(null);

        const clickedNativeInspectBtn = e.target.closest('button[wire\\:click*="inspect_broadcasts"]');
        if (!clickedNativeInspectBtn) {
            inspectBtn.click();
        }
    }, true);

    // Pantau pembukaan & penutupan modal Filament (laci Tayang Hari Ini, Ubah Data, maupun modal Kondisi)
    window.addEventListener('open-modal', clearVideotronRowLoading);
    window.addEventListener('close-modal', function() {
        clearVideotronRowLoading();
        clearVideotronRowActive();

        // Bersihkan query ?inspect=ID setelah laci ditutup agar refresh halaman tidak membuka ulang laci lama
        if (window.location.search.indexOf('inspect=') !== -1 && window.history && window.history.replaceState) {
            const url = new URL(window.location.href);
            if (url.searchParams.has('inspect')) {
                url.searchParams.delete('inspect');
                window.history.replaceState({}, '', url.pathname + (url.searchParams.toString() ? '?' + url.searchParams.toString() : '') + url.hash);
            }
        }
    });
    window.addEventListener('filament-modal-opened', clearVideotronRowLoading);
    window.addEventListener('filament-modal-closed', function() {
        clearVideotronRowLoading();
        clearVideotronRowActive();
    });

    // Bersihkan garis aksen saat klik overlay, tombol close, atau tombol Batal
    document.addEventListener('click', function(e) {
        if (e.target.closest('.fi-modal-close-overlay, .fi-modal-close-btn, [data-action="close"], .fi-modal-footer-actions button:not([type="submit"])')) {
            clearVideotronRowActive();
        }
    }, true);
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            clearVideotronRowActive();
        }
    });

    // Penanganan klik pada baris tabel Pengajuan (0ms fade feedback & spinner)
    document.addEventListener('click', function(e) {
        const subRow = e.target.closest('.sipetron-table tbody tr.sipetron-table-row');
        if (!subRow) return;

        // Jangan trigger loading jika yang diklik tombol salin tiket atau checklist penurunan
        if (e.target.closest('.sipetron-ticket-copy-btn, .sipetron-takedown-btn')) return;

        // Bersihkan loading baris lain
        document.querySelectorAll('.sipetron-row-loading').forEach(function(el) {
            el.classList.remove('sipetron-row-loading');
        });

        // Terapkan efek memudar instan (0ms) & catat timestamp klik agar spinner sempat berputar mulus
        window._subRowClickTime = Date.now();
        subRow.classList.add('sipetron-row-loading');
        setTimeout(clearSubmissionsRowLoading, 3000);
    }, true);

    window.addEventListener('sipetron:livewire-done', clearSubmissionsRowLoading);

    // Sinkronisasi langsung angka badge Sidebar Riwayat & Rekap saat admin menceklis / membatalkan penurunan
    window.addEventListener('sipetron:takedown-count-updated', function(e) {
        const count = Number(e?.detail?.count ?? 0);
        document.querySelectorAll('.fi-sidebar-nav a.fi-sidebar-item-button[href*="history-and-recap"]').forEach(function(navLink) {
            let badge = navLink.querySelector('.fi-badge');
            if (count > 0) {
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium px-2 py-1 ms-auto';
                    const inner = document.createElement('span');
                    inner.className = 'fi-badge-label';
                    badge.appendChild(inner);
                    navLink.appendChild(badge);
                }
                const labelEl = badge.querySelector('.fi-badge-label') || badge.querySelector('span') || badge;
                labelEl.textContent = String(count);
                badge.style.display = '';
            } else if (badge) {
                badge.style.display = 'none';
            }
        });
    });

    // Daftarkan Livewire request hook secara idempotent
    let _videotronHookRegistered = false;
    function registerVideotronLivewireHook() {
        if (_videotronHookRegistered || !window.Livewire || typeof window.Livewire.hook !== 'function') return;
        _videotronHookRegistered = true;
        window.Livewire.hook('request', function({ succeed, fail }) {
            if (typeof succeed === 'function') {
                succeed(function() {
                    setTimeout(clearVideotronRowLoading, 400);
                });
            }
            if (typeof fail === 'function') {
                fail(function(payload) {
                    clearVideotronRowLoading();
                    if (payload && typeof payload.preventDefault === 'function') {
                        // Prevent the intrusive Livewire full-screen 500 error modal
                        if (payload.status >= 500) {
                            payload.preventDefault();
                            document.body.style.overflow = '';
                            const errDialog = document.getElementById('livewire-error');
                            if (errDialog) errDialog.remove();
                            console.warn('[E-VITRON] Livewire server response error ' + payload.status + ' gracefully suppressed.');
                        }
                    }
                });
            }
        });
    }

    document.addEventListener('livewire:init', registerVideotronLivewireHook);
    if (window.Livewire) {
        registerVideotronLivewireHook();
    }

    // Inisialisasi Kalender Kustom & Quick Chips pada Modal Kondisi Videotron
    window.initVideotronCalendar = function(el, panel) {
        const wrp = el.closest('.fi-input-wrp');
        const pickerData = window.Alpine ? window.Alpine.$data(el) : null;

        if (pickerData && !pickerData._scrollHooked) {
            pickerData._scrollHooked = true;
            const origSelectDate = pickerData.selectDate.bind(pickerData);
            pickerData.selectDate = function(...args) {
                origSelectDate(...args);
                if (window.closeVideotronCalendar) {
                    window.closeVideotronCalendar(el, () => {
                        const dateInput = wrp ? wrp.querySelector('input') : null;
                        if (dateInput) dateInput.focus();
                    });
                } else {
                    const dateInput = wrp ? wrp.querySelector('input') : null;
                    if (dateInput) dateInput.focus();
                }
            };
        }

        if (wrp && wrp.parentElement && !wrp.parentElement.querySelector('.videotron-quick-chips-bar')) {
            const chipsBar = document.createElement('div');
            chipsBar.className = 'videotron-quick-chips-bar';
            chipsBar.innerHTML = `
                <button type="button" class="videotron-quick-chip" data-offset="0">Hari Ini</button>
                <button type="button" class="videotron-quick-chip" data-offset="1">Besok</button>
                <button type="button" class="videotron-quick-chip" data-offset="3">+3 Hari</button>
                <button type="button" class="videotron-quick-chip" data-offset="7">+1 Minggu</button>
                <button type="button" class="videotron-quick-chip videotron-quick-chip-clear">Kosongkan</button>
            `;
            wrp.parentElement.insertBefore(chipsBar, wrp);

            chipsBar.querySelectorAll('.videotron-quick-chip[data-offset]').forEach(btn => {
                btn.onclick = (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const offset = parseInt(btn.getAttribute('data-offset'), 10);
                    const d = new Date();
                    d.setDate(d.getDate() + offset);
                    if (pickerData) {
                        const base = pickerData.focusedDate ?? (typeof pickerData.getDefaultFocusedDate === 'function' ? pickerData.getDefaultFocusedDate() : null);
                        if (base && typeof base.year === 'function') {
                            const targetDate = base.year(d.getFullYear()).month(d.getMonth()).date(d.getDate());
                            pickerData.focusedDate = targetDate;
                            pickerData.focusedYear = d.getFullYear();
                            pickerData.focusedMonth = d.getMonth();
                            pickerData.setState(targetDate);
                        } else if (typeof pickerData.selectDate === 'function') {
                            pickerData.focusedYear = d.getFullYear();
                            pickerData.focusedMonth = d.getMonth();
                            pickerData.selectDate(d.getDate());
                        }
                        if (window.closeVideotronCalendar) {
                            window.closeVideotronCalendar(el, () => {
                                if (typeof pickerData.isOpen === 'function' && pickerData.isOpen()) {
                                    pickerData.togglePanelVisibility();
                                }
                                const dateInput = wrp ? wrp.querySelector('input') : null;
                                if (dateInput) dateInput.focus();
                            });
                        } else if (typeof pickerData.isOpen === 'function' && pickerData.isOpen()) {
                            pickerData.togglePanelVisibility();
                        }
                        const dateInput = wrp ? wrp.querySelector('input') : null;
                        if (dateInput) dateInput.focus();
                    }
                };
            });

            chipsBar.querySelector('.videotron-quick-chip-clear').onclick = (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (pickerData && typeof pickerData.clearState === 'function') {
                    pickerData.clearState();
                }
                if (window.closeVideotronCalendar) {
                    window.closeVideotronCalendar(el, () => {
                        if (typeof pickerData.isOpen === 'function' && pickerData.isOpen()) {
                            pickerData.togglePanelVisibility();
                        }
                        const dateInput = wrp ? wrp.querySelector('input') : null;
                        if (dateInput) dateInput.focus();
                    });
                } else if (typeof pickerData.isOpen === 'function' && pickerData.isOpen()) {
                    pickerData.togglePanelVisibility();
                }
                const dateInput = wrp ? wrp.querySelector('input') : null;
                if (dateInput) dateInput.focus();
            };
        }

        if (!panel) return;
        const header = panel.querySelector('.flex.items-center.justify-between');
        if (!header || header.querySelector('.videotron-month-nav-btn')) return;

        const select = header.querySelector('select');
        const input = header.querySelector('input');
        if (!select || !input) return;

        select.style.display = 'none';
        input.style.display = 'none';

        const prevBtn = document.createElement('button');
        prevBtn.type = 'button';
        prevBtn.className = 'videotron-month-nav-btn videotron-prev-month-btn';
        prevBtn.setAttribute('aria-label', 'Bulan sebelumnya');
        prevBtn.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7" /></svg>';
        prevBtn.onclick = (e) => {
            e.stopPropagation();
            e.preventDefault();
            const data = window.Alpine ? window.Alpine.$data(panel) : null;
            if (!data) return;
            const m = +data.focusedMonth;
            const y = +data.focusedYear;
            if (m === 0) {
                data.focusedYear = y - 1;
                data.focusedMonth = 11;
            } else {
                data.focusedMonth = m - 1;
            }
            const mc = panel.closest('.fi-modal-content');
            if (mc && mc.scrollTop < 155) {
                mc.scrollTop = 155;
            }
        };

        const titleGroup = document.createElement('div');
        titleGroup.className = 'videotron-calendar-title';
        const titleText = document.createElement('span');
        titleText.className = 'videotron-calendar-title-text';
        titleGroup.appendChild(titleText);

        if (window.Alpine && window.Alpine.effect) {
            window.Alpine.effect(() => {
                const data = window.Alpine.$data(panel);
                if (data && data.months) {
                    titleText.textContent = `${data.months[data.focusedMonth]} ${data.focusedYear}`;
                }
            });
        } else {
            const data = window.Alpine ? window.Alpine.$data(panel) : null;
            if (data && data.months) {
                titleText.textContent = `${data.months[data.focusedMonth]} ${data.focusedYear}`;
            }
        }

        const nextBtn = document.createElement('button');
        nextBtn.type = 'button';
        nextBtn.className = 'videotron-month-nav-btn videotron-next-month-btn';
        nextBtn.setAttribute('aria-label', 'Bulan berikutnya');
        nextBtn.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7" /></svg>';
        nextBtn.onclick = (e) => {
            e.stopPropagation();
            e.preventDefault();
            const data = window.Alpine ? window.Alpine.$data(panel) : null;
            if (!data) return;
            const m = +data.focusedMonth;
            const y = +data.focusedYear;
            if (m === 11) {
                data.focusedYear = y + 1;
                data.focusedMonth = 0;
            } else {
                data.focusedMonth = m + 1;
            }
            const mc = panel.closest('.fi-modal-content');
            if (mc && mc.scrollTop < 155) {
                mc.scrollTop = 155;
            }
        };

        header.prepend(prevBtn);
        header.appendChild(titleGroup);
        header.appendChild(nextBtn);
    };
</script>
