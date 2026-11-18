<div
    x-data="{
        isOpen: false,
        currentPassword: '',
        newPassword: '',
        newPasswordConfirmation: '',
        showCurrent: false,
        showNew: false,
        showConfirm: false,
        loading: false,
        errorMessage: '',
        successMessage: '',
        openModal() {
            this.isOpen = true;
            document.body.style.overflow = 'hidden';
            this.currentPassword = '';
            this.newPassword = '';
            this.newPasswordConfirmation = '';
            this.showCurrent = false;
            this.showNew = false;
            this.showConfirm = false;
            this.errorMessage = '';
            this.successMessage = '';
            this.$nextTick(() => {
                this.$refs.currentPasswordInput?.focus();
            });
        },
        closeModal() {
            if (this.loading) return;
            this.isOpen = false;
            document.body.style.overflow = '';
        },
        async submit() {
            this.errorMessage = '';
            this.successMessage = '';

            if (!this.currentPassword) {
                this.errorMessage = 'Password saat ini wajib diisi.';
                this.$refs.currentPasswordInput?.focus();
                return;
            }
            if (!this.newPassword) {
                this.errorMessage = 'Password baru wajib diisi.';
                this.$refs.newPasswordInput?.focus();
                return;
            }
            if (this.newPassword.length < 6) {
                this.errorMessage = 'Password baru minimal harus 6 karakter.';
                this.$refs.newPasswordInput?.focus();
                return;
            }
            if (this.newPassword !== this.newPasswordConfirmation) {
                this.errorMessage = 'Konfirmasi password baru tidak cocok.';
                this.$refs.confirmPasswordInput?.focus();
                return;
            }

            this.loading = true;
            try {
                const response = await fetch('{{ route('admin.change-password') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        current_password: this.currentPassword,
                        new_password: this.newPassword,
                        new_password_confirmation: this.newPasswordConfirmation
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    this.errorMessage = data.message || 'Terjadi kesalahan saat memproses permintaan.';
                    this.loading = false;
                    return;
                }

                this.successMessage = data.message || 'Password berhasil diperbarui!';
                this.loading = false;
                setTimeout(() => {
                    this.closeModal();
                }, 1600);
            } catch (err) {
                this.errorMessage = 'Gagal menghubungi server. Periksa koneksi Anda dan coba lagi.';
                this.loading = false;
            }
        }
    }"
    x-init="$watch('isOpen', value => { document.body.style.overflow = value ? 'hidden' : ''; })"
    @open-change-password-modal.window="openModal()"
    @keydown.escape.window="if (isOpen && !loading) closeModal()"
