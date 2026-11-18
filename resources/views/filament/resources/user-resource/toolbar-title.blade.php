@php
    $livewire = \Livewire\Livewire::current();
    $records = ($livewire && method_exists($livewire, 'getTableRecords')) ? $livewire->getTableRecords() : null;
    $count = match (true) {
        $records instanceof \Illuminate\Contracts\Pagination\Paginator => $records->total(),
        $records instanceof \Illuminate\Support\Collection => $records->count(),
        default => \App\Models\User::where('role', '!=', 'admin')->where('auth_type', 'local')->count(),
    };
@endphp

<div class="videotron-toolbar-left">
    <div class="videotron-toolbar-title-wrap">
        <h3 class="videotron-toolbar-title">Daftar Akun Pendaftar</h3>
        <span class="videotron-toolbar-badge">{{ $count }} Akun</span>
    </div>
</div>

