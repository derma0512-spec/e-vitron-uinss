@extends('layouts.portal')

@section('title', 'Ajukan Penayangan | E-VITRON UIN Siber Cirebon')

@section('content')
<style>
    /* =========================================================================
       ANIMASI MIKRO TOMBOL MENGIKUTI BERANDA (UIN SSC)
       ========================================================================= */
    /* Tombol Utama (Lanjutkan & Kirim Pengajuan) - Ramping & Proporsional */
    .btn-wizard-primary {
        background-color: #D9A93B;
        color: #0E4D3C;
        border-radius: 9999px;
        padding: 5.5px 15px;
        font-weight: 700;
        font-size: 11.5px;
        box-shadow: 0 2px 6px rgba(217, 169, 59, 0.20);
        transition: all 200ms cubic-bezier(0.16, 1, 0.3, 1);
        will-change: transform, background-color, box-shadow;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        user-select: none;
    }
    @media (min-width: 640px) {
        .btn-wizard-primary {
            padding: 6.5px 18px;
            font-size: 12px;
            box-shadow: 0 3px 10px rgba(217, 169, 59, 0.25);
        }
    }
    .btn-wizard-primary:hover:not(:disabled) {
        background-color: #C5942B;
        box-shadow: 0 4px 14px rgba(197, 148, 43, 0.35);
        transform: translateY(-1.5px);
    }
    .btn-wizard-primary:active:not(:disabled) {
        transform: translateY(0) scale(0.96);
    }
    .btn-wizard-primary:disabled {
        background-color: #E2E8F0;
        color: #94A3B8;
        box-shadow: none;
        cursor: not-allowed;
        transform: none;
    }

    /* Animasi mikro panah kanan */
    .btn-wizard-primary:hover:not(:disabled) .arrow-icon-right {
        transform: translateX(3px);
    }
    .arrow-icon-right {
        transition: transform 200ms cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Tombol Batal & Sebelumnya (Persis Mengikuti Tombol 'Lihat semua' di Beranda) */
    .btn-wizard-secondary {
        background-color: rgba(255, 255, 255, 0.90);
        border: 1px solid rgba(14, 77, 60, 0.20);
        color: #0E4D3C;
        border-radius: 9999px;
        padding: 5.5px 14px;
        font-weight: 700;
        font-size: 11.5px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        transition: all 200ms cubic-bezier(0.16, 1, 0.3, 1);
        will-change: transform, background-color, color, border-color, box-shadow;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        user-select: none;
    }
    @media (min-width: 640px) {
        .btn-wizard-secondary {
            padding: 6px 16px;
            font-size: 12px;
        }
    }
    .btn-wizard-secondary:hover:not(:disabled) {
        background-color: #0E4D3C !important;
        border-color: #0E4D3C !important;
        color: #ffffff !important;
        box-shadow: 0 6px 18px rgba(14, 77, 60, 0.22);
        transform: translateY(-2px);
    }
    .btn-wizard-secondary:hover:not(:disabled) svg {
        color: #ffffff !important;
        stroke: #ffffff !important;
    }
    .btn-wizard-secondary:active:not(:disabled) {
        transform: translateY(0) scale(0.95);
    }

    /* Animasi mikro panah kiri saat hover */
    .btn-wizard-secondary:hover:not(:disabled) .arrow-icon-left {
        transform: translateX(-3px);
    }
    .arrow-icon-left {
        transition: transform 200ms cubic-bezier(0.16, 1, 0.3, 1);
    }

    </style>

{{-- Hide step 1 before Alpine mounts when resuming a saved draft step from sessionStorage --}}
<script>
    (function() {
        try {
            sessionStorage.removeItem('sipetron_wizard_draft');
            var draftKey = 'sipetron_wizard_draft_{{ Auth::id() }}';
            var raw = sessionStorage.getItem(draftKey);
            if (raw) {
                var draft = JSON.parse(raw);
                if (draft && draft.currentStep > 1 && draft.currentStep < 5) {
                    var s = document.createElement('style');
                    s.id = 'sipetron-step-preloader';
                    s.textContent = '[data-step="1"] { display: none !important; } [data-step="' + draft.currentStep + '"] { display: block !important; }';
                    document.head.appendChild(s);
                }
            }
        } catch (err) { console.warn('[E-VITRON] Could not read draft step preloader:', err); }
    })();
</script>

<div class="w-full overflow-x-clip min-h-[calc(100vh-200px)] relative" style="background:#F4F8F6;" 
     x-data="submissionWizard()"
     @keydown.enter.window="handleEnterKey($event)">

    {{-- Islamic Corner Ornaments Background (Persis Beranda & Pengajuan Saya) --}}
    <div class="ornament-pattern" style="position:absolute;left:-40px;top:20px;height:240px;width:240px;opacity:0.35;pointer-events:none;"></div>
    <div class="ornament-pattern" style="position:absolute;right:-40px;top:80px;height:260px;width:260px;opacity:0.35;pointer-events:none;"></div>

    <div class="relative mx-auto max-w-[1400px] px-4 sm:px-10 lg:px-14 py-5 sm:py-8 space-y-4 sm:space-y-6 z-10">

        {{-- =========================================================================
             HEADER HALAMAN & STEPPER (Selaras Persis Beranda & Pengajuan Saya)
             ========================================================================= --}}
        <div>
            {{-- Baris Atas: Badge & Tombol Ketentuan Sejajar Rapi di Kanan Atas Sesuai Mockup --}}
            <div class="flex items-center justify-between gap-3">
                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[10px] font-bold text-white shadow-xs shrink-0"
                    style="background:linear-gradient(90deg,#C9901A 0%,#DDB140 60%,#EDD088 100%);letter-spacing:0.01em;">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Formulir Pengajuan Baru</span>
                </span>

                {{-- Tombol Buka Modal Ketentuan Pengajuan (di Pojok Kanan Atas) --}}
                <button type="button" 
                        @click="showNoticeModal = true; $el.blur()" 
                        class="group inline-flex h-8 sm:h-9 items-center gap-1.5 sm:gap-2 rounded-full bg-white/95 hover:bg-[#D9A93B] border border-[#0E4D3C]/20 hover:border-[#D9A93B] px-3 sm:px-4 text-[11px] sm:text-xs font-bold text-[#0E4D3C] hover:text-[#123F33] shadow-xs hover:shadow-md hover:shadow-[#D9A93B]/25 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 transition-all duration-300 select-none cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0E4D3C]/30 shrink-0">
                    <span class="flex h-4 w-4 sm:h-4.5 sm:w-4.5 items-center justify-center rounded-full bg-[#E3F1EA] group-hover:bg-white/40 text-[#0E4D3C] group-hover:text-[#123F33] transition-all duration-300 group-hover:scale-110">
                        <svg class="h-2.5 w-2.5 sm:h-3 sm:w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                        </svg>
                    </span>
                    <span class="sm:hidden">Ketentuan</span>
                    <span class="hidden sm:inline">Ketentuan Pengajuan</span>
                </button>
            </div>

            {{-- Judul Halaman Gagah & Deskripsi Ringkas dengan Breathing Space Lega --}}
            <div class="mt-2.5 sm:mt-3 mb-3.5 sm:mb-5">
                <h1 class="font-montserrat font-extrabold text-[#0E4D3C] text-xl sm:text-3xl tracking-tight leading-snug">
                    Ajukan Penayangan
                </h1>
                <p class="mt-1 text-xs sm:text-[13px] leading-relaxed text-[#4E655F] max-w-xl">
                    Isi formulir pengajuan materi videotron kampus melalui langkah mudah di bawah ini.
                </p>
            </div>

                        <!-- Stepper Mobile Ringkas (< sm) -->
            <div class="sm:hidden p-3.5 rounded-2xl bg-white/95 backdrop-blur-md border border-[#0E4D3C]/15 shadow-xs select-none">
                <div class="flex items-center justify-between text-xs font-bold text-[#0E4D3C] mb-2">
                    <span class="flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[#D9A93B] text-[#0E4D3C] font-black text-xs shadow-xs" x-text="currentStep"></span>
                        <span class="font-extrabold" x-text="steps[currentStep - 1]?.label"></span>
                    </span>
                    <span class="text-[11px] text-[#5B7A70] font-semibold" x-text="`Langkah ${currentStep} dari ${steps.length}`"></span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200/60">
                    <div class="bg-gradient-to-r from-[#0E4D3C] via-[#1E755D] to-[#D9A93B] h-2 rounded-full transition-all duration-300"
                         :style="`width: ${(currentStep / steps.length) * 100}%`"></div>
                </div>
            </div>

            <!-- Stepper 5 Langkah Horizontal Desktop (>= sm) -->
            <div class="hidden sm:block relative py-2.5 px-3 sm:px-4 rounded-2xl bg-white/80 backdrop-blur-md border border-[#0E4D3C]/10 shadow-xs select-none overflow-x-auto no-scrollbar">
                <div class="flex items-center justify-between min-w-[620px] sm:min-w-0 relative">
                    <!-- Garis Penghubung Track -->
                    <div class="absolute left-4 right-4 top-1/2 -translate-y-1/2 h-0.5 bg-slate-200/90 z-0"></div>
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 h-0.5 bg-[#D9A93B] transition-all duration-300 z-0"
                         :style="`width: calc(${((currentStep - 1) / 4) * 100}% - 8px)`"></div>

                    <!-- Steps 1 s/d 5 -->
                    <template x-for="(st, index) in steps" :key="index">
                        <div class="relative z-10 flex items-center gap-2 bg-white/95 px-2.5 py-1 rounded-full border border-slate-100 shadow-2xs transition-all"
                             :class="index + 1 < currentStep ? 'cursor-pointer hover:border-[#0E4D3C]/40 hover:bg-slate-50' : ''"
                             @click="index + 1 < currentStep ? goToStep(index + 1) : null"
                             :title="index + 1 < currentStep ? 'Kembali ke ' + st.label : ''">
                            <!-- Nomor Lingkaran -->
                            <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full flex items-center justify-center font-bold text-xs transition-all duration-200 border"
                                 :class="{
                                     'bg-[#D9A93B] border-[#D9A93B] text-[#0E4D3C] font-black shadow-xs ring-2 ring-[#D9A93B]/30': currentStep === index + 1,
                                     'bg-[#0E4D3C] border-[#0E4D3C] text-white': currentStep > index + 1,
                                     'bg-white border-slate-300 text-slate-400': currentStep < index + 1
                                 }">
                                <template x-if="currentStep > index + 1">
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </template>
                                <template x-if="currentStep <= index + 1">
                                    <span x-text="index + 1"></span>
                                </template>
                            </div>

                            <!-- Label Langkah -->
                            <span class="text-xs transition-colors whitespace-nowrap"
                                  :class="{
                                      'text-[#0E4D3C] font-bold': currentStep === index + 1,
                                      'text-[#0E4D3C] font-semibold': currentStep > index + 1,
                                      'text-slate-400 font-medium': currentStep < index + 1
                                  }"
                                  x-text="st.label">
                            </span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Alert Kesalahan Validasi Backend (Jika ada) --}}
        @if ($errors->any())
            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs shadow-2xs space-y-1">
                <div class="flex items-center gap-2 font-bold text-rose-900">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Terdapat kendala pada data pengajuan Anda:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 ml-1 pt-0.5 text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- =========================================================================
             FORM UTAMA — SATU CARD BESAR STEP-BY-STEP (TANPA SCROLL KE BAWAH)
             ========================================================================= --}}
        <form id="submission-form" 
              action="{{ route('pengajuan.store') }}" 
              method="POST" 
              enctype="multipart/form-data"
              @submit="handleFormSubmit($event)">
            @csrf

            {{-- ==============================================================
                 PANDUAN & PERSYARATAN PENGAJUAN (QUICK-NOTICE GUIDE)
                 Format bersih, elegan, dan informatif sebelum pengaju memilih.
                 Data dinamis diambil langsung dari Pengaturan Sistem (Admin).
                 ============================================================== --}}
            @php
                $minDaysBefore = (int) setting('min_days_before', 3);
                $maxVideoSec = (int) setting('max_video_duration', 30);
                $templateDocUrl = setting('template_doc_url');
                $maxFileSize = (int) setting('max_file_size', 50);

                // Konversi durasi detik ke format ramah (contoh: 180 dtk -> 3 menit, jika 90 dtk -> 1.5 menit atau 90 dtk)
                if ($maxVideoSec >= 60) {
                    $minutes = $maxVideoSec / 60;
                    $durationText = ($maxVideoSec % 60 === 0) ? (int)$minutes . ' menit' : round($minutes, 1) . ' menit';
                } else {
                    $durationText = $maxVideoSec . ' dtk';
                }
            @endphp


            {{-- Langkah 1: Pilih Videotron & Panduan --}}
            @include('pengajuan.partials.wizard.step-1-videotron')

            {{-- Langkah 2: Detail Kegiatan & Jadwal Tayang --}}
            @include('pengajuan.partials.wizard.step-2-jadwal')

            {{-- Langkah 3: Unggah Berkas & Materi Visual --}}
            @include('pengajuan.partials.wizard.step-3-berkas')

            {{-- Langkah 4: Tinjau & Kirim Pengajuan --}}
            @include('pengajuan.partials.wizard.step-4-review')
        </form>

        {{-- Langkah 5: Konfirmasi Pengajuan Berhasil --}}
        @include('pengajuan.partials.wizard.step-5-konfirmasi')

    </div>
</div>

{{-- Alpine.js State, Kalender, Draf & Validasi Wizard --}}
@include('pengajuan.partials.wizard.scripts-state-calendar')
@include('pengajuan.partials.wizard.scripts-draft-validation')
@endsection