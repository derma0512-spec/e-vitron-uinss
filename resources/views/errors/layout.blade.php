<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'E-VITRON - Layanan Pengajuan Penayangan Videotron')</title>

    <!-- Favicon E-VITRON -->
    <link rel="icon" type="image/png" href="{{ asset('images/sipetron-icon.png') }}?v=3">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/sipetron-icon.png') }}?v=3">
    <link rel="apple-touch-icon" href="{{ asset('images/sipetron-icon.png') }}?v=3">

    <!-- Tailwind CSS CDN -->
    <script src="{{ asset('js/tailwind.js') }}"></script>

    <!-- Alpine.js -->
    <script defer src="{{ asset('js/alpine.min.js') }}"></script>

    <!-- Google Fonts: Montserrat & Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        [x-cloak] { display: none !important; }

        body { 
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, -apple-system, sans-serif; 
            background-color: #F5F8F5;
            background-image: 
                radial-gradient(circle at 12% 15%, rgba(217, 164, 65, 0.08) 0%, transparent 35%),
                radial-gradient(circle at 88% 20%, rgba(217, 164, 65, 0.09) 0%, transparent 40%),
                radial-gradient(circle at 50% 85%, rgba(15, 61, 46, 0.05) 0%, transparent 45%);
            color: #1A332C;
            margin: 0;
            padding: 0;
        }

        .font-montserrat {
            font-family: 'Montserrat', 'Plus Jakarta Sans', sans-serif;
        }

        /* Islamic Geometric Ornament Pattern (Selaras dengan Beranda & Halaman Login) */
        .ornament-pattern {
            pointer-events: none;
            position: absolute;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60' viewBox='0 0 60 60'%3E%3Cg fill='none' stroke-linejoin='round'%3E%3Cg stroke='%23DDBA6E' stroke-width='3.6'%3E%3Cpath d='M30 9 L36.2 15.1 L44.8 15.2 L44.9 23.8 L51 30 L44.9 36.2 L44.8 44.8 L36.2 44.9 L30 51 L23.8 44.9 L15.2 44.8 L15.1 36.2 L9 30 L15.1 23.8 L15.2 15.2 L23.8 15.1 Z'/%3E%3Cpath d='M0 0 L15.2 15.2 M60 0 L44.8 15.2 M0 60 L15.2 44.8 M60 60 L44.8 44.8'/%3E%3Cpath d='M30 19 L41 30 L30 41 L19 30 Z'/%3E%3C/g%3E%3Cg stroke='%23FBF6EA' stroke-width='1.3'%3E%3Cpath d='M30 9 L36.2 15.1 L44.8 15.2 L44.9 23.8 L51 30 L44.9 36.2 L44.8 44.8 L36.2 44.9 L30 51 L23.8 44.9 L15.2 44.8 L15.1 36.2 L9 30 L15.1 23.8 L15.2 15.2 L23.8 15.1 Z'/%3E%3Cpath d='M0 0 L15.2 15.2 M60 0 L44.8 15.2 M0 60 L15.2 44.8 M60 60 L44.8 44.8'/%3E%3Cpath d='M30 19 L41 30 L30 41 L19 30 Z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            background-size: 60px 60px;
            mask-image: radial-gradient(circle at center, black 25%, transparent 68%);
            -webkit-mask-image: radial-gradient(circle at center, black 25%, transparent 68%);
        }
    </style>
