            {{-- ==============================================================
                 LANGKAH 4 — PERIKSA PENGAJUAN
                 ============================================================== --}}
            <div data-step="4" 
                 x-show="currentStep === 4" 
                 x-cloak 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1.5"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="relative overflow-hidden bg-white/95 backdrop-blur-md rounded-2xl sm:rounded-[22px] border border-white/90 shadow-sm p-3.5 sm:p-8 space-y-3.5 sm:space-y-6">
                
                {{-- Watermark ornamen di sudut form --}}
                <div class="ornament-pattern" style="position:absolute;right:-25px;bottom:-25px;height:120px;width:120px;opacity:0.12;pointer-events:none;"></div>

                <!-- Header Kartu Langkah 4 -->
                <div>
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-6 w-6 sm:h-7 sm:w-7 items-center justify-center rounded-full text-white shadow-xs shrink-0" style="background:#D9A93B;">
                            <svg class="h-3 w-3 sm:h-3.5 sm:w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </span>
                        <h2 class="text-sm sm:text-lg font-montserrat font-extrabold text-[#0E4D3C]">
                            <span class="sm:hidden">Konfirmasi Pengajuan</span>
                            <span class="hidden sm:inline">Langkah 4 — Periksa Pengajuan</span>
                        </h2>
                    </div>
                    <p class="text-[11px] sm:text-xs text-[#4E655F] mt-0.5 sm:mt-1 pl-8 sm:pl-9.5">Pastikan semua data yang kamu isi sudah benar sebelum dikirim.</p>
                </div>

                <!-- 3 Kartu Rangkuman Horizontal Sesuai Mockup -->
                <div class="space-y-4">
                    
                    <!-- KARTU 1: Lokasi & Jadwal Penayangan -->
                    <div class="p-3.5 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white shadow-2xs space-y-3 sm:space-y-3.5">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-[#0E4D3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <h3 class="font-bold text-[#0E4D3C] text-xs sm:text-sm">1. Lokasi & Jadwal Penayangan</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                            <!-- Kolom Kiri: Videotron Info (Format 3 Baris Rapi & Terstruktur) -->
                            <template x-for="item in selectedVideotronDetails" :key="item.id">
                                <div class="flex items-start gap-3">
                                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-lg bg-slate-100 overflow-hidden shrink-0 border border-slate-200/80 mt-0.5">
                                        <img :src="item.image_url || (item.orientation === 'portrait' ? '{{ asset('images/hero_banner.jpg') }}' : '{{ asset('images/campus-videotron.png') }}')" 
                                             :alt="item.name"
                                             class="w-full h-full object-cover">
                                    </div>
                                    <div class="min-w-0 flex-1 space-y-0.5">
                                        <h4 class="font-bold text-slate-800 text-xs sm:text-sm truncate" x-text="item.name"></h4>
                                        <div class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                            <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <span class="truncate" x-text="item.location"></span>
                                        </div>
                                        <div class="text-[10px] sm:text-[10.5px] text-slate-400 font-mono flex items-center gap-1">
                                            <span x-text="`${item.resolution} px`"></span>
                                            <span class="text-slate-300">&bull;</span>
                                            <span class="capitalize" x-text="item.orientation"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- Kolom Kanan: Jadwal Info & Badge Durasi Rapi Terintegrasi -->
                            <div class="pt-2.5 sm:pt-0 border-t sm:border-t-0 sm:border-l border-slate-100 sm:pl-6">
                                <div class="flex items-start gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-lg bg-[#EAF5F0] text-[#0E4D3C] flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-4 h-4 text-[#0E4D3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="block text-[10.5px] text-slate-400 font-medium">Periode Tayang</span>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-[#EAF5F0] text-[#0E4D3C] border border-[#CDE5D9] shrink-0 whitespace-nowrap shadow-2xs">
                                                <svg class="w-3 h-3 text-[#0E4D3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span x-text="`${calculatedDurationDays} Hari`"></span>
                                            </span>
                                        </div>
                                        <p class="font-bold text-slate-800 text-xs sm:text-sm whitespace-normal leading-snug mt-0.5" x-text="`${formatDisplayDate(startDate)} — ${formatDisplayDate(endDate)}`"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KARTU 2: Materi Publikasi -->
                    <div class="p-3.5 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white shadow-2xs space-y-3 sm:space-y-3.5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-[#0E4D3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
                                <h3 class="font-bold text-[#0E4D3C] text-xs sm:text-sm">2. Materi Publikasi</h3>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <template x-if="form.contentType === 'video'">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-[#EAF5F0] text-[#0E4D3C] border border-[#CDE5D9] whitespace-nowrap shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-[#0E4D3C] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        <span>Video</span>
                                        <span class="text-slate-300">&bull;</span>
                                        <span x-text="formattedDurationSummary"></span>
                                    </span>
                                </template>
                                <template x-if="form.contentType !== 'video'">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-[#EAF5F0] text-[#0E4D3C] border border-[#CDE5D9] whitespace-nowrap shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-[#0E4D3C] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span>Poster (Gambar)</span>
                                    </span>
                                </template>
                            </div>
                        </div>

                        <div class="space-y-2.5 text-xs">
                            <!-- Baris Judul Konten -->
                            <div class="flex flex-col sm:flex-row sm:items-baseline gap-1 sm:gap-4">
                                <span class="w-32 shrink-0 text-slate-400 font-medium">Judul Konten</span>
                                <span class="font-extrabold text-slate-900 text-xs sm:text-sm" x-text="form.title"></span>
                            </div>

                            <!-- Baris Bahan Tayang (Google Drive) -->
                            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4">
                                <span class="w-28 sm:w-32 shrink-0 text-slate-400 font-medium">Bahan Tayang</span>
                                <a :href="form.gdriveLink" 
                                   target="_blank" 
                                   rel="noopener noreferrer" 
                                   class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-[#EAF5F0] border border-slate-200 hover:border-[#0E4D3C]/40 text-xs font-semibold text-[#0E4D3C] transition-all shadow-2xs w-fit">
                                    <svg class="w-4 h-4 shrink-0 drop-shadow-2xs" viewBox="0 0 87.3 78" xmlns="http://www.w3.org/2000/svg">
                                        <path d="m6.6 66.85 3.85 6.65c.8 1.4 1.95 2.5 3.3 3.3l13.75-23.8h-27.5c0 1.55.4 3.1 1.2 4.5z" fill="#0066da"/>
                                        <path d="m43.65 25-13.75-23.8c-1.35.8-2.5 1.9-3.3 3.3l-25.4 44a9.06 9.06 0 0 0 -1.2 4.5h27.5z" fill="#00ac47"/>
                                        <path d="m73.55 76.8c1.35-.8 2.5-1.9 3.3-3.3l1.6-2.75 7.65-13.25c.8-1.4 1.2-2.95 1.2-4.5h-27.502l5.852 11.5z" fill="#ea4335"/>
                                        <path d="m43.65 25 13.75-23.8c-1.35-.8-2.9-1.2-4.5-1.2h-18.5c-1.6 0-3.15.45-4.5 1.2z" fill="#00832d"/>
                                        <path d="m59.8 53h-32.3l-13.75 23.8c1.35.8 2.9 1.2 4.5 1.2h50.8c1.6 0 3.15-.45 4.5-1.2z" fill="#2684fc"/>
                                        <path d="m73.4 26.5-12.7-22c-.8-1.4-1.95-2.5-3.3-3.3l-13.75 23.8 16.15 28h27.45c0-1.55-.4-3.1-1.2-4.5z" fill="#ffba00"/>
                                    </svg>
                                    <span>Buka File di Google Drive</span>
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </div>

                            <!-- Baris Deskripsi Singkat -->
                            <div class="flex flex-col sm:flex-row sm:items-baseline gap-1 sm:gap-4" x-show="form.description">
                                <span class="w-32 shrink-0 text-slate-400 font-medium">Deskripsi Singkat</span>
                                <span class="text-slate-600 leading-relaxed italic" x-text="form.description"></span>
                            </div>
                        </div>
                    </div>

                    <!-- KARTU 3: Penanggung Jawab (PIC) & Berkas Resmi -->
                    <div class="p-3.5 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white shadow-2xs">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                            <!-- Sisi Kiri: Data PIC -->
                            <div class="space-y-2">
                                <div class="flex items-center gap-1.5 pb-1">
                                    <svg class="w-4 h-4 text-[#0E4D3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <h3 class="font-bold text-[#0E4D3C] text-xs sm:text-sm">3. Penanggung Jawab (PIC) & Berkas Resmi</h3>
                                </div>
                                <div class="space-y-1.5 text-xs">
                                    <div class="flex items-center gap-3">
                                        <span class="w-24 shrink-0 text-slate-400 font-medium">Nama PIC</span>
                                        <span class="font-bold text-slate-800" x-text="form.picName"></span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="w-24 shrink-0 text-slate-400 font-medium">Unit / Ormawa</span>
                                        <span class="text-slate-700" x-text="form.organization || '-'"></span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="w-24 shrink-0 text-slate-400 font-medium">WhatsApp</span>
                                        <span class="font-mono font-bold text-slate-800 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            <span x-text="form.whatsapp"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Sisi Kanan: Dokumen Surat Permohonan Resmi -->
                            <div class="md:pl-6 md:border-l md:border-slate-100 space-y-2">
                                <div class="flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                                    <svg class="w-3.5 h-3.5 text-[#0E4D3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>Surat Permohonan Resmi</span>
                                </div>

                                <div class="flex items-center justify-between p-2.5 rounded-xl border border-slate-200/90 bg-white" x-show="suratFileName">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-7 h-7 rounded-lg bg-rose-50 border border-rose-100 flex items-center justify-center shrink-0 text-rose-600 font-black text-[9px]">
                                            PDF
                                        </div>
                                        <div class="min-w-0">
                                            <span class="text-xs font-bold text-slate-800 truncate block" x-text="suratFileName"></span>
                                            <span class="text-[10px] text-slate-400 font-mono" x-text="suratFileSize"></span>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-[#0E4D3C] bg-[#EAF5F0] px-2.5 py-0.5 rounded-full border border-[#CDE5D9] shrink-0">
                                        <svg class="w-3 h-3 text-[#0E4D3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <span>Terlampir</span>
                                    </span>
                                </div>
                                <div class="p-2.5 rounded-xl border border-amber-200 bg-amber-50/60 text-amber-800 text-xs" x-show="!suratFileName">
                                    Belum dilampirkan. Pastikan berkas surat telah diunggah di Langkah 3.
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Banner Informasi Verifikasi Jam Kerja -->
                <div class="mt-4 flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-[#EAF5F0] border border-[#CDE5D9] text-[#1E5546]">
                    <svg class="w-4 h-4 text-[#0E4D3C] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="text-xs leading-relaxed">
                        Pengajuan Anda akan ditinjau dan diverifikasi oleh Tim Pengelola Videotron UIN SSC di <strong>jam kerja</strong>.
                    </div>
                </div>

                <!-- Footer Card Langkah 4: Tombol Kirim Pengajuan Emas dengan Animasi Mikro Beranda -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <!-- Tombol Sebelumnya Mengikuti Gaya 'Lihat semua' di Beranda -->
                    <button type="button" 
                            @click="goToStep(3)" 
                            :disabled="isSubmitting"
                            class="group btn-wizard-secondary gap-1.5 disabled:opacity-50">
                        <svg class="w-3 h-3 arrow-icon-left text-[#0E4D3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        <span>Sebelumnya</span>
                    </button>

                    <button type="submit" 
                            :disabled="isSubmitting"
                            class="group btn-wizard-primary gap-2 disabled:opacity-50 disabled:cursor-wait">
                        <template x-if="!isSubmitting">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                                <span>Kirim Pengajuan</span>
                            </span>
                        </template>
                        <template x-if="isSubmitting">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="animate-spin h-3.5 w-3.5 text-[#0E4D3C]" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Mengirimkan...</span>
                            </span>
                        </template>
                    </button>
                </div>
            </div>

