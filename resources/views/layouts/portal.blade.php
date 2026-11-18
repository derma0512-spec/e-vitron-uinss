<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda | Pengajuan Videotron UIN Siber')</title>
    <!-- Favicon E-VITRON -->
    <link rel="icon" type="image/png" href="{{ asset('images/sipetron-icon.png') }}?v=2">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/sipetron-icon.png') }}?v=2">
    <link rel="apple-touch-icon" href="{{ asset('images/sipetron-icon.png') }}?v=2">
    <!-- Preconnect CDN & Fonts (Zero DNS/TLS Delay) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Tailwind CSS (Local Standalone Engine) -->
    <script src="{{ asset('js/tailwind.js') }}"></script>
    <!-- Alpine.js (Local Engine) -->
    <script defer src="{{ asset('js/alpine.min.js') }}"></script>
    <!-- Cegah BFCache: Jika pengguna sudah logout lalu menekan tombol Back di browser, otomatis verifikasi ulang ke server dan redirect ke login -->
    <script>
        window.addEventListener('pageshow', function (event) {
            if (event.persisted || (window.performance && window.performance.getEntriesByType("navigation")[0]?.type === "back_forward")) {
                window.location.reload();
            }
        });
    </script>
    <!-- Fonts: Plus Jakarta Sans & Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @include('layouts.partials.portal-styles-base')
    @include('layouts.partials.portal-styles-notifications')
</head>
<body class="min-h-screen flex flex-col text-slate-800 bg-[#F4F8F6]">

    @php
        $user           = Auth::user();
        $rawName        = $user?->name ?? 'Pengguna';
        // Bersihkan teks peran dalam tanda kurung jika ada (misal: "Ahmad Fadhil (Mahasiswa)" -> "Ahmad Fadhil")
        $userName       = trim(preg_replace('/\(.*?\)/', '', $rawName));
        $userRole       = $user?->role ?? '';
        $userIdentifier = $user?->identifier ?? '';
        $rawProdi       = $user?->unit_or_prodi ?? '';
        // Bersihkan kata "Teknik " jika ada di awal (misal: "Teknik Informatika" -> "Informatika")
        $cleanProdi     = trim(preg_replace('/^Teknik\s+/i', '', $rawProdi));
        $userProdi      = $cleanProdi ? ucwords(strtolower($cleanProdi)) : '';

        // Avatar initials — ambil 2 huruf inisial bersih (kata pertama & kata kedua)
        $cleanWords = array_values(array_filter(explode(' ', $userName)));
        $initials   = 'U';
        if (count($cleanWords) >= 2) {
            $initials = strtoupper(mb_substr($cleanWords[0], 0, 1) . mb_substr($cleanWords[1], 0, 1));
        } elseif (count($cleanWords) === 1 && !empty($cleanWords[0])) {
            $initials = strtoupper(mb_substr($cleanWords[0], 0, min(2, mb_strlen($cleanWords[0]))));
        }

        // Notifikasi pengajuan user yang sedang login (drawer lengkap)
        $userNotifications = $user
            ? $user->notifications()->where('data->event', '!=', 'submitted')->latest()->limit(50)->get()
            : collect();
        $recentNotifications = $userNotifications;

        $unreadNotifsCount = $user
            ? $user->unreadNotifications()->where('data->event', '!=', 'submitted')->count()
            : 0;
    @endphp

    <!-- HEADER / NAVBAR & MOBILE DRAWER -->
    @include('layouts.partials.portal-navbar')

    <!-- SLIDE-OVER NOTIFICATION DRAWER -->
    @include('layouts.partials.portal-notif-drawer')

    <!-- CONTENT BODY -->
    <main class="flex-1 pt-[70px]">
        @yield('content')
    </main>

    <!-- FOOTER -->
    @include('layouts.footer')

    <!-- FLOATING TOAST CONTAINER -->
    @include('layouts.partials.portal-toast-container')

    <!-- GLOBAL PORTAL SCRIPTS -->
    @include('layouts.partials.portal-scripts-navigation')
    @include('layouts.partials.portal-scripts-toast-polling')
</body>
</html>