</head>
<body class="min-h-screen bg-[#F5F8F5] flex flex-col justify-between relative overflow-x-hidden selection:bg-[#0F3D2E] selection:text-white"
    x-data="{
        showHelpModal: false,
        copiedEmail: false,
        copyEmail() {
            const email = 'info@uinssc.ac.id';
            const done = () => { this.copiedEmail = true; setTimeout(() => { this.copiedEmail = false; }, 2500); };
            const fallback = () => {
                const t = document.createElement('textarea');
                t.value = email; t.setAttribute('readonly', '');
                t.style.position = 'fixed'; t.style.opacity = '0';
                document.body.appendChild(t); t.select();
                try { document.execCommand('copy'); done(); } catch (err) { console.warn('[E-VITRON] Clipboard fallback failed:', err); }
                document.body.removeChild(t);
            };
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(email).then(done).catch(fallback);
            } else {
                fallback();
            }
        }
    }"
    @keydown.escape.window="showHelpModal = false">

    {{-- Elemen Ornamen Emas Khas UIN SSC di Background --}}
    <div class="absolute inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
        <div class="ornament-pattern absolute -left-12 -top-12 w-80 h-80 opacity-20"></div>
        <div class="ornament-pattern absolute -right-16 -top-16 w-80 h-80 opacity-20"></div>
        <div class="ornament-pattern absolute left-10 -bottom-16 w-96 h-64 opacity-20"></div>
        <div class="ornament-pattern absolute -right-10 -bottom-14 w-80 h-64 opacity-20"></div>
    </div>

    <!-- =========================================================
         HEADER BRANDING (Bersih, Rapi, & Standar Industri)
         ========================================================= -->
    {{-- Posisi, tinggi (70px), container, dan ukuran logo identik dengan navbar layouts.portal agar tidak "loncat" saat pindah ke Beranda --}}
    <header class="relative z-10 w-full h-[70px] select-none">
        <div class="mx-auto flex h-full max-w-[1400px] items-center justify-between px-4 sm:px-6 md:px-10 lg:px-14">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 sm:gap-3 group select-none" title="Menuju Beranda E-VITRON">
                <img src="{{ asset('images/sipetron-icon.png') }}" alt="Logo E-VITRON" class="h-9 w-9 sm:h-10 sm:w-10 object-contain transition-transform duration-200 group-hover:scale-105" />
                <div class="flex flex-col justify-center leading-none">
                    <span class="font-montserrat text-[19px] sm:text-[21px] font-extrabold tracking-tight text-[#0E4D3C] leading-none">E-VITRON</span>
                    <span class="text-[9px] sm:text-[9.5px] font-semibold text-[#B88219] tracking-tight mt-1 leading-none md:hidden">Pengajuan Videotron</span>
                    <span class="hidden md:inline-block text-[9.5px] font-semibold text-[#B88219] tracking-tight mt-1 leading-none">Layanan Pengajuan Penayangan Videotron</span>
                </div>
            </a>

            <!-- Badge Kampus Resmi UIN SSC -->
            <div class="hidden sm:inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/80 border border-[#DCE7DF] text-[#1A332C] text-xs font-semibold shadow-xs">
                <span class="w-2 h-2 rounded-full bg-[#10B981]"></span>
                <span>UIN Siber Syekh Nurjati Cirebon</span>
            </div>
        </div>
    </header>

    <!-- =========================================================
         MAIN CONTAINER (Tengah Layar, Pas 1 Layar Tanpa Scroll)
         ========================================================= -->
    <main class="relative z-10 mx-auto w-full max-w-6xl px-4 py-2 sm:py-4 flex-1 flex items-center justify-center">
        <!-- Kartu Konten Error -->
        <div class="w-full max-w-lg sm:max-w-xl bg-white rounded-[24px] sm:rounded-3xl border border-[#DCE7DF] shadow-[0_12px_40px_-10px_rgba(15,61,46,0.08)] px-6 py-7 sm:px-10 sm:py-9 text-center relative overflow-hidden">
            <!-- Decorative Top Accent Bar Emas & Hijau -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-[#0F3D2E] via-[#0B6B43] to-[#D9A441]"></div>

            @yield('error_content')
        </div>
    </main>

    <!-- =========================================================
         FOOTER HALAMAN (Identik Halaman Login E-VITRON)
         ========================================================= -->
    <footer class="relative z-10 w-full py-3 px-6 select-none">
        <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-1.5 text-xs text-[#647B71]">
            <p class="font-medium text-[11.5px] text-center sm:text-left">
                E-VITRON &bull; UIN Siber Syekh Nurjati Cirebon
            </p>
            
            <div class="flex items-center gap-1.5 text-[11.5px]">
                <span class="text-[#647B71] font-medium">Butuh bantuan?</span>
                <button 
                    type="button" 
                    @click="showHelpModal = true"
                    class="font-bold text-[#0F3D2E] hover:text-[#D9A441] transition-colors cursor-pointer underline underline-offset-3 decoration-[#0F3D2E]/40 hover:decoration-[#D9A441]">
                    Bantuan Layanan
                </button>
            </div>
        </div>
    </footer>

    <!-- =========================================================
         MODAL BANTUAN & KONTAK LAYANAN E-VITRON (100% IDENTIK LOGIN)
         ========================================================= -->
    <div 
        x-show="showHelpModal" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 sm:p-6"
        role="dialog" 
        aria-modal="true">
        
        <!-- Backdrop -->
        <div 
            x-show="showHelpModal"
            @click="showHelpModal = false"
            class="fixed inset-0 bg-black/60 cursor-pointer"></div>

        <!-- Modal Panel -->
        <div 
            x-show="showHelpModal"
            class="relative w-full max-w-lg bg-white rounded-[22px] sm:rounded-[24px] p-5 sm:p-7 shadow-2xl border border-[#DCE7DF] z-10 overflow-hidden">
            
            <!-- Header Modal -->
            <div class="flex items-start justify-between gap-3 pb-3.5 border-b border-[#DCE7DF]">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-[14px] bg-[#E8F3EC] text-[#0F3D2E]">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-[#0F3D2E] leading-tight">
                            Pusat Bantuan Layanan E-VITRON
                        </h3>
                        <p class="text-xs text-[#647B71] font-medium mt-0.5">
                            Layanan Pengelola E-VITRON &bull; UIN Siber Syekh Nurjati Cirebon
                        </p>
                    </div>
                </div>
                <button 
                    type="button" 
                    @click="showHelpModal = false"
                    class="text-[#647B71] hover:text-[#0F3D2E] p-1.5 rounded-lg hover:bg-[#F5F8F5] transition-colors focus:outline-none cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Body Modal -->
            <div class="py-3.5 space-y-3.5">
                <!-- Kontak Pengelola -->
                <div class="p-3.5 rounded-[16px] bg-[#F5F8F5] border border-[#DCE7DF]/80 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-white border border-[#DCE7DF] flex items-center justify-center text-[#0F3D2E] shadow-2xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold text-[#647B71]">Surel Layanan Resmi</p>
                            <p class="text-xs font-bold text-[#0F3D2E]">info@uinssc.ac.id</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button 
                            type="button" 
                            @click="copyEmail()"
                            class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-[#0F3D2E] bg-[#E8F3EC] hover:bg-[#D4E8DC] transition-colors flex items-center gap-1 cursor-pointer"
                            title="Salin alamat email">
                            <span x-text="copiedEmail ? 'Tersalin! ✓' : 'Salin'">Salin</span>
                        </button>
                        <a 
                            href="https://mail.google.com/mail/?view=cm&amp;fs=1&amp;to=info@uinssc.ac.id&amp;su=Bantuan%20Layanan%20SIPETRON%20UIN%20SSC" target="_blank" rel="noopener noreferrer"
                            class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-white bg-[#0F3D2E] hover:bg-[#0A2A20] transition-colors inline-flex items-center gap-1">
                            Kirim
                        </a>
                    </div>
                </div>

                <!-- Lokasi Offline -->
                <div class="flex items-start gap-2.5 p-3 rounded-[14px] bg-[#F5F8F5] text-xs text-[#647B71]">
                    <svg class="w-4 h-4 text-[#0F3D2E] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <p class="text-[11px] leading-relaxed">
                        <strong>Layanan Langsung:</strong> Gedung Rektorat Kampus UIN Siber Syekh Nurjati Cirebon. Jam pelayanan: Senin &ndash; Jumat (08.00 &ndash; 16.00 WIB).
                    </p>
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="pt-3 border-t border-[#DCE7DF] flex justify-end">
                <button 
                    type="button" 
                    @click="showHelpModal = false"
                    class="px-5 py-2 rounded-xl bg-[#0F3D2E] text-white text-xs font-bold hover:bg-[#0A2A20] transition-colors cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</body>
</html>
