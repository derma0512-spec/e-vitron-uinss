{{-- ================================================================
     TAB 5 — TIM ADMIN (KHUSUS SUPER ADMIN)
================================================================ --}}
@php
    $adminAccounts = $this->adminAccounts;
    $activeAdminsCount = $adminAccounts->filter(fn ($a) => $a->isApproved())->count();
    $activeSuperAdminsCount = $adminAccounts->filter(fn ($a) => $a->isSuperAdmin())->count();
    $currentAdminId = (int) auth()->id();
@endphp

<div x-show="tab === 'admins'" x-cloak class="sipetron-settings-card" style="display: none;">
    <div class="sipetron-card-header" style="display: flex !important; flex-direction: row !important; align-items: flex-start !important; justify-content: space-between !important; gap: 1rem; flex-wrap: wrap;">
        <div style="min-width: 240px; flex: 1 1 0%;">
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                <h3 class="sipetron-settings-card-title" style="margin: 0;">Tim Pengelola Admin E-VITRON</h3>
                <span style="display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.6875rem; font-weight: 700; color: #065F46; background: #ECFDF5; border: 1px solid #A7F3D0; padding: 0.125rem 0.55rem; border-radius: 9999px;">
                    {{ $activeAdminsCount }} Aktif
                </span>
                <span style="display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.6875rem; font-weight: 700; color: #92400E; background: #FFFBEB; border: 1px solid #FDE68A; padding: 0.125rem 0.55rem; border-radius: 9999px;">
                    {{ $activeSuperAdminsCount }} Super Admin
                </span>
            </div>
            <p class="sipetron-settings-card-desc" style="margin-top: 0.3rem;">
                Kelola akun pengelola internal (PUSTIKOM &amp; Operator). Akun yang tidak lagi bertugas cukup dinonaktifkan agar seluruh histori audit di Riwayat &amp; Rekap tetap utuh.
            </p>
        </div>

        <button
            type="button"
            wire:click="openCreateAdminModal"
            wire:loading.attr="disabled"
            wire:target="openCreateAdminModal"
            class="sipetron-settings-save-btn"
            style="height: 38px; padding: 0 1rem; font-size: 0.8125rem; flex-shrink: 0; align-self: center;"
        >
            <svg style="width: 15px; height: 15px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Tambah Admin</span>
        </button>
    </div>

    <div class="sipetron-form-section">
        <div class="sipetron-form-section-label">
            <span>Daftar Akun Pengelola</span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            @foreach($adminAccounts as $adminItem)
                @php
                    $isSelf = (int) $adminItem->id === $currentAdminId;
                    $isActive = $adminItem->isApproved();
                    $isSuper = (bool) $adminItem->is_super_admin;
                    $initials = collect(explode(' ', trim($adminItem->name)))
                        ->filter()
                        ->take(2)
                        ->map(fn ($word) => strtoupper(mb_substr($word, 0, 1)))
                        ->implode('') ?: 'AD';
                @endphp

                <div
                    wire:key="admin-account-row-{{ $adminItem->id }}"
                    style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; padding: 0.9rem 1rem; border-radius: 0.85rem; border: 1px solid {{ $isActive ? '#E2E8F0' : '#E5E7EB' }}; background: {{ $isActive ? '#FFFFFF' : '#F8FAFC' }};"
                >
                    {{-- Identitas Admin --}}
                    <div style="display: flex; align-items: center; gap: 0.8rem; min-width: 0; flex: 1 1 260px;">
                        <div style="width: 40px; height: 40px; border-radius: 9999px; background: {{ $isActive ? ($isSuper ? '#0F3D2E' : '#1E5546') : '#94A3B8' }}; color: #FFFFFF; font-weight: 800; font-size: 0.8125rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            {{ $initials }}
                        </div>

                        <div style="min-width: 0; flex: 1;">
                            <div style="display: flex; align-items: center; gap: 0.45rem; flex-wrap: wrap;">
                                <span style="font-size: 0.875rem; font-weight: 700; color: {{ $isActive ? '#0F172A' : '#64748B' }};">
                                    {{ $adminItem->name }}
                                </span>
                                @if($isSelf)
                                    <span style="font-size: 0.65625rem; font-weight: 700; color: #0F3D2E; background: #E8F3EC; border: 1px solid #C4DED0; padding: 0.05rem 0.45rem; border-radius: 9999px;">
                                        Anda
                                    </span>
                                @endif
                                @if($isSuper)
                                    <span style="font-size: 0.65625rem; font-weight: 700; color: #92400E; background: #FFFBEB; border: 1px solid #FDE68A; padding: 0.05rem 0.5rem; border-radius: 9999px;">
                                        Super Admin
                                    </span>
                                @else
                                    <span style="font-size: 0.65625rem; font-weight: 600; color: #334155; background: #F1F5F9; border: 1px solid #CBD5E1; padding: 0.05rem 0.5rem; border-radius: 9999px;">
                                        Admin Operator
                                    </span>
                                @endif
                                @if($isActive)
                                    <span style="display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.65625rem; font-weight: 700; color: #047857; background: #ECFDF5; border: 1px solid #A7F3D0; padding: 0.05rem 0.5rem; border-radius: 9999px;">
                                        <span style="width: 5px; height: 5px; border-radius: 9999px; background: #10B981;"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span style="display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.65625rem; font-weight: 700; color: #64748B; background: #F1F5F9; border: 1px solid #CBD5E1; padding: 0.05rem 0.5rem; border-radius: 9999px;">
                                        <span style="width: 5px; height: 5px; border-radius: 9999px; background: #94A3B8;"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </div>

                            <div style="font-size: 0.75rem; color: #475569; margin-top: 0.15rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $adminItem->email }}
                                @if(filled($adminItem->unit_or_prodi))
                                    <span style="color: #94A3B8;">&bull;</span> {{ $adminItem->unit_or_prodi }}
                                @endif
                                @if(filled($adminItem->phone))
                                    <span style="color: #94A3B8;">&bull;</span> WA: {{ $adminItem->phone }}
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Aksi Pengelolaan --}}
                    <div style="display: flex; align-items: center; gap: 0.45rem; flex-wrap: wrap; flex-shrink: 0;">
                        @if($isSelf)
                            <span style="font-size: 0.71875rem; font-weight: 600; color: #64748B; background: #F8FAFC; border: 1px dashed #CBD5E1; padding: 0.3rem 0.65rem; border-radius: 0.5rem;">
                                Gunakan menu profil untuk ganti password Anda
                            </span>
                        @else
                            <button
                                type="button"
                                wire:click="openResetPasswordModal({{ $adminItem->id }})"
                                style="display: inline-flex; align-items: center; gap: 0.35rem; height: 32px; padding: 0 0.7rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; background: #FFFFFF; color: #334155; font-size: 0.75rem; font-weight: 600; cursor: pointer;"
                            >
                                Reset Password
                            </button>

                            <button
                                type="button"
                                wire:click="toggleSuperAdminRole({{ $adminItem->id }})"
                                wire:confirm="{{ $isSuper ? "Turunkan {$adminItem->name} menjadi Admin Operator biasa?" : "Promosikan {$adminItem->name} menjadi Super Admin? Akun ini akan memiliki wewenang mengelola seluruh Tim Admin." }}"
                                style="display: inline-flex; align-items: center; gap: 0.35rem; height: 32px; padding: 0 0.7rem; border-radius: 0.5rem; border: 1px solid {{ $isSuper ? '#FDE68A' : '#C4DED0' }}; background: {{ $isSuper ? '#FFFBEB' : '#F0FDF4' }}; color: {{ $isSuper ? '#92400E' : '#0F3D2E' }}; font-size: 0.75rem; font-weight: 600; cursor: pointer;"
                            >
                                {{ $isSuper ? 'Turunkan ke Operator' : 'Jadikan Super Admin' }}
                            </button>

                            <button
                                type="button"
                                wire:click="toggleAdminStatus({{ $adminItem->id }})"
                                wire:confirm="{{ $isActive ? "Nonaktifkan akun admin {$adminItem->name} ({$adminItem->email})? Akun ini tidak akan bisa login dan berhenti menerima notifikasi, namun seluruh histori audit tetap utuh." : "Aktifkan kembali akun admin {$adminItem->name} ({$adminItem->email})?" }}"
                                style="display: inline-flex; align-items: center; gap: 0.35rem; height: 32px; padding: 0 0.7rem; border-radius: 0.5rem; border: 1px solid {{ $isActive ? '#FECACA' : '#A7F3D0' }}; background: {{ $isActive ? '#FEF2F2' : '#ECFDF5' }}; color: {{ $isActive ? '#B91C1C' : '#047857' }}; font-size: 0.75rem; font-weight: 700; cursor: pointer;"
                            >
                                {{ $isActive ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 1rem; padding: 0.75rem 0.95rem; border-radius: 0.75rem; background: #F8FAF9; border: 1px solid #E2E8F0; font-size: 0.75rem; color: #475569; line-height: 1.55;">
            <strong style="color: #0F3D2E;">Pemulihan Darurat Server (PUSTIKOM):</strong>
            Apabila seluruh akun Super Admin kehilangan akses atau lupa password, tim pengelola VPS dapat menjalankan perintah
            <code style="background: #E2E8F0; color: #0F172A; padding: 0.1rem 0.35rem; border-radius: 0.25rem; font-size: 0.71875rem;">php artisan evitron:admin</code>
            langsung dari terminal server untuk membuat, mereset password, atau mengaktifkan kembali Super Admin.
        </div>
    </div>
</div>

{{-- MODAL: Tambah Akun Admin Baru --}}
@if($showCreateAdminModal)
    <div
        wire:key="modal-create-admin"
        x-data="{
            showPwd: false,
            showConfirm: false,
            init() {
                document.documentElement.classList.add('sipetron-drawer-locked');
                document.body.style.overflow = 'hidden';
            },
            destroy() {
                document.documentElement.classList.remove('sipetron-drawer-locked');
                document.body.style.overflow = '';
            }
        }"
        @keydown.escape.window="$wire.closeCreateAdminModal()"
        @click.self="$wire.closeCreateAdminModal()"
        style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.58); z-index: 9998; display: flex; align-items: center; justify-content: center; padding: 1rem;"
    >
        <div style="background: #FFFFFF; width: 100%; max-width: 520px; border-radius: 1rem; border: 1px solid #E2E8F0; box-shadow: 0 20px 35px rgba(15, 23, 42, 0.22); overflow: hidden;">
            <div style="padding: 1.15rem 1.35rem; border-bottom: 1px solid #E2E8F0; display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem;">
                <div>
                    <h4 style="margin: 0; font-size: 1rem; font-weight: 800; color: #0F172A;">Tambah Akun Admin Baru</h4>
                    <p style="margin: 0.25rem 0 0; font-size: 0.75rem; color: #64748B;">Akun baru langsung aktif dan dapat masuk di halaman login menggunakan email.</p>
                </div>
                <button type="button" wire:click="closeCreateAdminModal" aria-label="Tutup" class="fi-modal-close-btn">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form wire:submit.prevent="createAdminAccount" novalidate>
                <div style="padding: 1.2rem 1.35rem; display: flex; flex-direction: column; gap: 0.85rem; max-height: 70vh; overflow-y: auto;">
                    <div class="sipetron-form-group">
                        <label class="sipetron-form-label">Nama Lengkap <span style="color: #EF4444;">*</span></label>
                        <input type="text" wire:model.defer="new_admin_name" class="sipetron-form-input" placeholder="Contoh: Budi Santoso">
                        @error('new_admin_name') <span class="sipetron-form-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="sipetron-form-group">
                        <label class="sipetron-form-label">Email Login <span style="color: #EF4444;">*</span></label>
                        <input type="email" wire:model.defer="new_admin_email" class="sipetron-form-input" placeholder="Contoh: budi@uinssc.ac.id">
                        @error('new_admin_email') <span class="sipetron-form-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="sipetron-form-grid sipetron-form-grid-2">
                        <div class="sipetron-form-group">
                            <label class="sipetron-form-label">Unit / Bagian</label>
                            <input type="text" wire:model.defer="new_admin_unit" class="sipetron-form-input" placeholder="PUSTIKOM / Humas">
                            @error('new_admin_unit') <span class="sipetron-form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="sipetron-form-group">
                            <label class="sipetron-form-label">No. WhatsApp</label>
                            <input type="text" wire:model.defer="new_admin_phone" class="sipetron-form-input" placeholder="081234567890">
                            @error('new_admin_phone') <span class="sipetron-form-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="sipetron-form-grid sipetron-form-grid-2">
                        <div class="sipetron-form-group">
                            <label class="sipetron-form-label">Password Awal <span style="color: #EF4444;">*</span></label>
                            <div style="position: relative;">
                                <input :type="showPwd ? 'text' : 'password'" wire:model.defer="new_admin_password" class="sipetron-form-input" style="padding-right: 2.4rem;" placeholder="Minimal 8 karakter" autocomplete="new-password">
                                <button type="button" @click="showPwd = !showPwd" :aria-label="showPwd ? 'Sembunyikan password' : 'Lihat password'" style="position: absolute; right: 0.55rem; top: 50%; transform: translateY(-50%); background: transparent; border: none; color: #64748B; cursor: pointer; padding: 0.25rem; display: inline-flex; align-items: center; justify-content: center;">
                                    <svg x-show="!showPwd" style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                    <svg x-show="showPwd" style="display: none; width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                                </button>
                            </div>
                            @error('new_admin_password') <span class="sipetron-form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="sipetron-form-group">
                            <label class="sipetron-form-label">Konfirmasi Password <span style="color: #EF4444;">*</span></label>
                            <div style="position: relative;">
                                <input :type="showConfirm ? 'text' : 'password'" wire:model.defer="new_admin_password_confirmation" class="sipetron-form-input" style="padding-right: 2.4rem;" placeholder="Ulangi password" autocomplete="new-password">
                                <button type="button" @click="showConfirm = !showConfirm" :aria-label="showConfirm ? 'Sembunyikan konfirmasi password' : 'Lihat konfirmasi password'" style="position: absolute; right: 0.55rem; top: 50%; transform: translateY(-50%); background: transparent; border: none; color: #64748B; cursor: pointer; padding: 0.25rem; display: inline-flex; align-items: center; justify-content: center;">
                                    <svg x-show="!showConfirm" style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                    <svg x-show="showConfirm" style="display: none; width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.85rem; padding: 0.75rem 0.9rem; border-radius: 0.65rem; border: 1px solid #FDE68A; background: #FFFBEB;">
                        <div style="font-size: 0.78125rem; color: #78350F; line-height: 1.45; min-width: 0; flex: 1;">
                            <strong style="display: block; color: #92400E;">Berikan hak Super Admin (Admin Utama)</strong>
                            Aktifkan hanya untuk penanggung jawab unit / PUSTIKOM yang berwenang mengelola Tim Admin. Biarkan nonaktif untuk <strong>Admin Operator</strong> biasa.
                        </div>
                        <label class="sipetron-switch-toggle" style="flex-shrink: 0; margin-top: 0.15rem;" title="Aktifkan hak Super Admin">
                            <input type="checkbox" wire:model="new_admin_is_super_admin">
                            <span class="sipetron-switch-slider"></span>
                        </label>
                    </div>
                </div>

                <div style="padding: 0.9rem 1.35rem; background: #F8FAF9; border-top: 1px solid #E2E8F0; display: flex; justify-content: flex-end; gap: 0.6rem;">
                    <button type="button" wire:click="closeCreateAdminModal" class="fi-btn-batal-custom">Batal</button>
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="createAdminAccount"
                        class="sipetron-settings-save-btn"
                        style="height: 38px; padding: 0 1.1rem; font-size: 0.8125rem;"
                    >
                        <span wire:loading.remove wire:target="createAdminAccount">Buat Akun Admin</span>
                        <span wire:loading wire:target="createAdminAccount" style="display: none;">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

