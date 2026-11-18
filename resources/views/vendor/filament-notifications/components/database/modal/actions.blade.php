@props([
    'notifications',
    'unreadNotificationsCount',
])

<div {{ $attributes->class('sipetron-notif-actions-bar') }}>
    @if ($unreadNotificationsCount)
        <button
            type="button"
            x-on:click.prevent="window.markAllAdminNotificationsRead ? window.markAllAdminNotificationsRead('{{ route('admin.notifications.markAllAsRead') }}') : $wire.markAllNotificationsAsRead()"
            class="sipetron-notif-action-btn sipetron-notif-action-btn--read"
            title="{{ __('filament-notifications::database.modal.actions.mark_all_as_read.label') }}"
        >
            <svg class="sipetron-notif-action-icon" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            <span>{{ __('filament-notifications::database.modal.actions.mark_all_as_read.label') }}</span>
        </button>
    @endif

    @if ($notifications->count())
        <button
            type="button"
            x-on:click.prevent="window.clearAllAdminNotifications ? window.clearAllAdminNotifications('{{ route('admin.notifications.clearAll') }}') : (confirm('Hapus semua notifikasi? Tindakan ini hanya akan mengosongkan daftar notifikasi Anda tanpa menghapus data pengajuan.') && $wire.clearNotifications())"
            class="sipetron-notif-action-btn sipetron-notif-action-btn--clear"
            title="{{ __('filament-notifications::database.modal.actions.clear.label') }}"
        >
            <svg class="sipetron-notif-action-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
            </svg>
            <span>{{ __('filament-notifications::database.modal.actions.clear.label') }}</span>
        </button>
    @endif
</div>
