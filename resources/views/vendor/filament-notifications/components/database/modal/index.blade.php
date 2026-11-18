@props([
    'notifications',
    'unreadNotificationsCount',
])

@php
    use Filament\Support\Enums\Alignment;

    $hasNotifications = $notifications->count();
    $isPaginated = $notifications instanceof \Illuminate\Contracts\Pagination\Paginator && $notifications->hasPages();
@endphp

<x-filament::modal
    :alignment="$hasNotifications ? null : Alignment::Center"
    close-button
    :description="$hasNotifications ? null : __('filament-notifications::database.modal.empty.description')"
    :heading="$hasNotifications ? null : __('filament-notifications::database.modal.empty.heading')"
    :icon="$hasNotifications ? null : 'heroicon-o-bell-slash'"
    :icon-alias="$hasNotifications ? null : 'notifications::database.modal.empty-state'"
    :icon-color="$hasNotifications ? null : 'gray'"
    id="database-notifications"
    slide-over
    :sticky-header="$hasNotifications"
    width="md"
    :extra-modal-window-attribute-bag="new \Illuminate\View\ComponentAttributeBag(['wire:ignore.self' => ''])"
>
    @if ($hasNotifications)
        <x-slot name="header">
            <div class="sipetron-notif-header-wrap">
                <x-filament-notifications::database.modal.heading
                    :unread-notifications-count="$unreadNotificationsCount"
                />

                <x-filament-notifications::database.modal.actions
                    :notifications="$notifications"
                    :unread-notifications-count="$unreadNotificationsCount"
                />
            </div>
        </x-slot>

        <div
            @class([
                '-mx-6 -mt-6 sipetron-notif-list',
                '-mb-6' => ! $isPaginated,
                'border-b border-gray-200 dark:border-white/10' => $isPaginated,
            ])
        >
            @foreach ($notifications as $notification)
                {{-- data-unread dipakai CSS untuk dot & border biru --}}
                <div wire:key="admin-notif-{{ $notification->id }}" data-unread="{{ $notification->unread() ? 'true' : 'false' }}">
                    @php
                        $notifDto = $this->getNotification($notification);
                        if ($notification->created_at) {
                            $notifDto->date($notification->created_at->locale('id')->diffForHumans());
                        }
                    @endphp
                    {{ $notifDto->inline() }}
                </div>
            @endforeach
        </div>

        @if ($isPaginated)
            <x-slot name="footer">
                <x-filament::pagination :paginator="$notifications" />
            </x-slot>
        @endif
    @endif
</x-filament::modal>
