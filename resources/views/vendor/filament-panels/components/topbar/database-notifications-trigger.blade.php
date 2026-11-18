<div class="relative inline-flex items-center group/admin-notif" style="position: relative; display: inline-flex; align-items: center;">
    <button
        type="button"
        x-data="{}"
        x-on:click="$dispatch('open-modal', { id: 'database-notifications' })"
        aria-label="Notifikasi Sistem"
        class="fi-topbar-database-notifications-btn sipetron-bell-btn"
    >
        <svg class="sipetron-bell-svg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>

        <span class="sipetron-bell-badge" style="{{ (!empty($unreadNotificationsCount) && $unreadNotificationsCount > 0) ? '' : 'display: none !important;' }}">
            {{ (!empty($unreadNotificationsCount) && $unreadNotificationsCount > 99) ? '99+' : ($unreadNotificationsCount ?? 0) }}
        </span>
    </button>

    {{-- Tooltip Notifikasi Admin (Selaras dengan Portal Pengaju) --}}
    <div role="tooltip" class="sipetron-admin-notif-tooltip">
        <div class="sipetron-admin-notif-tooltip-inner">
            {{ (!empty($unreadNotificationsCount) && $unreadNotificationsCount > 0) ? ($unreadNotificationsCount . ' Notifikasi Baru') : 'Tidak ada notifikasi baru' }}
            <div class="sipetron-admin-notif-tooltip-arrow"></div>
        </div>
    </div>
</div>