>
    <template x-teleport="body">
        <template x-if="isOpen">
            <div 
                class="sipetron-cp-modal-backdrop"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
            >
                {{-- Backdrop Click Dismiss --}}
                <div 
                    class="fixed inset-0"
                    style="position: fixed; inset: 0;"
                    @click="closeModal()"
                ></div>

                {{-- Modal Card --}}
                <div 
                    class="sipetron-cp-modal-card"
                    role="dialog"
                    aria-modal="true"
                >
                    {{-- Modal Header --}}
                    <div class="sipetron-cp-modal-header">
                        <div class="sipetron-cp-modal-header-left">
                            <div class="sipetron-cp-modal-icon-badge">
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="sipetron-cp-modal-title">Ubah Password Akun</h3>
                                <p class="sipetron-cp-modal-subtitle">Perbarui kata sandi login Admin E-VITRON.</p>
                            </div>
                        </div>
                        <button 
                            type="button" 
                            @click="closeModal()" 
                            :disabled="loading"
                            class="sipetron-cp-modal-close-btn"
                            title="Tutup dialog"
                        >
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Modal Body Form --}}
                    <form @submit.prevent="submit">
                        <div class="sipetron-cp-modal-body">
                            {{-- Alert Error --}}
                            <template x-if="errorMessage">
                                <div class="sipetron-cp-alert-error">
                                    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                    </svg>
                                    <span x-text="errorMessage"></span>
                                </div>
                            </template>

                            {{-- Alert Success --}}
                            <template x-if="successMessage">
                                <div class="sipetron-cp-alert-success">
                                    <svg fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                    <span x-text="successMessage"></span>
                                </div>
                            </template>

                            {{-- Field 1: Password Saat Ini --}}
                            <div class="sipetron-cp-form-group">
                                <label class="sipetron-cp-form-label">
                                    <span>Password Saat Ini</span>
                                    <span style="color: #EF4444;">*</span>
                                </label>
                                <div class="sipetron-cp-input-wrap">
                                    <input 
                                        :type="showCurrent ? 'text' : 'password'"
                                        x-model="currentPassword"
                                        x-ref="currentPasswordInput"
                                        :disabled="loading"
                                        placeholder="Ketik kata sandi akun saat ini"
                                        autocomplete="current-password"
                                        class="sipetron-cp-input"
                                    >
                                    <button 
                                        type="button" 
                                        @click="showCurrent = !showCurrent" 
                                        class="sipetron-cp-eye-btn"
                                        tabindex="-1"
                                        title="Lihat / sembunyikan password"
                                    >
                                        <svg x-show="!showCurrent" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <svg x-show="showCurrent" x-cloak style="display: none;" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Field 2: Password Baru --}}
                            <div class="sipetron-cp-form-group">
                                <label class="sipetron-cp-form-label">
                                    <span>Password Baru</span>
                                    <span style="color: #EF4444;">*</span>
                                </label>
                                <div class="sipetron-cp-input-wrap">
                                    <input 
                                        :type="showNew ? 'text' : 'password'"
                                        x-model="newPassword"
                                        x-ref="newPasswordInput"
                                        :disabled="loading"
                                        placeholder="Minimal 6 karakter"
                                        autocomplete="new-password"
                                        class="sipetron-cp-input"
                                    >
                                    <button 
                                        type="button" 
                                        @click="showNew = !showNew" 
                                        class="sipetron-cp-eye-btn"
                                        tabindex="-1"
                                        title="Lihat / sembunyikan password"
                                    >
                                        <svg x-show="!showNew" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <svg x-show="showNew" x-cloak style="display: none;" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                        </svg>
                                    </button>
                                </div>
                                <span class="sipetron-cp-hint">Disarankan kombinasi huruf dan angka minimal 6 karakter.</span>
                            </div>

                            {{-- Field 3: Konfirmasi Password Baru --}}
                            <div class="sipetron-cp-form-group">
                                <label class="sipetron-cp-form-label">
                                    <span>Ulangi Password Baru</span>
                                    <span style="color: #EF4444;">*</span>
                                </label>
                                <div class="sipetron-cp-input-wrap">
                                    <input 
                                        :type="showConfirm ? 'text' : 'password'"
                                        x-model="newPasswordConfirmation"
                                        x-ref="confirmPasswordInput"
                                        :disabled="loading"
                                        placeholder="Ulangi kata sandi baru"
                                        autocomplete="new-password"
                                        class="sipetron-cp-input"
                                    >
                                    <button 
                                        type="button" 
                                        @click="showConfirm = !showConfirm" 
                                        class="sipetron-cp-eye-btn"
                                        tabindex="-1"
                                        title="Lihat / sembunyikan password"
                                    >
                                        <svg x-show="!showConfirm" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <svg x-show="showConfirm" x-cloak style="display: none;" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="sipetron-cp-modal-footer">
                            <button 
                                type="button" 
                                @click="closeModal()" 
                                :disabled="loading"
                                class="sipetron-cp-btn-cancel"
                            >
                                Batal
                            </button>
                            <button 
                                type="submit" 
                                :disabled="loading"
                                class="sipetron-cp-btn-submit"
                            >
                                <svg x-show="loading" x-cloak style="display: none;" class="animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <span x-text="loading ? 'Menyimpan...' : 'Simpan Password Baru'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </template>
</div>
