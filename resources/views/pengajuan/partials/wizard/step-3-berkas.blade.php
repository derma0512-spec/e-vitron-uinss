            {{-- ==============================================================
                 LANGKAH 3 — INFORMASI KONTEN
                 ============================================================== --}}
            <div data-step="3" 
                 x-show="currentStep === 3" 
                 x-cloak 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1.5"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="relative overflow-hidden bg-white/95 backdrop-blur-md rounded-2xl sm:rounded-[22px] border border-white/90 shadow-sm p-3.5 sm:p-8 space-y-3.5 sm:space-y-6">
                
                {{-- Watermark ornamen di sudut form --}}
                <div class="ornament-pattern" style="position:absolute;right:-25px;bottom:-25px;height:120px;width:120px;opacity:0.12;pointer-events:none;"></div>

                <!-- Header Kartu Langkah 3 -->
                <div>
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-6 w-6 sm:h-7 sm:w-7 items-center justify-center rounded-full text-white shadow-xs shrink-0" style="background:#D9A93B;">
                            <svg class="h-3 w-3 sm:h-3.5 sm:w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </span>
                        <h2 class="text-sm sm:text-lg font-montserrat font-extrabold text-[#0E4D3C]">
                            <span class="sm:hidden">Detail Materi & Dokumen</span>
                            <span class="hidden sm:inline">Langkah 3 — Informasi Konten</span>
                        </h2>
                    </div>
                    <p class="text-[11px] sm:text-xs text-[#4E655F] mt-0.5 sm:mt-1 pl-8 sm:pl-9.5">Lengkapi rincian materi publikasi dan dokumen permohonan resmi sebelum dikirim.</p>
                </div>

                <!-- Struktur 2 Bagian Elegan (Materi Konten & Identitas/Dokumen) -->
                <div class="space-y-3.5 sm:space-y-5">
                    
                    <!-- ==============================================================
                         BAGIAN 1: MATERI PUBLIKASI (KONTEN)
                         ============================================================== -->
                    <div class="p-3.5 sm:p-5 rounded-xl sm:rounded-2xl bg-slate-50/70 border border-slate-200/80 space-y-3 sm:space-y-4">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-200/60">
                            <span class="flex h-5 w-5 items-center justify-center rounded-md bg-[#0E4D3C] text-white text-[10px] font-bold">1</span>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-[#0E4D3C]">Materi Publikasi</h3>
                        </div>

                        <!-- Baris 1: Judul Konten -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Judul Konten Kegiatan <span class="text-rose-500">*</span></label>
                            <input type="text" 
                                   name="title" 
                                   x-model="form.title" 
                                   placeholder="Contoh: Pengumuman Seminar Nasional & Expo Kampus 2026"
                                   required
                                   class="w-full px-3.5 py-2.5 bg-white border rounded-xl text-slate-800 text-xs sm:text-sm focus:outline-none focus:ring-1 focus:ring-[#0E4D3C] transition-all placeholder:text-slate-400"
                                   :class="formErrors.title ? 'border-rose-400 bg-rose-50/20' : 'border-slate-300 focus:border-[#0E4D3C]'">
                            <p x-show="formErrors.title" x-text="formErrors.title" class="text-[11px] text-rose-600 mt-1 font-medium"></p>
                        </div>

                        <!-- Baris 2: Jenis Konten (Segmented Button Tab) & Durasi Video -->
                        <div class="grid sm:grid-cols-2 gap-3 sm:gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Jenis Konten <span class="text-rose-500">*</span></label>
                                <input type="hidden" name="content_type" :value="form.contentType">
                                <div class="grid grid-cols-2 gap-1 p-1 bg-slate-100 rounded-xl border border-slate-200/80 select-none">
                                    <button type="button" 
                                            @click="form.contentType = 'video'; formErrors.contentType = ''"
                                            class="flex items-center justify-center gap-1.5 py-1.5 px-2 rounded-lg text-xs font-bold transition-all cursor-pointer whitespace-nowrap"
                                            :class="form.contentType === 'video' ? 'bg-white text-[#0E4D3C] shadow-xs border border-slate-200/90' : 'text-slate-500 hover:text-slate-800'">
                                        <svg class="w-3.5 h-3.5 text-[#0E4D3C] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        <span>Video</span>
                                    </button>
                                    <button type="button" 
                                            @click="form.contentType = 'poster'; formErrors.contentType = ''"
                                            class="flex items-center justify-center gap-1.5 py-1.5 px-2 rounded-lg text-xs font-bold transition-all cursor-pointer whitespace-nowrap"
                                            :class="form.contentType === 'poster' ? 'bg-white text-[#0E4D3C] shadow-xs border border-slate-200/90' : 'text-slate-500 hover:text-slate-800'">
                                        <svg class="w-3.5 h-3.5 text-[#0E4D3C] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>Poster</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Durasi Video (Pilihan Detik atau Menit - Hanya tampil saat Video dipilih) -->
                            <div x-show="form.contentType === 'video'" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-cloak>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="block text-xs font-semibold text-slate-700">Durasi Video <span class="text-rose-500">*</span></label>
                                    <span class="text-[11px] font-semibold text-slate-400" 
                                          x-text="form.durationUnit === 'menit' ? 'Maks. 3 Menit' : 'Maks. 180 Detik'"></span>
                                </div>
                                <div class="relative flex items-center bg-white border rounded-xl transition-all focus-within:ring-1 focus-within:ring-[#0E4D3C] focus-within:border-[#0E4D3C]"
                                     :class="formErrors.videoDuration ? 'border-rose-400 bg-rose-50/20' : 'border-slate-300'">
                                    <input type="number" 
                                           :step="form.durationUnit === 'menit' ? '0.25' : '1'"
                                           :min="form.durationUnit === 'menit' ? '0.25' : '5'" 
                                           :max="form.durationUnit === 'menit' ? '3' : '180'"
                                           x-model="form.durationValue" 
                                           @input="updateDurationFromValue()"
                                           :placeholder="form.durationUnit === 'menit' ? 'Contoh: 1 atau 3' : 'Contoh: 30 atau 60'"
                                           class="w-full pl-3.5 pr-2 py-2 bg-transparent text-slate-800 text-xs sm:text-sm focus:outline-none font-semibold placeholder:text-slate-400 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                           
                                    <!-- Toggle Pilihan Satuan: Detik / Menit -->
                                    <div class="flex items-center p-0.5 bg-slate-100 rounded-lg mr-2 border border-slate-200/80 shrink-0 select-none">
                                        <button type="button" 
                                                @click="setDurationUnit('detik')" 
                                                class="px-2.5 py-1 rounded-md text-[11px] font-bold transition-all cursor-pointer" 
                                                :class="form.durationUnit === 'detik' ? 'bg-[#0E4D3C] text-white shadow-xs' : 'text-slate-500 hover:text-slate-800'">
                                            Detik
                                        </button>
                                        <button type="button" 
                                                @click="setDurationUnit('menit')" 
                                                class="px-2.5 py-1 rounded-md text-[11px] font-bold transition-all cursor-pointer" 
                                                :class="form.durationUnit === 'menit' ? 'bg-[#0E4D3C] text-white shadow-xs' : 'text-slate-500 hover:text-slate-800'">
                                            Menit
                                        </button>
                                    </div>
                                </div>
                                <!-- Input tersembunyi yang mengirim detik ke backend -->
                                <input type="hidden" name="video_duration_seconds" :value="form.videoDurationSeconds">
                                <p x-show="formErrors.videoDuration" x-text="formErrors.videoDuration" class="text-[11px] text-rose-600 mt-1 font-medium"></p>
                            </div>
                        </div>

                        <!-- Panduan Resolusi Otomatis (Smart Spec Guide Sesuai Videotron Terpilih) -->
                        <div x-show="selectedVideotronDetails.length > 0" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="p-3 sm:p-3.5 rounded-xl bg-gradient-to-r from-emerald-50/90 via-teal-50/50 to-emerald-50/80 border border-emerald-200/80 shadow-2xs space-y-2">
                            <!-- Header: Judul & Badge Orientasi (Desktop Utuh 100%, Mobile Tetap 1 Baris Rapi) -->
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-1.5 text-xs font-bold text-[#0E4D3C] min-w-0">
                                    <svg class="w-3.5 h-3.5 text-[#0E4D3C] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="hidden sm:inline">Panduan Ukuran Kanvas Layar:</span>
                                    <span class="sm:hidden truncate">Resolusi Kanvas Layar:</span>
                                </div>
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-800 bg-white px-2 py-0.5 rounded-full border border-emerald-200 shadow-2xs shrink-0">
                                    <svg x-show="selectedOrientationSummary.includes('Landscape')" class="w-2.5 h-2.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <rect x="3" y="6" width="18" height="12" rx="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <svg x-show="selectedOrientationSummary.includes('Portrait')" class="w-2.5 h-2.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <rect x="6" y="3" width="12" height="18" rx="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <span class="hidden sm:inline" x-text="selectedOrientationSummary"></span>
                                    <span class="sm:hidden" x-text="selectedOrientationSummary.includes('Landscape') ? 'Landscape' : (selectedOrientationSummary.includes('Portrait') ? 'Portrait' : selectedOrientationSummary)"></span>
                                </span>
                            </div>

                            <!-- Versi Desktop: 100% PERSIS KODE ASLI SEBELUMNYA TANPA DIUBAH (hidden sm:flex) -->
                            <div class="hidden sm:flex flex-wrap items-center gap-2">
                                <template x-for="item in selectedVideotronDetails" :key="item.id">
                                    <div class="inline-flex items-center gap-2 py-1 px-2.5 rounded-lg bg-white border border-emerald-200/80 text-xs shadow-2xs">
                                        <span class="font-medium text-slate-800" x-text="item.name"></span>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <span class="font-mono font-bold text-slate-900 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200 text-[11px]" x-text="`${item.resolution} px`"></span>
                                            <span class="text-[10.5px] font-medium text-slate-500 capitalize" x-text="`(${item.orientation})`"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Versi Mobile: Spec Sheet Bersih, Rapi, & Elegan Tanpa Kotak Tebal (sm:hidden) -->
                            <div class="sm:hidden rounded-lg bg-white/80 border border-emerald-200/70 divide-y divide-emerald-100/90 overflow-hidden shadow-2xs">
                                <template x-for="item in selectedVideotronDetails" :key="item.id">
                                    <div class="flex items-center justify-between py-1.5 px-2.5 text-xs">
                                        <div class="flex items-center gap-1.5 min-w-0 pr-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 shrink-0"></span>
                                            <span class="font-semibold text-slate-700 text-[11px] truncate" x-text="item.name"></span>
                                        </div>
                                        <span class="font-mono font-bold text-[#0E4D3C] text-[11px] shrink-0" x-text="`${item.resolution} px`"></span>
                                    </div>
                                </template>
                            </div>

                            <div class="flex items-start gap-1.5 text-[10.5px] sm:text-[11px] text-[#2C6252] leading-snug pt-0.5">
                                <svg class="w-3.5 h-3.5 text-amber-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
                                </svg>
                                <!-- Desktop: Teks lengkap asli 100% -->
                                <p class="min-w-0 flex-1 hidden sm:block">
                                    <strong class="font-semibold text-slate-800">Perhatian:</strong> Setiap videotron memiliki rasio dan orientasi fisik (Landscape/Portrait) yang berbeda. Di <strong>Canva, CapCut, Photoshop, atau Premiere</strong>, buat ukuran kanvas sesuai resolusi di atas agar materi tidak <strong>terpotong (crop)</strong> atau gepeng di layar.
                                </p>
                                <!-- Mobile: Teks ringkas to the point 2 baris -->
                                <p class="min-w-0 flex-1 sm:hidden text-[10.5px] text-[#2C6252]">
                                    <strong class="font-semibold text-slate-800">Catatan:</strong> Buat kanvas di Canva/Premiere sesuai resolusi di atas agar video tidak <strong>terpotong</strong> atau gepeng di layar.
                                </p>
                            </div>
                        </div>

                        <!-- Baris 3: Link Google Drive -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Link Google Drive (Bahan Tayang) <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <input type="url" 
                                       name="gdrive_link" 
                                       x-model="form.gdriveLink" 
                                       @input="validateGdriveLink()"
                                       @blur="validateGdriveLink()"
                                       required 
                                       placeholder="https://drive.google.com/file/d/... atau https://drive.google.com/drive/folders/..."
                                       class="w-full pl-10 pr-3.5 py-2.5 bg-white border rounded-xl text-slate-800 text-xs sm:text-sm focus:outline-none focus:ring-1 focus:ring-[#0E4D3C] transition-all font-mono placeholder:text-slate-400 placeholder:text-xs"
                                       :class="formErrors.gdriveLink ? 'border-rose-400 bg-rose-50/20' : 'border-slate-300 focus:border-[#0E4D3C]'">
                                <!-- Ikon Resmi Google Drive Berwarna Asli -->
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none flex items-center">
                                    <svg class="w-4 h-4 shrink-0 drop-shadow-xs" viewBox="0 0 87.3 78" xmlns="http://www.w3.org/2000/svg">
                                        <path d="m6.6 66.85 3.85 6.65c.8 1.4 1.95 2.5 3.3 3.3l13.75-23.8h-27.5c0 1.55.4 3.1 1.2 4.5z" fill="#0066da"/>
                                        <path d="m43.65 25-13.75-23.8c-1.35.8-2.5 1.9-3.3 3.3l-25.4 44a9.06 9.06 0 0 0 -1.2 4.5h27.5z" fill="#00ac47"/>
                                        <path d="m73.55 76.8c1.35-.8 2.5-1.9 3.3-3.3l1.6-2.75 7.65-13.25c.8-1.4 1.2-2.95 1.2-4.5h-27.502l5.852 11.5z" fill="#ea4335"/>
                                        <path d="m43.65 25 13.75-23.8c-1.35-.8-2.9-1.2-4.5-1.2h-18.5c-1.6 0-3.15.45-4.5 1.2z" fill="#00832d"/>
                                        <path d="m59.8 53h-32.3l-13.75 23.8c1.35.8 2.9 1.2 4.5 1.2h50.8c1.6 0 3.15-.45 4.5-1.2z" fill="#2684fc"/>
                                        <path d="m73.4 26.5-12.7-22c-.8-1.4-1.95-2.5-3.3-3.3l-13.75 23.8 16.15 28h27.45c0-1.55-.4-3.1-1.2-4.5z" fill="#ffba00"/>
                                    </svg>
                                </div>
                            </div>
                            <!-- Petunjuk Izin Akses Dwibahasa (Indo & English) -->
                            <div class="mt-1 flex items-center gap-1.5 text-[10.5px] sm:text-[11px] text-[#1E5546]">
                                <svg class="w-3.5 h-3.5 text-[#0E4D3C] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Akses link wajib: <strong>Siapa saja yang memiliki link</strong> &bull; Maks. {{ setting('max_file_size_mb', 50) }} MB</span>
                            </div>
                            <p x-show="formErrors.gdriveLink" x-text="formErrors.gdriveLink" class="text-[11px] text-rose-600 mt-1 font-medium"></p>
                        </div>

                        <!-- Baris 4: Deskripsi Konten -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-semibold text-slate-700">Deskripsi Singkat Konten <span class="text-rose-500">*</span></label>
                                <span class="text-[11px] text-slate-400" x-text="`${form.description.length}/500`"></span>
                            </div>
                            <textarea name="description" 
                                      x-model="form.description" 
                                      rows="2" 
                                      maxlength="500"
                                      required 
                                      placeholder="Jelaskan secara singkat materi kegiatan yang akan ditayangkan..."
                                      class="w-full px-3.5 py-2.5 bg-white border rounded-xl text-slate-800 text-xs sm:text-sm focus:outline-none focus:ring-1 focus:ring-[#0E4D3C] transition-all leading-relaxed placeholder:text-slate-400"
                                      :class="formErrors.description ? 'border-rose-400 bg-rose-50/20' : 'border-slate-300 focus:border-[#0E4D3C]'"></textarea>
                            <p x-show="formErrors.description" x-text="formErrors.description" class="text-[11px] text-rose-600 mt-1 font-medium"></p>
                        </div>
                    </div>


                    <!-- ==============================================================
                         BAGIAN 2: PENANGGUNG JAWAB & DOKUMEN RESMI
                         ============================================================== -->
                    <div class="p-3.5 sm:p-5 rounded-xl sm:rounded-2xl bg-slate-50/70 border border-slate-200/80 space-y-3 sm:space-y-4">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-200/60">
                            <span class="flex h-5 w-5 items-center justify-center rounded-md bg-[#0E4D3C] text-white text-[10px] font-bold">2</span>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-[#0E4D3C]">Penanggung Jawab & Dokumen Resmi</h3>
                        </div>

                        <!-- Baris 1: PIC & WhatsApp (Grid 2 Kolom) -->
                        <div class="grid sm:grid-cols-2 gap-3 sm:gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Penanggung Jawab (PIC) <span class="text-rose-500">*</span></label>
                                <input type="text" 
                                       name="pic_name" 
                                       x-model="form.picName" 
                                       required 
                                       placeholder="Nama lengkap pemohon"
                                       class="w-full px-3.5 py-2.5 bg-white border rounded-xl text-slate-800 text-xs sm:text-sm focus:outline-none focus:ring-1 focus:ring-[#0E4D3C] transition-all placeholder:text-slate-400 font-medium"
                                       :class="formErrors.picName ? 'border-rose-400 bg-rose-50/20' : 'border-slate-300 focus:border-[#0E4D3C]'">
                                <p x-show="formErrors.picName" x-text="formErrors.picName" class="text-[11px] text-rose-600 mt-1 font-medium"></p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nomor WhatsApp <span class="text-rose-500">*</span></label>
                                <input type="tel" 
                                       name="whatsapp" 
                                       x-model="form.whatsapp" 
                                       required 
                                       placeholder="Contoh: 08123456789"
                                       class="w-full px-3.5 py-2.5 bg-white border rounded-xl text-slate-800 text-xs sm:text-sm focus:outline-none focus:ring-1 focus:ring-[#0E4D3C] transition-all placeholder:text-slate-400 font-medium"
                                       :class="formErrors.whatsapp ? 'border-rose-400 bg-rose-50/20' : 'border-slate-300 focus:border-[#0E4D3C]'">
                                <p x-show="formErrors.whatsapp" x-text="formErrors.whatsapp" class="text-[11px] text-rose-600 mt-1 font-medium"></p>
                            </div>
                        </div>

                        <!-- Baris 2: Unit / Organisasi -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Unit / Organisasi Pengaju</label>
                            <input type="text" 
                                   name="organization" 
                                   x-model="form.organization" 
                                   placeholder="Contoh: HMJ Informatika / BEM Fakultas / Unit Kegiatan Mahasiswa"
                                   class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-800 text-xs sm:text-sm focus:outline-none focus:border-[#0E4D3C] focus:ring-1 focus:ring-[#0E4D3C] transition-all placeholder:text-slate-400 font-medium">
                        </div>

                        <!-- Baris 3: Surat Permohonan Resmi (Dropzone Elegan Penuh) -->
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <label class="block text-xs font-semibold text-slate-700 whitespace-nowrap">
                                    <span>Surat Permohonan (PDF)</span>
                                    <span class="text-rose-500">*</span>
                                </label>
                                @if(setting('template_doc_url'))
                                    @php $tplDocUrl = (string) setting('template_doc_url'); @endphp
                                    <a href="{{ $tplDocUrl }}"
                                       @if(str_ends_with(strtolower($tplDocUrl), '.docx')) download="Template-Surat-Permohonan-Videotron-UINSSC.docx" @else target="_blank" rel="noopener noreferrer" @endif
                                       class="inline-flex items-center gap-1 text-[10.5px] font-bold text-[#0E4D3C] hover:text-[#0a372b] bg-[#EAF5F0] hover:bg-[#d8ece2] px-2 py-0.5 rounded-md border border-[#CDE5D9] shrink-0 whitespace-nowrap transition-all shadow-2xs">
                                        <svg class="w-3 h-3 text-[#0E4D3C] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        <span>Template (.docx)</span>
                                    </a>
                                @endif
                            </div>

                            <div class="relative">
                                <!-- Dropzone Area saat belum ada file -->
                                <div x-show="!suratFileName"
                                     @click="$refs.suratInput.click()"
                                     @dragover.prevent="isDraggingSurat = true"
                                     @dragleave.prevent="isDraggingSurat = false"
                                     @drop.prevent="handleFileDrop($event)"
                                     class="border-2 border-dashed rounded-xl p-3 sm:p-4 text-center cursor-pointer transition-all flex items-center justify-center gap-3 bg-white hover:bg-[#EAF5F0]/40"
                                     :class="[
                                         isDraggingSurat ? 'border-[#0E4D3C] bg-[#EAF5F0]/70 scale-[0.99]' : 'hover:border-[#0E4D3C]',
                                         formErrors.surat_peminjaman ? 'border-rose-400 bg-rose-50/20' : 'border-slate-300'
                                     ]">
                                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-rose-50 text-rose-600 border border-rose-200/70 flex items-center justify-center shrink-0 shadow-2xs">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div class="text-left min-w-0">
                                        <span class="text-xs sm:text-sm font-bold text-slate-700 block">Upload Surat Permohonan (PDF)</span>
                                        <p class="text-[10.5px] sm:text-[11px] text-slate-400 mt-0.5">PDF bertanda tangan pimpinan &bull; Maks. 2 MB</p>
                                    </div>
                                </div>

                                <!-- Card File Terpilih (Executive White Card Serasi Langkah 4) -->
                                <div x-show="suratFileName" x-cloak
                                     class="p-2.5 sm:p-3.5 rounded-xl border border-slate-200/90 bg-white shadow-2xs flex items-center justify-between gap-2.5 sm:gap-3 transition-all hover:border-slate-300">
                                    <div class="flex items-center gap-2.5 sm:gap-3 min-w-0 flex-1">
                                        <!-- PDF Badge Merah Elegan -->
                                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-rose-50 border border-rose-100 flex flex-col items-center justify-center shrink-0 text-rose-600 shadow-2xs">
                                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span class="hidden sm:inline font-black text-[8px] uppercase tracking-wider leading-none mt-0.5">PDF</span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs sm:text-sm font-bold text-slate-800 truncate" :title="suratFileName" x-text="suratFileName"></p>
                                            <div class="flex items-center gap-1.5 text-[10.5px] sm:text-[11px] text-slate-500 mt-0.5 truncate">
                                                <span class="font-mono text-slate-400" x-text="suratFileSize"></span>
                                                <span class="text-slate-300">&bull;</span>
                                                <span class="inline-flex items-center gap-1 font-semibold text-emerald-700">
                                                    <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Siap diunggah</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1 sm:gap-2 shrink-0">
                                        <!-- Tombol Ganti File (Icon-only di mobile, dengan teks di desktop) -->
                                        <button type="button" 
                                                @click="$refs.suratInput.click()"
                                                class="h-8 px-2 sm:px-3 rounded-lg text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 hover:text-[#0E4D3C] hover:border-[#0E4D3C]/40 border border-slate-200 shadow-2xs transition-all flex items-center justify-center gap-1.5 cursor-pointer"
                                                title="Ganti berkas">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            <span class="hidden sm:inline">Ganti File</span>
                                        </button>
                                        <!-- Tombol Hapus Berkas -->
                                        <button type="button" 
                                                @click="removeSuratFile()"
                                                class="h-8 w-8 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-200 border border-slate-200/80 transition-all flex items-center justify-center cursor-pointer shadow-2xs"
                                                title="Hapus berkas">
                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </div>

                                <input type="file" 
                                       x-ref="suratInput"
                                       name="surat_peminjaman" 
                                       accept=".pdf,application/pdf"
                                       @change="handleFileSelect($event)"
                                       class="hidden">
                            </div>
                            <p x-show="formErrors.surat_peminjaman" x-text="formErrors.surat_peminjaman" class="text-[11px] text-rose-600 mt-1.5 font-medium"></p>
                        </div>
                    </div>
                </div>

                <!-- Footer Card Langkah 3 dengan Animasi Mikro Beranda -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <!-- Tombol Sebelumnya Mengikuti Gaya 'Lihat semua' di Beranda -->
                    <button type="button" 
                            @click="goToStep(2)" 
                            class="group btn-wizard-secondary gap-1.5">
                        <svg class="w-3 h-3 arrow-icon-left text-[#0E4D3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        <span>Sebelumnya</span>
                    </button>

                    <button type="button" 
                            @click="validateAndGoStep4()"
                            class="group btn-wizard-primary gap-1.5">
                        <span>Lanjutkan</span>
                        <svg class="w-3.5 h-3.5 arrow-icon-right" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

