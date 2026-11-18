{{-- =========================================================================
     ALPINE.JS LOGIC & STATE (Menjaga Input & Validasi Ringan)
     ========================================================================= --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('submissionWizard', () => {
            // Baca draf secara sinkron SEBELUM Alpine me-mount DOM agar transisi reload langsung ke langkah aktif tanpa flicker
            let draftData = null;
            let startStep = {{ session('submission_success') ? 5 : 1 }};

            @if (! session('submission_success') && ! $errors->any())
                try {
                    sessionStorage.removeItem('sipetron_wizard_draft');
                    const draftKey = 'sipetron_wizard_draft_{{ Auth::id() }}';
                    const raw = sessionStorage.getItem(draftKey);
                    if (raw) {
                        const parsed = JSON.parse(raw);
                        if (parsed && typeof parsed === 'object') {
                            draftData = parsed;
                            if (parsed.currentStep && parsed.currentStep > 1 && parsed.currentStep < 5) {
                                startStep = parsed.currentStep;
                            }
                        }
                    }
                } catch (err) { console.warn('[E-VITRON] Initial state sync skipped:', err); }
            @endif

            // Helper sinkronisasi tahun/bulan kalender
            let initStartYear = new Date().getFullYear();
            let initStartMonth = new Date().getMonth();
            let initEndYear = new Date().getFullYear();
            let initEndMonth = new Date().getMonth();

            if (draftData && draftData.startDate) {
                const sParts = draftData.startDate.split('-');
                if (sParts.length === 3) {
                    initStartYear = parseInt(sParts[0], 10);
                    initStartMonth = parseInt(sParts[1], 10) - 1;
                }
            }
            if (draftData && draftData.endDate) {
                const eParts = draftData.endDate.split('-');
                if (eParts.length === 3) {
                    initEndYear = parseInt(eParts[0], 10);
                    initEndMonth = parseInt(eParts[1], 10) - 1;
                }
            }

            return {
                currentStep: startStep,
                isSubmitting: false,
                showNoticeModal: false,
                dontShowAgainToday: false,
                isConfirmingNotice: false,
                steps: [
                    { id: 1, label: 'Pilih Videotron' },
                    { id: 2, label: 'Tentukan Tanggal' },
                    { id: 3, label: 'Informasi Konten' },
                    { id: 4, label: 'Periksa Pengajuan' },
                    { id: 5, label: 'Konfirmasi' }
                ],

                // Step 1: Videotron Selection (Langsung pulihkan draf jika ada)
                selectedVideotrons: (draftData && Array.isArray(draftData.selectedVideotrons))
                    ? draftData.selectedVideotrons
                    : @json(old('videotrons', request('videotron_id') ? [(string) request('videotron_id')] : [])),
                videotronsCatalog: @json($videotrons),
                step1Error: '',

                // Step 2: Date Range (Langsung pulihkan draf jika ada)
                startDate: (draftData && draftData.startDate) ? draftData.startDate : '{{ old('start_date', '') }}',
                endDate: (draftData && draftData.endDate) ? draftData.endDate : '{{ old('end_date', '') }}',
                startDateError: '',
                endDateError: '',

                // Custom Calendar State
                startDisplay: (draftData && draftData.startDisplay) ? draftData.startDisplay : '',
                endDisplay: (draftData && draftData.endDisplay) ? draftData.endDisplay : '',
                showStartCalendar: false,
                showEndCalendar: false,
                startCalendarYear: initStartYear,
                startCalendarMonth: initStartMonth,
                endCalendarYear: initEndYear,
                endCalendarMonth: initEndMonth,
                todayDisplayIndo: '{{ now('Asia/Jakarta')->locale('id')->isoFormat('D MMM') }}',
                minDateDisplayIndo: '{{ now('Asia/Jakarta')->addDays((int) setting('min_days_before', 3))->locale('id')->isoFormat('D MMM') }}',
                minDateH3: '{{ now('Asia/Jakarta')->addDays((int) setting('min_days_before', 3))->format('Y-m-d') }}',
                monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                shortMonthNames: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],

                // Step 3: Content Info (Langsung pulihkan draf jika ada)
                form: {
                    title: (draftData && draftData.form && draftData.form.title !== undefined) ? draftData.form.title : @json(old('title', '')),
                    contentType: (draftData && draftData.form && draftData.form.contentType !== undefined) ? draftData.form.contentType : @json(old('content_type', 'video')),
                    durationUnit: (draftData && draftData.form && draftData.form.durationUnit !== undefined) ? draftData.form.durationUnit : 'menit',
                    durationValue: (draftData && draftData.form && draftData.form.durationValue !== undefined) ? draftData.form.durationValue : {{ old('video_duration_seconds') ? round((int) old('video_duration_seconds') / 60, 2) : 3 }},
                    videoDurationSeconds: (draftData && draftData.form && draftData.form.videoDurationSeconds !== undefined) ? draftData.form.videoDurationSeconds : {{ old('video_duration_seconds', 180) }},
                    gdriveLink: (draftData && draftData.form && draftData.form.gdriveLink !== undefined) ? draftData.form.gdriveLink : @json(old('gdrive_link', '')),
                    picName: (draftData && draftData.form && draftData.form.picName !== undefined) ? draftData.form.picName : @json(old('pic_name', Auth::user()->name)),
                    whatsapp: (draftData && draftData.form && draftData.form.whatsapp !== undefined) ? draftData.form.whatsapp : @json(old('whatsapp', Auth::user()->phone ?? '')),
                    organization: (draftData && draftData.form && draftData.form.organization !== undefined) ? draftData.form.organization : @json(old('organization', Auth::user()->unit_or_prodi ?? '')),
                    description: (draftData && draftData.form && draftData.form.description !== undefined) ? draftData.form.description : @json(old('description', ''))
                },
                formErrors: {},
                suratFileName: (draftData && draftData.suratFileName) ? draftData.suratFileName : '',
                suratFileSize: (draftData && draftData.suratFileSize) ? draftData.suratFileSize : '',
                isDraggingSurat: false,

            toggleBodyScroll(lock) {
                if (lock) {
                    const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
                    if (scrollbarWidth > 0) {
                        document.documentElement.style.setProperty('--sipetron-scrollbar-width', scrollbarWidth + 'px');
                    } else {
                        document.documentElement.style.setProperty('--sipetron-scrollbar-width', '0px');
                    }
                    document.documentElement.classList.add('sipetron-drawer-locked');
                    document.body.classList.add('overflow-hidden');
                } else {
                    document.documentElement.classList.remove('sipetron-drawer-locked');
                    document.body.classList.remove('overflow-hidden');
                    document.documentElement.style.removeProperty('--sipetron-scrollbar-width');
                }
            },

            onModalWheel(e) {
                const scrollable = e.target.closest('.overflow-y-auto');
                if (scrollable && scrollable.scrollHeight > scrollable.clientHeight) {
                    const atTop = scrollable.scrollTop <= 0 && e.deltaY < 0;
                    const atBottom = scrollable.scrollTop + scrollable.clientHeight >= scrollable.scrollHeight - 1 && e.deltaY > 0;
                    if (atTop || atBottom) {
                        e.preventDefault();
                    }
                } else {
                    e.preventDefault();
                }
            },

            onModalTouchMove(e) {
                const scrollable = e.target.closest('.overflow-y-auto');
                if (!scrollable || scrollable.scrollHeight <= scrollable.clientHeight) {
                    e.preventDefault();
                }
            },

            init() {
                // Kunci scroll halaman belakang saat modal aktif (Mobile & Web)
                this.$watch('showNoticeModal', (val) => {
                    this.toggleBodyScroll(val);
                });

                // Bersihkan style anti-flicker preloader setelah Alpine selesai mount
                try {
                    const pre = document.getElementById('sipetron-step-preloader');
                    if (pre) pre.remove();
                } catch (err) { console.warn('[E-VITRON] Initial state sync skipped:', err); }

                // Inisialisasi tampilan input tanggal jika sudah ada nilai awal
                if (this.startDate) {
                    this.startDisplay = this.formatDisplayShort(this.startDate);
                    const parts = this.startDate.split('-');
                    if (parts.length === 3) {
                        this.startCalendarYear = parseInt(parts[0], 10);
                        this.startCalendarMonth = parseInt(parts[1], 10) - 1;
                    }
                }
                if (this.endDate) {
                    this.endDisplay = this.formatDisplayShort(this.endDate);
                    const parts = this.endDate.split('-');
                    if (parts.length === 3) {
                        this.endCalendarYear = parseInt(parts[0], 10);
                        this.endCalendarMonth = parseInt(parts[1], 10) - 1;
                    }
                }

                // Pulihkan draf sebelumnya dari sessionStorage saat refresh
                @if (! session('submission_success'))
                    this.restoreDraft();
                @else
                    this.clearDraft();
                @endif

                // Sinkronkan penanggalan hari ini & batas minimal H-3 selalu akurat waktu WIB (Asia/Jakarta)
                try {
                    const nowWibStr = new Intl.DateTimeFormat('en-CA', { timeZone: 'Asia/Jakarta' }).format(new Date());
                    const nowWibParts = nowWibStr.split('-');
                    if (nowWibParts.length === 3) {
                        const todayNum = parseInt(nowWibParts[2], 10);
                        const todayMonthIdx = parseInt(nowWibParts[1], 10) - 1;
                        this.todayDisplayIndo = `${todayNum} ${this.shortMonthNames[todayMonthIdx] || ''}`;

                        const minDays = {{ (int) setting('min_days_before', 3) }};
                        const dMin = new Date();
                        dMin.setDate(dMin.getDate() + minDays);
                        const dMinWibStr = new Intl.DateTimeFormat('en-CA', { timeZone: 'Asia/Jakarta' }).format(dMin);
                        this.minDateH3 = dMinWibStr;
                        const minParts = dMinWibStr.split('-');
                        if (minParts.length === 3) {
                            const minNum = parseInt(minParts[2], 10);
                            const minMonthIdx = parseInt(minParts[1], 10) - 1;
                            this.minDateDisplayIndo = `${minNum} ${this.shortMonthNames[minMonthIdx] || ''}`;
                        }
                    }
                } catch (err) { console.warn('[E-VITRON] Initial state sync skipped:', err); }

                // Auto-save draf secara berkala saat ada perubahan input
                this.$watch('currentStep', () => this.saveDraft());
                this.$watch('selectedVideotrons', () => this.saveDraft());
                this.$watch('startDate', (val) => {
                    if (val && !this.startDisplay) {
                        this.startDisplay = this.formatDisplayShort(val);
                    }
                    this.saveDraft();
                });
                this.$watch('endDate', (val) => {
                    if (val && !this.endDisplay) {
                        this.endDisplay = this.formatDisplayShort(val);
                    }
                    this.saveDraft();
                });
                this.$watch('startDisplay', () => this.saveDraft());
                this.$watch('endDisplay', () => this.saveDraft());
                this.$watch('form', () => this.saveDraft(), { deep: true });

                // Auto scroll halus ke kalender saat dibuka agar selalu nyaman terlihat di viewport
                this.$watch('showStartCalendar', (isOpen) => {
                    if (isOpen) {
                        this.scrollToCalendar('start');
                    }
                });

                this.$watch('showEndCalendar', (isOpen) => {
                    if (isOpen) {
                        this.scrollToCalendar('end');
                    }
                });

                // Lock / Unlock scroll halaman latar belakang secara reaktif saat overlay aktif
                this.$watch('showNoticeModal', (isOpen) => {
                    this.toggleBodyScroll(isOpen);
                });

                // Konfirmasi pencegahan reload tak sengaja HANYA jika formulir masih aktif diisi (Langkah 1 - 4)
                window.addEventListener('beforeunload', (e) => {
                    this.toggleBodyScroll(false);
                    // Jangan pernah blokir jika pengajuan sudah berhasil dikirim (Langkah 5) atau sedang submit
                    if (this.currentStep >= 5 || {{ session('submission_success') ? 'true' : 'false' }} || this.isSubmitting) {
                        return;
                    }
                    if (this.currentStep > 1 || (this.form.title && this.form.title.trim().length > 0)) {
                        e.preventDefault();
                        e.returnValue = '';
                        return '';
                    }
                });

                // Deteksi status koneksi jaringan untuk menjaga keamanan draf pengajuan
                window.addEventListener('offline', () => {
                    if (typeof window.sipetronToast === 'function') {
                        window.sipetronToast({
                            type: 'error',
                            title: 'Koneksi Internet Terputus',
                            message: 'Draf pengajuan Anda tetap tersimpan otomatis di perangkat ini.'
                        });
                    }
                });

                window.addEventListener('online', () => {
                    if (typeof window.sipetronToast === 'function') {
                        window.sipetronToast({
                            type: 'success',
                            title: 'Koneksi Internet Kembali Aktif',
                            message: 'Anda dapat melanjutkan proses pengajuan penayangan.'
                        });
                    }
                });

                // Tampilkan popup panduan HANYA jika berada di Langkah 1 & belum ditutup hari ini
                @if (! session('submission_success') && ! $errors->any())
                    try {
                        const dismissedDate = localStorage.getItem('sipetron_dismiss_notice_date');
                        const todayWib = new Intl.DateTimeFormat('en-CA', { timeZone: 'Asia/Jakarta' }).format(new Date());
                        const isDismissedToday = (dismissedDate === todayWib);
                        this.dontShowAgainToday = isDismissedToday;
                        if (!isDismissedToday && this.currentStep === 1) {
                            this.showNoticeModal = true;
                        }
                    } catch (e) {
                        if (this.currentStep === 1) {
                            this.showNoticeModal = true;
                        }
                    }
                @endif

                if (this.showNoticeModal) {
                    this.toggleBodyScroll(true);
                }

                @if ($errors->any())
                    @if ($errors->has('videotrons'))
                        this.currentStep = 1;
                    @elseif ($errors->has('start_date') || $errors->has('end_date'))
                        this.currentStep = 2;
                    @else
                        this.currentStep = 3;
                        @if ($errors->has('surat_peminjaman'))
                            this.formErrors.surat_peminjaman = '{{ $errors->first('surat_peminjaman') }}';
                        @endif
                    @endif
                @endif
            },

            closeNoticeModal() {
                if (this.isConfirmingNotice) return;
                if (document.activeElement && typeof document.activeElement.blur === 'function') {
                    document.activeElement.blur();
                }
                this.showNoticeModal = false;
                this.toggleBodyScroll(false);
            },

            confirmNoticeModal() {
                if (this.isConfirmingNotice) return;
                this.isConfirmingNotice = true;
                if (document.activeElement && typeof document.activeElement.blur === 'function') {
                    document.activeElement.blur();
                }

                if (this.dontShowAgainToday) {
                    try {
                        const todayWib = new Intl.DateTimeFormat('en-CA', { timeZone: 'Asia/Jakarta' }).format(new Date());
                        localStorage.setItem('sipetron_dismiss_notice_date', todayWib);
                    } catch (e) {
                        console.warn('localStorage not available:', e);
                    }
                } else {
                    try {
                        localStorage.removeItem('sipetron_dismiss_notice_date');
                    } catch (e) {
                        console.warn('localStorage not available:', e);
                    }
                }

                // Micro-loading feedback ~350ms ala panel Admin sebelum modal tertutup
                setTimeout(() => {
                    this.showNoticeModal = false;
                    this.toggleBodyScroll(false);
                    this.isConfirmingNotice = false;
                    if (document.activeElement && typeof document.activeElement.blur === 'function') {
                        document.activeElement.blur();
                    }
                }, 350);
            },

            isVideotronSelected(id) {
                return this.selectedVideotrons.includes(String(id)) || this.selectedVideotrons.includes(Number(id));
            },

            get selectedVideotronDetails() {
                return this.videotronsCatalog.filter(v => this.isVideotronSelected(v.id));
            },

            get selectedOrientationSummary() {
                if (!this.selectedVideotronDetails || !this.selectedVideotronDetails.length) {
                    return 'Format Sesuai Layar';
                }
                const orientations = [...new Set(this.selectedVideotronDetails.map(v => (v.orientation || 'landscape').toLowerCase()))];
                if (orientations.length === 1) {
                    return orientations[0] === 'portrait' ? 'Format Portrait (Tegak)' : 'Format Landscape (Mendatar)';
                }
                return 'Format Sesuai Tiap Layar';
            },

            get formattedDurationSummary() {
                const sec = parseInt(this.form.videoDurationSeconds, 10) || 0;
                if (sec <= 0) return '-';
                if (sec % 60 === 0) {
                    return `${sec / 60} Menit`;
                } else if (sec < 60) {
                    return `${sec} Detik`;
                } else {
                    const min = Math.floor(sec / 60);
                    const remSec = sec % 60;
                    return `${min} Menit ${remSec} Detik`;
                }
            },

            get calculatedDurationDays() {
                if (!this.startDate || !this.endDate) return 0;
                const start = new Date(this.startDate);
                const end = new Date(this.endDate);
                if (end < start) return 0;
                const diffTime = Math.abs(end - start);
                return Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            },

            getMonthTitle(year, month) {
                return `${this.monthNames[month]} ${year}`;
            },

            formatDisplayShort(dateStr) {
                if (!dateStr) return '';
                try {
                    const parts = dateStr.split('-');
                    if (parts.length === 3) {
                        const day = parseInt(parts[2], 10);
                        const monthIdx = parseInt(parts[1], 10) - 1;
                        const year = parts[0];
                        return `${day} ${this.shortMonthNames[monthIdx] || ''} ${year}`;
                    }
                } catch (err) { console.warn('[E-VITRON] Initial state sync skipped:', err); }
                return dateStr;
            },

            getCalendarDays(year, month, minDateStr, maxDateStr) {
                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);
                const daysInMonth = lastDay.getDate();
                const paddingDays = (firstDay.getDay() + 6) % 7; // Senin = 0

                const days = [];
                for (let i = 0; i < paddingDays; i++) {
                    days.push({ day: null, dateStr: '', isDisabled: true, isToday: false });
                }

                const todayStr = new Intl.DateTimeFormat('en-CA', { timeZone: 'Asia/Jakarta' }).format(new Date());

                for (let d = 1; d <= daysInMonth; d++) {
                    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                    let isDisabled = false;
                    if (minDateStr && dateStr < minDateStr) {
                        isDisabled = true;
                    }
                    if (maxDateStr && dateStr > maxDateStr) {
                        isDisabled = true;
                    }
                    const isToday = (dateStr === todayStr);

                    days.push({
                        day: d,
                        dateStr: dateStr,
                        isDisabled: isDisabled,
                        isToday: isToday
                    });
                }
                return days;
            },

            prevMonth(type) {
                if (type === 'start') {
                    if (this.startCalendarMonth === 0) {
                        this.startCalendarMonth = 11;
                        this.startCalendarYear--;
                    } else {
                        this.startCalendarMonth--;
                    }
                } else {
                    if (this.endCalendarMonth === 0) {
                        this.endCalendarMonth = 11;
                        this.endCalendarYear--;
                    } else {
                        this.endCalendarMonth--;
                    }
                }
            },

            nextMonth(type) {
                if (type === 'start') {
                    if (this.startCalendarMonth === 11) {
                        this.startCalendarMonth = 0;
                        this.startCalendarYear++;
                    } else {
                        this.startCalendarMonth++;
                    }
                } else {
                    if (this.endCalendarMonth === 11) {
                        this.endCalendarMonth = 0;
                        this.endCalendarYear++;
                    } else {
                        this.endCalendarMonth++;
                    }
                }
            },

            selectDate(type, dateStr) {
                if (type === 'start') {
                    this.startDate = dateStr;
                    this.startDisplay = this.formatDisplayShort(dateStr);
                    this.showStartCalendar = false;
                    this.validateDates();

                    // Jika tanggal selesai kosong atau lebih awal dari tanggal mulai, arahkan
                    if (!this.endDate || this.endDate < dateStr) {
                        this.endDate = dateStr;
                        this.endDisplay = this.formatDisplayShort(dateStr);
                        const parts = dateStr.split('-');
                        this.endCalendarYear = parseInt(parts[0], 10);
                        this.endCalendarMonth = parseInt(parts[1], 10) - 1;
                        this.showEndCalendar = true;
                    }
                } else {
                    this.endDate = dateStr;
                    this.endDisplay = this.formatDisplayShort(dateStr);
                    this.showEndCalendar = false;
                    this.validateDates();
                }
            },

            handleDateInput(type, rawVal) {
                const parsed = this.parseTypedDate(rawVal);
                if (parsed) {
                    if (type === 'start') {
                        this.startDate = parsed;
                        const parts = parsed.split('-');
                        this.startCalendarYear = parseInt(parts[0], 10);
                        this.startCalendarMonth = parseInt(parts[1], 10) - 1;
                        this.validateDates();
                    } else {
                        this.endDate = parsed;
                        const parts = parsed.split('-');
                        this.endCalendarYear = parseInt(parts[0], 10);
                        this.endCalendarMonth = parseInt(parts[1], 10) - 1;
                        this.validateDates();
                    }
                }
            },

            handleDateBlur(type) {
                if (type === 'start') {
                    if (this.startDate) {
                        this.startDisplay = this.formatDisplayShort(this.startDate);
                    }
                } else {
                    if (this.endDate) {
                        this.endDisplay = this.formatDisplayShort(this.endDate);
                    }
                }
                this.validateDates();
            },

            parseTypedDate(input) {
                if (!input || typeof input !== 'string') return '';
                const trimmed = input.trim();
                const ddmmyyyy = trimmed.match(/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/);
                if (ddmmyyyy) {
                    const d = ddmmyyyy[1].padStart(2, '0');
                    const m = ddmmyyyy[2].padStart(2, '0');
                    const y = ddmmyyyy[3];
                    return y + '-' + m + '-' + d;
                }
                const yyyymmdd = trimmed.match(/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/);
                if (yyyymmdd) {
                    const y = yyyymmdd[1];
                    const m = yyyymmdd[2].padStart(2, '0');
                    const d = yyyymmdd[3].padStart(2, '0');
                    return y + '-' + m + '-' + d;
                }
                const textDate = trimmed.match(/^(\d{1,2})\s+([a-zA-Z]+)\s+(\d{4})$/);
                if (textDate) {
                    const d = textDate[1].padStart(2, '0');
                    const monthRaw = textDate[2].toLowerCase();
                    const y = textDate[3];
                    const monthMap = {
                        'jan': '01', 'januari': '01', 'january': '01',
                        'feb': '02', 'februari': '02', 'february': '02',
                        'mar': '03', 'maret': '03', 'march': '03',
                        'apr': '04', 'april': '04',
                        'mei': '05', 'may': '05',
                        'jun': '06', 'juni': '06', 'june': '06',
                        'jul': '07', 'juli': '07', 'july': '07',
                        'agu': '08', 'agustus': '08', 'agt': '08', 'aug': '08', 'august': '08',
                        'sep': '09', 'september': '09',
                        'okt': '10', 'oktober': '10', 'oct': '10', 'october': '10',
                        'nov': '11', 'november': '11',
                        'des': '12', 'desember': '12', 'dec': '12', 'december': '12'
                    };
                    if (monthMap[monthRaw]) {
                        return y + '-' + monthMap[monthRaw] + '-' + d;
                    }
                }
                return '';
            },

            ...window.SipetronWizardMethods
        };
    });
});
</script>