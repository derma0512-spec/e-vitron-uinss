{{-- Bagian 1: Method Helper Kalender, Penyimpanan Draf & Validasi Form Wizard (Dipanggil oleh pengajuan/wizard.blade.php) --}}
<script>
    window.SipetronWizardMethods = {
            setQuickDate(offsetStart, durationDays) {
                if (document.activeElement && document.activeElement.blur) {
                    document.activeElement.blur();
                }
                const dStart = new Date();
                dStart.setDate(dStart.getDate() + offsetStart);
                const startStr = `${dStart.getFullYear()}-${String(dStart.getMonth() + 1).padStart(2, '0')}-${String(dStart.getDate()).padStart(2, '0')}`;
                
                const dEnd = new Date(dStart);
                dEnd.setDate(dEnd.getDate() + (durationDays - 1));
                const endStr = `${dEnd.getFullYear()}-${String(dEnd.getMonth() + 1).padStart(2, '0')}-${String(dEnd.getDate()).padStart(2, '0')}`;

                this.startDate = startStr;
                this.startDisplay = this.formatDisplayShort(startStr);
                this.startCalendarYear = dStart.getFullYear();
                this.startCalendarMonth = dStart.getMonth();

                this.endDate = endStr;
                this.endDisplay = this.formatDisplayShort(endStr);
                this.endCalendarYear = dEnd.getFullYear();
                this.endCalendarMonth = dEnd.getMonth();

                this.showStartCalendar = false;
                this.showEndCalendar = false;
                this.validateDates();
            },

            scrollToCalendar(type) {
                this.$nextTick(() => {
                    const el = (type === 'start') ? this.$refs.startCalendarPopover : this.$refs.endCalendarPopover;
                    if (!el) return;
                    const rect = el.getBoundingClientRect();
                    const windowHeight = window.innerHeight || document.documentElement.clientHeight;
                    const bottomBuffer = 45; // Jarak penyangga nyaman dari batas bawah layar

                    if (rect.bottom + bottomBuffer > windowHeight) {
                        const scrollOffset = (rect.bottom + bottomBuffer) - windowHeight;
                        window.scrollBy({ top: scrollOffset, behavior: 'smooth' });
                    }
                });
            },

            clearDates() {
                this.startDate = '';
                this.startDisplay = '';
                this.endDate = '';
                this.endDisplay = '';
                this.showStartCalendar = false;
                this.showEndCalendar = false;
                this.startDateError = '';
                this.endDateError = '';
            },

            openSipetronDB() {
                return new Promise((resolve, reject) => {
                    if (!window.indexedDB) {
                        return reject(new Error('IndexedDB not supported'));
                    }
                    const req = window.indexedDB.open('sipetron_wizard_db', 1);
                    req.onupgradeneeded = (e) => {
                        const db = e.target.result;
                        if (!db.objectStoreNames.contains('draft_files')) {
                            db.createObjectStore('draft_files', { keyPath: 'id' });
                        }
                    };
                    req.onsuccess = () => resolve(req.result);
                    req.onerror = () => reject(req.error);
                });
            },

            async storeSuratInIndexedDB(file) {
                try {
                    const db = await this.openSipetronDB();
                    const tx = db.transaction('draft_files', 'readwrite');
                    tx.objectStore('draft_files').put({
                        id: 'surat_permohonan',
                        name: file.name,
                        type: file.type || 'application/pdf',
                        lastModified: file.lastModified,
                        blob: file
                    });
                } catch (e) {
                    console.warn('Failed to save file to IndexedDB:', e);
                }
            },

            async restoreSuratFromIndexedDB() {
                try {
                    const db = await this.openSipetronDB();
                    const tx = db.transaction('draft_files', 'readonly');
                    const store = tx.objectStore('draft_files');
                    const request = store.get('surat_permohonan');
                    request.onsuccess = () => {
                        const data = request.result;
                        if (data && data.blob) {
                            try {
                                const restoredFile = new File([data.blob], data.name, {
                                    type: data.type || 'application/pdf',
                                    lastModified: data.lastModified || Date.now()
                                });
                                this.$nextTick(() => {
                                    if (this.$refs.suratInput) {
                                        const dt = new DataTransfer();
                                        dt.items.add(restoredFile);
                                        this.$refs.suratInput.files = dt.files;
                                    }
                                });
                                this.processSuratFile(restoredFile, false);
                            } catch (err) {
                                console.warn('Could not populate DataTransfer:', err);
                                if (data.name) {
                                    this.suratFileName = data.name;
                                    const sizeKb = (data.blob.size || 0) / 1024;
                                    this.suratFileSize = sizeKb >= 1024 ? (sizeKb / 1024).toFixed(1) + ' MB' : Math.round(sizeKb) + ' KB';
                                }
                            }
                        }
                    };
                } catch (e) {
                    console.warn('Failed to restore file from IndexedDB:', e);
                }
            },

            async clearSuratFromIndexedDB() {
                try {
                    const db = await this.openSipetronDB();
                    const tx = db.transaction('draft_files', 'readwrite');
                    tx.objectStore('draft_files').delete('surat_permohonan');
                } catch (err) { console.warn('[E-VITRON] Storage cleanup skipped:', err); }
            },

            saveDraft() {
                if (this.currentStep === 5 || {{ session('submission_success') ? 'true' : 'false' }}) {
                    return;
                }
                try {
                    const draft = {
                        currentStep: this.currentStep,
                        selectedVideotrons: this.selectedVideotrons,
                        startDate: this.startDate,
                        endDate: this.endDate,
                        startDisplay: this.startDisplay,
                        endDisplay: this.endDisplay,
                        suratFileName: this.suratFileName,
                        suratFileSize: this.suratFileSize,
                        form: {
                            title: this.form.title,
                            contentType: this.form.contentType,
                            durationUnit: this.form.durationUnit,
                            durationValue: this.form.durationValue,
                            videoDurationSeconds: this.form.videoDurationSeconds,
                            gdriveLink: this.form.gdriveLink,
                            picName: this.form.picName,
                            whatsapp: this.form.whatsapp,
                            organization: this.form.organization,
                            description: this.form.description
                        }
                    };
                    sessionStorage.setItem('sipetron_wizard_draft_{{ Auth::id() }}', JSON.stringify(draft));
                } catch (e) {
                    console.warn('sessionStorage not available:', e);
                }
            },

            restoreDraft() {
                @if ($errors->any() || session('submission_success'))
                    this.clearSuratFromIndexedDB();
                    return;
                @endif

                try {
                    const saved = sessionStorage.getItem('sipetron_wizard_draft_{{ Auth::id() }}');
                    if (saved) {
                        const draft = JSON.parse(saved);
                        if (draft && typeof draft === 'object') {
                            // Pulihkan Langkah 1
                            if (Array.isArray(draft.selectedVideotrons) && draft.selectedVideotrons.length > 0) {
                                this.selectedVideotrons = draft.selectedVideotrons;
                            }

                            // Pulihkan Langkah 2 (Beserta Display Text & Tahun/Bulan Kalender)
                            if (draft.startDate) {
                                this.startDate = draft.startDate;
                                this.startDisplay = draft.startDisplay || this.formatDisplayShort(draft.startDate);
                                const parts = draft.startDate.split('-');
                                if (parts.length === 3) {
                                    this.startCalendarYear = parseInt(parts[0], 10);
                                    this.startCalendarMonth = parseInt(parts[1], 10) - 1;
                                }
                            }
                            if (draft.endDate) {
                                this.endDate = draft.endDate;
                                this.endDisplay = draft.endDisplay || this.formatDisplayShort(draft.endDate);
                                const parts = draft.endDate.split('-');
                                if (parts.length === 3) {
                                    this.endCalendarYear = parseInt(parts[0], 10);
                                    this.endCalendarMonth = parseInt(parts[1], 10) - 1;
                                }
                            }

                            // Pulihkan info berkas metadata
                            if (draft.suratFileName) {
                                this.suratFileName = draft.suratFileName;
                                this.suratFileSize = draft.suratFileSize || '';
                            }

                            // Pulihkan Langkah 3
                            if (draft.form && typeof draft.form === 'object') {
                                if (draft.form.title !== undefined) this.form.title = draft.form.title;
                                if (draft.form.contentType !== undefined) this.form.contentType = draft.form.contentType;
                                if (draft.form.durationUnit !== undefined) this.form.durationUnit = draft.form.durationUnit;
                                if (draft.form.durationValue !== undefined) this.form.durationValue = draft.form.durationValue;
                                if (draft.form.videoDurationSeconds !== undefined) this.form.videoDurationSeconds = draft.form.videoDurationSeconds;
                                if (draft.form.gdriveLink !== undefined) this.form.gdriveLink = draft.form.gdriveLink;
                                if (draft.form.picName !== undefined) this.form.picName = draft.form.picName;
                                if (draft.form.whatsapp !== undefined) this.form.whatsapp = draft.form.whatsapp;
                                if (draft.form.organization !== undefined) this.form.organization = draft.form.organization;
                                if (draft.form.description !== undefined) this.form.description = draft.form.description;
                            }

                            // Pulihkan Langkah Aktif (jika lebih dari langkah 1)
                            if (draft.currentStep && draft.currentStep > 1 && draft.currentStep < 5) {
                                this.currentStep = draft.currentStep;
                                this.showNoticeModal = false;
                                this.toggleBodyScroll(false);
                            }

                            // HANYA pulihkan file fisik jika draf aktif memang memiliki file surat
                            if (draft.suratFileName) {
                                this.restoreSuratFromIndexedDB();
                            } else {
                                this.clearSuratFromIndexedDB();
                            }
                        }
                    } else {
                        // Sesi baru (laptop baru nyala / browser baru dibuka): bersihkan berkas lama dari IndexedDB
                        this.clearSuratFromIndexedDB();
                        this.suratFileName = '';
                        this.suratFileSize = '';
                        if (this.$refs.suratInput) {
                            this.$refs.suratInput.value = '';
                        }
                    }
                } catch (e) {
                    console.warn('Failed to restore wizard draft:', e);
                }
            },

            clearDraft() {
                try {
                    sessionStorage.removeItem('sipetron_wizard_draft');
                sessionStorage.removeItem('sipetron_wizard_draft_{{ Auth::id() }}');
                } catch (err) { console.warn('[E-VITRON] Storage cleanup skipped:', err); }
                this.clearSuratFromIndexedDB();
            },

            goToStep(stepNumber) {
                this.currentStep = stepNumber;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            handleEnterKey(event) {
                // Jangan proses jika modal ketentuan sedang terbuka (biarkan modal yang memproses)
                if (this.showNoticeModal || this.isConfirmingNotice) {
                    return;
                }

                // Jangan intercept Enter jika user sedang berada di textarea (agar tetap bisa buat baris baru)
                if (event.target && event.target.tagName === 'TEXTAREA') {
                    return;
                }

                // Jangan intercept jika user sengaja fokus di link navigasi atau tombol 'Sebelumnya'
                if (event.target && (event.target.classList.contains('btn-wizard-secondary') || (event.target.closest && event.target.closest('.btn-wizard-secondary')) || event.target.tagName === 'A' || (event.target.closest && event.target.closest('a')))) {
                    return;
                }

                // Cegah perilaku submit form bawaan browser atau re-trigger tombol aktif
                event.preventDefault();
                if (document.activeElement && document.activeElement.blur) {
                    document.activeElement.blur();
                }

                if (this.currentStep === 1) {
                    this.validateAndGoStep2();
                } else if (this.currentStep === 2) {
                    this.showStartCalendar = false;
                    this.showEndCalendar = false;
                    this.validateAndGoStep3();
                } else if (this.currentStep === 3) {
                    this.validateAndGoStep4();
                } else if (this.currentStep === 4) {
                    if (!this.isSubmitting) {
                        const form = document.getElementById('submission-form');
                        if (form) form.requestSubmit();
                    }
                }
            },

            validateAndGoStep2() {
                if (this.selectedVideotrons.length === 0) {
                    this.step1Error = 'Silakan pilih minimal 1 perangkat videotron sebelum melanjutkan.';
                    return;
                }
                this.step1Error = '';
                this.goToStep(2);
            },

            validateDates() {
                let valid = true;
                this.startDateError = '';
                this.endDateError = '';

                if (!this.startDate) {
                    this.startDateError = 'Tanggal mulai penayangan wajib diisi.';
                    valid = false;
                }

                if (!this.endDate) {
                    this.endDateError = 'Tanggal selesai penayangan wajib diisi.';
                    valid = false;
                }

                if (this.startDate && this.endDate) {
                    const start = new Date(this.startDate);
                    const end = new Date(this.endDate);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    if (start < today) {
                        this.startDateError = 'Tanggal mulai tidak boleh berada di masa lalu.';
                        valid = false;
                    }

                    if (end < start) {
                        this.endDateError = 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.';
                        valid = false;
                    } else {
                        const diffTime = Math.abs(end - start);
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                        const maxDays = {{ (int) setting('max_display_days', 7) }};
                        if (maxDays > 0 && diffDays > maxDays) {
                            this.endDateError = `Maksimal durasi penayangan adalah ${maxDays} hari berturut-turut.`;
                            valid = false;
                        }
                    }
                }

                return valid;
            },

            validateAndGoStep3() {
                if (!this.validateDates()) {
                    return;
                }
                this.goToStep(3);
            },

            setDurationUnit(unit) {
                if (this.form.durationUnit === unit) return;
                this.form.durationUnit = unit;
                if (unit === 'detik') {
                    const sec = Math.round(parseFloat(this.form.durationValue || 0) * 60);
                    this.form.durationValue = sec > 0 ? Math.min(180, Math.max(5, sec)) : 30;
                    this.form.videoDurationSeconds = this.form.durationValue;
                } else {
                    const sec = parseInt(this.form.durationValue, 10) || parseInt(this.form.videoDurationSeconds, 10) || 30;
                    const min = Math.round((sec / 60) * 100) / 100;
                    this.form.durationValue = Math.min(3, Math.max(0.25, min));
                    this.form.videoDurationSeconds = Math.round(this.form.durationValue * 60);
                }
                delete this.formErrors.videoDuration;
            },

            updateDurationFromValue() {
                const val = parseFloat(this.form.durationValue);
                if (isNaN(val) || val <= 0) {
                    this.form.videoDurationSeconds = 0;
                    return;
                }
                if (this.form.durationUnit === 'menit') {
                    this.form.videoDurationSeconds = Math.round(val * 60);
                } else {
                    this.form.videoDurationSeconds = Math.round(val);
                }
                if (this.form.videoDurationSeconds >= 5 && this.form.videoDurationSeconds <= 180) {
                    delete this.formErrors.videoDuration;
                }
            },

            setQuickDuration(seconds, minutes) {
                this.form.videoDurationSeconds = seconds;
                this.form.durationValue = (this.form.durationUnit === 'menit') ? minutes : seconds;
                delete this.formErrors.videoDuration;
            },

            validateGdriveLink() {
                if (!this.form.gdriveLink || !this.form.gdriveLink.trim()) {
                    delete this.formErrors.gdriveLink;
                    return true;
                }
                const rawLink = this.form.gdriveLink.trim();
                try {
                    const url = new URL(rawLink);
                    if (url.protocol !== 'http:' && url.protocol !== 'https:') {
                        this.formErrors.gdriveLink = 'Format tautan harus diawali http:// atau https://';
                        return false;
                    }
                    const host = url.hostname.toLowerCase();
                    const isGoogleDrive = (host === 'drive.google.com' || host === 'docs.google.com');
                    const hasPath = url.pathname && url.pathname.length > 1;
                    if (!isGoogleDrive || !hasPath) {
                        this.formErrors.gdriveLink = 'Hanya menerima tautan Google Drive resmi (drive.google.com). Tautan lain tidak diperbolehkan.';
                        return false;
                    }
                    delete this.formErrors.gdriveLink;
                    return true;
                } catch (_) {
                    if (rawLink.length > 8) {
                        this.formErrors.gdriveLink = 'Format URL tidak valid. Contoh: https://drive.google.com/...';
                    }
                    return false;
                }
            },

            handleFileSelect(event) {
                const file = event.target.files && event.target.files[0];
                this.processSuratFile(file, true);
            },

            handleFileDrop(event) {
                this.isDraggingSurat = false;
                const file = event.dataTransfer.files && event.dataTransfer.files[0];
                if (file) {
                    if (this.$refs.suratInput) {
                        this.$refs.suratInput.files = event.dataTransfer.files;
                    }
                    this.processSuratFile(file, true);
                }
            },

            processSuratFile(file, saveToDb = true) {
                if (!file) return;
                delete this.formErrors.surat_peminjaman;

                if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
                    this.formErrors.surat_peminjaman = 'Berkas surat permohonan harus berformat PDF (.pdf).';
                    this.removeSuratFile();
                    return;
                }

                if (file.size > 2 * 1024 * 1024) {
                    this.formErrors.surat_peminjaman = 'Ukuran berkas surat permohonan maksimal 2 MB.';
                    this.removeSuratFile();
                    return;
                }

                this.suratFileName = file.name;
                const sizeKb = file.size / 1024;
                if (sizeKb >= 1024) {
                    this.suratFileSize = (sizeKb / 1024).toFixed(1) + ' MB';
                } else {
                    this.suratFileSize = Math.round(sizeKb) + ' KB';
                }

                this.saveDraft();

                if (saveToDb) {
                    this.storeSuratInIndexedDB(file);
                }
            },

            removeSuratFile() {
                this.suratFileName = '';
                this.suratFileSize = '';
                if (this.$refs.suratInput) {
                    this.$refs.suratInput.value = '';
                }
                this.clearSuratFromIndexedDB();
                this.saveDraft();
            },

            validateContentInfo() {
                this.formErrors = {};
                let valid = true;

                if (!this.form.title || !this.form.title.trim()) {
                    this.formErrors.title = 'Judul konten kegiatan wajib diisi.';
                    valid = false;
                }

                if (this.form.contentType === 'video') {
                    const sec = parseInt(this.form.videoDurationSeconds, 10);
                    if (isNaN(sec) || sec < 5 || sec > 180) {
                        this.formErrors.videoDuration = 'Durasi video maksimal 3 menit (antara 5 s/d 180 detik).';
                        valid = false;
                    }
                }

                if (!this.form.gdriveLink || !this.form.gdriveLink.trim()) {
                    this.formErrors.gdriveLink = 'Link Google Drive bahan tayang wajib diisi.';
                    valid = false;
                } else if (!this.validateGdriveLink()) {
                    valid = false;
                }

                if (!this.form.picName || !this.form.picName.trim()) {
                    this.formErrors.picName = 'Nama penanggung jawab wajib diisi.';
                    valid = false;
                }

                if (!this.form.whatsapp || !this.form.whatsapp.trim()) {
                    this.formErrors.whatsapp = 'Nomor WhatsApp wajib diisi.';
                    valid = false;
                }

                if (!this.form.description || !this.form.description.trim()) {
                    this.formErrors.description = 'Deskripsi konten wajib diisi.';
                    valid = false;
                }

                if (!this.suratFileName) {
                    this.formErrors.surat_peminjaman = 'Surat permohonan resmi (PDF) wajib dilampirkan.';
                    valid = false;
                }

                return valid;
            },

            validateAndGoStep4() {
                if (!this.validateContentInfo()) {
                    return;
                }
                this.goToStep(4);
            },

            handleFormSubmit(event) {
                if (typeof navigator !== 'undefined' && navigator.onLine === false) {
                    if (event && typeof event.preventDefault === 'function') {
                        event.preventDefault();
                    }
                    this.isSubmitting = false;
                    if (typeof window.sipetronToast === 'function') {
                        window.sipetronToast({
                            type: 'error',
                            title: 'Koneksi Internet Terputus',
                            message: 'Mohon periksa jaringan internet Anda sebelum mengirim pengajuan.'
                        });
                    }
                    return false;
                }
                this.isSubmitting = true;
                this.clearDraft();
            },

            formatDisplayDate(dateStr) {
                if (!dateStr) return '-';
                try {
                    const parts = dateStr.split('-');
                    if (parts.length === 3) {
                        const d = new Date(parts[0], parts[1] - 1, parts[2]);
                        return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                    }
                    return dateStr;
                } catch (_) {
                    return dateStr;
                }
            }
    };
</script>