{{-- MODAL: Reset Password Admin --}}
@if($showResetPasswordModal)
    <div
        wire:key="modal-reset-admin-password"
        x-data="{
            showPwd: false,
            showConfirm: false,
            init() {
                document.documentElement.classList.add('sipetron-drawer-locked');
                document.body.style.overflow = 'hidden';
            },
            destroy() {
                document.documentElement.classList.remove('sipetron-drawer-locked');
                document.body.style.overflow = '';
            }
        }"
        @keydown.escape.window="$wire.closeResetPasswordModal()"
        @click.self="$wire.closeResetPasswordModal()"
        style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.58); z-index: 9998; display: flex; align-items: center; justify-content: center; padding: 1rem;"
    >
        <div style="background: #FFFFFF; width: 100%; max-width: 460px; border-radius: 1rem; border: 1px solid #E2E8F0; box-shadow: 0 20px 35px rgba(15, 23, 42, 0.22); overflow: hidden;">
            <div style="padding: 1.15rem 1.35rem; border-bottom: 1px solid #E2E8F0; display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem;">
                <div>
                    <h4 style="margin: 0; font-size: 1rem; font-weight: 800; color: #0F172A;">Reset Password Admin</h4>
                    <p style="margin: 0.25rem 0 0; font-size: 0.75rem; color: #64748B;">
                        Akun: <strong>{{ $reset_password_admin_name }}</strong> ({{ $reset_password_admin_email }})
                    </p>
                </div>
                <button type="button" wire:click="closeResetPasswordModal" aria-label="Tutup" class="fi-modal-close-btn">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form wire:submit.prevent="confirmResetAdminPassword" novalidate>
                <div style="padding: 1.2rem 1.35rem; display: flex; flex-direction: column; gap: 0.85rem;">
                    <div class="sipetron-form-group">
                        <label class="sipetron-form-label">Password Baru <span style="color: #EF4444;">*</span></label>
                        <div style="position: relative;">
                            <input :type="showPwd ? 'text' : 'password'" wire:model.defer="reset_admin_password" class="sipetron-form-input" style="padding-right: 2.4rem;" placeholder="Minimal 8 karakter" autocomplete="new-password">
                            <button type="button" @click="showPwd = !showPwd" :aria-label="showPwd ? 'Sembunyikan password' : 'Lihat password'" style="position: absolute; right: 0.55rem; top: 50%; transform: translateY(-50%); background: transparent; border: none; color: #64748B; cursor: pointer; padding: 0.25rem; display: inline-flex; align-items: center; justify-content: center;">
                                <svg x-show="!showPwd" style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                <svg x-show="showPwd" style="display: none; width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                            </button>
                        </div>
                        @error('reset_admin_password') <span class="sipetron-form-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="sipetron-form-group">
                        <label class="sipetron-form-label">Konfirmasi Password Baru <span style="color: #EF4444;">*</span></label>
                        <div style="position: relative;">
                            <input :type="showConfirm ? 'text' : 'password'" wire:model.defer="reset_admin_password_confirmation" class="sipetron-form-input" style="padding-right: 2.4rem;" placeholder="Ulangi password baru" autocomplete="new-password">
                            <button type="button" @click="showConfirm = !showConfirm" :aria-label="showConfirm ? 'Sembunyikan konfirmasi password' : 'Lihat konfirmasi password'" style="position: absolute; right: 0.55rem; top: 50%; transform: translateY(-50%); background: transparent; border: none; color: #64748B; cursor: pointer; padding: 0.25rem; display: inline-flex; align-items: center; justify-content: center;">
                                <svg x-show="!showConfirm" style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                <svg x-show="showConfirm" style="display: none; width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                            </button>
                        </div>
                    </div>

                    <span class="sipetron-form-hint">Sesi login lama akun ini akan dicabut otomatis. Sarankan petugas mengganti password lagi setelah berhasil login.</span>
                </div>

                <div style="padding: 0.9rem 1.35rem; background: #F8FAF9; border-top: 1px solid #E2E8F0; display: flex; justify-content: flex-end; gap: 0.6rem;">
                    <button type="button" wire:click="closeResetPasswordModal" class="fi-btn-batal-custom">Batal</button>
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="confirmResetAdminPassword"
                        class="sipetron-settings-save-btn"
                        style="height: 38px; padding: 0 1.1rem; font-size: 0.8125rem;"
                    >
                        <span wire:loading.remove wire:target="confirmResetAdminPassword">Simpan Password Baru</span>
                        <span wire:loading wire:target="confirmResetAdminPassword" style="display: none;">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
