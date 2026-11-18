{{-- Submissions Grid: Desain Kartu Mengambang & Frosted Glass Selaras Persis Beranda --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-5">
    @foreach($submissions as $sub)
        @php
            $statusMap = [
                'menunggu'  => ['label' => 'Menunggu',       'bg' => '#FEF3C7', 'border' => '#FDE68A', 'color' => '#92400E', 'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                'tayang'    => ['label' => 'Sedang Tayang',  'bg' => '#DCFCE7', 'border' => '#BBF7D0', 'color' => '#15803D', 'icon' => 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                'terjadwal' => ['label' => 'Disetujui',      'bg' => '#CCFBF1', 'border' => '#99F6E4', 'color' => '#0F766E', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                'disetujui' => ['label' => 'Disetujui',      'bg' => '#CCFBF1', 'border' => '#99F6E4', 'color' => '#0F766E', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                'revisi'    => ['label' => 'Perlu Revisi',   'bg' => '#FFEDD5', 'border' => '#FDBA74', 'color' => '#C2410C', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                'ditolak'   => ['label' => 'Ditolak',        'bg' => '#FEE2E2', 'border' => '#FECACA', 'color' => '#991B1B', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
                'dibatalkan'=> ['label' => 'Dibatalkan',     'bg' => '#F1F5F9', 'border' => '#E2E8F0', 'color' => '#64748B', 'icon' => 'M6 18L18 6M6 6l12 12'],
                'selesai'   => ['label' => 'Selesai',        'bg' => '#EEF2FF', 'border' => '#C7D2FE', 'color' => '#4338CA', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ];
            $dispStatus = $sub->display_status;
            $s = $statusMap[$dispStatus] ?? ($statusMap[$sub->status] ?? $statusMap['menunggu']);
            $videotron = $sub->videotrons->first();
            $otherVideotronsCount = max(0, $sub->videotrons->count() - 1);
            $hasTroubledVideotron = $sub->videotrons->contains(fn ($v) => in_array($v->condition, ['gangguan', 'dalam_perbaikan']) || ! (bool)$v->is_active);

            $sDate = $sub->start_date?->locale('id');
            $eDate = $sub->end_date?->locale('id');
            if ($sDate && $eDate) {
                if ($sDate->isSameDay($eDate)) {
                    $pFormatted = $sDate->translatedFormat('d M Y');
                } elseif ($sDate->isSameMonth($eDate) && $sDate->isSameYear($eDate)) {
                    $pFormatted = $sDate->translatedFormat('d') . ' – ' . $eDate->translatedFormat('d M Y');
                } elseif ($sDate->isSameYear($eDate)) {
                    $pFormatted = $sDate->translatedFormat('d M') . ' – ' . $eDate->translatedFormat('d M Y');
                } else {
                    $pFormatted = $sDate->translatedFormat('d M Y') . ' – ' . $eDate->translatedFormat('d M Y');
                }
                $durasi = $sDate->diffInDays($eDate) + 1;
            } else {
                $pFormatted = '-';
                $durasi = 0;
            }
        @endphp

        <a id="submission-card-{{ $sub->ticket_code }}"
            href="{{ route('pengajuan.show', $sub->ticket_code) }}"
            @click.prevent="openDrawer('{{ $sub->ticket_code }}')"
            class="relative flex flex-col justify-between overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-xl focus:outline-none focus-visible:outline-none focus:ring-0 outline-none block select-none cursor-pointer p-3.5 sm:p-5 rounded-[16px] sm:rounded-[18px]"
            :class="(isOpen && activeTicketCode === '{{ $sub->ticket_code }}') ? 'sipetron-card-active' : ''"
            style="background:linear-gradient(145deg, rgba(255,255,255,0.97) 0%, rgba(252,254,252,0.88) 60%, rgba(255,255,255,0.94) 100%);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,0.98);box-shadow:0 2px 4px rgba(14,77,60,0.04), 0 6px 20px -3px rgba(14,77,60,0.09), 0 12px 32px -6px rgba(14,77,60,0.06), inset 0 1px 0 rgba(255,255,255,1);outline:none;">

            {{-- Watermark ornamen di sudut kartu, tembus pandang halus persis Beranda --}}
            <div class="ornament-pattern" style="position:absolute;right:-18px;bottom:-18px;height:80px;width:80px;opacity:0.18;pointer-events:none;"></div>

            {{-- Bagian Atas: Icon Monitor + Judul + Status Badge --}}
            <div>
                <div class="flex items-start gap-2.5">
                    <span class="flex h-8 w-8 sm:h-10 sm:w-10 shrink-0 items-center justify-center rounded-lg sm:rounded-xl text-[#0E4D3C] shadow-2xs sm:shadow-sm"
                        style="background:#E3F1EA;border:1px solid rgba(14,77,60,0.08);">
                        <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    <div class="flex-1 min-w-0 pr-0.5">
                        <div class="flex items-start justify-between gap-1.5">
                            <h3 class="text-xs sm:text-[13px] font-bold leading-snug sm:leading-[1.25] text-[#0E4D3C] line-clamp-1 sm:line-clamp-2">
                                <span class="transition-colors duration-200 hover:text-[#D9A93B]">{{ $sub->title }}</span>
                            </h3>

                            {{-- Status Badge (Compact di Mobile) --}}
                            <span class="inline-flex shrink-0 items-center gap-1 sm:gap-1.5 rounded-full px-2 py-0.5 sm:px-2.5 text-[9.5px] sm:text-[10.5px] font-bold shadow-2xs sm:shadow-xs"
                                style="background:{{ $s['bg'] }};color:{{ $s['color'] }};border:1px solid {{ $s['border'] }};">
                                <svg class="h-2 w-2 sm:h-2.5 sm:w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $s['icon'] }}"/>
                                </svg>
                                <span>{{ $s['label'] }}</span>
                            </span>
                        </div>

                        {{-- Format Konten Tag (Poster / Video) + Badge Perangkat Terkendala Khusus Desktop --}}
                        <div class="mt-0.5 sm:mt-1 flex items-center gap-1.5 flex-wrap">
                            @if($sub->content_type === 'video')
                                <span class="inline-flex items-center gap-1 rounded-md px-1.5 py-0.2 sm:py-0.5 text-[8.5px] sm:text-[9px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                    <svg class="h-2 w-2 sm:h-2.5 sm:w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Video @if($sub->video_duration_seconds) ({{ $sub->video_duration_seconds }}s) @endif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-md px-1.5 py-0.2 sm:py-0.5 text-[8.5px] sm:text-[9px] font-semibold bg-emerald-50 text-[#0E4D3C] border border-emerald-200">
                                    <svg class="h-2 w-2 sm:h-2.5 sm:w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Poster
                                </span>
                            @endif

                            @if($hasTroubledVideotron && in_array($dispStatus, ['menunggu', 'terjadwal', 'tayang']))
                                <span class="hidden sm:inline-flex items-center gap-1 text-[8.5px] sm:text-[9px] font-bold text-amber-700 bg-amber-50 px-1.5 py-0.2 sm:py-0.5 rounded-md border border-amber-200" title="Salah satu perangkat target sedang mengalami kendala / perbaikan">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    <span>Perangkat Terkendala</span>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Detail Lokasi & Periode Khusus Mobile: 2 Baris Super Ringkas --}}
                <div class="mt-2 space-y-1 text-[11px] sm:hidden text-[#4E615B]">
                    <div class="flex items-center gap-1.5 truncate">
                        <svg class="h-3 w-3 text-[#3D6B5C] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="truncate font-semibold text-slate-700">{{ $videotron?->name ?? 'Videotron 1' }}</span>
                        <span class="text-slate-300">•</span>
                        <span class="truncate text-slate-500">{{ $videotron?->location ?? 'Kampus Utama' }}</span>
                        @if($otherVideotronsCount > 0)
                            <span class="text-[9px] text-[#0E4D3C] font-bold bg-[#E3F1EA] px-1 rounded-full">+{{ $otherVideotronsCount }}</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-1.5 text-slate-600">
                        <svg class="h-3 w-3 text-[#3D6B5C] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="font-medium text-slate-700">{{ $pFormatted }}</span>
                        @if($durasi > 0)
                            <span class="text-[10px] text-slate-400 font-normal">({{ $durasi }} Hari)</span>
                        @endif
                    </div>
                </div>

                {{-- Detail Lokasi & Periode Khusus Layar Tablet/Desktop (>= sm): Tampilan Rapi, Sejajar & Modern --}}
                <div class="hidden sm:block mt-3 pt-2.5 border-t border-slate-100/90 space-y-1.5 text-xs text-[#4E615B]">
                    {{-- Baris 1: Videotron & Lokasi --}}
                    <div class="flex items-center gap-2 text-slate-700 min-w-0">
                        <svg class="h-3.5 w-3.5 text-[#3D6B5C] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <div class="flex items-center gap-1.5 min-w-0 truncate text-[11.5px]">
                            <strong class="font-bold text-slate-800 truncate">{{ $videotron?->name ?? 'Videotron' }}</strong>
                            <span class="text-slate-300 shrink-0">•</span>
                            <span class="text-slate-500 truncate">{{ $videotron?->location ?? 'Kampus Utama' }}</span>
                            @if($otherVideotronsCount > 0)
                                <span class="ml-0.5 inline-flex items-center text-[9.5px] font-bold text-[#0E4D3C] bg-[#E3F1EA] px-1.5 py-0.2 rounded-full shrink-0">
                                    +{{ $otherVideotronsCount }} lainnya
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Baris 2: Periode Penayangan --}}
                    <div class="flex items-center gap-2 text-slate-600 min-w-0">
                        <svg class="h-3.5 w-3.5 text-[#3D6B5C] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <div class="flex items-center gap-1.5 text-[11.5px] min-w-0">
                            <span class="font-semibold text-slate-700">{{ $pFormatted }}</span>
                            @if($durasi > 0)
                                <span class="text-[10.5px] text-slate-400 font-normal shrink-0">({{ $durasi }} Hari)</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bawah: Link Detail + Nomor Tiket --}}
            <div class="mt-2.5 pt-1.5 sm:mt-3 sm:pt-0.5 border-t border-slate-100/70 sm:border-0 flex items-center justify-between">
                <span class="inline-flex items-center gap-1 sm:gap-1.5 text-[11px] sm:text-xs font-bold text-[#0E4D3C] transition-colors duration-200 hover:text-[#D9A93B]">
                    <span>Detail Pengajuan</span>
                    <svg class="h-2.5 w-2.5 sm:h-3 sm:w-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </span>
                <span class="text-[10px] sm:text-[11px] font-mono font-bold text-slate-400">
                    {{ $sub->ticket_code }}
                </span>
            </div>
        </a>
    @endforeach

    {{-- Empty State jika tidak ada pengajuan --}}
    @if($submissions->isEmpty())
        <div class="col-span-full flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-[#C8DDD6] bg-[#F7FAF8]/80 py-16 px-6 text-center backdrop-blur-sm">
            <div class="h-16 w-16 rounded-full bg-[#E3F1EA] flex items-center justify-center text-[#0E4D3C] mb-4 shadow-xs">
                <svg class="h-8 w-8 text-[#0E4D3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-[#0E4D3C]">
                @if(request('search'))
                    Tidak Ditemukan Pengajuan dengan Kata Kunci "{{ request('search') }}"
                @elseif($currentStatus)
                    Tidak Ada Pengajuan dengan Status "{{ ucfirst($currentStatus) }}"
                @else
                    Belum Ada Pengajuan
                @endif
            </h3>
            <p class="mt-1 text-xs text-[#6B8C82] max-w-sm">
                @if(request('search'))
                    Silakan periksa kembali kata kunci pencarian Anda atau reset filter untuk melihat semua pengajuan.
                @elseif($currentStatus)
                    Anda tidak memiliki pengajuan dalam kategori status ini. Coba pilih status lain atau buat pengajuan baru.
                @else
                    Anda belum memiliki riwayat pengajuan penayangan videotron. Mulai ajukan materi penayangan sekarang.
                @endif
            </p>
            @if(request('search'))
                <button type="button"
                    @click="clearSearch()"
                    class="group btn-wizard-secondary mt-5 inline-flex h-9 items-center gap-2 rounded-full px-5 text-xs font-bold text-[#0E4D3C] select-none shadow-xs cursor-pointer">
                    <span>Reset Pencarian</span>
                </button>
            @else
                <a href="{{ route('pengajuan.create') }}"
                    class="group btn-hero-primary mt-5 inline-flex h-10 items-center gap-2 rounded-full px-5 text-xs font-bold text-[#123F33] select-none shadow-md">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-white/30 text-[#123F33] transition-transform duration-300 group-hover:scale-105">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </span>
                    <span>Ajukan Penayangan Sekarang</span>
                </a>
            @endif
        </div>
    @endif
</div>

{{-- Pagination Links (Desain Elegan UIN SSC & Bahasa Indonesia) --}}
@if($submissions->hasPages())
    <div class="pt-2 pb-5 sm:pb-6">
        {{ $submissions->links('vendor.pagination.sipetron') }}
    </div>
@endif
