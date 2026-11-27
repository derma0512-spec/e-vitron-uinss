@php
    $user = auth()->user();
    $name = $user?->name ?? 'Admin';
    $role = $user?->isAdmin() ? $user->adminRoleLabel() : ucfirst($user?->role ?? 'Administrator');
    $initials = collect(explode(' ', $name))->take(2)->map(fn($w) => strtoupper($w[0]))->implode('');
    $logoutUrl = route('filament.admin.auth.logout');
@endphp

<div class="ssp-footer" x-data="{ open: false }">

    <div class="ssp-dropup" x-show="open" @click.outside="open = false" style="display:none;">

        {{-- Info profil di atas dropup --}}
        <div class="ssp-drop-profile">
            <div class="ssp-drop-avatar">{{ $initials }}</div>
            <div class="ssp-drop-info">
                <span class="ssp-drop-name">{{ Str::limit($name, 22) }}</span>
                <span class="ssp-drop-role">{{ $role }}</span>
            </div>
        </div>

        <div class="ssp-drop-divider"></div>

        {{-- Tombol Ubah Password --}}
        <button type="button" 
                @click="open = false; $dispatch('open-change-password-modal')" 
                class="ssp-drop-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
            </svg>
            <span>Ubah Password</span>
        </button>

        <div class="ssp-drop-divider"></div>

        {{-- Tombol Keluar --}}
        <form method="POST" action="{{ $logoutUrl }}">
            @csrf
            <button type="submit" class="ssp-drop-item ssp-drop-logout">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                <span>Keluar</span>
            </button>
        </form>
    </div>

    {{-- Trigger: Profile Card --}}
    <button class="ssp-profile-trigger" @click="open = !open" :class="{ 'ssp-profile-active': open }">
        <div class="ssp-trigger-avatar">{{ $initials }}</div>
        <div class="ssp-trigger-info">
            <span class="ssp-trigger-name">{{ Str::limit($name, 18) }}</span>
            <span class="ssp-trigger-role">{{ $role }}</span>
        </div>
        <svg class="ssp-trigger-chevron" :class="{ 'ssp-chevron-up': open }"
             xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round">
            <polyline points="18 15 12 9 6 15"/>
        </svg>
    </button>

</div>
