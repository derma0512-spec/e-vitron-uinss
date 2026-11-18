@php
    $notifications = $this->getNotifications();
    $unreadNotificationsCount = $this->getUnreadNotificationsCount();
    $notifList = method_exists($notifications, 'items') ? $notifications->items() : $notifications;
    $firstUnread = collect($notifList)->first(fn ($n) => is_null($n->read_at));
    $ageSeconds = $firstUnread?->created_at ? (int) max(0, $firstUnread->created_at->diffInSeconds(now(), true)) : null;
    $latestData = $firstUnread ? [
        'id' => (string) $firstUnread->id,
        'title' => (string) ($firstUnread->data['title'] ?? 'Pengajuan Baru Masuk'),
        'body' => (string) ($firstUnread->data['body'] ?? ($firstUnread->data['message'] ?? '')),
        'url' => (string) ($firstUnread->data['actions'][0]['url'] ?? ($firstUnread->data['url'] ?? '')),
        'age_seconds' => $ageSeconds,
    ] : null;
@endphp

<div
    @if ($pollingInterval = $this->getPollingInterval())
        wire:poll.{{ $pollingInterval }}
    @endif
    class="flex"
    x-data="{
        lastUnreadId: null,
        lastUnreadCount: @js($unreadNotificationsCount),
        isReady: false,
        playChime() {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                if (ctx.state === 'suspended') {
                    ctx.resume();
                }
                const now = ctx.currentTime;
                
                // Nada 1: E5 (659 Hz)
                const osc1 = ctx.createOscillator();
                const gain1 = ctx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(659.25, now);
                gain1.gain.setValueAtTime(0.08, now);
                gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.3);
                osc1.connect(gain1);
                gain1.connect(ctx.destination);
                osc1.start(now);
                osc1.stop(now + 0.3);

                // Nada 2: A5 (880 Hz)
                const osc2 = ctx.createOscillator();
                const gain2 = ctx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(880, now + 0.12);
                gain2.gain.setValueAtTime(0.12, now + 0.12);
                gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.5);
                osc2.connect(gain2);
                gain2.connect(ctx.destination);
                osc2.start(now + 0.12);
                osc2.stop(now + 0.5);
            } catch (err) { console.warn('[E-VITRON] AudioContext chime blocked by browser autoplay policy:', err); }
        },
        checkIncoming(latest, currentUnreadCount) {
            currentUnreadCount = parseInt(currentUnreadCount || 0, 10);

            if (!latest || !latest.id) {
                this.lastUnreadId = null;
                this.lastUnreadCount = currentUnreadCount;
                this.isReady = true;
                return;
            }

            // Inisialisasi awal saat admin pertama kali membuka halaman
            if (!this.isReady) {
                this.lastUnreadId = latest.id;
                this.lastUnreadCount = currentUnreadCount;
                this.isReady = true;
                return;
            }

            const previousId = this.lastUnreadId;
            const previousCount = this.lastUnreadCount;

            this.lastUnreadId = latest.id;
            this.lastUnreadCount = currentUnreadCount;

            // Jika ID tidak berubah, tidak ada notifikasi baru
            if (latest.id === previousId) {
                return;
            }

            // Jika jumlah unread berkurang (karena dihapus / dibaca),
            // perubahan latest.id adalah akibat pergeseran notifikasi lama ke atas, BUKAN event baru masuk!
            if (currentUnreadCount < previousCount) {
                return;
            }

            // Syarat Mutlak: Notifikasi HARUS berumur < 90 detik agar dianggap event real-time langsung.
            // Notifikasi basi (misal 22 jam lalu yang bergeser naik) 100% diabaikan!
            const FRESH_WINDOW_SECONDS = 90;
            if (typeof latest.age_seconds === 'number' && latest.age_seconds > FRESH_WINDOW_SECONDS) {
                return;
            }

            // Jika lolos semua validasi, ini murni pengajuan baru yang baru saja masuk secara real-time!
            this.playChime();
            
            // Munculkan Toast Pop-Up Otomatis di Pojok Layar
            if (window.FilamentNotification) {
                const notif = new window.FilamentNotification()
                    .title(latest.title || 'Pengajuan Baru Masuk')
                    .body(latest.body || '')
                    .icon('heroicon-o-bell')
                    .iconColor('success')
                    .duration(8000);

                if (latest.url && window.FilamentNotificationAction) {
                    const act = new window.FilamentNotificationAction('view')
                        .label('Tinjau Sekarang')
                        .button()
                        .size('xs')
                        .url(latest.url);
                    notif.actions([act]);
                }
                notif.send();
            }

            // Kabarkan event ke halaman admin agar tabel/badge ikut terbarukan
            window.dispatchEvent(new CustomEvent('sipetron:new-notification-arrived', { detail: latest }));
        }
    }"
    x-init="checkIncoming(@js($latestData), {{ (int) $unreadNotificationsCount }})"
    x-effect="checkIncoming(@js($latestData), {{ (int) $unreadNotificationsCount }})"
>
    @if ($trigger = $this->getTrigger())
        <x-filament-notifications::database.trigger>
            {{ $trigger->with(['unreadNotificationsCount' => $unreadNotificationsCount]) }}
        </x-filament-notifications::database.trigger>
    @endif

    <x-filament-notifications::database.modal
        :notifications="$notifications"
        :unread-notifications-count="$unreadNotificationsCount"
    />

    @if ($broadcastChannel = $this->getBroadcastChannel())
        <x-filament-notifications::database.echo
            :channel="$broadcastChannel"
        />
    @endif
</div>
