@props([
    'unreadNotificationsCount',
])

<div class="sipetron-notif-header-title-row">
    <h2 class="sipetron-notif-header-title">
        {{ __('filament-notifications::database.modal.heading') }}
    </h2>

    @if ($unreadNotificationsCount)
        <span class="sipetron-notif-badge" title="{{ $unreadNotificationsCount }} belum dibaca">
            {{ $unreadNotificationsCount }}
        </span>
    @endif
</div>

<p class="sipetron-notif-heading-subtitle">
    Pantau pembaruan pengajuan videotron Anda.
</p>
