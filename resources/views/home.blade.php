@extends('layouts.portal')

@section('title', 'Beranda | Pengajuan Videotron UIN Siber')

@section('content')
<div class="w-full overflow-x-clip" style="background:#F4F8F6;">

  {{-- Banner Pengumuman Resmi (Jika diaktifkan pada Pengaturan Sistem) --}}
  @if(setting('announcement_active', false) && setting('announcement_banner_text'))
    <div class="bg-gradient-to-r from-[#0F3D2E] via-[#14533D] to-[#0F3D2E] text-white px-4 py-2.5 text-xs sm:text-sm font-medium shadow-sm border-b border-[#D9A93B]/40">
      <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-10 flex items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
          <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#D9A93B] text-[#0F3D2E] text-[11px] font-bold shrink-0">!</span>
          <span class="leading-normal">{{ setting('announcement_banner_text') }}</span>
        </div>
      </div>
    </div>
  @endif

  {{-- ====================================================
       HERO SECTION — Full-bleed, Responsive Mobile & Desktop
  ===================================================== --}}
  <section class="relative bg-[#EEF4F1] h-[250px] sm:h-[270px] md:h-[290px] overflow-hidden flex flex-col justify-center">

    {{-- Foto Kampus + Videotron --}}
    <img
      src="{{ asset('images/campus-videotron.png?v=20261027') }}"
      alt="Videotron UIN SSC di area kampus Syekh Nurjati Cirebon"
      class="absolute inset-y-0 right-0 h-full w-full md:w-[62%] object-cover object-center pointer-events-none"
      style="mask-image:linear-gradient(90deg,transparent 0%,black 28%);-webkit-mask-image:linear-gradient(90deg,transparent 0%,black 28%);"
    />

    {{-- Gradient Overlay Khusus Mobile (< md): Foto gedung kampus tetap kelihatan tapi teks 100% tajam --}}
    <div
      aria-hidden="true"
      class="absolute inset-0 md:hidden bg-gradient-to-r from-[#EEF4F1]/95 via-[#EEF4F1]/90 to-[#EEF4F1]/65 pointer-events-none"
    ></div>

    {{-- Gradient Desktop (md:block) --}}
    <div
      aria-hidden="true"
      class="hidden md:block absolute inset-0 pointer-events-none"
      style="background:linear-gradient(90deg,#EEF4F1 0%,#EEF4F1 32%,rgba(238,244,241,0.88) 44%,rgba(238,244,241,0.28) 60%,transparent 74%);"
    ></div>

    {{-- Islamic Corner Ornaments --}}
    <div class="ornament-pattern pointer-events-none" style="position:absolute;left:-60px;bottom:-40px;height:180px;width:180px;opacity:0.35;"></div>
    <div class="ornament-pattern pointer-events-none" style="position:absolute;right:-50px;top:-50px;height:180px;width:180px;opacity:0.35;"></div>

    {{-- Konten Teks Hero --}}
    <div class="relative mx-auto w-full max-w-[1400px] px-5 sm:px-10 lg:px-14 flex flex-col justify-center z-10">
      <div class="max-w-[500px]">
        {{-- Badge Layanan Ramping --}}
        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[9.5px] sm:text-[10px] font-bold text-white shadow-xs"
          style="background:linear-gradient(90deg,#C9901A 0%,#DDB140 60%,#EDD088 100%);letter-spacing:0.01em;">
          Layanan Videotron Kampus
        </span>

        {{-- Heading Utama: Ringkas, Tegas, & Tidak Memakan Layar --}}
        <h1 class="mt-2 font-extrabold leading-[1.15] tracking-tight text-[#0E4D3C] text-[21px] sm:text-[26px] md:text-[34px]">
          Pengajuan Penayangan<br class="hidden sm:inline"> Videotron Kampus
        </h1>

        {{-- Deskripsi Padat --}}
        <p class="mt-1.5 text-[12px] sm:text-[13px] leading-snug sm:leading-relaxed text-[#3B544D] max-w-[390px] md:max-w-[440px]">
          Tayangkan publikasi kegiatan dan pengumuman resmi di layar videotron UIN SSC secara cepat dan terintegrasi.
        </p>

        {{-- CTA Buttons: Sejajar Horizontal 65% : 35% di Mobile --}}
        <div class="mt-3.5 flex items-center gap-2 sm:gap-3 max-w-[380px] sm:max-w-none">
          {{-- Tombol Utama: 65% Lebar di Mobile, Dominan & Empuk --}}
          <a href="{{ route('pengajuan.create') }}"
            class="group btn-hero-primary flex-1 sm:flex-initial inline-flex h-10 sm:h-10 items-center justify-center gap-2 rounded-xl sm:rounded-full px-4 sm:px-5 text-xs sm:text-sm font-bold text-[#123F33] select-none shadow-sm">
            <span class="truncate">Ajukan Sekarang</span>
            <svg class="h-3.5 w-3.5 shrink-0 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
          </a>

          {{-- Tombol Sekunder: 35% Lebar di Mobile, Ramping & Bersih --}}
          <a href="{{ route('panduan') }}"
            class="group btn-hero-secondary shrink-0 inline-flex h-10 items-center justify-center gap-1.5 rounded-xl sm:rounded-full px-3.5 sm:px-4 text-xs sm:text-sm font-bold select-none">
            <svg class="h-4 w-4 shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
            </svg>
            <span>Panduan</span>
          </a>
        </div>
      </div>
    </div>

    {{-- Feature Bar Desktop (md:block) — Modern Rounded Rectangle --}}
    <div class="absolute hidden md:block z-20"
      style="bottom: 20px; right: 16px; width: min(50vw, 580px); border-radius: 50px; background-color: rgba(255, 255, 255, 0.82); border: none; box-shadow: none; backdrop-filter: none; -webkit-backdrop-filter: none; padding: 12px 36px; mask-image: linear-gradient(90deg, transparent 0%, black 12%, black 88%, transparent 100%); -webkit-mask-image: linear-gradient(90deg, transparent 0%, black 12%, black 88%, transparent 100%);">

      {{-- Grid 3 Kolom Sama Lebar dengan Divider Vertikal --}}
      <div class="relative grid grid-cols-3 items-center" style="grid-template-columns: repeat(3, minmax(0, 1fr));">

        {{-- Kolom 1: Mudah dan Cepat --}}
        <div class="flex items-center justify-center gap-2.5 px-2">
          <span class="flex h-[38px] w-[38px] shrink-0 items-center justify-center rounded-full text-white"
            style="background: linear-gradient(145deg, #D9A93B 0%, #B87C1A 100%); box-shadow: 0 3px 10px rgba(196, 138, 37, 0.28);">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
          </span>
          <div class="text-[12px] font-semibold leading-[1.38] text-[#0E4D3C]">
            Mudah<br>dan Cepat
          </div>
        </div>

        {{-- Divider 1 (antara kolom 1 dan 2) --}}
        <div class="absolute left-1/3 top-1/2 -translate-y-1/2 w-px h-[32px] pointer-events-none" style="background-color: rgba(20, 70, 65, 0.25);"></div>

        {{-- Kolom 2: Tersedia di Berbagai Lokasi --}}
        <div class="flex items-center justify-center gap-2.5 px-2">
          <span class="flex h-[38px] w-[38px] shrink-0 items-center justify-center rounded-full text-white"
            style="background: linear-gradient(145deg, #D9A93B 0%, #B87C1A 100%); box-shadow: 0 3px 10px rgba(196, 138, 37, 0.28);">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
          </span>
          <div class="text-[12px] font-semibold leading-[1.38] text-[#0E4D3C]">
            Tersedia di<br>Berbagai Lokasi
          </div>
        </div>

        {{-- Divider 2 (antara kolom 2 dan 3) --}}
        <div class="absolute left-2/3 top-1/2 -translate-y-1/2 w-px h-[32px] pointer-events-none" style="background-color: rgba(20, 70, 65, 0.25);"></div>

        {{-- Kolom 3: Terintegrasi dengan Sistem --}}
        <div class="flex items-center justify-center gap-2.5 px-2">
          <span class="flex h-[38px] w-[38px] shrink-0 items-center justify-center rounded-full text-white"
            style="background: linear-gradient(145deg, #D9A93B 0%, #B87C1A 100%); box-shadow: 0 3px 10px rgba(196, 138, 37, 0.28);">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.3">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </span>
          <div class="text-[12px] font-semibold leading-[1.38] text-[#0E4D3C]">
            Terintegrasi<br>dengan Sistem
          </div>
        </div>

      </div>
    </div>
  </section>

  {{-- Mobile Feature Bar (< md) — Floating Pill Mewah & Simetris Penghubung Banner --}}
  <div class="md:hidden mx-4 -mt-6 mb-2 relative z-30 select-none">
    <div class="rounded-full bg-white/95 border border-slate-200/90 shadow-md backdrop-blur-sm px-3 py-2.5">
      <div class="relative grid grid-cols-3 items-center">

        {{-- Kolom 1: Mudah & Cepat --}}
        <div class="flex items-center justify-center gap-1.5 px-1">
          <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-white shadow-xs"
            style="background: linear-gradient(145deg, #D9A93B 0%, #B87C1A 100%);">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.3">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
          </span>
          <div class="text-[10px] font-bold leading-tight text-[#0E4D3C] text-left">
            Mudah<br>& Cepat
          </div>
        </div>

        {{-- Divider 1 --}}
        <div class="absolute left-1/3 top-1/2 -translate-y-1/2 w-px h-5 pointer-events-none bg-slate-200"></div>

        {{-- Kolom 2: Multi Lokasi --}}
        <div class="flex items-center justify-center gap-1.5 px-1">
          <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-white shadow-xs"
            style="background: linear-gradient(145deg, #D9A93B 0%, #B87C1A 100%);">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.3">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
          </span>
          <div class="text-[10px] font-bold leading-tight text-[#0E4D3C] text-left">
            Multi<br>Lokasi
          </div>
        </div>

        {{-- Divider 2 --}}
        <div class="absolute left-2/3 top-1/2 -translate-y-1/2 w-px h-5 pointer-events-none bg-slate-200"></div>

        {{-- Kolom 3: Terintegrasi --}}
        <div class="flex items-center justify-center gap-1.5 px-1">
          <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-white shadow-xs"
            style="background: linear-gradient(145deg, #D9A93B 0%, #B87C1A 100%);">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.4">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </span>
          <div class="text-[10px] font-bold leading-tight text-[#0E4D3C] text-left">
            Auto<br>Sistem
          </div>
        </div>

      </div>
    </div>
  </div>

  {{-- ====================================================
       SECTION: Pengajuan Terakhir Saya
  ===================================================== --}}
  <section aria-labelledby="recent-title" class="relative overflow-hidden mx-auto max-w-[1400px] px-4 sm:px-10 lg:px-14 pt-4 sm:pt-6 pb-8 sm:pb-14" style="z-index:1;">

    {{-- Watermark Ornamen Sudut Halaman (bawah kiri dan kanan) --}}
    <div class="ornament-pattern" style="position:absolute;left:-30px;bottom:-20px;height:160px;width:160px;opacity:0.35;pointer-events:none;"></div>
    <div class="ornament-pattern" style="position:absolute;right:-30px;bottom:-20px;height:160px;width:160px;opacity:0.35;pointer-events:none;"></div>

    {{-- Header Section: Rapi & 1 Baris Sejajar di Mobile Maupun Desktop --}}
    <div class="flex items-center justify-between mb-3.5">
      <div class="flex items-center gap-2 min-w-0 pr-2">
        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-white shadow-sm" style="background:#D9A93B;">
          <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
          </svg>
        </span>
        <h2 id="recent-title" class="text-[13.5px] sm:text-lg font-black tracking-tight text-[#0E4D3C] truncate">
          Pengajuan Terakhir Saya
        </h2>
      </div>
      <a href="{{ route('pengajuan.index') }}"
        class="inline-flex shrink-0 items-center gap-1 sm:gap-1.5 whitespace-nowrap rounded-full border border-[#0E4D3C]/20 bg-white/90 px-2.5 py-1 sm:px-3 text-[11px] sm:text-xs font-bold text-[#0E4D3C] shadow-xs backdrop-blur-xs transition-all duration-200 hover:border-[#0E4D3C] hover:bg-[#0E4D3C] hover:text-white hover:shadow-md hover:-translate-y-0.5 active:translate-y-0 active:scale-95">
        <span>Lihat semua</span>
        <svg class="h-2.5 w-2.5 sm:h-3 sm:w-3 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
        </svg>
      </a>
    </div>

    {{-- Carousel Wrapper dengan Alpine.js untuk Tracking Active Index Dot di Mobile --}}
    <div x-data="{
      activeIndex: 0,
      onScroll(el) {
        const card = el.querySelector('a');
        if (!card) return;
        const cardWidth = card.offsetWidth + 12; // card width + gap-3
        this.activeIndex = Math.min(
          {{ max(0, $recentSubmissions->count() - 1) }},
          Math.max(0, Math.round(el.scrollLeft / cardWidth))
        );
      }
    }" class="relative">

      {{-- Container: Horizontal Carousel dengan Magnetic Snap di Mobile (< md) & Grid 3 Kolom di Desktop (>= md) --}}
      <div 
        @scroll.passive="onScroll($el)"
        class="flex md:grid md:grid-cols-2 lg:grid-cols-3 overflow-x-auto md:overflow-visible gap-3 sm:gap-4 pb-2 md:pb-0 snap-x snap-mandatory scroll-smooth -mx-4 px-4 md:mx-0 md:px-0 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
        
        @foreach($recentSubmissions as $index => $sub)
          @php
            $statusMap = [
              'menunggu'  => ['label'=>'Menunggu',       'bg'=>'#FEF3C7','border'=>'#FDE68A','color'=>'#92400E','icon'=>'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
              'tayang'    => ['label'=>'Sedang Tayang',  'bg'=>'#DCFCE7','border'=>'#BBF7D0','color'=>'#15803D','icon'=>'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
              'terjadwal' => ['label'=>'Disetujui',      'bg'=>'#CCFBF1','border'=>'#99F6E4','color'=>'#0F766E','icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
              'disetujui' => ['label'=>'Disetujui',      'bg'=>'#CCFBF1','border'=>'#99F6E4','color'=>'#0F766E','icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
              'revisi'    => ['label'=>'Perlu Revisi',   'bg'=>'#FFEDD5','border'=>'#FDBA74','color'=>'#C2410C','icon'=>'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
              'ditolak'   => ['label'=>'Ditolak',        'bg'=>'#FEE2E2','border'=>'#FECACA','color'=>'#991B1B','icon'=>'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
              'dibatalkan'=> ['label'=>'Dibatalkan',     'bg'=>'#F1F5F9','border'=>'#E2E8F0','color'=>'#64748B','icon'=>'M6 18L18 6M6 6l12 12'],
              'selesai'   => ['label'=>'Selesai',        'bg'=>'#EEF2FF','border'=>'#C7D2FE','color'=>'#4338CA','icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ];
            $dispStatus = $sub->display_status;
            $s = $statusMap[$dispStatus] ?? ($statusMap[$sub->status] ?? $statusMap['menunggu']);
            $videotron = $sub->videotrons->first();
            $otherVideotronsCount = max(0, $sub->videotrons->count() - 1);
          @endphp

          <a href="{{ route('pengajuan.show', $sub->ticket_code) }}"
            class="shrink-0 {{ $recentSubmissions->count() === 1 ? 'w-full' : 'w-[84vw] max-w-[325px]' }} md:w-auto md:max-w-none snap-start relative flex flex-col justify-between overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-lg focus:outline-none focus-visible:outline-none focus:ring-0 outline-none select-none cursor-pointer rounded-2xl md:rounded-[18px] p-4 sm:p-5"
            style="background:linear-gradient(145deg, rgba(255,255,255,0.98) 0%, rgba(252,254,252,0.92) 60%, rgba(255,255,255,0.96) 100%);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid rgba(226,232,240,0.85);box-shadow:0 2px 6px rgba(14,77,60,0.04), 0 8px 24px -4px rgba(14,77,60,0.08);outline:none;">

            {{-- Watermark ornamen di sudut kartu, tembus pandang halus --}}
            <div class="ornament-pattern" style="position:absolute;right:-18px;bottom:-18px;height:75px;width:75px;opacity:0.15;pointer-events:none;"></div>

            {{-- Bagian Atas: Icon Monitor + Judul + Status Badge --}}
            <div>
              <div class="flex items-start gap-2 sm:gap-2.5">
                <span class="flex h-8 w-8 md:h-9 md:w-9 shrink-0 items-center justify-center rounded-[10px] text-[#0E4D3C] shadow-2xs" style="background:#E3F1EA;border:1px solid rgba(14,77,60,0.08);">
                  <svg class="h-4 w-4 md:h-4.5 md:w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.85">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                  </svg>
                </span>
                <div class="flex-1 min-w-0 pr-0.5">
                  <h3 class="text-[13px] font-bold leading-[1.25] text-[#0E4D3C] line-clamp-1 md:line-clamp-2" title="{{ $sub->title }}">
                    <span class="transition-colors duration-200 hover:text-[#D9A93B]">{{ $sub->title }}</span>
                  </h3>
                  {{-- Format Konten Tag (Poster / Video) --}}
                  <div class="mt-1 flex items-center gap-1.5">
                    @if($sub->content_type === 'video')
                      <span class="inline-flex items-center gap-1 rounded-md px-1.5 py-0.5 text-[8.5px] md:text-[9px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                        <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                          <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Video @if($sub->video_duration_seconds) ({{ $sub->video_duration_seconds }}s) @endif
                      </span>
                    @else
                      <span class="inline-flex items-center gap-1 rounded-md px-1.5 py-0.5 text-[8.5px] md:text-[9px] font-semibold bg-emerald-50 text-[#0E4D3C] border border-emerald-200">
                        <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Poster
                      </span>
                    @endif
                  </div>
                </div>
                {{-- Status Badge --}}
                <span class="inline-flex shrink-0 items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold shadow-xs"
                  style="background:{{ $s['bg'] }};color:{{ $s['color'] }};border:1px solid {{ $s['border'] }};">
                  <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $s['icon'] }}"/>
                  </svg>
                  <span>{{ $s['label'] }}</span>
                </span>
              </div>

              {{-- Detail Lokasi & Periode --}}
              <dl class="mt-2.5 space-y-1 text-xs text-[#4E615B]">
                {{-- Videotron --}}
                <div class="flex items-start gap-1.5">
                  <dt class="mt-0.5 shrink-0 text-slate-400">
                    <svg class="h-3 w-3 text-[#3D6B5C]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="sr-only">Videotron</span>
                  </dt>
                  <dd class="leading-tight truncate">
                    <span class="block text-[8.5px] font-bold text-slate-400 uppercase tracking-wider">VIDEOTRON</span>
                    <strong class="text-[10.5px] md:text-[11px] font-semibold text-slate-700 truncate block">
                      {{ $videotron?->location ?? 'Kampus Utama' }} – {{ $videotron?->name ?? 'Videotron Masjid' }}
                      @if($otherVideotronsCount > 0)
                        <span class="ml-0.5 text-[9.5px] font-medium text-emerald-700 bg-emerald-50 px-1 py-0.2 rounded border border-emerald-200">
                          +{{ $otherVideotronsCount }}
                        </span>
                      @endif
                    </strong>
                  </dd>
                </div>
                {{-- Periode --}}
                <div class="flex items-start gap-1.5">
                  <dt class="mt-0.5 shrink-0 text-slate-400">
                    <svg class="h-3 w-3 text-[#3D6B5C]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="sr-only">Periode</span>
                  </dt>
                  <dd class="leading-tight">
                    <span class="block text-[8.5px] font-bold text-slate-400 uppercase tracking-wider">PERIODE PENAYANGAN</span>
                    <strong class="text-[10.5px] md:text-[11px] font-semibold text-slate-700">
                      {{ $sub->start_date->format('d M') }} – {{ $sub->end_date->format('d M Y') }}
                      <span class="text-[9.5px] text-slate-400 font-normal">({{ $sub->start_date->diffInDays($sub->end_date) + 1 }}h)</span>
                    </strong>
                  </dd>
                </div>
              </dl>
            </div>

            {{-- Bawah: Link Detail + Nomor Tiket --}}
            <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-center justify-between">
              <span class="inline-flex items-center gap-1 text-[11px] font-bold text-[#0E4D3C] transition-colors duration-200 hover:text-[#D9A93B]">
                <span>Detail</span>
                <svg class="h-2.5 w-2.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
              </span>
              <span class="text-[10px] font-mono font-bold text-slate-400">
                {{ $sub->ticket_code }}
              </span>
            </div>
          </a>
        @endforeach

        {{-- Spacer agar kartu terakhir pada mobile carousel memiliki margin kanan yang pas --}}
        <div class="shrink-0 w-1 md:hidden pointer-events-none" aria-hidden="true"></div>

        {{-- Placeholder jika belum ada pengajuan --}}
        @if($recentSubmissions->isEmpty())
          <div class="col-span-3 w-full flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-[#C8DDD6] bg-[#F7FAF8]/80 py-10 text-center backdrop-blur-sm">
            <svg class="h-10 w-10 text-[#A5C4B8] mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-sm font-bold text-[#3D6B5C]">Belum ada pengajuan</p>
            <p class="mt-1 text-xs text-[#6B8C82]">Ajukan penayangan pertama kamu sekarang.</p>
            <a href="{{ route('pengajuan.create') }}"
              class="mt-4 inline-flex h-9 items-center gap-2 rounded-full px-4 text-xs font-bold text-[#123F33]"
              style="background:#D9A93B;">
              Ajukan Sekarang
            </a>
          </div>
        @endif
      </div>{{-- end container --}}

      {{-- Dots Indicator Khusus Mobile (Muncul jika ada lebih dari 1 pengajuan) --}}
      @if($recentSubmissions->count() > 1)
        <div class="flex items-center justify-center gap-1.5 mt-3.5 md:hidden select-none">
          @foreach($recentSubmissions as $index => $sub)
            <span 
              class="h-1.5 rounded-full transition-all duration-300"
              :class="activeIndex === {{ $index }} ? 'w-5 bg-[#0E4D3C]' : 'w-1.5 bg-slate-300/80'"
            ></span>
          @endforeach
        </div>
      @endif

    </div>{{-- end x-data --}}
  </section>

</div>
@endsection
