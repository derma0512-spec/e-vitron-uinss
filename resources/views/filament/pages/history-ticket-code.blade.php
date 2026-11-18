@php
    $record = $getRecord();
    $ticketCode = $getState();
    $createdAt = $record?->created_at?->locale('id')->translatedFormat('d M Y');
    $recordKey = $record?->getKey();
@endphp

<div 
    class="px-3 py-4" 
    style="white-space: nowrap; cursor: pointer;"
    wire:click="mountTableAction('view_detail', '{{ $recordKey }}')"
>
    <div>
        <span 
            title="Klik untuk melihat detail arsip"
            style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: 0.78125rem; font-weight: 700; color: #0F172A; letter-spacing: -0.01em;"
            class="hover:underline hover:text-emerald-800 transition-colors"
        >
            {{ $ticketCode }}
        </span>
    </div>
    @if($createdAt)
        <div 
            title="Tanggal pengajuan dibuat"
            style="font-size: 0.65625rem; color: #94A3B8; margin-top: 0.15rem;"
        >
            {{ $createdAt }}
        </div>
    @endif
</div>
