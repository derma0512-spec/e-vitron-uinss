            {{-- ==============================================================
                 LANGKAH 2 — TENTUKAN TANGGAL
                 ============================================================== --}}
            <div data-step="2" 
                 x-show="currentStep === 2" 
                 x-cloak 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1.5"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="relative min-h-[460px] bg-white/95 backdrop-blur-md rounded-2xl sm:rounded-[22px] border border-white/90 shadow-sm p-3.5 sm:p-8 space-y-3.5 sm:space-y-6">
                
                {{-- Watermark ornamen di sudut form (dengan container terisolasi overflow-hidden agar popover kalender tidak terpotong) --}}
                <div class="absolute inset-0 rounded-2xl sm:rounded-[22px] overflow-hidden pointer-events-none">
                    <div class="ornament-pattern" style="position:absolute;right:-25px;bottom:-25px;height:120px;width:120px;opacity:0.12;"></div>
                </div>

                <!-- Header Kartu Langkah 2 -->
                <div>
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-6 w-6 sm:h-7 sm:w-7 items-center justify-center rounded-full text-white shadow-xs shrink-0" style="background:#D9A93B;">
                            <svg class="h-3 w-3 sm:h-3.5 sm:w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <h2 class="text-sm sm:text-lg font-montserrat font-extrabold text-[#0E4D3C]">
                            <span class="sm:hidden">Tentukan Periode Penayangan</span>
                            <span class="hidden sm:inline">Langkah 2 — Tentukan Tanggal</span>
                        </h2>
                    </div>
                    <p class="text-[11px] sm:text-xs text-[#4E655F] mt-0.5 sm:mt-1 pl-8 sm:pl-9.5">Tentukan rentang tanggal penayangan materi publikasi Anda. Setiap pengajuan berlaku untuk satu periode jadwal.</p>
                </div>

                @php
                    $todayWib = now('Asia/Jakarta');
                    $todayDisplayIndo = $todayWib->locale('id')->isoFormat('D MMM');
                    $minDaysSetting = (int) setting('min_days_before', 3);
                    $minDateDisplayIndo = $todayWib->copy()->addDays($minDaysSetting)->locale('id')->isoFormat('D MMM');
                @endphp

                <!-- Input Tanggal Mulai & Tanggal Selesai (2 Kolom - Custom Popover Murni Sesuai Screenshot) -->
                <div class="grid sm:grid-cols-2 gap-3 sm:gap-4">
                    {{-- Kolom 1: Tanggal Mulai --}}
                    <div class="relative" @click.outside="showStartCalendar = false">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Mulai <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="hidden" name="start_date" :value="startDate">
                            <input type="text" 
                                   x-model="startDisplay" 
                                   @focus="showStartCalendar = true; showEndCalendar = false"
                                   @click="showStartCalendar = true; showEndCalendar = false"
                                   @input="handleDateInput('start', $event.target.value)"
                                   @blur="handleDateBlur('start')"
                                   placeholder="Pilih atau ketik (contoh: 28 Okt 2026)" 
                                   class="w-full pl-3.5 pr-11 py-2.5 bg-white border rounded-xl text-slate-800 text-xs sm:text-sm focus:outline-none focus:ring-1 focus:ring-[#0E4D3C] transition-all cursor-pointer font-medium placeholder:text-slate-400"
                                   :class="startDateError ? 'border-rose-400 bg-rose-50/20' : 'border-slate-300 focus:border-[#0E4D3C]'"
                                   autocomplete="off">
                            <button type="button" 
                                    @click="showStartCalendar = !showStartCalendar; showEndCalendar = false" 
                                    class="absolute right-2 top-1/2 -translate-y-1/2 w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-[#0E4D3C] hover:bg-[#EAF5F0] transition-colors cursor-pointer">
                                <svg class="w-4 h-4 text-[#0E4D3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                        <p x-show="startDateError" x-text="startDateError" class="text-[11px] text-rose-600 mt-1 font-medium"></p>

                        <!-- Popover Kalender Mulai (Desain Compact, Rapi, & Indikator Hari Ini) -->
                        <div x-show="showStartCalendar"
                             x-ref="startCalendarPopover"
                             x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-98"
                             class="absolute left-0 top-full mt-2 z-50 bg-white rounded-2xl shadow-2xl ring-1 ring-black/5 border border-slate-200/90 p-3 w-[265px] select-none">
                            
                            <!-- Header Kalender Compact -->
                            <div class="flex items-center justify-between mb-2">
                                <button type="button" 
                                        @click.stop="prevMonth('start')" 
                                        class="h-6 w-6 flex items-center justify-center rounded-lg text-slate-400 hover:text-[#0E4D3C] hover:bg-[#EAF5F0] transition-colors cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <span class="font-montserrat font-bold text-xs text-[#0E4D3C]" x-text="getMonthTitle(startCalendarYear, startCalendarMonth)"></span>
                                <button type="button" 
                                        @click.stop="nextMonth('start')" 
                                        class="h-6 w-6 flex items-center justify-center rounded-lg text-slate-400 hover:text-[#0E4D3C] hover:bg-[#EAF5F0] transition-colors cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>

                            <!-- Hari: Sen Sel Rab Kam Jum Sab Min -->
                            <div class="grid grid-cols-7 mb-1.5 text-center">
                                <span class="text-[10px] font-bold text-slate-400">Sen</span>
                                <span class="text-[10px] font-bold text-slate-400">Sel</span>
                                <span class="text-[10px] font-bold text-slate-400">Rab</span>
                                <span class="text-[10px] font-bold text-slate-400">Kam</span>
                                <span class="text-[10px] font-bold text-slate-400">Jum</span>
                                <span class="text-[10px] font-bold text-slate-400">Sab</span>
                                <span class="text-[10px] font-bold text-slate-400">Min</span>
                            </div>

                            <!-- Grid Tanggal Compact (28px Sel) -->
                            <div class="grid grid-cols-7 gap-1 text-center">
                                <template x-for="(cell, idx) in getCalendarDays(startCalendarYear, startCalendarMonth, minDateH3, '')" :key="idx">
                                    <div class="h-7 w-7 mx-auto flex items-center justify-center">
                                        <template x-if="cell.day">
                                            <button type="button"
                                                    @click.stop="selectDate('start', cell.dateStr)"
                                                    :disabled="cell.isDisabled"
                                                    class="h-7 w-7 flex items-center justify-center text-[11px] rounded-full transition-all"
                                                    :class="{
                                                        'bg-[#0E4D3C] text-white font-bold shadow-xs scale-105': startDate === cell.dateStr && !cell.isDisabled,
                                                        'border-2 border-[#0E4D3C] text-[#0E4D3C] font-bold': cell.isToday && startDate !== cell.dateStr && !cell.isDisabled,
                                                        'border-2 border-[#0E4D3C]/60 text-slate-400 font-bold cursor-not-allowed pointer-events-none': cell.isToday && cell.isDisabled,
                                                        'text-slate-300 cursor-not-allowed pointer-events-none': cell.isDisabled && !cell.isToday,
                                                        'text-slate-700 hover:bg-slate-100 font-semibold cursor-pointer': !cell.isDisabled && startDate !== cell.dateStr && !cell.isToday
                                                    }"
                                                    x-text="cell.day">
                                            </button>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <!-- Footer Mini Info (Penjelasan Hari Ini & Syarat H-3) -->
                            <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-500">
                                <span class="flex items-center gap-1.5">
                                    <span class="inline-block w-2.5 h-2.5 rounded-full border-2 border-[#0E4D3C]/70"></span>
                                    <span>Hari ini (<span x-text="todayDisplayIndo">{{ $todayDisplayIndo }}</span>)</span>
                                </span>
                                <span class="font-bold text-[#0E4D3C]">Paling awal <span x-text="minDateDisplayIndo">{{ $minDateDisplayIndo }}</span></span>
                            </div>
                        </div>
                    </div>

                    {{-- Kolom 2: Tanggal Selesai --}}
                    <div class="relative" @click.outside="showEndCalendar = false">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Selesai <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="hidden" name="end_date" :value="endDate">
                            <input type="text" 
                                   x-model="endDisplay" 
                                   @focus="showEndCalendar = true; showStartCalendar = false"
                                   @click="showEndCalendar = true; showStartCalendar = false"
                                   @input="handleDateInput('end', $event.target.value)"
                                   @blur="handleDateBlur('end')"
                                   placeholder="Pilih atau ketik (contoh: 30 Okt 2026)" 
                                   class="w-full pl-3.5 pr-11 py-2.5 bg-white border rounded-xl text-slate-800 text-xs sm:text-sm focus:outline-none focus:ring-1 focus:ring-[#0E4D3C] transition-all cursor-pointer font-medium placeholder:text-slate-400"
                                   :class="endDateError ? 'border-rose-400 bg-rose-50/20' : 'border-slate-300 focus:border-[#0E4D3C]'"
                                   autocomplete="off">
                            <button type="button" 
                                    @click="showEndCalendar = !showEndCalendar; showStartCalendar = false" 
                                    class="absolute right-2 top-1/2 -translate-y-1/2 w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-[#0E4D3C] hover:bg-[#EAF5F0] transition-colors cursor-pointer">
                                <svg class="w-4 h-4 text-[#0E4D3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                        <p x-show="endDateError" x-text="endDateError" class="text-[11px] text-rose-600 mt-1 font-medium"></p>

                        <!-- Popover Kalender Selesai (Desain Compact, Rapi, & Indikator Hari Ini) -->
                        <div x-show="showEndCalendar"
                             x-ref="endCalendarPopover"
                             x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-98"
                             class="absolute left-0 sm:left-auto sm:right-0 top-full mt-2 z-50 bg-white rounded-2xl shadow-2xl ring-1 ring-black/5 border border-slate-200/90 p-3 w-[265px] select-none">
                            
                            <!-- Header Kalender Compact -->
                            <div class="flex items-center justify-between mb-2">
                                <button type="button" 
                                        @click.stop="prevMonth('end')" 
                                        class="h-6 w-6 flex items-center justify-center rounded-lg text-slate-400 hover:text-[#0E4D3C] hover:bg-[#EAF5F0] transition-colors cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <span class="font-montserrat font-bold text-xs text-[#0E4D3C]" x-text="getMonthTitle(endCalendarYear, endCalendarMonth)"></span>
                                <button type="button" 
                                        @click.stop="nextMonth('end')" 
                                        class="h-6 w-6 flex items-center justify-center rounded-lg text-slate-400 hover:text-[#0E4D3C] hover:bg-[#EAF5F0] transition-colors cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>

                            <!-- Hari: Sen Sel Rab Kam Jum Sab Min -->
                            <div class="grid grid-cols-7 mb-1.5 text-center">
                                <span class="text-[10px] font-bold text-slate-400">Sen</span>
                                <span class="text-[10px] font-bold text-slate-400">Sel</span>
                                <span class="text-[10px] font-bold text-slate-400">Rab</span>
                                <span class="text-[10px] font-bold text-slate-400">Kam</span>
                                <span class="text-[10px] font-bold text-slate-400">Jum</span>
                                <span class="text-[10px] font-bold text-slate-400">Sab</span>
                                <span class="text-[10px] font-bold text-slate-400">Min</span>
                            </div>

                            <!-- Grid Tanggal Compact (28px Sel) -->
                            <div class="grid grid-cols-7 gap-1 text-center">
                                <template x-for="(cell, idx) in getCalendarDays(endCalendarYear, endCalendarMonth, startDate || minDateH3, '')" :key="idx">
                                    <div class="h-7 w-7 mx-auto flex items-center justify-center">
                                        <template x-if="cell.day">
                                            <button type="button"
                                                    @click.stop="selectDate('end', cell.dateStr)"
                                                    :disabled="cell.isDisabled"
                                                    class="h-7 w-7 flex items-center justify-center text-[11px] rounded-full transition-all"
                                                    :class="{
                                                        'bg-[#0E4D3C] text-white font-bold shadow-xs scale-105': endDate === cell.dateStr && !cell.isDisabled,
                                                        'border-2 border-[#0E4D3C] text-[#0E4D3C] font-bold': cell.isToday && endDate !== cell.dateStr && !cell.isDisabled,
                                                        'border-2 border-[#0E4D3C]/60 text-slate-400 font-bold cursor-not-allowed pointer-events-none': cell.isToday && cell.isDisabled,
                                                        'text-slate-300 cursor-not-allowed pointer-events-none': cell.isDisabled && !cell.isToday,
                                                        'text-slate-700 hover:bg-slate-100 font-semibold cursor-pointer': !cell.isDisabled && endDate !== cell.dateStr && !cell.isToday
                                                    }"
                                                    x-text="cell.day">
                                            </button>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <!-- Footer Mini Info (Penjelasan Hari Ini & Syarat H-3) -->
                            <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-500">
                                <span class="flex items-center gap-1.5">
                                    <span class="inline-block w-2.5 h-2.5 rounded-full border-2 border-[#0E4D3C]/70"></span>
                                    <span>Hari ini ({{ $todayDisplayIndo }})</span>
                                </span>
                                <span class="font-bold text-[#0E4D3C]">Tanggal akhir tayang</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Live Summary Card Kalkulasi Durasi Hari (Muncul Halus saat Kedua Tanggal Dipilih & Valid) -->
                <div x-show="calculatedDurationDays > 0"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="p-4 rounded-xl bg-gradient-to-r from-[#EAF5F0] via-[#F4F9F6] to-white border border-[#CDE5D9] shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#0E4D3C] text-white shadow-xs shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold text-slate-600">Total Durasi Penayangan:</span>
                                <span class="text-sm font-extrabold text-[#0E4D3C]" x-text="`${calculatedDurationDays} Hari`"></span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5" x-text="`${formatDisplayDate(startDate)} s/d ${formatDisplayDate(endDate)}`"></p>
                        </div>
                    </div>

                    <div class="shrink-0 flex items-center gap-1.5 self-start sm:self-auto">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-100/80 text-emerald-800 border border-emerald-200/60">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Jadwal Terverifikasi</span>
                        </span>
                    </div>
                </div>

                <!-- Alert Info Hijau Muda Sesuai Kebijakan Sistem (Bahasa Operasional Logis & Terstruktur) -->
                <div class="p-3 sm:p-3.5 rounded-xl bg-[#EAF5F0] border border-[#CDE5D9] flex items-center gap-2.5 sm:gap-3 text-[11px] sm:text-xs text-[#1E5546] leading-relaxed">
                    <svg class="w-4 h-4 text-[#0E4D3C] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @php
                        $maxDaysSetting = (int) setting('max_display_days', 7);
                        $minDaysSetting = (int) setting('min_days_before', 3);
                    @endphp
                    @if($maxDaysSetting > 0)
                        <span>Pengajuan wajib dikirim minimal <strong>H-{{ $minDaysSetting }}</strong> sebelum tanggal penayangan, dengan batas durasi maksimal <strong>{{ $maxDaysSetting }} hari</strong> per pengajuan.</span>
                    @else
                        <span>Pengajuan wajib dikirim minimal <strong>H-{{ $minDaysSetting }}</strong> sebelum tanggal penayangan. Durasi penayangan akan disesuaikan dengan ketersediaan slot antrean videotron.</span>
                    @endif
                </div>

                <!-- Footer Card Langkah 2 dengan Animasi Mikro Beranda -->
                <div class="pt-3.5 sm:pt-4 border-t border-slate-100 flex items-center justify-between">
                    <!-- Tombol Sebelumnya Mengikuti Gaya 'Lihat semua' di Beranda -->
                    <button type="button" 
                            @click="goToStep(1)" 
                            class="group btn-wizard-secondary gap-1.5">
                        <svg class="w-3 h-3 arrow-icon-left text-[#0E4D3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        <span>Sebelumnya</span>
                    </button>

                    <button type="button" 
                            @click="validateAndGoStep3()"
                            class="group btn-wizard-primary gap-1.5">
                        <span>Lanjutkan</span>
                        <svg class="w-3.5 h-3.5 arrow-icon-right" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

