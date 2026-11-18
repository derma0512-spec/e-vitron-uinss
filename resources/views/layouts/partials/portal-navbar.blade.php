    <!-- HEADER / NAVBAR -->
    <header class="fixed top-0 left-0 right-0 z-50 h-[70px] border-b border-[#E6ECE9] bg-white/95 backdrop-blur-md shadow-xs">
      <div class="mx-auto flex h-full max-w-[1400px] items-center justify-between px-4 sm:px-6 md:px-10 lg:px-14">

        <!-- KIRI: Tombol Hamburger Mobile (< md) -->
        <div class="flex items-center md:hidden">
          <button id="mobile-menu-btn" type="button" onclick="openMobileNav()"
            class="flex h-10 w-10 items-center justify-center text-[#0E4D3C] hover:bg-[#F0F6F3] active:bg-[#E3EFEA] rounded-xl transition-all duration-150 outline-none select-none cursor-pointer"
            aria-label="Buka Navigasi Menu" aria-expanded="false" aria-controls="mobile-nav-drawer">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
            </svg>
          </button>
        </div>

        <!-- Logo E-VITRON: Center di Mobile, Kiri di Desktop -->
        <div class="flex-1 md:flex-initial flex justify-center md:justify-start">
          <a href="{{ route('home') }}" class="flex items-center gap-2.5 sm:gap-3 group select-none">
            <img src="{{ asset('images/sipetron-icon.png') }}" alt="Logo E-VITRON" class="h-9 w-9 sm:h-10 sm:w-10 object-contain transition-transform duration-200 group-hover:scale-105" />
            <div class="flex flex-col justify-center leading-none">
              <span class="font-montserrat text-[19px] sm:text-[21px] font-extrabold tracking-tight text-[#0E4D3C] leading-none">E-VITRON</span>
              <!-- Teks sub di Mobile lebih ringkas, Desktop lengkap -->
              <span class="text-[9px] sm:text-[9.5px] font-semibold text-[#B88219] tracking-tight mt-1 leading-none md:hidden">Pengajuan Videotron</span>
              <span class="evitron-nav-subtitle hidden md:inline-block text-[9.5px] font-semibold text-[#B88219] tracking-tight mt-1 leading-none">Layanan Pengajuan Penayangan Videotron</span>
            </div>
          </a>
        </div>

        <!-- Navigasi Menu Tengah (Desktop Only) -->
        <nav aria-label="Navigasi utama" class="hidden md:flex h-full items-center">
          <ul class="evitron-nav-list flex h-full items-center gap-8">
            <li class="h-full">
              <a href="{{ route('home') }}" class="relative flex h-full items-center group/nav text-sm outline-none select-none">
                <span class="nav-link {{ request()->routeIs('home') ? 'font-black text-[#0E4D3C]' : 'font-semibold text-[#1E5546]' }}">Beranda</span>
                @if(request()->routeIs('home'))
                  <span class="nav-bar-active"></span>
                @else
                  <span class="nav-bar-hover"></span>
                @endif
              </a>
            </li>
            <li class="h-full">
              <a href="{{ route('pengajuan.create') }}" class="relative flex h-full items-center group/nav text-sm outline-none select-none">
                <span class="nav-link {{ request()->routeIs('pengajuan.create') ? 'font-black text-[#0E4D3C]' : 'font-semibold text-[#1E5546]' }}">Ajukan Penayangan</span>
                @if(request()->routeIs('pengajuan.create'))
                  <span class="nav-bar-active"></span>
                @else
                  <span class="nav-bar-hover"></span>
                @endif
              </a>
            </li>
            <li class="h-full">
              <a href="{{ route('pengajuan.index') }}" class="relative flex h-full items-center group/nav text-sm outline-none select-none">
                <span class="nav-link {{ request()->routeIs('pengajuan.index', 'pengajuan.show') ? 'font-black text-[#0E4D3C]' : 'font-semibold text-[#1E5546]' }}">Pengajuan Saya</span>
                @if(request()->routeIs('pengajuan.index', 'pengajuan.show'))
                  <span class="nav-bar-active"></span>
                @else
                  <span class="nav-bar-hover"></span>
                @endif
              </a>
            </li>
            <li class="h-full">
              <a href="{{ route('panduan') }}" class="relative flex h-full items-center group/nav text-sm outline-none select-none">
                <span class="nav-link {{ request()->routeIs('panduan') ? 'font-black text-[#0E4D3C]' : 'font-semibold text-[#1E5546]' }}">Informasi</span>
                @if(request()->routeIs('panduan'))
                  <span class="nav-bar-active"></span>
                @else
                  <span class="nav-bar-hover"></span>
                @endif
              </a>
            </li>
          </ul>
        </nav>

        <!-- KANAN: Notifikasi (Mobile & Desktop) & Profil (Desktop Only) -->
        <div class="flex items-center gap-2 sm:gap-3 shrink-0">

          @auth
            {{-- ===== NOTIFIKASI TRIGGER ===== --}}
            <div class="relative group/notif" id="notif-wrapper">
              <button id="notif-btn" type="button" onclick="openNotifDrawer()"
                class="relative flex h-10 w-10 items-center justify-center text-[#0E4D3C] hover:bg-[#F0F6F3] rounded-full transition-all duration-150 outline-none focus:outline-none focus:ring-0 focus-visible:outline-none focus-visible:ring-0 active:scale-95 cursor-pointer"
                aria-label="Buka Notifikasi Masuk" aria-expanded="false" aria-controls="notif-drawer">
                <svg class="h-5 w-5 bell-icon transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span id="notif-badge-container">
                  @if($unreadNotifsCount > 0)
                    <span id="notif-badge-count" class="absolute top-1.5 right-1.5 flex h-4 min-w-[16px] px-1 items-center justify-center rounded-full bg-[#D9A93B] text-[9px] font-extrabold text-white border-2 border-white shadow-xs leading-none transition-transform group-hover/notif:scale-110">
                      {{ $unreadNotifsCount > 99 ? '99+' : $unreadNotifsCount }}
                    </span>
                  @endif
                </span>
              </button>

              <!-- Tooltip Keterangan Hover Notifikasi Tepat di Tengah Lonceng -->
              <div id="notif-tooltip" role="tooltip" class="portal-tooltip">
                <div class="relative whitespace-nowrap rounded-md bg-[#0E4D3C] px-2.5 py-1 text-[11px] font-semibold text-white shadow-lg border border-[#16604B]">
                  {{ $unreadNotifsCount > 0 ? $unreadNotifsCount . ' Notifikasi Baru' : 'Tidak ada notifikasi baru' }}
                  <!-- Panah Segitiga Tooltip ke Atas -->
                  <div class="absolute -top-1 left-1/2 -translate-x-1/2 border-4 border-transparent border-b-[#0E4D3C]"></div>
                </div>
              </div>
            </div>

            <!-- Profil Dropdown Khusus Layar Tablet/Desktop (md:block) -->
            <div class="hidden md:flex items-center gap-3">
              <div class="h-7 w-px bg-[#E3E9E6]"></div>

              {{-- ===== PROFIL DROPDOWN DESKTOP ===== --}}
              <div class="relative group/profile" id="profile-wrapper">
                <button id="profile-btn" type="button"
                  class="flex h-[44px] items-center gap-[10px] rounded-[12px] border border-transparent bg-transparent px-2.5 py-1.5 hover:bg-[#F1F6F3] active:scale-[0.98] transition-all duration-150 outline-none focus:outline-none focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#0E4D3C] focus-visible:outline-offset-2 select-none"
                  aria-label="Menu profil pengguna" aria-expanded="false" aria-haspopup="true" aria-controls="profile-panel">
                  <div class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#0E4D3C] text-[11px] font-bold text-white shadow-xs select-none transition-transform duration-200 group-hover/profile:scale-105 overflow-hidden">
                    @if(!empty($user?->avatar_url))
                      <img src="{{ $user->avatar_url }}" alt="{{ $userName }}" class="absolute inset-0 h-full w-full object-cover rounded-full" onerror="this.remove()">
                    @endif
                    <span>{{ $initials }}</span>
                  </div>
                  <span class="inline-block text-[13.5px] font-bold text-[#123F33] leading-none">{{ $userName }}</span>
                  <svg class="h-3.5 w-3.5 text-[#5B7A70] transition-transform duration-150 stroke-[1.8]" id="profile-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                  </svg>
                </button>

                <!-- Profil Dropdown Panel -->
                <div id="profile-panel" role="menu" aria-label="Menu akun"
                  class="dropdown-panel absolute right-0 top-[calc(100%+6px)] w-[196px] rounded-[16px] border border-[#E2EBE5] bg-white p-2.5 z-50"
                  style="box-shadow: 0 10px 28px rgba(15, 61, 46, 0.10), 0 2px 6px rgba(0, 0, 0, 0.03);">
                  
                  <!-- Kartu Profil Ringkas Berlatar Cool Slate -->
                  <div class="flex flex-col items-center text-center p-3 rounded-[12px] bg-[#EEF2F6] border border-[#DEE5ED]">
                    <div class="mb-2">
                      <div class="relative flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-[#0E4D3C] to-[#18604C] text-[14px] font-black text-white shadow-xs ring-2 ring-white select-none mx-auto overflow-hidden">
                        @if(!empty($user?->avatar_url))
                          <img src="{{ $user->avatar_url }}" alt="{{ $userName }}" class="absolute inset-0 h-full w-full object-cover rounded-full" onerror="this.remove()">
                        @endif
                        <span>{{ $initials }}</span>
                      </div>
                    </div>

                    <!-- Nama Lengkap -->
                    <p class="text-[13px] font-bold text-slate-800 leading-tight truncate w-full px-0.5" title="{{ $userName }}">
                      {{ $userName }}
                    </p>

                    <!-- NIM -->
                    @if(!empty($userIdentifier))
                      <p class="text-[11px] font-mono text-slate-500 leading-none mt-1">
                        {{ $userIdentifier }}
                      </p>
                    @endif

                    <!-- Jurusan Badge Mini -->
                    @if(!empty($userProdi))
                      <div class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white text-slate-700 text-[10.5px] font-semibold border border-slate-200/90 shadow-2xs">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#1E755D]"></span>
                        <span class="truncate max-w-[125px]">{{ $userProdi }}</span>
                      </div>
                    @endif
                  </div>

                  <!-- Tombol Keluar Desktop -->
                  <form method="POST" action="{{ route('logout') }}" class="m-0 mt-2">
                    @csrf
                    <button type="submit" role="menuitem"
                      class="flex h-10 w-full items-center justify-start gap-2.5 px-3 text-[13.5px] font-semibold text-[#DC2626] bg-transparent hover:bg-[#FEE2E2]/70 rounded-[10px] transition-colors outline-none focus:outline-none focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#DC2626] select-none">
                      <svg class="w-4 h-4 shrink-0 text-[#DC2626]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                      </svg>
                      <span>Keluar</span>
                    </button>
                  </form>
                </div>
              </div>
            </div>
          @else
            <!-- Pengguna Tamu (Belum Login) -->
            <a href="{{ route('login') }}" 
               class="inline-flex items-center gap-2 px-4 sm:px-5 py-2 rounded-full bg-[#0E4D3C] hover:bg-[#145C49] text-white text-xs font-bold transition-all shadow-xs">
              <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
              </svg>
              Masuk
            </a>
          @endauth

        </div>
      </div>
    </header>

    <!-- =========================================================
         SLIDE-OVER MOBILE NAVIGATION DRAWER (HYBRID ENTERPRISE)
         ========================================================= -->
    <!-- Backdrop Overlay Mobile Nav -->
    <div id="mobile-nav-backdrop" 
         class="sipetron-drawer-backdrop transition-opacity duration-300 opacity-0 pointer-events-none md:hidden" 
         onclick="closeMobileNav()"></div>

    <!-- Drawer Panel Samping Kiri Mobile Nav (Slide in from LEFT - Responsive Dynamic Viewport) -->
    <aside id="mobile-nav-drawer" 
           class="fixed inset-y-0 left-0 w-[310px] max-w-[85vw] bg-white shadow-2xl flex flex-col transform -translate-x-full transition-transform duration-300 ease-in-out md:hidden"
           style="z-index: 99992 !important; height: 100dvh;"
           role="dialog" aria-modal="true" aria-label="Menu Navigasi Mobile">
      
      <!-- 1. Header Drawer Mobile: Full Putih Bersih, Langsung Profil & Tombol [✕] Tanpa Logo -->
      <div class="px-5 pt-5 pb-4 bg-white shrink-0 select-none">
        @auth
          <!-- Profil Pengguna (Sesuai Mockup Claude) -->
          <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-3.5 min-w-0 flex-1">
              <!-- Avatar Inisial Bulat Elegan / Foto Profil -->
              <div class="relative shrink-0">
                <div class="relative flex h-12 w-12 items-center justify-center rounded-full bg-[#0E4D3C] text-[15px] font-black text-white ring-2 ring-[#D9A93B]/70 shadow-xs overflow-hidden">
                  @if(!empty($user?->avatar_url))
                    <img src="{{ $user->avatar_url }}" alt="{{ $userName }}" class="absolute inset-0 h-full w-full object-cover rounded-full" onerror="this.remove()">
                  @endif
                  <span>{{ $initials }}</span>
                </div>
              </div>

              <!-- Nama & Identitas Pengguna -->
              <div class="min-w-0 flex-1">
                <h3 class="text-[15px] font-extrabold text-slate-900 tracking-tight leading-tight truncate" title="{{ $userName }}">
                  {{ $userName }}
                </h3>
                @if(!empty($userIdentifier))
                  <p class="text-[11.5px] font-mono text-slate-500 leading-none mt-1">
                    NIM {{ $userIdentifier }}
                  </p>
                @endif
                @if(!empty($userProdi))
                  <div class="mt-1.5 inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-[#ECFDF5] border border-[#A7F3D0] text-[#065F46] text-[10.5px] font-bold">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#10B981]"></span>
                    <span class="truncate max-w-[130px]">{{ $userProdi }}</span>
                  </div>
                @endif
              </div>
            </div>

            <!-- Tombol Tutup [X] Rapi di Kanan Atas -->
            <button type="button" 
                    onclick="closeMobileNav()"
                    class="shrink-0 flex h-8 w-8 items-center justify-center rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer"
                    aria-label="Tutup Menu Navigasi">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        @else
          <!-- Header Khusus Tamu (Belum Login) -->
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
              <span class="flex h-10 w-10 items-center justify-center rounded-full bg-[#0E4D3C] text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                </svg>
              </span>
              <div>
                <p class="text-sm font-bold text-slate-900 leading-tight">Pengunjung Tamu</p>
                <p class="text-[11px] text-slate-500">Silakan login untuk mengajukan</p>
              </div>
            </div>
            <button type="button" 
                    onclick="closeMobileNav()"
                    class="shrink-0 flex h-8 w-8 items-center justify-center rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer"
                    aria-label="Tutup Menu Navigasi">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        @endauth
      </div>

      <!-- Garis Pemisah (Divider) Elegan di Bawah Profil Sebelum Menu -->
      <div class="px-5">
        <div class="border-t border-slate-200/80"></div>
      </div>

      <!-- 2. Body Drawer: Scrollable Flex Container yang Otomatis Proporsional di Semua Ukuran Layar HP -->
      <div class="flex-1 overflow-y-auto px-4 py-3 flex flex-col justify-between bg-white" style="overscroll-behavior: contain;">
        
        <!-- Daftar Menu -->
        <nav aria-label="Menu navigasi mobile" class="space-y-1.5 shrink-0">
          
          <!-- 1. Beranda -->
          <a href="{{ route('home') }}" onclick="closeMobileNav()"
             class="group flex items-center justify-between px-4 py-3 rounded-xl text-[14px] font-bold transition-all duration-150 select-none {{ request()->routeIs('home') ? 'bg-[#0E4D3C] text-white shadow-sm' : 'text-slate-700 hover:bg-[#F3F7F5] hover:text-[#0E4D3C]' }}">
            <div class="flex items-center gap-3.5 min-w-0">
              <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('home') ? 'text-white' : 'text-[#D9A93B] group-hover:text-[#0E4D3C]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
              </svg>
              <span>Beranda</span>
            </div>
            @if(request()->routeIs('home'))
              <span class="h-2 w-2 rounded-full bg-[#D9A93B] shrink-0"></span>
            @endif
          </a>

          <!-- 2. Ajukan Penayangan -->
          <a href="{{ route('pengajuan.create') }}" onclick="closeMobileNav()"
             class="group flex items-center justify-between px-4 py-3 rounded-xl text-[14px] font-bold transition-all duration-150 select-none {{ request()->routeIs('pengajuan.create') ? 'bg-[#0E4D3C] text-white shadow-sm' : 'text-slate-700 hover:bg-[#F3F7F5] hover:text-[#0E4D3C]' }}">
            <div class="flex items-center gap-3.5 min-w-0">
              <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('pengajuan.create') ? 'text-white' : 'text-[#D9A93B] group-hover:text-[#0E4D3C]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span>Ajukan Penayangan</span>
            </div>
            @if(request()->routeIs('pengajuan.create'))
              <span class="h-2 w-2 rounded-full bg-[#D9A93B] shrink-0"></span>
            @endif
          </a>

          <!-- 3. Pengajuan Saya -->
          <a href="{{ route('pengajuan.index') }}" onclick="closeMobileNav()"
             class="group flex items-center justify-between px-4 py-3 rounded-xl text-[14px] font-bold transition-all duration-150 select-none {{ request()->routeIs('pengajuan.index', 'pengajuan.show') ? 'bg-[#0E4D3C] text-white shadow-sm' : 'text-slate-700 hover:bg-[#F3F7F5] hover:text-[#0E4D3C]' }}">
            <div class="flex items-center gap-3.5 min-w-0">
              <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('pengajuan.index', 'pengajuan.show') ? 'text-white' : 'text-[#D9A93B] group-hover:text-[#0E4D3C]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
              </svg>
              <span>Pengajuan Saya</span>
            </div>
            @if(request()->routeIs('pengajuan.index', 'pengajuan.show'))
              <span class="h-2 w-2 rounded-full bg-[#D9A93B] shrink-0"></span>
            @endif
          </a>

          <!-- 4. Informasi & Panduan -->
          <a href="{{ route('panduan') }}" onclick="closeMobileNav()"
             class="group flex items-center justify-between px-4 py-3 rounded-xl text-[14px] font-bold transition-all duration-150 select-none {{ request()->routeIs('panduan') ? 'bg-[#0E4D3C] text-white shadow-sm' : 'text-slate-700 hover:bg-[#F3F7F5] hover:text-[#0E4D3C]' }}">
            <div class="flex items-center gap-3.5 min-w-0">
              <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('panduan') ? 'text-white' : 'text-[#D9A93B] group-hover:text-[#0E4D3C]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
              </svg>
              <span>Informasi & Panduan</span>
            </div>
            @if(request()->routeIs('panduan'))
              <span class="h-2 w-2 rounded-full bg-[#D9A93B] shrink-0"></span>
            @endif
          </a>

        </nav>

        <!-- Area Bawah Logout: Terkunci di Bawah Tapi Fleksibel Menyesuaikan Tinggi Layar HP -->
        <div class="pt-6 pb-4 mt-auto shrink-0 select-none">
          @auth
            <form method="POST" action="{{ route('logout') }}" class="m-0">
              @csrf
              <button type="submit"
                      class="flex h-11 w-full items-center justify-center gap-2.5 rounded-xl text-sm font-bold text-red-600 bg-[#FEF2F2] border border-red-200 hover:bg-red-100 hover:border-red-300 active:scale-[0.99] shadow-2xs transition-all select-none cursor-pointer">
                <svg class="w-4 h-4 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                </svg>
                <span>Keluar dari Akun</span>
              </button>
            </form>
          @else
            <a href="{{ route('login') }}" 
               class="flex h-11 w-full items-center justify-center gap-2 rounded-xl text-sm font-bold text-white bg-[#0E4D3C] hover:bg-[#145C49] active:scale-[0.99] transition-all shadow-xs select-none">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
              </svg>
              <span>Masuk ke E-VITRON</span>
            </a>
          @endauth

          <p class="text-[10px] text-center text-slate-400 font-medium mt-3">
            E-VITRON • UIN Siber Syekh Nurjati Cirebon
          </p>
        </div>

      </div>

    </aside>

