(function () {
    // Intercept fetch to static endpoints so background polling or Livewire calls never error on static Vercel hosting
    const originalFetch = window.fetch;
    window.fetch = function (input, init) {
        const url = typeof input === 'string' ? input : (input && input.url ? input.url : '');
        if (url.includes('/notifikasi/cek-terbaru')) {
            return Promise.resolve(new Response(JSON.stringify({
                unread_count: 2,
                latest_id: null,
                latest_notification: null
            }), {
                status: 200,
                headers: { 'Content-Type': 'application/json' }
            }));
        }
        if (url.includes('/notifikasi/') || url.includes('/livewire/update')) {
            return Promise.resolve(new Response(JSON.stringify({ success: true }), {
                status: 200,
                headers: { 'Content-Type': 'application/json' }
            }));
        }
        return originalFetch.apply(this, arguments);
    };

    document.addEventListener('DOMContentLoaded', function () {
        const path = window.location.pathname;
        const isGuestPage = ['/login', '/register', '/index.html', '/login.html', '/register.html'].includes(path)
            || (path === '/' && !!document.querySelector('form[action$="/login"]'));

        // Rewrite "/" links inside authenticated portal pages to "/beranda"
        document.querySelectorAll('a[href]').forEach(function (a) {
            const href = a.getAttribute('href');
            if (!href) return;

            if ((href === '/' || href === 'http://localhost' || href === 'http://videotron-uin-ssc.test') && !isGuestPage) {
                a.setAttribute('href', '/beranda');
            } else if (href.startsWith('/admin/submissions?')) {
                a.setAttribute('href', '/admin/submissions');
            } else if (href.includes('/cetak') || href.includes('/surat') || href.includes('/print') || href.includes('/export-pdf')) {
                a.addEventListener('click', function (ev) {
                    ev.preventDefault();
                    alert('Pratinjau Demo Statis: Pada server Laravel produksi, tautan ini langsung mengunduh berkas PDF resmi (DomPDF / Private Storage).');
                });
            }
        });

        // Handle form submissions (Login, Register, Logout, Pengajuan) on static hosting
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (!form || !form.tagName || form.tagName.toLowerCase() !== 'form') return;

            const action = (form.getAttribute('action') || '').toLowerCase();
            const method = (form.getAttribute('method') || 'get').toLowerCase();

            // 1. Login Form (Bridge SSO / Local Auth)
            if (action.endsWith('/login')) {
                e.preventDefault();
                const idInput = form.querySelector('input[name="identifier"]');
                const rawId = idInput && idInput.value ? idInput.value.trim() : '2208101001';
                sessionStorage.setItem('evitron_demo_user', rawId);

                if (rawId.toLowerCase().includes('admin')) {
                    window.location.href = '/admin';
                } else {
                    window.location.href = '/beranda';
                }
                return;
            }

            // 2. Register Form (Tendik / Mitra)
            if (action.endsWith('/register')) {
                e.preventDefault();
                alert('Pendaftaran berhasil dikirim! Akun Anda sedang menunggu persetujuan (approval) dari Admin PUSTIKOM.');
                window.location.href = '/login';
                return;
            }

            // 3. Logout Form (Portal & Filament Admin)
            if (action.includes('/logout')) {
                e.preventDefault();
                sessionStorage.removeItem('evitron_demo_user');
                window.location.href = '/login';
                return;
            }

            // 4. Pengajuan Wizard Store / Revision / Cancel
            if (action.includes('/pengajuan') && method === 'post') {
                e.preventDefault();
                alert('Simulasi Demo: Pengajuan penayangan videotron berhasil dikirim dan dicatat ke daftar pengajuan.');
                window.location.href = '/pengajuan';
            }
        }, true);
    });
})();
