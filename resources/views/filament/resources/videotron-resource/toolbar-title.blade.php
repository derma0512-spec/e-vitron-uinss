@php
    $livewire = \Livewire\Livewire::current();
    $records = ($livewire && method_exists($livewire, 'getTableRecords')) ? $livewire->getTableRecords() : null;
    $count = match (true) {
        $records instanceof \Illuminate\Contracts\Pagination\Paginator => $records->total(),
        $records instanceof \Illuminate\Support\Collection => $records->count(),
        default => \App\Models\Videotron::count(),
    };
@endphp

<div class="videotron-toolbar-left">
    <div class="videotron-toolbar-title-wrap">
        <h3 class="videotron-toolbar-title">Daftar Videotron</h3>
        <span class="videotron-toolbar-badge" id="videotron-badge-count">{{ $count }} Perangkat</span>
    </div>
</div>
