@php
    $user = filament()->auth()->user();
    $name = $user?->name ?? 'Admin E-VITRON';
    $email = $user?->email ?? 'admin@uin.ac.id';
    $role = $user?->role ?? 'admin';
    $roleLabel = $user?->isAdmin() ? $user->adminRoleLabel() : ($role === 'pengaju' ? 'Pengaju' : 'Pengguna');
    
    $cleanWords = array_values(array_filter(explode(' ', trim($name))));
    $initials = 'AP';
    if (count($cleanWords) >= 2) {
        $initials = strtoupper(mb_substr($cleanWords[0], 0, 1) . mb_substr($cleanWords[1], 0, 1));
    } elseif (count($cleanWords) === 1 && !empty($cleanWords[0])) {
        $initials = strtoupper(mb_substr($cleanWords[0], 0, min(2, mb_strlen($cleanWords[0]))));
    }
    
    $logoutUrl = filament()->getLogoutUrl() ?: route('filament.admin.auth.logout');
@endphp

{{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::USER_MENU_BEFORE) }}

<div
    x-data="{
        toggle(event) {
            if ($refs.panel) {
                $refs.panel.style.display = '';
                $refs.panel.style.opacity = '';
                $refs.panel.style.visibility = '';
            }
            $refs.panel?.toggle(event)
        },
        open(event) {
            if ($refs.panel) {
                $refs.panel.style.display = '';
                $refs.panel.style.opacity = '';
                $refs.panel.style.visibility = '';
            }
            $refs.panel?.open(event)
        },
        close() {
            if ($refs.panel) {
                $refs.panel.style.display = 'none';
            }
            $refs.panel?.close()
        },
    }"
    x-init="
        const dismissDropdown = () => {
            if ($refs.panel) {
                $refs.panel.style.display = 'none';
                $refs.panel.style.opacity = '0';
                $refs.panel.style.visibility = 'hidden';
                $refs.panel?.close();
            }
        };
        window.addEventListener('wheel', dismissDropdown, { passive: true, capture: true });
        window.addEventListener('scroll', dismissDropdown, { passive: true, capture: true });
        window.addEventListener('touchmove', dismissDropdown, { passive: true, capture: true });
        document.addEventListener('scroll', dismissDropdown, { passive: true, capture: true });
        window.addEventListener('pointerdown', (e) => {
            if (!$el.contains(e.target) && $refs.panel && !$refs.panel.contains(e.target)) {
                dismissDropdown();
            }
        }, { passive: true, capture: true });
        window.addEventListener('keydown', (e) => {
            if (['ArrowDown', 'ArrowUp', 'PageDown', 'PageUp', 'Home', 'End', ' ', 'Escape'].includes(e.key)) {
                dismissDropdown();
            }
        }, { passive: true, capture: true });
    "
    class="fi-dropdown fi-user-menu"
>
    <div
        x-on:click="toggle"
        class="fi-dropdown-trigger flex cursor-pointer"
    >
        <button
            type="button"
            aria-label="Menu profil pengguna"
            class="sipetron-user-btn"
        >
            <span class="sipetron-user-avatar select-none">
                {{ $initials }}
            </span>
            <div class="sipetron-user-info">
                <span class="sipetron-user-name">{{ $name }}</span>
                <span class="sipetron-user-role">{{ $roleLabel }}</span>
            </div>
            <svg class="sipetron-user-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
    </div>

    <div
        x-cloak
        x-float.placement.bottom-end.flip.teleport.offset="{ offset: 8 }"
        x-ref="panel"
        class="fi-dropdown-panel sipetron-green-dropdown absolute z-50 overflow-hidden bg-white shadow-xl"
    >
        {{-- Profile Header Card (Hijau Elegan) --}}
        <div class="sipetron-dropdown-header">
            <div class="sipetron-dropdown-header-top">
                <div class="sipetron-header-avatar select-none">
                    {{ $initials }}
                </div>
                <div class="sipetron-header-meta">
                    <div class="sipetron-header-name">{{ $name }}</div>
                    <div class="sipetron-header-email">{{ $email }}</div>
                    <div class="sipetron-header-role-badge">
                        <span class="sipetron-header-role-dot"></span>
                        <span>{{ $roleLabel }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Menu Aksi Profil --}}
        <div class="sipetron-dropdown-footer">
            {{-- Tombol Ubah Password --}}
            <button type="button" 
                    @click="close(); $dispatch('open-change-password-modal')" 
                    class="sipetron-dropdown-action-btn">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                </svg>
                <span>Ubah Password</span>
            </button>

            <div class="sipetron-dropdown-divider"></div>

            {{-- Tombol Keluar Langsung di Bawah Kartu Hijau --}}
            <form method="POST" action="{{ $logoutUrl }}">
                @csrf
                <button type="submit" class="sipetron-dropdown-logout-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                    </svg>
                    <span>Keluar dari Sistem</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Modal Ubah Password Global --}}
    @include('filament.partials.change-password-modal')
</div>

{{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::USER_MENU_AFTER) }}
