@extends('layouts.portal')

@section('title', 'Informasi & Panduan Penayangan | E-VITRON UIN Siber Cirebon')

@section('content')
@php
    $maxVideoSec = (int) setting('max_video_duration', 30);
    if ($maxVideoSec >= 60) {
        $minutes = $maxVideoSec / 60;
        $videoDurationText = ($maxVideoSec % 60 === 0) 
            ? ((int)$minutes . ' menit') 
            : (round($minutes, 1) . ' menit');
        $videoDurationLabel = $videoDurationText . ' (' . $maxVideoSec . ' detik)';
    } else {
        $videoDurationLabel = $maxVideoSec . ' detik';
    }
    $minDaysBefore = (int) setting('min_days_before', 3);
@endphp
<div class="w-full overflow-x-hidden min-h-[calc(100vh-200px)] relative" style="background:#F4F8F6;">

    {{-- Islamic Corner Ornaments Background (Selaras Beranda, Pengajuan Saya, Ajukan Penayangan) --}}
    <div class="ornament-pattern" style="position:absolute;left:-40px;top:20px;height:240px;width:240px;opacity:0.35;pointer-events:none;"></div>
    <div class="ornament-pattern" style="position:absolute;right:-40px;top:80px;height:260px;width:260px;opacity:0.35;pointer-events:none;"></div>

    <div class="relative mx-auto max-w-[1400px] px-4 sm:px-10 lg:px-14 py-5 sm:py-8 space-y-5 sm:space-y-8 z-10">

        {{-- =========================================================================
             HEADER HALAMAN (Selaras Persis dengan Beranda & Pengajuan Saya)
             ========================================================================= --}}
        <div>
            {{-- Badge Emas Gradasi --}}
            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[10px] font-bold text-white shadow-xs"
                style="background:linear-gradient(90deg,#C9901A 0%,#DDB140 60%,#EDD088 100%);letter-spacing:0.01em;">
                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Panduan Teknis</span>
            </span>

            {{-- Judul Halaman --}}
            <div class="mt-2 mb-1.5">
                <h1 class="font-montserrat font-extrabold text-[#0E4D3C] text-xl sm:text-2xl md:text-3xl tracking-tight leading-tight">
                    Informasi & Panduan Penayangan
                </h1>
                <p class="mt-1 text-xs sm:text-[13px] leading-relaxed text-[#4E655F] max-w-2xl">
                    Panduan teknis materi dan tata cara pengajuan videotron UIN Siber Cirebon.
                </p>
            </div>
        </div>

        {{-- =========================================================================
             STRIP ALUR PENGAJUAN (4 LANGKAH MUDAH & CEPAT)
             Mobile: Horizontal Swipe Card Kompak | Desktop: 4 Kolom Grid Mewah
             ========================================================================= --}}
        <div class="relative overflow-hidden rounded-[22px] bg-white/95 border border-white/90 shadow-sm p-4 sm:p-7 backdrop-blur-md"
             x-data="{
                 activeStep: 0,
                 scrollToStep(idx) {
                     this.activeStep = idx;
                     const container = this.$refs.stepsScroll;
                     const card = container.children[idx];
                     if (card) {
                         const targetLeft = card.offsetLeft - container.offsetLeft - 16;
                         container.scrollTo({ left: Math.max(0, targetLeft), behavior: 'smooth' });
                     }
                 },
                 onScroll(e) {
                     const container = e.target;
                     const scrollLeft = container.scrollLeft;
                     const cardWidth = container.children[0]?.offsetWidth || 260;
                     this.activeStep = Math.min(3, Math.max(0, Math.round(scrollLeft / (cardWidth + 12))));
                 }
             }">
            {{-- Watermark Ornamen --}}
            <div class="ornament-pattern" style="position:absolute;right:-25px;bottom:-25px;height:120px;width:120px;opacity:0.12;pointer-events:none;"></div>

            <div class="flex items-center gap-2.5 mb-3.5 sm:mb-5">
                <span class="flex h-7 w-7 items-center justify-center rounded-full text-white shadow-xs shrink-0" style="background:#D9A93B;">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
                <h2 class="text-sm sm:text-lg font-montserrat font-extrabold text-[#0E4D3C]">
                    Alur Proses Pengajuan Penayangan
                </h2>
            </div>

            {{-- Container 4 Langkah: Horizontal Scroll Snap di Mobile, Grid 4 Kolom di Desktop --}}
            <div x-ref="stepsScroll"
                 @scroll.passive="onScroll($event)"
                 class="flex sm:grid sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 overflow-x-auto sm:overflow-visible pb-2 sm:pb-0 scroll-smooth snap-x snap-mandatory"
                 style="-webkit-overflow-scrolling: touch; scrollbar-width: none;">
                
                {{-- Langkah 1 --}}
                <div class="w-[78vw] max-w-[270px] shrink-0 sm:w-auto sm:max-w-none snap-start relative p-4 rounded-2xl bg-[#F8FAF9] border border-slate-200/70 space-y-2 flex flex-col justify-between">
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="flex h-7 w-7 sm:h-8 sm:w-8 items-center justify-center rounded-xl bg-[#E3F1EA] text-[#0E4D3C] font-black text-xs font-montserrat shadow-2xs">
                                01
                            </span>
                            <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
                                Persiapan
                            </span>
                        </div>
                        <h3 class="font-bold text-xs sm:text-sm text-[#0E4D3C] pt-0.5">Siapkan Materi Konten</h3>
                        <p class="text-[11.5px] sm:text-xs text-[#4E655F] leading-relaxed">
                            Desain poster atau video dengan resolusi standar Full HD (1080p). Unggah berkas ke Google Drive dan atur akses terbuka.
                        </p>
                    </div>
                </div>

                {{-- Langkah 2 --}}
                <div class="w-[78vw] max-w-[270px] shrink-0 sm:w-auto sm:max-w-none snap-start relative p-4 rounded-2xl bg-[#F8FAF9] border border-slate-200/70 space-y-2 flex flex-col justify-between">
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="flex h-7 w-7 sm:h-8 sm:w-8 items-center justify-center rounded-xl bg-[#E3F1EA] text-[#0E4D3C] font-black text-xs font-montserrat shadow-2xs">
                                02
                            </span>
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                Pengajuan
                            </span>
                        </div>
                        <h3 class="font-bold text-xs sm:text-sm text-[#0E4D3C] pt-0.5">Isi Formulir Online</h3>
                        <p class="text-[11.5px] sm:text-xs text-[#4E655F] leading-relaxed">
                            Pilih titik videotron yang diinginkan, tentukan rentang tanggal penayangan, dan lengkapi deskripsi publikasi kegiatan.
                        </p>
                    </div>
                </div>

                {{-- Langkah 3 --}}
                <div class="w-[78vw] max-w-[270px] shrink-0 sm:w-auto sm:max-w-none snap-start relative p-4 rounded-2xl bg-[#F8FAF9] border border-slate-200/70 space-y-2 flex flex-col justify-between">
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="flex h-7 w-7 sm:h-8 sm:w-8 items-center justify-center rounded-xl bg-[#E3F1EA] text-[#0E4D3C] font-black text-xs font-montserrat shadow-2xs">
                                03
                            </span>
                            <span class="text-[10px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200">
                                Hari Kerja
                            </span>
                        </div>
                        <h3 class="font-bold text-xs sm:text-sm text-[#0E4D3C] pt-0.5">Verifikasi & Kurasi Admin</h3>
                        <p class="text-[11.5px] sm:text-xs text-[#4E655F] leading-relaxed">
                            Pengajuan dapat dikirim kapan saja (24/7). Verifikasi materi diproses pada jam kerja dinas kampus (Senin – Jumat).
                        </p>
                    </div>
                </div>

                {{-- Langkah 4 --}}
                <div class="w-[78vw] max-w-[270px] shrink-0 sm:w-auto sm:max-w-none snap-start relative p-4 rounded-2xl bg-[#F8FAF9] border border-slate-200/70 space-y-2 flex flex-col justify-between">
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="flex h-7 w-7 sm:h-8 sm:w-8 items-center justify-center rounded-xl bg-[#E3F1EA] text-[#0E4D3C] font-black text-xs font-montserrat shadow-2xs">
                                04
                            </span>
                            <span class="text-[10px] font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-full border border-purple-200">
                                Tayang
                            </span>
                        </div>
                        <h3 class="font-bold text-xs sm:text-sm text-[#0E4D3C] pt-0.5">Penjadwalan & Tayang</h3>
                        <p class="text-[11.5px] sm:text-xs text-[#4E655F] leading-relaxed">
                            Materi yang lolos kurasi akan dijadwalkan tayang otomatis pada media player videotron terpilih sesuai tanggal agenda.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Dots Indicator di Mobile --}}
            <div class="flex sm:hidden items-center justify-center gap-1.5 pt-2.5">
                <template x-for="(step, idx) in [0, 1, 2, 3]" :key="idx">
                    <button type="button" 
                            @click="scrollToStep(idx)"
                            class="h-1.5 rounded-full transition-all duration-200 cursor-pointer"
                            :class="activeStep === idx ? 'w-5 bg-[#0E4D3C]' : 'w-1.5 bg-slate-200'"></button>
                </template>
            </div>
        </div>

        {{-- =========================================================================
             KETENTUAN MATERI & WAKTU PENGAJUAN
             Mobile: Segmented Tabs Rapi | Desktop: 2 Kolom Seimbang
             ========================================================================= --}}
        <div x-data="{ activeTab: 'format' }" class="space-y-3 sm:space-y-0">
            
            {{-- Mobile Segmented Tabs Switcher (Pilihan Cepat Tanpa Perlu Scroll Panjang) --}}
            <div class="flex md:hidden p-1 rounded-2xl bg-white border border-[#0E4D3C]/15 shadow-2xs">
                <button type="button" 
                        @click="activeTab = 'format'"
                        class="flex-1 py-2 px-3 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer select-none"
                        :class="activeTab === 'format' ? 'bg-[#0E4D3C] text-white shadow-xs' : 'text-slate-600 hover:text-[#0E4D3C]'">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Format Materi</span>
                </button>
                <button type="button" 
                        @click="activeTab = 'waktu'"
                        class="flex-1 py-2 px-3 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer select-none"
                        :class="activeTab === 'waktu' ? 'bg-[#0E4D3C] text-white shadow-xs' : 'text-slate-600 hover:text-[#0E4D3C]'">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Waktu & Prosedur</span>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">

                {{-- Kolom 1: Format Materi Konten --}}
                <div class="relative overflow-hidden rounded-[22px] bg-white/95 border border-white/90 shadow-sm p-5 sm:p-7 backdrop-blur-md space-y-4"
                     :class="activeTab === 'format' ? 'block' : 'hidden md:block'">
                    <div class="ornament-pattern" style="position:absolute;right:-25px;bottom:-25px;height:100px;width:100px;opacity:0.10;pointer-events:none;"></div>

                    <div class="flex items-center gap-2.5">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full text-white shadow-xs shrink-0" style="background:#D9A93B;">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <h2 class="text-base sm:text-lg font-montserrat font-extrabold text-[#0E4D3C]">
                            Format & Standar Materi
                        </h2>
                    </div>

                    <ul class="text-xs text-[#4E655F] space-y-3 leading-relaxed">
                        <li class="flex items-start gap-2.5">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#E3F1EA] text-[#0E4D3C] mt-0.5 shadow-2xs">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                </svg>
                            </span>
                            <div>
                                <strong class="text-slate-800">Poster / Gambar Diam:</strong> Format JPG atau PNG wajib mengikuti orientasi layar (Landscape mendatar atau Portrait tegak) dan resolusi piksel videotron tujuan tanpa kompresi berlebih.
                            </div>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#E3F1EA] text-[#0E4D3C] mt-0.5 shadow-2xs">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                </svg>
                            </span>
                            <div>
                                <strong class="text-slate-800">Video Animasi / Teaser:</strong> Format MP4 (Codec H.264), wajib mengikuti orientasi dan rasio aspek videotron tujuan, durasi maksimal {{ $videoDurationLabel }} per siklus penayangan.
                            </div>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#E3F1EA] text-[#0E4D3C] mt-0.5 shadow-2xs">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                </svg>
                            </span>
                            <div>
                                <strong class="text-slate-800">Tanpa Keluaran Audio:</strong> Seluruh videotron beroperasi tanpa audio (bisu). Materi video wajib menyampaikan pesan secara visual yang jelas—gunakan grafis teks atau sertakan subtitle jika memuat narasi/dialog.
                            </div>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#E3F1EA] text-[#0E4D3C] mt-0.5 shadow-2xs">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                </svg>
                            </span>
                            <div>
                                <strong class="text-slate-800">Kejelasan Teks:</strong> Gunakan ukuran huruf yang kontras dan mudah dibaca dari jarak pandang pejalan kaki maupun pengendara kampus (minimal 24–36 pt).
                            </div>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#E3F1EA] text-[#0E4D3C] mt-0.5 shadow-2xs">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                </svg>
                            </span>
                            <div>
                                <strong class="text-slate-800">Norma & Etika Kampus:</strong> Materi tidak memuat konten politik praktis, komersial pihak ketiga tanpa izin, ujaran kebencian, atau unsur yang bertentangan dengan norma akademik UIN SSC.
                            </div>
                        </li>
                    </ul>
                </div>

                {{-- Kolom 2: Ketentuan Pengajuan & Tautan Berkas --}}
                <div class="relative overflow-hidden rounded-[22px] bg-white/95 border border-white/90 shadow-sm p-5 sm:p-7 backdrop-blur-md space-y-4"
                     :class="activeTab === 'waktu' ? 'block' : 'hidden md:block'">
                    <div class="ornament-pattern" style="position:absolute;right:-25px;bottom:-25px;height:100px;width:100px;opacity:0.10;pointer-events:none;"></div>

                    <div class="flex items-center gap-2.5">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full text-white shadow-xs shrink-0" style="background:#D9A93B;">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <h2 class="text-base sm:text-lg font-montserrat font-extrabold text-[#0E4D3C]">
                            Waktu & Prosedur Verifikasi
                        </h2>
                    </div>

                    <ul class="text-xs text-[#4E655F] space-y-3 leading-relaxed">
                        <li class="flex items-start gap-2.5">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-amber-100/80 border border-amber-200/60 mt-0.5 shadow-2xs">
                                <span class="h-2 w-2 rounded-full bg-[#D9A93B]"></span>
                            </span>
                            <div>
                                <strong class="text-slate-800">Waktu Pengajuan:</strong> Disarankan mengirimkan pengajuan minimal <strong class="text-[#0E4D3C]">H-{{ $minDaysBefore }} hari kerja</strong> sebelum tanggal penayangan perdana yang diharapkan.
                            </div>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-amber-100/80 border border-amber-200/60 mt-0.5 shadow-2xs">
                                <span class="h-2 w-2 rounded-full bg-[#D9A93B]"></span>
                            </span>
                            <div>
                                <strong class="text-slate-800">Kurasi di Jam Kerja:</strong> Pengajuan yang dikirim di luar jam kerja (malam hari, akhir pekan, atau hari libur) akan diproses oleh Tim Admin pada hari kerja kedinasan berikutnya.
                            </div>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-amber-100/80 border border-amber-200/60 mt-0.5 shadow-2xs">
                                <span class="h-2 w-2 rounded-full bg-[#D9A93B]"></span>
                            </span>
                            <div>
                                <strong class="text-slate-800">Akses Google Drive Terbuka:</strong> Pastikan tautan folder atau berkas telah diatur ke <strong class="text-[#0E4D3C]">"Anyone with the link can view"</strong> agar berkas dapat diunduh oleh verifikator.
                            </div>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-amber-100/80 border border-amber-200/60 mt-0.5 shadow-2xs">
                                <span class="h-2 w-2 rounded-full bg-[#D9A93B]"></span>
                            </span>
                            <div>
                                <strong class="text-slate-800">Tindak Lanjut Revisi:</strong> Jika materi dinyatakan perlu revisi, pengaju dapat memperbarui link materi melalui menu <em>Pengajuan Saya</em> tanpa perlu membuat tiket baru.
                            </div>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-amber-100/80 border border-amber-200/60 mt-0.5 shadow-2xs">
                                <span class="h-2 w-2 rounded-full bg-[#D9A93B]"></span>
                            </span>
                            <div>
                                <strong class="text-slate-800">Dukungan &amp; Koordinasi Khusus:</strong> Untuk kegiatan resmi institusional yang bersifat mendesak atau memerlukan asistensi khusus, silakan hubungi Tim Admin E-VITRON melalui kanal helpdesk resmi.
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- =========================================================================
             BANNER HUBUNGI ADMIN E-VITRON (BANTUAN & KONSULTASI)
             ========================================================================= --}}
        @php
            $waRaw = (string) setting('helpdesk_whatsapp', setting('contact_whatsapp', '081234567890'));
            $waDigits = preg_replace('/[^0-9]/', '', $waRaw);
            $waLink = str_starts_with($waDigits, '0')
                ? ('62' . substr($waDigits, 1))
                : (str_starts_with($waDigits, '62') ? $waDigits : ('62' . $waDigits));
        @endphp
        <div class="relative overflow-hidden rounded-[22px] p-5 sm:p-7 text-white shadow-md flex flex-col md:flex-row md:items-center justify-between gap-4 sm:gap-5"
             style="background: linear-gradient(135deg, #0E4D3C 0%, #165B48 60%, #1A6953 100%);">
            
            <div class="ornament-pattern" style="position:absolute;right:-20px;bottom:-20px;height:140px;width:140px;opacity:0.25;pointer-events:none;"></div>

            <div class="space-y-1.5 z-10 max-w-xl">
                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[10px] font-bold text-[#0E4D3C] bg-[#D9A93B] shadow-2xs">
                    Layanan Helpdesk
                </span>
                <h3 class="text-base sm:text-xl font-montserrat font-extrabold tracking-tight">
                    Butuh Bantuan atau Konsultasi Penayangan?
                </h3>
                <p class="text-xs sm:text-[13px] text-white/80 leading-relaxed">
                    Tim Admin E-VITRON UIN Siber Syekh Nurjati Cirebon siap membantu kelancaran publikasi agenda dan kegiatan sivitas akademika kampus.
                </p>
            </div>

            <div class="flex items-center gap-3 z-10 shrink-0 w-full sm:w-auto">
                <a href="https://wa.me/{{ $waLink }}?text=Halo%20Admin%20SIPETRON%20UIN%20SSC,%20saya%20ingin%20berkonsultasi%20mengenai%20penayangan%20videotron." target="_blank"
                   class="group w-full sm:w-auto inline-flex items-center justify-center gap-2.5 rounded-full px-5 py-2.5 text-xs sm:text-sm font-bold bg-[#D9A93B] text-[#0E4D3C] shadow-md hover:bg-[#C5942B] hover:-translate-y-0.5 transition-all duration-200 select-none">
                    <svg class="h-5 w-5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.01 2c-5.5 0-9.97 4.47-9.97 9.97 0 1.76.46 3.48 1.33 4.99L2 22l5.22-1.37c1.45.79 3.09 1.21 4.78 1.21 5.5 0 9.98-4.47 9.98-9.97 0-2.66-1.04-5.16-2.92-7.04C17.17 3.04 14.67 2 12.01 2zm5.83 14.12c-.24.68-1.4 1.25-1.94 1.32-.51.07-1.15.1-3.34-.78-2.8-1.13-4.59-3.99-4.73-4.18-.14-.19-1.14-1.52-1.14-2.9 0-1.38.72-2.06.98-2.35.26-.28.56-.35.75-.35.19 0 .38 0 .54.01.17.01.4-.06.63.48.24.57.81 1.97.88 2.11.07.14.12.31.02.5-.1.19-.15.31-.29.47-.14.16-.3.35-.43.47-.14.14-.29.3-.12.59.16.29.73 1.21 1.57 1.96 1.08.97 1.99 1.27 2.28 1.41.28.14.45.12.61-.07.17-.19.72-.84.91-1.13.19-.28.38-.24.64-.14.26.09 1.66.78 1.94.92.28.14.47.21.54.33.07.12.07.69-.17 1.37z"/>
                    </svg>
                    <span>Hubungi WhatsApp Admin E-VITRON</span>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
