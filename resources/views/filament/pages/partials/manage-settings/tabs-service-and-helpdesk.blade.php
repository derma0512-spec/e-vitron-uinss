            {{-- ================================================================
                 TAB 3 — LAYANAN & PENGUMUMAN
            ================================================================ --}}
            <div x-show="tab === 'service'" class="sipetron-settings-card" style="display:none;">

                <div class="sipetron-card-header">
                    <h3 class="sipetron-settings-card-title">Status Layanan & Pengumuman Kampus</h3>
                    <p class="sipetron-settings-card-desc">Kendalikan buka/tutup pengajuan dan siaran pengumuman di beranda portal pemohon.</p>
                </div>

                {{-- Section 1: Operasional Penerimaan Pengajuan --}}
                <div class="sipetron-form-section">
                    <div class="sipetron-form-section-label">
                        <span>Operasional Penerimaan Pengajuan</span>
                    </div>

                    <div class="sipetron-subgroup">
                        <div class="sipetron-subgroup-header">
                            <span class="sipetron-subgroup-title">
                                <span class="sipetron-subgroup-badge">
                                    <svg class="sipetron-subgroup-badge-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.348 14.652a3.75 3.75 0 0 1 0-5.304m5.304 0a3.75 3.75 0 0 1 0 5.304m-7.425 2.121a6.75 6.75 0 0 1 0-9.546m9.546 0a6.75 6.75 0 0 1 0 9.546M5.106 18.894c-3.808-3.807-3.808-9.98 0-13.788m13.788 0c3.808 3.807 3.808 9.98 0 13.788M12 12h.008v.008H12V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                    </svg>
                                </span>
                                <span>Penerimaan Pengajuan Pemohon</span>
                            </span>

                            <div class="sipetron-subgroup-action">
                                <span class="sipetron-toggle-state-badge" 
                                      :class="$wire.service_active ? 'sipetron-toggle-state-active' : 'sipetron-toggle-state-warning'" 
                                      x-text="$wire.service_active ? '● Layanan Dibuka' : '● Layanan Ditutup'"></span>
                                <label class="sipetron-switch-toggle" title="Buka atau tutup penerimaan pengajuan">
                                    <input type="checkbox" wire:model="service_active">
                                    <span class="sipetron-switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        {{-- Saat Layanan Aktif --}}
                        <div x-show="$wire.service_active" class="sipetron-service-active-card">
                            <div class="sipetron-service-active-badge">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                <span>Portal Aktif Menerima Pengajuan</span>
                            </div>
                            <p class="sipetron-service-active-desc">
                                Formulir pengajuan di portal publik terbuka normal. Sivitas akademika, unit kampus, dan mitra dapat mengajukan materi publikasi kegiatan ke sistem videotron.
                            </p>
                        </div>

                        {{-- Saat Layanan Ditutup --}}
                        <div x-show="!$wire.service_active" class="sipetron-service-closed-wrapper">
                            <div class="sipetron-service-closed-notice">
                                <div class="sipetron-service-closed-badge">
                                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                    </svg>
                                    <span>Layanan Pengajuan Ditutup Sementara</span>
                                </div>
                                <p class="sipetron-service-closed-desc">
                                    Portal pengajuan dinonaktifkan sementara. Pemohon yang membuka halaman formulir akan melihat pesan penutupan di bawah ini.
                                </p>
                            </div>

                            <div class="sipetron-form-group">
                                <label class="sipetron-form-label">Pesan Pemberitahuan Penutupan Layanan <span class="sipetron-required-star" style="color: #EF4444; font-weight: 700;">*</span></label>
                                <textarea wire:model.defer="service_closed_message" rows="3" class="sipetron-form-textarea" placeholder="Contoh: Layanan pengajuan ditutup sementara selama masa libur semester ganjil atau pemeliharaan teknis sistem."></textarea>
                                <span class="sipetron-form-hint">Pesan ini ditampilkan secara mencolok pada halaman formulir pengajuan pemohon.</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Siaran Pengumuman Halaman Depan --}}
                <div class="sipetron-form-section">
                    <div class="sipetron-form-section-label">
                        <span>Siaran Pengumuman Halaman Depan</span>
                    </div>

                    <div class="sipetron-subgroup" x-data="{ bannerText: @entangle('announcement_banner_text') }">
                        <div class="sipetron-subgroup-header">
                            <span class="sipetron-subgroup-title">
                                <span class="sipetron-subgroup-badge">
                                    <svg class="sipetron-subgroup-badge-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 0 1-1.44-4.282m3.102.069a18.03 18.03 0 0 1-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 0 1 8.835 2.535M10.34 6.66a23.847 23.847 0 0 0 8.835-2.535m0 0A23.74 23.74 0 0 0 18.795 3m.38 1.125a23.91 23.91 0 0 1 1.01 5.395m-1.01 8.355c.386 1.76.732 3.565 1.01 5.395m0-13.75a2.25 2.25 0 0 1 2.25 2.25v4.5a2.25 2.25 0 0 1-2.25 2.25" />
                                    </svg>
                                </span>
                                <span>Banner Pemberitahuan di Beranda Pemohon</span>
                            </span>

                            <div class="sipetron-subgroup-action">
                                <span class="sipetron-toggle-state-badge" 
                                      :class="$wire.announcement_active ? 'sipetron-toggle-state-active' : 'sipetron-toggle-state-inactive'" 
                                      x-text="$wire.announcement_active ? '● Banner Aktif' : '● Banner Nonaktif'"></span>
                                <label class="sipetron-switch-toggle" title="Aktifkan atau nonaktifkan banner pengumuman">
                                    <input type="checkbox" wire:model="announcement_active">
                                    <span class="sipetron-switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        {{-- Saat Banner Aktif --}}
                        <div x-show="$wire.announcement_active" class="sipetron-banner-active-wrapper">
                            <div class="sipetron-form-group">
                                <label class="sipetron-form-label">Teks Isi Pengumuman <span class="sipetron-required-star" style="color: #EF4444; font-weight: 700;">*</span></label>
                                <textarea x-model="bannerText" wire:model.defer="announcement_banner_text" rows="2" class="sipetron-form-textarea" placeholder="Contoh: Perhatian: Penayangan konten pada masa libur operasional kampus dijadwalkan secara terbatas."></textarea>
                                <span class="sipetron-form-hint">Pesan ini langsung muncul di pita atas beranda portal pengajuan.</span>
                            </div>

                            {{-- Live Mockup / Preview Banner --}}
                            <div class="sipetron-banner-preview-box">
                                <div class="sipetron-banner-preview-header">
                                    <span class="sipetron-banner-preview-tag">SIMULASI TAMPILAN DI BERANDA PEMOHON:</span>
                                </div>
                                <div class="sipetron-banner-mockup">
                                    <div class="sipetron-banner-mockup-icon">
                                        <svg class="w-4 h-4 text-[#D97706]" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                        </svg>
                                    </div>
                                    <div class="sipetron-banner-mockup-text" x-text="bannerText || 'Teks pengumuman akan ditampilkan di sini...'"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Saat Banner Nonaktif --}}
                        <div x-show="!$wire.announcement_active" class="sipetron-unlimited-notice">
                            <div class="sipetron-unlimited-badge" style="color: #64748B;">
                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                                <span>Banner Tidak Ditampilkan</span>
                            </div>
                            <p class="sipetron-unlimited-desc" style="color: #64748B;">
                                Beranda portal pemohon akan tampil bersih tanpa pita siaran pengumuman di bagian atas.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================================================================
                 TAB 4 — KONTAK & HELPDESK
            ================================================================ --}}
            <div x-show="tab === 'helpdesk'" class="sipetron-settings-card" style="display:none;">

                <div class="sipetron-card-header">
                    <h3 class="sipetron-settings-card-title">Kontak & Helpdesk Layanan</h3>
                    <p class="sipetron-settings-card-desc">Narahubung resmi yang tampil di footer portal dan pesan notifikasi bantuan pemohon.</p>
                </div>

                {{-- Section: Kanal Komunikasi & Layanan Kampus --}}
                <div class="sipetron-form-section">
                    <div class="sipetron-form-section-label">
                        <span>Kanal Komunikasi & Layanan Kampus</span>
                    </div>

                    {{-- Subgroup 1: Narahubung Daring (WhatsApp & Email Resmi) --}}
                    <div class="sipetron-subgroup">
                        <div class="sipetron-subgroup-header">
                            <span class="sipetron-subgroup-title">
                                <span class="sipetron-subgroup-badge">
                                    <svg class="sipetron-subgroup-badge-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a.75.75 0 0 1-.974-.974 5.968 5.968 0 0 1 1.058-4.035A8.04 8.04 0 0 1 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
                                    </svg>
                                </span>
                                <span>Narahubung Daring (Online Support)</span>
                            </span>
                        </div>
                        <div class="sipetron-form-grid sipetron-form-grid-2">
                            {{-- Field WhatsApp --}}
                            <div class="sipetron-form-group">
                                <label class="sipetron-form-label">Nomor WhatsApp Helpdesk</label>
                                <input type="text" wire:model.defer="helpdesk_whatsapp" class="sipetron-form-input" placeholder="081234567890 atau +6281234567890">
                                <span class="sipetron-form-hint">Format bebas: bisa diawali <strong>08...</strong> atau <strong>+62...</strong> (sistem otomatis menghubungkan tautan WhatsApp pemohon).</span>
                            </div>

                            {{-- Field Email --}}
                            <div class="sipetron-form-group">
                                <label class="sipetron-form-label">Pos-el (Email) Layanan Bantuan</label>
                                <input type="email" wire:model.defer="helpdesk_email" class="sipetron-form-input" placeholder="info@uinssc.ac.id">
                                <span class="sipetron-form-hint">Email resmi untuk korespondensi teknis dan pertanyaan pemohon.</span>
                            </div>
                        </div>
                    </div>

                    {{-- Subgroup 2: Layanan Kantor & Lokasi Fisik --}}
                    <div class="sipetron-subgroup">
                        <div class="sipetron-subgroup-header">
                            <span class="sipetron-subgroup-title">
                                <span class="sipetron-subgroup-badge">
                                    <svg class="sipetron-subgroup-badge-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                    </svg>
                                </span>
                                <span>Operasional Kantor & Lokasi Pelayanan</span>
                            </span>
                        </div>
                        <div class="sipetron-form-grid sipetron-form-grid-2">
                            {{-- Field Jam Operasional --}}
                            <div class="sipetron-form-group">
                                <label class="sipetron-form-label">Jam Operasional Pelayanan</label>
                                <input type="text" wire:model.defer="helpdesk_hours" class="sipetron-form-input" placeholder="Senin – Jumat, 08.00 – 16.00 WIB">
                                <span class="sipetron-form-hint">Jadwal aktif staf pengelola untuk melayani konsultasi langsung.</span>
                            </div>

                            {{-- Field Lokasi --}}
                            <div class="sipetron-form-group">
                                <label class="sipetron-form-label">Lokasi Ruang Layanan Fisik</label>
                                <input type="text" wire:model.defer="helpdesk_location" class="sipetron-form-input" placeholder="Gedung Rektorat Kampus UIN Siber Syekh Nurjati Cirebon">
                                <span class="sipetron-form-hint">Gedung dan lantai tempat pemohon dapat berkonsultasi secara tatap muka.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================================================================
                 FORM FOOTER — Tombol Simpan (sekali, di bawah, kanan)
            ================================================================ --}}
            <div class="sipetron-form-footer">
                <p class="sipetron-form-footer-note">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" /></svg>
                    Perubahan diterapkan ke seluruh sistem setelah disimpan.
                </p>
                <button type="submit" 
                        id="sipetron-save-btn"
                        wire:loading.attr="disabled" 
                        wire:loading.class="opacity-75 cursor-wait" 
                        class="sipetron-settings-save-btn"
                        title="Simpan pengaturan (Tekan Enter untuk simpan)">
                    <svg wire:loading class="w-4 h-4 animate-spin shrink-0" fill="none" viewBox="0 0 24 24" style="display: none;">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span wire:loading.remove>Simpan Perubahan</span>
                    <span wire:loading style="display: none;">Menyimpan...</span>
                </button>
            </div>
