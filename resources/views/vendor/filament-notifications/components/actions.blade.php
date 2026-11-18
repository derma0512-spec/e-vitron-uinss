@props([
    'actions',
])

<div
    {{ $attributes->class([
        'fi-no-notification-actions flex items-center gap-x-2',
        '[&_.fi-btn]:text-xs [&_.fi-btn]:py-1.5 [&_.fi-btn]:px-3.5 [&_.fi-btn]:rounded-lg [&_.fi-btn]:font-semibold',
        '[&_.fi-btn]:shadow-2xs hover:[&_.fi-btn]:shadow-xs hover:[&_.fi-btn]:-translate-y-0.5 [&_.fi-btn]:transition-all',
    ]) }}
>
    @foreach ($actions as $action)
        {{ $action }}
    @endforeach
</div>
