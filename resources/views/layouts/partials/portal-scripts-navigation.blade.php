    <!-- Professional Dropdown & Slide-over Notification Drawer JS -->
    <script>
      (function () {
        'use strict';

        const dropdowns = [
          {
            btnId: 'profile-btn',
            panelId: 'profile-panel',
            wrapperId: 'profile-wrapper',
            chevronId: 'profile-chevron'
          }
        ];

        let activeDropdown = null;

        function openDropdown(item) {
          const btn     = document.getElementById(item.btnId);
          const panel   = document.getElementById(item.panelId);
          const chevron = item.chevronId ? document.getElementById(item.chevronId) : null;
          if (!btn || !panel) return;

          // Tutup dropdown lain jika sedang terbuka (Mutual Exclusion)
          if (activeDropdown && activeDropdown.btnId !== item.btnId) {
            closeDropdown(activeDropdown);
          }

          panel.classList.add('open');
          btn.setAttribute('aria-expanded', 'true');
          if (chevron) chevron.style.transform = 'rotate(180deg)';
          if (item.btnId === 'profile-btn') {
            btn.classList.add('!bg-[#F1F6F3]');
          }
          activeDropdown = item;

          // Responsif: Pastikan tidak terpotong tepi layar di HP/tablet
          const rect = panel.getBoundingClientRect();
          if (rect.right > window.innerWidth - 8) {
            panel.style.right = '8px';
            panel.style.left = 'auto';
          }
          if (rect.left < 8) {
            panel.style.left = '8px';
            panel.style.right = 'auto';
          }
        }

        function closeDropdown(item) {
          if (!item) return;
          const btn     = document.getElementById(item.btnId);
          const panel   = document.getElementById(item.panelId);
          const chevron = item.chevronId ? document.getElementById(item.chevronId) : null;
          if (!btn || !panel) return;

          panel.classList.remove('open');
          panel.style.left = '';
          panel.style.right = '';
          btn.setAttribute('aria-expanded', 'false');
          if (chevron) chevron.style.transform = '';
          if (item.btnId === 'profile-btn') {
            btn.classList.remove('!bg-[#F1F6F3]');
          }
          if (activeDropdown && activeDropdown.btnId === item.btnId) {
            activeDropdown = null;
          }
        }

        dropdowns.forEach(function (item) {
          const btn   = document.getElementById(item.btnId);
          const panel = document.getElementById(item.panelId);
          if (!btn || !panel) return;

          btn.addEventListener('click', function (e) {
            e.stopPropagation();
            if (panel.classList.contains('open')) {
              closeDropdown(item);
            } else {
              openDropdown(item);
            }
          });

          // Cegah klik di dalam panel agar tidak menutup diri sendiri
          panel.addEventListener('click', function (e) {
            e.stopPropagation();
          });
        });

        // ===== SLIDE-OVER MOBILE NAVIGATION DRAWER (SLIDE IN FROM LEFT) =====
        window.openMobileNav = function () {
          if (typeof closeNotifDrawer === 'function') closeNotifDrawer();
          if (activeDropdown) closeDropdown(activeDropdown);

          const drawer = document.getElementById('mobile-nav-drawer');
          const backdrop = document.getElementById('mobile-nav-backdrop');
          const menuBtn = document.getElementById('mobile-menu-btn');
          if (!drawer || !backdrop) return;

          if (drawer.classList.contains('translate-x-0')) {
            closeMobileNav();
            return;
          }

          document.documentElement.classList.add('sipetron-drawer-locked', 'mobile-nav-open');
          backdrop.classList.remove('opacity-0', 'pointer-events-none');
          backdrop.classList.add('opacity-100', 'pointer-events-auto');
          backdrop.style.opacity = '1';
          backdrop.style.pointerEvents = 'auto';
          drawer.classList.remove('-translate-x-full');
          drawer.classList.add('translate-x-0');
          if (menuBtn) menuBtn.setAttribute('aria-expanded', 'true');

          // Tutup dan hilangkan semua toast aktif saat pengguna membuka sidebar
          document.querySelectorAll('#sipetron-toast-container .sipetron-toast-floating-item').forEach(function (item) {
            if (typeof window.dismissToast === 'function') {
              window.dismissToast(item);
            }
          });
          if (window._pendingToastQueue) {
            window._pendingToastQueue = [];
          }
        };

        window.closeMobileNav = function () {
          const drawer = document.getElementById('mobile-nav-drawer');
          const backdrop = document.getElementById('mobile-nav-backdrop');
          const menuBtn = document.getElementById('mobile-menu-btn');
          if (!drawer || !backdrop) return;

          drawer.classList.remove('translate-x-0');
          drawer.classList.add('-translate-x-full');
          backdrop.classList.remove('opacity-100', 'pointer-events-auto');
          backdrop.classList.add('opacity-0', 'pointer-events-none');
          backdrop.style.opacity = '0';
          backdrop.style.pointerEvents = 'none';
          document.documentElement.classList.remove('sipetron-drawer-locked', 'mobile-nav-open');
          if (menuBtn) {
            menuBtn.setAttribute('aria-expanded', 'false');
            menuBtn.blur();
          }

          // Sidebar ditutup: tidak memunculkan toast lama kembali agar layar tetap bersih
        };

        window.toggleMobileNav = function () {
          const drawer = document.getElementById('mobile-nav-drawer');
          if (drawer && drawer.classList.contains('translate-x-0')) {
            closeMobileNav();
          } else {
            openMobileNav();
          }
        };

        // ===== SLIDE-OVER NOTIFIKASI DRAWER (ALA ADMIN) =====
        window.openNotifDrawer = function () {
          // Tutup dropdown profil jika sedang terbuka
          if (activeDropdown) {
            closeDropdown(activeDropdown);
          }
          const drawer = document.getElementById('notif-drawer');
          const backdrop = document.getElementById('notif-drawer-backdrop');
          const notifBtn = document.getElementById('notif-btn');
          if (!drawer || !backdrop) return;

          // Jika sedang terbuka, toggle menjadi tutup
          if (drawer.classList.contains('translate-x-0')) {
            closeNotifDrawer();
            return;
          }

          document.documentElement.classList.add('sipetron-drawer-locked', 'notif-drawer-open');
          backdrop.classList.remove('opacity-0', 'pointer-events-none');
          backdrop.classList.add('opacity-100', 'pointer-events-auto');
          backdrop.style.opacity = '1';
          backdrop.style.pointerEvents = 'auto';
          drawer.classList.remove('translate-x-full');
          drawer.classList.add('translate-x-0');
          if (notifBtn) notifBtn.setAttribute('aria-expanded', 'true');

          // Segarkan isi daftar notifikasi terbaru segera saat laci dibuka
          if (typeof window.pollPortalNotifications === 'function') {
            window.pollPortalNotifications(true);
          }
        };

        window.closeNotifDrawer = function () {
          const drawer = document.getElementById('notif-drawer');
          const backdrop = document.getElementById('notif-drawer-backdrop');
          const notifBtn = document.getElementById('notif-btn');
          if (!drawer || !backdrop) return;

          drawer.classList.remove('translate-x-0');
          drawer.classList.add('translate-x-full');
          backdrop.classList.remove('opacity-100', 'pointer-events-auto');
          backdrop.classList.add('opacity-0', 'pointer-events-none');
          backdrop.style.opacity = '0';
          backdrop.style.pointerEvents = 'none';
          document.documentElement.classList.remove('sipetron-drawer-locked', 'notif-drawer-open');
          if (notifBtn) {
            notifBtn.setAttribute('aria-expanded', 'false');
            notifBtn.blur();
          }
        };

        window.toggleNotifDrawer = function () {
          const drawer = document.getElementById('notif-drawer');
          if (drawer && drawer.classList.contains('translate-x-0')) {
            closeNotifDrawer();
          } else {
            openNotifDrawer();
          }
        };

        window.filterDrawerNotifs = function (filter) {
          const allTab = document.getElementById('tab-drawer-all');
          const unreadTab = document.getElementById('tab-drawer-unread');
          const items = document.querySelectorAll('.drawer-notif-item');
          if (!allTab || !unreadTab) return;

          if (filter === 'unread') {
            allTab.className = 'px-3 py-1 rounded-full text-xs font-bold transition-all border border-[#0E4D3C]/20 bg-white text-[#0E4D3C] hover:bg-[#0E4D3C]/10 cursor-pointer';
            unreadTab.className = 'px-3 py-1 rounded-full text-xs font-bold transition-all bg-[#0E4D3C] text-white shadow-xs cursor-pointer';
            items.forEach(function (el) {
              el.style.display = (el.getAttribute('data-unread') === '1' ? '' : 'none');
            });
          } else {
            allTab.className = 'px-3 py-1 rounded-full text-xs font-bold transition-all bg-[#0E4D3C] text-white shadow-xs cursor-pointer';
            unreadTab.className = 'px-3 py-1 rounded-full text-xs font-bold transition-all border border-[#0E4D3C]/20 bg-white text-[#0E4D3C] hover:bg-[#0E4D3C]/10 cursor-pointer';
            items.forEach(function (el) {
              el.style.display = '';
            });
          }
        };

        // Helper Sinkronisasi Badge Notifikasi Real-Time (Navbar Lonceng & Drawer Header)
        window.updateAllBadgeCounts = function (count) {
          count = Math.max(0, parseInt(count || '0', 10));

          // 1. Badge di header drawer
          const drawerBadge = document.querySelector('.sipetron-notif-badge');
          if (drawerBadge) {
            if (count > 0) {
              drawerBadge.textContent = count;
              drawerBadge.style.setProperty('display', 'inline-flex', '');
              drawerBadge.classList.remove('is-hidden');
            } else {
              drawerBadge.style.setProperty('display', 'none', 'important');
              drawerBadge.classList.add('is-hidden');
            }
          }

          // 2. Tombol "Tandai Semua Dibaca" di header drawer
          const markReadForm = document.getElementById('form-mark-all-read');
          if (markReadForm) {
            if (count === 0) {
              markReadForm.style.setProperty('display', 'none', 'important');
              markReadForm.classList.add('is-hidden');
            } else {
              markReadForm.style.setProperty('display', 'inline', '');
              markReadForm.classList.remove('is-hidden');
            }
          }

          // 3. Badge lonceng di navbar (Instan 0ms)
          const badgeContainer = document.getElementById('notif-badge-container');
          if (badgeContainer) {
            if (count > 0) {
              const badgeText = count > 99 ? '99+' : count;
              badgeContainer.innerHTML = `<span id="notif-badge-count" class="absolute top-1.5 right-1.5 flex h-4 min-w-[16px] px-1 items-center justify-center rounded-full bg-[#D9A93B] text-[9px] font-extrabold text-white border-2 border-white shadow-xs leading-none transition-transform group-hover/notif:scale-110">${badgeText}</span>`;
            } else {
              badgeContainer.innerHTML = '';
            }
          }

          // 4. Tooltip lonceng di navbar
          const tooltipEl = document.querySelector('#notif-tooltip div');
          if (tooltipEl) {
            tooltipEl.innerHTML = count > 0
              ? `${count} Notifikasi Baru<div class="absolute -top-1 left-1/2 -translate-x-1/2 border-4 border-transparent border-b-[#0E4D3C]"></div>`
              : `Tidak ada notifikasi baru<div class="absolute -top-1 left-1/2 -translate-x-1/2 border-4 border-transparent border-b-[#0E4D3C]"></div>`;
          }
        };

        // Hapus satu notifikasi secara instan (AJAX, tanpa reload, drawer tetap terbuka)
        window.deleteSingleNotif = function (e, notifId, url) {
          if (e) {
            e.preventDefault();
            e.stopPropagation();
          }
          const itemEl = document.getElementById('notif-item-' + notifId);
          if (!itemEl) return;

          const isUnread = itemEl.getAttribute('data-unread') === 'true';

          // 1. Kurangi counter badge unread seketika (0ms Optimistic UI)
          if (isUnread) {
            const currentBadge = parseInt(document.querySelector('.sipetron-notif-badge')?.textContent || document.querySelector('#notif-badge-count')?.textContent || '0', 10);
            window.updateAllBadgeCounts(Math.max(0, currentBadge - 1));
          }

          // 2. Animasi menyusut & menghilang seketika
          itemEl.style.transition = 'opacity 180ms ease, transform 180ms ease, max-height 200ms ease, margin 200ms ease, padding 200ms ease';
          itemEl.style.overflow = 'hidden';
          itemEl.style.maxHeight = itemEl.offsetHeight + 'px';
          itemEl.offsetHeight; // force reflow
          itemEl.style.opacity = '0';
          itemEl.style.transform = 'translateX(24px)';
          itemEl.style.maxHeight = '0px';
          itemEl.style.margin = '0px';
          itemEl.style.paddingTop = '0px';
          itemEl.style.paddingBottom = '0px';

          // 3. Kirim request DELETE di latar belakang
          const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            || itemEl.querySelector('input[name="_token"]')?.value;

          fetch(url, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            }
          }).then(function (res) {
            return res.json();
          }).then(function (data) {
            if (data && typeof data.unread_count === 'number') {
              window.updateAllBadgeCounts(data.unread_count);
            }
          }).catch(function (err) { console.warn('[E-VITRON] Notification request failed:', err); });

          // 4. Setelah animasi selesai, bersihkan elemen dari DOM
          setTimeout(function () {
            if (itemEl && itemEl.parentNode) {
              itemEl.parentNode.removeChild(itemEl);
            }

            // Cek apakah notifikasi di list sudah habis
            const remainingItems = document.querySelectorAll('.drawer-notif-item');
            if (remainingItems.length === 0) {
              const listContainer = document.getElementById('notif-drawer-list');
              if (listContainer) {
                listContainer.innerHTML = `
                  <div class="flex flex-col items-center justify-center py-20 px-4 text-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-3">
                      <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                      </svg>
                    </div>
                    <p class="text-sm font-bold text-slate-700">Tidak Ada Notifikasi</p>
                    <p class="text-xs text-slate-400 mt-1 max-w-[220px]">Pemberitahuan pembaruan pengajuan videotron Anda akan muncul di sini.</p>
                  </div>
                `;
              }
              const actionsBar = document.getElementById('notif-actions-bar');
              if (actionsBar) {
                actionsBar.style.setProperty('display', 'none', 'important');
                actionsBar.classList.add('is-hidden');
              }
              window.updateAllBadgeCounts(0);
            }
          }, 200);
        };

        // Tandai semua notifikasi sudah dibaca (AJAX, tanpa reload, drawer tetap terbuka)
        window.markAllNotifsAsRead = function (e, url) {
          if (e) {
            e.preventDefault();
            e.stopPropagation();
          }
          const form = document.getElementById('form-mark-all-read');
          const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            || form?.querySelector('input[name="_token"]')?.value;

          // Hapus semua dot unread dan update status
          document.querySelectorAll('.sipetron-notif-unread-dot').forEach(function (el) {
            el.remove();
          });
          document.querySelectorAll('.drawer-notif-item').forEach(function (el) {
            el.setAttribute('data-unread', 'false');
          });
          if (form) form.style.display = 'none';
          window.updateAllBadgeCounts(0);

          fetch(url, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            }
          }).catch(function (err) { console.warn('[E-VITRON] Notification request failed:', err); });
        };

        // Hapus semua notifikasi (AJAX, dengan konfirmasi, tanpa reload, drawer tetap terbuka)
        window.clearAllNotifs = function (e, url) {
          if (e) {
            e.preventDefault();
            e.stopPropagation();
          }
          if (!confirm('Hapus semua notifikasi? Tindakan ini hanya akan mengosongkan daftar notifikasi Anda tanpa menghapus data pengajuan.')) {
            return;
          }

          const form = document.getElementById('form-clear-all');
          const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            || form?.querySelector('input[name="_token"]')?.value;

          const listContainer = document.getElementById('notif-drawer-list');
          if (listContainer) {
            listContainer.style.transition = 'opacity 150ms ease';
            listContainer.style.opacity = '0';
            setTimeout(function () {
              listContainer.innerHTML = `
                <div class="flex flex-col items-center justify-center py-20 px-4 text-center">
                  <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-3">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                  </div>
                  <p class="text-sm font-bold text-slate-700">Tidak Ada Notifikasi</p>
                  <p class="text-xs text-slate-400 mt-1 max-w-[220px]">Pemberitahuan pembaruan pengajuan videotron Anda akan muncul di sini.</p>
                </div>
              `;
              listContainer.style.opacity = '1';
            }, 150);
          }

          const actionsBar = document.getElementById('notif-actions-bar');
          if (actionsBar) {
            actionsBar.style.setProperty('display', 'none', 'important');
            actionsBar.classList.add('is-hidden');
          }
          window.updateAllBadgeCounts(0);

          fetch(url, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            }
          }).catch(function (err) { console.warn('[E-VITRON] Notification request failed:', err); });
        };

        // Otomatis buka slide-over drawer jika URL membawa parameter notif_drawer=1 (misal dari redirect /notifikasi)
        try {
          const urlParams = new URLSearchParams(window.location.search);
          if (urlParams.get('notif_drawer') === '1') {
            setTimeout(function () {
              if (typeof window.openNotifDrawer === 'function') {
                window.openNotifDrawer();
              }
            }, 120);

            const cleanUrl = new URL(window.location.href);
            cleanUrl.searchParams.delete('notif_drawer');
            window.history.replaceState({}, '', cleanUrl.toString());
          }
        } catch (err) { console.warn('[E-VITRON] Failed to parse notification drawer query param:', err); }

        // 1. Dropdown otomatis menutup saat halaman di-scroll (tidak melayang tertinggal)
        window.addEventListener('scroll', function (e) {
          if (!activeDropdown) return;
          if (e.target && e.target.nodeType === 1) {
            const panel = document.getElementById(activeDropdown.panelId);
            if (panel && panel.contains(e.target)) return;
          }
          closeDropdown(activeDropdown);
        }, { passive: true, capture: true });

        // 2. Dropdown & Drawer menutup saat pengguna mengklik di luar area (halaman kiri / luar panel)
        document.addEventListener('click', function (e) {
          if (activeDropdown) {
            const wrapper = document.getElementById(activeDropdown.wrapperId);
            if (wrapper && !wrapper.contains(e.target)) {
              closeDropdown(activeDropdown);
            }
          }

          // Klik pada halaman kiri (di luar drawer notifikasi) otomatis menutup drawer
          const drawer = document.getElementById('notif-drawer');
          if (drawer && drawer.classList.contains('translate-x-0')) {
            const notifBtn = document.getElementById('notif-btn');
            if (!drawer.contains(e.target) && (!notifBtn || !notifBtn.contains(e.target))) {
              closeNotifDrawer();
            }
          }

          // Klik di luar drawer navigasi mobile otomatis menutup drawer
          const mobileDrawer = document.getElementById('mobile-nav-drawer');
          if (mobileDrawer && mobileDrawer.classList.contains('translate-x-0')) {
            const menuBtn = document.getElementById('mobile-menu-btn');
            if (!mobileDrawer.contains(e.target) && (!menuBtn || !menuBtn.contains(e.target))) {
              closeMobileNav();
            }
          }
        });

        // 3. Menutup saat pengguna menekan tombol Escape (Drawer & Dropdown)
        document.addEventListener('keydown', function (e) {
          if (e.key === 'Escape') {
            const mobileDrawer = document.getElementById('mobile-nav-drawer');
            if (mobileDrawer && mobileDrawer.classList.contains('translate-x-0')) {
              closeMobileNav();
              return;
            }
            const drawer = document.getElementById('notif-drawer');
            if (drawer && drawer.classList.contains('translate-x-0')) {
              closeNotifDrawer();
              return;
            }
            if (activeDropdown) {
              const current = activeDropdown;
              closeDropdown(current);
              const trigger = document.getElementById(current.btnId);
              if (trigger) trigger.focus();
            }
          }
        });

        // 4. Pastikan semua drawer & dropdown selalu tertutup saat navigasi browser Back/Forward (BFCache)
        window.addEventListener('pageshow', function () {
          closeMobileNav();
          closeNotifDrawer();
          dropdowns.forEach(function (item) {
            closeDropdown(item);
          });
        });

        // 5. Tutup dropdown sebelum submit form (khususnya logout)
        document.querySelectorAll('form[action*="logout"]').forEach(function (form) {
          form.addEventListener('submit', function () {
            closeMobileNav();
            closeNotifDrawer();
            dropdowns.forEach(function (item) {
              closeDropdown(item);
            });
          });
        });

        // Inisialisasi: Pastikan kondisi awal selalu bersih dan tertutup
        dropdowns.forEach(function (item) {
          closeDropdown(item);
        });
      })();
    </script>

