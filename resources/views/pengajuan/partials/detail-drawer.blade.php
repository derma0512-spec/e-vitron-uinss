@php
    $statusMap = [
        'menunggu'   => [
            'label'  => 'Menunggu',
            'title'  => 'Menunggu Pemeriksaan Admin',
            'desc'   => 'Pengajuan Anda telah masuk antrean kurasi. Tim Admin E-VITRON akan memeriksa kesesuaian berkas surat permohonan dan materi tayang Anda.',
            'bg'     => '#FEF3C7',
            'border' => '#FDE68A',
            'color'  => '#92400E',
            'icon'   => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        'revisi'     => [
            'label'  => 'Perlu Revisi',
            'title'  => 'Perlu Perbaikan Materi (Revisi)',
            'desc'   => 'Admin E-VITRON meminta perbaikan materi atau dokumen. Silakan periksa catatan perbaikan di bawah dan kirimkan tautan pembaruan.',
            'bg'     => '#FFEDD5',
            'border' => '#FDBA74',
            'color'  => '#C2410C',
            'icon'   => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
        ],
        'tayang'     => [
            'label'  => 'Sedang Tayang',
            'title'  => 'Materi Sedang Aktif Ditayangkan!',
            'desc'   => 'Materi Anda saat ini sedang disiarkan secara aktif pada perangkat videotron terpilih sesuai periode penayangan.',
            'bg'     => '#DCFCE7',
            'border' => '#BBF7D0',
            'color'  => '#15803D',
            'icon'   => 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        'terjadwal'  => [
            'label'  => 'Disetujui',
            'title'  => 'Pengajuan Disetujui (Menunggu Jadwal)',
            'desc'   => 'Materi Anda telah lolos kurasi Admin E-VITRON dan siap disiarkan pada perangkat videotron sasaran saat tanggal jadwal penayangan dimulai.',
            'bg'     => '#CCFBF1',
            'border' => '#99F6E4',
            'color'  => '#0F766E',
            'icon'   => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        'disetujui'  => [
            'label'  => 'Disetujui',
            'title'  => 'Pengajuan Disetujui & Siap Ditayangkan!',
            'desc'   => 'Materi Anda telah lolos kurasi Admin E-VITRON dan siap disiarkan pada perangkat videotron terpilih sesuai tanggal jadwal.',
            'bg'     => '#CCFBF1',
            'border' => '#99F6E4',
            'color'  => '#0F766E',
            'icon'   => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        'ditolak'    => [
            'label'  => 'Ditolak',
            'title'  => 'Pengajuan Tidak Disetujui (Ditolak)',
            'desc'   => 'Materi pengajuan tidak memenuhi kriteria kelayakan penayangan videotron UIN Siber Cirebon.',
            'bg'     => '#FEE2E2',
            'border' => '#FECACA',
            'color'  => '#991B1B',
            'icon'   => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        'dibatalkan' => [
            'label'  => 'Dibatalkan',
            'title'  => 'Pengajuan Dibatalkan',
            'desc'   => 'Pengajuan ini telah dibatalkan secara mandiri atas permintaan Anda.',
            'bg'     => '#F1F5F9',
            'border' => '#E2E8F0',
            'color'  => '#64748B',
            'icon'   => 'M6 18L18 6M6 6l12 12',
        ],
        'selesai'    => [
            'label'  => 'Selesai',
            'title'  => 'Masa Penayangan Selesai',
            'desc'   => 'Periode penayangan materi videotron telah berakhir dan telah diarsipkan secara aman di sistem.',
            'bg'     => '#EEF2FF',
            'border' => '#C7D2FE',
            'color'  => '#4338CA',
            'icon'   => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
    ];
    $dispStatus = $submission->display_status;
    $s = $statusMap[$dispStatus] ?? ($statusMap[$submission->status] ?? $statusMap['menunggu']);

    $sDate = $submission->start_date?->locale('id');
    $eDate = $submission->end_date?->locale('id');
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

    $picDisplayName = $submission->pic_name ?: ($submission->user?->name ?: 'Pengaju');
    $cleanWords = array_values(array_filter(explode(' ', trim(preg_replace('/\(.*?\)/', '', $picDisplayName)))));
    $picInitials = 'PG';
    if (count($cleanWords) >= 2) {
        $picInitials = strtoupper(mb_substr($cleanWords[0], 0, 1) . mb_substr($cleanWords[1], 0, 1));
    } elseif (count($cleanWords) === 1 && !empty($cleanWords[0])) {
        $picInitials = strtoupper(mb_substr($cleanWords[0], 0, min(2, mb_strlen($cleanWords[0]))));
    }

    $rawOrg = $submission->organization ?: ($submission->user?->unit_or_prodi ?: 'Civitas Akademika UIN SSC');
    $cleanOrg = trim(preg_replace('/^Teknik\s+/i', '', (string) $rawOrg));
    if (empty($cleanOrg)) {
        $cleanOrg = 'Civitas Akademika UIN SSC';
    }

    $cleanPhone = preg_replace('/[^0-9]/', '', (string) $submission->whatsapp);
    if (str_starts_with($cleanPhone, '0')) {
        $waNumber = '62' . substr($cleanPhone, 1);
    } else {
        $waNumber = $cleanPhone;
    }
@endphp

{{-- 1. STICKY DRAWER HEADER (Metadata Tiket, Waktu, Status, & Tombol Close - Solid 1-Row Compact) --}}
<div class="sipetron-drawer-header" @wheel.prevent.stop @touchmove.prevent.stop>
    <div class="flex items-center justify-between gap-2.5 sm:gap-3">
        {{-- Sisi Kiri: Kode Tiket Monospace & Waktu Pengajuan --}}
        <div class="min-w-0">
            <div class="flex items-center gap-1.5 sm:gap-2">
                <span class="font-mono font-extrabold text-[15px] sm:text-base text-slate-900 tracking-tight whitespace-nowrap">
                    {{ $submission->ticket_code }}
                </span>

                {{-- Tombol Salin Tiket dengan Icon Checkmark & Efek Hijau Lembut (Bersih & Zero Layout Shift) --}}
                <button 
                    type="button" 
                    onclick="window.copyTicketToClipboard('{{ $submission->ticket_code }}', this)"
                    title="Salin Kode Tiket"
                    class="sipetron-copy-btn inline-flex items-center justify-center w-[26px] h-[26px] rounded-md border border-slate-200 bg-[#F8FAF9] text-slate-500 hover:border-slate-300 hover:bg-white active:scale-95 transition-all cursor-pointer shrink-0"
                >
                    <svg class="sipetron-copy-icon-standby w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <svg class="sipetron-copy-icon-copied w-3.5 h-3.5 text-emerald-600" style="display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
            </div>
            <div class="text-[11px] font-medium text-slate-500 mt-0.5 whitespace-nowrap">
                Diajukan {{ $submission->created_at->locale('id')->translatedFormat('d M Y, H:i') }} WIB
            </div>
        </div>

        {{-- Sisi Kanan: Status Badge & Tombol Close [X] --}}
        <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
            <span class="inline-flex items-center gap-1 sm:gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold shadow-2xs whitespace-nowrap"
                  style="background:{{ $s['bg'] }};color:{{ $s['color'] }};border:1px solid {{ $s['border'] }};">
                <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $s['icon'] }}"/>
                </svg>
                <span>{{ $s['label'] }}</span>
            </span>

            <button type="button"
                    @click="closeDrawer()"
                    class="h-7 w-7 rounded-full border border-slate-200 bg-slate-100 text-slate-500 hover:text-slate-800 hover:bg-slate-200 hover:border-slate-300 active:bg-slate-300 flex items-center justify-center transition-all cursor-pointer shadow-2xs active:scale-95 shrink-0"
                    title="Tutup (Esc)">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
</div>

{{-- 2. SCROLLABLE DRAWER BODY --}}
<div class="sipetron-drawer-body">

    {{-- CARD 1: STATUS BANNER, FORM REVISI & PANDUAN PENGAJU --}}
    @include('pengajuan.partials.detail-drawer-revision-form')

    {{-- ALERT KENDALA TEKNIS PERANGKAT VIDEOTRON (JIKA ADA PERANGKAT TERPILIH YANG BERKENDALA) --}}
    @php
        $troubledVideotrons = $submission->videotrons->filter(fn ($v) => in_array($v->condition, ['gangguan', 'dalam_perbaikan']) || ! (bool) $v->is_active);
    @endphp
    @if($troubledVideotrons->isNotEmpty() && in_array($dispStatus, ['menunggu', 'terjadwal', 'tayang']))
        <div class="rounded-xl border border-amber-300 bg-amber-50/95 p-3.5 shadow-2xs space-y-2">
            @foreach($troubledVideotrons as $tv)
                <div class="flex items-start gap-2.5">
                    <div class="h-6 w-6 rounded-full bg-amber-200 text-amber-900 flex items-center justify-center shrink-0 mt-0.5 border border-amber-300">
                        <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1 text-xs text-amber-950 leading-relaxed">
                        <span class="font-bold text-amber-950">Kendala Teknis {{ $tv->name }}:</span>
                        <span>{{ filled($tv->issue_notes) ? $tv->issue_notes : 'Sedang mengalami kendala teknis / pemeliharaan perangkat.' }}</span>
                        @if($tv->estimated_recovery_date)
                            <span class="font-semibold block sm:inline text-amber-800">
                                (Estimasi normal: {{ \Carbon\Carbon::parse($tv->estimated_recovery_date)->locale('id')->translatedFormat('d M Y') }})
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- CARD 2: INFORMASI KONTEN KEGIATAN & JADWAL PENAYANGAN --}}
    <div class="rounded-xl border border-slate-200 bg-white p-3.5 sm:p-4 shadow-2xs space-y-3">
        {{-- Card Header: Title & Badges --}}
        <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-2.5">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#0E4D3C] flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-[#0E4D3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <span>Informasi Penayangan</span>
            </span>

            <div class="flex items-center gap-1.5 shrink-0">
                @if($submission->content_type === 'video')
                    <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                        <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Video @if($submission->video_duration_seconds) ({{ $submission->video_duration_seconds }}s) @endif
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-[10px] font-bold bg-emerald-50 text-[#0E4D3C] border border-emerald-200">
                        <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Poster
                    </span>
                @endif

                @if($durasi > 0)
                    <span class="inline-flex items-center text-[10px] font-bold text-[#0E4D3C] bg-[#E8F3EC] border border-[#C4DED0] px-2 py-0.5 rounded-md">
                        {{ $durasi }} Hari
                    </span>
                @endif
            </div>
        </div>

        {{-- Judul Kegiatan & Deskripsi --}}
        <div>
            <h3 class="font-montserrat text-sm sm:text-base font-bold text-slate-900 leading-snug">
                {{ $submission->title }}
            </h3>
            @if($submission->description)
                <p class="text-xs text-slate-500 mt-1 leading-relaxed italic">
                    "{{ $submission->description }}"
                </p>
            @endif
        </div>

        {{-- 2 Kotak Metrik Kompak: Jadwal & Layar --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
            <div class="flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-50 border border-slate-200/80">
                <div class="h-8 w-8 rounded-lg bg-[#E8F3EC] text-[#0E4D3C] flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Jadwal Tayang</span>
                    <span class="text-xs font-bold text-slate-800 truncate block">{{ $pFormatted }}</span>
                </div>
            </div>

            <div class="flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-50 border border-slate-200/80">
                <div class="h-8 w-8 rounded-lg bg-[#E8F3EC] text-[#0E4D3C] flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Target Layar</span>
                    <div class="space-y-1 mt-0.5">
                        @forelse($submission->videotrons as $v)
                            @php
                                $isTroubled = in_array($v->condition, ['gangguan', 'dalam_perbaikan']) || ! (bool) $v->is_active;
                                $statusBadgeText = match(true) {
                                    $v->condition === 'gangguan' => 'Gangguan',
                                    $v->condition === 'dalam_perbaikan' => 'Perbaikan',
                                    ! $v->is_active => 'Nonaktif',
                                    default => 'Normal',
                                };
                            @endphp
                            <div class="flex items-center justify-between text-xs gap-1.5">
                                <span class="font-bold text-slate-800 truncate text-[11px] flex items-center gap-1.5">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $isTroubled ? 'bg-amber-500 animate-pulse' : 'bg-[#0E4D3C]' }}"></span>
                                    <span>{{ $v->name }}</span>
                                </span>
                                <div class="flex items-center gap-1 shrink-0">
                                    @if($isTroubled)
                                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-amber-100 text-amber-900 border border-amber-300">
                                            {{ $statusBadgeText }}
                                        </span>
                                    @endif
                                    <span class="text-[9.5px] font-mono text-slate-500 bg-white px-1.5 py-0.2 rounded border border-slate-200">{{ $v->resolution }}</span>
                                </div>
                            </div>
                        @empty
                            <span class="text-xs text-slate-400 italic">Belum terhubung</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CARD 3: BERKAS & MATERI YANG DIAJUKAN --}}
    <div class="rounded-xl border border-slate-200 bg-white p-3.5 sm:p-4 shadow-2xs space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#0E4D3C] flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-[#0E4D3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Berkas & Materi Pengajuan</span>
            </span>
        </div>

        <div class="space-y-2.5">
            {{-- Google Drive Link Item --}}
            @php
                $gdriveDisplay = preg_replace('#^https?://(www\.)?#i', '', (string) ($submission->gdrive_link ?? ''));
                if (str_contains($gdriveDisplay, 'folders/')) {
                    $cleanDriveText = 'drive.google.com/folders/...';
                } elseif (str_contains($gdriveDisplay, 'drive.google.com')) {
                    $cleanDriveText = 'drive.google.com/...';
                } else {
                    $cleanDriveText = \Illuminate\Support\Str::limit($gdriveDisplay, 28, '...');
                }
            @endphp
            <a href="{{ $submission->gdrive_link }}" target="_blank" rel="noopener noreferrer"
               class="group flex items-center justify-between gap-2.5 p-2.5 sm:p-3 rounded-xl border border-sky-200 bg-sky-50/60 hover:bg-sky-50 hover:border-sky-300 transition-all cursor-pointer shadow-2xs">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="h-8 w-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center shrink-0 border border-sky-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM17 13l-5 5-5-5h3V9h4v4h3z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="block text-xs font-bold text-sky-950 truncate">Materi di Google Drive</span>
                        <span class="text-[11px] font-medium text-sky-800/80 font-mono truncate block mt-0.5" title="{{ $submission->gdrive_link }}">{{ $cleanDriveText }}</span>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-sky-600 group-hover:bg-sky-700 text-white text-[11px] font-bold shadow-2xs transition-colors duration-150 shrink-0">
                    <span>Buka Drive</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </span>
            </a>

            {{-- Surat Permohonan (PDF) Item --}}
            @if($submission->surat_peminjaman)
                <a href="{{ $submission->surat_peminjaman_url }}" target="_blank" rel="noopener noreferrer"
                   class="group flex items-center justify-between gap-2.5 p-2.5 sm:p-3 rounded-xl border border-emerald-200/80 bg-[#F4F9F6] hover:bg-[#EAF5F0] hover:border-emerald-300 transition-all cursor-pointer shadow-2xs">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="h-8 w-8 rounded-lg bg-rose-100 text-rose-700 font-extrabold text-[10.5px] flex items-center justify-center shrink-0 border border-rose-200/90 shadow-2xs">
                            PDF
                        </div>
                        <div class="min-w-0">
                            <span class="block text-xs font-bold text-slate-900 truncate">Surat Permohonan Resmi</span>
                            <span class="text-[11px] font-medium text-[#3D6B5C] truncate block mt-0.5">Dokumen PDF &bull; Siap Ditinjau</span>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#0E4D3C] group-hover:bg-[#165641] text-white text-[11px] font-bold shadow-2xs transition-colors duration-150 shrink-0">
                        <span>Buka PDF</span>
                        <svg class="w-3 h-3 text-[#D9A93B]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </span>
                </a>
            @else
                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-200 text-slate-500 text-xs flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Tidak melampirkan berkas surat permohonan.</span>
                </div>
            @endif
        </div>
    </div>

    {{-- CARD 4: PENANGGUNG JAWAB (PIC) & KONTAK --}}
    <div class="rounded-xl border border-slate-200 bg-white p-3.5 sm:p-4 shadow-2xs space-y-2.5">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#0E4D3C] flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-[#0E4D3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>Penanggung Jawab (PIC)</span>
            </span>
        </div>

        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="h-9 w-9 rounded-full bg-gradient-to-br from-[#0E4D3C] to-[#1B6F57] text-white font-extrabold text-xs flex items-center justify-center shadow-xs shrink-0">
                    {{ $picInitials }}
                </div>
                <div class="min-w-0">
                    <h5 class="text-xs font-bold text-slate-900 truncate leading-snug">{{ $picDisplayName }}</h5>
                    <p class="text-[11px] text-slate-500 truncate mt-0.5">
                        {{ $cleanOrg }}
                    </p>
                </div>
            </div>

            @if($cleanPhone)
                <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-[11px] font-bold hover:bg-emerald-100 transition-colors shrink-0 shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.52 3.449C18.24 1.245 15.24 0 12.045 0 5.463 0 .104 5.334.101 11.893c0 2.096.549 4.14 1.595 5.945L0 24l6.335-1.652c1.746.943 3.71 1.444 5.71 1.447h.006c6.585 0 11.946-5.336 11.949-11.896 0-3.176-1.24-6.165-3.48-8.45zm-8.475 18.321h-.005c-1.774 0-3.513-.477-5.032-1.378l-.361-.214-3.76 1.003 1.005-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.002-5.45 4.456-9.884 9.903-9.884 2.645 0 5.132 1.03 7.001 2.898a9.825 9.825 0 012.897 6.994c-.003 5.45-4.457 9.863-9.903 9.863zm5.432-7.397c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                    </svg>
                    <span>{{ $submission->whatsapp }}</span>
                </a>
            @endif
        </div>
    </div>

    {{-- CARD 5: LINIMASA PERJALANAN TIKET --}}
    <div class="rounded-xl border border-slate-200 bg-white p-3.5 sm:p-4 shadow-2xs space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#0E4D3C] flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-[#0E4D3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Linimasa Tiket</span>
            </span>
        </div>

        <div class="relative pl-5 border-l-2 border-[#0E4D3C]/20 space-y-3.5">
            @forelse($submission->statusHistories as $history)
                <div class="relative">
                    <div class="absolute -left-[27px] top-1 h-3 w-3 rounded-full bg-[#0E4D3C] border-2 border-white shadow-2xs"></div>
                    <div>
                        <div class="flex flex-wrap items-center gap-1.5">
                            <span class="text-xs font-bold text-[#0E4D3C] capitalize">Status: {{ $history->to_status }}</span>
                            <span class="text-[10.5px] text-slate-400 font-mono">{{ $history->created_at->locale('id')->translatedFormat('d M Y, H:i') }} WIB</span>
                        </div>
                        @if($history->notes)
                            <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">{{ $history->notes }}</p>
                        @endif
                        @if($history->user)
                            <span class="text-[10px] text-[#3D6B5C] font-semibold block mt-0.5">Oleh: {{ $history->user->name }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-xs text-slate-400 italic">Belum ada aktivitas riwayat tambahan.</div>
            @endforelse
        </div>
    </div>

</div>

{{-- 3. STICKY DRAWER FOOTER (Tombol Seimbang Sempurna & Berdampingan) --}}
<div class="sipetron-drawer-footer" @wheel.prevent.stop @touchmove.prevent.stop>
    <div class="flex items-center justify-between gap-2.5 sm:gap-3">
        <a href="{{ route('pengajuan.print', $submission->ticket_code) }}"
           target="_blank"
           rel="noopener noreferrer"
           class="flex-1 h-10 px-3 sm:px-5 rounded-xl bg-[#0E4D3C] hover:bg-[#165641] text-white text-xs font-bold shadow-sm transition-all inline-flex items-center justify-center gap-1.5 sm:gap-2 select-none cursor-pointer active:scale-[0.98]">
            <svg class="w-4 h-4 text-[#D9A93B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            <span class="truncate">
                <span class="sm:hidden">Cetak Bukti (PDF)</span>
                <span class="hidden sm:inline">Cetak Bukti Pengajuan (PDF)</span>
            </span>
        </a>

        <button type="button"
                @click="closeDrawer()"
                class="h-10 px-4 sm:px-5 rounded-xl border border-slate-200 bg-slate-100 hover:bg-slate-200 hover:border-slate-300 hover:text-slate-900 active:bg-slate-300/80 text-slate-700 text-xs font-bold transition-all inline-flex items-center justify-center cursor-pointer select-none active:scale-[0.98] shrink-0">
            <span>Tutup</span>
        </button>
    </div>
</div>
