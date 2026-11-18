@if ($paginator->total() > 0)
    <nav role="navigation" aria-label="Navigasi Halaman" class="flex flex-col-reverse sm:flex-row items-center justify-between gap-3 sm:gap-4 w-full pt-4 pb-2">
        {{-- Teks Informasi Hasil & Custom Dropdown Jumlah Per Halaman --}}
        <div class="flex items-center justify-center sm:justify-start gap-2 text-xs text-[#4E655F] w-full sm:w-auto">
            <div class="whitespace-nowrap">
                <span class="hidden sm:inline">Menampilkan </span>
                <span class="font-bold text-[#0E4D3C]">{{ $paginator->firstItem() ?? 0 }}</span>
                <span class="text-slate-400">–</span>
                <span class="font-bold text-[#0E4D3C]">{{ $paginator->lastItem() ?? 0 }}</span>
                <span class="text-slate-500"> dari </span>
                <span class="font-bold text-[#0E4D3C]">{{ $paginator->total() }}</span>
                <span class="text-slate-500"> pengajuan</span>
            </div>

            <span class="text-slate-300">&bull;</span>

            <div class="flex items-center gap-1.5 whitespace-nowrap" x-data="{ open: false }">
                <span class="text-slate-500 font-medium hidden sm:inline">Tampilkan:</span>
                
                {{-- Custom Dropdown Menu (Membuka ke Atas agar serasi & tidak tertutup footer) --}}
                <div class="relative">
                    <button type="button" 
                            @click="open = !open" 
                            class="bg-white/95 border border-[#0E4D3C]/20 hover:border-[#0E4D3C] text-[#0E4D3C] font-bold text-xs rounded-xl px-2 sm:px-2.5 py-0.5 sm:py-1 inline-flex items-center gap-1 sm:gap-1.5 shadow-2xs hover:shadow-xs transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#0E4D3C]/20 select-none"
                            :class="open ? 'border-[#0E4D3C] ring-2 ring-[#0E4D3C]/15' : ''">
                        <span>{{ request('per_page', 9) }}</span>
                        <svg class="h-3 w-3 text-slate-400 transition-transform duration-200" 
                             :class="open ? 'rotate-180 text-[#0E4D3C]' : ''" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Popover Options (Buka ke Atas: bottom-full mb-1.5) --}}
                    <div x-show="open" 
                         @click.away="open = false" 
                         x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         class="absolute bottom-full left-1/2 -translate-x-1/2 sm:left-0 sm:translate-x-0 mb-1.5 w-24 bg-white/95 backdrop-blur-md border border-[#0E4D3C]/15 rounded-xl shadow-lg shadow-[#0E4D3C]/10 p-1 z-50">
                        <div class="space-y-0.5">
                            @foreach([6, 9, 12, 18, 24] as $limit)
                                @php $isSelected = (int) request('per_page', 9) === $limit; @endphp
                                <a href="{{ request()->fullUrlWithQuery(['per_page' => $limit, 'page' => 1]) }}"
                                   class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs transition-all select-none {{ $isSelected ? 'bg-[#0E4D3C] text-white font-bold shadow-2xs' : 'text-slate-700 font-semibold hover:bg-[#EAF5F0] hover:text-[#0E4D3C]' }}">
                                    <span>{{ $limit }}</span>
                                    @if($isSelected)
                                        <svg class="w-3 h-3 text-[#D9A93B]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <span class="text-slate-500">
                    <span class="sm:hidden">/ hal</span>
                    <span class="hidden sm:inline">per halaman</span>
                </span>
            </div>
        </div>

        {{-- Tombol Navigasi Halaman (Pill di Tengah pada Layar Mobile) --}}
        @if ($paginator->hasPages())
            <div class="inline-flex items-center gap-1 sm:gap-1.5 p-1 rounded-2xl bg-white/95 border border-[#0E4D3C]/15 shadow-2xs">
                {{-- Tombol Halaman Sebelumnya --}}
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-slate-300 cursor-not-allowed select-none" aria-disabled="true" aria-label="Sebelumnya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-slate-600 hover:text-[#0E4D3C] hover:bg-[#EAF5F0] transition-colors cursor-pointer" aria-label="Sebelumnya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                @endif

                {{-- Elemen Nomor Halaman --}}
                @foreach ($elements as $element)
                    {{-- Pemisah Tiga Titik "..." --}}
                    @if (is_string($element))
                        <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-semibold text-slate-400 select-none">{{ $element }}</span>
                    @endif

                    {{-- Array Tautan Nomor Halaman --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-xs font-bold bg-[#0E4D3C] text-white shadow-xs select-none" aria-current="page">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-xs font-semibold text-slate-700 hover:text-[#0E4D3C] hover:bg-[#EAF5F0] transition-colors cursor-pointer">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Tombol Halaman Berikutnya --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-slate-600 hover:text-[#0E4D3C] hover:bg-[#EAF5F0] transition-colors cursor-pointer" aria-label="Berikutnya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @else
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-slate-300 cursor-not-allowed select-none" aria-disabled="true" aria-label="Berikutnya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </span>
                @endif
            </div>
        @endif
    </nav>
@endif
