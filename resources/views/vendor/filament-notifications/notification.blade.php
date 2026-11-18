@php
    use Filament\Notifications\Livewire\Notifications;
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\VerticalAlignment;
    use Illuminate\Support\Arr;

    $color = $getColor() ?? 'gray';
    $isInline = $isInline();
    $status = $getStatus();
    $title = $getTitle();
    if (filled($title) && is_string($title)) {
        $title = preg_replace('/^(?:Pemberitahuan\s+)?Kendala\s+(?:Perangkat:\s*)?/ui', 'Kendala: ', $title);
        $title = preg_replace('/^Kabar\s+Baik:\s*Perangkat\s*(.*?)\s*Selesai\s*Diperbaiki$/ui', 'Kabar Baik: $1 Pulih', $title);
        $title = preg_replace('/^Kabar\s+Baik:\s*(.*?)\s*Normal\s*Kembali$/ui', 'Kabar Baik: $1 Pulih', $title);
    }
    $hasTitle = filled($title);
    $date = $getDate();
    $hasDate = filled($date);
    if ($hasDate && is_string($date)) {
        // Normalisasi format waktu ke Bahasa Indonesia
        $enToId = [
            'just now' => 'baru saja',
            'seconds ago' => 'detik yang lalu',
            'second ago' => 'detik yang lalu',
            'minutes ago' => 'menit yang lalu',
            'minute ago' => 'menit yang lalu',
            'hours ago' => 'jam yang lalu',
            'hour ago' => 'jam yang lalu',
            'days ago' => 'hari yang lalu',
            'day ago' => 'hari yang lalu',
            'weeks ago' => 'minggu yang lalu',
            'week ago' => 'minggu yang lalu',
            'months ago' => 'bulan yang lalu',
            'month ago' => 'bulan yang lalu',
            'years ago' => 'tahun yang lalu',
            'year ago' => 'tahun yang lalu',
            'yesterday' => 'kemarin',
            'from now' => 'dari sekarang',
        ];
        $date = str_ireplace(array_keys($enToId), array_values($enToId), $date);
    }
    $body = $getBody();
    $hasBody = filled($body);

    $targetUrl = null;
    $actions = $getActions();
    if ($actions && count($actions) > 0) {
        $firstAction = $actions[0];
        if (is_object($firstAction) && method_exists($firstAction, 'getUrl')) {
            $rawUrl = $firstAction->getUrl();
            if ($rawUrl) {
                // Selalu jadikan URL relatif (path + query) agar tidak terkunci ke http://127.0.0.1:8000
                // dan tetap berada pada domain/vhost aktif saat ini (misal: videotron-uin-ssc.test)
                $parsed = parse_url($rawUrl);
                if (isset($parsed['path'])) {
                    $targetUrl = $parsed['path']
                        . (isset($parsed['query']) ? '?' . $parsed['query'] : '')
                        . (isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '');
                } else {
                    $targetUrl = $rawUrl;
                }
            }
        }
    }

    $iconColorName = $getIconColor() ?? $color;

    // Extract ticket code from body
    $ticketCode = null;
    if ($hasBody && preg_match('/(VTR-\d{6}-\d{4})/', $body, $matches)) {
        $ticketCode = $matches[1];
    }

    // Extract activity title (text inside parentheses)
    $activityTitle = null;
    if ($hasBody && preg_match('/\((.*?)\)/u', $body, $mTitle)) {
        $activityTitle = trim($mTitle[1]);
    }

    // Extract meaningful description for Row 2 (sesuai referensi)
    $cleanDesc = null;
    if ($hasBody) {
        $raw = (string) $body;
        // Hapus judul kegiatan di dalam kurung misal (contohh)
        $raw = preg_replace('/\s*\([^)]*\)\s*/u', ' ', $raw);
        // Hapus "untuk tiket VTR-xxxxxx-xxxx" atau "tiket VTR-xxxxxx-xxxx"
        $raw = preg_replace('/(?:untuk\s+)?tiket\s*VTR-\d{6}-\d{4}/ui', '', $raw);
        $raw = preg_replace('/VTR-\d{6}-\d{4}/ui', '', $raw);
        $raw = trim($raw);
        // Normalisasi teks pembuka
        $raw = preg_replace('/^pengaju\s+telah\s+mengirim/ui', 'Pengajuan telah mengirim', $raw);
        $cleanDesc = trim($raw, " \t\n\r\0\x0B.,-–:");
        if (filled($cleanDesc) && preg_match('/[a-zA-Z0-9]/', $cleanDesc)) {
            $cleanDesc = ucfirst($cleanDesc);
            if (! str_ends_with($cleanDesc, '.')) {
                $cleanDesc .= '.';
            }
        } else {
            $cleanDesc = null;
        }
    }

    $isSuccess = $iconColorName === 'success' || str_contains($title ?? '', 'Baru');
    $isInfo    = $iconColorName === 'info'    || str_contains($title ?? '', 'Revisi');
    $isDanger  = $iconColorName === 'danger'  || str_contains($title ?? '', 'Batal') || str_contains($title ?? '', 'Gagal') || str_contains($title ?? '', 'Tolak');

    $typeClass = match (true) {
        $isDanger  => 'is-danger',
        $isSuccess => 'is-success',
        default    => 'is-info',
    };

    $iconSvg = match (true) {
        $isSuccess => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>',
        $isInfo    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>',
        $isDanger  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
        default    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>',
    };
