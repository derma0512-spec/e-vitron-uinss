<x-filament-panels::page class="fi-manage-settings-page">
    <div class="sipetron-settings-wrap" 
         x-data="{ 
            tab: @entangle('activeTab'),
            switchTab(targetTab) {
                this.tab = targetTab;
                const url = new URL(window.location);
                if (targetTab === 'signatory') {
                    url.searchParams.delete('tab');
                } else {
                    url.searchParams.set('tab', targetTab);
                }
                window.history.replaceState({}, '', url);
            }
         }"
         @keydown.enter="if ($event.target.tagName !== 'TEXTAREA' && $event.target.tagName !== 'BUTTON') { $event.preventDefault(); document.getElementById('sipetron-save-btn')?.click(); }"
         @keydown.ctrl.enter="$event.preventDefault(); document.getElementById('sipetron-save-btn')?.click();">

        {{-- Navigation Tabs --}}
        <div class="sipetron-settings-nav" role="tablist">
            <button type="button" role="tab" :aria-selected="tab === 'signatory'" @click="switchTab('signatory')" :class="tab === 'signatory' ? 'is-active bg-[#0F3D2E] text-white border-[#0F3D2E] shadow-sm font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'" class="sipetron-settings-tab-btn">
                <svg class="sipetron-settings-tab-icon" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                <span>Dokumen & Penandatangan</span>
            </button>
            <button type="button" role="tab" :aria-selected="tab === 'policy'" @click="switchTab('policy')" :class="tab === 'policy' ? 'is-active bg-[#0F3D2E] text-white border-[#0F3D2E] shadow-sm font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'" class="sipetron-settings-tab-btn">
                <svg class="sipetron-settings-tab-icon" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0 0 12 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 0 1-2.031.352 5.988 5.988 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971Zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 0 1-2.031.352 5.989 5.989 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.971Z" /></svg>
                <span>Kebijakan Pengajuan</span>
            </button>
            <button type="button" role="tab" :aria-selected="tab === 'service'" @click="switchTab('service')" :class="tab === 'service' ? 'is-active bg-[#0F3D2E] text-white border-[#0F3D2E] shadow-sm font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'" class="sipetron-settings-tab-btn">
                <svg class="sipetron-settings-tab-icon" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
                <span>Layanan & Pengumuman</span>
            </button>
            <button type="button" role="tab" :aria-selected="tab === 'helpdesk'" @click="switchTab('helpdesk')" :class="tab === 'helpdesk' ? 'is-active bg-[#0F3D2E] text-white border-[#0F3D2E] shadow-sm font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'" class="sipetron-settings-tab-btn">
                <svg class="sipetron-settings-tab-icon" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>
                <span>Kontak & Helpdesk</span>
            </button>
        </div>

        <form wire:submit.prevent="save" id="sipetron-settings-form" novalidate>

            {{-- ================================================================
                 TAB 1 — DOKUMEN & PENANDATANGAN
            ================================================================ --}}
            <div x-show="tab === 'signatory'" class="sipetron-settings-card">

                {{-- Card Header --}}
                <div class="sipetron-card-header">
                    <h3 class="sipetron-settings-card-title">Pejabat Penandatangan & Kop Surat Resmi</h3>
                    <p class="sipetron-settings-card-desc">Data pejabat pada lembar pengesahan laporan rekap PDF serta teks resmi kop surat dinas kampus.</p>
                </div>

                {{-- Section 1: Lembar Pengesahan --}}
                <div class="sipetron-form-section">
                    <div class="sipetron-form-section-label">
                        <span>Lembar Pengesahan & Tanda Tangan Laporan</span>
                    </div>

                    {{-- Identitas Penandatangan Dokumen Resmi (Opsi A: Tunggal Kanan) --}}
                    <div class="sipetron-subgroup">
                        <div class="sipetron-subgroup-header">
                            <span class="sipetron-subgroup-title">
                                <span class="sipetron-subgroup-badge">
                                    <svg class="sipetron-subgroup-badge-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                </span>
                                <span>Identitas Penandatangan Dokumen Resmi</span>
                            </span>
                        </div>
                        <div class="sipetron-form-grid sipetron-form-grid-2">
                            <div class="sipetron-form-group">
                                <label class="sipetron-form-label">Sebutan Jabatan Pengelola <span class="sipetron-required-star" style="color: #EF4444; font-weight: 700;">*</span></label>
                                <input type="text" wire:model.defer="signatory_admin_title" class="sipetron-form-input" placeholder="Tim Pengelola E-VITRON">
                                <span class="sipetron-form-hint">Ditampilkan di atas ruang tanda tangan (sisi kanan).</span>
                                @error('signatory_admin_title') <span class="sipetron-form-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="sipetron-form-group">
                                <label class="sipetron-form-label">Satuan Kerja / Unit Kampus</label>
                                <input type="text" wire:model.defer="signatory_admin_unit" class="sipetron-form-input" placeholder="UIN Siber Syekh Nurjati Cirebon">
                                <span class="sipetron-form-hint">Unit induk penyelenggara penayangan videotron.</span>
                            </div>
                            <div class="sipetron-form-group sipetron-col-full">
                                <label class="sipetron-form-label">Nama Pengesah Dokumen <span class="sipetron-required-star" style="color: #EF4444; font-weight: 700;">*</span></label>
                                <input type="text" wire:model.defer="signatory_admin_name" class="sipetron-form-input" placeholder="Tim Admin E-VITRON">
                                <span class="sipetron-form-hint">Ditampilkan di bawah ruang tanda tangan / cap dinas.</span>
                                @error('signatory_admin_name') <span class="sipetron-form-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Kop Surat --}}
                <div class="sipetron-form-section">
                    <div class="sipetron-form-section-label">
                        <span>Teks Resmi Kop Surat Kampus</span>
                    </div>

                    {{-- Kelompok 1: Identitas Lembaga & Instansi --}}
                    <div class="sipetron-subgroup">
                        <div class="sipetron-subgroup-header">
                            <span class="sipetron-subgroup-title">
                                <span class="sipetron-subgroup-badge">
                                    <svg class="sipetron-subgroup-badge-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />
                                    </svg>
                                </span>
                                <span>Identitas Lembaga & Instansi</span>
                            </span>
                        </div>
                        <div class="sipetron-form-grid sipetron-form-grid-2">
                            <div class="sipetron-form-group sipetron-col-full">
                                <label class="sipetron-form-label">Baris 1 — Kementerian / Lembaga Induk</label>
                                <input type="text" wire:model.defer="institution_line_1" class="sipetron-form-input" placeholder="KEMENTERIAN AGAMA REPUBLIK INDONESIA">
                            </div>
                            <div class="sipetron-form-group sipetron-col-full">
                                <label class="sipetron-form-label">Baris 2 — Nama Universitas Resmi</label>
                                <input type="text" wire:model.defer="institution_line_2" class="sipetron-form-input" placeholder="UNIVERSITAS ISLAM NEGERI SIBER SYEKH NURJATI CIREBON">
                            </div>
                            <div class="sipetron-form-group sipetron-col-full">
                                <label class="sipetron-form-label">Baris 3 — Unit / Lembaga Pengelola</label>
                                <input type="text" wire:model.defer="institution_line_3" class="sipetron-form-input" placeholder="LAYANAN PENGAJUAN PENAYANGAN VIDEOTRON (E-VITRON)">
                            </div>
                        </div>
                    </div>

                    {{-- Kelompok 2: Alamat & Kontak Resmi --}}
                    <div class="sipetron-subgroup">
                        <div class="sipetron-subgroup-header">
                            <span class="sipetron-subgroup-title">
                                <span class="sipetron-subgroup-badge">
                                    <svg class="sipetron-subgroup-badge-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                    </svg>
                                </span>
                                <span>Alamat & Kontak Resmi</span>
                            </span>
                        </div>
                        <div class="sipetron-form-grid sipetron-form-grid-2">
                            <div class="sipetron-form-group sipetron-col-full">
                                <label class="sipetron-form-label">Alamat Kampus Lengkap</label>
                                <input type="text" wire:model.defer="institution_address" class="sipetron-form-input" placeholder="Jalan Perjuangan By Pass Sunyaragi, Kota Cirebon, Jawa Barat 45132">
                            </div>
                            <div class="sipetron-form-group">
                                <label class="sipetron-form-label">Website Resmi</label>
                                <input type="text" wire:model.defer="institution_web" class="sipetron-form-input" placeholder="https://uinssc.ac.id">
                            </div>
                            <div class="sipetron-form-group">
                                <label class="sipetron-form-label">Pos-el (Email) Resmi Unit</label>
                                <input type="email" wire:model.defer="institution_email" class="sipetron-form-input" placeholder="info@uinssc.ac.id">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================================================================
                 TAB 2 — KEBIJAKAN PENGAJUAN
            ================================================================ --}}
            <div x-show="tab === 'policy'" class="sipetron-settings-card" style="display:none;">

                <div class="sipetron-card-header">
                    <h3 class="sipetron-settings-card-title">Kebijakan & Batasan Operasional Pengajuan</h3>
                    <p class="sipetron-settings-card-desc">Parameter jadwal dan syarat teknis yang diterapkan otomatis pada form pengajuan pemohon.</p>
                </div>

                {{-- Section 1: Jadwal & Periode Penayangan --}}
                <div class="sipetron-form-section">
                    <div class="sipetron-form-section-label">
                        <span>Jadwal & Periode Penayangan</span>
                    </div>

                    {{-- Subgroup 1: Ketentuan Jadwal Pengajuan & Durasi Tayang (2 Kolom Sejajar Rata) --}}
                    <div class="sipetron-subgroup">
                        <div class="sipetron-subgroup-header">
                            <span class="sipetron-subgroup-title">
                                <span class="sipetron-subgroup-badge">
                                    <svg class="sipetron-subgroup-badge-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.253M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                    </svg>
                                </span>
                                <span>Ketentuan Jadwal Pengajuan & Durasi Tayang</span>
                            </span>

                            {{-- Toggle Batas Hari Tayang di Header Subgroup --}}
                            <div class="sipetron-subgroup-action">
                                <span class="sipetron-toggle-state-badge" :class="$wire.limit_display_days_enabled ? 'sipetron-toggle-state-active' : 'sipetron-toggle-state-inactive'" x-text="$wire.limit_display_days_enabled ? 'Batas Hari Aktif' : 'Bebas / Tanpa Batas'"></span>
                                <label class="sipetron-switch-toggle" title="Aktifkan/nonaktifkan batas durasi hari tayang">
                                    <input type="checkbox" wire:model="limit_display_days_enabled">
                                    <span class="sipetron-switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="sipetron-form-grid sipetron-form-grid-2">
                            {{-- Kolom 1: Minimal Hari Sebelum Tayang --}}
                            <div class="sipetron-form-group">
                                <label class="sipetron-form-label">Minimal Hari Sebelum Tayang (H-X) <span class="sipetron-required-star" style="color: #EF4444; font-weight: 700;">*</span></label>
                                <div class="sipetron-input-addon-wrap">
                                    <input type="number" min="0" max="30" wire:model.defer="min_days_before" class="sipetron-form-input">
                                    <span class="sipetron-input-addon">Hari</span>
                                </div>
                                <span class="sipetron-form-hint">Nilai 3 = pemohon wajib mengajukan minimal H-3 sebelum tanggal tayang.</span>
                                @error('min_days_before') <span class="sipetron-form-error">{{ $message }}</span> @enderror
                            </div>

                            {{-- Kolom 2: Maksimal Hari Penayangan --}}
                            <div class="sipetron-form-group">
                                <label class="sipetron-form-label">
                                    Maksimal Durasi Penayangan
                                    <span class="sipetron-required-star" style="color: #EF4444; font-weight: 700;" x-show="$wire.limit_display_days_enabled">*</span>
                                </label>

                                {{-- Jika Batas Aktif --}}
                                <div x-show="$wire.limit_display_days_enabled">
                                    <div class="sipetron-input-addon-wrap">
                                        <input type="number" min="1" max="365" wire:model.defer="max_display_days" class="sipetron-form-input">
                                        <span class="sipetron-input-addon">Hari</span>
                                    </div>
                                    <span class="sipetron-form-hint">Batas rentang tanggal tayang dalam satu nomor tiket pengajuan.</span>
                                    @error('max_display_days') <span class="sipetron-form-error">{{ $message }}</span> @enderror
                                </div>

                                {{-- Jika Bebas / Tanpa Batas --}}
                                <div x-show="!$wire.limit_display_days_enabled">
                                    <div class="sipetron-unlimited-pill">
                                        <svg class="sipetron-subgroup-badge-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                        <span>Bebas / Tanpa Batas Hari (Unlimited)</span>
                                    </div>
                                    <span class="sipetron-form-hint">Pemohon bebas memilih rentang tanggal tanpa batasan kuota hari.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Standar Teknis Materi & Konten --}}
                <div class="sipetron-form-section">
                    <div class="sipetron-form-section-label">
                        <span>Standar Teknis Materi & Berkas</span>
                    </div>

                    {{-- Subgroup 2: Durasi Video Cerdas --}}
                    <div class="sipetron-subgroup" 
                         x-data="{
                            seconds: @entangle('max_video_duration'),
                            unit: 'seconds',
                            inputVal: 30,
                            init() {
                                this.syncFromSeconds();
                                this.$watch('seconds', () => this.syncFromSeconds());
                            },
                            syncFromSeconds() {
                                const sec = parseInt(this.seconds) || 30;
                                if (sec >= 60 && sec % 60 === 0) {
                                    this.unit = 'minutes';
                                    this.inputVal = sec / 60;
                                } else {
                                    this.unit = 'seconds';
                                    this.inputVal = sec;
                                }
                            },
                            applyInput() {
                                let val = parseFloat(this.inputVal) || 0;
                                let targetSec = this.unit === 'minutes' ? Math.round(val * 60) : Math.round(val);
                                if (targetSec < 5) targetSec = 5;
                                if (targetSec > 300) targetSec = 300;
                                this.seconds = targetSec;
                            },
                            setPreset(sec) {
                                this.seconds = sec;
                                this.syncFromSeconds();
                            },
                            switchUnit(newUnit) {
                                if (this.unit === newUnit) return;
                                this.unit = newUnit;
                                const sec = parseInt(this.seconds) || 30;
                                if (this.unit === 'minutes') {
                                    this.inputVal = (sec / 60).toFixed(sec % 60 === 0 ? 0 : 1);
                                } else {
                                    this.inputVal = sec;
                                }
                            }
                         }">
                        <div class="sipetron-subgroup-header">
                            <span class="sipetron-subgroup-title">
                                <span class="sipetron-subgroup-badge">
                                    <svg class="sipetron-subgroup-badge-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                                    </svg>
                                </span>
                                <span>Maksimal Durasi Klip Video</span>
                            </span>
                            
                            {{-- Live Plafon Badge --}}
                            <span class="sipetron-pill-badge">Plafon Sistem: Hingga 5 Menit (300 Detik)</span>
                        </div>

                        <div class="sipetron-duration-wrapper">
                            <div class="sipetron-duration-input-row">
                                <div class="sipetron-duration-input-box">
                                    <label class="sipetron-form-label">Durasi Maksimal Kebijakan Kampus <span class="sipetron-required-star" style="color: #EF4444; font-weight: 700;">*</span></label>
                                    <div class="sipetron-unit-input-group">
                                        <input type="number" 
                                               x-model="inputVal" 
                                               @input.debounce.300ms="applyInput()" 
                                               step="any"
                                               min="0"
                                               class="sipetron-form-input">
                                        
                                        {{-- Unit Selector Segmented Control --}}
                                        <div class="sipetron-unit-selector">
                                            <button type="button" 
                                                    @click="switchUnit('seconds')" 
                                                    :class="unit === 'seconds' ? 'sipetron-unit-btn-active bg-[#0F3D2E] text-white shadow-sm font-bold' : 'sipetron-unit-btn text-slate-500 hover:text-slate-900'">
                                                Detik
                                            </button>
                                            <button type="button" 
                                                    @click="switchUnit('minutes')" 
                                                    :class="unit === 'minutes' ? 'sipetron-unit-btn-active bg-[#0F3D2E] text-white shadow-sm font-bold' : 'sipetron-unit-btn text-slate-500 hover:text-slate-900'">
                                                Menit
                                            </button>
                                        </div>
                                    </div>
                                    <span class="sipetron-form-hint">Admin bebas mengatur durasi sesuai kebijakan. Sistem mendukung hingga 5 menit (300 detik).</span>
                                </div>

                                {{-- Live Preview Card --}}
                                <div class="sipetron-duration-preview-card">
                                    <div class="sipetron-preview-label">Konversi Aktif di Sistem:</div>
                                    <div class="sipetron-preview-value">
                                        <span x-text="seconds"></span> Detik 
                                        <span class="sipetron-preview-sub" x-text="`(${(seconds / 60).toFixed(seconds % 60 === 0 ? 0 : 1)} Menit)`"></span>
                                    </div>
                                    <div class="sipetron-preview-hint">
                                        Diterapkan langsung ke form pengajuan pemohon & validasi file video.
                                    </div>
                                </div>
                            </div>

                            {{-- Preset Quick Chips --}}
                            <div class="sipetron-preset-section">
                                <span class="sipetron-preset-label">Pilihan Durasi Cepat (Quick Presets):</span>
                                <div class="sipetron-preset-chips">
                                    <button type="button" @click="setPreset(30)" :class="seconds == 30 ? 'sipetron-chip-active' : 'sipetron-chip'">30 Detik</button>
                                    <button type="button" @click="setPreset(60)" :class="seconds == 60 ? 'sipetron-chip-active' : 'sipetron-chip'">60 Detik (1 Menit)</button>
                                    <button type="button" @click="setPreset(120)" :class="seconds == 120 ? 'sipetron-chip-active' : 'sipetron-chip'">2 Menit</button>
                                    <button type="button" @click="setPreset(180)" :class="seconds == 180 ? 'sipetron-chip-active' : 'sipetron-chip'">3 Menit</button>
                                    <button type="button" @click="setPreset(240)" :class="seconds == 240 ? 'sipetron-chip-active' : 'sipetron-chip'">4 Menit</button>
                                    <button type="button" @click="setPreset(300)" :class="seconds == 300 ? 'sipetron-chip-active' : 'sipetron-chip'">5 Menit (Maksimal)</button>
                                </div>
                            </div>
                        </div>

                        {{-- Hidden field to guarantee wire sync --}}
                        <input type="hidden" wire:model.defer="max_video_duration">
                        @error('max_video_duration') <span class="sipetron-form-error">{{ $message }}</span> @enderror
                    </div>

                    {{-- Subgroup 3: Kapasitas Berkas & Format Dokumen (2 Kolom Sejajar) --}}
                    <div class="sipetron-subgroup">
                        <div class="sipetron-subgroup-header">
                            <span class="sipetron-subgroup-title">
                                <span class="sipetron-subgroup-badge">
                                    <svg class="sipetron-subgroup-badge-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
                                    </svg>
                                </span>
                                <span>Kapasitas Berkas & Tautan Template Dokumen</span>
                            </span>
                        </div>
                        <div class="sipetron-form-grid sipetron-form-grid-2">
                            {{-- Kolom 1: Ukuran Berkas --}}
                            <div class="sipetron-form-group">
                                <label class="sipetron-form-label">Maksimal Ukuran File Materi <span class="sipetron-required-star" style="color: #EF4444; font-weight: 700;">*</span></label>
                                <div class="sipetron-input-addon-wrap">
                                    <input type="number" min="5" max="500" wire:model.defer="max_file_size" class="sipetron-form-input">
                                    <span class="sipetron-input-addon">MB</span>
                                </div>
                                <span class="sipetron-form-hint">Kapasitas file video/gambar yang dapat diunggah pemohon ke Google Drive.</span>
                                @error('max_file_size') <span class="sipetron-form-error">{{ $message }}</span> @enderror
                            </div>

                            {{-- Kolom 2: Template Dokumen --}}
                            <div class="sipetron-form-group">
                                <label class="sipetron-form-label">Tautan Format Dokumen Surat Resmi</label>
                                <input type="text" wire:model.defer="template_doc_url" class="sipetron-form-input" placeholder="/downloads/Template-Surat-Permohonan-Videotron-UINSSC.docx">
                                <span class="sipetron-form-hint">Tombol "Unduh Template Surat" akan muncul otomatis di form pengajuan jika tautan diisi.</span>
                                @error('template_doc_url') <span class="sipetron-form-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- Tab 3 (Layanan & Pengumuman), Tab 4 (Kontak & Helpdesk) & Footer Simpan --}}
            @include('filament.pages.partials.manage-settings.tabs-service-and-helpdesk')

        </form>
    </div>
</x-filament-panels::page>
