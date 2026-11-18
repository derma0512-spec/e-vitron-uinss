<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light only">
    @include('layouts.partials.demo-kiosk-viewport')
    <title>Masuk | E-VITRON - Layanan Pengajuan Penayangan Videotron</title>

    <!-- Favicon E-VITRON -->
    <link rel="icon" type="image/png" href="{{ asset('images/sipetron-icon.png') }}?v=2">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/sipetron-icon.png') }}?v=2">
    <link rel="apple-touch-icon" href="{{ asset('images/sipetron-icon.png') }}?v=2">

    <!-- Preconnect CDN & Fonts (Zero DNS/TLS Delay) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Tailwind CSS CDN -->
    <script src="{{ asset('js/tailwind.js') }}"></script>

    <!-- Alpine.js -->
    <script defer src="{{ asset('js/alpine.min.js') }}"></script>

    <!-- Cegah Akses Balik / BFCache: Jika pengguna sudah login lalu klik Back di browser, otomatis diarahkan kembali ke halaman pengaju/admin -->
    <script>
        window.addEventListener('pageshow', function (event) {
            if (event.persisted || (window.performance && window.performance.getEntriesByType("navigation")[0]?.type === "back_forward")) {
                window.location.reload();
            }
        });
    </script>
    @auth
    <script>
        window.location.replace("{{ Auth::user()->role === 'admin' ? url('/admin') : route('home') }}");
    </script>
    @endauth

    <!-- Google Fonts: Montserrat (Heading) & Plus Jakarta Sans (Body) -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        [x-cloak] { display: none !important; }

        html, :root {
            color-scheme: light only;
        }

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

        /* Islamic Geometric Ornament Pattern (Selaras dengan Beranda & Portal UIN SSC) */
        .ornament-pattern {
            pointer-events: none;
            position: absolute;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60' viewBox='0 0 60 60'%3E%3Cg fill='none' stroke-linejoin='round'%3E%3Cg stroke='%23DDBA6E' stroke-width='3.6'%3E%3Cpath d='M30 9 L36.2 15.1 L44.8 15.2 L44.9 23.8 L51 30 L44.9 36.2 L44.8 44.8 L36.2 44.9 L30 51 L23.8 44.9 L15.2 44.8 L15.1 36.2 L9 30 L15.1 23.8 L15.2 15.2 L23.8 15.1 Z'/%3E%3Cpath d='M0 0 L15.2 15.2 M60 0 L44.8 15.2 M0 60 L15.2 44.8 M60 60 L44.8 44.8'/%3E%3Cpath d='M30 19 L41 30 L30 41 L19 30 Z'/%3E%3C/g%3E%3Cg stroke='%23FBF6EA' stroke-width='1.3'%3E%3Cpath d='M30 9 L36.2 15.1 L44.8 15.2 L44.9 23.8 L51 30 L44.9 36.2 L44.8 44.8 L36.2 44.9 L30 51 L23.8 44.9 L15.2 44.8 L15.1 36.2 L9 30 L15.1 23.8 L15.2 15.2 L23.8 15.1 Z'/%3E%3Cpath d='M0 0 L15.2 15.2 M60 0 L44.8 15.2 M0 60 L15.2 44.8 M60 60 L44.8 44.8'/%3E%3Cpath d='M30 19 L41 30 L30 41 L19 30 Z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            background-size: 60px 60px;
            mask-image: radial-gradient(circle at center, black 25%, transparent 68%);
            -webkit-mask-image: radial-gradient(circle at center, black 25%, transparent 68%);
        }

        /* Kartu Manfaat (Tampil Langsung Tanpa Efek Memudar) */
        .benefit-card {
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.25s ease;
        }

        .benefit-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 22px -3px rgba(15, 61, 46, 0.10);
            border-color: rgba(15, 61, 46, 0.28);
        }

        /* Hilangkan warna autofill buatan browser */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0px 1000px #FFFFFF inset !important;
            -webkit-text-fill-color: #0F3D2E !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        /* Hilangkan ikon mata & clear bawaan browser (Microsoft Edge & Chrome) agar tidak bertumpuk */
        input::-ms-reveal,
        input::-ms-clear {
            display: none !important;
        }
    </style>
