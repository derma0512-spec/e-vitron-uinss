@extends('errors.layout')

@section('title', '503 - Pemeliharaan Sistem')

@section('error_content')
    <div class="flex flex-col items-center select-none">
        <!-- Friendly Vector Illustration for Maintenance -->
        <div class="relative w-[230px] sm:w-[260px] h-[150px] sm:h-[170px] mx-auto">
            <svg viewBox="0 0 320 210" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full overflow-visible">
                <!-- Ground Soft Shadow -->
                <ellipse cx="160" cy="188" rx="100" ry="8" fill="#E8F0EB" />

                <!-- Foliage / Leaves on Left -->
                <g transform="translate(100, 105)">
                    <path d="M12 40 C-8 15 -18 -8 -5 -24 C5 -12 25 10 20 40 Z" fill="#75A691" />
                    <path d="M22 42 C12 20 -2 -2 5 -20 C18 -8 38 10 30 42 Z" fill="#50806E" />
                    <path d="M10 24 C14 10 22 2 28 -5" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" opacity="0.6" />
                </g>

                <!-- Sun Sparks -->
                <g stroke="#EAA824" stroke-width="3" stroke-linecap="round">
                    <line x1="124" y1="26" x2="114" y2="18" />
                    <line x1="134" y1="18" x2="132" y2="6" />
                    <line x1="148" y1="24" x2="154" y2="14" />
                </g>

                <!-- Browser Window Container -->
                <g filter="drop-shadow(0 6px 16px rgba(14,77,60,0.08))">
                    <rect x="100" y="42" width="130" height="110" rx="12" fill="#FFFFFF" stroke="#5D8274" stroke-width="2.5" />
                    <path d="M101.5 54 C101.5 47.5 106.5 43.5 113 43.5 H217 C223.5 43.5 228.5 47.5 228.5 54 V64 H101.5 V54 Z" fill="#5D8274" />
                    <circle cx="111" cy="54" r="3" fill="#EF4444" />
                    <circle cx="119" cy="54" r="3" fill="#F59E0B" />
                    <circle cx="127" cy="54" r="3" fill="#10B981" />

                    <!-- Gear / Maintenance Icon in Center -->
                    <circle cx="165" cy="104" r="24" fill="#EAA824" />
                    <g transform="translate(153, 92)" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none">
                        <circle cx="12" cy="12" r="4" />
                        <path d="M12 2v2m0 16v2M2 12h2m16 0h2m-3.05-7.05l-1.41 1.41M6.46 17.54l-1.41 1.41m0-13.9l1.41 1.41m11.08 11.08l1.41 1.41" />
                    </g>
                </g>
            </svg>
        </div>

        <!-- Heading & Deskripsi -->
        <h1 class="font-montserrat text-2xl sm:text-[26px] font-black text-[#0F3D2E] tracking-tight mt-4 mb-2 leading-tight">
            Sistem Sedang Dipelihara
        </h1>

        <p class="text-xs sm:text-[13.5px] text-[#627B73] max-w-md mx-auto leading-relaxed mb-6 font-normal">
            Layanan E-VITRON sedang dalam proses pemeliharaan berkala untuk peningkatan performa dan keamanan. Layanan akan segera aktif kembali.
        </p>

        <!-- Tombol Tindakan -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full">
            <button onclick="window.location.reload()" 
                    type="button"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-[#0F3D2E] hover:bg-[#16533F] text-white text-xs sm:text-sm font-bold shadow-md hover:shadow-lg transition-all active:scale-[0.98]">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>Muat Ulang Status</span>
            </button>
        </div>
    </div>
@endsection
