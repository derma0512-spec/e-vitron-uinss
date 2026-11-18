<script>
    function submissionDrawer() {
        return {
            isOpen: false,
            loading: false,
            ticketCode: '',
            activeTicketCode: '',
            contentHtml: '',
            errorMessage: '',
            drawerCache: {},
            showCancelModal: false,
            cancelTicketCode: '',
            cancelTitle: '',
            isCancelling: false,
            cancelErrorMessage: '',
            cancelTriggerEl: null,
            wheelHandler: null,
            keyHandler: null,

            search: @js(request('search') ?? ''),
            currentStatus: @js($currentStatus ?? ''),
            fromNotification: @js($fromNotification ?? false),
            isSearchingGrid: false,
            isClearing: false,
            clearStartTime: 0,
            debounceTimer: null,
            searchAbortController: null,

            cleanNotificationParams() {
                const url = new URL(window.location.href);
                url.searchParams.delete('open');
                url.searchParams.delete('from');
                const cleanQuery = url.searchParams.toString();
                const targetUrl = url.pathname + (cleanQuery ? '?' + cleanQuery : '');
                if (window.history && window.history.replaceState) {
                    window.history.replaceState(null, '', targetUrl);
                }
            },

            handleSearchInput(value) {
                this.search = value;
                clearTimeout(this.debounceTimer);
                this.debounceTimer = setTimeout(() => {
                    this.performGridSearch(this.search);
                }, 300);
            },

            async performGridSearch(term, onComplete = null) {
                if (this.searchAbortController) {
                    this.searchAbortController.abort();
                }
                this.searchAbortController = new AbortController();

                this.isSearchingGrid = true;
                const url = new URL('{{ route('pengajuan.index') }}', window.location.origin);
                if (this.currentStatus) {
                    url.searchParams.set('status', this.currentStatus);
                }
                if (term && term.trim().length > 0) {
                    url.searchParams.set('search', term.trim());
                }
                const perPage = '{{ request('per_page', 9) }}';
                if (perPage && perPage !== '9') {
                    url.searchParams.set('per_page', perPage);
                }

                try {
                    const response = await fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        },
                        signal: this.searchAbortController.signal
                    });

                    if (response.ok) {
                        const html = await response.text();
                        if (this.$refs.submissionsContainer) {
                            this.$refs.submissionsContainer.innerHTML = html;
                            this.$nextTick(() => {
                                if (window.Alpine) {
                                    window.Alpine.initTree(this.$refs.submissionsContainer);
                                }
                            });
                        }
                        if (window.history && window.history.replaceState) {
                            window.history.replaceState(null, '', url.toString());
                        }
                    }
                } catch (err) {
                    if (err.name !== 'AbortError') {
                        console.error('Search error:', err);
                    }
                } finally {
                    this.isSearchingGrid = false;
                    if (typeof onComplete === 'function') {
                        onComplete();
                    }
                }
            },

            clearSearch() {
                if (!this.search && !(this.$refs.searchInput && this.$refs.searchInput.value)) {
                    return;
                }
                clearTimeout(this.debounceTimer);
                this.clearStartTime = Date.now();
                this.isClearing = true;
                this.search = '';
                if (this.$refs.searchInput) {
                    this.$refs.searchInput.value = '';
                    this.$refs.searchInput.focus();
                }
                this.performGridSearch('', () => {
                    const elapsed = Date.now() - (this.clearStartTime || 0);
                    const delay = Math.max(0, 300 - elapsed);
                    setTimeout(() => {
                        this.isClearing = false;
                    }, delay);
                });
            },

            selectFilter(statusKey, el) {
                this.currentStatus = statusKey || '';
                this.scrollTabIntoCenter(el);
                this.performGridSearch(this.search);
            },

            scrollTabIntoCenter(el) {
                const container = this.$refs.filterTabsContainer;
                if (!container || !el) return;

                // Hitung posisi tengah persis di dalam kontainer tab
                // Tanpa menggeser atau menyentuh window/page viewport sama sekali
                const containerWidth = container.clientWidth;
                const tabOffsetLeft = el.offsetLeft;
                const tabWidth = el.offsetWidth;
                const targetScrollLeft = tabOffsetLeft - (containerWidth / 2) + (tabWidth / 2);

                container.scrollTo({
                    left: Math.max(0, targetScrollLeft),
                    behavior: 'smooth'
                });
            },

            init() {
                // Auto-scroll tab status aktif ke tengah layar secara silky smooth & terisolasi
                this.$nextTick(() => {
                    const activeTab = document.querySelector('.active-filter-tab');
                    if (activeTab && this.$refs.filterTabsContainer) {
                        this.scrollTabIntoCenter(activeTab);
                    }
                });

                window.addEventListener('popstate', (e) => {
                    if (this.isOpen) {
                        this.closeDrawer();
                    } else {
                        const urlParams = new URLSearchParams(window.location.search);
                        const s = urlParams.get('search') || '';
                        const st = urlParams.get('status') || '';
                        if (s !== this.search || st !== this.currentStatus) {
                            this.search = s;
                            this.currentStatus = st;
                            this.performGridSearch(s);
                            this.$nextTick(() => {
                                const activeTab = document.querySelector('.active-filter-tab');
                                if (activeTab) {
                                    this.scrollTabIntoCenter(activeTab);
                                }
                            });
                        }
                    }
                });

                // Cek apakah ada query param ?open=VTR-...
                const urlParams = new URLSearchParams(window.location.search);
                const openCode = urlParams.get('open');
                if (openCode) {
                    this.activeTicketCode = openCode;

                    // Auto-scroll ke kartu aktif agar langsung berada di depan mata user
                    const cardEl = document.getElementById('submission-card-' + openCode);
                    if (cardEl) {
                        cardEl.scrollIntoView({ behavior: 'auto', block: 'center' });
                    }

                    this.openDrawer(openCode);

                    // Segera bersihkan parameter ?open dan ?from dari address bar agar saat user me-reload halaman,
                    // browser tetap di halaman daftar pengajuan normal dan tidak otomatis membuka detail lagi
                    urlParams.delete('open');
                    urlParams.delete('from');
                    const cleanQuery = urlParams.toString();
                    const cleanUrl = window.location.pathname + (cleanQuery ? '?' + cleanQuery : '');
                    if (window.history && window.history.replaceState) {
                        window.history.replaceState(null, '', cleanUrl);
                    }
                }
            },

            applyLock() {
                const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
                if (scrollbarWidth > 0) {
                    document.documentElement.style.setProperty('--sipetron-scrollbar-width', scrollbarWidth + 'px');
                } else {
                    document.documentElement.style.setProperty('--sipetron-scrollbar-width', '0px');
                }
                document.documentElement.classList.add('sipetron-drawer-locked');
                document.body.classList.add('sipetron-drawer-locked');

                // Pasang wheel & touchmove interceptor di window (passive: false agar bisa e.preventDefault())
                this.wheelHandler = (e) => {
                    if (!this.isOpen) return;
                    if (!e.target.closest('.sipetron-drawer-body') && !e.target.closest('.sipetron-cancel-dialog')) {
                        e.preventDefault();
                    }
                };
                window.addEventListener('wheel', this.wheelHandler, { passive: false });
                window.addEventListener('touchmove', this.wheelHandler, { passive: false });

                // Pasang keyboard scroll interceptor
                this.keyHandler = (e) => {
                    if (!this.isOpen) return;
                    if (['ArrowUp', 'ArrowDown', 'PageUp', 'PageDown', 'Home', 'End', ' '].includes(e.key)) {
                        if (!e.target.closest('.sipetron-drawer-body') && !e.target.closest('.sipetron-cancel-dialog') && !['INPUT', 'TEXTAREA'].includes(e.target.tagName)) {
                            e.preventDefault();
                        }
                    }
                };
                window.addEventListener('keydown', this.keyHandler);
            },

            removeLock() {
                document.documentElement.classList.remove('sipetron-drawer-locked');
                document.body.classList.remove('sipetron-drawer-locked');
                document.documentElement.style.removeProperty('--sipetron-scrollbar-width');
                if (this.wheelHandler) {
                    window.removeEventListener('wheel', this.wheelHandler);
                    window.removeEventListener('touchmove', this.wheelHandler);
                    this.wheelHandler = null;
                }
                if (this.keyHandler) {
                    window.removeEventListener('keydown', this.keyHandler);
                    this.keyHandler = null;
                }
            },

            async openDrawer(code, force = false) {
                if (document.activeElement && typeof document.activeElement.blur === 'function') {
                    document.activeElement.blur();
                }
                this.isOpen = true;
                this.ticketCode = code;
                this.activeTicketCode = code;
                this.applyLock();

                // Bersihkan query param ?open dari address bar agar reload tetap di daftar pengajuan
                if (window.history && window.history.replaceState) {
                    const cleanUrl = new URL(window.location.href);
                    cleanUrl.searchParams.delete('open');
                    window.history.replaceState({ drawerCode: code }, '', cleanUrl.pathname + (cleanUrl.search ? cleanUrl.search : ''));
                }

                if (!force && this.drawerCache[code]) {
                    this.contentHtml = this.drawerCache[code];
                    this.loading = false;
                    this.$nextTick(() => {
                        if (window.Alpine && this.$refs.drawerContent) {
                            window.Alpine.initTree(this.$refs.drawerContent);
                        }
                    });
                    return;
                }

                this.loading = true;
                this.errorMessage = '';
                this.contentHtml = '';

                try {
                    const response = await fetch('{{ url('/pengajuan') }}/' + encodeURIComponent(code) + '?drawer=1', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Gagal memuat data (HTTP ' + response.status + ')');
                    }

                    const html = await response.text();
                    this.drawerCache[code] = html;
                    this.contentHtml = html;

                    this.$nextTick(() => {
                        if (window.Alpine && this.$refs.drawerContent) {
                            window.Alpine.initTree(this.$refs.drawerContent);
                        }
                    });
                } catch (err) {
                    this.errorMessage = 'Terjadi kesalahan saat memuat detail pengajuan.';
                } finally {
                    this.loading = false;
                }
            },

            closeDrawer() {
                if (!this.isOpen) return;
                this.isOpen = false;
                this.activeTicketCode = '';
                this.removeLock();
                if (document.activeElement && typeof document.activeElement.blur === 'function') {
                    document.activeElement.blur();
                }

                // Bersihkan URL dari parameter ?open dan ?from
                this.cleanNotificationParams();
            },

            confirmCancel(code, title, triggerEl = null) {
                this.cancelTicketCode = code;
                this.cancelTitle = title;
                this.cancelErrorMessage = '';
                this.isCancelling = false;
                this.cancelTriggerEl = triggerEl || document.activeElement;
                this.showCancelModal = true;
                this.$nextTick(() => {
                    if (this.$refs.cancelDismissBtn) {
                        this.$refs.cancelDismissBtn.focus();
                    }
                });
            },

            closeCancelModal() {
                if (this.isCancelling) return;
                this.showCancelModal = false;
                this.cancelErrorMessage = '';
                this.$nextTick(() => {
                    if (this.cancelTriggerEl && typeof this.cancelTriggerEl.focus === 'function') {
                        this.cancelTriggerEl.focus();
                    }
                });
            },

            async executeCancel() {
                if (this.isCancelling || !this.cancelTicketCode) return;
                this.isCancelling = true;
                this.cancelErrorMessage = '';

                try {
                    const response = await fetch('{{ url('/pengajuan') }}/' + encodeURIComponent(this.cancelTicketCode) + '/batal', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    const data = await response.json().catch(() => ({}));

                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Gagal membatalkan pengajuan. Silakan coba kembali.');
                    }

                    // Berhasil: tutup modal dan reload untuk memunculkan flash message & status terupdate
                    this.showCancelModal = false;
                    window.location.reload();
                } catch (err) {
                    this.cancelErrorMessage = err.message || 'Terjadi kesalahan sistem saat membatalkan pengajuan.';
                    this.isCancelling = false;
                }
            },

            trapFocus(e) {
                if (!this.showCancelModal) return;
                const modal = this.$refs.cancelModalDialog;
                if (!modal) return;
                const focusable = modal.querySelectorAll('button:not([disabled]), [tabindex]:not([tabindex="-1"])');
                if (!focusable.length) return;
                const first = focusable[0];
                const last = focusable[focusable.length - 1];

                if (e.shiftKey && document.activeElement === first) {
                    e.preventDefault();
                    last.focus();
                } else if (!e.shiftKey && document.activeElement === last) {
                    e.preventDefault();
                    first.focus();
                }
            }
        };
    }
</script>
