    @auth
    {{-- ===== SLIDE-OVER NOTIFIKASI DRAWER (SAMA PERSIS DENGAN PANEL ADMIN) ===== --}}
    <!-- Backdrop Overlay (Sama Persis Panel Admin: Hitam Netral 60% Full Viewport) -->
    <div id="notif-drawer-backdrop" 
         class="fixed inset-0 bg-black/60 transition-opacity duration-300 opacity-0 pointer-events-none" 
         style="z-index: 99990 !important; cursor: pointer;"
         aria-hidden="true" 
         onclick="closeNotifDrawer()"></div>

    <!-- Drawer Panel Samping Kanan (100vh Full Viewport Height, max-w-md Sama Persis Admin) -->
    <aside id="notif-drawer" 
           class="fixed top-0 bottom-0 right-0 h-screen w-full max-w-md bg-white shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300 ease-out box-border" 
           style="z-index: 99991 !important;"
           role="dialog" 
           aria-modal="true" 
           aria-label="Pusat Notifikasi">

      <!-- 1. Header Drawer (Sama Persis Admin: Heading + Badge + Subtitle + Action Buttons + Close [X]) -->
      <div class="relative p-6 border-b border-gray-200 bg-white shrink-0">
        {{-- Tombol Close [X] di pojok kanan atas --}}
        <button type="button" 
                onclick="closeNotifDrawer()" 
                aria-label="Tutup"
                class="fi-modal-close-btn absolute top-5 right-5 p-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors cursor-pointer">
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>

        <div class="sipetron-notif-header-wrap">
          <div class="sipetron-notif-header-title-row">
            <h2 class="sipetron-notif-header-title">
              Notifikasi Masuk
            </h2>
            <span class="sipetron-notif-badge" style="{{ $unreadNotifsCount > 0 ? '' : 'display: none !important;' }}" title="{{ $unreadNotifsCount }} belum dibaca">
              {{ $unreadNotifsCount }}
            </span>
          </div>

          <p class="sipetron-notif-heading-subtitle">
            Pantau pembaruan pengajuan videotron Anda.
          </p>

          {{-- Action Buttons Bar (Sama Persis Admin) --}}
          <div class="sipetron-notif-actions-bar" id="notif-actions-bar">
            @if ($unreadNotifsCount > 0)
              <form method="POST" action="{{ route('notifikasi.markAllAsRead') }}" class="inline" id="form-mark-all-read" onsubmit="markAllNotifsAsRead(event, '{{ route('notifikasi.markAllAsRead') }}')">
                @csrf
                <button type="submit" 
                        class="sipetron-notif-action-btn sipetron-notif-action-btn--read" 
                        title="Tandai semua dibaca">
                  <svg class="sipetron-notif-action-icon" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                  </svg>
                  <span>Tandai Semua Dibaca</span>
                </button>
              </form>
            @endif

            @if ($userNotifications->count() > 0)
              <form method="POST" action="{{ route('notifikasi.clearAll') }}" class="inline" id="form-clear-all" onsubmit="clearAllNotifs(event, '{{ route('notifikasi.clearAll') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="sipetron-notif-action-btn sipetron-notif-action-btn--clear" 
                        title="Hapus Semua">
                  <svg class="sipetron-notif-action-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                  </svg>
                  <span>Hapus Semua</span>
                </button>
              </form>
            @endif
          </div>
        </div>
      </div>

      <!-- 2. Scrollable Body: Daftar Kartu Notifikasi (.sipetron-notif-list Sama Persis Admin) -->
      <div class="sipetron-notif-list flex-1 overflow-y-auto" id="notif-drawer-list">
        @forelse($userNotifications as $notification)
          @php
            $data = $notification->data;
            $isUnread = is_null($notification->read_at);
            $event = $data['event'] ?? 'submitted';
            $title = $data['title'] ?? 'Pemberitahuan Status';
            // Singkatkan judul kendala & pemulihan agar nama videotron langsung terlihat jelas tanpa terpotong
            $title = preg_replace('/^(?:Pemberitahuan\s+)?Kendala\s+(?:Perangkat:\s*)?/ui', 'Kendala: ', $title);
            $title = preg_replace('/^Kabar\s+Baik:\s*Perangkat\s*(.*?)\s*Selesai\s*Diperbaiki$/ui', 'Kabar Baik: $1 Pulih', $title);
            $title = preg_replace('/^Kabar\s+Baik:\s*(.*?)\s*Normal\s*Kembali$/ui', 'Kabar Baik: $1 Pulih', $title);
            
            $date = $notification->created_at ? $notification->created_at->locale('id')->diffForHumans() : '';
            $ticketCode = $data['ticket_code'] ?? null;
            $activityTitle = $data['submission_title'] ?? null;
            $desc = !empty($data['notes']) ? ('Catatan Admin: "' . $data['notes'] . '"') : ($data['message'] ?? '');

            $isSuccess = in_array($event, ['disetujui', 'selesai', 'videotron_recovered']) || str_contains($title, 'Disetujui') || str_contains($title, 'Selesai') || str_contains($title, 'Selesai Diperbaiki');
            $isDanger = in_array($event, ['ditolak', 'videotron_disruption']) || str_contains($title, 'Ditolak') || str_contains($title, 'Batal') || str_contains($title, 'Kendala Perangkat');
            $isInfo = !$isSuccess && !$isDanger;

            $typeClass = match (true) {
                $isDanger => 'is-danger',
                $isSuccess => 'is-success',
                default => 'is-info',
            };

            $iconSvg = match (true) {
                $isSuccess => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>',
                $isInfo => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>',
                $isDanger => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
                default => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>',
            };

            $targetUrl = route('notifikasi.read', $notification->id);
          @endphp

          <div id="notif-item-{{ $notification->id }}" class="drawer-notif-item" data-unread="{{ $isUnread ? 'true' : 'false' }}">
            <div class="sipetron-notif-card {{ $typeClass }}"
                 onclick="window.location.href='{{ $targetUrl }}'">
              
              {{-- Icon kiri --}}
              <div class="sipetron-notif-icon-box {{ $typeClass }}">
                {!! $iconSvg !!}
              </div>

              {{-- Konten teks --}}
              <div class="sipetron-notif-body-wrap">
                {{-- Baris 1: Judul + Waktu + Dot --}}
                <div class="sipetron-notif-row-1">
                  <h4 class="sipetron-notif-card-title">
                    {{ $title }}
                  </h4>

                  <div class="sipetron-notif-meta-right">
                    <span class="sipetron-notif-time-text">{{ $date }}</span>
                    @if ($isUnread)
                      <span class="sipetron-notif-unread-dot" aria-hidden="true"></span>
                    @endif
                  </div>
                </div>

                {{-- Baris 2: Pill Tiket + Nama Kegiatan --}}
                @if ($ticketCode || $activityTitle)
                  <div class="sipetron-notif-row-2">
                    @if ($ticketCode)
                      <span class="sipetron-notif-pill {{ $typeClass }}">{{ $ticketCode }}</span>
                    @endif
                    @if ($activityTitle)
                      <span class="sipetron-notif-activity-title" title="{{ $activityTitle }}">
                        {{ $activityTitle }}
                      </span>
                    @endif
                  </div>
                @endif

                {{-- Baris 3: Deskripsi singkat / Catatan Admin --}}
                @if ($desc)
                  <p class="sipetron-notif-desc-text" title="{{ $desc }}">{{ $desc }}</p>
                @endif
              </div>

              {{-- Kolom kanan: arrow dan delete toggle --}}
              <div class="sipetron-notif-right-col" onclick="event.stopPropagation()">
                <svg class="sipetron-notif-arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <polyline points="9 18 15 12 9 6"></polyline>
                </svg>

                <form method="POST" action="{{ route('notifikasi.destroy', $notification->id) }}" onsubmit="deleteSingleNotif(event, '{{ $notification->id }}', '{{ route('notifikasi.destroy', $notification->id) }}')" onclick="event.stopPropagation()">
                  @csrf
                  @method('DELETE')
                  <button type="submit" 
                          title="Hapus notifikasi ini" 
                          aria-label="Hapus notifikasi ini"
                          onclick="event.stopPropagation()"
                          class="sipetron-notif-del-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <line x1="18" y1="6" x2="6" y2="18"></line>
                      <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                  </button>
                </form>
              </div>
            </div>
          </div>
        @empty
          <div class="flex flex-col items-center justify-center py-20 px-4 text-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-3">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
              </svg>
            </div>
            <p class="text-sm font-bold text-slate-700">Tidak Ada Notifikasi</p>
            <p class="text-xs text-slate-400 mt-1 max-w-[220px]">Pemberitahuan pembaruan pengajuan videotron Anda akan muncul di sini.</p>
          </div>
        @endforelse
      </div>
    </aside>
    @endauth

