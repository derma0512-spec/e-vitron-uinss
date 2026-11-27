    <script>
      (function () {
        'use strict';

        // ===== E-VITRON FLOATING TOAST SYSTEM (DENGAN MANAJEMEN ANTRIAN SIDEBAR) =====
        window._pendingToastQueue = [];
        window._activeToastTimers = new Map();

        window._isMobileNavOpen = function () {
          return document.documentElement.classList.contains('mobile-nav-open');
        };

        window._pauseAllToasts = function () {
          window._activeToastTimers.forEach(function (timerId) {
            clearTimeout(timerId);
          });
          window._activeToastTimers.clear();
        };

        window._resumeAllToasts = function () {
          // 1. Resume auto-dismiss untuk toast yang sedang ada di DOM (diberi waktu 4.5 detik untuk dibaca)
          document.querySelectorAll('#sipetron-toast-container .sipetron-toast-floating-item').forEach(function (item) {
            const timer = setTimeout(function () {
              window.dismissToast(item);
            }, 4500);
            window._activeToastTimers.set(item, timer);
          });

          // 2. Munculkan toast yang sempat tertunda saat sidebar terbuka
          if (window._pendingToastQueue.length > 0) {
            const queue = window._pendingToastQueue.slice();
            window._pendingToastQueue = [];
            queue.forEach(function (opts, idx) {
              setTimeout(function () {
                window.sipetronToast(opts);
              }, 250 + (idx * 300));
            });
          }
        };

        window.dismissToast = function (el) {
          if (!el) return;
          if (window._activeToastTimers.has(el)) {
            clearTimeout(window._activeToastTimers.get(el));
            window._activeToastTimers.delete(el);
          }
          el.classList.remove('is-entering');
          el.classList.add('is-leaving');
          setTimeout(function () {
            if (el && el.parentNode) {
              el.parentNode.removeChild(el);
            }
          }, 200);
        };

        window.sipetronToast = function (opts) {
          opts = opts || {};

          function escapeHtml(value) {
            return String(value == null ? '' : value)
              .replace(/&/g, '&amp;')
              .replace(/</g, '&lt;')
              .replace(/>/g, '&gt;')
              .replace(/"/g, '&quot;')
              .replace(/'/g, '&#39;');
          }

          // Jika sidebar mobile sedang terbuka, tunda kemunculan toast sampai sidebar ditutup
          if (window._isMobileNavOpen()) {
            window._pendingToastQueue.push(opts);
            return;
          }

          const container = document.getElementById('sipetron-toast-container');
          if (!container) return;

          const title = opts.title || (opts.type === 'error' ? 'Terjadi Kesalahan' : 'Berhasil');
          const message = opts.message || '';
          const ticketCode = opts.ticketCode || '';
          const safeTitle = escapeHtml(title);
          const safeMessage = escapeHtml(message);
          const safeTicketCode = escapeHtml(ticketCode);
          const isError = opts.type === 'error';
          const duration = opts.duration || 3200;
          const url = typeof opts.url === 'string' && !/^\s*javascript:/i.test(opts.url) ? opts.url : '';
          const hasAction = (typeof opts.onClick === 'function') || Boolean(url);

          const item = document.createElement('div');
          item.className = 'sipetron-toast-floating-item is-entering';
          
          let iconSvg = isError
            ? '<svg viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>'
            : '<svg viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';

          let metaHtml = '';
          if (safeTicketCode || safeMessage) {
            metaHtml = '<div class="sipetron-toast-meta">';
            if (safeTicketCode) {
              metaHtml += '<span class="sipetron-toast-ticket">' + safeTicketCode + '</span>';
              if (safeMessage) metaHtml += '<span class="sipetron-toast-dot" aria-hidden="true">·</span>';
            }
            if (safeMessage) {
              metaHtml += '<span class="sipetron-toast-desc" title="' + safeMessage + '">' + safeMessage + '</span>';
            }
            metaHtml += '</div>';
          }

          item.innerHTML = `
            <div class="sipetron-toast-card ${isError ? 'is-danger' : ''} ${hasAction ? 'is-clickable' : ''}">
              <div class="sipetron-toast-icon-circle ${isError ? 'is-danger' : ''}">
                ${iconSvg}
              </div>
              <div class="sipetron-toast-content">
                <h4 class="sipetron-toast-title">${safeTitle}</h4>
                ${metaHtml}
              </div>
              <button type="button" class="sipetron-toast-close-btn" title="Tutup notifikasi" aria-label="Tutup notifikasi">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
              </button>
            </div>
          `;

          const closeBtn = item.querySelector('.sipetron-toast-close-btn');
          if (closeBtn) {
            closeBtn.addEventListener('click', function (e) {
              e.stopPropagation();
              window.dismissToast(item);
            });
          }

          if (typeof opts.onClick === 'function') {
            const card = item.querySelector('.sipetron-toast-card');
            if (card) {
              card.addEventListener('click', function () {
                opts.onClick();
                window.dismissToast(item);
              });
            }
          } else if (url) {
            const card = item.querySelector('.sipetron-toast-card');
            if (card) {
              card.addEventListener('click', function () {
                window.location.href = url;
              });
            }
          }

          container.appendChild(item);

          let autoTimer = setTimeout(function () {
            window.dismissToast(item);
          }, duration);
          window._activeToastTimers.set(item, autoTimer);

          item.addEventListener('mouseenter', function () {
            clearTimeout(autoTimer);
            window._activeToastTimers.delete(item);
          });

          item.addEventListener('mouseleave', function () {
            autoTimer = setTimeout(function () {
              window.dismissToast(item);
            }, 3000);
            window._activeToastTimers.set(item, autoTimer);
          });
        };

        // ===== UNIVERSAL COPY TICKET TO CLIPBOARD (SUPPORTS HTTP & HTTPS WITH FALLBACK) =====
        let lastCopyTimestamp = 0;
        window.copyTicketToClipboard = function (code, btn) {
          const now = Date.now();
          if (now - lastCopyTimestamp < 400) return;
          lastCopyTimestamp = now;

          const text = String(code || '').trim();
          if (!text) return;

          function triggerSuccess() {
            if (btn) {
              const iconStandby = btn.querySelector('.sipetron-copy-icon-standby');
              const iconCopied = btn.querySelector('.sipetron-copy-icon-copied');

              if (iconStandby) iconStandby.style.display = 'none';
              if (iconCopied) iconCopied.style.display = 'block';

              btn.style.borderColor = '#86EFAC';
              btn.style.backgroundColor = '#F0FDF4';

              const parent = btn.parentElement;
              const badgeCopied = parent ? parent.querySelector('.sipetron-copy-badge-copied') : null;
              if (badgeCopied) badgeCopied.style.display = 'inline-block';

              setTimeout(function () {
                if (iconStandby) iconStandby.style.display = '';
                if (iconCopied) iconCopied.style.display = 'none';
                if (badgeCopied) badgeCopied.style.display = 'none';
                btn.style.borderColor = '';
                btn.style.backgroundColor = '';
              }, 2000);
            }
          }

          if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(triggerSuccess).catch(function () {
              execFallback();
            });
          } else {
            execFallback();
          }

          function execFallback() {
            try {
              const ta = document.createElement('textarea');
              ta.value = text;
              ta.style.position = 'fixed';
              ta.style.top = '0';
              ta.style.left = '0';
              ta.style.width = '2em';
              ta.style.height = '2em';
              ta.style.padding = '0';
              ta.style.border = 'none';
              ta.style.outline = 'none';
              ta.style.boxShadow = 'none';
              ta.style.background = 'transparent';
              ta.style.opacity = '0';
              ta.style.pointerEvents = 'none';
              document.body.appendChild(ta);
              ta.focus();
              ta.select();
              const ok = document.execCommand('copy');
              document.body.removeChild(ta);
              if (ok) {
                triggerSuccess();
              }
            } catch (err) {
              console.error('Copy fallback error:', err);
            }
          }
        };

        // Auto-dismiss initial flash messages after 3.2 seconds
        document.querySelectorAll('#sipetron-toast-container .sipetron-toast-floating-item').forEach(function (item) {
          let autoTimer = setTimeout(function () {
            window.dismissToast(item);
          }, 3200);
          window._activeToastTimers.set(item, autoTimer);

          item.addEventListener('mouseenter', function () {
            clearTimeout(autoTimer);
            window._activeToastTimers.delete(item);
          });

          item.addEventListener('mouseleave', function () {
            autoTimer = setTimeout(function () {
              window.dismissToast(item);
            }, 3000);
            window._activeToastTimers.set(item, autoTimer);
          });
        });

        @auth
        // ===== REAL-TIME NOTIFICATION POLLING (OPSI A: PERSIS PANEL ADMIN) =====
        (function () {
          const checkUrl = '{{ route('notifikasi.checkUnread') }}';
          let isFirstPoll = true;
          let lastPollAt = 0;
          let lastDrawerSignature = null;
          let shownToasts = [];
          try {
            shownToasts = JSON.parse(sessionStorage.getItem('sipetron_shown_toast_ids') || '[]');
          } catch (e) {
            shownToasts = [];
          }

          function escapeNotifHtml(value) {
            return String(value == null ? '' : value)
              .replace(/&/g, '&amp;')
              .replace(/</g, '&lt;')
              .replace(/>/g, '&gt;')
              .replace(/"/g, '&quot;')
              .replace(/'/g, '&#39;');
          }

          function getDrawerIconSvg(typeClass) {
            if (typeClass === 'is-success') {
              return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>';
            }
            if (typeClass === 'is-danger') {
              return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>';
            }
            return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>';
          }

          function renderEmptyDrawerState() {
            return `
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

          function syncDrawerList(data) {
            const listContainer = document.getElementById('notif-drawer-list');
            if (!listContainer || !Array.isArray(data.drawer_notifications)) {
              return;
            }

            const items = data.drawer_notifications;
            const totalCount = typeof data.total_count === 'number' ? data.total_count : items.length;
            const unreadCount = parseInt(data.unread_count || 0, 10);

            // Sinkronkan visibilitas bar aksi header drawer
            const actionsBar = document.getElementById('notif-actions-bar');
            const markReadForm = document.getElementById('form-mark-all-read');
            const clearAllForm = document.getElementById('form-clear-all');
            if (actionsBar) {
              if (totalCount > 0) {
                actionsBar.style.setProperty('display', '', '');
                actionsBar.classList.remove('is-hidden');
              } else {
                actionsBar.style.setProperty('display', 'none', 'important');
                actionsBar.classList.add('is-hidden');
              }
            }
            if (markReadForm) {
              if (unreadCount > 0) {
                markReadForm.style.setProperty('display', 'inline', '');
                markReadForm.classList.remove('is-hidden');
              } else {
                markReadForm.style.setProperty('display', 'none', 'important');
                markReadForm.classList.add('is-hidden');
              }
            }
            if (clearAllForm) {
              if (totalCount > 0) {
                clearAllForm.style.setProperty('display', 'inline', '');
                clearAllForm.classList.remove('is-hidden');
              } else {
                clearAllForm.style.setProperty('display', 'none', 'important');
                clearAllForm.classList.add('is-hidden');
              }
            }

            const signature = items.map(n => `${n.id}:${n.is_unread ? 1 : 0}:${n.time_ago || ''}`).join('|');
            if (lastDrawerSignature === signature) {
              return;
            }
            lastDrawerSignature = signature;

            if (items.length === 0) {
              listContainer.innerHTML = renderEmptyDrawerState();
              return;
            }

            const html = items.map(n => {
              const safeId = escapeNotifHtml(n.id);
              const typeClass = ['is-success', 'is-danger', 'is-info'].includes(n.type_class) ? n.type_class : 'is-info';
              const safeTitle = escapeNotifHtml(n.title || 'Pemberitahuan Status');
              const safeDate = escapeNotifHtml(n.time_ago || 'Baru saja');
              const safeTicket = escapeNotifHtml(n.ticket_code || '');
              const safeActivity = escapeNotifHtml(n.submission_title || '');
              const safeDesc = escapeNotifHtml(n.message || '');
              const safeUrl = typeof n.url === 'string' && !/^\s*javascript:/i.test(n.url) ? escapeNotifHtml(n.url) : '#';
              const safeDeleteUrl = typeof n.delete_url === 'string' && !/^\s*javascript:/i.test(n.delete_url) ? escapeNotifHtml(n.delete_url) : '';
              const unreadAttr = n.is_unread ? 'true' : 'false';
              const unreadDot = n.is_unread ? '<span class="sipetron-notif-unread-dot" aria-hidden="true"></span>' : '';
              const row2 = (safeTicket || safeActivity)
                ? `<div class="sipetron-notif-row-2">${safeTicket ? `<span class="sipetron-notif-pill ${typeClass}">${safeTicket}</span>` : ''}${safeActivity ? `<span class="sipetron-notif-activity-title" title="${safeActivity}">${safeActivity}</span>` : ''}</div>`
                : '';
              const descHtml = safeDesc ? `<p class="sipetron-notif-desc-text" title="${safeDesc}">${safeDesc}</p>` : '';

              return `
                <div id="notif-item-${safeId}" class="drawer-notif-item" data-unread="${unreadAttr}">
                  <div class="sipetron-notif-card ${typeClass}" data-notif-url="${safeUrl}">
                    <div class="sipetron-notif-icon-box ${typeClass}">${getDrawerIconSvg(typeClass)}</div>
                    <div class="sipetron-notif-body-wrap">
                      <div class="sipetron-notif-row-1">
                        <h4 class="sipetron-notif-card-title">${safeTitle}</h4>
                        <div class="sipetron-notif-meta-right">
                          <span class="sipetron-notif-time-text">${safeDate}</span>
                          ${unreadDot}
                        </div>
                      </div>
                      ${row2}
                      ${descHtml}
                    </div>
                    <div class="sipetron-notif-right-col" data-notif-right-col="true">
                      <svg class="sipetron-notif-arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polyline points="9 18 15 12 9 6"></polyline>
                      </svg>
                      <button type="button"
                              title="Hapus notifikasi ini"
                              aria-label="Hapus notifikasi ini"
                              class="sipetron-notif-del-btn"
                              data-delete-id="${safeId}"
                              data-delete-url="${safeDeleteUrl}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                          <line x1="18" y1="6" x2="6" y2="18"></line>
                          <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              `;
            }).join('');

            listContainer.innerHTML = html;

            listContainer.querySelectorAll('.sipetron-notif-card[data-notif-url]').forEach(card => {
              card.addEventListener('click', function () {
                const target = card.getAttribute('data-notif-url');
                if (target && target !== '#') {
                  window.location.href = target;
                }
              });
            });

            listContainer.querySelectorAll('[data-notif-right-col="true"]').forEach(col => {
              col.addEventListener('click', function (ev) {
                ev.stopPropagation();
              });
            });

            listContainer.querySelectorAll('.sipetron-notif-del-btn[data-delete-id]').forEach(btn => {
              btn.addEventListener('click', function (ev) {
                ev.preventDefault();
                ev.stopPropagation();
                const id = btn.getAttribute('data-delete-id');
                const delUrl = btn.getAttribute('data-delete-url');
                if (id && delUrl && typeof window.deleteSingleNotif === 'function') {
                  window.deleteSingleNotif(ev, id, delUrl);
                }
              });
            });
          }

          async function pollUnread(force) {
            const now = Date.now();
            if (!force && (now - lastPollAt < 1500)) {
              return;
            }
            lastPollAt = now;

            try {
              const res = await fetch(checkUrl, {
                headers: {
                  'Accept': 'application/json',
                  'X-Requested-With': 'XMLHttpRequest'
                }
              });
              if (!res.ok) return;
              const data = await res.json();

              // 1. Update Badge Lonceng Navbar
              const badgeContainer = document.getElementById('notif-badge-container');
              if (badgeContainer) {
                if (data.unread_count > 0) {
                  const badgeText = data.unread_count > 99 ? '99+' : data.unread_count;
                  badgeContainer.innerHTML = `<span id="notif-badge-count" class="absolute top-1.5 right-1.5 flex h-4 min-w-[16px] px-1 items-center justify-center rounded-full bg-[#D9A93B] text-[9px] font-extrabold text-white border-2 border-white shadow-xs leading-none transition-transform group-hover/notif:scale-110">${badgeText}</span>`;
                } else {
                  badgeContainer.innerHTML = '';
                }
              }

              // 2. Update Tooltip Lonceng
              const tooltipEl = document.querySelector('#notif-tooltip div');
              if (tooltipEl) {
                tooltipEl.innerHTML = data.unread_count > 0
                  ? `${data.unread_count} Notifikasi Baru<div class="absolute -top-1 left-1/2 -translate-x-1/2 border-4 border-transparent border-b-[#0E4D3C]"></div>`
                  : `Tidak ada notifikasi baru<div class="absolute -top-1 left-1/2 -translate-x-1/2 border-4 border-transparent border-b-[#0E4D3C]"></div>`;
              }

              // 3. Update Badge Drawer Header jika ada
              const drawerBadge = document.querySelector('.sipetron-notif-badge');
              if (drawerBadge) {
                if (data.unread_count > 0) {
                  drawerBadge.textContent = data.unread_count;
                  drawerBadge.style.setProperty('display', 'inline-flex', '');
                } else {
                  drawerBadge.style.setProperty('display', 'none', 'important');
                }
              }

              // 3b. Sinkronkan isi daftar laci notifikasi tanpa perlu F5
              syncDrawerList(data);

              // 4. Deteksi Notifikasi Baru Masuk & Munculkan Floating Toast
              // ATURAN PROFESIONAL & BERSIH:
              // 1. Pop-up toast HANYA untuk status pengajuan personal user (Disetujui, Perlu Revisi, Ditolak).
              // 2. Status teknis perangkat videotron (pulih / kendala) TIDAK PERNAH memunculkan pop-up toast,
              //    melainkan cukup masuk ke lonceng drawer & menaikkan angka unread.
              // 3. Pop-up HANYA terjadi secara real-time saat user sedang aktif membuka web (!isFirstPoll & age <= 90s).
              const notifications = data.notifications || (data.latest ? [data.latest] : []);
              const FRESH_WINDOW_SECONDS = 90;
              const MAX_TOASTS_PER_POLL = 2;

              const candidates = notifications.filter(n => {
                if (!n || !n.id || shownToasts.includes(n.id)) return false;

                // Hanya izinkan status pengajuan langsung milik user: disetujui, revisi, ditolak
                const event = String(n.event || '').toLowerCase();
                const title = String(n.title || '').toLowerCase();
                const isSubmissionStatus = ['disetujui', 'revisi', 'ditolak'].includes(event)
                  || title.includes('disetujui')
                  || title.includes('revisi')
                  || title.includes('ditolak');

                if (!isSubmissionStatus) {
                  return false;
                }

                // Jangan pernah pop-up jika baru pertama kali load/refresh halaman
                if (isFirstPoll) {
                  return false;
                }

                // Wajib baru terjadi saat user aktif di halaman (< 90 detik)
                const isFresh = typeof n.age_seconds === 'number' && n.age_seconds <= FRESH_WINDOW_SECONDS;
                if (!isFresh) {
                  return false;
                }

                return true;
              });

              // Deteksi jika ada notifikasi baru masuk untuk animasi getar/goyang lonceng
              if (!isFirstPoll && notifications.some(n => n && typeof n.age_seconds === 'number' && n.age_seconds <= FRESH_WINDOW_SECONDS && !shownToasts.includes(n.id))) {
                const bellIcon = document.querySelector('#notif-btn .bell-icon');
                if (bellIcon) {
                  bellIcon.style.animation = 'none';
                  bellIcon.offsetHeight; // force reflow
                  bellIcon.style.animation = 'bell-ring 600ms ease-in-out 2';
                }
              }

              // Tandai semua notifikasi saat ini sebagai sudah diproses agar tidak duplikasi
              notifications.forEach(n => {
                if (n && n.id && !shownToasts.includes(n.id)) {
                  shownToasts.push(n.id);
                }
              });
              isFirstPoll = false;

              if (candidates.length > 0) {
                try {
                  sessionStorage.setItem('sipetron_shown_toast_ids', JSON.stringify(shownToasts.slice(-100)));
                } catch (err) { console.warn('[E-VITRON] Could not persist toast history in sessionStorage:', err); }

                candidates.slice(0, MAX_TOASTS_PER_POLL).forEach(n => {
                  if (!window.sipetronToast) return;

                  window.sipetronToast({
                    title: n.title,
                    ticketCode: n.ticket_code,
                    message: n.message,
                    type: n.type,
                    url: n.url,
                    duration: 7000
                  });
                });
              }
            } catch (err) {
              // Silently ignore network hiccup
            }
          }

          window.pollPortalNotifications = pollUnread;

          // Panggil langsung seketika halaman dibuka
          pollUnread(true);

          // Polling berkala setiap 10 detik (hanya saat tab aktif/terlihat agar tidak membebani worker PHP di background)
          setInterval(function () {
            if (document.visibilityState === 'visible') {
              pollUnread(false);
            }
          }, 10000);

          // Cek segera saat tab atau jendela browser kembali dibuka/aktif
          document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'visible') {
              pollUnread(false);
            }
          });

          window.addEventListener('focus', function () {
            pollUnread(false);
          });
        })();
        @endauth
      })();
    </script>

