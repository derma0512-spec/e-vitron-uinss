            
            {{-- ==============================================================
                 POP-UP BRIEFING: KETENTUAN PENGAJUAN (REFINED 2-COLUMN 4-CARDS)
                 Layout 2 Kolom Seimbang, Ikon Ringkas Inline, Badge Netral,
                 Footer 3 Area Sejajar, dan 100% Full-Screen Dimming
                 ============================================================== --}}
            <template x-teleport="body">
                <div x-show="showNoticeModal" 
                     x-cloak 
                     @keydown.escape.window.prevent="if (showNoticeModal && !isConfirmingNotice) closeNoticeModal()"
                     @keydown.enter.window.prevent="if (showNoticeModal && !isConfirmingNotice) confirmNoticeModal()"
                     @click.self="closeNoticeModal()"
                     @wheel="onModalWheel($event)"
                     @touchmove="onModalTouchMove($event)"
                     class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 overflow-y-auto cursor-pointer"
                     style="overscroll-behavior: contain !important;"
                     role="dialog" 
                     aria-modal="true" 
                     aria-labelledby="modal-notice-title">
                    
                    {{-- Backdrop Gelap 100% Menyeluruh Menutupi Seluruh Layar (Termasuk Navbar) --}}
                    <div x-show="showNoticeModal"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         @click="closeNoticeModal()"
                         @wheel.prevent
                         @touchmove.prevent
                         class="fixed inset-0 bg-black/60 cursor-pointer"
                         style="backdrop-filter: none !important; -webkit-backdrop-filter: none !important;"></div>

                    {{-- Modal Card (max-w-2xl / ~640px: Proporsional 2 Kolom, Kompak, & Elegan) --}}
                    <div x-show="showNoticeModal"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         @click.stop
                         class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-slate-200/90 overflow-hidden z-10 my-auto flex flex-col max-h-[90vh] cursor-default">

                        <!-- Header Modal Kompak & Profesional (Optimized Mobile) -->
                        <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-slate-100 flex items-center justify-between gap-3 shrink-0 bg-white">
                            <div class="flex items-center gap-3">
                                <span class="flex h-8 w-8 sm:h-9 sm:w-9 items-center justify-center rounded-xl bg-[#E3F1EA] text-[#0E4D3C] border border-[#0E4D3C]/15 shadow-2xs shrink-0">
                                    <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5 text-[#0E4D3C] shrink-0" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                                    </svg>
                                </span>
                                <div>
                                    <h3 id="modal-notice-title" class="text-xs sm:text-base font-bold text-slate-900 font-montserrat tracking-tight leading-snug">
                                        Ketentuan Pengajuan Penayangan
                                    </h3>
                                    <p class="text-[10.5px] sm:text-xs text-slate-500 mt-0.5 leading-tight">
                                        Pahami 4 ketentuan pokok sebelum mengisi formulir.
                                    </p>
                                </div>
                            </div>

                            <!-- Tombol Tutup X Lingkaran Bulat Halus -->
                            <button type="button" 
                                    @click="closeNoticeModal()" 
                                    aria-label="Tutup ketentuan pengajuan"
                                    title="Tutup"
                                    class="flex h-7.5 w-7.5 sm:h-8 sm:w-8 items-center justify-center rounded-full border border-slate-200/80 bg-slate-50/80 text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0E4D3C]/30">
                                <svg class="w-4 h-4 shrink-0" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Body Modal: 4 Card Dua Kolom Proporsional & Rapi (Dengan Micro-Interaction Halus & Warna Pastel Fungsional) -->
                        <div class="p-3.5 sm:p-5 overflow-y-auto flex-1">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3.5">
                                {{-- 1. Batas Waktu --}}
                                <div class="group p-3 sm:p-4 rounded-xl border border-slate-200/90 bg-slate-50/50 hover:bg-white hover:border-[#0E4D3C]/35 hover:-translate-y-0.5 hover:shadow-xs transition-all duration-200 ease-out cursor-default flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="flex items-center gap-2 text-xs sm:text-[13px] font-bold text-slate-800">
                                                <svg class="w-4 h-4 text-amber-600 shrink-0 transition-transform duration-200 group-hover:scale-110" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
                                                </svg>
                                                <span>Batas Waktu</span>
                                            </div>
                                            <span class="px-2 py-0.5 rounded-md text-[10.5px] font-semibold bg-amber-50 text-amber-800 border border-amber-200/60 shrink-0 transition-colors">
                                                Minimal H-{{ $minDaysBefore }}
                                            </span>
                                        </div>
                                        <p class="text-[11px] sm:text-xs text-slate-600 mt-1.5 sm:mt-2 leading-snug sm:leading-relaxed">
                                            Formulir dikirim paling lambat <strong class="text-slate-800 font-semibold">{{ $minDaysBefore }} hari kerja</strong> sebelum tanggal mulai penayangan. Kurasi materi oleh Tim Admin diproses pada hari dan jam kerja dinas kampus.
                                        </p>
                                    </div>
                                </div>

                                {{-- 2. Surat Pengantar --}}
                                <div class="group p-3 sm:p-4 rounded-xl border border-slate-200/90 bg-slate-50/50 hover:bg-white hover:border-[#0E4D3C]/35 hover:-translate-y-0.5 hover:shadow-xs transition-all duration-200 ease-out cursor-default flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="flex items-center gap-2 text-xs sm:text-[13px] font-bold text-slate-800">
                                                <svg class="w-4 h-4 text-blue-600 shrink-0 transition-transform duration-200 group-hover:scale-110" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                                </svg>
                                                <span>Surat Pengantar</span>
                                            </div>
                                            <span class="px-2 py-0.5 rounded-md text-[10.5px] font-semibold bg-blue-50 text-blue-700 border border-blue-200/60 shrink-0 transition-colors">
                                                Format PDF
                                            </span>
                                        </div>
                                        <p class="text-[11px] sm:text-xs text-slate-600 mt-1.5 sm:mt-2 leading-snug sm:leading-relaxed">
                                            Lampirkan surat permohonan resmi bertanda tangan pimpinan Ormawa / Unit / Fakultas (PDF, maks 2MB).
                                        </p>
                                    </div>
                                    @if($templateDocUrl)
                                        <div class="pt-1.5 sm:pt-2 mt-auto">
                                            <a href="{{ $templateDocUrl }}" @if(str_ends_with(strtolower((string) $templateDocUrl), '.docx')) download="Template-Surat-Permohonan-Videotron-UINSSC.docx" @else target="_blank" rel="noopener noreferrer" @endif 
                                               class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#0E4D3C] hover:text-[#083529] hover:underline cursor-pointer group/link">
                                                <span>Unduh Format Surat Resmi</span>
                                                <svg class="w-3 h-3 text-[#0E4D3C] shrink-0 transition-transform duration-150 group-hover/link:translate-x-0.5" width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                {{-- 3. Format Konten --}}
                                <div class="group p-3 sm:p-4 rounded-xl border border-slate-200/90 bg-slate-50/50 hover:bg-white hover:border-[#0E4D3C]/35 hover:-translate-y-0.5 hover:shadow-xs transition-all duration-200 ease-out cursor-default flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="flex items-center gap-2 text-xs sm:text-[13px] font-bold text-slate-800">
                                                <svg class="w-4 h-4 text-indigo-600 shrink-0 transition-transform duration-200 group-hover:scale-110" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9A2.25 2.25 0 0013.5 5.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/>
                                                </svg>
                                                <span>Format Konten</span>
                                            </div>
                                            <span class="px-2 py-0.5 rounded-md text-[10.5px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200/60 shrink-0 transition-colors">
                                                Sesuai Layar (Maks {{ $maxFileSize }}MB)
                                            </span>
                                        </div>
                                        <p class="text-[11px] sm:text-xs text-slate-600 mt-1.5 sm:mt-2 leading-snug sm:leading-relaxed">
                                            <strong class="text-slate-800 font-semibold">Wajib sesuai orientasi layar tujuan:</strong> Video MP4 (durasi maks <strong class="text-slate-800 font-semibold">{{ $durationText }}</strong>, <strong class="text-slate-800 font-semibold">tanpa audio</strong>) atau Poster JPG/PNG. Format materi wajib mengikuti orientasi (Landscape mendatar atau Portrait tegak) dan resolusi videotron yang dipilih agar tidak terpotong atau gepeng.
                                        </p>
                                    </div>
                                </div>

                                {{-- 4. Etika Publikasi --}}
                                <div class="group p-3 sm:p-4 rounded-xl border border-slate-200/90 bg-slate-50/50 hover:bg-white hover:border-[#0E4D3C]/35 hover:-translate-y-0.5 hover:shadow-xs transition-all duration-200 ease-out cursor-default flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="flex items-center gap-2 text-xs sm:text-[13px] font-bold text-slate-800">
                                                <svg class="w-4 h-4 text-emerald-600 shrink-0 transition-transform duration-200 group-hover:scale-110" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                                                </svg>
                                                <span>Etika Publikasi</span>
                                            </div>
                                            <span class="px-2 py-0.5 rounded-md text-[10.5px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60 shrink-0 transition-colors">
                                                Norma Kampus
                                            </span>
                                        </div>
                                        <p class="text-[11px] sm:text-xs text-slate-600 mt-1.5 sm:mt-2 leading-snug sm:leading-relaxed">
                                            Materi kegiatan resmi kampus, tidak memuat unsur provokatif/SARA, politik praktis, atau iklan komersial luar.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Modal Aksi: 3 Area Sejajar & Proporsional di Desktop -->
                        <div class="px-4 py-3 sm:px-6 sm:py-3.5 bg-slate-50/90 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 sm:gap-3 shrink-0">
                            {{-- Area 1: Checkbox --}}
                            <label class="inline-flex items-center gap-2 cursor-pointer select-none text-[11px] sm:text-xs text-slate-600 hover:text-slate-900 font-medium whitespace-nowrap shrink-0">
                                <input type="checkbox" 
                                       x-model="dontShowAgainToday" 
                                       class="w-3.5 h-3.5 rounded border-slate-300 text-[#0E4D3C] focus:ring-[#0E4D3C] cursor-pointer">
                                <span>Jangan tampilkan lagi hari ini</span>
                            </label>

                            {{-- Tombol Aksi: Sejajar 2 Tombol di Mobile & Alami di Desktop --}}
                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                {{-- Area 2: Tombol Panduan Sekunder --}}
                                <a href="{{ route('panduan') }}" target="_blank" rel="noopener noreferrer"
                                   class="group w-1/3 sm:w-auto px-3 py-2 sm:px-4 sm:py-2.5 rounded-xl border border-slate-200/90 bg-white hover:bg-slate-50 text-slate-700 hover:text-[#0E4D3C] hover:border-[#0E4D3C]/35 hover:shadow-2xs transition-all inline-flex items-center justify-center gap-1.5 text-xs font-semibold whitespace-nowrap shrink-0 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0E4D3C]/30">
                                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-[#0E4D3C] transition-colors shrink-0 hidden xs:inline-block" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                                    </svg>
                                    <span class="sm:hidden">Panduan</span>
                                    <span class="hidden sm:inline">Baca Panduan Lengkap</span>
                                    <svg class="w-3 h-3 text-slate-400 group-hover:text-[#0E4D3C] group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all shrink-0" width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                                    </svg>
                                </a>

                                {{-- Area 3: Tombol Utama --}}
                                <button type="button"
                                        @click="confirmNoticeModal()"
                                        :disabled="isConfirmingNotice"
                                        class="flex-1 sm:flex-none px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl text-white font-bold text-xs flex items-center justify-center gap-2 shadow-xs hover:shadow-md transition-all cursor-pointer group shrink-0 whitespace-nowrap disabled:opacity-85 disabled:cursor-wait focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0E4D3C]/40 focus-visible:ring-offset-2"
                                        style="background:#0E4D3C;">
                                    <template x-if="!isConfirmingNotice">
                                        <span class="inline-flex items-center gap-1.5">
                                            <span>Mengerti, Lanjutkan</span>
                                            <svg class="w-3.5 h-3.5 shrink-0 group-hover:translate-x-0.5 transition-transform" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                                            </svg>
                                        </span>
                                    </template>
                                    <template x-if="isConfirmingNotice">
                                        <span class="inline-flex items-center gap-2">
                                            <svg class="animate-spin h-3.5 w-3.5 text-white shrink-0" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span>Menyiapkan...</span>
                                        </span>
                                    </template>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            {{-- ==============================================================
                 LANGKAH 1 — PILIH VIDEOTRON
                 (6 Videotron: Masjid, Rektorat, Syariah, FITK, Siber, FEBI)
                 ============================================================== --}}
            <div data-step="1" 
                 x-show="currentStep === 1" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1.5"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="relative overflow-hidden bg-white/95 backdrop-blur-md rounded-2xl sm:rounded-[22px] border border-white/90 shadow-sm p-3.5 sm:p-7 space-y-3.5 sm:space-y-5">
                
                {{-- Watermark ornamen di sudut form --}}
                <div class="ornament-pattern" style="position:absolute;right:-25px;bottom:-25px;height:120px;width:120px;opacity:0.12;pointer-events:none;"></div>

                                <!-- Header Kartu Langkah 1 -->
                <div>
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-6 w-6 sm:h-7 sm:w-7 items-center justify-center rounded-full text-white shadow-xs shrink-0" style="background:#D9A93B;">
                            <svg class="h-3 w-3 sm:h-3.5 sm:w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <h2 class="text-sm sm:text-lg font-montserrat font-extrabold text-[#0E4D3C]">
                            <span class="sm:hidden">Pilih Lokasi Videotron</span>
                            <span class="hidden sm:inline">Langkah 1 — Pilih Videotron</span>
                        </h2>
                    </div>
                    <p class="text-[11px] sm:text-xs text-[#4E655F] mt-0.5 sm:mt-1 pl-8 sm:pl-9.5">Pilih satu atau beberapa perangkat yang tersedia untuk penayangan konten kamu.</p>
                </div>

                

                {{-- Alert Frontend Step 1 jika belum memilih --}}
                <div x-show="step1Error" x-cloak class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span x-text="step1Error"></span>
                </div>

                <!-- Grid Kartu Videotron (3 Kolom Desktop Ramping & Sejajar) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5">
                    @forelse($videotrons as $v)
                        @php
                            $isInactive = ! $v->is_active;
                            $isGangguan = $v->condition === 'gangguan';
                            $isPerbaikan = $v->condition === 'dalam_perbaikan';
                            $isUnavailable = $isInactive || $isGangguan || $isPerbaikan;
                            $thumbImage = $v->image_url;
                        @endphp

                        <label class="group relative flex flex-col justify-between p-2.5 sm:p-3 rounded-xl sm:rounded-2xl border transition-all duration-200 select-none {{ $isUnavailable ? 'opacity-65 grayscale-[25%] bg-slate-50/90 border-slate-200 cursor-not-allowed' : 'cursor-pointer hover:border-slate-300 hover:shadow-md hover:-translate-y-0.5' }}"
                               :class="{
                                   'border-[#D9A93B] bg-white ring-2 ring-[#D9A93B]/30 shadow-md shadow-[#D9A93B]/10': isVideotronSelected('{{ $v->id }}') && !{{ $isUnavailable ? 'true' : 'false' }},
                                   'border-slate-200/90 bg-white/95': !isVideotronSelected('{{ $v->id }}') && !{{ $isUnavailable ? 'true' : 'false' }}
                               }">
                            
                            <input type="checkbox" 
                                   name="videotrons[]" 
                                   value="{{ $v->id }}"
                                   x-model="selectedVideotrons" 
                                   @change="step1Error = '';"
                                   {{ $isUnavailable ? 'disabled' : '' }}
                                   class="sr-only">

                            {{-- Bagian Atas: Thumbnail + Informasi Detail --}}
                            <div class="flex items-start gap-2.5 sm:gap-3">
                                <!-- Thumbnail Gambar Videotron -->
                                <div class="relative w-16 h-16 sm:w-20 sm:h-20 rounded-lg sm:rounded-xl overflow-hidden shrink-0 bg-slate-100 border border-slate-200/80 shadow-2xs mt-0.5">
                                    <img src="{{ $thumbImage }}" 
                                         alt="{{ $v->name }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @if($isUnavailable)
                                        <div class="absolute inset-0 bg-slate-900/10 backdrop-blur-[0.5px]"></div>
                                    @endif
                                </div>

                                <!-- Detail Videotron -->
                                <div class="flex-1 min-w-0">
                                    <!-- Baris 1: Judul Videotron + Indikator Aksi/Gembok -->
                                    <div class="flex items-start justify-between gap-1.5">
                                        <h3 class="font-bold text-[13px] sm:text-[14px] leading-snug truncate"
                                            title="{{ $v->name }}"
                                            :class="isVideotronSelected('{{ $v->id }}') ? 'text-[#C9901A]' : 'text-[#0E4D3C] md:group-hover:text-[#C9901A]'">
                                            {{ $v->name }}
                                        </h3>

                                        <!-- Aksi Kanan: Checkbox bundar jika aktif, Gembok jika nonaktif/rusak -->
                                        @if($isUnavailable)
                                            <div class="w-5 h-5 rounded-full bg-slate-100 border border-slate-300 flex items-center justify-center text-slate-400 shrink-0 shadow-2xs" 
                                                 title="Perangkat tidak dapat dipilih sementara">
                                                <svg class="w-2.5 h-2.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-5 h-5 rounded-full border flex items-center justify-center transition-all duration-200 shrink-0"
                                                 :class="isVideotronSelected('{{ $v->id }}') ? 'bg-[#D9A93B] border-[#D9A93B] text-white shadow-2xs scale-105' : 'border-slate-300 bg-white group-hover:border-slate-400'">
                                                <svg x-show="isVideotronSelected('{{ $v->id }}')" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Baris 2: Lokasi dengan Icon Pin -->
                                    <div class="mt-0.5 flex items-center gap-1 text-[10.5px] sm:text-[11.5px] text-slate-500 leading-snug">
                                        <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                        </svg>
                                        <span class="truncate" title="{{ $v->location }}">{{ $v->location }}</span>
                                    </div>

                                    <!-- Baris 3: Chips Spek Resolusi & Orientasi Layar -->
                                    <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 font-mono text-[10px] sm:text-[10.5px] font-bold border border-slate-200/80 shadow-2xs">
                                            <svg class="w-2.5 h-2.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0H3" />
                                            </svg>
                                            {{ $v->resolution }} px
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] sm:text-[10.5px] font-medium border capitalize {{ strtolower($v->orientation) === 'portrait' ? 'bg-amber-50 text-amber-800 border-amber-200/70' : 'bg-emerald-50 text-emerald-800 border-emerald-200/70' }}">
                                            @if(strtolower($v->orientation) === 'portrait')
                                                <svg class="w-2.5 h-2.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <rect x="6" y="3" width="12" height="18" rx="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            @else
                                                <svg class="w-2.5 h-2.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <rect x="3" y="6" width="18" height="12" rx="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            @endif
                                            {{ $v->orientation }}
                                        </span>
                                    </div>

                                    <!-- Baris 4: Jam Operasional -->
                                    <div class="mt-1 flex items-center gap-1 text-[10px] sm:text-[10.5px] text-slate-400 font-medium">
                                        <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ $v->operating_hours }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Zona Status & Catatan Admin di Bagian Bawah (Terbaca Utuh & Proporsional) -->
                            @php
                                $isOverdue = $v->estimated_recovery_date && $v->estimated_recovery_date->endOfDay()->isPast();
                                $estDate = null;
                                if ($v->estimated_recovery_date) {
                                    $estDate = $isOverdue
                                        ? 'Tahap Konfirmasi Teknisi'
                                        : 'Est. normal: '.$v->estimated_recovery_date->locale('id')->translatedFormat('d M Y');
                                }

                                if ($isGangguan) {
                                    $statusLabel = 'Kendala Gangguan';
                                    $adminNote = $v->issue_notes ?: ($isOverdue ? 'Tenggat estimasi terlewat, teknisi sedang dalam tahap uji coba perangkat.' : 'Ada kendala teknis pada perangkat ini.');
                                    $boxClasses = 'bg-rose-50/90 border-rose-200/80 text-rose-950';
                                    $headerClasses = 'text-rose-800';
                                    $noteClasses = 'text-rose-700/90';
                                    $statusDot = 'bg-rose-500';
                                    $estBadgeClasses = $isOverdue ? 'bg-amber-100 text-amber-900 border-amber-300' : 'bg-rose-100 text-rose-900 border-rose-200/80';
                                } elseif ($isPerbaikan) {
                                    $statusLabel = 'Dalam Perbaikan';
                                    $adminNote = $v->issue_notes ?: ($isOverdue ? 'Tenggat estimasi terlewat, perangkat sedang dalam tahap uji coba oleh teknisi.' : 'Sedang dalam pemeliharaan teknisi.');
                                    $boxClasses = 'bg-amber-50/90 border-amber-200/80 text-amber-950';
                                    $headerClasses = 'text-amber-800';
                                    $noteClasses = 'text-amber-800/90';
                                    $statusDot = 'bg-amber-500';
                                    $estBadgeClasses = $isOverdue ? 'bg-blue-100 text-blue-900 border-blue-200/80' : 'bg-amber-100 text-amber-900 border-amber-200/80';
                                } elseif ($isInactive) {
                                    $statusLabel = 'Nonaktif Sementara';
                                    $adminNote = $v->issue_notes ?: 'Perangkat sementara tidak menerima jadwal penayangan.';
                                    $boxClasses = 'bg-slate-100/90 border-slate-200/80 text-slate-800';
                                    $headerClasses = 'text-slate-700';
                                    $noteClasses = 'text-slate-600';
                                    $statusDot = 'bg-slate-400';
                                    $estBadgeClasses = 'bg-slate-200 text-slate-800 border-slate-300';
                                } else {
                                    $statusLabel = 'Siap Digunakan';
                                    $adminNote = 'Perangkat aktif & siap menerima penayangan konten.';
                                    $estDate = null;
                                    $boxClasses = 'bg-emerald-50/70 border-emerald-200/70 text-[#0E4D3C]';
                                    $headerClasses = 'text-[#0E4D3C]';
                                    $noteClasses = 'text-[#2F6B58]';
                                    $statusDot = 'bg-emerald-500';
                                    $estBadgeClasses = '';
                                }
                            @endphp
                            <div class="mt-3 pt-2.5 border-t border-slate-100/90">
                                <div class="rounded-xl p-2.5 border {{ $boxClasses }}">
                                    <!-- Baris Header Status -->
                                    <div class="flex items-center justify-between gap-1.5 flex-wrap">
                                        <div class="flex items-center gap-1.5 min-w-0">
                                            <span class="h-2 w-2 rounded-full shrink-0 {{ $statusDot }} {{ !$isUnavailable ? 'animate-pulse' : '' }}"></span>
                                            <span class="font-bold text-[11.5px] sm:text-[12px] leading-none {{ $headerClasses }}">{{ $statusLabel }}</span>
                                        </div>
                                        @if($estDate)
                                            <span class="inline-flex items-center text-[10px] sm:text-[10.5px] font-semibold px-1.5 py-0.5 rounded border shrink-0 shadow-2xs {{ $estBadgeClasses }}">
                                                {{ $estDate }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Baris Pesan / Catatan Admin (Tampil Utuh & Terbaca Jelas) -->
                                    <div class="mt-1.5 flex items-start gap-1.5 text-[11px] leading-relaxed {{ $noteClasses }}">
                                        @if($isUnavailable)
                                            <svg class="w-3.5 h-3.5 shrink-0 mt-0.5 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="font-medium break-words">{{ $adminNote }}</span>
                                        @else
                                            <span class="font-medium break-words">
                                                <span class="sm:hidden">Perangkat aktif & siap digunakan.</span>
                                                <span class="hidden sm:inline">Perangkat aktif & siap menerima penayangan konten.</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </label>
                    @empty
                        <div class="sm:col-span-2 text-center py-8 text-xs text-slate-500">
                            Tidak ada videotron aktif yang tersedia saat ini.
                        </div>
                    @endforelse
                </div>

                <!-- Footer Card Langkah 1: Kiri teks hitungan, Kanan tombol aksi -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-500 font-medium"
                          x-text="`${selectedVideotrons.length} perangkat dipilih`">
                    </span>

                    <div class="flex items-center">
                        <!-- Tombol Lanjutkan dengan Animasi Mikro Beranda -->
                        <button type="button" 
                                @click="validateAndGoStep2()"
                                :disabled="selectedVideotrons.length === 0"
                                class="group btn-wizard-primary gap-1.5">
                            <span>Lanjutkan</span>
                            <svg class="w-3.5 h-3.5 arrow-icon-right" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