</head>
<body class="min-h-screen bg-[#F5F8F5] flex flex-col justify-between relative overflow-x-hidden selection:bg-[#0F3D2E] selection:text-white"
    x-data="{
        identifier: '{{ old('identifier', '') }}',
        password: '',
        activeDemo: '',
        showPassword: false,
        showHelpModal: false,
        copiedEmail: false,
        fillDemo(role, user, pass) {
            this.identifier = user;
            this.password = pass;
            this.activeDemo = role;
        },
        copyEmail(targetEmail) {
            const email = targetEmail || 'info@uinssc.ac.id';
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

    {{-- Elemen Ornamen Emas Khas UIN SSC di Background (Ditempatkan presisi di area yang diminta dengan opasitas lembut) --}}
    <div class="absolute inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
        <!-- Area 1 (Situ): Sebelah Kanan Judul E-VITRON / Ruang Kosong Atas -->
        <div class="ornament-pattern absolute left-[32%] sm:left-[38%] -top-8 w-64 h-64 sm:w-80 sm:h-80 opacity-20"></div>

        <!-- Area 2 (Sama Situ): Di Bawah 3 Kartu Manfaat / Ruang Kosong Bawah Sebelum Footer -->
        <div class="ornament-pattern absolute left-6 sm:left-20 -bottom-12 w-96 h-60 sm:w-[500px] sm:h-64 opacity-25"></div>

        <!-- Area 3 (Bawah Kartu Login): Ruang Kosong di Bawah Kartu Login Sebelum Footer -->
        <div class="ornament-pattern absolute right-4 sm:right-12 -bottom-14 w-80 h-60 sm:w-96 sm:h-64 opacity-25"></div>

        <!-- Aksen Sudut Kanan Atas (Sangat Lembut untuk Keseimbangan) -->
        <div class="ornament-pattern absolute -right-20 -top-20 w-80 h-80 sm:w-96 sm:h-96 opacity-25"></div>
    </div>

    <!-- MAIN CONTAINER (1240px max, Terpusat, Proporsional dan Pas di Layar Laptop Tanpa Scroll) -->
    <main class="relative z-10 mx-auto w-full max-w-[1240px] px-4 sm:px-8 py-3 sm:py-3 lg:py-4 flex-1 flex items-center justify-center">
        
        <div class="w-full grid grid-cols-1 lg:grid-cols-12 gap-3.5 sm:gap-6 lg:gap-12 items-center">

            <!-- =========================================================
                 KOLOM KIRI: BRANDING & GAMBAR HERO
                 ========================================================= -->
            <div class="lg:col-span-7 flex flex-col justify-between space-y-2.5 sm:space-y-3.5 select-none">
                
                <!-- Tampilan Khusus Mobile (< sm): Crown Lockup Simetris, Elegan, dan Pas 1 Layar -->
                <div class="sm:hidden flex flex-col items-center text-center select-none pt-1">
                    <img src="{{ asset('images/sipetron-icon.png') }}?v=3" 
                         alt="Logo E-VITRON" 
                         class="h-11 w-auto object-contain shrink-0 mb-1.5" />
                    <h1 class="font-montserrat text-[26px] font-black tracking-tight text-[#0F3D2E] leading-none">
                        E-VITRON
                    </h1>
                    <p class="text-xs font-semibold text-[#1A332C] mt-1 tracking-tight">
                        UIN Siber Syekh Nurjati Cirebon
                    </p>
                    <div class="mt-1.5 w-10 h-0.5 rounded-full bg-[#D9A441]"></div>
                </div>

                <!-- Tampilan Tablet & Desktop (sm: dan lg:) — 100% UTUH PERSIS ASLI -->
                <div class="hidden sm:flex items-center gap-3.5">
                    <img src="{{ asset('images/sipetron-icon.png') }}?v=3" 
                         alt="Logo E-VITRON UIN Siber Syekh Nurjati Cirebon" 
                         class="h-11 w-auto object-contain shrink-0" />
                    <div class="flex flex-col justify-center leading-tight">
                        <span class="text-[13px] font-semibold text-[#1A332C]">UIN Siber</span>
                        <span class="text-[15px] font-bold text-[#0F3D2E] tracking-tight">Syekh Nurjati Cirebon</span>
                    </div>
                </div>

                <div class="hidden sm:block">
                    <h1 class="font-montserrat text-3xl lg:text-[42px] font-black tracking-tight text-[#0F3D2E] leading-none">
                        E-VITRON
                    </h1>
                    
                    {{-- Aksen Garis Emas --}}
                    <div class="mt-1.5 mb-1.5 w-12 h-1 rounded-full bg-[#D9A441]"></div>

                    <h2 class="text-base lg:text-[18px] font-bold text-[#0F3D2E] tracking-tight">
                        Layanan Pengajuan Penayangan Videotron
                    </h2>
                    
                    <p class="mt-0.5 text-[13px] text-[#647B71] font-medium leading-relaxed max-w-xl">
                        Media terpadu untuk publikasi informasi dan kegiatan kampus.
                    </p>
                </div>

                <!-- 3. Gambar Hero Kampus & Videotron (Ditampilkan pada Layar Tablet & Desktop) -->
                <div class="hidden sm:block w-full aspect-[16/9] max-h-[175px] sm:max-h-[195px] lg:max-h-[235px] rounded-[16px] overflow-hidden border border-[#DCE7DF] shadow-sm bg-white select-none">
                    <img 
                        src="{{ asset('images/campus-hero-clean.jpg') }}?v=2" 
                        alt="Videotron Kampus UIN Siber Syekh Nurjati Cirebon" 
                        class="w-full h-full object-cover object-center"
                    />
                </div>

                <!-- 4. Tiga Manfaat Layanan (Proporsional dengan Animasi & Micro-Interaksi) -->
                <div class="hidden sm:grid grid-cols-3 gap-2.5 pt-0.5 select-none">
                    
                    {{-- Manfaat 1: Pengajuan Online --}}
                    <div class="benefit-card group flex items-center gap-2.5 p-2 sm:p-2.5 rounded-xl bg-white border border-[#DCE7DF] shadow-xs cursor-default">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#E8F3EC] text-[#0F3D2E] group-hover:bg-[#0F3D2E] group-hover:text-white group-hover:scale-105 transition-all duration-300">
                            <svg class="w-4 h-4 transition-transform duration-300 group-hover:rotate-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <h3 class="text-xs font-bold text-[#0F3D2E] group-hover:text-[#0A2A20] transition-colors truncate">Pengajuan Online</h3>
                            <p class="text-[10.5px] text-[#647B71] truncate">Mudah & cepat</p>
                        </div>
                    </div>

                    {{-- Manfaat 2: Pantau Status --}}
                    <div class="benefit-card group flex items-center gap-2.5 p-2 sm:p-2.5 rounded-xl bg-white border border-[#DCE7DF] shadow-xs cursor-default">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#E8F3EC] text-[#0F3D2E] group-hover:bg-[#0F3D2E] group-hover:text-white group-hover:scale-105 transition-all duration-300">
                            <svg class="w-4 h-4 transition-transform duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <circle cx="12" cy="12" r="9"></circle>
                                <polyline points="12 7 12 12 15 15"></polyline>
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <h3 class="text-xs font-bold text-[#0F3D2E] group-hover:text-[#0A2A20] transition-colors truncate">Pantau Status</h3>
                            <p class="text-[10.5px] text-[#647B71] truncate">Secara real-time</p>
                        </div>
                    </div>

                    {{-- Manfaat 3: Informasi Penayangan --}}
                    <div class="benefit-card group flex items-center gap-2.5 p-2 sm:p-2.5 rounded-xl bg-white border border-[#DCE7DF] shadow-xs cursor-default">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#E8F3EC] text-[#0F3D2E] group-hover:bg-[#0F3D2E] group-hover:text-white group-hover:scale-105 transition-all duration-300">
                            <svg class="w-4 h-4 transition-transform duration-300 group-hover:-rotate-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <h3 class="text-xs font-bold text-[#0F3D2E] group-hover:text-[#0A2A20] transition-colors truncate">Informasi Penayangan</h3>
                            <p class="text-[10.5px] text-[#647B71] truncate">Terkini & akurat</p>
                        </div>
                    </div>

                </div>

            </div>

            <!-- =========================================================
                 KOLOM KANAN: KARTU LOGIN
                 ========================================================= -->
            <div class="lg:col-span-5 flex justify-center lg:justify-end">
                <div class="w-full max-w-[450px] bg-white rounded-[22px] sm:rounded-[24px] p-5 sm:p-7 lg:p-7.5 border border-[#DCE7DF] shadow-[0_12px_40px_rgba(15,61,46,0.06)]">
                    
                    <!-- Header Kartu: Logo E-VITRON & Judul (Tanpa Box) -->
                    <div class="text-center mb-3.5 sm:mb-5">
                        <img src="{{ asset('images/sipetron-icon.png') }}?v=3" 
                             alt="Logo E-VITRON" 
                             class="hidden sm:block h-10 sm:h-12 w-auto mx-auto mb-2.5 object-contain select-none" />
                        <h2 class="text-lg lg:text-[21px] font-bold text-[#0F3D2E] leading-snug">
                            Masuk ke E-VITRON
                        </h2>
                        <p class="text-xs text-[#647B71] font-medium mt-0.5">
                            Gunakan akun kampus untuk melanjutkan.
                        </p>
                    </div>

                    {{-- Feedback Alert Error / Session --}}
                    @if(session('error') || $errors->any())
                        <div class="mb-3.5 p-3 bg-rose-50 border border-rose-200 rounded-xl flex items-start gap-2.5 text-xs text-rose-800 shadow-sm">
                            <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-medium leading-relaxed">{{ session('error') ?? $errors->first() }}</span>
                        </div>
                    @endif

                    @if(session('success'))
                        <div x-data="{ showSuccessAlert: true }"
                             x-init="setTimeout(() => showSuccessAlert = false, 5000)"
                             x-show="showSuccessAlert"
                             x-transition.opacity.duration.300ms
                             class="mb-3.5 p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-start justify-between gap-2.5 text-xs text-emerald-800 shadow-sm">
                            <div class="flex items-start gap-2.5 min-w-0">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="font-medium leading-relaxed">{{ session('success') }}</span>
                            </div>
                            <button type="button" @click="showSuccessAlert = false" class="text-emerald-600/70 hover:text-emerald-800 shrink-0 mt-0.5 cursor-pointer" aria-label="Tutup notifikasi">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @endif

                    <!-- Formulir Login Tunggal -->
                    <form action="{{ route('login.post') }}" method="POST" class="space-y-3.5">
                        @csrf

                        <!-- Field 1: Username -->
                        <div>
                            <label for="identifier" class="block text-xs font-bold text-[#0F3D2E] mb-1">
                                Username
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#647B71]">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <input 
                                    type="text" 
                                    id="identifier" 
                                    name="identifier" 
                                    x-model="identifier"
                                    required
                                    autocomplete="username"
                                    placeholder="Masukkan username"
                                    class="w-full h-[48px] pl-11 pr-4 bg-[#FBFDFB] hover:bg-white focus:bg-white border @if(session('error') || $errors->has('identifier')) border-rose-300 ring-2 ring-rose-100 @else border-[#DCE7DF] @endif rounded-[12px] text-slate-800 placeholder-slate-400 focus:outline-none focus:border-[#0F3D2E] focus:ring-2 focus:ring-[#0F3D2E]/10 transition-all text-xs sm:text-sm font-medium">
                            </div>
                        </div>

                        <!-- Field 2: Password -->
                        <div>
                            <label for="password" class="block text-xs font-bold text-[#0F3D2E] mb-1">
                                Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#647B71]">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <input 
                                    :type="showPassword ? 'text' : 'password'" 
                                    id="password" 
                                    name="password" 
                                    x-model="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="Masukkan password"
                                    class="w-full h-[48px] pl-11 pr-11 bg-[#FBFDFB] hover:bg-white focus:bg-white border border-[#DCE7DF] rounded-[12px] text-slate-800 placeholder-slate-400 focus:outline-none focus:border-[#0F3D2E] focus:ring-2 focus:ring-[#0F3D2E]/10 transition-all text-xs sm:text-sm font-medium">
                                
                                <!-- Tombol Tampil/Sembunyikan Password -->
                                <button 
                                    type="button" 
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#647B71] hover:text-[#0F3D2E] focus:outline-none transition-colors cursor-pointer"
                                    :title="showPassword ? 'Sembunyikan password' : 'Tampilkan password'">
                                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Keterangan Autentikasi Kampus Terintegrasi -->
                        <div class="flex items-center justify-between gap-1.5 text-xs text-[#647B71] px-1 py-0.5">
                            <span class="inline-flex items-center gap-2 font-medium min-w-0">
                                <span class="relative flex h-2 w-2 shrink-0">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-600"></span>
                                </span>
                                <span class="text-[#0F3D2E] font-semibold text-[11px] sm:text-[11.5px] leading-tight">Masuk dengan Akun Portal Akademik (NIM / NIP)</span>
                            </span>
                            <span class="text-[10px] font-bold text-[#D9A441] bg-[#D9A441]/10 px-2 py-0.5 rounded-full uppercase tracking-wider shrink-0">SSO</span>
                        </div>

                        <!-- Tombol Utama: Masuk ke E-VITRON -->
                        <button type="submit"
                            class="w-full h-[48px] rounded-[12px] bg-[#0F3D2E] hover:bg-[#0A2A20] active:scale-[0.99] text-white font-bold text-sm flex items-center justify-center gap-2 transition-all duration-150 shadow-[0_4px_14px_rgba(15,61,46,0.2)] select-none mt-1 cursor-pointer">
                            <span>Masuk ke E-VITRON</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </form>

                    @if(config('services.demo.quick_login'))
                        <!-- Akses Cepat Demo Pameran (Hanya muncul saat saklar demo Railway aktif) -->
                        <div class="mt-3.5 pt-3 border-t border-[#DCE7DF]/80" data-testid="demo-quick-login">
                            <div class="flex items-center justify-between mb-2 px-0.5">
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-[#0F3D2E]">
                                    <svg class="w-3.5 h-3.5 text-[#D9A441]" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Akses Cepat Demo</span>
                                </span>
                                <span class="text-[10px] font-semibold text-[#647B71]">1-Klik Isi Otomatis</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button"
                                    @click="fillDemo('applicant', @js(config('services.demo.applicant_identifier')), @js(config('services.demo.applicant_password')))"
                                    :class="activeDemo === 'applicant' ? 'bg-[#E8F3EC] border-[#0F3D2E] text-[#0F3D2E] ring-1 ring-[#0F3D2E]/15' : 'bg-[#FBFDFB] hover:bg-[#F5F8F5] border-[#DCE7DF] text-[#0F3D2E]'"
                                    class="group flex items-center justify-center gap-1.5 h-[36px] px-2.5 rounded-[10px] border text-xs font-bold transition-all cursor-pointer select-none">
                                    <svg class="w-3.5 h-3.5 text-[#0F3D2E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span class="truncate">Pemohon Demo</span>
                                </button>
                                <button type="button"
                                    @click="fillDemo('admin', @js(config('services.demo.admin_email')), @js(config('services.demo.admin_password')))"
                                    :class="activeDemo === 'admin' ? 'bg-[#E8F3EC] border-[#0F3D2E] text-[#0F3D2E] ring-1 ring-[#0F3D2E]/15' : 'bg-[#FBFDFB] hover:bg-[#F5F8F5] border-[#DCE7DF] text-[#0F3D2E]'"
                                    class="group flex items-center justify-center gap-1.5 h-[36px] px-2.5 rounded-[10px] border text-xs font-bold transition-all cursor-pointer select-none">
                                    <svg class="w-3.5 h-3.5 text-[#D9A441] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    <span class="truncate">Admin E-VITRON</span>
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Navigasi Registrasi Tendik & Mitra Eksternal (Clean & Lega Tanpa Divider Menumpuk) -->
                    <div class="mt-4 text-center">
                        <p class="text-xs text-[#647B71] leading-relaxed">
                            Belum punya akun portal? 
                            <a href="{{ route('register') }}" class="inline-block whitespace-nowrap font-bold text-[#0F3D2E] hover:text-[#D9A441] underline underline-offset-3 decoration-[#0F3D2E]/30 hover:decoration-[#D9A441] transition-all ml-1">
                                Daftar Akun Mitra &amp; Tendik&nbsp;&rarr;
                            </a>
                        </p>
                    </div>

                </div>
            </div>

        </div>

    </main>


    {{-- Footer & Modal Pusat Bantuan Layanan E-VITRON --}}
    @include('auth.partials.footer-and-help-modal')

</body>
</html>