@endphp

<x-filament-notifications::notification
    :notification="$notification"
    :x-transition:enter-start="
        Arr::toCssClasses([
            'opacity-0',
            ($this instanceof Notifications)
            ? match (static::$alignment) {
                Alignment::Start, Alignment::Left => '-translate-x-12',
                Alignment::End, Alignment::Right => 'translate-x-12',
                Alignment::Center => match (static::$verticalAlignment) {
                    VerticalAlignment::Start => '-translate-y-12',
                    VerticalAlignment::End => 'translate-y-12',
                    default => null,
                },
                default => null,
            }
            : null,
        ])
    "
    :x-transition:leave-end="
        Arr::toCssClasses([
            'opacity-0',
            'scale-95' => ! $isInline,
        ])
    "
    @class([
        'fi-no-notification transition duration-200',
        ...match ($isInline) {
            true => ['fi-inline', 'w-full', 'overflow-hidden'],
            false => [
                'sipetron-floating-toast-root',
            ],
        },
    ])
>
    @if ($isInline)
        {{-- ======= COMPACT CARD IN DRAWER ======= --}}
        <div
            class="sipetron-notif-card {{ $typeClass }}"
            @if ($targetUrl)
                x-on:click="
                    $wire.markNotificationAsRead('{{ $notification->getId() }}');
                    window.location.href = '{{ $targetUrl }}';
                "
            @else
                x-on:click="$wire.markNotificationAsRead('{{ $notification->getId() }}')"
            @endif
        >
            {{-- Icon kiri --}}
            <div class="sipetron-notif-icon-box {{ $typeClass }}">
                {!! $iconSvg !!}
            </div>

            {{-- Konten teks --}}
            <div class="sipetron-notif-body-wrap">

                {{-- Baris 1: Judul + Waktu + Dot (unread dikontrol CSS via data-unread di parent) --}}
                <div class="sipetron-notif-row-1">
                    @if ($hasTitle)
                        <h4 class="sipetron-notif-card-title">
                            {{ str($title)->sanitizeHtml()->toHtmlString() }}
                        </h4>
                    @endif

                    <div class="sipetron-notif-meta-right">
                        @if ($hasDate)
                            <span class="sipetron-notif-time-text">{{ $date }}</span>
                        @endif
                        {{-- dot dikontrol visibility oleh CSS berdasarkan [data-unread="true"] --}}
                        <span class="sipetron-notif-unread-dot" aria-hidden="true"></span>
                    </div>
                </div>

                {{-- Baris 2: Pill Tiket + Nama Kegiatan --}}
                @if ($ticketCode || $activityTitle)
                    <div class="sipetron-notif-row-2">
                        @if ($ticketCode)
                            <span class="sipetron-notif-pill {{ $typeClass }}">{{ $ticketCode }}</span>
                        @endif
                        @if ($activityTitle)
                            <span class="sipetron-notif-activity-title" title="{{ $activityTitle }}">
                                {{ $activityTitle }}
                            </span>
                        @endif
                    </div>
                @endif

                {{-- Baris 3: Deskripsi singkat --}}
                @if ($cleanDesc)
                    <p class="sipetron-notif-desc-text">{{ $cleanDesc }}</p>
                @endif
            </div>

            {{-- Kolom kanan: arrow dan delete button saling toggle saat hover --}}
            <div class="sipetron-notif-right-col">
                @if ($targetUrl)
                    <svg class="sipetron-notif-arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                @endif
                <button
                    type="button"
                    x-on:click.stop="window.deleteAdminSingleNotif ? window.deleteAdminSingleNotif($event, '{{ $notification->getId() }}', '{{ route('admin.notifications.destroy', $notification->getId()) }}') : $wire.removeNotification('{{ $notification->getId() }}')"
                    title="Hapus notifikasi ini"
                    aria-label="Hapus notifikasi ini"
                    class="sipetron-notif-del-btn"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>
    @else
        {{-- ======= E-VITRON MINT PASTEL TOAST (QUEUE-CONTROLLED) ======= --}}
        @php
            $toastSig = addslashes($title . '|' . ($ticketCode ?? '') . '|' . ($cleanDesc ?? ''));
        @endphp
        <div
            x-data="sipetronToastItem({
                id: '{{ $notification->getId() }}',
                sig: '{{ $toastSig }}',
                isError: {{ $isDanger ? 'true' : 'false' }}
            })"
            x-show="isVisible"
            x-cloak
            class="sipetron-toast-floating-item"
            :class="{ 'is-entering': isEntering, 'is-leaving': isLeaving }"
            x-on:mouseenter="onMouseEnter()"
            x-on:mouseleave="onMouseLeave()"
        >
            <div
                class="sipetron-toast-card {{ $typeClass }} {{ $targetUrl ? 'is-clickable' : '' }}"
                @if ($targetUrl)
                    x-on:click="close(); window.location.href = '{{ $targetUrl }}';"
                    title="Buka pengajuan"
                @endif
            >
                {{-- Icon Kiri: Lingkaran Hijau Zamrud Solid dengan Centang Putih (✓) --}}
                <div class="sipetron-toast-icon-circle {{ $typeClass }}">
                    @if ($isDanger)
                        <svg viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    @elseif ($isInfo && ! $isSuccess && str_contains(strtolower($title ?? ''), 'peringatan'))
                        <svg viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    @else
                        <svg viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    @endif
                </div>

                {{-- Konten Teks 2 Baris --}}
                <div class="sipetron-toast-content">
                    {{-- Baris 1: Judul Utama --}}
                    @if ($hasTitle)
                        <h4 class="sipetron-toast-title">
                            {{ str($title)->sanitizeHtml()->toHtmlString() }}
                        </h4>
                    @endif

                    {{-- Baris 2: Badge Tiket + Dot + Deskripsi --}}
                    <div class="sipetron-toast-meta">
                        @if ($ticketCode)
                            <span class="sipetron-toast-ticket">{{ $ticketCode }}</span>
                            @if ($cleanDesc)
                                <span class="sipetron-toast-dot" aria-hidden="true">·</span>
                            @endif
                        @endif

                        @if ($cleanDesc)
                            <span class="sipetron-toast-desc" title="{{ $cleanDesc }}">
                                {{ $cleanDesc }}
                            </span>
                        @elseif ($hasBody && ! $ticketCode)
                            <span class="sipetron-toast-desc" title="{{ $body }}">
                                {{ str($body)->sanitizeHtml()->toHtmlString() }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Tombol Tutup X di Pojok Kanan Atas --}}
                <button
                    type="button"
                    x-on:click.stop="close()"
                    class="sipetron-toast-close-btn"
                    title="Tutup notifikasi"
                    aria-label="Tutup notifikasi"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>
    @endif
</x-filament-notifications::notification>
