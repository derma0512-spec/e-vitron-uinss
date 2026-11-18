@extends('layouts.portal')

@section('title', 'Daftar Pengajuan Saya | E-VITRON UIN Siber Cirebon')

@section('content')
<div x-data="submissionDrawer()"
     @keydown.escape.window="if (showCancelModal) { if (!isCancelling) { closeCancelModal(); } } else if (isOpen) { closeDrawer(); }"
     class="w-full min-h-[calc(100vh-200px)] relative overflow-x-hidden" style="background:#F4F8F6;">

    {{-- Banner Peringatan Layanan Ditutup Sementara (Full-width tepat di bawah Navbar - Selaras 100% Persis Beranda) --}}
    @if(! setting('service_active', true))
        <div class="bg-gradient-to-r from-[#0F3D2E] via-[#14533D] to-[#0F3D2E] text-white px-4 py-2.5 text-xs sm:text-sm font-medium shadow-sm border-b border-[#D9A93B]/40">
            <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-10 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#D9A93B] text-[#0F3D2E] text-[11px] font-bold shrink-0">!</span>
                    <span class="leading-normal">
                        @php
                            $closedMsg = setting('service_closed_message', 'Layanan pengajuan penayangan videotron sedang ditutup sementara untuk pemeliharaan teknis berkala.');
                            if (! str_starts_with(strtolower(trim($closedMsg)), 'perhatian:')) {
                                $closedMsg = 'Perhatian: ' . $closedMsg;
                            }
                        @endphp
                        {{ $closedMsg }}
                    </span>
                </div>
            </div>
        </div>
    @endif
    {{-- Islamic Corner Ornaments Background --}}
    <div class="ornament-pattern" style="position:absolute;left:-40px;top:20px;height:240px;width:240px;opacity:0.35;pointer-events:none;"></div>
    <div class="ornament-pattern" style="position:absolute;right:-40px;top:80px;height:260px;width:260px;opacity:0.35;pointer-events:none;"></div>

    <div class="relative mx-auto max-w-[1400px] px-4 sm:px-10 lg:px-14 py-4 sm:py-8 space-y-3.5 sm:space-y-6 z-10">

        {{-- Header Section: Selaras dengan Beranda & Ajukan Penayangan --}}
        <div>
            {{-- Badge Emas (Disembunyikan di Mobile Agar Tidak Memakan Ruang Vertikal) --}}
            <span class="hidden sm:inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[10px] font-bold text-white shadow-xs"
                style="background:linear-gradient(90deg,#C9901A 0%,#DDB140 60%,#EDD088 100%);letter-spacing:0.01em;">
                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span>Manajemen Riwayat Pengajuan</span>
            </span>

            {{-- Judul Halaman & Tombol Aksi --}}
            <div class="mt-1 sm:mt-2 flex flex-col md:flex-row md:items-end justify-between gap-3 sm:gap-4">
                {{-- Judul + Tombol "+ Baru" Sejajar di Mobile --}}
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h1 class="font-montserrat font-extrabold text-[#0E4D3C] text-xl sm:text-3xl tracking-tight leading-tight">
                            Pengajuan Saya
                        </h1>
                        <p class="hidden sm:block mt-1 text-xs sm:text-[13px] leading-relaxed text-[#4E655F] max-w-xl">
                            Pantau proses verifikasi, perbaikan revisi, dan status penayangan videotron Anda.
                        </p>
                    </div>

                    {{-- Tombol Buat Pengajuan Baru Khusus Mobile (Kapsul Mini Hemat Ruang) --}}
                    @if(setting('service_active', true))
                    <a href="{{ route('pengajuan.create') }}"
                        class="sm:hidden group inline-flex h-8 items-center justify-center gap-1.5 rounded-full bg-[#D9A93B] hover:bg-[#C9901A] px-3.5 text-[11px] font-bold text-[#123F33] shadow-xs active:scale-95 transition-all select-none shrink-0 cursor-pointer">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Buat Pengajuan</span>
                    </a>
                    @endif
                </div>

                {{-- Action Toolbar: Kolom Pencarian Cepat & Tombol Buat Pengajuan Baru Desktop --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full md:w-auto shrink-0">
                    {{-- Kolom Pencarian Cepat (Live AJAX Search) --}}
                    <div class="relative w-full sm:w-64 md:w-72">
                        <div class="relative w-full">
                            {{-- Ikon Kaca Pembesar Statis --}}
                            <div class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 flex items-center z-10">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                                </svg>
                            </div>

                            <input x-ref="searchInput"
                                   type="text" 
                                   name="search" 
                                   x-model="search"
                                   x-on:input="handleSearchInput($event.target.value)"
                                   x-on:keydown.enter.prevent="clearTimeout(debounceTimer); performGridSearch(search)"
                                   x-on:keydown.escape="if (search) { clearSearch(); }"
                                   placeholder="Cari kode tiket, judul, atau videotron..." 
                                   autocomplete="off"
                                   class="w-full pl-9 pr-9 py-2 text-xs rounded-full bg-white/95 border border-[#0E4D3C]/20 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-[#0E4D3C] focus:ring-2 focus:ring-[#0E4D3C]/20 shadow-2xs transition-all">

                            {{-- Tombol Hapus (X) yang Bertransisi Jadi Mikro-Spinner Saat Diklik --}}
                            <button type="button" 
                                    x-show="Boolean(search && String(search).trim().length > 0) || isClearing" 
                                    x-cloak 
                                    x-on:click="if (!isClearing) clearSearch()" 
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 text-slate-400 hover:text-rose-600 transition-colors cursor-pointer rounded-full hover:bg-slate-100 flex items-center justify-center z-10" 
                                    title="Hapus kata kunci pencarian (Esc)"
                                    tabindex="-1">
                                {{-- Ikon Silang (X) saat standby --}}
                                <svg x-show="!isClearing" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                {{-- Mikro Spinner saat tombol ini diklik --}}
                                <svg x-show="isClearing" x-cloak class="w-3.5 h-3.5 animate-spin text-[#0E4D3C]" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="9" stroke="#CBD5E1" stroke-width="2.5"></circle>
                                    <path d="M12 3a9 9 0 0 1 9 9" stroke="#0E4D3C" stroke-width="2.5" stroke-linecap="round"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Tombol Buat Pengajuan Baru Khusus Layar Tablet / Desktop (>= sm) --}}
                    @if(setting('service_active', true))
                    <a href="{{ route('pengajuan.create') }}"
                        class="hidden sm:inline-flex group h-9 items-center justify-center gap-2 rounded-full bg-white/95 hover:bg-[#D9A93B] border border-[#0E4D3C]/20 hover:border-[#D9A93B] px-4 text-xs font-bold text-[#0E4D3C] hover:text-[#123F33] shadow-xs hover:shadow-md hover:shadow-[#D9A93B]/25 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 transition-all duration-300 select-none shrink-0 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0E4D3C]/30">
                        <span class="flex h-4.5 w-4.5 items-center justify-center rounded-full bg-[#E3F1EA] group-hover:bg-white/40 text-[#0E4D3C] group-hover:text-[#123F33] transition-all duration-300 group-hover:scale-105">
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </span>
                        <span>Buat Pengajuan Baru</span>
                    </a>
                    @else
                    <button type="button" disabled
                        title="Layanan pengajuan penayangan sedang ditutup sementara"
                        class="hidden sm:inline-flex h-9 items-center justify-center gap-2 rounded-full bg-slate-100/90 border border-slate-200/90 px-4 text-xs font-semibold text-slate-400 cursor-not-allowed select-none shrink-0 shadow-none">
                        <span class="flex h-4.5 w-4.5 items-center justify-center rounded-full bg-slate-200/60 text-slate-400">
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </span>
                        <span>Layanan Ditutup</span>
                    </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Filter Tab Status dengan Smooth Auto-Scroll into Center View --}}
        <div class="relative w-full">
            <div id="filter-tabs-container"
                 x-ref="filterTabsContainer"
                 class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto pb-1 pt-0.5 pr-4 sm:pr-0 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden scroll-smooth">
                @php
                    $filters = [
                        ['key' => null,         'label' => 'Semua',         'count' => $statusCounts['all'] ?? 0],
                        ['key' => 'menunggu',   'label' => 'Menunggu',      'count' => $statusCounts['menunggu'] ?? 0],
                        ['key' => 'disetujui',  'label' => 'Disetujui',     'count' => $statusCounts['disetujui'] ?? 0],
                        ['key' => 'revisi',     'label' => 'Perlu Revisi',  'count' => $statusCounts['revisi'] ?? 0],
                        ['key' => 'tayang',     'label' => 'Sedang Tayang', 'count' => $statusCounts['tayang'] ?? 0],
                        ['key' => 'selesai',    'label' => 'Selesai',       'count' => $statusCounts['selesai'] ?? 0],
                    ];
                    if (($statusCounts['ditolak'] ?? 0) > 0 || $currentStatus === 'ditolak') {
                        $filters[] = ['key' => 'ditolak', 'label' => 'Ditolak', 'count' => $statusCounts['ditolak'] ?? 0];
                    }
                @endphp

                @foreach($filters as $f)
                    @php
                        $isActive = ($currentStatus === $f['key']) || ($f['key'] === null && empty($currentStatus));
                        $filterParams = array_filter(['status' => $f['key'], 'search' => request('search')]);
                    @endphp
                    <a href="{{ route('pengajuan.index', $filterParams) }}"
                        id="filter-tab-{{ $f['key'] ?? 'all' }}"
                        @click.prevent="selectFilter('{{ $f['key'] ?? '' }}', $el)"
                        :class="(currentStatus === '{{ $f['key'] ?? '' }}' || (!currentStatus && '{{ $f['key'] ?? '' }}' === '')) ? 'active-filter-tab bg-[#0E4D3C] text-white shadow-sm' : 'border border-[#0E4D3C]/20 bg-white/90 text-[#0E4D3C] shadow-xs backdrop-blur-xs hover:border-[#0E4D3C] hover:bg-[#0E4D3C] hover:text-white hover:shadow-md hover:-translate-y-0.5 active:translate-y-0 active:scale-95'"
                        class="group inline-flex shrink-0 items-center gap-1.5 sm:gap-2 rounded-full px-3 py-1 sm:px-3.5 sm:py-1.5 text-[11px] sm:text-xs font-bold transition-all duration-200 select-none cursor-pointer">
                        <span>{{ $f['label'] }}</span>
                        <span :class="(currentStatus === '{{ $f['key'] ?? '' }}' || (!currentStatus && '{{ $f['key'] ?? '' }}' === '')) ? 'bg-[#D9A93B] text-[#123F33]' : 'bg-[#E3F1EA] text-[#0E4D3C] group-hover:bg-white group-hover:text-[#0E4D3C]'"
                              class="rounded-full px-1.5 sm:px-2 py-0.2 text-[9.5px] sm:text-[10px] transition-colors">
                            {{ $f['count'] }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>



        {{-- Submissions Grid: Container AJAX Live Update --}}
        <div id="submissions-container"
             x-ref="submissionsContainer"
             class="transition-opacity duration-200"
             :class="isSearchingGrid ? 'opacity-60 pointer-events-none' : 'opacity-100'">
            @include('pengajuan.partials.submissions-grid')
        </div>

{{-- Slide-Over Drawer Detail Pengajuan (Teleport Langsung ke Body Agar Menutupi Navbar Secara Total, Opsi A) --}}
        <template x-teleport="body">
            <div x-cloak x-show="isOpen"
                 class="relative z-[99990]"
                 role="dialog"
                 aria-modal="true">

                {{-- Backdrop Overlay (Menutupi Seluruh Layar Termasuk Navbar, Persis Panel Admin) --}}
                <div class="sipetron-drawer-backdrop"
                     x-show="isOpen"
                     @click="closeDrawer()"
                     @wheel.prevent.stop
                     @touchmove.prevent.stop
                     x-transition:enter="transition-opacity ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                </div>

                {{-- Slide-Over Drawer Panel (Persis Panel Admin: Fixed Top-Right, Width 580px, Height 100vh) --}}
                <div class="sipetron-drawer-panel"
                     x-show="isOpen"
                     @click.stop
                     x-transition:enter="transition transform ease-out duration-300"
                     x-transition:enter-start="translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transition transform ease-in duration-200"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="translate-x-full">

                    {{-- 1. Loading Skeleton saat data sedang diambil --}}
                    <div x-show="loading" class="flex-1 flex flex-col h-full bg-white">
                        <div class="sipetron-drawer-header">
                            <div class="flex items-center justify-between">
                                <div class="space-y-1.5">
                                    <div class="h-5 w-36 bg-slate-200 rounded-md animate-pulse"></div>
                                    <div class="h-3 w-28 bg-slate-100 rounded animate-pulse"></div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="h-6 w-20 bg-slate-200 rounded-full animate-pulse"></div>
                                    <div class="h-7 w-7 bg-slate-200 rounded-full animate-pulse"></div>
                                </div>
                            </div>
                        </div>
                        <div class="sipetron-drawer-body bg-[#F8FAF9]">
                            <div class="h-20 bg-amber-50/70 border border-amber-100 rounded-2xl animate-pulse"></div>
                            <div class="h-44 bg-white border border-slate-100 rounded-2xl p-5 space-y-3 animate-pulse">
                                <div class="h-4 w-1/3 bg-slate-200 rounded"></div>
                                <div class="h-10 bg-slate-100 rounded-xl"></div>
                                <div class="h-12 bg-slate-100 rounded-xl"></div>
                            </div>
                            <div class="h-56 bg-white border border-slate-100 rounded-2xl p-5 space-y-3 animate-pulse">
                                <div class="h-4 w-1/4 bg-slate-200 rounded"></div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="h-10 bg-slate-100 rounded-xl"></div>
                                    <div class="h-10 bg-slate-100 rounded-xl"></div>
                                </div>
                                <div class="h-16 bg-slate-100 rounded-xl"></div>
                            </div>
                        </div>
                        <div class="sipetron-drawer-footer flex justify-between items-center bg-white">
                            <div class="h-8 w-32 bg-slate-200 rounded-full animate-pulse"></div>
                            <div class="h-8 w-20 bg-slate-200 rounded-full animate-pulse"></div>
                        </div>
                    </div>

                    {{-- 2. Error State jika koneksi gagal --}}
                    <div x-show="!loading && errorMessage" class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-white">
                        <div class="h-14 w-14 rounded-full bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center mb-4">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-800" x-text="errorMessage"></h3>
                        <p class="text-xs text-slate-500 mt-1 max-w-sm">Pastikan koneksi internet Anda stabil atau silakan muat ulang.</p>
                        <div class="mt-5 flex items-center gap-3">
                            <button type="button" @click="openDrawer(ticketCode, true)"
                                    class="btn-hero-primary px-5 py-2 rounded-full text-xs font-bold text-[#123F33] cursor-pointer shadow-sm">
                                Coba Lagi
                            </button>
                            <button type="button" @click="closeDrawer()"
                                    class="btn-wizard-secondary px-5 py-2 rounded-full text-xs font-bold text-[#0E4D3C] cursor-pointer shadow-xs">
                                Tutup
                            </button>
                        </div>
                    </div>

                    {{-- 3. Drawer Content Loaded (Injected HTML) --}}
                    <div x-show="!loading && !errorMessage"
                         x-ref="drawerContent"
                         class="flex-1 flex flex-col h-full overflow-hidden"
                         x-html="contentHtml">
                    </div>
                </div>
            </div>
        </template>

        {{-- MODAL KONFIRMASI PEMBATALAN PENGAJUAN (Enterprise Standard & Accessible) --}}
        <template x-teleport="body">
            <div x-cloak x-show="showCancelModal"
                 @keydown.escape.window="if (showCancelModal && !isCancelling) closeCancelModal()"
                 class="fixed inset-0 z-[99995] flex items-center justify-center p-4 sm:p-6"
                 role="dialog"
                 aria-modal="true"
                 aria-labelledby="cancel-modal-title"
                 aria-describedby="cancel-modal-desc">

                {{-- Backdrop Gelap Lembut --}}
                <div x-show="showCancelModal"
                     @click="if (!isCancelling) closeCancelModal()"
                     x-transition:enter="transition-opacity ease-out duration-200 motion-reduce:transition-none"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity ease-in duration-150 motion-reduce:transition-none"
                     x-transition:leave-start="opacity-100"
                     class="fixed inset-0 bg-black/60 cursor-pointer"
                     style="backdrop-filter: none !important; -webkit-backdrop-filter: none !important;"></div>

                {{-- Kotak Modal Dialog (Clean Enterprise Style matching user mockup) --}}
                <div x-show="showCancelModal"
                     @click.outside="if (!isCancelling) closeCancelModal()"
                     @keydown.tab="trapFocus($event)"
                     x-ref="cancelModalDialog"
                     x-transition:enter="transition ease-out duration-200 motion-reduce:transition-none"
                     x-transition:enter-start="opacity-0 scale-[0.98] -translate-y-1"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150 motion-reduce:transition-none"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-[0.98] -translate-y-1"
                     class="relative w-full max-w-[460px] rounded-2xl bg-white p-6 shadow-xl border border-slate-200/80 z-10 text-left">

                    {{-- 1. HEADER ROW: Icon + Title + Close Button --}}
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3.5">
                            {{-- Warning Icon Badge (Square Rounded) --}}
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600 border border-rose-100" aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>

                            {{-- Title & Subtitle --}}
                            <div>
                                <h3 id="cancel-modal-title" class="text-base font-bold text-slate-900 leading-snug">
                                    Batalkan pengajuan?
                                </h3>
                                <p id="cancel-modal-desc" class="text-xs text-slate-500 mt-0.5 leading-normal">
                                    Pengajuan yang dibatalkan tidak dapat diproses kembali.
                                </p>
                            </div>
                        </div>

                        {{-- Close Button (X) - Sama Persis dengan Drawer Detail Pengajuan --}}
                        <button type="button"
                                @click="closeCancelModal()"
                                :disabled="isCancelling"
                                class="h-7 w-7 rounded-full border border-slate-200 bg-slate-100 text-slate-500 hover:text-slate-800 hover:bg-slate-200 hover:border-slate-300 active:bg-slate-300 flex items-center justify-center transition-all cursor-pointer shadow-2xs disabled:opacity-40 disabled:cursor-not-allowed shrink-0 active:scale-95"
                                title="Tutup (Esc)">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- 2. DIVIDER --}}
                    <div class="h-px bg-slate-100 my-4" role="separator"></div>

                    {{-- 3. DETAIL PENGAJUAN CARD --}}
                    <div class="rounded-xl border border-slate-200/80 bg-slate-50/60 p-3.5 sm:p-4">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">
                            DETAIL PENGAJUAN
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="font-bold text-slate-900 text-sm truncate" x-text="cancelTitle"></div>
                                <div class="text-xs text-slate-500 font-mono mt-0.5" x-text="cancelTicketCode"></div>
                            </div>
                            <span class="inline-flex shrink-0 items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 border border-slate-200">
                                Menunggu
                            </span>
                        </div>
                    </div>

                    {{-- 4. WARNING NOTE (Inline icon & text) --}}
                    <div class="flex items-start gap-2 mt-3.5 text-xs text-rose-700">
                        <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span class="leading-relaxed">
                            Pastikan kembali sebelum melanjutkan. Pengajuan yang sudah dibatalkan tidak dapat diaktifkan kembali.
                        </span>
                    </div>

                    {{-- 5. ERROR ALERT (If server responds with failure) --}}
                    <div x-cloak x-show="cancelErrorMessage" class="mt-3 p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-700 flex items-start gap-2" role="alert">
                        <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="leading-relaxed font-medium" x-text="cancelErrorMessage"></span>
                    </div>

                    {{-- 6. ACTION BUTTONS (Symmetrical 2-Column Grid - Sleek & Balanced) --}}
                    <div class="mt-5 grid grid-cols-2 gap-3">
                        <button type="button"
                                x-ref="cancelDismissBtn"
                                @click="closeCancelModal()"
                                :disabled="isCancelling"
                                class="group h-10 inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-slate-100 hover:bg-slate-200 hover:border-slate-300 active:bg-slate-300/80 text-slate-700 hover:text-slate-900 text-xs sm:text-[13px] font-bold transition-all duration-150 focus:outline-none focus-visible:border-slate-400 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer select-none active:scale-[0.98]">
                            <svg class="w-4 h-4 text-slate-500 transition-transform duration-150 group-hover:-translate-x-0.5 group-hover:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            <span>Kembali</span>
                        </button>

                        <button type="button"
                                @click="executeCancel()"
                                :disabled="isCancelling"
                                class="group h-10 inline-flex items-center justify-center gap-2 rounded-xl bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white text-xs sm:text-[13px] font-bold shadow-sm shadow-rose-600/25 transition-all duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500/30 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer select-none active:scale-[0.98]">
                            <svg x-show="isCancelling" x-cloak class="animate-spin -ml-1 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <svg x-show="!isCancelling" class="w-4 h-4 text-white/90 transition-transform duration-150 group-hover:scale-105" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <span x-text="isCancelling ? 'Membatalkan...' : 'Ya, Batalkan'">Ya, Batalkan</span>
                        </button>
                    </div>
                </div>
            </div>
        </template>

    </div>
</div>

{{-- Script Alpine.js Laci Detail, Pencarian AJAX & Pembatalan Pengajuan --}}
@include('pengajuan.partials.index.scripts-drawer')
@endsection