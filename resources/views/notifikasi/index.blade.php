@extends('layouts.portal')

@section('title', 'Notifikasi Pengajuan | E-VITRON UIN Siber Cirebon')

@section('content')
<div class="w-full overflow-x-hidden min-h-[calc(100vh-200px)] relative" style="background:#F4F8F6;">

    {{-- Islamic Corner Ornaments Background (Selaras Persis Beranda, Pengajuan Saya, Ajukan, Informasi) --}}
    <div class="ornament-pattern" style="position:absolute;left:-40px;top:20px;height:240px;width:240px;opacity:0.35;pointer-events:none;"></div>
    <div class="ornament-pattern" style="position:absolute;right:-40px;top:80px;height:260px;width:260px;opacity:0.35;pointer-events:none;"></div>

    <div class="relative mx-auto max-w-[1400px] px-6 sm:px-10 lg:px-14 py-8 space-y-6 z-10">

        {{-- =========================================================================
             HEADER HALAMAN (Selaras Persis dengan Beranda, Pengajuan Saya, Informasi)
             ========================================================================= --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                {{-- Badge Emas Gradasi --}}
                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[10px] font-bold text-white shadow-xs"
                    style="background:linear-gradient(90deg,#C9901A 0%,#DDB140 60%,#EDD088 100%);letter-spacing:0.01em;">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span>Pusat Notifikasi & Aktivitas</span>
                </span>

                {{-- Judul Halaman --}}
                <div class="mt-2 mb-1 flex flex-wrap items-center gap-3">
                    <h1 class="font-montserrat font-extrabold text-[#0E4D3C] text-2xl sm:text-3xl tracking-tight leading-tight">
                        Notifikasi Pengajuan
                    </h1>
                    @if($unreadCount > 0)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold text-amber-800 bg-amber-100/90 border border-amber-200">
                            {{ $unreadCount }} Belum Dibaca
                        </span>
                    @endif
                </div>
                <p class="text-xs sm:text-[13px] leading-relaxed text-[#4E655F] max-w-2xl">
                    Riwayat pemberitahuan status, tindak lanjut revisi, dan keputusan admin terkait pengajuan videotron Anda.
                </p>
            </div>

            <div class="flex items-center gap-3 self-start sm:self-auto shrink-0">
                @if($unreadCount > 0)
                    <form method="POST" action="{{ route('notifikasi.markAllAsRead') }}">
                        @csrf
                        <button type="submit" 
                            class="group inline-flex items-center gap-1.5 rounded-full border border-[#0E4D3C]/20 bg-white/90 px-3.5 py-1.5 text-xs font-bold text-[#0E4D3C] shadow-xs backdrop-blur-xs transition-all duration-200 hover:border-[#0E4D3C] hover:bg-[#0E4D3C] hover:text-white hover:shadow-md hover:-translate-y-0.5 active:translate-y-0 active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Tandai Semua Dibaca</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- =========================================================================
             TABS FILTER NOTIFIKASI (Pills Modern Selaras Persis Pengajuan Saya)
             ========================================================================= --}}
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
            @php
                $isAll = ($filter !== 'unread');
                $isUnreadTab = ($filter === 'unread');
            @endphp
            <a href="{{ route('notifikasi.index') }}"
                class="group inline-flex shrink-0 items-center gap-2 rounded-full px-4 py-1.5 text-xs font-bold transition-all duration-200 select-none {{ $isAll ? 'bg-[#0E4D3C] text-white shadow-sm' : 'border border-[#0E4D3C]/20 bg-white/90 text-[#0E4D3C] shadow-xs backdrop-blur-xs hover:border-[#0E4D3C] hover:bg-[#0E4D3C] hover:text-white hover:shadow-md hover:-translate-y-0.5 active:translate-y-0 active:scale-95' }}">
                <span>Semua</span>
            </a>
            <a href="{{ route('notifikasi.index', ['filter' => 'unread']) }}"
                class="group inline-flex shrink-0 items-center gap-2 rounded-full px-3.5 py-1.5 text-xs font-bold transition-all duration-200 select-none {{ $isUnreadTab ? 'bg-[#0E4D3C] text-white shadow-sm' : 'border border-[#0E4D3C]/20 bg-white/90 text-[#0E4D3C] shadow-xs backdrop-blur-xs hover:border-[#0E4D3C] hover:bg-[#0E4D3C] hover:text-white hover:shadow-md hover:-translate-y-0.5 active:translate-y-0 active:scale-95' }}">
                <span>Belum Dibaca</span>
                @if($unreadCount > 0)
                    <span class="rounded-full px-2 py-0.2 text-[10px] font-bold {{ $isUnreadTab ? 'bg-[#D9A93B] text-[#123F33]' : 'bg-amber-100 text-amber-800 group-hover:bg-white group-hover:text-[#0E4D3C]' }}">
                        {{ $unreadCount }}
                    </span>
                @endif
            </a>
        </div>

        {{-- =========================================================================
             DAFTAR KARTU NOTIFIKASI
             ========================================================================= --}}
        <div class="space-y-3.5">
            @forelse($notifications as $notif)
                @php
                    $data = $notif->data;
                    $isUnread = is_null($notif->read_at);
                    $event = $data['event'] ?? 'submitted';

                    // Tampilan ikon & tema warna per status
                    $theme = match($event) {
                        'submitted' => [
                            'bg' => '#E3F1EA',
                            'text' => '#0E4D3C',
                            'badge_bg' => '#FEF3C7',
                            'badge_text' => '#92400E',
                            'badge_border' => 'border-amber-200',
                            'label' => 'Menunggu',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        ],
                        'disetujui' => [
                            'bg' => '#DCFCE7',
                            'text' => '#166534',
                            'badge_bg' => '#DCFCE7',
                            'badge_text' => '#166534',
                            'badge_border' => 'border-emerald-200',
                            'label' => 'Disetujui',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        ],
                        'revisi' => [
                            'bg' => '#FEF3C7',
                            'text' => '#92400E',
                            'badge_bg' => '#FEE2E2',
                            'badge_text' => '#991B1B',
                            'badge_border' => 'border-rose-200',
                            'label' => 'Perlu Revisi',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
                        ],
                        'ditolak' => [
                            'bg' => '#FEE2E2',
                            'text' => '#991B1B',
                            'badge_bg' => '#FEE2E2',
                            'badge_text' => '#991B1B',
                            'badge_border' => 'border-rose-300',
                            'label' => 'Ditolak',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        ],
                        'selesai' => [
                            'bg' => '#E0F2FE',
                            'text' => '#0369A1',
                            'badge_bg' => '#E0F2FE',
                            'badge_text' => '#0369A1',
                            'badge_border' => 'border-sky-200',
                            'label' => 'Selesai',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        ],
                        'videotron_disruption' => [
                            'bg' => '#FEF3C7',
                            'text' => '#92400E',
                            'badge_bg' => '#FEF3C7',
                            'badge_text' => '#92400E',
                            'badge_border' => 'border-amber-300',
                            'label' => 'Kendala Perangkat',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
                        ],
                        'videotron_recovered' => [
                            'bg' => '#DCFCE7',
                            'text' => '#166534',
                            'badge_bg' => '#DCFCE7',
                            'badge_text' => '#166534',
                            'badge_border' => 'border-emerald-300',
                            'label' => 'Normal Kembali',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        ],
                        default => [
                            'bg' => '#E3F1EA',
                            'text' => '#0E4D3C',
                            'badge_bg' => '#E3F1EA',
                            'badge_text' => '#0E4D3C',
                            'badge_border' => 'border-[#0E4D3C]/15',
                            'label' => 'Info',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        ],
                    };
                @endphp

                <div class="relative overflow-hidden rounded-[20px] transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md p-5 sm:p-6 {{ $isUnread ? 'bg-white border-l-4 border-l-[#D9A93B] border border-slate-200/90 shadow-sm ring-1 ring-[#D9A93B]/25' : 'bg-[#F8FAF9]/85 border-l-4 border-l-slate-300 border border-slate-200/70 shadow-2xs opacity-80 hover:opacity-100 hover:bg-white' }}"
                     style="backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);">

                    {{-- Watermark ornamen di sudut kartu --}}
                    <div class="ornament-pattern" style="position:absolute;right:-18px;bottom:-18px;height:90px;width:90px;opacity:0.10;pointer-events:none;"></div>

                    <div class="relative z-10 flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                        <div class="flex items-start gap-4 flex-1 min-w-0">
                            <!-- Icon Bulat Berwarna Status -->
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl shadow-2xs {{ $isUnread ? 'ring-2 ring-[#D9A93B]/30' : 'opacity-80' }}"
                                 style="background: {{ $theme['bg'] }}; color: {{ $theme['text'] }}; border: 1px solid rgba(14,77,60,0.08);">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    {!! $theme['icon'] !!}
                                </svg>
                            </div>

                            <!-- Info Notifikasi -->
                            <div class="space-y-1.5 flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    @if($isUnread)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#D9A93B] text-[#0E4D3C] shadow-2xs">
                                            <span class="h-1.5 w-1.5 rounded-full bg-[#0E4D3C] animate-pulse"></span>
                                            Belum Dibaca
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium text-slate-500 bg-slate-100 border border-slate-200">
                                            Sudah Dibaca
                                        </span>
                                    @endif

                                    <h3 class="text-sm sm:text-base font-bold {{ $isUnread ? 'text-[#0E4D3C] font-extrabold' : 'text-slate-700' }} leading-snug">
                                        {{ $data['title'] ?? 'Pemberitahuan Status' }}
                                    </h3>

                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $theme['badge_border'] }}"
                                          style="background: {{ $theme['badge_bg'] }}; color: {{ $theme['badge_text'] }};">
                                        {{ $theme['label'] }}
                                    </span>
                                </div>

                                @if(!empty($data['ticket_code']))
                                    <div class="text-xs font-semibold text-[#0E4D3C] flex items-center gap-1.5 pt-0.5">
                                        <span class="font-mono bg-[#E3F1EA] text-[#0E4D3C] px-2 py-0.5 rounded-md border border-[#0E4D3C]/15 font-bold">{{ $data['ticket_code'] }}</span>
                                        <span class="text-slate-300">·</span>
                                        <span class="{{ $isUnread ? 'text-slate-800 font-semibold' : 'text-slate-600' }} truncate">{{ $data['submission_title'] ?? '' }}</span>
                                    </div>
                                @endif

                                <p class="text-xs sm:text-[13px] {{ $isUnread ? 'text-slate-700 font-medium' : 'text-slate-500' }} leading-relaxed pt-0.5">
                                    {{ $data['message'] ?? '' }}
                                </p>

                                @if(!empty($data['notes']))
                                    <div class="mt-2.5 rounded-xl bg-amber-50/90 border border-amber-200/80 p-3 text-xs text-amber-900 shadow-2xs">
                                        <span class="font-bold block mb-0.5">Catatan Admin:</span>
                                        {{ $data['notes'] }}
                                    </div>
                                @endif

                                <div class="flex items-center gap-2 text-[11px] text-slate-400 pt-1">
                                    <span>{{ $notif->created_at->diffForHumans() }}</span>
                                    <span>•</span>
                                    <span>{{ $notif->created_at->translatedFormat('d F Y, H:i') }} WIB</span>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi Kanan -->
                        <div class="flex sm:flex-col items-center sm:items-end gap-2 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                            <a href="{{ route('notifikasi.read', $notif->id) }}"
                               class="group inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold transition-all duration-200 shadow-xs hover:shadow-md hover:-translate-y-0.5 active:translate-y-0 w-full sm:w-auto {{ $isUnread ? 'text-white' : 'bg-white border border-[#0E4D3C]/25 text-[#0E4D3C] hover:bg-[#0E4D3C] hover:text-white' }}"
                               style="{{ $isUnread ? 'background: #0E4D3C;' : '' }}">
                                <span>Buka Detail</span>
                                <svg class="h-3.5 w-3.5 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                                </svg>
                            </a>

                            <div class="flex items-center gap-1.5 w-full sm:w-auto justify-end">
                                @if($isUnread)
                                    <form method="POST" action="{{ route('notifikasi.markAsRead', $notif->id) }}">
                                        @csrf
                                        <button type="submit" 
                                            class="px-2.5 py-1 text-[11px] font-semibold text-[#0E4D3C] hover:bg-[#E3F1EA] rounded-lg transition-colors cursor-pointer"
                                            title="Tandai sudah dibaca">
                                            Tandai Dibaca
                                        </button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('notifikasi.destroy', $notif->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus notifikasi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer"
                                        title="Hapus notifikasi">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="relative overflow-hidden rounded-[22px] bg-white/95 border border-white/90 shadow-sm py-16 px-6 text-center backdrop-blur-md">
                    <div class="ornament-pattern" style="position:absolute;right:-20px;bottom:-20px;height:120px;width:120px;opacity:0.12;pointer-events:none;"></div>
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-[#E3F1EA] text-[#0E4D3C] mb-4 shadow-2xs">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold font-montserrat text-[#0E4D3C]">Belum Ada Notifikasi</h3>
                    <p class="text-xs sm:text-[13px] text-[#4E655F] mt-1 max-w-sm mx-auto leading-relaxed">
                        {{ $filter === 'unread' ? 'Semua notifikasi pengajuan Anda telah dibaca.' : 'Saat Anda mengajukan penayangan videotron atau ada pembaruan status dari admin, Anda akan menerima pemberitahuan di sini.' }}
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('pengajuan.index') }}" 
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#D9A93B] hover:bg-[#C2871D] text-[#0E4D3C] font-bold text-xs shadow-md hover:-translate-y-0.5 transition-all">
                            <span>Lihat Pengajuan Saya</span>
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($notifications->hasPages())
            <div class="pt-4 flex justify-center">
                {{ $notifications->links() }}
            </div>
        @endif

    </div>
</div>
@endsection
